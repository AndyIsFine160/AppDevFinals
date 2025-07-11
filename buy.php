<?php
include 'db_connect.php';
include 'user_check.php';

$user_id  = $_SESSION['id'];
$stock_id = mysqli_real_escape_string($conn, $_POST['stock_id']);
$amount   = mysqli_real_escape_string($conn, $_POST['amount']);

$userResult = mysqli_query($conn, "SELECT balance FROM users WHERE id = $user_id");
$user = mysqli_fetch_assoc($userResult);

if ($user['balance'] < $amount) {
    echo '<script> alert("Insufficient funds");
        window.location.href = "stocks.php";
        </script>';
    exit;
}

$stockResult = mysqli_query($conn, "SELECT stockValue FROM stockTable WHERE id = $stock_id");
$stock = mysqli_fetch_assoc($stockResult);
$currentPrice = $stock['stockValue'];

if (!mysqli_query($conn, "UPDATE users SET balance = balance - $amount WHERE id = $user_id")) {
    echo '<script> alert("Error");
        .location.href = "stocks.php";
    </script>';
    exit;
}

$portResult = mysqli_query($conn,
    "SELECT amountSpent, buyPrice FROM portfolios
    WHERE userIdf = $user_id AND stockIdf = $stock_id");

if (mysqli_num_rows($portResult) > 0) {
    $p = mysqli_fetch_assoc($portResult);
    $total = $p['amountSpent'] + $amount;
    $avgPrice = (($p['amountSpent'] * $p['buyPrice']) + ($amount * $currentPrice)) / $total;

    $update = mysqli_query($conn, "UPDATE portfolios
        SET amountSpent = $total, buyPrice = $avgPrice
        WHERE userIdf = $user_id AND stockIdf = $stock_id");

    if ($update) {
        echo '<script> alert("Successfully bought, redirecting to portfolio");
        window.location.href = "portfolio.php";
        </script>';
    } else {
        echo '<script> alert("Error");
        window.location.href = "stocks.php";
        </script>';
    }
} else {
    $insert = mysqli_query($conn,
        "INSERT INTO portfolios (userIdf, stockIdf, amountSpent, buyPrice)
        VALUES ($user_id, $stock_id, $amount, $currentPrice)");

    if ($insert) {
        echo '<script> alert("Successfully bought, redirecting to portfolio");
        window.location.href = "portfolio.php";
        </script>';
    } else {
        echo '<script> alert("Error");
        window.location.href = "stocks.php";
        </script>';
    }
}
exit;
?>