<?php
// Include the database connection
include 'db.php';

// Fetch products
$sql = "SELECT name, description, price, stock_quantity, image_url FROM products";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<div class='product-menu'>";
    while ($row = $result->fetch_assoc()) {
        $image_url = $row['image_url'];

        // Check if the URL is a Google Drive link and convert it to a direct link
        if (strpos($image_url, 'drive.google.com') !== false) {
            preg_match('/\/d\/(.*?)\//', $image_url, $matches);
            if (!empty($matches[1])) {
                $file_id = $matches[1];
                $image_url = "https://drive.google.com/uc?id=$file_id";
            }
        }

        echo "<div class='product-item'>";
        echo "<img src='" . htmlspecialchars($image_url) . "' alt='" . htmlspecialchars($row['name']) . "' />";
        echo "<h3>" . htmlspecialchars($row['name']) . "</h3>";
        echo "<p>" . htmlspecialchars($row['description']) . "</p>";
        echo "<p>Price: $" . number_format($row['price'], 2) . "</p>";
        echo "<p>Stock: " . (int)$row['stock_quantity'] . "</p>";
        echo "</div>";
    }
    echo "</div>";
} else {
    echo "No products available.";
}

// Close connection
$conn->close();
?>
