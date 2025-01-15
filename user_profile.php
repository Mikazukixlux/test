<?php
session_start();
include('db.php');

if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}

$user = $_SESSION['user'];
$no_matric = $user['no_matric'];
$qr_code_path = $user['qr_code_path'] ?? null; // Assuming you store QR code path in the session or DB

// Handle QR code upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['qr_code'])) {
    $upload_dir = 'uploads/qr_codes/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $file = $_FILES['qr_code'];
    $file_name = basename($file['name']);
    $target_file = $upload_dir . time() . '_' . $file_name;

    // Validate file type
    $allowed_types = ['image/png', 'image/jpeg', 'image/jpg'];
    if (in_array($file['type'], $allowed_types)) {
        if (move_uploaded_file($file['tmp_name'], $target_file)) {
            // Update the database with the new QR code path
            $update_qr_sql = "UPDATE users SET qr = '$target_file' WHERE no_matric = '$no_matric'";
            if ($conn->query($update_qr_sql)) {
                $qr_code_path = $target_file;
                $message = "QR code uploaded successfully!";
            } else {
                $error = "Failed to save QR code: " . $conn->error;
            }
        } else {
            $error = "Failed to upload file.";
        }
    } else {
        $error = "Invalid file type. Only PNG, JPEG, and JPG are allowed.";
    }
}

// Handle Confirm Received action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_received'])) {
    $order_id = $_POST['order_id'];

    // Update the order status to 'Confirmed'
    $update_order_sql = "UPDATE orders SET status = 'confirmed' WHERE order_id = '$order_id' AND no_matric = '$no_matric'";
    if ($conn->query($update_order_sql)) {
        $message = "Order status updated successfully!";
    } else {
        $error = "Failed to update order status: " . $conn->error;
    }
}

// Handle Cancel Order action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_order'])) {
    $order_id = $_POST['order_id'];

    // Update the order status to 'Cancelled'
    $update_order_sql = "UPDATE orders SET status = 'cancelled' WHERE order_id = '$order_id' AND no_matric = '$no_matric'";
    if ($conn->query($update_order_sql)) {
        $message = "Order cancelled successfully!";
    } else {
        $error = "Failed to cancel order: " . $conn->error;
    }
}

// Fetch orders
$query = "SELECT * FROM orders WHERE no_matric = '$no_matric'";
$orders = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Profile</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 100px;
            min-height: 100vh;
            background: url('background.jpeg') no-repeat;
            background-size: cover;
            background-position: center;
        }
        .container {
            width: 80%;
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
        p {
            font-size: 16px;
            line-height: 1.6;
        }
        .editlogout {
            display: inline-block;
            margin: 10px 0;
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            text-align: center;
        }
        .editlogout:hover {
            background-color: #0056b3;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }
        th {
            background-color: #007bff;
            color: #fff;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        tr:hover {
            background-color: #ddd;
        }
        .no-orders {
            text-align: center;
            font-size: 18px;
            color: #555;
            margin-top: 20px;
        }
        .confirm-button {
            background-color: #28a745;
            color: #fff;
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .confirm-button:hover {
            background-color: #218838;
        }
        .qr-container {
            margin-top: 20px;
            text-align: center;
        }
        .qr-container img {
            max-width: 150px;
            height: auto;
            display: block;
            margin: 10px auto;
            border: 1px solid #ddd;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>User Profile</h2>
        <p><strong>No Matric:</strong> <?php echo $user['no_matric']; ?></p>
        <p><strong>Username:</strong> <?php echo $user['username']; ?></p>
        <p><strong>Email:</strong> <?php echo $user['email']; ?></p>
        <p><strong>Phone Number:</strong> <?php echo $user['phone_number'] ?: 'Not Provided'; ?></p>
        <p><strong>Address:</strong> <?php echo $user['address'] ?: 'Not Provided'; ?></p>

        <a class="editlogout" href="edit_profile.php">Edit Profile</a>
        <a class="editlogout" href="logout.php">Logout</a>

        <div class="qr-container">
            <h3>Your QR Code</h3>
            <?php if ($qr_code_path): ?>
                <img src="<?php echo $qr_code_path; ?>" alt="QR Code">
            <?php else: ?>
                <p>No QR code uploaded yet.</p>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data" action="">
                <label for="qr_code">Upload QR Code:</label><br>
                <input type="file" name="qr_code" id="qr_code" accept="image/*" required>
                <button type="submit">Upload</button>
            </form>
        </div>

        <h2>Orders</h2>
        <?php if (isset($message)): ?>
            <p style="color: green;"><?= $message ?></p>
        <?php endif; ?>
        <?php if (isset($error)): ?>
            <p style="color: red;"><?= $error ?></p>
        <?php endif; ?>
        <?php if ($orders->num_rows > 0): ?>
            <table>
                <tr>
                    <th>Order ID</th>
                    <th>Order Date</th>
                    <th>Status</th>
                    <th>Total Price</th>
                    <th>Actions</th>
                </tr>
                <?php while ($order = $orders->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $order['order_id']; ?></td>
                        <td><?php echo $order['order_date']; ?></td>
                        <td><?php echo ucfirst($order['status']); ?></td>
                        <td>RM<?php echo number_format($order['total_price'], 2); ?></td>
                        <td>
                            <?php if ($order['status'] === 'processing'): ?>
                                <form method="POST" action="">
                                    <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                                    <button type="submit" name="confirm_received" class="confirm-button">
                                        Confirm Received
                                    </button>
                                </form>
                            <?php endif; ?>
                            
                            <?php if ($order['status'] === 'pending'): ?>
                                <form method="POST" action="">
                                    <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                                    <button type="submit" name="cancel_order" class="confirm-button">
                                        CANCEL ORDER
                                    </button>
                                </form>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p class="no-orders">No orders found.</p>
        <?php endif; ?>
    </div>
</body>
</html>
