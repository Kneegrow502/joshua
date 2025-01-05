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

// Ensure lrn exists in the session
if (!isset($_SESSION['lrn'])) {
    die("Error: lrn not found in session. Please log in first.");
}

$lrn = $_SESSION['lrn']; // Get lrn from session

// Check for quiz_id in POST
if (!isset($_POST['quiz_id'])) {
    die("Error: Quiz ID is missing.");
}

$quiz_id = intval($_POST['quiz_id']);

$total_score = 0;

// Process submitted answers
foreach ($_POST as $key => $value) {
    if (strpos($key, 'question_') === 0) {
        $question_id = intval(str_replace('question_', '', $key));
        $selected_answer = intval($value);

        // Get the correct answer for this question
        $sql = "SELECT correct_answer FROM questions WHERE question_id = ?";
        $stmt = $conn->prepare($sql);
        
        // Check if prepare statement succeeded
        if ($stmt === false) {
            die("Error preparing the statement: " . $conn->error);
        }

        $stmt->bind_param("i", $question_id);
        $stmt->execute();
        $stmt->bind_result($correct_answer);
        $stmt->fetch();
        $stmt->close();

        // If the selected answer is correct, increase the score
        if ($selected_answer === $correct_answer) {
            $total_score++;
        }

        // Insert the answer into the student_answers table
        $sql = "INSERT INTO student_answers (lrn, question_id, selected_answer, quiz_id) 
                VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        
        // Check if prepare statement succeeded
        if ($stmt === false) {
            die("Error preparing the statement: " . $conn->error);
        }

        $stmt->bind_param("iiii", $lrn, $question_id, $selected_answer, $quiz_id);
        $stmt->execute();
    }
}

// After processing all answers, insert the score into quiz_scores
$sql = "INSERT INTO quiz_scores (lrn, quiz_id, score) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);

// Check if prepare statement succeeded
if ($stmt === false) {
    die("Error preparing the statement: " . $conn->error);
}

$stmt->bind_param("iii", $lrn, $quiz_id, $total_score);
$stmt->execute();

// Output success message
echo "Quiz submitted successfully! Your score is: $total_score";

// Close the connection
$conn->close();
?>
