<?php
$conn = new mysqli("localhost", "root", "", "login_demo");
if (isset($_POST['submit'])) {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $conn->query("INSERT INTO users (username, password) VALUES ('$username', '$password')");
    echo "Registered securely!";
}
?>
