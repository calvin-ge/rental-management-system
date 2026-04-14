<?php

require_once __DIR__ . '/../config/connection.php';
requireAdmin();

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $db->query("DELETE FROM users WHERE uid = '$id' AND role = 'user'");
    header("Location: dashboard.php");
}

?>