<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "bike_shop";


// to Create connection
$db = new mysqli($servername, $username, $password, $dbname);

if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}


// to check connection
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);

}

?>