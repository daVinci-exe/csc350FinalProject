<?php
// Author:       Josiah de Leon
// Class:        CSC 350
// Instructor:   Dr. Galathara Kahanda
// Filename:     hash.php
// Description:  Generates a password hash for a predefined password string using the default hashing algorithm.

// Function to generate and display a password hash
function generatePasswordHash($password) {
    echo password_hash($password, PASSWORD_DEFAULT);
}

// Generate the password hash for 'iudex4'
generatePasswordHash('iudex4');
?>
