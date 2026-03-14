<?php

if (!isset($_SESSION)) {
    session_start();
}

require_once __DIR__ . "/../db/connection.php";

if (isset($_COOKIE["skillshop_remember"]) && isset($_COOKIE["skillshop_user_id"])) {
    $rememberToken = $_COOKIE["skillshop_remember"];
    $userId = intval($_COOKIE["skillshop_user_id"]);


    $tokenResult = Database::search(
        "SELECT `token_hash` FROM `remember_tokens` WHERE `user_id`=? AND `expiry` > NOW() ORDER BY `created_at` DESC LIMIT 1",
        "i",
        [$userId]
    );

    if ($tokenResult && $tokenResult->num_rows > 0) {
        $tokenRecord = $tokenResult->fetch_assoc();

        if (password_verify($rememberToken, $tokenRecord["token_hash"])) {

            //User Details
            $userResult = Database::search(
                "SELECT u.`id`,u.`fname`,u.`lname`,u.`email`,u.`active_account_type_id`,at.`name`
    FROM `user`u 
    JOIN `account_type` at ON u.`active_account_type_id`=at.`id` 
    WHERE u.`id`= ?",
                "i",
                [$userId]
            );

            if ($userResult && $user = $userResult->fetch_assoc()) {
                //Create Session
                $_SESSION["user_id"] = $user["id"];
                $_SESSION["user_name"] = $user["fname"] . " " . $user["lname"];
                $_SESSION["user_email"] = $user["email"];
                $_SESSION["account_type_id"] =  $user["active_account_type_id"];
                $_SESSION["active_account_type"] = $user["name"];
                $_SESSION["logged_in"] = true;


                Database::iud(
                    "DELETE FROM `remember_tokens` WHERE `user_id`= ? AND `token_hash`!=?",
                    "is",
                    [$userId, $tokenRecord["token_hash"]]
                );

                return true; //Successfully authenticated via remember token
            }
        }
    }


    setcookie("skillshop_remember", "", time() - 3600, "/");
    setcookie("skillshop_user_id", "", time() - 3600, "/");
    setcookie("skillshop_user_email", "", time() - 3600, "/");
}
