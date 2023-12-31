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
            $connection->beginTransaction();
            
            parent::save($connection);
            
            $query = $connection->prepare('INSERT INTO furniture_details (sku, width, height, length) VALUES (?, ?, ?, ?)');
            $query->execute([$this->sku, $this->width, $this->height, $this->length]);

            $connection->commit();
            return 1;
        } catch (\Throwable $th) {
            $connection->rollback();
            return $th->getCode();
        }
    }

    public function getFurnitures(PDO $connection)
    {
        return $connection->query('SELECT products.*, width, height, length FROM products INNER JOIN furniture_details ON products.sku = furniture_details.sku;')->fetchAll();
    }
}
