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
            
            $query1 = $connection->prepare('INSERT INTO products (sku, type, name, price) VALUES (?, ?, ?, ?)');
            $query2 = $connection->prepare('INSERT INTO book_details (sku, weight) VALUES (?, ?)');

            $query1->execute([$this->sku, $this->type, $this->name, $this->price]);
            $query2->execute([$this->sku, $this->weight]);

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
