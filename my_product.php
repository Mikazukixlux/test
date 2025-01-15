<?php
session_start();
include 'db.php';

// Check if user is logged in
if (!isset($_SESSION['user'])) {
    echo "You must be logged in first.";
    exit;
}

$user = $_SESSION['user'];
$no_matric = $user['no_matric'];

// Fetch products owned by the logged-in user
$sql = "SELECT * FROM products WHERE owner_id = '$no_matric'";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Products</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body{
;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: url('background.jpeg')no-repeat;
            background-size: cover;
            background-position: center;

        }

        h1 {
            text-align: center;
            color: #444;
            margin: 20px 0;
        }

        .container {
            width: 90%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background: #fff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        table th, table td {
            padding: 12px 15px;
            border: 1px solid #ddd;
            text-align: left;
        }

        table th {
            background-color: #007BFF;
            color: white;
        }

        table tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        table a {
            color: #007BFF;
            text-decoration: none;
            font-weight: bold;
        }

        table a:hover {
            text-decoration: underline;
        }

        .actions a {
            margin-right: 10px;
        }

        .no-products {
            text-align: center;
            font-size: 16px;
            margin: 20px 0;
        }

        .no-products a {
            color: #007BFF;
            font-weight: bold;
            text-decoration: none;
        }

        .no-products a:hover {
            text-decoration: underline;
        }

        .back-menu {
            display: block;
            width: 150px;
            text-align: center;
            margin: 20px auto;
            padding: 10px;
            background: #007BFF;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            transition: background 0.3s;
        }

        .back-menu:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>My Products</h1>

        <?php if ($result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Product ID</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($product = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $product['product_id'] ?></td>
                            <td><?= htmlspecialchars($product['name']) ?></td>
                            <td><?= htmlspecialchars($product['description']) ?></td>
                            <td>RM<?= number_format($product['price'], 2) ?></td>
                            <td><?= $product['stock_quantity'] ?></td>
                            <td class="actions">
                                <a href="edit_product.php?product_id=<?= $product['product_id'] ?>&field=name">Edit Name</a> |
                                <a href="edit_product.php?product_id=<?= $product['product_id'] ?>&field=description">Edit Description</a> |
                                <a href="edit_product.php?product_id=<?= $product['product_id'] ?>&field=price">Edit Price</a> |
                                <a href="edit_product.php?product_id=<?= $product['product_id'] ?>&field=stock_quantity">Edit Stock</a> |
                                <a href="delete_product.php?product_id=<?= $product['product_id'] ?>" onclick="return confirm('Are you sure you want to delete this product?');">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="no-products">You don't own any products yet. <a href="upload_product.php">Upload a new product</a></p>
        <?php endif; ?>

        <a href="menu.php" class="back-menu">Back to Menu</a>
    </div>
</body>
</html>
