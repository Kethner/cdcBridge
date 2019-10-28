<?php
namespace Kethner\cdcBridge\implementations\MySQL;

use Kethner\cdcBridge\interfaces\Connection;
use PDO;

class sqlConnection implements Connection {

    private $host;
    private $dbname;
    private $username;
    private $password;
    public $pdo;

    function __construct($host, $dbname, $username, $password) {
        $this->host = $host;
        $this->dbname = $dbname;
        $this->username = $username;
        $this->password = $password;
        $this->pdo = new PDO(
            "mysql:host=" . $host . ";dbname=" . $dbname, 
            $username, 
            $password
        );
    }


    public function connect() {
        $this->pdo = new PDO(
            "mysql:host=" . $this->host . ";dbname=" . $this->dbname, 
            $this->username, 
            $this->password
        );
    }

    public function request($query, $data=[]) {
        $result = $this->pdo->prepare($query);
        $result->setFetchMode(PDO::FETCH_ASSOC);
        
        foreach ($data as $key => $value) {
            $key = ":" . $key;
            $result->bindValue($key, $value);
        }

        $result->execute();
        return $result;
    }

}
