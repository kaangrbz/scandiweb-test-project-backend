<?php

header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json');

include_once '../models/Database.php';
include_once '../models/DVD.php';
include_once '../models/Book.php';
include_once '../models/Furniture.php';


$db = new Database();
$connection = $db->getConnection();


if (!isset($_POST['sku'], $_POST['name'], $_POST['price'], $_POST['type'])) {
    $response_data = array(
        'error' => true,
        'code' => "null_value",
        'message' => "Please enter all values",
    );

    echo json_encode($response_data);
    return;
}

$sku = $_POST['sku'];
$name = $_POST['name'];
$price = $_POST['price'];
$type = $_POST['type'];

switch ($type) {
    case 'dvd':
        if (!isset($_POST['size'])) {
            echo json_encode(array(
                'error' => true,
                'code' => 'null_attribute',
                'message' => 'Pleae enter attribute of product',
            ));
            return;
        }

        $size = $_POST['size'];
        $obj = new DVD($sku, $name, $price, $type, $size);
        break;

    case 'book':

        if (!isset($_POST['weight'])) {
            echo json_encode(array(
                'error' => true,
                'code' => 'null_attribute',
                'message' => 'Pleae enter attribute of product',
            ));
            return;
        }
        $weight = $_POST['weight'];
        $obj = new Book($sku, $name, $price, $type, $weight);
        break;
        
    case 'furniture':

        if (!isset($_POST['width'], $_POST['height'], $_POST['length'])) {
            echo json_encode(array(
                'error' => true,
                'code' => 'null_attribute',
                'message' => 'Pleae enter all attributes of product',
            ));
            return;
        }

        $width = $_POST['width'];
        $height = $_POST['height'];
        $length = $_POST['length'];
        $obj = new Furniture($sku, $name, $price, $type, $width, $height, $length);
        break;
}

if (!isset($obj)) {
    $response_data = array(
        'error' => true,
        'code' => "unexpected_category",
        'message' => "Please select category"
    );

    echo json_encode($response_data);
    return;
}

$result = $obj->save($db);

if ($result == 1) {
    http_response_code(200);

    // Generate a JSON response indicating the error
    $response_data = array(
        'success' => true,
        'code' => "success",
        'message' => "Successfully created",
    );

    echo json_encode($response_data);
} else if ($result == 23000) {
    $response_data = array(
        'error' => true,
        'code' => 'duplicacte',
        'message' => 'This product is already added',
    );

    echo json_encode($response_data);
}
