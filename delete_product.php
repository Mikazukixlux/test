<?php
session_start();
include 'db.php';

// Check if user is logged in
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}

$user = $_SESSION['user'];
$no_matric = $user['no_matric'];
$product_id = (int)$_GET['product_id'];

// Verify ownership of the product
$check_sql = "SELECT * FROM products WHERE product_id = $product_id AND owner_id = '$no_matric'";
$result = $conn->query($check_sql);

if ($result->num_rows > 0) {
    // Delete the product
    $delete_sql = "DELETE FROM products WHERE product_id = $product_id";
    if ($conn->query($delete_sql)) {
        echo "Product deleted successfully!";
    } else {
        echo "Error deleting product: " . $conn->error;
    }
} else {
    echo "You do not have permission to delete this product.";
}

$conn->close();
header("Location: my_product.php");
exit;
?>
