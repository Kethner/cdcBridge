<?php

namespace Kethner\cdcBridge\implementations\MySQL;

use Kethner\cdcBridge\interfaces\Connection;
use PDO;

class sqlConnection implements Connection
{
    private $host;
    private $port;
    private $dbname;
    private $dsn;
    private $username;
    private $password;
    public $pdo;

    function __construct($host, $port = false, $dbname, $username, $password)
    {
        $this->host = $host;
        $this->port = $port;
        $this->dbname = $dbname;
        $this->username = $username;
        $this->password = $password;
        $this->dsn = "mysql:host=$host;dbname=$dbname;" . ($port ? "port=$port;" : '');
        $this->connect();
    }

    public function connect()
    {
        $this->pdo = new PDO($this->dsn, $this->username, $this->password);
    }

    public function request($query, $data = [])
    {
        $result = $this->pdo->prepare($query);
        $result->setFetchMode(PDO::FETCH_ASSOC);

        foreach ($data as $key => $value) {
            $key = ':' . $key;
            $result->bindValue($key, $value);
        }

        $result->execute();
        return $result;
    }
}
