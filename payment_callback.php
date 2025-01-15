<?php
include 'db.php';

// Example of payment verification logic (depends on the gateway)
if ($_GET['payment_status'] === 'success') {
    $order_id = (int)$_GET['order_id'];

    // Update order status to "paid"
    $update_sql = "UPDATE orders SET status = 'paid' WHERE order_id = $order_id";
    if ($conn->query($update_sql)) {
        echo "Payment successful! Your order has been placed.";
    } else {
        echo "Error updating order: " . $conn->error;
    }
} else {
    echo "Payment failed. Please try again.";
}
?>
