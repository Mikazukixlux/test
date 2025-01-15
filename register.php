<?php
require 'db.php';  // Including the database connection file

if ($_SERVER["REQUEST_METHOD"] == "register") {
    $no_matric = $_POST['no_matric'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $terms = isset($_POST['terms']) ? 1 : 0;

    $sql = "INSERT INTO Users (no_matric, email, password, terms) VALUES ('$no_matric', '$email', '$password')";

    if ($conn->query($sql) === TRUE) {
        echo "Registration successful";
        // Redirect or perform further actions
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>
