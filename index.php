<?php
include 'config/connection.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body>

<nav class="navbar navbar-dark bg-dark sticky-top shadow-sm"> 
        <div class="container tex-center py-5">
            <h1 class="display-4 text-white">Rent Cycle</h1>
            <nav class="nav">
                <a class="nav-link text-white" href="index.php">Home</a>
                <a class="nav-link text-white" href="about.php">About</a>
                <a class="nav-link text-white" href="contact.php">Contact</a>
                <a class="nav-link text-white" href="login.php">Login</a>
                <a class="nav-link text-white" href="register.php">Register</a>
            </nav>
        </div>
       
</nav>



    <div class="container text-center my-5">
        <h2>Welcome to Our Bicycle Rental Shop!</h2>
        <p class="lead">We offer a wide range of bicycles for rent.</p>
        <div class="mt-4">
            <a href="login.php" class="btn btn-primary" "mb-4">Login</a>
            <a href="register.php" class="btn btn-secondary">Register</a>
    
        
        </div>
    </div>


<footer class="bg-dark text-white-50 text-center py-3 mt-4">
    <div class="container">
        <small>&copy; 2026 CycleRent - Bicycle Rental System | Late fees support cycling education</small>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
    