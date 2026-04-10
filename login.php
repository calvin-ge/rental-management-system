<?php
include "config/connection.php";
session_start();

if(isLoggedIn()) {
    if (getUserRole() === "admin") {
        header("Location: admin/dashboard.php");
    } else {
        header("Location: user/dashboard.php");
    }
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if (empty($_POST["username"]) || empty($_POST["password"])) {
        $error = "Both fields are required.";
    } else {
        $username = $_POST["username"];
        $password = md5($_POST["password"]);
        $role = $_POST["role"];

        $sql = "SELECT * FROM users 
            WHERE username='$username' AND password='$password' AND role='$role'";
        $result = $db->query($sql);

        //to check if a user is found
        if(mysqli_num_rows($result) == 1) {
            $user = mysqli_fetch_assoc($result);

            //code to store in sessiion
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username']; 
            $_SESSION['fullname'] = $user['fullname'];
            $_SESSION['role'] = $user['role'];

        //redirecting user to the appropriate dashboard
        if ($user['role'] === 'admin') {
            header("Location: admin/dashboard.php");
        } else {
            header("Location: user/dashboard.php");
        }   
        exit();

    } else {
        $error = "Incorrect username, password, or role.";
    }
    }
}

?>



<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset ="UTF-8">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

        <title>Login Page</title>
    </head>
    <body>
        <div class="container-fluid border">
            <h2>Login</h2>
            

                
            <input type ="email" id="email" placeholder="Email">
            <input type="password" id="password" placeholder="Password">
            
            <div class="col">
                <button onclick="">Login</button>


            </div>
            
            

        
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    </body>
</html>

