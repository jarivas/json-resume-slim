<?php

namespace Tests\Unit;

use PDO;
use Tests\TestCase;

class ImportJsonCommandTest extends TestCase
{


    protected function setUp(): void
    {
        parent::setUp();

        if (!$this->databaseAvailable()) {
            $this->markTestSkipped('Database unavailable for ImportJson command tests.');
        }

        $this->ensureSchemaLoaded();
        $this->cleanupResumeTables();

    }//end setUp()


    protected function tearDown(): void
    {
        if ($this->databaseAvailable()) {
            $this->cleanupResumeTables();
        }

        parent::tearDown();

    }//end tearDown()


    public function test_importjson_imports_cv_fixture(): void
    {
        $jsonPath = getRootPath() . '/tests/Features/fixtures/cv.json';

        $result = $this->runImportCommand($jsonPath);

        $this->assertSame(0, $result['exitCode'], implode(PHP_EOL, $result['output']));

        $pdo = $this->getPdo();

        $this->assertSame(1, $this->countRows($pdo, 'basics'));
        $this->assertSame(6, $this->countRows($pdo, 'works'));
        $this->assertSame(2, $this->countRows($pdo, 'educations'));
        $this->assertSame(10, $this->countRows($pdo, 'certificates'));
        $this->assertSame(4, $this->countRows($pdo, 'skills'));
        $this->assertSame(2, $this->countRows($pdo, 'languages'));

    }//end test_importjson_imports_cv_fixture()


    public function test_importjson_fails_with_invalid_schema_payload(): void
    {
        $tmpPath = tempnam(sys_get_temp_dir(), 'resume-invalid-');
        $this->assertIsString($tmpPath);

        file_put_contents(
            $tmpPath,
            json_encode(
                [
                    'basics' => ['name' => 'Invalid Resume'],
                    'work' => 'invalid-type',
                ],
                JSON_THROW_ON_ERROR
            )
        );

        try {
            $result = $this->runImportCommand($tmpPath);
            $this->assertNotSame(0, $result['exitCode']);
            $this->assertStringContainsString(
                'JSON Resume schema validation failed',
                implode(PHP_EOL, $result['output'])
            );
        } finally {
            if (is_file($tmpPath)) {
                unlink($tmpPath);
            }
        }

    }//end test_importjson_fails_with_invalid_schema_payload()


    private function runImportCommand(string $jsonPath): array
    {
        $command = sprintf(
            'php %s %s 2>&1',
            escapeshellarg(getRootPath() . '/commands/ImportJson.php'),
            escapeshellarg($jsonPath)
        );

        $output = [];
        $exitCode = 1;
        exec($command, $output, $exitCode);

        return [
            'output' => $output,
            'exitCode' => $exitCode,
        ];

    }//end runImportCommand()


    private function databaseAvailable(): bool
    {
        try {
            $this->getPdo();
            return true;
        } catch (\Throwable) {
            return false;
        }

    }//end databaseAvailable()


    private function getPdo(): PDO
    {
        $dsn = sprintf(
            'mysql:host=%s;dbname=%s;port=%s;charset=utf8mb4',
            env('MARIADB_HOST'),
            env('MARIADB_DATABASE'),
            env('MARIADB_PORT')
        );

        return new PDO(
            $dsn,
            (string) env('MARIADB_USER'),
            (string) env('MARIADB_PASSWORD'),
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            ]
        );

    }//end getPdo()


    private function cleanupResumeTables(): void
    {
        $pdo = $this->getPdo();

        $tables = [
            'awards',
            'certificates',
            'educations',
            'interests',
            'languages',
            'projects',
            'publications',
            'references',
            'skills',
            'volunteers',
            'works',
            'basics',
        ];

        foreach ($tables as $table) {
            if (!$this->tableExists($pdo, $table)) {
                continue;
            }

            $pdo->exec("DELETE FROM `{$table}`");
        }

    }//end cleanupResumeTables()


    private function ensureSchemaLoaded(): void
    {
        $pdo = $this->getPdo();

        if ($this->tableExists($pdo, 'basics')) {
            return;
        }

        $schemaPath = getRootPath() . '/db.schema.sql';
        $schema = file_get_contents($schemaPath);
        if ($schema === false) {
            throw new \RuntimeException('Unable to read db.schema.sql');
        }

        $lines = explode(PHP_EOL, $schema);
        $cleanLines = [];
        foreach ($lines as $line) {
            if (str_starts_with(trim($line), '--')) {
                continue;
            }

            $cleanLines[] = $line;
        }

        $statements = explode(';', implode(PHP_EOL, $cleanLines));
        foreach ($statements as $statement) {
            $statement = trim($statement);
            if ($statement === '') {
                continue;
            }

            $pdo->exec($statement);
        }

    }//end ensureSchemaLoaded()


    private function tableExists(PDO $pdo, string $table): bool
    {
        $sql = 'SELECT COUNT(*) AS total FROM information_schema.tables WHERE table_schema = :schema AND table_name = :table';
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'schema' => (string) env('MARIADB_DATABASE'),
            'table' => $table,
        ]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return is_array($result)
            && isset($result['total'])
            && (int) $result['total'] > 0;

    }//end tableExists()


    private function countRows(PDO $pdo, string $table): int
    {
        $stmt = $pdo->query("SELECT COUNT(*) AS total FROM `{$table}`");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!is_array($result) || !isset($result['total'])) {
            return 0;
        }

        return (int) $result['total'];

    }//end countRows()


}//end class
