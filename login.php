<?php
session_start();  // Start session
require 'db.php';  // Including the database connection file

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM Users WHERE email='$email' AND password='$password'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $_SESSION['user_id'] = $result->fetch_assoc()['user_id'];
        header("Location: test.php");
    } else {
        // Invalid login
        $error = "Invalid email or password";
    }

    $conn->close();
}
?>
