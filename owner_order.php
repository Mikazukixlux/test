<?php
session_start();
include 'db.php';

// Check if the user is logged in
if (!isset($_SESSION['user'])) {
    echo "You must be logged first.";
    exit;
}

$user = $_SESSION['user'];
$no_matric = $user['no_matric'];;

// Fetch the products owned by the user
$product_sql = "SELECT * FROM products WHERE owner_id = '$no_matric'";
$product_result = $conn->query($product_sql);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = (int)$_POST['product_id'];
    $quantity = (int)$_POST['quantity'];

    // Validate product ownership
    $check_product_sql = "SELECT * FROM products WHERE product_id = $product_id AND owner_id = '$user_id'";
    $product_check_result = $conn->query($check_product_sql);

    if ($product_check_result->num_rows === 0) {
        echo "Invalid product or you do not own this product.";
        exit;
    }

    $product = $product_check_result->fetch_assoc();

    // Check stock availability
    if ($quantity > $product['stock_quantity']) {
        echo "Insufficient stock available.";
        exit;
    }

    // Calculate total price
    $total_price = $product['price'] * $quantity;

    // Insert into orders table
    $order_sql = "INSERT INTO orders (no_matric, total_price, status) VALUES ('$user_id', $total_price, 'pending')";
    if ($conn->query($order_sql)) {
        $order_id = $conn->insert_id;

        // Insert into order_details table
        $order_detail_sql = "INSERT INTO order_details (product_id, order_id, quantity, unit_price) 
                             VALUES ($product_id, $order_id, $quantity, {$product['price']})";
        $conn->query($order_detail_sql);

        // Update stock quantity
        $new_stock = $product['stock_quantity'] - $quantity;
        $update_stock_sql = "UPDATE products SET stock_quantity = $new_stock WHERE product_id = $product_id";
        $conn->query($update_stock_sql);

        echo "Order placed successfully! <a href='owner_order.php'>Go back</a>";
    } else {
        echo "Error placing order: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Owner Order</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Order from Your Products</h1>
    <form action="" method="POST">
        <label for="product_id">Select Product:</label>
        <select name="product_id" id="product_id" required>
            <?php while ($row = $product_result->fetch_assoc()): ?>
                <option value="<?= $row['product_id'] ?>"><?= $row['name'] ?> (Stock: <?= $row['stock_quantity'] ?>)</option>
            <?php endwhile; ?>
        </select><br><br>

        <label for="quantity">Quantity:</label>
        <input type="number" name="quantity" id="quantity" min="1" required><br><br>

        <button type="submit">Place Order</button>
    </form>
</body>
</html>
