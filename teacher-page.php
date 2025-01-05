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

// Handle quiz creation
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['create_quiz'])) {
    $quiz_name = $_POST['quiz_name'];
    $quiz_description = $_POST['quiz_description'];
    $questions = $_POST['questions'];
    $answers = $_POST['answers'];
    $correct_answers = $_POST['correct_answers'];

    if (!empty($quiz_name) && !empty($quiz_description) && !empty($questions)) {
        // Insert quiz details
        $sql = "INSERT INTO quizzes (quiz_name, quiz_description) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $quiz_name, $quiz_description);

        if ($stmt->execute()) {
            $quiz_id = $stmt->insert_id;

            // Insert questions
            foreach ($questions as $index => $question) {
                $answer_choices = explode(";", $answers[$index]);
                $correct_answer = $correct_answers[$index];

                $sql = "INSERT INTO questions (quiz_id, question_text, answer_choice_1, answer_choice_2, answer_choice_3, answer_choice_4, correct_answer) 
                        VALUES (?, ?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param(
                    "isssssi",
                    $quiz_id,
                    $question,
                    $answer_choices[0],
                    $answer_choices[1],
                    $answer_choices[2],
                    $answer_choices[3],
                    $correct_answer
                );
                $stmt->execute();
            }

            echo "<p>Quiz created successfully!</p>";
        } else {
            echo "<p>Error creating quiz: " . $conn->error . "</p>";
        }
    } else {
        echo "<p>All fields are required.</p>";
    }
}

// Fetch all quizzes for the dropdown
$quizzes_sql = "SELECT * FROM quizzes";
$quizzes_result = $conn->query($quizzes_sql);
$quizzes = [];
while ($quiz = $quizzes_result->fetch_assoc()) {
    $quizzes[] = $quiz;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Dashboard</title>
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
        form {
            display: flex;
            flex-direction: column;
        }
        input[type="text"], textarea, select {
            margin: 10px 0;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }
        button {
            padding: 10px;
            background-color: #003366;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #002244;
        }
        .add-question-btn {
            margin-bottom: 20px;
            background-color: #4caf50;
        }
        .add-question-btn:hover {
            background-color: #388e3c;
        }

        /* Hamburger menu styles */
        .menu-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            padding: 20px;
        }

        .hamburger {
            font-size: 30px;
            cursor: pointer;
        }

        .dropdown-menu {
            display: none;
            position: absolute;
            top: 60px;
            left: 10px;
            background-color: #ffffff;
            border: 1px solid #ddd;
            border-radius: 5px;
            width: 200px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }

        .dropdown-menu a {
            padding: 10px;
            display: block;
            text-decoration: none;
            color: #003366;
        }

        .dropdown-menu a:hover {
            background-color: #f0f0f0;
        }
    </style>
</head>
<body>
    <!-- Hamburger Menu -->
    <div class="menu-container">
        
        <span class="hamburger" onclick="toggleMenu()">&#9776;</span>
        <div class="dropdown-menu" id="dropdown-menu">
            <h3>Quiz Records</h3>
            <?php foreach ($quizzes as $quiz): ?>
                <a href="view_quiz.php?quiz_id=<?= $quiz['quiz_id']; ?>"><?= $quiz['quiz_name']; ?></a>
            <?php endforeach; ?>
            <a href="student_record.php">Student Records</a> <!-- New menu item -->
            
        </div>
    </div>

    <div class="container">
        <h2>Create a New Quiz</h2>
        <form method="POST" action="">
            <input type="text" name="quiz_name" placeholder="Quiz Name" required>
            <textarea name="quiz_description" placeholder="Quiz Description" rows="3" required></textarea>

            <h3>Questions</h3>
            <div id="questions-container">
                <div class="question-group">
                    <input type="text" name="questions[]" placeholder="Question" required>
                    <textarea name="answers[]" placeholder="Answers (separated by ;)" rows="2" required></textarea>
                    <select name="correct_answers[]" required>
                        <option value="1">Answer 1</option>
                        <option value="2">Answer 2</option>
                        <option value="3">Answer 3</option>
                        <option value="4">Answer 4</option>
                    </select>
                </div>
            </div>
            <button type="button" class="add-question-btn" onclick="addQuestion()">Add Another Question</button>
            <button type="submit" name="create_quiz">Create Quiz</button>
        </form>
    </div>

    <script>
        // Toggle the dropdown menu visibility
        function toggleMenu() {
            const menu = document.getElementById('dropdown-menu');
            menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
        }

        // Add a new question to the form
        function addQuestion() {
            const questionGroup = document.createElement('div');
            questionGroup.classList.add('question-group');
            questionGroup.innerHTML = ` 
                <input type="text" name="questions[]" placeholder="Question" required>
                <textarea name="answers[]" placeholder="Answers (separated by ;)" rows="2" required></textarea>
                <select name="correct_answers[]" required>
                    <option value="1">Answer 1</option>
                    <option value="2">Answer 2</option>
                    <option value="3">Answer 3</option>
                    <option value="4">Answer 4</option>
                </select>
            `;
            document.getElementById('questions-container').appendChild(questionGroup);
        }
    </script>
</body>
</html>
