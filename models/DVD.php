<?php

include_once 'Product.php';

class DVD extends Product
{
    protected $size;

    public function __construct($sku, $name, $price, $type, $size)
    {
        parent::__construct($sku, $name, $price, $type);
        $this->size = $size;
    }

    public function save(PDO $connection)
    {
        try {
            $query = "INSERT INTO products (sku, type, name, price) VALUES (?, ?, ?, ?); INSERT INTO dvd_details (sku, size) VALUES (?, ?);";
            $stmt = $connection->prepare($query);
            
            if (!$stmt) {
                // Handle errors
            }

            $parameters = array($this->sku, $this->type, $this->name, $this->price, $this->sku, $this->size);
            return $stmt->execute($parameters);
        } catch (\Throwable $th) {
            return $th->getCode();
        }
    }
    public function getDVDs(PDO $connection)
    {
        return $connection->query('SELECT products.*, size FROM products INNER JOIN dvd_details ON products.sku = dvd_details.sku;')->fetchAll();
        
    }
}
