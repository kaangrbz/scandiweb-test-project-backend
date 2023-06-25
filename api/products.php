<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header('Content-Type: application/json');

include_once '../models/Database.php';
include_once '../models/DVD.php';
include_once '../models/Book.php';
include_once '../models/Furniture.php';

$db = new Database();
$connection = $db->getConnection();
$method = $_SERVER["REQUEST_METHOD"];

switch (strtoupper($method)) {
    case 'GET':

        echo json_encode(array(
            'success' => false,
            'code' => 'bad_request',
            'message' => 'Bad request. type parameter not found'
        ));
        return;

    case 'POST':

        $data = json_encode([]);

        try {
            $data = $db->getAllProducts();
        } catch (\Throwable $th) {
            $data = json_encode([]);
            
            echo json_encode(array(
                'success' => false,
                'code' => 'get_product',
                'message' => 'An error was occured while getting products',
                'data' => $data
            ));
            return;
        }

        echo json_encode(array(
            'success' => true,
            'code' => 'success',
            'message' => 'Success',
            'data' => $data
        ));

        break;

    case 'PUT':

        // Validate input data
        if (!isset($_POST['sku'], $_POST['name'], $_POST['price'], $_POST['type'])) {
            $response_data = array(
                'error' => true,
                'code' => "null_value",
                'message' => "Please enter all values",
            );

            http_response_code(400);
            echo json_encode($response_data);
            return;
        }

        $sku = $_POST['sku'];
        $name = $_POST['name'];
        $price = filter_input(INPUT_POST, 'price', FILTER_VALIDATE_FLOAT);
        $type = $_POST['type'];

        if (!$price) {
            $response_data = array(
                'error' => true,
                'code' => "invalid_price",
                'message' => "Please enter a valid price",
            );

            http_response_code(400);
            echo json_encode($response_data);
            return;
        }

        $max_sku_length = 50; // Set max length for SKU
        if (strlen($sku) > $max_sku_length) {
            $response_data = array(
                'error' => true,
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
                if (!isset($_POST['size'])) {
                    $response_data = array(
                        'error' => true,
                        'code' => 'null_attribute',
                        'message' => 'Please enter the size of the DVD',
                    );

                    http_response_code(400);
                    echo json_encode($response_data);
                    return;
                }

                $size = $_POST['size'];
                $obj = new DVD($sku, $name, $price, $type, $size);
                break;

            case 'book':

                if (!isset($_POST['weight'])) {
                    $response_data = array(
                        'error' => true,
                        'code' => 'null_attribute',
                        'message' => 'Please enter the weight of the book',
                    );

                    http_response_code(400);
                    echo json_encode($response_data);
                    return;
                }
                $weight = $_POST['weight'];
                $obj = new Book($sku, $name, $price, $type, $weight);
                break;

            case 'furniture':

                if (!isset($_POST['width'], $_POST['height'], $_POST['length'])) {
                    $response_data = array(
                        'error' => true,
                        'code' => 'null_attribute',
                        'message' => 'Please enter the dimensions of the furniture',
                    );

                    http_response_code(400);
                    echo json_encode($response_data);
                    return;
                }

                $width = $_POST['width'];
                $height = $_POST['height'];
                $length = $_POST['length'];
                $obj = new Furniture($sku, $name, $price, $type, $width, $height, $length);
                break;

            default:
                $response_data = array(
                    'error' => true,
                    'code' => "invalid_type",
                    'message' => "Please select a valid product type",
                );

                http_response_code(400);
                echo json_encode($response_data);
                return;
        }

        // Save object to database
        $result = $obj->save($db);

        if ($result == 1) {
            http_response_code(201);

            // Generate a JSON response indicating success
            $response_data = array(
                'success' => true,
                'code' => "success",
                'message' => "Product successfully created",
            );

            echo json_encode($response_data);
        } else if ($result == 23000) {
            $response_data = array(
                'error' => true,
                'code' => 'duplicate',
                'message' => 'This product already exists',
            );

            http_response_code(409);
            echo json_encode($response_data);
        } else {
            $response_data = array(
                'error' => true,
                'code' => 'unknown_error',
                'message' => 'An unknown error occurred',
            );

            http_response_code(500);
            echo json_encode($response_data);
        }


        break;

    case 'DELETE':
        if (!isset($_DELETE['skus'])) {

            $response_data = array(
                'error' => true,
                'code' => 'null_data',
                'message' => 'Missing input data. Please select some products',
            );

            http_response_code(400);
            echo json_encode($response_data);
            return;
        }
        
        http_response_code(200);
        echo json_encode(array(
            'error' => true,
            'code' => 'null_data',
            'message' => 'Missing input data. Please select some products',
        ));
        
        return;

        break;
    default:
        $response_data = array(
            'error' => true,
            'code' => 'unsupported_method',
            'message' => 'The requested HTTP method is not supported',
        );

        http_response_code(405);
        echo json_encode($response_data);
        break;
}
