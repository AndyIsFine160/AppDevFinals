<?php
include 'db_connect.php';
include 'user_check.php';

$user_id = $_SESSION['id'];
$stock_id = mysqli_real_escape_string($conn, $_POST['stock_id']);

$portfiolioResult = mysqli_query($conn,
    "SELECT amountSpent, buyPrice FROM portfolios
    WHERE userIdf = $user_id AND stockIdf = $stock_id");
$p = mysqli_fetch_assoc($portfiolioResult);

$shares = $p['amountSpent'] / $p['buyPrice'];

$shareResult = mysqli_query($conn,
    "SELECT stockValue FROM stockTable WHERE id = $stock_id");
$s = mysqli_fetch_assoc($shareResult);
$currentValue = $shares * $s['stockValue'];

mysqli_query($conn,
    "UPDATE users SET balance = balance + $currentValue WHERE id = $user_id");

mysqli_query($conn,
    "DELETE FROM portfolios WHERE userIdf = $user_id AND stockIdf = $stock_id");

echo '<script> alert("Sold successfully! You earned Php' . number_format($currentValue, 2) . '.");
        window.location.href = "portfolio.php";
        </script>';
    exit;
?>