<?php
session_start();

// Check if the user is logged in as an admin
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');  // If not logged in as admin, redirect to login page
    exit;
}

// Database connection details
$servername = "localhost";
$username = "root";
$password = "12345678";
$dbname = "system";
$port = "3307";

// Create a connection to MySQL
$conn = new mysqli($servername, $username, $password, $dbname, $port);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$errorMsg = "";
$successMsg = "";

// Handle the form submission for adding a student
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_username = $_POST['username'];
    $student_password = $_POST['password'];
    $student_fullname = $_POST['fullname'];
    $student_lrn = $_POST['lrn'];  // Capture LRN from the form
    
    // Ensure the username, password, fullname, and lrn are not empty
    if (empty($student_username) || empty($student_password) || empty($student_fullname) || empty($student_lrn)) {
        $errorMsg = "All fields are required.";
    } else {
        // Hash the student's password before storing
        $hashed_password = password_hash($student_password, PASSWORD_DEFAULT);
        
        // Prepare the SQL query to insert the student into the database
        $sql = "INSERT INTO students (username, password, fullname, lrn) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);

        // Check if prepare statement is successful
        if ($stmt) {
            // Bind parameters for SQL query
            $stmt->bind_param("ssss", $student_username, $hashed_password, $student_fullname, $student_lrn);
            
            // Execute the query
            if ($stmt->execute()) {
                $successMsg = "Student account for '$student_username' added successfully!";
            } else {
                $errorMsg = "Error adding student account: " . $conn->error;
            }
            
            // Close the statement
            $stmt->close();
        } else {
            $errorMsg = "Failed to prepare the SQL statement.";
        }
    }
}

// Fetch all students from the database
$sql = "SELECT id, username, fullname, lrn FROM students";
$result = $conn->query($sql);

// Check if the query was successful
if ($result === false) {
    die("Error fetching students: " . $conn->error);
}

$students = [];

if ($result->num_rows > 0) {
    // Store all students in an array
    while ($row = $result->fetch_assoc()) {
        $students[] = $row;
    }
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Student Management</title>
    <style>
        /* Styling for the login form */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7fc;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            flex-direction: column;
            height: 100vh;
        }

        .dashboard-container {
            background-color: #ffffff;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            width: 80%;
            margin-top: 50px;
            text-align: center;
        }

        .form-container {
            margin-bottom: 40px;
        }

        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }

        .form-btn {
            width: 100%;
            padding: 10px;
            background-color: #003366;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        .form-btn:hover {
            background-color: #002244;
        }

        .error-msg {
            color: red;
            font-size: 14px;
            margin-top: 10px;
        }

        .success-msg {
            color: green;
            font-size: 14px;
            margin-top: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #003366;
            color: white;
        }

    </style>
</head>
<body>

    <div class="dashboard-container">
        <h2>Welcome, Admin</h2>
        <p>Manage student accounts and view details.</p>

        <div class="form-container">
            <h3>Add Student Account</h3>

            <?php
            if (!empty($errorMsg)) {
                echo "<p class='error-msg'>$errorMsg</p>";
            }

            if (!empty($successMsg)) {
                echo "<p class='success-msg'>$successMsg</p>";
            }
            ?>

            <form method="POST" action="admin_dashboard.php">
                <input type="text" name="username" placeholder="Student Username" required><br>
                <input type="password" name="password" placeholder="Password" required><br>
                <input type="text" name="fullname" placeholder="Full Name" required><br>
                <input type="text" name="lrn" placeholder="Student LRN" required><br>
                <button type="submit" class="form-btn">Add Student</button>
            </form>

             
            
        </div>

        <h3>Student Accounts</h3>
        <table>
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Full Name</th>
                    <th>LRN</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (count($students) > 0) {
                    foreach ($students as $student) {
                        echo "<tr>
                                <td>{$student['username']}</td>
                                <td>{$student['fullname']}</td>
                                <td>{$student['lrn']}</td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='3'>No students found.</td></tr>";
                }
                ?>
            </tbody>
        </table>

        <a href="logout.php"><button class="logout-btn">Logout</button></a>
    </div>

</body>
</html>
