<?php

require_once __DIR__ . '/../config/connection.php';
requireAdmin();

if (isset($_GET['id'])) {
    $bike_id = (int)$_GET['id'];
    

    $check = $db->query("SELECT name FROM bicycles WHERE bike_id = $bike_id");
    if ($check->num_rows > 0) {
        $bike = $check->fetch_assoc();
        
        
        if ($db->query("DELETE FROM bicycles WHERE bike_id = $bike_id")) {
            $_SESSION['success'] = "Bike '{$bike['name']}' has been deleted!";
        } else {
            $_SESSION['error'] = "Error deleting bike!";
        }
    } else {
        $_SESSION['error'] = "Bike not found!";
    }
}

header('Location: dashboard.php');
exit();
?>