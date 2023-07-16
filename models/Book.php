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
            $connection->beginTransaction();
            
            parent::save($connection);

            $query = $connection->prepare('INSERT INTO book_details (sku, weight) VALUES (?, ?)');
            $query->execute([$this->sku, $this->weight]);

            $connection->commit();
            return 1;
        } catch (\Throwable $th) {
            $connection->rollback();
            return $th->getCode();
        }
    }

    public function getBooks(PDO $connection)
    {
        return $connection->query('SELECT products.*, weight FROM products INNER JOIN book_details ON products.sku = book_details.sku;')->fetchAll();
    }
}
