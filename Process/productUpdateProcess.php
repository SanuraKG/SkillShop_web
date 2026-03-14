<?php

if (!isset($_SESSION)) {
    session_start();
}

header("Content-Type: text/plain");
require_once "../db/connection.php";

$loggedIn = isset($_SESSION["logged_in"]) ? $_SESSION["logged_in"] : false;
$userRole = isset($_SESSION["active_account_type"]) ? $_SESSION["active_account_type"] : "";

if (!$loggedIn || strtolower($userRole) != "seller") {

    echo "Unauthorized";

    exit;
}

$userId = isset($_SESSION["user_id"]) ? $_SESSION["user_id"] : "";

$data = [
    "id" => intval($_POST["productId"] ?? ""),
    "title" => trim($_POST["productTitle"] ?? ""),
    "description" => trim($_POST["description"] ?? ""),
    "category" => intval($_POST["category"] ?? 0),
    "price" => floatval($_POST["price"] ?? 0),
    "level" => trim($_POST["level"] ?? ""),
    "status" => trim($_POST["status"] ?? "")


];


$validation = [
    ["cond" => strlen($data["title"]) > 0 && strlen($data["title"]) <= 150, "msg" => "Title Required, max 150 chars"],
    ["cond" => strlen($data["description"]) > 0 && strlen($data["description"]) <= 1000, "msg" => "Description required, max 1000 chars"],
    ["cond" => ($data["category"]) > 0, "msg" => "Select valid category"],
    ["cond" => ($data["price"]) > 0, "msg" => "Price must be greater than 0"],
    ["cond" => in_array($data["level"], ["Beginner", "Intermediate", "Advanced"]), "msg" => "Invalid level"],
    ["cond" => in_array($data["status"], ["active", "inactive"]), "msg" => "Invalid Status"]

];


foreach ($validation as $v) {
    if (!$v["cond"]) {
        echo $v["msg"];
        exit;
    }
}

$catCheck = Database::search("SELECT `id` FROM `category` WHERE `id`=?", "i", [$data["category"]]);
if (!$catCheck || $catCheck->num_rows == 0) {
    echo "Invalid Category";
    exit;
}

//Checking seller and image are realterd both and if they are same get the image
$oldProduct = Database::search("SELECT `image_url` FROM `product` WHERE `id`=? AND `seller_id`=?", "ii", [$data["id"], $userId]);
if (!$oldProduct && $oldProduct->num_rows == 0) {
    echo "Product not found";
    exit;
}

$oldImg = $oldProduct->fetch_assoc();
$imgUrl = $oldImg["image_url"];
$filePath = null;
//if image uploaded then update in database
if (isset($_FILES["productImage"]) && $_FILES["productImage"]["error"] == UPLOAD_ERR_OK) {

    $image = $_FILES["productImage"];

    $allowMimes = ["image/jpeg", "image/png", "image/webp", "image/gif"];
    $fInfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($fInfo, $image["tmp_name"]);
    finfo_close($fInfo);

    if (!in_array($mimeType, $allowMimes)) {
        echo "Invalid file type. only jpeg,png,gif and webp are allowed";
        exit;
    }

    //Check the file size (5MB)
    $maxSize = 5 * 1024 * 1024;
    if ($image["size"] > $maxSize) {
        echo "Image size must be less than 5MB";
        exit;
    }

    //Create upload directory if not exists
    $uploadDir = __DIR__ . "/../uploades/products/";
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    //Generate unique file name
    $fileExtention = pathinfo($image["name"], PATHINFO_EXTENSION);
    $fileName = "product_" . $userId . "_" . time() . "_" . bin2hex(random_bytes(4)) .  "." . $fileExtention;
    $filePath = $uploadDir . $fileName;
    $imgUrl = "uploades/products/" . $fileName;

    //Move uploaded file
    if (!move_uploaded_file($image["tmp_name"], $filePath)) {
        echo "Failed to upload image .Please try again";
        exit;
    }
}


try {

    $result = Database::iud(
        "UPDATE `product` SET `category_id`=?,`title`=? ,`description`=?,`price`=?,`level`=?,`status`=?,`image_url`=?
        WHERE `id`=? AND `seller_id`=?",
        "issdsssii",
        [$data["category"], $data["title"], $data["description"], $data["price"], $data["level"], $data["status"], $imgUrl, $data["id"], $userId]

    );


    if ($result) {
//If success the database delete old image url
        if ($filePath && !empty($oldImg) && $oldImg["image_url"] != $imgUrl) {

            $oldPath = __DIR__ . "/../" . $oldImg["image_url"];
            if (file_exists($oldPath)) @unlink($oldPath);
        }
        echo "success";
    } else {
        //Delete newly uploadede image
        if ($filePath && file_exists($filePath)) @unlink($filePath);
        echo "Update Failed";
    }
} catch (Exception $e) {
    //if systme crash delete newly uploaded image
    if ($filePath && file_exists($filePath)) @unlink($filePath);
    echo "Error:" . $e->getMessage();
}
