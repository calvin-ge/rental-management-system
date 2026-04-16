<?php
include "config/connection.php";
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

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
            $_SESSION['user_id'] = $user['uid'];
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
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Login - Bike Shop</title>
         <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
         <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    </head>
    <body>
        <nav class="navbar navbar-dark bg-dark sticky-top shadow-sm">
            <div class="container">
                <a class="navbar-brand fw-bold" href="#">
                    <i class="bi bi-bicycle me-2"></i>RentCycle</a>
            </div>
       
        </nav>


        <div class="container mt-5">
            
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="mb-3">
                            <label for="role" class="form-label">Role</label>
                            <select class="form-select" id="role" name="role" required>
                                <option value="">Select Role</option>
                                <option value="admin">Admin</option>
                                <option value="user">User</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Login</button>
                    </form>
                </div>
            </div>
        </div>




<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
