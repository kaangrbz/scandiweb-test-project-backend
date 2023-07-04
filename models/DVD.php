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
            $connection->beginTransaction();
            
            $query1 = $connection->prepare('INSERT INTO products (sku, type, name, price) VALUES (?, ?, ?, ?)');
            $query2 = $connection->prepare('INSERT INTO dvd_details (sku, size) VALUES (?, ?)');

            $query1->execute([$this->sku, $this->type, $this->name, $this->price]);
            $query2->execute([$this->sku, $this->size]);

            $connection->commit();
            return 1;
        } catch (\Throwable $th) {
            $connection->rollback();
            return $th->getCode();
        }
    }
    public function getDVDs(PDO $connection)
    {
        return $connection->query('SELECT products.*, size FROM products INNER JOIN dvd_details ON products.sku = dvd_details.sku;')->fetchAll();
        
    }
}
