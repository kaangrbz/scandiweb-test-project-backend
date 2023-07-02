<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header('Content-Type: application/json');

include_once '../models/Database.php';
include_once '../models/DVD.php';
include_once '../models/Book.php';
include_once '../models/Furniture.php';
include_once '../models/Product.php';

$db = new Database();
$connection = $db->getConnection();
$method = $_SERVER["REQUEST_METHOD"];

try {
    $data = json_decode(file_get_contents('php://input'), true);
} catch (\Throwable $th) {

    echo json_encode(array(
        'status' => false,
        'code' => 'bad_request',
        'message' => 'Bad request. JSON'
    ));
    return;
}

/**
 ** TODO: GET method handling, return bad request
 ** TODO: POST method handling, return all products
 *! TODO: [?] PUT method handling, add product
 ** TODO: DELETE method handling, delete product(s)
 */

switch (strtoupper($method)) {
    case 'GET':

        echo json_encode(array(
            'status' => false,
            'code' => 'bad_request',
            'message' => 'Bad request. type parameter not found'
        ));
        return;

    case 'POST':
        try {
            $products = Product::getAllProducts($connection);
        } catch (\Throwable $th) {

            print_r($th);

            echo json_encode(array(
                'status' => false,
                'code' => 'get_product',
                'message' => 'An error was occured while getting products',
                'error_message' => $th->getMessage(),
                'data' => array()
            ));
            return;
        }

        echo json_encode(array(
            'status' => true,
            'code' => 'success',
            'message' => 'Success',
            'data' => $products
        ));

        break;

    case 'PUT':

        // Validate input data
        if (!isset($data['sku'], $data['name'], $data['type'])) {
            $response_data = array(
                'status' => false,
                'code' => "null_value",
                'message' => "Please enter all values",
            );

            http_response_code(400);
            echo json_encode($response_data);
            return;
        }

        // Validate price data
        if (!isset($data['price']) || !is_numeric($data['price']) || !floatval($data['price']) < 0) {
            $response_data = array(
                'status' => false,
                'code' => "invalid_price",
                'message' => "Please enter a valid price",
            );

            http_response_code(400);
            echo json_encode($response_data);
            return;
        }

        $sku = $data['sku'];
        $name = $data['name'];
        $price = $data['price'];
        $type = $data['type'];

        $max_sku_length = 50; // Set max length for SKU
        if (strlen($sku) > $max_sku_length) {
            $response_data = array(
                'status' => false,
                'code' => "invalid_sku",
                'message' => "SKU must be less than or equal to $max_sku_length characters",
            );

            http_response_code(400);
            echo json_encode($response_data);
            return;
        }

        // Create object based on product type
        switch ($type) {
            case 'dvd':
                if (!isset($data['size'])) {
                    $response_data = array(
                        'status' => false,
                        'code' => 'null_attribute',
                        'message' => 'Please enter the size of the DVD',
                    );

                    http_response_code(400);
                    echo json_encode($response_data);
                    return;
                }

                $size = $data['size'];
                $obj = new DVD($sku, $name, $price, $type, $size);
                break;

            case 'book':

                if (!isset($data['weight'])) {
                    $response_data = array(
                        'status' => false,
                        'code' => 'null_attribute',
                        'message' => 'Please enter the weight of the book',
                    );

                    http_response_code(400);
                    echo json_encode($response_data);
                    return;
                }
                $weight = $data['weight'];
                $obj = new Book($sku, $name, $price, $type, $weight);
                break;

            case 'furniture':

                if (!isset($data['width'], $data['height'], $data['length'])) {
                    $response_data = array(
                        'status' => false,
                        'code' => 'null_attribute',
                        'message' => 'Please enter the dimensions of the furniture',
                    );

                    http_response_code(400);
                    echo json_encode($response_data);
                    return;
                }

                $width = $data['width'];
                $height = $data['height'];
                $length = $data['length'];
                $obj = new Furniture($sku, $name, $price, $type, $width, $height, $length);
                break;

            default:
                $response_data = array(
                    'status' => false,
                    'code' => "invalid_type",
                    'message' => "Please select a valid product type",
                );

                http_response_code(400);
                echo json_encode($response_data);
                return;
        }

        // Save object to database
        $result = $obj->save($connection);

        if ($result == 1) {
            http_response_code(201);

            // Generate a JSON response indicating success
            $response_data = array(
                'status' => true,
                'code' => "success",
                'message' => "Product successfully created",
            );

            echo json_encode($response_data);
        } else if ($result == 23000) {
            $response_data = array(
                'status' => false,
                'code' => 'duplicate',
                'message' => 'This product already exists',
            );

            http_response_code(409);
            echo json_encode($response_data);
        } else {
            $response_data = array(
                'status' => false,
                'code' => 'unknown_error',
                'message' => 'An unknown error occurred',
            );

            http_response_code(500);
            echo json_encode($response_data);
        }


        break;

    case 'DELETE':

        if (!isset($data['skus'])) {

            $response_data = array(
                'status' => false,
                'code' => 'null_data',
                'message' => 'Missing input data. Please select some products',
            );

            http_response_code(400);
            echo json_encode($response_data);
            return;
        }

        try {
            if (is_array($data['skus']) && count($data['skus']) > 0) {
                Product::deleteProducts(join(',', $data['skus']), $connection);
            } else {
                http_response_code(400);
                echo json_encode(array(
                    'status' => false,
                    'code' => 'bad_request',
                    'message' => 'You must select at least one product for delete',
                ));
                return;
            }
        } catch (\Throwable $th) {

            http_response_code(500);
            echo json_encode(array(
                'status' => false,
                'code' => 'interval_error',
                'message' => 'There is an interval error occured while mass delete.',
            ));

            return;
        }

        http_response_code(200);
        echo json_encode(array(
            'status' => true,
            'code' => 'success',
            'message' => 'Successfuly deleted',
        ));

        return;

    default:
        break;
}
