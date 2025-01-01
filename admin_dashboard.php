<?php
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php');  // If not logged in, redirect back to login page
    exit;
}

echo "<h1>Welcome, " . $_SESSION['username'] . "!</h1>";
echo "<p>You are now logged in as an admin.</p>";
?>
