<?php
declare(strict_types=1);

namespace App\Model;

use Psr\Log\LoggerInterface;
use Exception;
use PDO;

class Connection {

    /**
     * @var null|Connection $instance
     */
    private static null|Connection $instance = null;

    /**
     * @var string $dsn
     */
    private string $dsn = 'mysql:host=mariadb;dbname=app;port=3306';

    /**
     * @var null|string $sshTunnel
     * @phpstan-ignore property.unusedType
     */
    private null|string $sshTunnel = null;

    /**
     * @var PDO $db
     */
    private PDO $db;

    /**
     * @var null|string $username
     * @phpstan-ignore property.unusedType
     */
    private null|string $username = 'root';

    /**
     * @var null|string $password
     * @phpstan-ignore property.unusedType
     */
    private null|string $password = 'root';

    /**
     * @var LoggerInterface $logger
     */
    protected LoggerInterface $logger;


    public static function getInstance() : Connection
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }

        return self::$instance;

    }//end getInstance()


    private function __construct()
    {
        if (is_string($this->sshTunnel)) {
            shell_exec($this->sshTunnel);
        }

        $this->db = new PDO($this->dsn, $this->username, $this->password);

    }//end __construct()


    /**
     * @param string $sql
     * @param bool|array<string, mixed> $params
     * @param string $className
     * @return bool|array<string, mixed> array of $className
     */
    public function get(string $sql, bool|array $params, string $className): bool|array
    {
        $stmt = $this->db->prepare($sql);

        try {
            if (! $stmt->execute($params)) { // @phpstan-ignore argument.type
                throw new Exception(implode(', ', $stmt->errorInfo()));
            }
        } catch (Exception $e) {
            throw new Exception($sql.' '.$e->getMessage());
        }

        return $stmt->fetchAll(PDO::FETCH_CLASS, $className);

    }//end get()


    /**
     * @param string $sql
     * @param bool|array<string, mixed> $params
     */
    public function executeSql(string $sql, bool|array $params=false): void
    {
        $stmt = $this->db->prepare($sql);

        try {
            if (! $stmt->execute($params)) { // @phpstan-ignore argument.type
                throw new Exception(implode(', ', $stmt->errorInfo()));
            }
        } catch (Exception $e) {
            throw new Exception($sql.' '.$e->getMessage());
        }

    }//end executeSql()


    /**
     * @param string $sql
     */
    public function exec(string $sql): void
    {
        $this->log($sql);

        if (! $this->db->exec($sql)) {
            $error = $this->db->errorInfo();

            if ($error[0] != "00000") {
                throw new Exception($sql.' '.implode(', ', $error));
            }
        }

    }//end exec()


    public function lastInsertId(): bool|string
    {
        return $this->db->lastInsertId();

    }//end lastInsertId()


    /**
     * Summary of log
     * @param string $sql
     * @param array<string, mixed> $params
     * @return void
     */
    protected function log(string $sql, array $params=[]): void
    {
        $p = json_encode($params);

        $this->logger->info("{$sql} :: {$p}");

    }//end log()


}//end class
