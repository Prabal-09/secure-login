<?php
$conn = new mysqli("localhost", "root", "", "login_demo");

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $res = $conn->query("SELECT * FROM users WHERE username = '$username'");
    $row = $res->fetch_assoc();

    if ($row && password_verify($password, $row['password'])) {
        echo "Login Successful (Secure)";
        header("Location: dashboard.php");
        exit();
    } else {
        echo "Invalid Credentials";
    }
}
?>

