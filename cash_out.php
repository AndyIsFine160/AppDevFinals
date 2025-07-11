<?php
include 'db_connect.php';
include 'user_check.php';

$user_id = $_SESSION['id'];
$amount = mysqli_real_escape_string($conn, $_POST['amount']);

$bal = mysqli_query($conn, "SELECT balance FROM users WHERE id = $user_id");
$user = mysqli_fetch_assoc($bal);

if ($amount <= 0) {
    echo '<script> alert("NO FUNDS TO CASH OUT");
        window.location.href = "portfolio.php";
        </script>';
    exit;
} elseif ($user['balance'] < $amount) {
    echo '<script> alert("INSUFFICIENT FUNDS");
        window.location.href = "portfolio.php";
        </script>';
    exit;
}
$query = "UPDATE users SET balance = balance - $amount WHERE id = $user_id";

if (mysqli_query($conn, $query)) {
    echo '<script> alert("CASHOUT SUCCESSFUL");
        window.location.href = "portfolio.php";
        </script>';
    exit;
} else {
    echo '<script> alert("ERROR");
        window.location.href = "portfolio.php";
        </script>';
    exit;
}

?>