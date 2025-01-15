<?php
session_start();
include 'db.php';

// Check if user is logged in
if (!isset($_SESSION['user'])) {
    echo "You must be logged in to upload product.";
    exit;
}

$user = $_SESSION['user'];
$no_matric = $user['no_matric'];
$product_id = isset($_GET['product_id']) ? (int)$_GET['product_id'] : 0;
$field = isset($_GET['field']) ? $_GET['field'] : '';
$new_value = isset($_POST['new_value']) ? $_POST['new_value'] : null;

// Validate field and ensure the user owns the product
$valid_fields = ['name', 'description', 'price', 'stock_quantity'];
if (!in_array($field, $valid_fields)) {
    echo "Invalid field.";
    exit;
}

$check_sql = "SELECT * FROM products WHERE product_id = $product_id AND owner_id = '$no_matric'";
$result = $conn->query($check_sql);

if ($result->num_rows === 0) {
    echo "You do not have permission to edit this product.";
    exit;
}

// Handle update when form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and format value based on field type
    if ($field === 'price' || $field === 'stock_quantity') {
        $new_value = (float)$new_value;
    } else {
        $new_value = htmlspecialchars($new_value);
    }

    
    $update_sql = "UPDATE products SET $field = '$new_value' WHERE product_id = $product_id";

    if ($conn->query($update_sql)) {
        echo "Product $field updated successfully!";
        echo "<a href='my_product.php'>Go back to My Products</a>";
    } else {
        echo "Error updating product: " . $conn->error;
    }
    $conn->close();
    exit;
}

$product = $result->fetch_assoc();
$current_value = $product[$field];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product <?= ucfirst($field) ?></title>
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

        .container {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background: #fff;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        h1 {
            text-align: center;
            color: #444;
            margin-bottom: 20px;
        }

        p {
            font-size: 16px;
            margin-bottom: 20px;
            text-align: center;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        label {
            font-size: 14px;
            font-weight: bold;
        }

        input[type="text"],
        input[type="number"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }

        button {
            background-color: #007BFF;
            color: #fff;
            padding: 10px;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #0056b3;
        }

        a {
            display: block;
            margin-top: 10px;
            text-align: center;
            color: #007BFF;
            text-decoration: none;
            font-weight: bold;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Edit Product <?= ucfirst($field) ?></h1>
        <p>Current <?= ucfirst($field) ?>: <strong><?= htmlspecialchars($current_value) ?></strong></p>
        <form action="" method="POST">
            <label for="new_value">New <?= ucfirst($field) ?>:</label>
            <input type="<?= ($field === 'price' || $field === 'stock_quantity') ? 'number' : 'text' ?>" 
                   name="new_value" 
                   id="new_value" 
                   value="<?= htmlspecialchars($current_value) ?>" 
                   required>
            <button type="submit">Update</button>
        </form>
        <a href="my_products.php">Cancel</a>
    </div>
</body>
</html>
