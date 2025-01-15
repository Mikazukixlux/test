<?php
session_start();
include 'db.php';

// Check if the user is logged in
if (!isset($_SESSION['user'])) {
    echo "You must log in first.";
    exit;
}

$user = $_SESSION['user'];
$no_matric = $user['no_matric'];

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $order_id = (int)$_POST['order_id'];
    $new_status = htmlspecialchars($_POST['status']);

    // Check the current status of the order
    $current_status_sql = "SELECT status FROM orders WHERE order_id = $order_id";
    $current_status_result = $conn->query($current_status_sql);

    if ($current_status_result && $current_status_result->num_rows > 0) {
        $current_status = $current_status_result->fetch_assoc()['status'];

        // Proceed only if the status is being changed to "canceled"
        if ($new_status === 'canceled' && $current_status !== 'canceled') {
            // Fetch the product details from the order
            $order_items_sql = "
                SELECT od.product_id, od.quantity
                FROM order_details od
                JOIN products p ON od.product_id = p.product_id
                WHERE od.order_id = $order_id AND p.owner_id = '$no_matric'
            ";
            $order_items_result = $conn->query($order_items_sql);

            if ($order_items_result->num_rows > 0) {
                // Update stock quantities for each product in the order
                while ($item = $order_items_result->fetch_assoc()) {
                    $product_id = $item['product_id'];
                    $quantity = $item['quantity'];

                    $update_stock_sql = "UPDATE products SET stock_quantity = stock_quantity + $quantity WHERE product_id = $product_id";
                    $conn->query($update_stock_sql);
                }

                // Update the order status to "canceled"
                $update_status_sql = "UPDATE orders SET status = '$new_status' WHERE order_id = $order_id";
                if ($conn->query($update_status_sql)) {
                    $message = "Order canceled successfully, and stock quantities updated.";
                } else {
                    $error = "Failed to update order status: " . $conn->error;
                }
            } else {
                $error = "No items found for this order.";
            }
        } elseif ($new_status !== 'canceled' || $current_status === 'canceled') {
            // Update the order status for non-canceled updates
            $update_status_sql = "UPDATE orders SET status = '$new_status' WHERE order_id = $order_id";
            if ($conn->query($update_status_sql)) {
                $message = "Order status updated successfully!";
            } else {
                $error = "Failed to update order status: " . $conn->error;
            }
        }
    } else {
        $error = "Order not found or invalid status.";
    }
}

// Fetch orders for the products owned by the user
$order_sql = "
    SELECT 
        o.order_id, 
        o.no_matric, 
        o.order_date, 
        o.status, 
        o.total_price, 
        p.name AS product_name, 
        od.quantity, 
        od.unit_price, 
        u.username AS buyer_name, 
        u.address AS buyer_address, 
        u.phone_number AS buyer_phone
    FROM orders o
    JOIN order_details od ON o.order_id = od.order_id
    JOIN products p ON od.product_id = p.product_id
    JOIN users u ON o.no_matric = u.no_matric
    WHERE p.owner_id = '$no_matric'
    ORDER BY o.order_date DESC
";
$order_result = $conn->query($order_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check Orders</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: url('background.jpeg') no-repeat;
            background-size: cover;
            background-position: center;
        }
        h1 {
            text-align: center;
            margin-top: 20px;
            color: #333;
        }
        table {
            width: 90%;
            margin: 20px auto;
            border-collapse: collapse;
            background: #fff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        table th, table td {
            padding: 12px;
            text-align: center;
            border: 1px solid #ddd;
        }
        table th {
            background: #007BFF;
            color: white;
        }
        table tr:nth-child(even) {
            background: #f2f2f2;
        }
        table tr:hover {
            background: #ddd;
        }
        form select, form button {
            padding: 8px 12px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
        }
        form button {
            background-color: #28a745;
            color: white;
            border: none;
            cursor: pointer;
        }
        form button:hover {
            background-color: #218838;
        }
        .message {
            width: 90%;
            margin: 20px auto;
            text-align: center;
            padding: 10px;
            border-radius: 4px;
        }
        .message.success {
            background-color: #d4edda;
            color: #155724;
        }
        .message.error {
            background-color: #f8d7da;
            color: #721c24;
        }
        a {
            display: block;
            text-align: center;
            margin: 20px auto;
            padding: 10px 20px;
            width: 150px;
            background: #007BFF;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
        }
        a:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <h1>Orders Received for Your Products</h1>

    <?php if (isset($message)): ?>
        <div class="message success"><?= $message ?></div>
    <?php endif; ?>
    <?php if (isset($error)): ?>
        <div class="message error"><?= $error ?></div>
    <?php endif; ?>

    <?php if ($order_result->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Buyer Name</th>
                    <th>Buyer Address</th>
                    <th>Buyer Phone</th>
                    <th>Order Date</th>
                    <th>Status</th>
                    <th>Total Price</th>
                    <th>Product Name</th>
                    <th>Quantity</th>
                    <th>Unit Price</th>
                    <th>Update Status</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $order_result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['order_id'] ?></td>
                        <td><?= htmlspecialchars($row['buyer_name']) ?></td>
                        <td><?= htmlspecialchars($row['buyer_address']) ?></td>
                        <td><?= htmlspecialchars($row['buyer_phone']) ?></td>
                        <td><?= $row['order_date'] ?></td>
                        <td><?= ucfirst($row['status']) ?></td>
                        <td>$<?= number_format($row['total_price'], 2) ?></td>
                        <td><?= $row['product_name'] ?></td>
                        <td><?= $row['quantity'] ?></td>
                        <td>$<?= number_format($row['unit_price'], 2) ?></td>
                        <td>
                            <form method="POST" action="">
                                <input type="hidden" name="order_id" value="<?= $row['order_id'] ?>">
                                <select name="status" required>
                                    <option value="pending" <?= $row['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                                    <option value="processing" <?= $row['status'] === 'processing' ? 'selected' : '' ?>>Processing</option>
                                    <option value="canceled" <?= $row['status'] === 'canceled' ? 'selected' : '' ?>>Canceled</option>
                                </select>
                                <button type="submit" name="update_status">Update</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p style="text-align: center; color: #555;">No orders received for your products yet.</p>
    <?php endif; ?>

    <a href="menu.php">Back to Menu</a>
</body>
</html>

