<?php
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

    public static function getAllProducts(PDO $db)
    {
        $products = $db->query('SELECT products.*, size, weight, width, height, length FROM products
                                LEFT JOIN book_details ON products.sku = book_details.sku
                                LEFT JOIN dvd_details ON products.sku = dvd_details.sku
                                LEFT JOIN furniture_details ON products.sku = furniture_details.sku;')->fetchAll();
        return $products;
    }

    public static function deleteProducts(string $skus, PDO $db) {
        // Example skus: DVD-001,BK-002,FNT-012
        $query = "DELETE FROM products WHERE sku in ($skus)";
        $result = $db->query($query)->execute();
        return $result;
    }
}