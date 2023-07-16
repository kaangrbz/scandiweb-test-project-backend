<?php

include_once "Database.php";

class Product
{
    protected $sku;
    protected $name;
    protected $price;
    protected $type;

    public function __construct(string $sku, string $name, float $price, string $type)
    {
        $this->sku = $sku;
        $this->name = $name;
        $this->price = $price;
        $this->type = $type;
    }

    public static function getAllProducts(PDO $connection)
    {
        try {
            return $connection->query(
                'SELECT products.*, size, weight, width, height, length FROM products
                LEFT JOIN book_details ON products.sku = book_details.sku
                LEFT JOIN dvd_details ON products.sku = dvd_details.sku
                LEFT JOIN furniture_details ON products.sku = furniture_details.sku;')->fetchAll();
        } catch (\Exception $th) {
            return array();
        }
    }

    public static function deleteProducts(string $skus, PDO $connection)
    {
        // Example skus: DVD-001,BK-002,FNT-012

        $query = "DELETE FROM products WHERE sku in ($skus)";
        $result = $connection->query($query)->execute();
        
        return $result;
    }

    public function save(PDO $connection)
    {
        $query = $connection->prepare('INSERT INTO products (sku, type, name, price) VALUES (?, ?, ?, ?)');
        $query->execute([$this->sku, $this->type, $this->name, $this->price]);
    }
}
