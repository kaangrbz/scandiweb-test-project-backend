<?php

include_once 'Product.php';

class Furniture extends Product
{
    protected $width;
    protected $height;
    protected $length;

    public function __construct(
        $sku,
        $name,
        $price,
        $type,
        $width,
        $height,
        $length
    ) {
        parent::__construct($sku, $name, $price, $type);
        $this->width = $width;
        $this->height = $height;
        $this->length = $length;
    }

    public function save(PDO $connection)
    {
        try {
            $query = "INSERT INTO products (sku, type, name, price) VALUES (?, ?, ?, ?); INSERT INTO furniture_details (sku, width, height, length) VALUES (?, ?, ?, ?);";
            $stmt = $connection->prepare($query);

            if (!$stmt) {
                // Handle errors
            }

            $parameters = array($this->sku, $this->type, $this->name, $this->price, $this->sku, $this->width, $this->height, $this->length);

            return $stmt->execute($parameters);
        } catch (\Throwable $th) {
            return $th->getCode();
        }
    }

    public function getFurnitures(PDO $connection)
    {
        return $connection->query('SELECT products.*, width, height, length FROM products INNER JOIN furniture_details ON products.sku = furniture_details.sku;')->fetchAll();
    }
}
