<?php
// Author:       Josiah de Leon
// Class:        CSC 350
// Instructor:   Dr. Galathara Kahanda
// Filename:     db_connect.php
// Description:  Establishes a connection to the MySQL database for use in other scripts.
//
// Defines database connection parameters
$host = 'sql102.ezyro.com'; // Database host
$user = 'ezyro_38994256'; // Database username
$password = '336af5668'; // Database password
$database = 'ezyro_38994256_Project350'; // Database name

// Create a connection to the MySQL database
$conn = mysqli_connect($host, $user, $password, $database);

// Check if the connection was successful
if (!$conn) {
    // If connection fails, display an error message and stop execution
    die("Connection failed: " . mysqli_connect_error());
}
?>
