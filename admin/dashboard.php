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

$active_rentals = 0;
$rentals_result = $db->query("SELECT COUNT(*) as total FROM rentals WHERE rental_status='active'");
if ($rentals_result && $rentals_result->num_rows > 0) {
    $row = $rentals_result->fetch_assoc();
    $active_rentals = $row['total'];
}

$total_donated = 0;
$donation_result = $db->query("SELECT SUM(amount) as total FROM charity_donations");
if ($donation_result && $donation_result->num_rows > 0) {
    $row = $donation_result->fetch_assoc();
    $total_donated = $row['total'] ?? 0;
}


$bikes = $db->query("SELECT * FROM bicycles ORDER BY name ASC");

$users = $db->query("SELECT * FROM users WHERE role='user' ORDER BY uid ASC");

$rentals = $db->query("SELECT r.*, b.name AS bike_name, u.fullname 
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
                    <h5 class="card-title">Total Bikes</h5>
                    <p class="card-text display-4"><?php echo $total_bikes; ?></p>
                </div>
            </div>
        </div>


        <div class="col-md-3">
            <div class="card text-white bg-primary mb-3">
                <div class="card-body">
                    <h5 class="card-title">Customers</h5>
                    <p class="card-text display-4"><?php echo $total_users; ?></p>
                </div>
            </div>
        </div>
    

    
        <div class="col-md-3">
            <div class="card text-white bg-primary mb-3">
                <div class="card-body">
                    <h5 class="card-title">Active Rentals</h5>
                    <p class="card-text display-4"><?php echo $active_rentals; ?></p>
                </div>
            </div>
        </div>
    

        
        <div class="col-md-3">
            <div class="card text-white bg-primary mb-3">
                <div class="card-body">
                    <h5 class="card-title">Charity Donations</h5>
                    <p class="card-text display-4"><?php echo number_format($total_donated, 0); ?></p>
                </div>
            </div>
        </div>
    </div>

<!---  switch between tabs -->
    <ul class="nav nav-tabs mb-4" id="myTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="bikes-tab" data-bs-toggle="tab" data-bs-target="#bikesTab" type="button" role="tab">Bikes</button>
    </li>
            <li class="nav-item" role="presentation">
        <button class="nav-link" id="users-tab" data-bs-toggle="tab" data-bs-target="#usersTab" type="button" role="tab">Users</button>
    </li>
            <li class="nav-item" role="presentation">
        <button class="nav-link" id="rentals-tab" data-bs-toggle="tab" data-bs-target="#rentalsTab" type="button" role="tab">Rentals</button>
    </li>
</ul>

    <div class="tab-content">
        <div class="tab-pane fade show active" id="bikesTab">
            <div class="card">
                <div class="card-header">
                    <h4>Add New Bike</h4>
                </div>
                <div class="card-body">
                    <form action="add_bike.php" method="POST" class="row g-3">
                        <div class="col-md-3">
                            <input type="text" name="name" class="form-control" placeholder="Bicycle Name" required>
                        </div>
                        <div class="col-md-2">
                            <select name="category" class="form-select" required>
                                <option value="">Category</option>
                                <option value="Mountain">Mountain</option>
                                <option value="City">City</option>
                                <option value="Road">Road</option>
                                <option value="Electric">Electric</option>
                            </select>
                        </div>
                        
                        <div class="col-md-2">
                            <input type="number" step="0.01" name="price_per_day" class="form-control" placeholder="Price/Day" required>
                        </div>

                        <div class="col-md-2">
                            <input type="number" name="quantity" class="form-control" placeholder="Quantity" required >
                        
                        </div>
                        <div class="col-md-1">
                            <button type="submit" class="btn btn-primary">Add</button>
                        </div>
                    </form>
                </div>
            </div>

            <!--- Bike Inventory Table-->
            <div class="card mt-4">
                <div class="card-header">
                    <h4>Bike Inventory</h4> 
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-danger">
                                <tr>
                                    <th>S/N</th>    
                                    <th>Name</th>
                                    <th>Category</th>
                                    <th>Serial Number</th>
                                    <th>Price/Day</th>
                                    <th>Quantity</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($bike = $bikes->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $bike['bike_id']; ?></td>
                                    <td><?php echo $bike['name']; ?></td>
                                    <td><?php echo $bike['category']; ?></td>
                                    <td><?php echo $bike['serial_number']; ?></td>
                                    <td >£<?php echo number_format($bike['price_per_day'], 2); ?></td>
                                    <td>
                                        <?php if ($bike['quantity'] <= 5): ?>
                                            <span class="badge bg-danger"><?php echo $bike['quantity']; ?></span>
                                        <?php else: ?>
                                            <span class="badge bg-success"><?php echo $bike['quantity']; ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="delte_bike.php?id=<?php echo $bike["bike_id"]; ?>"
                                            class="btn btn-danger btn-sm"
                                            onclick="return confirm('Delete item?')">
                                            Delete
                                        </a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
        </div>

        <!--- user Management Tab -->
        <div class="tab-pane fade" id="usersTab">
            <div class="card">
                <div class="card-header">
                    <h5> New Customer</h5>
                </div>
                <div class="card-body">
                    <form action="add_user.php" method="POST" class="row g-3">
                        <div class="col-md-4">
                            <input type="text" name="fullname" class="form-control" placeholder="Full Name" required>
                        </div>
                        <div class="col-md-4">
                            <input type="email" name="email" class="form-control" placeholder="Email Address" required>
                        </div>
                        <div class="col-md-2">
                            <input type="text" name="username" class="form-control" placeholder="Username" required>
                        </div>
                        <div class="col-md-2">
                            <input type="password" name="password" class="form-control" placeholder="Password" required>
                        </div>
                        <div class="col-md-1">
                            <button type="submit" class="btn btn-primary">Add User</button>
                        </div>
                    </form>
                </div>
            </div>
        

            <div class="card mt-4">
                <div class="card_header">
                    <h5>Users</h5>
                </div>
                <div class ="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Username</th>
                                    <th>Full Name</th>
                                    <th>Email</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($user = $users->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $user['uid']; ?></td>
                                    <td><?php echo $user['username']; ?></td>
                                    <td><?php echo $user['fullname']; ?></td>
                                    <td><?php echo $user['email']; ?></td>
                                    
                                    <td>
                                        <a href="delete_user.php?id=<?php echo $user["uid"]; ?>"
                                            class="btn btn-danger btn-sm"
                                            onclick="return confirm('Delete user?')">
                                            Delete
                                        </a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!--- Rentals Tab -->

        <div class="tab-pane fade" id="rentalsTab">
            <div class="card">
                <div class="card-header">
                    <h5>Active Rentals</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Customer</th>
                                    <th>Bike</th>
                                    <th>Rental Date</th>
                                    <th>Return Date</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($rental = $rentals->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $rental['rental_id']; ?></td>
                                    <td><?php echo $rental['fullname']; ?></td>
                                    <td><?php echo $rental['bike_name']; ?></td>
                                    <td><?php echo date("d M Y", strtotime($rental['rental_date'])); ?></td>
                                    <td><?php echo date("d M Y", strtotime($rental['return_date'])); ?></td>
                                    <td>£<?php echo number_format($rental['total_amount'], 2); ?></td>

                                    <td>
                                        <?php if ($rental['rental_status'] == 'active'): ?>
                                            <span class="badge bg-warning">Active</span>
                                        <?php else: ?>
                                            <span class="badge bg-success">Returned</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
        </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Initialize Bootstrap tabs
    var triggerTabList = [].slice.call(document.querySelectorAll('.nav-tabs a'))
    triggerTabList.forEach(function (triggerEl) {
        var tabTrigger = new bootstrap.Tab(triggerEl)
        triggerEl.addEventListener('click', function (event) {
            event.preventDefault()
            tabTrigger.show()
        })
    })
</script>
</body>
</html>   


