<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['id'];

$result = mysqli_query($conn, "SELECT isAdmin FROM users WHERE id = $user_id");
$user = mysqli_fetch_assoc($result);

if (!$user || !$user['isAdmin']) {
    die("Access denied. Admins only.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = mysqli_real_escape_string($conn, $_POST['stockName']);
    $value = mysqli_real_escape_string($conn, $_POST['stockValue']);
    $qty   = mysqli_real_escape_string($conn, $_POST['stockQuantity']);

    $shemayKo = "SELECT id FROM stocktable WHERE stockName = '$name'";
    $shemay = mysqli_query($conn, $shemayKo);

    if(mysqli_num_rows($shemay) > 0){
         echo "<script>
            alert('STOCK ALREADY EXISTS');
            window.location.href = 'admin.php';
        </script>";
        exit;
    }

    $query = "INSERT INTO stockTable (stockName, stockValue, stockQuantity)
        VALUES ('$name', $value, $qty)";
    mysqli_query($conn, $query);
    echo '<script> 
        alert("Stock Added");
        window.location.href = "stocks.php";
    </script>';
} else {
    echo '<script> 
        alert("Error");
        window.location.href = "stocks.php";
    </script>';
    exit;
}

?>

