<?php

require_once __DIR__ . '/../config/connection.php';
requireUser();


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $bike_id = $_POST['bike_id'];
    $start_date = $_POST['start_date'];
    $return_date = $_POST['return_date'];
    $quantity = $_POST['quantity'];


    $bike = $db->query("SELECT * FROM bicycles WHERE bike_id = $bike_id")->fetch_assoc();

    $start = new DateTime($start_date);
    $end = new DateTime($return_date);
    $days = $start->diff($end)->days;
    if ($days == 0) $days = 1;
    %total = $days * $bike['price_per_day'] * $quantity;   

    $active_count = $db->query("SELECT COUNT(*) as count FROM rentals WHERE user_id = $user_id AND rental_status = 'active'")->fetch_assoc()['count'];

    if ($active_count >= 2) {
        $_SESSION['error'] = "You already have 2 active rentals.";
        header('Location: dashboard.php');
        exit();
    }

    $sql = "INSERT INTO rentals (user_id, bike_id, rent_start_date, expected_return_date, quantity_rented, total_amount) 
            VALUES ($user_id, $bike_id, '$start_date', '$return_date', $quantity, $total)";
    
    if ($db->query($sql)) {
        $new_stock = $bike['quantity'] - $quantity;
        $db->query("UPDATE bicycles SET quantity = $new_stock WHERE bike_id = $bike_id");
        $_SESSION['Success'] = "Bike rented Successfully!";

    } else {
        $_SESSION['error'] = "Error: " . $db->error;
    }
    header('Location: dashboard.php');
}
?>