<?php
include 'db_connect.php';

$username = mysqli_real_escape_string($conn, $_POST['username']);
$email    = mysqli_real_escape_string($conn, $_POST['email']);
$password = mysqli_real_escape_string($conn, $_POST['password']);
$confirmPass = mysqli_real_escape_string($conn, $_POST['confirmPass']);
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

if ($confirmPass != $password) {
    echo "<script>
        alert('Password Mismatch');
        window.location.href = 'registration.html';
    </script>";
    exit;
}

$checkQuery = "SELECT id FROM users WHERE username = '$username' OR email = '$email'";
$checkResult = mysqli_query($conn, $checkQuery);

if (mysqli_num_rows($checkResult) > 0) {
    echo "<script>
        alert('Username or email already exists.');
        window.location.href = '/appdev/registration.html';
    </script>";
    exit;
}

$query = "INSERT INTO users (username, email, password)
        VALUES ('$username', '$email', '$hashedPassword')";

if (mysqli_query($conn, $query)) {
    echo "<script>
        alert('User registered successfully!');
        window.location.href = '/appdev/login.html';
    </script>";
} else {
    echo "<script>
        alert('Error: " . mysqli_error($conn) . "');
        window.location.href = 'signup.php';
    </script>";
}
?>