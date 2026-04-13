<?php

if(session_status() === PHP_SESSION_NONE) {
    session_start();
}

include  '../config/connection.php';  

requireAdmin();

$total_bikes = 0;
$bikes_result = $db->query("SELECT SUM(quantity) AS total FROM bicycles");
if ($bikes_result && $bikes_result->num_rows > 0) {
    $row = $bikes_result->fetch_assoc();
    $total_bikes = $row['total'] ?? 0;
}


$total_users = 0;
$users_result = $db->query("SELECT COUNT(*) AS total FROM users");
if ($users_result && $users_result->num_rows > 0) {
    $row = $users_result->fetch_assoc();
    $total_users = $row['total'] ?? 0;
}

$total_donated = 0;
$donation_result = $db->query("SELECT SUM(amount) as total FROM charity_donations");
if ($donation_result && $donation_result->num_rows > 0) {
    $row = $donation_result->fetch_assoc();
    $total_donated = $row['total'] ?? 0;
}

$bikes = $db->query("SELECT * FROM bicycles ORDER BY name ASC");

$users = $db->query("SELECT * FROM users WHERE role='user' ORDER BY uid ASC");

$rentals = $db->query("
    SELECT r.*, b.name AS bike_name, u.fullname 
    FROM rentals r 
    JOIN bicycles b ON r.bike_id = b.bike_id
    JOIN users u ON r.user_id = u.uid
    ORDER BY r.rental_date DESC
");

//Message handler
$message = $_SESSION['successful'] ?? '';
$error_message = $_SESSION['error'] ?? '';
unset($_SESSION['successful'], $_SESSION['error']);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Bike Rental Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body>
    <div class="bg-secondary text-white py-5"> 
        <div class="container-fluid">
            <span class="navbar-brand">Admin Dashboard</span>
            <nav class="nav"> 
                
                <a class="nav-link text-white" href="../index.php">Home</a>
                <a class="nav-link text-white" href="../about.php">About</a>
                <a class="nav-link text-white" href="../contact.php">Contact</a>
                <a class="nav-link text-white" href="../login.php">Login</a>
                <a class="nav-link text-white" href="../register.php">Register</a>
                <a href="../logout.php" class="btn btn-danger btn-sm">Logout</a>
                 <span class="text-white me-3">Welcome, <?php echo $_SESSION['fullname']; ?></span>
            
            </nav>  
        </div>

    </div>

     <nav class="navbar navbar-dark bg-dark">
    <div class="container-fluid">
        <span class="navbar-brand">
            <!-- <i class="bi "></i> Admin Dashboard -->
            
        </span>
        <div>
          
            
        </div>
    </div>
</nav>


<?php if ($message): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?php echo $message; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>

    <?php if ($error_message): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?php echo $error_message; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>


    <!--- Bike Inventory Overview -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-white bg-primary mb-3">
                <div class="card-body">
                    <h5 class="card-title">Total Bikes in Inventory</h5>
                    <p class="card-text display-4"><?php echo $total_bikes; ?></p>
                </div>
            </div>
        </div>


        <div class="col-md-3">
            <div class="card text-white bg-primary mb-3">
                <div class="card-body">
                    <h5 class="card-title">Total Bikes in Inventory</h5>
                    <p class="card-text display-4"><?php echo $total_bikes; ?></p>
                </div>
            </div>
        </div>
    

    
        <div class="col-md-3">
            <div class="card text-white bg-primary mb-3">
                <div class="card-body">
                    <h5 class="card-title">Total Bikes in Inventory</h5>
                    <p class="card-text display-4"><?php echo $total_bikes; ?></p>
                </div>
            </div>
        </div>
    

        
        <div class="col-md-3">
            <div class="card text-white bg-primary mb-3">
                <div class="card-body">
                    <h5 class="card-title">Total Bikes in Inventory</h5>
                    <p class="card-text display-4"><?php echo $total_bikes; ?></p>
                </div>
            </div>
        </div>
    </div>
