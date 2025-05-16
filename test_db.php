<?php
// Author:       Josiah de Leon
// Class:        CSC 350
// Instructor:   Dr. Galathara Kahanda
// Filename:     test_db.php
// Description:  Tests the database connection by connecting to the database and confirming success or not.

// Function to test the database connection
function testDatabaseConnection() {
    include 'db_connect.php';
    echo "Database connected successfully!";
    mysqli_close($conn);
}

// Execute the database connection test
testDatabaseConnection();
?>
