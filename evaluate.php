<?php
// Author:       Josiah de Leon
// Class:        CSC 350
// Instructor:   Dr. Galathara Kahanda
// Filename:     evaluate.php
// Description:  Handles the evaluation form submission for a group project in CSC 350. Also
//               ensures scores are within valid ranges, and inserts the evaluation into the 
//               database.


session_start();

// Function to check if the judge is logged in
function checkLoggedIn() {
    if (!isset($_SESSION['id'])) {
        header("Location: index.php");
        exit();
    }
}

// Function to establish database connection and fetch groups
function fetchGroups($conn) {
    if (!$conn) {
        die("Database connection failed: " . mysqli_connect_error());
    }

    $groups = mysqli_query($conn, "SELECT group_id, group_number, project_title, member_names FROM groups");
    if (!$groups) {
        die("Query failed: " . mysqli_error($conn));
    }

    return $groups;
}

// Function to process the evaluation form submission
function processEvaluation($conn) {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Retrieve form data
        $group_id = (int) $_POST['group_id'];
        $articulate_dev = (int) $_POST['articulate_dev'];
        $articulate_acc = (int) $_POST['articulate_acc'];
        $tools_dev = (int) $_POST['tools_dev'];
        $tools_acc = (int) $_POST['tools_acc'];
        $presentation_dev = (int) $_POST['presentation_dev'];
        $presentation_acc = (int) $_POST['presentation_acc'];
        $team_dev = (int) $_POST['team_dev'];
        $team_acc = (int) $_POST['team_acc'];
        $comments = mysqli_real_escape_string($conn, $_POST['comments']);
        $judge_id = $_SESSION['id'];

        // Calculate total (sum of all Developing and Accomplished scores)
        $total = $articulate_dev + $articulate_acc + $tools_dev + $tools_acc + $presentation_dev + $presentation_acc + $team_dev + $team_acc;

        // Server side validation to ensure total is between 1 and 100
        if ($total < 1 || $total > 100) {
            echo "Error: Total score must be between 1 and 100. Current total: $total";
            exit();
        }

        // Validate Accomplished scores are between 10 and 15
        $acc_scores = [$articulate_acc, $tools_acc, $presentation_acc, $team_acc];
        foreach ($acc_scores as $score) {
            if ($score < 10 || $score > 15) {
                echo "Error: Accomplished scores must be between 10 and 15.";
                exit();
            }
        }

        // Inserts evaluation into database
        $sql = "INSERT INTO evaluations (judge_id, group_id, articulate_dev, articulate_acc, tools_dev, tools_acc, presentation_dev, presentation_acc, team_dev, team_acc, total, comments)
                VALUES ('$judge_id', '$group_id', '$articulate_dev', '$articulate_acc', '$tools_dev', '$tools_acc', '$presentation_dev', '$presentation_acc', '$team_dev', '$team_acc', '$total', '$comments')";
        
        if (mysqli_query($conn, $sql)) {
            echo "Evaluation submitted!";
        } else {
            echo "Error: " . mysqli_error($conn);
        }

        // Close the database connection after submission
        mysqli_close($conn);
    }
}

// Checks if the judge is logged in
checkLoggedIn();

//Include database connection
include 'db_connect.php';

$groups = fetchGroups($conn);

// Proceses the evaluation form if submitted
processEvaluation($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Computer Science Project Evaluation</title>
    <style>
        table {
            border-collapse: collapse;
            width: 80%;
        }
        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h1>Computer Science Project Evaluation</h1>
    <form method="POST" action="">
        <label>Select Group:</label>
        <select name="group_id" required>
            <option value="">--Select Group--</option>
            <?php while ($group = mysqli_fetch_assoc($groups)) { ?>
                <option value="<?php echo $group['group_id']; ?>">
                    Group <?php echo $group['group_number']; ?> - <?php echo $group['project_title']; ?>
                </option>
            <?php } ?>
        </select><br><br>

        <table>
            <tr>
                <th>Criteria</th>
                <th>Developing (1-10)</th>
                <th>Accomplished (10-15)</th>
                <th>Total</th>
            </tr>
            <tr>
                <td>Articulate requirements</td>
                <td><input type="number" name="articulate_dev" min="1" required></td>
                <td><input type="number" name="articulate_acc" min="10" max="15" required></td>
                <td id="articulate_total">0</td>
            </tr>
            <tr>
                <td>Choose appropriate tools and methods for each task</td>
                <td><input type="number" name="tools_dev" min="1" required></td>
                <td><input type="number" name="tools_acc" min="10" max="15" required></td>
                <td id="tools_total">0</td>
            </tr>
            <tr>
                <td>Give clear and coherent oral presentation</td>
                <td><input type="number" name="presentation_dev" min="1" required></td>
                <td><input type="number" name="presentation_acc" min="10" max="15" required></td>
                <td id="presentation_total">0</td>
            </tr>
            <tr>
                <td>Functioned well as a team</td>
                <td><input type="number" name="team_dev" min="1" required></td>
                <td><input type="number" name="team_acc" min="10" max="15" required></td>
                <td id="team_total">0</td>
            </tr>
            <tr>
                <td><strong>Total</strong></td>
                <td id="total_dev">0</td>
                <td id="total_acc">0</td>
                <td id="total_sum">0</td>
            </tr>
        </table><br>

        <label>Judge's Name:</label> <?php echo $_SESSION['judge_name']; ?><br><br>
        <label>Comments:</label><br>
        <textarea name="comments" rows="4" cols="50"></textarea><br><br>
        <button type="submit" id="submitBtn">Submit</button>
    </form>

    <br>
    <a href="index.php"><button>Logout</button></a>

    <script>
        // Calculates totals dynamically and validate total score
        const inputs = document.querySelectorAll('input[type="number"]');
        const submitBtn = document.getElementById('submitBtn');

        inputs.forEach(input => {
            input.addEventListener('input', calculateTotals);
        });

        function calculateTotals() {
            let totalDev = 0;
            let totalAcc = 0;

            // Articulate totals
            const articulateDev = parseInt(document.querySelector('[name="articulate_dev"]').value) || 0;
            const articulateAcc = parseInt(document.querySelector('[name="articulate_acc"]').value) || 0;
            document.getElementById('articulate_total').textContent = articulateDev + articulateAcc;
            totalDev += articulateDev;
            totalAcc += articulateAcc;

            // Tools totals
            const toolsDev = parseInt(document.querySelector('[name="tools_dev"]').value) || 0;
            const toolsAcc = parseInt(document.querySelector('[name="tools_acc"]').value) || 0;
            document.getElementById('tools_total').textContent = toolsDev + toolsAcc;
            totalDev += toolsDev;
            totalAcc += toolsAcc;

            // Presentation totals
            const presentationDev = parseInt(document.querySelector('[name="presentation_dev"]').value) || 0;
            const presentationAcc = parseInt(document.querySelector('[name="presentation_acc"]').value) || 0;
            document.getElementById('presentation_total').textContent = presentationDev + presentationAcc;
            totalDev += presentationDev;
            totalAcc += presentationAcc;

            // Team totals
            const teamDev = parseInt(document.querySelector('[name="team_dev"]').value) || 0;
            const teamAcc = parseInt(document.querySelector('[name="team_acc"]').value) || 0;
            document.getElementById('team_total').textContent = teamDev + teamAcc;
            totalDev += teamDev;
            totalAcc += teamAcc;

            // Update final totals
            const totalSum = totalDev + totalAcc;
            document.getElementById('total_dev').textContent = totalDev;
            document.getElementById('total_acc').textContent = totalAcc;
            document.getElementById('total_sum').textContent = totalSum;

            // Enable/disable submit button based on total
            if (totalSum < 1 || totalSum > 100) {
                submitBtn.disabled = true;
                submitBtn.title = "Total score must be between 1 and 100.";
            } else {
                submitBtn.disabled = false;
                submitBtn.title = "";
            }
        }
    </script>
</body>
</html>

<?php
// Close the database connection if not already closed
if (isset($conn) && !$conn->connect_error) {
    mysqli_close($conn);
}
?>
