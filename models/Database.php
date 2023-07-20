<?php
class Database
{
    protected $servername = "scandiweb-test-database.cz6ej6fqngnj.eu-north-1.rds.amazonaws.com";
    protected $username = "root";
    protected $password = "Kaan_Amazon.1471";
    protected $databasename = "database";
    protected $connection;

    public function __construct()
    {
        try {
            $this->connection = new PDO("mysql:host=$this->servername;dbname=$this->databasename", $this->username, $this->password);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
            die;
        }
    }

    public function getConnection()
    {
        return $this->connection;
    }
}
