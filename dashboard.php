<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.html");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f1f1f1;
            text-align: center;
            padding-top: 80px;
        }

        .card {
            background: white;
            margin: auto;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 15px #ccc;
            width: 400px;
        }

        a {
            display: inline-block;
            margin-top: 20px;
            text-decoration: none;
            padding: 10px 20px;
            background: #007bff;
            color: white;
            border-radius: 6px;
        }
    </style>
</head>
<body>
    <div class="card">
        <h2>Welcome, <?php echo $_SESSION['username']; ?>!</h2>
        <p>You have successfully logged in using the <strong>Secure Login</strong> system.</p>
        <a href="logout.php">Logout</a>
    </div>
</body>
</html>
