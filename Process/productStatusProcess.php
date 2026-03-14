<?php

if (!isset($_SESSION)) {
    session_start();
}
require_once "../db/connection.php";

header("Content-Type: application/json");

// Check Authentication
if (!isset($_SESSION["logged_in"]) || $_SESSION["logged_in"] != true || $_SESSION["active_account_type"] != "seller") {
    echo json_encode(["success" => false, "message" => "Unauthorized Access"]);
    http_response_code(401);
    exit;
}

$userId = $_SESSION["user_id"];
$productId = intval($_POST["productId"] ?? 0);

if ($productId <= 0) {
    echo json_encode(["success" => false, "message" => "Invalid Product ID!"]);
    http_response_code(400);
    exit;
}

// Fetch the Current Product Status
$statusResult = Database::search(
    "SELECT `status` FROM `product` WHERE `id` =? AND `seller_id` =?",
    "ii",
    [$productId, $userId]
);

if (!$statusResult || $statusResult->num_rows == 0) {
    echo json_encode(["success" => false, "message" => "Product Not Found Or Unauthorized"]);
    http_response_code(403);
    exit;
}

$currentStatus = $statusResult->fetch_assoc()["status"];

// toggle Status
$newStatus = ($currentStatus == "active") ? "inactive" : "active";

// Update Product Status
$result = Database::iud(
    "UPDATE `product` SET `status`=? WHERE `id`=? AND `seller_id`=?",
    "sii",
    [$newStatus, $productId, $userId]
);

if ($result) {
    echo json_encode([
      "success" => true,
      "message" => "Product Stauts Updates Sucessfully",
      "newStatus" => $newStatus
    ]);
} else {
    echo json_encode(["success" => false, "message" => "Failed To Update Product Status"]);
    http_response_code(500);
}