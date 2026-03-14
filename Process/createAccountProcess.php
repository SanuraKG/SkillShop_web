<?php
require "../DB/connection.php";


$fname = $_POST["fname"];
$lname = $_POST["lname"];
$email = $_POST["email"];
$password = $_POST["password"];
$pass_confirm = $_POST["pass_confirm"];
$accounType = $_POST["accounType"];
$termsConditions = $_POST["termsConditions"];

if (empty($fname)) {
    echo "Please enter first name";
} else if (empty($lname)) {
    echo "Please enter the last name";
} else if (empty($email)) {
    echo "Please enter the email address";
} else if (strlen($email) >= 150) {
    echo "Email must be less than 150 characters";
} else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "Invalid Email Address.";
} else if (empty($password)) {
    echo "Please enter the password";
} else if ($password != $pass_confirm) {
    echo "Passwords do not match";
} else if (strlen($password) < 8 || strlen($password) > 20) {
    echo "password length should be between 8 and 20";
} else if (!$termsConditions) {
    echo "Please read and check I agree to the Terms & Conditions";
} else if (empty($accounType)) {
    echo "Please select account type";
} else {

    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    //CHECK ACCOUNT TYPE from database
    $result = Database::search("SELECT `id` FROM `account_type` WHERE `name`=?", "s", [$accounType]);

    if ($result && $row = $result->fetch_assoc()) {
        $accounTypeId = $row["id"];
    } else {
        echo "Invalid account type";
        exit;
    }

    //Check email already exists 

    $check = Database::search("SELECT `id` FROM `user` WHERE `email`=? ", "s", [$email]);
    if ($check && $check->num_rows > 0) {
        echo "Email is already registerd";
    } else {

        //Inserted user into Database user table

        $insertUser = Database::iud(
            "INSERT INTO `user` (`fname`,`lname`,`email`,`password_hash`,`active_account_type_id`)VALUES(?,?,?,?,?)",
            "ssssi",
            [$fname, $lname, $email, $passwordHash, $accounTypeId]
        );

        if ($insertUser) {

            //get new user id
            $user_id = Database::getConnection()->insert_id;

            $insertRole = Database::iud(
                "INSERT INTO `user_has_account_type`(`user_id`,`account_type_id`) VALUES (? , ?)",
                "ii",
                [$user_id, $accounTypeId]
            );

            echo($insertRole ? "success" : "Error assigning account type");
        }else{
            echo "Error creating user account";
        }
    }
}
