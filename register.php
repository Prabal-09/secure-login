<?php
$conn = new mysqli("localhost", "root", "", "login_demo");

if (isset($_POST['submit'])) {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

   $sql = "INSERT INTO users (username, password) VALUES ('$username', '$password')";
    if ($conn->query($sql)) {
        
        header("Location: login.html"); 
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
}
?>
