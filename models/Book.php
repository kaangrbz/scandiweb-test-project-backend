<?php

include_once 'Product.php';

class Book extends Product
{
    protected $weight;
    protected $type = 'book';

    public function __construct($sku, $name, $price, $type, $weight)
    {
        parent::__construct($sku, $name, $price, $type);
        $this->weight = $weight;
    }

    public function save(PDO $connection)
    {
        try {
            $query = "INSERT INTO products (sku, type, name, price) VALUES (?, ?, ?, ?); INSERT INTO book_details (sku, weight) VALUES (?, ?);";
            $stmt = $connection->prepare($query);

            if (!$stmt) {
                // Handle errors
            }

            $parameters = array($this->sku, $this->type, $this->name, $this->price, $this->sku, $this->weight);
            return $stmt->execute($parameters);
        } catch (\Throwable $th) {
            return $th->getCode();
        }
    }

    public function getBooks(PDO $connection)
    {
        return $connection->query('SELECT products.*, weight FROM products INNER JOIN book_details ON products.sku = book_details.sku;')->fetchAll();
    }
}
