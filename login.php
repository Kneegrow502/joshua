<?php
session_start();

// Database connection details
$servername = "localhost";  // Database server
$username = "root";         // Database username (default in XAMPP)
$password = "";             // Database password (empty in XAMPP by default)
$dbname = "system";         // Your database name

// Create a connection to MySQL
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$errorMsg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $input_password = $_POST['password'];

    // Fetch the stored password from the database for the entered username
    $sql = "SELECT password FROM students WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);  // Bind username parameter
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($stored_password);  // Get the stored password
        $stmt->fetch();

        // Verify if the input password matches the stored password
        if ($input_password === $stored_password) {
            // Password matches, start session and redirect
            $_SESSION['logged_in'] = true;
            $_SESSION['username'] = $username;
            header('Location: index.php');  // Redirect to the homepage/dashboard
            exit;
        } else {
            // Invalid credentials
            $errorMsg = "Invalid username or password. Please try again.";
        }
    } else {
        // Username not found
        $errorMsg = "Invalid username or password. Please try again.";
    }

    // Close the prepared statement and database connection
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - School Quiz System</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7fc;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .login-container {
            background-color: #ffffff;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            width: 300px;
            text-align: center;
        }

        .login-container h2 {
            margin-bottom: 20px;
            color: #003366;
        }

        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }

        .login-btn {
            width: 100%;
            padding: 10px;
            background-color: #003366;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        .login-btn:hover {
            background-color: #002244;
        }

        .error-msg {
            color: red;
            font-size: 14px;
            margin-top: 10px;
        }
    </style>
</head>
<body>

    <div class="login-container">
        <h2>Login</h2>
        <form method="POST" action="login.php">
            <input type="text" name="username" placeholder="Username" required><br>
            <input type="password" name="password" placeholder="Password" required><br>
            <button type="submit" class="login-btn">Login</button>
        </form>

        <?php
        if (!empty($errorMsg)) {
            echo "<p class='error-msg'>$errorMsg</p>";
        }
        ?>

    </div>

</body>
</html>
