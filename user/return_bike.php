<?php

require_once __DIR__ . '/../config/connection.php';
requireUser();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $rental_id = $_POST['rental_id'];
    $return_date = date('Y-m-d');
   
   $rental = $db->query("
        SELECT r.*, b.price_per_day, b.bike_id, b.name 
        FROM rentals r 
        JOIN bicycles b ON r.bike_id = b.bike_id 
        WHERE r.rental_id = $rental_id
    ")->fetch_assoc(); 

    $penalty = calculateLateFee($rental['expected_return_date'], $return_date, $rental['price_per_day']);
    $late_fee = $penalty['fee'];
    $days_late = $penalty['days_late'];

     $db->query("UPDATE rentals SET actual_return_date = '$return_date', rental_status = 'returned', late_fee = $late_fee WHERE rental_id = $rental_id");
    $db->query("UPDATE bicycles SET quantity = quantity + {$rental['quantity_rented']} WHERE bike_id = {$rental['bike_id']}");


    if ($late_fee > 0) {
        $charity = "World Bicycle Relief";
        $db->query("INSERT INTO charity_donations (rental_id, user_id, days_late, amount, charity_name) 
                      VALUES ($rental_id, {$rental['user_id']}, $days_late, $late_fee, '$charity')");
        $_SESSION['success'] = "Bike returned! Late fee of $$late_fee donated to $charity.";
    } else {
        $_SESSION['success'] = "Bike returned on time!";
    }
    
    header('Location: dashboard.php');
}