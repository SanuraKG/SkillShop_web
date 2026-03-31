<?php
session_start();


require_once "../../db/connection.php";
header('Content-Type: application/json');

if (!isset($_SESSION["admin_logged_in"])) {
    echo json_encode(["success" => false, "message" => "Unauthrized"]);
    exit();
}
$name = isset($_POST["name"]) ? trim($_POST["name"]) : "";

if (empty($name)) {
    echo json_encode(["success" => false, "message" => "Category name is required!"]);
    exit();
}

//Check if the category is already exists
$check = Database::search("SELECT * FROM `category` WHERE `name`=?", "s", [$name]);
if ($check && $check->num_rows > 0) {
    echo json_encode(["success" => false, "message" => "Category is already exits"]);
    exit();
}

//Insert New Category
Database::iud("INSERT INTO `category` (`name`) VALUES (?)", "s", [$name]);

echo json_encode(["success" => true, "message" => "Category registerd successfully!"]);
