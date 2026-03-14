<?php

header('Content-Type: application/json');

require "../db/connection.php";

if(isset($_POST["country_id"])){

$countryId = intval($_POST["country_id"]);

$citiesResult = Database::search(
    "SELECT `id`,`name` FROM `city` WHERE `country_id`=? ORDER BY `name`",
    "i",
    [$countryId]

);

$cities = [];
if($citiesResult && $citiesResult->num_rows > 0){
    while ($city = $citiesResult->fetch_assoc()){
        $cities[] = $city;
    }
}
  echo json_encode([
        "success"=> true,
        "cities" => $cities
    ]);

   
}else{
    echo json_encode([
        "success"=> false,
        "cities" => []
    ]);
}














?>