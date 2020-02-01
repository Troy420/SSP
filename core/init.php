<?php
$db = mysqli_connect('127.0.0.1','root','','ssp_database');

if(mysqli_connect_errno()){
    echo 'Database Connection Failed with the following errors: '.mysqli_connect_error();
    die();
}

session_start();

require_once $_SERVER['DOCUMENT_ROOT'].'/ssp/config.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/ssp/helpers/helpers.php';

$cart_id = "";
if(isset($_COOKIE[CART_COOKIE])){
    $cart_id = sanitize($_COOKIE[CART_COOKIE]); 
}

if(isset($_SESSION['SBUser'])){
    $user_id = $_SESSION['SBUser'];
    $query = $db->query("SELECT * FROM users WHERE id = '$user_id' ");
    $user_data = mysqli_fetch_assoc($query);
    $fn = explode(" ", $user_data['full_name']);
    $user_data['first'] = $fn[0];
    $user_data['last'] = $fn[1];
}

// session_destroy();

