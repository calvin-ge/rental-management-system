<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/connection.php';

//  to check the logi statusts
requireAdmin();

//stats
$total_bikes = $db->query("SELECT SUM(quantity) as total FROM bicycles")->fetch_assoc()['total'] ?? 0;
$total_users = $db->query("SELECT COUNT(*) as total FROM users WHERE role='user'")->fetch_assoc()['total'];
$active_rentals = $db->query("SELECT COUNT(*) as total FROM rentals WHERE rental_status='active'")->fetch_assoc()['total'];
$total_donated = $db->query("SELECT SUM(amount) as total FROM charity_donations")->fetch_assoc()['total'] ?? 0;

// to get data for tables
$bikes = $db->query("SELECT * FROM bicycles ORDER BY bike_id DESC");
$users = $db->query("SELECT * FROM users WHERE role='user' ORDER BY uid DESC");
$rentals = $db->query("SELECT r.*, b.name as bike_name, u.fullname 
                     FROM rentals r 
                     JOIN bicycles b ON r.bike_id = b.bike_id 
                     JOIN users u ON r.user_id = u.uid 
                     ORDER BY r.rental_id DESC");

// Donations for Charity
$charity = $db->query("SELECT * FROM charity_donations ORDER BY donation_id DESC LIMIT 20");


// Message handler
$message = $_SESSION['successful'] ?? '';
$error_message = $_SESSION['error'] ?? '';
unset($_SESSION['succesful'], $_SESSION['error']);
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
<div class="bg-dark text-white py-3">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="mb-0"> Admin Dashboard</h4>
            <div class="d-flex align-items-center gap-3">
                <span><i class="bi bi-person-circle"></i> <?php echo $_SESSION['fullname']; ?></span>
                <a href="../logout.php" class="btn btn-outline-light btn-sm"> Logout</a>
            </div>
        </div>
    </div>
</div>




</nav>

<div class="container mt-4">
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

      <!---  switch between tabs -->
    <ul class="nav nav-tabs mb-3">
        <li class="nav-item">
            <a class="nav-link active" data-bs-toggle="tab" href="#bikesTab">Bikes</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#usersTab">Users</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#rentalsTab">Rentals</a>
        
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" data-bs-toggle="tab" data-bs-target="#charityTab"> Charity</a>
        </li>
    </ul>


    <!--- Bike Inventory Overview -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-white bg-primary mb-3">
                <div class="card-body">
                    <h5 class="card-title">Total Bikes</h5>
                    <h2><?php echo $total_bikes; ?></h2>
                </div>
            </div>
        </div>


        <div class="col-md-3">
            <div class="card text-white bg-success mb-3">
                <div class="card-body">
                    <h5 class="card-title">Customers</h5>
                    <h2><?php echo $total_users; ?></h2>
                </div>
            </div>
        </div>
    
        <div class="col-md-3">
            <div class="card text-white bg-warning mb-3">
                <div class="card-body">
                    <h5 class="card-title">Active Rentals</h5>
                    <h2><?php echo $active_rentals; ?></h2>
                </div>
            </div>
        </div>
    

        <div class="col-md-3">
            <div class="card text-white bg-danger mb-3">
                <div class="card-body">
                    <h5 class="card-title">Charity Donations</h5>
                    <h2>£<?php echo number_format($total_donated, 0); ?></h2>
                </div>
            </div>
        </div>
    </div>

  

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
                                <option value="Hybrid">Hybrid</option>
                                <option value="Kids">Kids</option>
                                <option value="Cruiser">Cruiser</option>
                            </select>
                        </div>
                        
                        <div class="col-md-2">
                            <input type="number" step="0.01" name="price_per_day" class="form-control" placeholder="Price/Day" required>

                        </div>

                        <div class="col-md-3">
                            <input type="text" name="serial_number" class="form-control" placeholder="Serial Number (e.g., EL-TWN-007)" required>
                            <small class="text-muted">Format: XX-XXX-000 (e.g., EL-TWN-007)</small>
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
                        <table class="table table-bordered table">
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
                                    <td><strong><?php echo $bike['name']; ?></strong></td>
                                    <td><?php echo $bike['category']; ?></td>
                                    <td><?php echo $bike['serial_number']; ?></td>
                                    <td>£<?php echo number_format($bike['price_per_day'], 2); ?></td>
                                    <td>
                                        <?php if ($bike['quantity'] <= 3): ?>
                                            <span class="badge bg-danger"><?php echo $bike['quantity']; ?></span>
                                        <?php elseif ($bike['quantity'] <= 8): ?>
                                            <span class="badge bg-warning text-dark"><?php echo $bike['quantity']; ?></span>
                                        <?php else: ?>
                                            <span class="badge bg-success"><?php echo $bike['quantity']; ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <!-- Increase -->
                                            <a href="update_quantity.php?id=<?php echo $bike['bike_id']; ?>&action=increase" 
                                               class="btn btn-success" 
                                               title="Increase Quantity">
                                                <i class="bi bi-plus-lg"></i>
                                            </a>
                                            <!-- Decrease) -->
                                            <a href="update_quantity.php?id=<?php echo $bike['bike_id']; ?>&action=decrease" 
                                               class="btn btn-warning"
                                               onclick="return confirm('Decrease quantity by 1?')"
                                               title="Decrease Quantity">
                                                <i class="bi bi-dash-lg"></i>
                                            </a>
                                            <!-- Delete -->
                                            <a href="delete_bike.php?id=<?php echo $bike['bike_id']; ?>" 
                                               class="btn btn-danger"
                                               onclick="return confirm('Permanently delete this bike?')"
                                               title="Delete Bike">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </div>
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
                    <h5> Add New Customer</h5>
                </div>
                <div class="card-body">
                    <form action="add_user.php" method="POST" class="row g-3">
                        <div class="col-md-3">
                            <input type="text" name="fullname" class="form-control" placeholder="Full Name" required>
                        </div>
                        <div class="col-md-3">
                            <input type="email" name="email" class="form-control" placeholder="Email Address" required>
                        </div>
                        <div class="col-md-2">
                            <input type="text" name="username" class="form-control" placeholder="Username" required>
                        </div>
                        <div class="col-md-2">
                            <input type="password" name="password" class="form-control" placeholder="Password" required>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary">Add User</button>
                        </div>
                    </form>
                </div>
            </div>
        

            <div class="card mt-4">
                <div class="card_header">
                    <h5 class="mb-0"> Users </h5>
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
                                    <th>Start Date</th>
                                    <th>Due Date</th>
                                    <th>Return Date</th>
                                    <th>Total</th>
                                    <th>Late Fee</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($rental = $rentals->fetch_assoc()): ?>
                                <tr class="<?php echo ($rental['rental_status'] == 'active' && strtotime($rental['expected_return_date']) < time()) ? 'table-danger' : ''; ?>">
                                    <td><?php echo $rental['rental_id']; ?></td>
                                    <td><?php echo $rental['fullname']; ?></td>
                                    <td><?php echo $rental['bike_name']; ?></td>
                                    <td><?php echo date('d M Y', strtotime($rental['rent_start_date'])); ?></td>
                                    <td><?php echo date('d M Y', strtotime($rental['expected_return_date'])); ?></td>
                                    <td><?php echo $rental['actual_return_date'] ? date('d M Y', strtotime($rental['actual_return_date'])) : '-'; ?></td>
                                    <td>£<?php echo number_format($rental['total_amount'], 2); ?></td>
                                    <td class="text-danger fw-bold">£<?php echo number_format($rental['late_fee'], 2); ?></td>
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

        <div class="tab-pane fade" id="charityTab">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-heart"></i> Charity Donations</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-success">
                        <i class="bi bi-info-circle-fill"></i>
                        <strong>Making a Difference!</strong> All late fees are automatically donated to 
                        <strong>World Bicycle Relief</strong> - providing bikes for education.
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Rental ID</th>
                                    <th>Days Late</th>
                                    <th>Amount</th>
                                    <th>Charity</th>
                                    <th>Donation Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($donation = $charity->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $donation['donation_id']; ?></td>
                                    <td><?php echo $donation['rental_id']; ?></td>
                                    <td><?php echo $donation['days_late']; ?> days</td>
                                    <td class="text-success fw-bold">£<?php echo number_format($donation['amount'], 2); ?></td>
                                    <td><?php echo $donation['charity_name']; ?></td>
                                    <td><?php echo date('d M Y', strtotime($donation['donation_date'])); ?></td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>                               
</body>
</html>   


