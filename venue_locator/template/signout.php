<?php
// signout.php - This file will handle the sign-out process

session_start(); // Start the session

// Destroy the session to log out the user
session_unset(); // Unset all session variables
session_destroy(); // Destroy the session

// Redirect to login page after successful logout
header("Location: signin.php");
exit(); // Make sure no further code is executed
?>