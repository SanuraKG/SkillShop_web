<?php
if (!isset($_SESSION))
    session_start();

require_once "../db/connection.php";

header("Content-Type:application/json");

//Auth Check 
if (!isset($_SESSION["logged_in"]) || !$_SESSION["logged_in"] || ($_SESSION["active_account_type"] ?? "") != "buyer") {
    echo json_encode(["success" => false, "message" => "Unauthorized"]);
    exit;
}

if (!isset($_POST["order_id"])) {
    echo json_encode(["success" => false, "message" => "message order ID !"]);
    exit;
}


$userId = intval($_SESSION["user_id"] ?? 0);
$payHereOrderId = $_POST["order_id"];



//fetch cart items
$cartItemsQ = Database::search(
    "SELECT c.`product_id` AS `pid`,p.`price`,p.`seller_id`,sa.`city_id` AS `seller_city_id`
    FROM `cart` c
    JOIN `product` p ON c.`product_id`=p.`id`
    JOIN `user` u ON p.`seller_id`=u.`id`
    LEFT JOIN `user_profile` up ON u.`id`=up.`user_id`
    LEFT JOIN `address` sa ON up.`address_id`= sa.`id`
    WHERE c.`user_id`=?",
    "i",
    [$userId]
);


if (!$cartItemsQ || $cartItemsQ->num_rows == 0) {
    echo json_encode(["success" => false, "message" => "cart is empty!"]);
    exit;
}


$buyerCityQ = Database::search(
    "SELECT a.`city_id` FROM `user_profile` up JOIN `address` a ON up.`address_id`=a.`id`
    WHERE up.`user_id`=?",
    "i",
    [$userId]
);

$buyerCityId = ($buyerCityQ && $buyerCityQ->num_rows > 0) ? $buyerCityQ->fetch_assoc()["city_id"] : 0;


$subtotal = 0;
$totalDeliveryFee = 0;
$sellerInCart = [];
$items = [];


while ($item = $cartItemsQ?->fetch_assoc()) {
    $items[] = $item;
    $price = floatval($item["price"]);
    $subtotal += $price;

    $sellerId = $item["seller_id"];
    if (!isset($sellerInCart[$sellerId])) {
        $deliveryFee = ($item["seller_city_id"] == $buyerCityId && $buyerCityId != 0) ? 200 : 500;
        $totalDeliveryFee += $deliveryFee;
        $sellerInCart[$sellerId] = $deliveryFee;
    }
}


$totalAmount = $subtotal + $totalDeliveryFee;
$date = date("Y-m-d H:i:s");

//Insert Order
if (!empty($items)) {
    $firstItem = $items[0];
    $orderInserted = Database::iud(
        "INSERT INTO `order` (`order_id`,`user_id`,`product_id`,`total_amount`,`payment_status`) VALUES (?,?,?,?,'completed')",
        "siid",
        
        [$payHereOrderId, $userId, $firstItem["pid"], $totalAmount]
    );
}

//insert Invoice

$invoiceInsertID = Database::iud(
    "INSERT INTO `invoice` (`order_order_id`,`user_id`,`subtotal`,`delivery_fee`,`total`,`date`) VALUES (?,?,?,?,?,?)",
    "siddds",
    [$payHereOrderId, $userId, $subtotal, $totalDeliveryFee, $totalAmount, $date]
);


if (!$invoiceInsertID) {
    echo json_encode(["success" => false, "message" => "Invoice Insert Error "]);
    exit;
}

$conn = Database::getConnection();
$invoiceID = $conn->insert_id;

//Insert Invoice Item
foreach ($items as $item) {
    $itemInserted = Database::iud(
        "INSERT INTO `invoice_item` (`invoice_id`,`product_id`,`price`,`seller_id`) VALUES (?,?,?,?)",
        "iidi",
        [$invoiceID, $item["pid"], $item["price"], $item["seller_id"]]
    );
    if (!$itemInserted) {
        echo json_encode(["success" => false, "message" => "Invoice Item Insert Error "]);
        exit;
    }
}


//Empty Cart
Database::iud("DELETE FROM `cart` WHERE `user_id`=?", "i", [$userId]);

echo json_encode(["success" => true, "invoice_id" => $payHereOrderId]);
