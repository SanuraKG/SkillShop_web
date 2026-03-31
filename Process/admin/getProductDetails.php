<?php
session_start();


require_once "../../db/connection.php";
header('Content-Type: application/json');

if (!isset($_SESSION["admin_logged_in"])) {
    echo json_encode(["success" => false, "message" => "Unauthrized"]);
    exit();
}

$id = isset($_GET["id"]) ? $_GET["id"] : "";

if (empty($id)) {
    echo json_encode(["success" => false, "message" => "Product ID is required"]);
    exit();
}


$query = "SELECT p.*, c.`name` AS `category_name` , u.`fname`, u.`lname`,u.`email` AS `seller_email`
FROM `product` p
JOIN `category` c ON p.`category_id`=c.`id`
JOIN `user` u ON p.`seller_id`=u.`id`
WHERE p.`id`=?";

$res = Database::search($query, "i", [$id]);


if ($res && $res->num_rows > 0) {
    $product = $res->fetch_assoc();
    echo json_encode(["success" => true, "product" => $product]);
} else {
    echo json_encode(["success" => false, "message" => "Product not found !"]);
}
