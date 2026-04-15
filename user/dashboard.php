<?php
require_once __DIR__ . '/../config/connection.php';
requireLogin();

if (getUserRole() == 'admin') {
    header('Location: ../admin/dashboard.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$message = '';
$error = '';

if (isset($_SESSION['success'])) {
    $message = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>' . $_SESSION['success'] . '
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>';
    unset($_SESSION['success']);
}

if (isset($_SESSION['error'])) {
    $error = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>' . $_SESSION['error'] . '
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
              </div>';
    unset($_SESSION['error']);
}

$active_rentals_query = "SELECT r.*, b.name as bike_name, b.price_per_day, b.category
                         FROM rentals r
                         JOIN bicycles b ON r.bike_id = b.bike_id
                         WHERE r.user_id = $user_id AND r.rental_status = 'active'
                         ORDER BY r.expected_return_date ASC";
$active_rentals = $db->query($active_rentals_query);

$search = isset($_GET['search']) ? mysqli_real_escape_string($db, $_GET['search']) : '';
$category = isset($_GET['category']) ? mysqli_real_escape_string($db, $_GET['category']) : '';

$bikes_sql = "SELECT * FROM bicycles WHERE quantity > 0";
if (!empty($search)) {
    $bikes_sql .= " AND name LIKE '%$search%'";
}
if (!empty($category)) {
    $bikes_sql .= " AND category = '$category'";
}
$bikes_sql .= " ORDER BY name ASC";
$available_bikes = $db->query($bikes_sql);

$history_query = "SELECT r.*, b.name as bike_name, b.category
                  FROM rentals r
                  JOIN bicycles b ON r.bike_id = b.bike_id
                  WHERE r.user_id = $user_id AND r.rental_status = 'returned'
                  ORDER BY r.rental_id DESC LIMIT 10";
$rental_history = $db->query($history_query);

function getBikeImage($category) {
    $imageMap = [
        'Mountain' => 'mountain_bike.jpg',
        'City' => 'city_bike.jpg',
        'Road' => 'road_bike.jpg',
        'Electric' => 'electric_bike.jpg',
        'Hybrid' => 'hybrid_bike.jpg',
        'Kids' => 'kids_bike.jpg',
        'Cruiser' => 'cruiser_bike.jpg'
    ];
    $filename = isset($imageMap[$category]) ? $imageMap[$category] : 'default_bike.jpg';
    return '../assets/images/bikes/' . $filename;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Dashboard | CycleRent</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-dark sticky-top shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="#">
            <i class="bi bi-bicycle me-2"></i>CycleRent
        </a>
        <div class="dropdown">
            <button class="btn btn-outline-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                <i class="bi bi-person-circle"></i> <?php echo $_SESSION['fullname']; ?>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item text-danger" href="../logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="bg-primary text-white py-5 mb-4">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h1 class="display-5 fw-bold">Welcome back, <?php echo $_SESSION['fullname']; ?>!</h1>
                <p class="lead mb-0">Find your perfect ride and start your adventure today.</p>
                <small><i class="bi bi-heart-fill text-danger"></i> Late fees support cycling education worldwide</small>
            </div>
            <div class="col-lg-4 text-center d-none d-lg-block">
                <i class="bi bi-bicycle fs-1"></i>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <?php echo $message . $error; ?>

    <?php if($active_rentals && $active_rentals->num_rows > 0): ?>
    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-header bg-white border-0 pt-4">
            <h4 class="mb-0 fw-bold">
                <i class="bi bi-clock-history text-warning me-2"></i>Your Active Rentals
            </h4>
            <p class="text-muted small mb-0">View and manage your current rentals</p>
        </div>
        <div class="card-body">
            <?php while($rental = $active_rentals->fetch_assoc()): 
                $today = new DateTime();
                $due_date = new DateTime($rental['expected_return_date']);
                $is_overdue = $today > $due_date;
            ?>
            <div class="card mb-3 <?php echo $is_overdue ? 'border-danger' : 'border-warning'; ?>">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-2 text-center">
                            <img src="<?php echo getBikeImage($rental['category']); ?>" 
                                 class="rounded" 
                                 style="width: 70px; height: 70px; object-fit: cover;"
                                 alt="<?php echo $rental['bike_name']; ?>">
                        </div>
                        <div class="col-md-4">
                            <h5 class="mb-1"><?php echo $rental['bike_name']; ?></h5>
                            <small class="text-muted">
                                <i class="bi bi-calendar"></i> Rented: <?php echo date('M d, Y', strtotime($rental['rent_start_date'])); ?>
                            </small>
                        </div>
                        <div class="col-md-3">
                            <p class="mb-0 fw-bold">Due Date:</p>
                            <p class="mb-0 <?php echo $is_overdue ? 'text-danger fw-bold' : ''; ?>">
                                <i class="bi bi-calendar-check"></i> <?php echo date('M d, Y', strtotime($rental['expected_return_date'])); ?>
                            </p>
                            <?php if($is_overdue): ?>
                                <small class="text-danger">
                                    <i class="bi bi-exclamation-triangle"></i> OVERDUE!
                                </small>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-3 text-md-end mt-3 mt-md-0">
                            <form action="return_bike.php" method="POST" onsubmit="return confirm('Return this bike?')">
                                <input type="hidden" name="rental_id" value="<?php echo $rental['rental_id']; ?>">
                                <button type="submit" class="btn btn-warning">
                                    <i class="bi bi-arrow-return-left"></i> Return Bike
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
    <?php endif; ?>

    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-body">
            <h5 class="mb-3 fw-bold">
                <i class="bi bi-search me-2"></i>Find Your Perfect Bike
            </h5>
            <form method="GET" action="" class="mb-3">
                <div class="row g-3">
                    <div class="col-md-7">
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                            <input type="text" name="search" class="form-control" 
                                   placeholder="Search by name ..." 
                                   value="<?php echo htmlspecialchars($search); ?>">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select name="category" class="form-select">
                            <option value="">All Categories</option>
                            <option value="Mountain" <?php echo $category == 'Mountain' ? 'selected' : ''; ?>>Mountain</option>
                            <option value="City" <?php echo $category == 'City' ? 'selected' : ''; ?>>City</option>
                            <option value="Road" <?php echo $category == 'Road' ? 'selected' : ''; ?>>Road</option>
                            <option value="Electric" <?php echo $category == 'Electric' ? 'selected' : ''; ?>>Electric</option>
                            <option value="Hybrid" <?php echo $category == 'Hybrid' ? 'selected' : ''; ?>>Hybrid</option>
                            <option value="Kids" <?php echo $category == 'Kids' ? 'selected' : ''; ?>>Kids</option>
                            <option value="Cruiser" <?php echo $category == 'Cruiser' ? 'selected' : ''; ?>>Cruiser</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-search"></i> Search
                        </button>
                    </div>
                </div>
            </form>
            <div>
                <span class="text-muted small me-2">Quick filters:</span>
                <a href="?category=Mountain" class="badge bg-secondary text-decoration-none me-1">Mountain</a>
                <a href="?category=City" class="badge bg-secondary text-decoration-none me-1">City</a>
                <a href="?category=Road" class="badge bg-secondary text-decoration-none me-1">Road</a>
                <a href="?category=Electric" class="badge bg-secondary text-decoration-none me-1">Electric</a>
                <a href="?category=Hybrid" class="badge bg-secondary text-decoration-none me-1">Hybrid</a>
                <a href="?" class="badge bg-primary text-decoration-none">Clear All</a>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="fw-bold">
            <i class="bi bi-bicycle me-2"></i>Available Bicycles
        </h3>
        <span class="badge bg-success rounded-pill"><?php echo $available_bikes ? $available_bikes->num_rows : 0; ?> bikes available</span>
    </div>

    <div class="row">
        <?php if($available_bikes && $available_bikes->num_rows > 0): ?>
            <?php while($bike = $available_bikes->fetch_assoc()): ?>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100 shadow-sm">
                    <img src="<?php echo getBikeImage($bike['category']); ?>" 
                         class="card-img-top" 
                         style="height: 200px; object-fit: cover;"
                         alt="<?php echo $bike['name']; ?>">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <h5 class="card-title mb-0"><?php echo $bike['name']; ?></h5>
                            <span class="badge bg-primary"><?php echo $bike['category']; ?></span>
                        </div>
                        <p class="card-text text-muted small mt-2">
                            <i class="bi bi-building"></i> <?php echo $bike['serial_number'] ?: 'Model Number'; ?>
                        </p>
                        <div class="mt-3">
                            <span class="display-6 text-primary fw-bold">£<?php echo number_format($bike['price_per_day'], 2); ?></span>
                            <span class="text-muted">/ day</span>
                            <div>
                                <small class="text-muted">
                                    <i class="bi bi-box"></i> <?php echo $bike['quantity']; ?> available
                                </small>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-white border-0 pb-3">
                        <button class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#rentModal<?php echo $bike['bike_id']; ?>">
                            <i class="bi bi-cart-plus"></i> Rent Now
                        </button>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="rentModal<?php echo $bike['bike_id']; ?>" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title">
                                <i class="bi bi-bicycle me-2"></i>Rent <?php echo $bike['name']; ?>
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <form action="rent_bike.php" method="POST">
                            <div class="modal-body">
                                <input type="hidden" name="bike_id" value="<?php echo $bike['bike_id']; ?>">
                                <div class="row">
                                    <div class="col-md-5">
                                        <img src="<?php echo getBikeImage($bike['category']); ?>" 
                                             class="img-fluid rounded" 
                                             alt="<?php echo $bike['name']; ?>">
                                    </div>
                                    <div class="col-md-7">
                                        <h6><?php echo $bike['name']; ?></h6>
                                        <p class="text-muted small"><?php echo $bike['serial_number']; ?> | <?php echo $bike['category']; ?></p>
                                        <hr>
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">
                                                <i class="bi bi-calendar"></i> Start Date
                                            </label>
                                            <input type="date" name="start_date" class="form-control" 
                                                   value="<?php echo date('Y-m-d'); ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">
                                                <i class="bi bi-calendar-check"></i> Return Date
                                            </label>
                                            <input type="date" name="return_date" class="form-control" 
                                                   min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">
                                                <i class="bi bi-hash"></i> Quantity
                                            </label>
                                            <input type="number" name="quantity" class="form-control" 
                                                   value="1" min="1" max="<?php echo $bike['quantity']; ?>" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="alert alert-success mt-3 small">
                                    <i class="bi bi-info-circle-fill me-2"></i>
                                    <strong>Good News!</strong> Late fees (30% per day) are donated to World Bicycle Relief charity.
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-circle"></i> Confirm Rental
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-info text-center py-5">
                    <i class="bi bi-emoji-frown fs-1 d-block"></i>
                    <h5 class="mt-3">No bikes available at the moment</h5>
                    <p class="mb-0">Please check back later or try different search criteria.</p>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <?php if($rental_history && $rental_history->num_rows > 0): ?>
    <div class="card shadow-sm border-0 rounded-4 mt-5 mb-4">
        <div class="card-header bg-white border-0 pt-4">
            <h4 class="mb-0 fw-bold">
                <i class="bi bi-clock-history me-2"></i>Your Rental History
            </h4>
            <p class="text-muted small mb-0">View your past rentals</p>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-striped">
                    <thead class="table-light">
                        <tr><th>Bike</th><th>Rental Period</th><th>Qty</th><th>Total</th><th>Late Fee</th><th>Status</th></tr>
                    </thead>
                    <tbody>
                        <?php while($history = $rental_history->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <img src="<?php echo getBikeImage($history['category']); ?>" 
                                     class="rounded me-2" style="width: 40px; height: 40px; object-fit: cover;">
                                <?php echo $history['bike_name']; ?>
                            </strong>
                            </td>
                            <td><?php echo date('M d', strtotime($history['rent_start_date'])); ?> - <?php echo date('M d, Y', strtotime($history['actual_return_date'])); ?></td>
                            <td><?php echo $history['quantity_rented']; ?></td>
                            <td>$<?php echo number_format($history['total_amount'], 2); ?></td>
                            <td class="<?php echo $history['late_fee'] > 0 ? 'text-danger fw-bold' : 'text-muted'; ?>">
                                $<?php echo number_format($history['late_fee'], 2); ?>
                                <?php if($history['late_fee'] > 0): ?>
                                    <i class="bi bi-heart-fill text-danger" title="Donated to charity!"></i>
                                <?php endif; ?>
                            </td>
                            <td><span class="badge bg-secondary">Returned</span></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="bg-dark text-white rounded-4 p-4 text-center mb-5">
        <i class="bi bi-heart-fill text-danger fs-1"></i>
        <h4 class="mt-2">Making a Difference Together</h4>
        <p class="mb-0">100% of late fees are donated to <strong>World Bicycle Relief</strong></p>
        <small class="text-white-50">Your responsible returns help children get to school!</small>
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