<?php
session_start();

// Example login credentials (you can replace this with a database check in a real application)
$validUsername = "student";
$validPassword = "password123";

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Check if the entered username and password are correct
    if ($username === $validUsername && $password === $validPassword) {
        // Start session and store login information
        $_SESSION['logged_in'] = true;
        $_SESSION['username'] = $username;

        // Redirect to the homepage (index.php)
        header('Location: index.php');
        exit;
    } else {
        // If login fails, show an error message
        $errorMsg = "Invalid username or password. Please try again.";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>

  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Select Subject - Computer Programming</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f4f7fc;
      margin: 0;
      padding: 0;
    }

    header {
      background-color: #003366;
      color: white;
      text-align: center;
      padding: 20px;
    }

    .container {
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 20px;
      height: 80vh;
    }

    .card {
      width: 250px;
      background-color: #ffffff;
      border-radius: 8px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
      overflow: hidden;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      cursor: pointer;
      text-align: center;
      font-family: 'Arial', sans-serif;
      color: #003366;
    }

    .card:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
    }

    .card img {
      width: 100%;
      height: 150px;
      object-fit: cover;
    }

    .card h3 {
      margin: 15px 0;
      font-size: 20px;
    }

    .card p {
      color: #555;
      font-size: 14px;
      padding: 0 15px;
    }

    .card a {
      display: block;
      background-color: #003366;
      color: white;
      padding: 10px;
      text-decoration: none;
      font-size: 16px;
      border-radius: 0 0 8px 8px;
      transition: background-color 0.3s;
    }

    .card a:hover {
      background-color: #002244;
    }

    footer {
      background-color: #003366;
      color: white;
      text-align: center;
      padding: 15px;
      margin-top: 40px;
    }

    footer a {
      color: #fff;
      text-decoration: none;
    }

    footer a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <header>
    <h1>Computer Programming</h1>
    <p>Sharpen your coding skills and learn programming languages like Python, Java, and C++.</p>
  </header>

  <div class="container">
    <!-- Card for Computer Programming -->
    <div class="card">
      <img src="https://via.placeholder.com/250x150?text=Computer+Programming" alt="Computer Programming">
      <h3>Computer Programming</h3>
      <p>Learn the fundamentals of programming and improve your coding abilities with quizzes on languages such as Python, Java, and C++.</p>
      <a href="programming-quiz.html">Enter Class</a>
      <a href="logout.php">Logout</a>

    </div>
  </div>

  <footer>
    <p>&copy; 2024 School Quiz & Exam System | All Rights Reserved</p>
    <p><a href="#">Contact Us</a> | <a href="#">Privacy Policy</a></p>
  </footer>

</body>
</html>
