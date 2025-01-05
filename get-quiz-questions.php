<?php
session_start();

// Database connection details
$servername = "localhost";
$username = "root";
$password = "12345678";
$dbname = "system";
$port = "3307";

$conn = new mysqli($servername, $username, $password, $dbname, $port);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['quiz_id'])) {
    $quiz_id = intval($_GET['quiz_id']); // Sanitize input

    // Fetch quiz questions
    $sql = "SELECT * FROM questions WHERE quiz_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $quiz_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<input type='hidden' name='quiz_id' value='$quiz_id'>"; // Add quiz_id as a hidden input
        while ($row = $result->fetch_assoc()) {
            echo "<p>" . $row['question_text'] . "</p>";
            echo "<input type='radio' name='question_" . $row['question_id'] . "' value='1'> " . $row['answer_choice_1'] . "<br>";
            echo "<input type='radio' name='question_" . $row['question_id'] . "' value='2'> " . $row['answer_choice_2'] . "<br>";
            echo "<input type='radio' name='question_" . $row['question_id'] . "' value='3'> " . $row['answer_choice_3'] . "<br>";
            echo "<input type='radio' name='question_" . $row['question_id'] . "' value='4'> " . $row['answer_choice_4'] . "<br><br>";
        }
    } else {
        echo "<p>No questions found for this quiz.</p>";
    }

    $stmt->close();
} else {
    echo "Error: Quiz ID is missing.";
}

$conn->close();
?>
