<?php
session_start();
include('db.php');

if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}

$user = $_SESSION['user'];
$no_matric = $user['no_matric'];

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? $user['username'];
    $email = $_POST['email'] ?? $user['email'];
    $phone_number = $_POST['phone_number'] ?? $user['phone_number'];
    $address = $_POST['address'] ?? $user['address'];

    // Update the database
    $update_sql = "UPDATE users SET username = ?, email = ?, phone_number = ?, address = ? WHERE no_matric = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("sssss", $username, $email, $phone_number, $address, $no_matric);

    if ($stmt->execute()) {
        // Update session data
        $_SESSION['user']['username'] = $username;
        $_SESSION['user']['email'] = $email;
        $_SESSION['user']['phone_number'] = $phone_number;
        $_SESSION['user']['address'] = $address;
        $message = "Profile updated successfully!";
    } else {
        $error = "Failed to update profile: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit Profile</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 50px;
            min-height: 100vh;
            background: url('background.jpeg') no-repeat;
            background-size: cover;
            background-position: center;
        }
        .container {
            width: 60%;
            margin: 20px auto;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }
        h2 {
            color: #333;
            text-align: center;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            font-size: 14px;
            margin-bottom: 5px;
        }
        input[type="text"], input[type="email"], textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        textarea {
            resize: vertical;
        }
        .form-actions {
            text-align: center;
        }
        button {
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
        .message {
            text-align: center;
            color: green;
        }
        .error {
            text-align: center;
            color: red;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Edit Profile</h2>
        <?php if (isset($message)): ?>
            <p class="message"><?= $message ?></p>
        <?php endif; ?>
        <?php if (isset($error)): ?>
            <p class="error"><?= $error ?></p>
        <?php endif; ?>
        <form method="POST" action="">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" name="username" id="username" value="<?= htmlspecialchars($user['username']) ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" value="<?= htmlspecialchars($user['email']) ?>" required>
            </div>
            <div class="form-group">
                <label for="phone_number">Phone Number</label>
                <input type="text" name="phone_number" id="phone_number" value="<?= htmlspecialchars($user['phone_number']) ?>">
            </div>
            <div class="form-group">
                <label for="address">Address</label>
                <textarea name="address" id="address" rows="4"><?= htmlspecialchars($user['address'] ?? '') ?></textarea>
            </div>
            <div class="form-actions">
                <button type="submit">Save Changes</button>
                <a href="user_profile.php" style="margin-left: 10px; text-decoration: none; color: #007bff;">Cancel</a>
            </div>
        </form>
    </div>
</body>
</html>
