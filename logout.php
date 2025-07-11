<?php
session_start();
session_unset();
session_destroy();

echo '<script> alert("Log Out Successful");
        window.location.href = "login.html";
        </script>';
exit;
?>