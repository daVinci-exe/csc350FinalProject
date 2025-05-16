<?php
// Author:        Josiah de Leon
// Class:         CSC 350
// Instructor:    Dr. Galathara Kahanda
// Filename:      admin_login.php
// Description:   Handles the admin login functionality for a web application.


session_start();


// Function to check if the user is already logged in
function checkLoggedIn() {
    if (isset($_SESSION['admin_id'])) {
        header("Location: admin_dashboard.php");
        exit();
    }
}

// Function to validate and process the login form
function processLogin($conn) {
    $error = null;

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = mysqli_real_escape_string($conn, $_POST['username']);
        $password = $_POST['password'];

        $sql = "SELECT admin_id, password FROM admin WHERE username = '$username'";
        $result = mysqli_query($conn, $sql);

        if (!$result) {
            die("Query failed: " . mysqli_error($conn));
        }

        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            if (password_verify($password, $row['password'])) {
                $_SESSION['admin_id'] = $row['admin_id'];
                header("Location: admin_dashboard.php");
                exit();
            } else {
                $error = "Invalid password.";
            }
        } else {
            $error = "No admin user found with that username.";
        }
    }

    return $error;
}

// Include database connection
include 'db_connect.php';

// Check if user is already logged in
checkLoggedIn();

// Process the login form and get any error message
$error = processLogin($conn);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login</title>
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
        .main-login-btn {
            background-color: #007BFF;
        }
        .main-login-btn:hover {
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
        <h2>Admin Login</h2>
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
        <a href="index.php"><button class="btn main-login-btn">Go to Main Login</button></a>

        <!-- Debugging output -->
        <p class="debug"><?php echo $dashboardExists; ?></p>
    </div>
</body>
</html>

<?php

mysqli_close($conn);
?>
