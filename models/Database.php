<?php
class Database
{
    protected $servername = "scandiweb-test-database.cz6ej6fqngnj.eu-north-1.rds.amazonaws.com";
    protected $username = "root";
    protected $password = "Kaan_Amazon.1471";
    protected $databasename = "database";
    protected $db;

    public function __construct()
    {
        try {
            $this->db = new PDO("mysql:host=$this->servername;dbname=$this->databasename", $this->username, $this->password);
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }
    }

    public function getConnection()
    {
        return $this->db;
    }
}
