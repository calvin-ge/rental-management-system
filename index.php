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

    <div class="bg-secondary text-white py-5"> 
        <div class="container tex-center py-5">
            <h1 class="display-4">Bicycle Rental Shop</h1>
            <nav class="nav">
                <a class="nav-link text-white" href="index.php">Home</a>
                <a class="nav-link text-white" href="about.php">About</a>
                <a class="nav-link text-white" href="contact.php">Contact</a>
                <a class="nav-link text-white" href="login.php">Login</a>
                <a class="nav-link text-white" href="register.php">Register</a>
            </nav>
        </div>

    </div>

   


        
        <p>Welcome to our bicycle rental shop! We offer a wide range of bicycles for rent.</p>
        <a href="login.php" class="btn btn-primary" "mb-4">Login</a>
        <a href="register.php" class="btn btn-secondary">Register</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>


<!--   <section class="hero">
  <div class="hero-content">
    <h1>Welcome to Our Platform</h1>
    <p>The easiest way to manage your tasks</p>
    <button class="cta-button">Get Started →</button>
  </div>
  <div class="hero-image">
    <img src="hero-image.jpg" alt="Product demo">
  </div>
</section>