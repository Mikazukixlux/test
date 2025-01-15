<?php
session_start(); // Start the session
include 'db.php';

if (!isset($_SESSION['user'])) {
    echo "You must be logged in to place an order.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = (int)$_POST['product_id'];
    $quantity = (int)$_POST['quantity'];
    $payment_method = $_POST['payment_method']; // Capture payment method
    $user = $_SESSION['user'];
    $no_matric = $user['no_matric'];

    // Validate product and stock
    $product_sql = "SELECT * FROM products WHERE product_id = $product_id";
    $product_result = $conn->query($product_sql);
    $product = $product_result->fetch_assoc();

    if (!$product || $quantity > $product['stock_quantity']) {
        echo "Invalid product or insufficient stock.";
        exit;
    }

    // Calculate total price
    $unit_price = $product['price'];
    $total_price = $unit_price * $quantity;

   
    if ($payment_method === 'qr') {
        
        echo "Your order will process by seller...<br>";
        
    } elseif ($payment_method === 'cod') {
        // Process Cash on Delivery
        echo "You have selected Cash on Delivery.<br>";
    } else {
        echo "Invalid payment method.";
        exit;
    }

    // Insert into orders
    $order_sql = "INSERT INTO orders (no_matric, total_price, status) 
                  VALUES ('$no_matric', $total_price, 'pending')";
    if ($conn->query($order_sql)) {
        $order_id = $conn->insert_id;

        // Insert into order_details
        $detail_sql = "INSERT INTO order_details (product_id, order_id, quantity, unit_price) 
                       VALUES ($product_id, $order_id, $quantity, $unit_price)";
        $conn->query($detail_sql);

        // Update product stock
        $new_stock = $product['stock_quantity'] - $quantity;
        $update_stock_sql = "UPDATE products SET stock_quantity = $new_stock WHERE product_id = $product_id";
        $conn->query($update_stock_sql);

        echo "Order placed successfully! <a href='menu.php'>Back to Menu</a>";
    } else {
        echo "Error placing order: " . $conn->error;
    }
}

$conn->close();
?>
