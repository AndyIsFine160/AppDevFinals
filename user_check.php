<?php
session_start();

$timeout_duration = 1200; // 20 minutes

if (isset($_SESSION['LAST_ACTIVITY']) &&
    (time() - $_SESSION['LAST_ACTIVITY']) > $timeout_duration) {
    
    session_unset();
    session_destroy();
    header("Location: login.php?timeout=1");
    exit;
}

$_SESSION['LAST_ACTIVITY'] = time();

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_SESSION['isAdmin'])) {
    include 'db_connect.php';
    $user_id = $_SESSION['id'];
    $result = mysqli_query($conn, "SELECT isAdmin FROM users WHERE id = $user_id");
    $user = mysqli_fetch_assoc($result);
    $_SESSION['isAdmin'] = $user['isAdmin'] ?? 0;
}
?>