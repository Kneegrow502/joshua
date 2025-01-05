<?php
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "12345678";
$dbname = "system";
$port = "3307";

$conn = new mysqli($servername, $username, $password, $dbname, $port);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SESSION['role'] === 'student') {
    // Fetch quizzes
    $sql = "SELECT * FROM quizzes";
    $result = $conn->query($sql);

    echo "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Take Quiz</title>
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
        .quiz-container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            width: 50%;
            text-align: center;
        }
        .quiz-container h2 {
            color: #003366;
            margin-bottom: 20px;
        }
        .quiz-link {
            display: block;
            margin: 10px 0;
            text-decoration: none;
            color: #003366;
            font-weight: bold;
            background-color: #e0f7fa;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
        }
        .quiz-link:hover {
            background-color: #b2ebf2;
        }
        /* Modal styling */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
        }
        .modal-content {
            background-color: white;
            margin: 10% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 50%;
            border-radius: 10px;
        }
        .close {
            color: red;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        .close:hover {
            color: darkred;
        }
        .submit-btn {
            background-color: #003366;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .submit-btn:hover {
            background-color: #002244;
        }
    </style>
</head>
<body>
    <div class='quiz-container'>
        <h2>Available Quizzes</h2>";
        while ($row = $result->fetch_assoc()) {
            echo "<div class='quiz-link' onclick='openQuiz(" . $row['quiz_id'] . ")' data-quiz-id='" . $row['quiz_id'] . "'>" . $row['quiz_name'] . "</div>";
        }
        echo "
    </div>
    <!-- Modal -->
    <div id='quiz-modal' class='modal'>
        <div class='modal-content'>
            <span class='close' onclick='closeModal()'>&times;</span>
            <form id='quiz-form' method='POST' action='submit-quiz.php'>
                <div id='quiz-content'></div>
                <button type='submit' class='submit-btn'>Submit Quiz</button>
            </form>
        </div>
    </div>

    <script>
        function openQuiz(quizId) {
            // Fetch questions using AJAX
            fetch('get-quiz-questions.php?quiz_id=' + quizId)
                .then(response => response.text())
                .then(data => {
                    document.getElementById('quiz-content').innerHTML = data;
                    document.getElementById('quiz-modal').style.display = 'block';
                })
                .catch(error => console.error('Error fetching quiz:', error));
        }

        function closeModal() {
            document.getElementById('quiz-modal').style.display = 'none';
        }
    </script>
</body>
</html>";
} else {
    echo "You do not have permission to access this page.";
}

$conn->close();
?>
