<?php

class Database
{

    private static $connection;

    public static function getConnection()
    {
        if (!isset(self::$connection)) {
            self::$connection = new mysqli("localhost", "root", "skg123", "skillshop_db", 3306);

            if (self::$connection->connect_error) {
                die("connection failed:" . self::$connection->connect_error);
            }
        }
        return self::$connection;
    }


    public static function iud($query, $types, $params)
    {
        $conn = self::getConnection();
        $statement = $conn->prepare($query);
        $statement->bind_param($types, ...$params);
        // variable arguiments kiyala kiynawa ara dot thunata 

        $result = $statement->execute();
        $statement->close();
        return $result;
    }

    public static function search($query, $types = null, $params = [])
    {
        $conn = self::getConnection();
        $statement = $conn->prepare($query);

        if ($types != null && !empty($params)) {
            $statement->bind_param($types, ...$params);
        }

        $statement->execute();
        $result = $statement->get_result();
        $statement->close();
        return $result;
    }
}
