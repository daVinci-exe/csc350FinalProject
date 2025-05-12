<?php
session_start();
include 'db_connect.php';


if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    $sql = "SELECT judge_id, password, name FROM judge WHERE username = '$username'";
    $result = mysqli_query($conn, $sqli);

    if(mysqli_num_rows($result) > 0){
        $row = mysqli_fetch_assoc($result);
        if (password_verify($password, $row['password'])){
            $_SESSION['judge_id'] = $row['judge_id'];
            $_SESSION['judge_name'] = $row['name'];
            header("Location: index.php");
        } else {
            echo "Invalid password! Please try again.";
        }

    } 
else {
        echo "Judge not found!";
     }
}
mysqli_close($conn)
?>
<form method ="POST">
    <input type ="text" name="username" placeholder="Username" required>
    <input type ="password" name="password" placeholder="Password" required>
    <button type ="submit">Login</button>
</form>