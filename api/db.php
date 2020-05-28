<?php

include_once 'config/conf.php';

try {

	$conf = new Conf();

    $conn = new mysqli($conf::$host, $conf::$username, $conf::$password, $conf::$db_name);
    $conn->set_charset("utf8mb4");
} catch(Exception $e) {
    error_log($e->getMessage());
    exit('Error connecting to database'); //Should be a message a typical user could understand
}