<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "12345678";
$dbname = "system";
$port = "3307";

$conn = new mysqli($servername, $username, $password, $dbname, $port);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$errorMsg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $input_password = $_POST['password'];

    // Check if the username exists in the student table
    $sql_student = "SELECT username, password FROM students WHERE username = ?";
    $stmt = $conn->prepare($sql_student);
    $stmt->bind_param("s", $username);  // Bind username parameter for student
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($db_username, $db_password);  // Get student username and password
        $stmt->fetch();

        // For students, verify the password using password_verify (hashed password)
        if (password_verify($input_password, $db_password)) {
            $_SESSION['logged_in'] = true;
            $_SESSION['username'] = $db_username;
            $_SESSION['role'] = 'student';
            header('Location: index.php');  // Redirect to student dashboard
            exit;
        } else {
            // Invalid student credentials
            $errorMsg = "Invalid username or password. Please try again.";
        }
    } else {
        // If the student doesn't exist, check if the user is an admin or teacher
        // Admin check: if username is "admin", compare with plain-text password
        if ($username === "admin" && $input_password === "admin_password") {
            $_SESSION['logged_in'] = true;
            $_SESSION['username'] = "admin";
            $_SESSION['role'] = 'admin';
            header('Location: admin_dashboard.php');  // Redirect to admin dashboard
            exit;
        }

        // Teacher check: if username is "teacher", compare with plain-text password
        if ($username === "teacher" && $input_password === "teacher_password") {
            $_SESSION['logged_in'] = true;
            $_SESSION['username'] = "teacher";
            $_SESSION['role'] = 'teacher';
            header('Location: teacher-page.php');  // Redirect to teacher dashboard
            exit;
        }

        // If the username isn't found in any table, show error
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
