<?php

include_once 'Dvd.php';
include_once 'Book.php';
include_once 'Furniture.php';
include_once 'Product.php';
include_once 'HttpStatusCodes.php';

class Api
{
    private $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }
    public function getProducts()
    {
        try {
            $products = Product::getAllProducts($this->connection);

            return Helper::createErrorMessage(true, 'success', 'Success', HttpStatusCodes::OK, $products);
        } catch (\Exception $th) {
            return Helper::createErrorMessage(false, 'get_product', 'An error was occured while getting products', HttpStatusCodes::INTERNAL_SERVER_ERROR);
        }
    }

    public function addProduct($requestData)
    {
        // Validate input data
        if (!isset($requestData['sku'], $requestData['name'], $requestData['type'])) {
            return Helper::createErrorMessage(false, 'null_value', 'Please enter all values', HttpStatusCodes::BAD_REQUEST);
        }

        // Validate price data
        if (!isset($requestData['price']) || !is_numeric($requestData['price']) || floatval($requestData['price']) < 0) {
            return Helper::createErrorMessage(false, 'invalid_price', 'Please enter a valid price', HttpStatusCodes::BAD_REQUEST);
        }

        $sku = trim($requestData['sku']);
        $name = trim($requestData['name']);
        $price = $requestData['price'];
        $type = $requestData['type'];

        $maxSkuLength = 50; // Set max length for SKU
        if (strlen($sku) > $maxSkuLength) {
            return Helper::createErrorMessage(false, 'invalid_sku', "SKU must be less than or equal to $maxSkuLength characters", HttpStatusCodes::BAD_REQUEST);
        }

        // Create object based on product type
        $obj = $this->createProductByType($type, $requestData);
        if ($obj === null) {
            return Helper::createErrorMessage(false, 'invalid_type', 'Please select a valid product type', HttpStatusCodes::BAD_REQUEST);
        }

        // Save object to database
        $result = $obj->save($this->connection);

        if ($result == 1) {
            return Helper::createErrorMessage(true, 'success', 'Success', 201);
        } else if ($result == 23000) {
            return Helper::createErrorMessage(false, 'duplicate', 'This product already exists', HttpStatusCodes::CONFLICT);
        } else {
            return Helper::createErrorMessage(false, 'unknown_error', 'An unknown error occurred', HttpStatusCodes::INTERNAL_SERVER_ERROR);
        }
    }

    private function createProductByType(string $type, array $requestData)
    {
        $validTypes = [
            'dvd' => ['size'],
            'book' => ['weight'],
            'furniture' => ['width', 'height', 'length']
        ];

        if (!isset($validTypes[$type])) {
            return null;
        }

        $requiredAttributes = $validTypes[$type];
        if (!$this->hasRequiredAttributes($requestData, $requiredAttributes)) {
            return null;
        }

        switch ($type) {
            case 'dvd':
                return new DVD($requestData['sku'], $requestData['name'], $requestData['price'], $type, $requestData['size']);
            case 'book':
                return new Book($requestData['sku'], $requestData['name'], $requestData['price'], $type, $requestData['weight']);
            case 'furniture':
                return new Furniture($requestData['sku'], $requestData['name'], $requestData['price'], $type, $requestData['width'], $requestData['height'], $requestData['length']);
        }

        return null;
    }

    private function hasRequiredAttributes($requestData, $requiredAttributes)
    {
        foreach ($requiredAttributes as $attribute) {
            if (!isset($requestData[$attribute])) {
                return false;
            }
        }

        return true;
    }


    public function deleteProducts(array $skus = [])
    {

        if (empty($skus) || count($skus) <= 0) {
            return Helper::createErrorMessage(false, 'null_data', 'Please select some products', HttpStatusCodes::BAD_REQUEST);
        }

        try {
            Product::deleteProducts(join(',', $skus), $this->connection);
            return Helper::createErrorMessage(true, 'success', 'Successfuly deleted', HttpStatusCodes::OK);
        } catch (\Exception $th) {
            return Helper::createErrorMessage(false, 'interval_error', 'There is an interval error occured while mass delete', HttpStatusCodes::INTERNAL_SERVER_ERROR);
        }
    }
}
