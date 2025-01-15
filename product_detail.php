<?php
include 'db.php'; // Include database connection

$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch product details, including the owner_id
$sql = "SELECT * FROM products WHERE product_id = $product_id";
$result = $conn->query($sql);
$product = $result->fetch_assoc();

if (!$product) {
    echo "Product not found.";
    exit;
}

// Fetch owner's details using the owner_id from the product table
$owner_id = $product['owner_id']; // Owner's no_matric
$owner_sql = "SELECT username, qr FROM users WHERE no_matric = '$owner_id'";
$owner_result = $conn->query($owner_sql);
$owner = $owner_result->fetch_assoc();
$owner_name = $owner['username'] ?? 'Unknown';
$owner_qr_code = $owner['qr'] ?? null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Details</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: url('background.jpeg') no-repeat;
            background-size: cover;
            background-position: center;
        }
        .container {
            max-width: 900px;
            margin: 40px auto;
            padding: 20px;
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        h1 {
            font-size: 2em;
            color: #333;
            margin-bottom: 20px;
        }
        img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
        }
        .product-details {
            text-align: left;
            margin-top: 20px;
        }
        .product-details p {
            font-size: 1.2em;
            margin: 8px 0;
        }
        form {
            margin-top: 20px;
        }
        label {
            font-size: 1.1em;
        }
        input[type="number"], button {
            padding: 10px;
            font-size: 1em;
            margin-top: 10px;
        }
        button {
            background-color: #28a745;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #218838;
        }
        .qr-container {
            display: none;
            margin-top: 30px;
            padding: 20px;
            background: #f9f9f9;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .qr-container h3 {
            font-size: 1.5em;
            margin-bottom: 15px;
        }
        .qr-container img {
            max-width: 350px; /* Larger QR Code */
            margin: 0 auto;
            display: block;
        }
    </style>
    <script>
        function toggleQR(show) {
            const qrContainer = document.getElementById('qr-container');
            qrContainer.style.display = show ? 'block' : 'none';
        }
    </script>
</head>
<body>
    <div class="container">
        <h1><?= htmlspecialchars($product['name']) ?></h1>
        <img src="<?= htmlspecialchars($product['image_url']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" />
        <div class="product-details">
            <p><?= htmlspecialchars($product['description']) ?></p>
            <p><strong>Price:</strong> RM<?= number_format($product['price'], 2) ?></p>
            <p><strong>Stock:</strong> <?= (int)$product['stock_quantity'] ?></p>
            <p><strong>Owner Name:</strong> <?= htmlspecialchars($owner_name) ?></p> <!-- Display owner name -->
        </div>

        <form action="placeorder.php" method="POST">
            <input type="hidden" name="product_id" value="<?= $product_id ?>">
            
            <label for="quantity">Quantity:</label><br>
            <input type="number" name="quantity" id="quantity" required><br><br>

            <label for="payment_method">Payment Method:</label><br>
            <input type="radio" name="payment_method" id="qr" value="qr" required onclick="toggleQR(true)">
            <label for="qr">QR Duit Now</label><br>
            <input type="radio" name="payment_method" id="cod" value="cod" required onclick="toggleQR(false)">
            <label for="cod">Cash on Delivery</label><br><br>

            <div id="qr-container" class="qr-container">
                <h3>Scan this QR Code to Pay:</h3>
                <?php if ($owner_qr_code): ?>
                    <img src="<?= htmlspecialchars($owner_qr_code) ?>" alt="QR Code">
                <?php else: ?>
                    <p>No QR code available for this owner.</p>
                <?php endif; ?>
            </div>

            <button type="submit">Buy Now</button>
        </form>
    </div>
</body>
</html>
