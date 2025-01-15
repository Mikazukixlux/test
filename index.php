<?php
session_start();  // Start session
require 'db.php';  // Including the database connection file
if (isset($_SESSION['user_id'])) {
    header("Location: menu.php");
    exit;
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
        $username = $conn->real_escape_string($_POST['matric']);
    $password = $conn->real_escape_string($_POST['password']);

    $query = "SELECT * FROM users WHERE no_matric = '$username' AND password = '$password'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $_SESSION['user'] = $result->fetch_assoc();
        header("Location: menu.php");
        exit;
    } else {
        $error = "Invalid username or password!";
    }
    } elseif (isset($_POST['register'])) {
        // Registration functionality
        $no_matric = $_POST['no_matric'];
        $email = $_POST['email'];
        $password = $_POST['password'];

        $sql = "INSERT INTO Users (no_matric, email, password) VALUES ('$no_matric', '$email', '$password')";

        if ($conn->query($sql) === TRUE) {
            $success = "Registration successful";
        } else {
            $error = "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Website with Login And Registration</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="index">
<header>
        <img src="logo.png" class="logo">
        <nav class="navigation">
            <a href="menu.php">Home</a>
            <a href="user_profile.php?id=no_matric">Profile</a>
            <a href="my_product.php">Product</a>
            <a href="check_order.php">Order</a>
            <button class="btnlogin-popup">Login</button>
        </nav>
    </header>
    <div class="wrapper">
        <span class="icon-close"><ion-icon name="close"></ion-icon></span>
        
        <!-- Login Form -->
        <div class="form-box login">
            <h2>Login</h2>
            <?php if(isset($error)) echo "<p class='error'>$error</p>"; ?>
            <form method="POST" action="">
                <div class="inputbox">
                    <span class="icon"><ion-icon name="mail"></ion-icon></span>
                    <input type="text" name="matric" required>
                    <label>NO MATRIC</label>
                </div>
                <div class="inputbox">
                    <span class="icon"><ion-icon name="lock-closed"></ion-icon></span>
                    <input type="password" name="password" required>
                    <label>PASSWORD</label>
                </div>
                <button type="submit" name="login" class="btn">Login</button>
                <div class="login-register">
                    <p>Don't Have Account <a href="#" class="register-link">Register</a></p>
                </div>
            </form>
        </div>

        <!-- Registration Form -->
        <div class="form-box register">
            <h2>Registration</h2>
            <?php if(isset($success)) echo "<p class='success'>$success</p>"; ?>
            <?php if(isset($error)) echo "<p class='error'>$error</p>"; ?>
            <form method="POST" action="">
                <div class="inputbox">
                    <span class="icon"><ion-icon name="person-circle-outline"></ion-icon></span>
                    <input type="text" name="no_matric" required>
                    <label>NO MATRIC</label>
                </div>
                <div class="inputbox">
                    <span class="icon"><ion-icon name="mail"></ion-icon></span>
                    <input type="email" name="email" required>
                    <label>EMAIL</label>
                </div>
                <div class="inputbox">
                    <span class="icon"><ion-icon name="lock-closed"></ion-icon></span>
                    <input type="password" name="password" required>
                    <label>PASSWORD</label>
                </div>
                <div class="remeber-forgot">
                    <label><input type="checkbox" name="terms"> I agree to term & condition</label>
                </div>
                <button type="submit" name="register" class="btn">Register</button>
                <div class="login-register">
                    <p>Already Have Account <a href="#" class="login-link">Login</a></p>
                </div>
            </form>
        </div>
    </div>
    <script src="script.js"></script>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</body>
</html>
