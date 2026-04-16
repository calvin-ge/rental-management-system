<?php

require_once __DIR__ . '/../config/connection.php';
requireAdmin();

if (isset($_GET['id']) && isset($_GET['action'])) {
    $bike_id = (int)$_GET['id'];
    $action = $_GET['action'];
    
       $result = $db->query("SELECT quantity FROM bicycles WHERE bike_id = $bike_id");
    $bike = $result->fetch_assoc();
    


    if ($bike) {
        $current_qty = $bike['quantity'];
        

        if ($action == 'increase') {


            $new_qty = $current_qty + 1;
            
            $_SESSION['success'] = "Quantity increased from $current_qty to $new_qty";
        } elseif ($action == 'decrease') {


            if ($current_qty > 0) {
                $new_qty = $current_qty - 1;
                $_SESSION['success'] = "Quantity decreased from $current_qty to $new_qty";


            } else {
                $_SESSION['error'] = "Cannot decrease below zero!";
                header('Location: dashboard.php');
                exit();
            }
            
        } else {
            $_SESSION['error'] = "Invalid action!";
            header('Location: dashboard.php');
            exit();
        }
        
        
        $db->query("UPDATE bicycles SET quantity = $new_qty WHERE bike_id = $bike_id");


    } else {
        $_SESSION['error'] = "Bike not found!";
    }
}



header('Location: dashboard.php');
exit();
?>