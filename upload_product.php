<?php
session_start();
include 'db.php'; // Include database connection
if (!isset($_SESSION['user'])) {
    echo "You must be logged in to upload a product.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = htmlspecialchars($_POST['name']);
    $description = htmlspecialchars($_POST['description']);
    $price = (float)$_POST['price'];
    $stock_quantity = (int)$_POST['stock_quantity'];
    $user = $_SESSION['user'];
    $no_matric = $user['no_matric'];

    // Handling the image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/'; // Folder to store uploaded images
        $imageName = basename($_FILES['image']['name']);
        $imagePath = $uploadDir . uniqid() . '_' . $imageName;

        // Create upload directory if it doesn't exist
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Move the uploaded file to the server
        if (move_uploaded_file($_FILES['image']['tmp_name'], $imagePath)) {
            // Insert product details into the database
            $sql = "INSERT INTO products (name, description, price, stock_quantity, image_url, owner_id) 
                    VALUES ('$name', '$description', $price, $stock_quantity, '$imagePath', '$no_matric')";

            if ($conn->query($sql) === TRUE) {
                echo "<div class='success-message'>Product uploaded successfully!</div>";
                echo "<a href='menu.php' class='btn'>Go to Product Menu</a>";
            } else {
                echo "<div class='error-message'>Error: " . $conn->error . "</div>";
            }
        } else {
            echo "<div class='error-message'>Failed to upload image.</div>";
        }
    } else {
        echo "<div class='error-message'>Image upload failed. Please try again.</div>";
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Sell a Product</title>
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
        body.upproduct {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            color: #333;
            margin: 0;
            padding: 0;
        }

        h1 {
            text-align: center;
            margin: 20px 0;
            color: #444;
        }

        form {
            width: 60%;
            margin: 20px auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        form label {
            font-size: 16px;
            display: block;
            margin-bottom: 6px;
        }

        form input, form textarea, form button {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
        }

        form textarea {
            resize: vertical;
        }

        form button {
            background-color: #007BFF;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }

        form button:hover {
            background-color: #0056b3;
        }

        .success-message, .error-message {
            width: 60%;
            margin: 20px auto;
            padding: 15px;
            border-radius: 4px;
            font-size: 14px;
            text-align: center;
        }

        .success-message {
            background-color: #d4edda;
            color: #155724;
        }

        .error-message {
            background-color: #f8d7da;
            color: #721c24;
        }

        .btn {
            display: block;
            width: 200px;
            margin: 20px auto;
            padding: 10px;
            text-align: center;
            background: #007BFF;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-size: 16px;
            transition: background-color 0.3s;
        }

        .btn:hover {
            background: #0056b3;
        }
    </style>
</head>
<body class="upproduct">
    <h1>Sell a Product</h1>
    <form action="" method="POST" enctype="multipart/form-data">
        <label for="name">Product Name:</label>
        <input type="text" name="name" id="name" required>

        <label for="description">Description:</label>
        <textarea name="description" id="description" rows="4"></textarea>

        <label for="price">Price:</label>
        <input type="number" name="price" id="price" step="0.01" required>

        <label for="stock_quantity">Stock Quantity:</label>
        <input type="number" name="stock_quantity" id="stock_quantity" required>

        <label for="image">Upload Image:</label>
        <input type="file" name="image" id="image" accept="image/*" required>

        <button type="submit">Submit</button>
    </form>
</body>
</html>
