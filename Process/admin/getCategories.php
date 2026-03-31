<?php
session_start();


require_once "../../db/connection.php";
header('Content-Type: application/json');

if (!isset($_SESSION["admin_logged_in"])) {
    echo json_encode(["success" => false, "message" => "Unauthrized"]);
    exit();
}

$res = Database::search("SELECT * FROM `category` ORDER BY `name` ASC");

$categories = [];
while ($row = $res->fetch_assoc()) {
    $categories[] = $row;
}

echo json_encode(["success" => true, "categories" => $categories]);
