<?php

include_once 'Dvd.php';
include_once 'Book.php';
include_once 'Furniture.php';
include_once 'Product.php';

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

            return Helper::createErrorMessage(true, 'success', 'Success', 200, $products);
        } catch (\Exception $th) {
            return Helper::createErrorMessage(false, 'get_product', 'An error was occured while getting products', 500);
        }
    }

    public function addProduct($requestData)
    {
        // Validate input data
        if (!isset($requestData['sku'], $requestData['name'], $requestData['type'])) {
            return Helper::createErrorMessage(false, 'null_value', 'Please enter all values', 400);
        }

        // Validate price data
        if (!isset($requestData['price']) || !is_numeric($requestData['price']) || floatval($requestData['price']) < 0) {
            return Helper::createErrorMessage(false, 'invalid_price', 'Please enter a valid price', 400);
        }

        $sku = $requestData['sku'];
        $name = $requestData['name'];
        $price = $requestData['price'];
        $type = $requestData['type'];

        $maxSkuLength = 50; // Set max length for SKU
        if (strlen($sku) > $maxSkuLength) {
            return Helper::createErrorMessage(false, 'invalid_sku', "SKU must be less than or equal to $maxSkuLength characters", 400);
        }


        // Create object based on product type
        switch ($type) {
            case 'dvd':
                if (!isset($requestData['size'])) {

                    return Helper::createErrorMessage(false, 'null_attribute', 'Please enter the size of the DVD', 400);
                }

                $size = $requestData['size'];

                if (!is_numeric($size) || $size < 0) {
                    return Helper::createErrorMessage(false, 'bad_attribute', 'Please enter a size that is 0 or greater', 400);
                }

                $obj = new DVD($sku, $name, $price, $type, $size);
                break;

            case 'book':

                if (!isset($requestData['weight'])) {
                    return Helper::createErrorMessage(false, 'null_attribute', 'Please enter the weight of the book', 400);
                }

                $weight = $requestData['weight'];

                if (!is_numeric($weight) || $weight < 0) {
                    return Helper::createErrorMessage(false, 'bad_attribute', 'Please enter a weight that is 0 or greater', 400);
                }


                $obj = new Book($sku, $name, $price, $type, $weight);
                break;

            case 'furniture':

                if (!isset($requestData['width'], $requestData['height'], $requestData['length'])) {
                    return Helper::createErrorMessage(false, 'null_attribute', 'Please enter the dimensions of the furniture', 400);
                }

                $width = $requestData['width'];
                $height = $requestData['height'];
                $length = $requestData['length'];

                $datas = array($width, $height, $length);

                if (Helper::isAllNumber($datas) && Helper::isAllEqualOrOver($datas, 0)) {
                    return Helper::createErrorMessage(false, 'bad_attribute', 'Please enter dimensions 0 or above 0', 400);
                }

                $obj = new Furniture($sku, $name, $price, $type, $width, $height, $length);
                break;

            default:
                return Helper::createErrorMessage(false, 'invalid_type', 'Please select a valid product type', 400);
        }

        // Save object to database
        $result = $obj->save($this->connection);

        if ($result == 1) {
            return Helper::createErrorMessage(true, 'success', 'Success', 201);
        } else if ($result == 23000) {
            return Helper::createErrorMessage(false, 'duplicate', 'This product already exists', 409);
        } else {
            return Helper::createErrorMessage(false, 'unknown_error', 'An unknown error occurred', 500);
        }
    }

    public function deleteProducts(array $skus = [])
    {

        if (!isset($skus)) {
            return Helper::createErrorMessage(false, 'null_data', 'Missing input data. Please select some products', 400);
        }

        try {
            if (is_array($skus) && count($skus) > 0) {
                Product::deleteProducts(join(',', $skus), $this->connection);
            } else {
                return Helper::createErrorMessage(false, 'bad_request', 'You must select at least one product for delete', 400);
            }

            return Helper::createErrorMessage(true, 'success', 'Successfuly deleted', 200);
        } catch (\Exception $th) {
            return Helper::createErrorMessage(false, 'interval_error', 'There is an interval error occured while mass delete', 500);
        }
    }
}
