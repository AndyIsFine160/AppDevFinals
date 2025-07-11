<?php
include 'db_connect.php';
include 'user_check.php';

$user_id = $_SESSION['id'];
$amount = mysqli_real_escape_string($conn, $_POST['amount']);

if ($amount <= 0) {
    die("Invalid deposit amount.");
}

$query = "UPDATE users SET balance = balance + $amount WHERE id = $user_id";

if (mysqli_query($conn, $query)) {
    echo '<script> alert("CASHIN SUCCESSFUL");
        window.location.href = "portfolio.php";
        </script>';
} else {
    echo '<script> alert("ERROR");
        window.location.href = "portfolio.php";
        </script>';
}
?>