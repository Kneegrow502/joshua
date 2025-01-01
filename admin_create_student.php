<?php
// Start the session to check if the admin is logged in
session_start();

// Check if the admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin.php"); // Redirect to login page if not logged in
    exit();
}

// Database connection
$servername = "localhost";
$username = "root";
$password = ""; // Default XAMPP MySQL password is empty
$dbname = "system"; // Your database name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle student account creation
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['create_student'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];
    $name = $_POST['name'];

    // Hash the password for security
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert new student into the database
    $sql = "INSERT INTO students (username, password, email, name) 
            VALUES ('$username', '$hashed_password', '$email', '$name')";

    if ($conn->query($sql) === TRUE) {
        $success_message = "Student account created successfully!";
    } else {
        $error_message = "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Student Account</title>
</head>
<body>
    <h2>Create a New Student Account</h2>
    
    <!-- Success/Error Message -->
    <?php
    if (isset($success_message)) {
        echo "<p style='color: green;'>$success_message</p>";
    }
    if (isset($error_message)) {
        echo "<p style='color: red;'>$error_message</p>";
    }
    ?>
    
    <form action="admin_create_student.php" method="POST">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required><br><br>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br><br>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required><br><br>

        <label for="name">Full Name:</label>
        <input type="text" id="name" name="name" required><br><br>

        <button type="submit" name="create_student">Create Account</button>
    </form>
    
    <br>
    <a href="admin_dashboard.php">Back to Admin Dashboard</a>
</body>
</html>
