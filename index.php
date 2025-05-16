<?php
// Author:       Josiah de Leon
// Class:        CSC 350
// Instructor:   Dr. Galathara Kahanda
// Filename:     index.php
// Description:  Handles the login functionality for judges in a web application


// Start a new session
session_start();

// Function to establish database connection
function connectToDatabase() {
    include 'db_connect.php';
    if (!$conn) {
        die("Database connection failed: " . mysqli_connect_error());
    }
    return $conn;
}

// Function to process the login form
function processLogin($conn) {
    $error = null;

    if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST['admin_login'])) {
        // Retrieve data
        $username = mysqli_real_escape_string($conn, $_POST['username']);
        $password = mysqli_real_escape_string($conn, $_POST['password']);

        // Query to verify username and password
        $sql = "SELECT judge_id, password, name FROM judges WHERE username = '$username'";
        $result = mysqli_query($conn, $sql);

        if (!$result) {
            die("Query failed: " . mysqli_error($conn));
        }

        // Verify credentials and set session variables
        $row = mysqli_fetch_assoc($result);
        if ($row && password_verify($password, $row['password'])) {
            $_SESSION['id'] = $row['judge_id'];
            $_SESSION['judge_name'] = $row['name'];
            header("Location: evaluate.php");
            exit();
        } else {
            $error = "Invalid username or password.";
        }
    }

    return $error;
}

// Function to handle admin login redirect
function handleAdminLoginRedirect() {
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['admin_login'])) {
        // Ensure no output has been sent before the redirect
        if (headers_sent()) {
            echo "<p class='error'>Cannot redirect: Headers already sent.</p>";
        } else {
            header("Location: admin_login.php");
            exit();
        }
    }
}

// Connect to the database
$conn = connectToDatabase();

// Process the login form and get any error message (debugging)
$error = processLogin($conn);

// Handle admin login redirect if the button is clicked
handleAdminLoginRedirect();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Judge Login</title>
    <style>
        .container {
            width: 300px;
            margin: 50px auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .error {
            color: red;
        }

        .btn {
            margin-top: 10px;
            padding: 10px;
            width: 100%;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }

        .btn:hover {
            background-color: #45a049;
        }

        .admin-login-btn {
            background-color: #007BFF;
        }

        .admin-login-btn:hover {
            background-color: #0056b3;
        }

        .debug {
            color: blue;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Judge Login</h2>
        <?php if (isset($error)) { ?>
            <p class="error"><?php echo $error; ?></p>
        <?php } ?>
        <form method="POST" action="">
            <label for="username">Username:</label><br>
            <input type="text" id="username" name="username" required><br><br>

            <label for="password">Password:</label><br>
            <input type="password" id="password" name="password" required><br><br>

            <button type="submit" class="btn">Login</button>
        </form>

        <!-- Form for Admin Login redirect -->
        <form method="POST" action="">
            <input type="hidden" name="admin_login" value="1">
            <button type="submit" class="btn admin-login-btn">Admin Login</button>
        </form>

</body>
</html>

<?php
// Close the database connection
mysqli_close($conn);
?>
