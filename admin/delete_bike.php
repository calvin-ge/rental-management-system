<?php
require_once __DIR__ . '/../config/connection.php';

requireAdmin();

if(isser($_GET['id'])) {
    $id = intval($_GET['id']);

    $check_qury = "SELECT quanity, name FROM bicycles WHERE bike_id = $id";
    
}