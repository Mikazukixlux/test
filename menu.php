<?php
session_start(); // Start the session
require 'db.php'; // Include database connection


if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}

$user = $_SESSION['user'];
$no_matric = $user['no_matric'];

// Fetch orders
$query = "SELECT * FROM orders WHERE no_matric = '$no_matric'";
$orders = $conn->query($query);

// Search functionality
$searchQuery = isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '';
if ($searchQuery) {
    $sql = "SELECT product_id, name, description, price, stock_quantity, image_url FROM products 
            WHERE name LIKE ? OR description LIKE ?";
    $searchTerm = "%$searchQuery%";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $searchTerm, $searchTerm);
} else {
    $sql = "SELECT product_id, name, description, price, stock_quantity, image_url FROM products";
    $stmt = $conn->prepare($sql);
}
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    
    <title>Product Menu</title>
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
        header {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            padding: 20px 100px;
            background: black;
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 99;

        }

        .logo {
            height: 50px;
            display: flex;
            user-select: none;

        }

        .navigation a{
            position:  relative;
            font-size : 1.1em;
            color: azure;
            text-decoration: none;
            font-weight: 500;
            margin-left: 40px;
        }

        .navigation a::after{
            content: '';
            position: absolute;
            left: 0;
            bottom: -6px;
            width: 100%;
            height: 3px;
            background: #fff;
            border-radius: 5px;
            transform-origin: right;
            transform: scaleX(0);
            transition: transform .5s;
        }

        .navigation a:hover::after{
            transform-origin: left;
            transform: scaleX(1);
        }

         .welcome-message {
            text-align: center;
            margin: 20px 0;
        }
        .search-bar {
            text-align: center;
            margin: 20px 0;
        }
        .search-bar input {
            width: 50%;
            padding: 10px;
            margin-right: 10px;
        }
        .search-bar button {
            padding: 10px 20px;
        }
        .product-menu {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            margin-top: 20px;
        }
        .product-item {
            width: 200px;
            min-height: 300;
            margin-top: 10px;
            text-decoration: none;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            display: inline-block;
            background: transparent;
            border: 2px solid rgba(255, 255, 255, .5);
            border-radius: 20px;
            backdrop-filter: blur(20px);
            box-shadow: 0 0 30px rgba(0, 0, 0, .5);

        }
        .product-item img {
            max-width: 100%;
            height: 150px;
            object-fit: cover;
            border-radius: 10px;
            margin-bottom: 10px;
        }
        .product-item h3 {
            margin: 10px 0;
        }
        .product-item p {
            margin: 5px 0;
        }
        .product-item .price {
            font-size: 1.1em;
            font-weight: bold;
        }
        .product-item .buy-button {
            margin-top: 10px;
            text-decoration: none;
            color: white;
            background-color: #28a745;
            padding: 10px 20px;
            border-radius: 5px;
            display: inline-block;
        }
        .product-item .buy-button:hover {
            background-color: #218838;
        }
    </style>
</head>
<body class="menu">
    <header>
        <img src="logo.png" class="logo">
        <nav class="navigation">
            <a href="menu.php">Home</a>
            <a href="user_profile.php?id=no_matric">Profile</a>
            <a href="my_product.php">Product</a>
            <a href="check_order.php">Order</a>
            <a href="upload_product.php">Add a New Product</a>
            <a href="logout.php">Logout</a>
        </nav>
    </header>

    <!-- Welcome Message -->
    <div class="welcome-message">
        <br>
        <br>
        <br>
        <br>
        <h1>WELCOME TO UTHM SHOP</h1>
    </div>
    <!-- Search Bar -->
    <div class="search-bar">
        <form action="menu.php" method="GET">
            <input type="text" name="search" placeholder="Search for products..." value="<?= $searchQuery ?>">
            <button type="submit">Search</button>
        </form>
    </div>

    <!-- Product Menu -->
    <div class="product-menu">
    <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                if ((int)$row['stock_quantity'] > 0) { // Check if the product is in stock
                    echo "<div class='product-item'>";
                    echo "<img src='" . htmlspecialchars($row['image_url']) . "' alt='" . htmlspecialchars($row['name']) . "' />";
                    echo "<h3>" . htmlspecialchars($row['name']) . "</h3>";
                    echo "<p>" . htmlspecialchars($row['description']) . "</p>";
                    echo "<p class='price'>Price: RM" . number_format($row['price'], 2) . "</p>";
                    echo "<p class='stock'>Stock: " . (int)$row['stock_quantity'] . "</p>";
                    echo "<a href='product_detail.php?id=" . $row['product_id'] . "' class='buy-button'>Buy Now</a>";
                    echo "</div>";
                }
            }
        } else {
            echo "<p>No products found for '$searchQuery'</p>";
        }

        $conn->close();
        ?>
    </div>
</body>
</html>
