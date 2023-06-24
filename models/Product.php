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
}
