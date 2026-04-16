<?php
require_once '../config/connection.php';
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $category = $_POST['category'];
    $serial_number = $_POST['serial_number'];
    $price_per_day = $_POST['price_per_day'];
    $quantity = $_POST['quantity'];
    $serial = "BIKE" . time() . rand(100, 999);

    $sql = "INSERT INTO bicycles (name, category, serial_number, price_per_day, quantity) 
            VALUES ('$name', '$category', '$serial', $price_per_day, $quantity)";
    
    if ($db->query($sql)) {
        header('Location: dashboard.php');
    } else {
        echo "Error: " . $db->error;

    }
}
?>