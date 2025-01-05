<?php
session_start();

// Check if the user is a teacher
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'teacher') {
    die("Access denied. You do not have permission to access this page.");
}

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

// Check if quiz_id is provided in the URL
if (isset($_GET['quiz_id'])) {
    $quiz_id = $_GET['quiz_id'];

    // Fetch the quiz details
    $quiz_sql = "SELECT * FROM quizzes WHERE quiz_id = ?";
    $stmt = $conn->prepare($quiz_sql);
    $stmt->bind_param("i", $quiz_id);
    $stmt->execute();
    $quiz_result = $stmt->get_result();
    $quiz = $quiz_result->fetch_assoc();

    // Fetch questions for this quiz
    $questions_sql = "SELECT * FROM questions WHERE quiz_id = ?";
    $stmt = $conn->prepare($questions_sql);
    $stmt->bind_param("i", $quiz_id);
    $stmt->execute();
    $questions_result = $stmt->get_result();
} else {
    die("Quiz ID is required.");
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Quiz</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7fc;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .container {
            margin-top: 50px;
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 80%;
            max-width: 600px;
        }
        h2 {
            color: #003366;
        }
        .question {
            background-color: #f9f9f9;
            padding: 15px;
            margin: 10px 0;
            border-radius: 5px;
            border: 1px solid #ddd;
        }
        .question h4 {
            margin: 0;
        }
        .question p {
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Quiz: <?= htmlspecialchars($quiz['quiz_name']); ?></h2>
        <p><strong>Description:</strong> <?= htmlspecialchars($quiz['quiz_description']); ?></p>

        <h3>Questions</h3>
        <?php while ($question = $questions_result->fetch_assoc()): ?>
            <div class="question">
                <h4><?= htmlspecialchars($question['question_text']); ?></h4>
                <p><strong>Answer Choices:</strong></p>
                <ul>
                    <li><?= htmlspecialchars($question['answer_choice_1']); ?></li>
                    <li><?= htmlspecialchars($question['answer_choice_2']); ?></li>
                    <li><?= htmlspecialchars($question['answer_choice_3']); ?></li>
                    <li><?= htmlspecialchars($question['answer_choice_4']); ?></li>
                </ul>
                <p><strong>Correct Answer:</strong> <?= $question['correct_answer']; ?></p>
            </div>
        <?php endwhile; ?>
    </div>
</body>
</html>
