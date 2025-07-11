<?php
session_start();
include 'db_connect.php';

$login = mysqli_real_escape_string($conn, $_POST['usernameEmail']);
$password = mysqli_real_escape_string($conn, $_POST['password']);

$query = "SELECT id, username, password, isAdmin FROM users
    WHERE username = '$login' OR email = '$login'";

$result = mysqli_query($conn, $query);

if ($row = mysqli_fetch_assoc($result)) {
    if (password_verify($password, $row['password'])) {
        $_SESSION['id'] = $row['id'];
        $_SESSION['username'] = $row['username'];
        $_SESSION['isAdmin'] = $row['isAdmin'];
        echo '<script> alert("LOGIN SUCCESSFUL");
        window.location.href = "stocks.php";
        </script>';
    exit;
        } else {
            echo '<script> alert("Invalid login Try Again.");
        window.location.href = "login.html";
        </script>';
    exit;
        }
} else {
    echo '<script> alert("Invalid login Try Again.");
        window.location.href = "login.html";
        </script>';
    exit;
}
?>