<?php
require_once __DIR__ . '/../config/connection.php';
requireAdmin();


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = md5($_POST['password']);
    
    
    
    $sql = "INSERT INTO users (fullname, email, username, password, role)
            VALUES ('$fullname', '$email', '$username', '$password', 'user')";


    if ($db->query($sql)) {
        header('Location: dashboard.php');
    } else {
        echo "Error: " . $db->error;
    }

}


?>


