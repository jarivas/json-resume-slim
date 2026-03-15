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

main($argv);

/**
 * @param array<int, string> $argv
 */
function main(array $argv): void
{
	$jsonPath = $argv[1];

	if (!is_file($jsonPath)) {
		fwrite(STDERR, "JSON file not found: {$jsonPath}\n");
		exit(1);
	}

	try {
		$payload = readJsonFile($jsonPath);
		validateResumeSchema($payload);
		importResume($payload);
		fwrite(STDOUT, "Import completed successfully\n");
		exit(0);
	} catch (Throwable $exception) {
		fwrite(STDERR, $exception->getMessage() . "\n");
		exit(1);
	}

}//end main()


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
 */
function importResume(array $payload): void
{
	clearResumeModels();

	$basicId = importBasics($payload['basics'] ?? null);

	importWorks($basicId, $payload['work'] ?? null);
	importEducations($basicId, $payload['education'] ?? null);
	importCertificates($basicId, $payload['certificates'] ?? null);
	importSkills($basicId, $payload['skills'] ?? null);
	importLanguages($basicId, $payload['languages'] ?? null);

}//end importResume()


function clearResumeModels(): void
{
	foreach (RESUME_MODELS as $modelClass) {
		$rows = $modelClass::get([], 0, 10000);
		if (is_bool($rows)) {
			continue;
		}

		foreach ($rows as $row) {
			if ($row instanceof Model) {
				$row->delete();
			}
		}
	}

}//end clearResumeModels()


/**
 * @param null|mixed $basics
 */
function importBasics(mixed $basics): string
{
	if (!is_array($basics)) {
		throw new RuntimeException('Missing basics section in resume JSON');
	}

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
