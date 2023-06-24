<?php
class Database
{
    protected $servername = getenv('DB_HOST');
    protected $username = getenv('DB_USERNAME');
    protected $password = getenv('DB_PASSWORD');
    protected $databasename = getenv('DB_NAME');
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

    public function getAllProducts()
    {
        $products = $this->db->query('SELECT products.*, size, weight, width, height, length FROM products
                                LEFT JOIN book_details ON products.sku = book_details.sku
                                LEFT JOIN dvd_details ON products.sku = dvd_details.sku
                                LEFT JOIN furniture_details ON products.sku = furniture_details.sku;')->fetchAll();
        return $products;
    }
    // TODO?: siniflara yaz
    public function getBooks()
    {
        $books = $this->db->query('SELECT products.*, weight FROM products
                                INNER JOIN book_details ON products.sku = book_details.sku;')->fetchAll();
        return $books;
    }

    public function getDVDs()
    {
        $dvds = $this->db->query('SELECT products.*, size FROM products
                                INNER JOIN dvd_details ON products.sku = dvd_details.sku;')->fetchAll();
        return $dvds;
    }

    public function getFurnitures()
    {
        $furnitures = $this->db->query('SELECT products.*, width, height, length FROM products
                                INNER JOIN furniture_details ON products.sku = furniture_details.sku;')->fetchAll();
        return $furnitures;
    }
}
