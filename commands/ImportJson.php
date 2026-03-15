<?php

use JsonSchema\Validator;
use JsonSchema\Constraints\Constraint;
use App\Model\Awards;
use App\Model\Basics;
use App\Model\Certificates;
use App\Model\Educations;
use App\Model\Interests;
use App\Model\Languages;
use App\Model\Model;
use App\Model\Projects;
use App\Model\Publications;
use App\Model\References;
use App\Model\Skills;
use App\Model\Volunteers;
use App\Model\Works;
use Ulid\Ulid;

require 'index.php';

const RESUME_MODELS = [
	Awards::class,
	Certificates::class,
	Educations::class,
	Interests::class,
	Languages::class,
	Projects::class,
	Publications::class,
	References::class,
	Skills::class,
	Volunteers::class,
	Works::class,
	Basics::class,
];

const IMPORT_TARGET_KEYS = [
	'work',
	'education',
	'certificates',
	'skills',
	'languages',
];

main($argv);

/**
 * @param array<int, string> $argv
 */
function main(array $argv): void
{
	[$jsonPath, $options] = parseArguments($argv);

	if (!is_file($jsonPath)) {
		fwrite(STDERR, "JSON file not found: {$jsonPath}\n");
		exit(1);
	}

	try {
		verboseLog($options, "Loading JSON file: {$jsonPath}");
		$payload = readJsonFile($jsonPath);
		verboseLog($options, 'Validating payload against jsonresume.schema.json');
		validateResumeSchema($payload);
		importResume($payload, $options);

		if ($options['dryRun'] === true) {
			fwrite(STDOUT, "Dry-run completed successfully (no database changes applied)\n");
		} else {
			fwrite(STDOUT, "Import completed successfully\n");
		}

		exit(0);
	} catch (Throwable $exception) {
		fwrite(STDERR, $exception->getMessage() . "\n");
		exit(1);
	}

}//end main()


/**
 * @param array<int, string> $argv
 * @return array{0:string,1:array{verbose:bool,dryRun:bool}}
 */
function parseArguments(array $argv): array
{
	$args = $argv;
	array_shift($args);

	$options = [
		'verbose' => false,
		'dryRun' => false,
	];

	$jsonPath = null;

	foreach ($args as $arg) {
		switch ($arg) {
			case '--verbose':
			case '-v':
				$options['verbose'] = true;
				break;
			case '--dry-run':
				$options['dryRun'] = true;
				break;
			default:
				if (str_starts_with($arg, '-')) {
					throw new RuntimeException("Unknown option: {$arg}");
				}

				if ($jsonPath !== null) {
					throw new RuntimeException('Only one JSON file path is allowed');
				}

				$jsonPath = $arg;
				break;
		}
	}

	if ($jsonPath === null) {
		throw new RuntimeException('Usage: php commands/ImportJson.php <resume.json> [--verbose|-v] [--dry-run]');
	}

	return [$jsonPath, $options];

}//end parseArguments()


/**
 * @param array{verbose:bool,dryRun:bool} $options
 */
function verboseLog(array $options, string $message): void
{
	if ($options['verbose'] === false) {
		return;
	}

	fwrite(STDOUT, "[verbose] {$message}\n");

}//end verboseLog()


/**
 * @return array<string, mixed>
 */
function readJsonFile(string $path): array
{
	$raw = file_get_contents($path);
	if ($raw === false) {
		throw new RuntimeException("Unable to read JSON file: {$path}");
	}

	$decoded = json_decode($raw, true);
	if (json_last_error() !== JSON_ERROR_NONE || !is_array($decoded)) {
		throw new RuntimeException('Invalid JSON file: ' . json_last_error_msg());
	}

	return $decoded;

}//end readJsonFile()


/**
 * @param array<string, mixed> $payload
 */
function validateResumeSchema(array $payload): void
{
	$validator = new Validator();
	$payloadObject = json_decode(json_encode($payload, JSON_THROW_ON_ERROR), false, 512, JSON_THROW_ON_ERROR);
	$schemaPath = getRootPath() . '/jsonresume.schema.json';

	if (!is_file($schemaPath)) {
		fwrite(STDERR, "Schema file not found: {$schemaPath}\n");
		exit(1);
	}

	$schema = json_decode(
		file_get_contents($schemaPath) ?: '{}',
		false,
		512,
		JSON_THROW_ON_ERROR
	);

	$validator->validate(
		$payloadObject,
		$schema,
		Constraint::CHECK_MODE_NORMAL
	);

	if ($validator->isValid()) {
		return;
	}

	$messages = [];
	foreach ($validator->getErrors() as $error) {
		$property = $error['property'] === '' ? '$' : $error['property'];
		$messages[] = "{$property}: {$error['message']}";
	}

	throw new RuntimeException('JSON Resume schema validation failed: ' . implode('; ', $messages));

}//end validateResumeSchema()


/**
 * @param array<string, mixed> $payload
 * @param array{verbose:bool,dryRun:bool} $options
 */
function importResume(array $payload, array $options): void
{
	verboseLog($options, 'Preparing import targets');
	clearResumeModels($options);

	if ($options['dryRun'] === true) {
		validateBasicsSection($payload['basics'] ?? null);
		$basicId = Ulid::generate()->__toString();
		verboseLog($options, "Dry-run basics id: {$basicId}");
		verboseLog(
			$options,
			'Planned rows by section: ' . json_encode(summarizePlannedRows($payload), JSON_THROW_ON_ERROR)
		);
		return;
	}

	$basicId = importBasics($payload['basics'] ?? null);
	verboseLog($options, "Resolved basics id: {$basicId}");

	importWorks($basicId, $payload['work'] ?? null);
	importEducations($basicId, $payload['education'] ?? null);
	importCertificates($basicId, $payload['certificates'] ?? null);
	importSkills($basicId, $payload['skills'] ?? null);
	importLanguages($basicId, $payload['languages'] ?? null);

	verboseLog($options, 'Database write phase completed');

}//end importResume()


/**
 * @param null|mixed $basics
 */
function validateBasicsSection(mixed $basics): void
{
	if (!is_array($basics)) {
		throw new RuntimeException('Missing basics section in resume JSON');
	}

}//end validateBasicsSection()


/**
 * @param array{verbose:bool,dryRun:bool} $options
 */
function clearResumeModels(array $options): void
{
	if ($options['dryRun'] === true) {
		verboseLog($options, 'Dry-run active: skipping deletion of existing rows');
		return;
	}

	foreach (RESUME_MODELS as $modelClass) {
		$rows = $modelClass::get([], 0, 10000);
		if (is_bool($rows)) {
			verboseLog($options, "No rows found for {$modelClass}");
			continue;
		}

		verboseLog($options, sprintf('Deleting %d rows from %s', count($rows), $modelClass));

		foreach ($rows as $row) {
			if ($row instanceof Model) {
				$row->delete();
			}
		}
	}

}//end clearResumeModels()


/**
 * @param array<string, mixed> $payload
 * @return array<string, int>
 */
function summarizePlannedRows(array $payload): array
{
	$summary = [
		'basics' => is_array($payload['basics'] ?? null) ? 1 : 0,
	];

	foreach (IMPORT_TARGET_KEYS as $key) {
		$items = $payload[$key] ?? null;
		if (!is_array($items)) {
			$summary[$key] = 0;
			continue;
		}

		$total = 0;
		foreach ($items as $item) {
			if (is_array($item)) {
				$total++;
			}
		}

		$summary[$key] = $total;
	}

	return $summary;

}//end summarizePlannedRows()


/**
 * @param null|mixed $basics
 */
function importBasics(mixed $basics): string
{
	validateBasicsSection($basics);

	$id = Ulid::generate()->__toString();

	$location = is_array($basics['location'] ?? null)
		? json_encode($basics['location'], JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE)
		: null;

	$profiles = is_array($basics['profiles'] ?? null)
		? json_encode($basics['profiles'], JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE)
		: null;

	$model = new Basics();
	$model->setData([
		'id' => $id,
		'name' => normalizeString($basics['name'] ?? ''),
		'label' => normalizeString($basics['label'] ?? ''),
		'email' => normalizeString($basics['email'] ?? ''),
		'phone' => normalizeString($basics['phone'] ?? ''),
		'url' => normalizeNullableString($basics['url'] ?? null),
		'summary' => normalizeNullableString($basics['summary'] ?? null),
		'location' => $location,
		'profiles' => $profiles,
	]);
	$model->insert();

	return $id;

}//end importBasics()


function importWorks(string $basicId, mixed $items): void
{
	if (!is_array($items)) {
		return;
	}

	foreach ($items as $item) {
		if (!is_array($item)) {
			continue;
		}

		$startDate = normalizeDateTime($item['startDate'] ?? null);
		$endDate = normalizeDateTime($item['endDate'] ?? null) ?? $startDate;

		if ($startDate === null || $endDate === null) {
			throw new RuntimeException('Invalid work dates in resume JSON');
		}

		$model = new Works();
		$model->setData([
			'id' => Ulid::generate()->__toString(),
			'name' => normalizeString($item['name'] ?? ''),
			'position' => normalizeString($item['position'] ?? ''),
			'url' => normalizeNullableString($item['url'] ?? null),
			'startDate' => $startDate,
			'endDate' => $endDate,
			'summary' => normalizeString($item['summary'] ?? ''),
			'highlights' => json_encode(
				normalizeStringArray($item['highlights'] ?? []),
				JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE
			),
			'basic_id' => $basicId,
		]);
		$model->insert();
	}

}//end importWorks()


function importEducations(string $basicId, mixed $items): void
{
	if (!is_array($items)) {
		return;
	}

	foreach ($items as $item) {
		if (!is_array($item)) {
			continue;
		}

		$startDate = normalizeDateTime($item['startDate'] ?? null);
		$endDate = normalizeDateTime($item['endDate'] ?? null) ?? $startDate;

		if ($startDate === null || $endDate === null) {
			throw new RuntimeException('Invalid education dates in resume JSON');
		}

		$courses = null;
		if (is_array($item['courses'] ?? null)) {
			$courses = json_encode(
				normalizeStringArray($item['courses']),
				JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE
			);
		}

		$model = new Educations();
		$model->setData([
			'id' => Ulid::generate()->__toString(),
			'institution' => normalizeString($item['institution'] ?? ''),
			'url' => normalizeNullableString($item['url'] ?? null),
			'area' => normalizeString($item['area'] ?? ''),
			'studyType' => normalizeString($item['studyType'] ?? ''),
			'startDate' => $startDate,
			'endDate' => $endDate,
			'score' => normalizeNullableString($item['score'] ?? null),
			'summary' => normalizeString($item['summary'] ?? ''),
			'courses' => $courses,
			'basic_id' => $basicId,
		]);
		$model->insert();
	}

}//end importEducations()


function importCertificates(string $basicId, mixed $items): void
{
	if (!is_array($items)) {
		return;
	}

	foreach ($items as $item) {
		if (!is_array($item)) {
			continue;
		}

		$date = normalizeDateTime($item['date'] ?? null);
		if ($date === null) {
			throw new RuntimeException('Invalid certificate date in resume JSON');
		}

		$model = new Certificates();
		$model->setData([
			'id' => Ulid::generate()->__toString(),
			'name' => normalizeString($item['name'] ?? ''),
			'date' => $date,
			'issuer' => normalizeString($item['issuer'] ?? ''),
			'url' => normalizeString($item['url'] ?? ''),
			'basic_id' => $basicId,
		]);
		$model->insert();
	}

}//end importCertificates()


function importSkills(string $basicId, mixed $items): void
{
	if (!is_array($items)) {
		return;
	}

	foreach ($items as $item) {
		if (!is_array($item)) {
			continue;
		}

		$model = new Skills();
		$model->setData([
			'id' => Ulid::generate()->__toString(),
			'name' => normalizeString($item['name'] ?? ''),
			'level' => normalizeString($item['level'] ?? ''),
			'keywords' => json_encode(
				normalizeStringArray($item['keywords'] ?? []),
				JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE
			),
			'basic_id' => $basicId,
		]);
		$model->insert();
	}

}//end importSkills()


function importLanguages(string $basicId, mixed $items): void
{
	if (!is_array($items)) {
		return;
	}

	foreach ($items as $item) {
		if (!is_array($item)) {
			continue;
		}

		$model = new Languages();
		$model->setData([
			'id' => Ulid::generate()->__toString(),
			'language' => normalizeString($item['language'] ?? ''),
			'fluency' => normalizeString($item['fluency'] ?? ''),
			'basic_id' => $basicId,
		]);
		$model->insert();
	}

}//end importLanguages()


function normalizeString(mixed $value): string
{
	return is_string($value) ? trim($value) : '';

}//end normalizeString()


function normalizeNullableString(mixed $value): ?string
{
	if (!is_string($value)) {
		return null;
	}

	$normalized = trim($value);

	return ($normalized === '') ? null : $normalized;

}//end normalizeNullableString()


/**
 * @param mixed $value
 * @return array<int, string>
 */
function normalizeStringArray(mixed $value): array
{
	if (!is_array($value)) {
		return [];
	}

	$result = [];
	foreach ($value as $entry) {
		if (is_string($entry)) {
			$result[] = trim($entry);
		}
	}

	return $result;

}//end normalizeStringArray()


function normalizeDateTime(mixed $value): ?string
{
	if (!is_string($value)) {
		return null;
	}

	$value = trim($value);
	if (preg_match('/^\d{4}$/', $value) === 1) {
		return "{$value}-01-01 00:00:00";
	}

	if (preg_match('/^\d{4}-\d{2}$/', $value) === 1) {
		return "{$value}-01 00:00:00";
	}

	if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value) === 1) {
		return "{$value} 00:00:00";
	}

	$timestamp = strtotime($value);
	if ($timestamp === false) {
		return null;
	}

	return date('Y-m-d H:i:s', $timestamp);

}//end normalizeDateTime()
