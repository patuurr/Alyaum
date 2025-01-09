<?php
session_start();

$allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
$products = [];
$usersFile = 'data/users.csv';

// Read the CSV file into an array
$usersData = array_map('str_getcsv', file($usersFile));

// Flag to find the user role
$userRole = null;

// Loop through users and find the current user's role
foreach ($usersData as $user) {
    // Assuming the CSV structure: user_id, username, password, role_id
    if ($user[0] == $_SESSION['username']) {
        $userRole = $user[2]; // The role_id is at index 3
        break; // Exit loop once user is found
    }
}

// Compare the session's role with the user's role
if ($userRole == 1) {
    header("Location: ../index.php");
}

// Path to the CSV file
$csvFile = "data/products.csv";

// Function to refresh the products array
function refreshProducts(&$products, $csvFile) {
    $products = file_exists($csvFile) ? array_map('str_getcsv', file($csvFile)) : [];
}

// Refresh products array initially
refreshProducts($products, $csvFile);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $productName = $_POST['product_name'];
    $productPrice = $_POST['product_price'];
    $productQuantity = $_POST['product_stock'];
    $targetDir = "uploads/";
    $targetFile = $targetDir . basename($_FILES["product_image"]["name"]);
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    // Validate input
    if (!empty($productName) && !empty($productPrice) && !empty($productQuantity) && !empty($_FILES["product_image"]["name"])) {
        // Upload image
        if (!in_array($imageFileType, $allowedTypes)) {
            echo "Error: Only JPG, JPEG, PNG, and GIF files are allowed.";
            exit;
        }

        // Move the uploaded image to the target directory
        if (move_uploaded_file($_FILES["product_image"]["tmp_name"], $targetFile)) {
            // Check if the CSV file exists, if not create it with headers
            if (!file_exists($csvFile)) {
                $header = ['product_id', 'product_name', 'product_price', 'product_stock', 'product_image'];
                $file = fopen($csvFile, 'w');
                fputcsv($file, $header);
                fclose($file);
            }

            // Generate a unique product ID
            $productId = uniqid();

            // Prepare product data to write into the CSV file
            $productData = [$productId, $productName, $productPrice, $productQuantity, $targetFile];

            // Open the CSV file for appending new product data
            $file = fopen($csvFile, 'a');
            if (fputcsv($file, $productData)) {
                echo "Product added successfully!";
                refreshProducts($products, $csvFile); // Refresh products after adding
            } else {
                echo "Error writing to CSV file.";
            }
            fclose($file);
        } else {
            echo "Error uploading image.";
        }
    } else {
        echo "All fields are required.";
    }
}

// Handle product removal
if (isset($_GET['remove']) && isset($_GET['product_id'])) {
    $productIdToRemove = $_GET['product_id'];

    if (file_exists($csvFile)) {
        $products = array_map('str_getcsv', file($csvFile));

        // Open the file for writing
        $file = fopen($csvFile, 'w');

        foreach ($products as $product) {
            // Skip the header or the product to be removed
            if ($product[0] !== $productIdToRemove || $product === $products[0]) {
                fputcsv($file, $product);
            } else {
                // Delete the associated image file
                if (file_exists($product[4])) {
                    unlink($product[4]);
                }
            }
        }

        fclose($file);
        echo "Product removed successfully!";
        refreshProducts($products, $csvFile); // Refresh products after removal
    } else {
        echo "Error: Products file not found.";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product</title>
</head>
<body>
    <h1>Add a New Product</h1>
    <form action="add_product.php" method="POST" enctype="multipart/form-data">
        <label for="product_name">Product Name:</label>
        <input type="text" id="product_name" name="product_name" required><br><br>

        <label for="product_price">Product Price:</label>
        <input type="number" id="product_price" name="product_price" step="0.01" required><br><br>

        <label for="product_quantity">Quantity:</label>
        <input type="number" id="product_quantity" name="product_quantity" required><br><br>

        <label for="product_image">Product Image:</label>
        <input type="file" id="product_image" name="product_image" accept="image/*" required><br><br>

        <button type="submit">Add Product</button>
        <br><br>
        <a href="index.php">Homepage</a>
    </form>

    <h2>Existing Products</h2>
    <table border="1">
        <tr>
            <th>Product ID</th>
            <th>Product Name</th>
            <th>Price</th>
            <th>Quantity</th>
            <th>Image</th>
            <th>Action</th>
        </tr>
        <?php if (!empty($products)) : ?>
            <?php foreach ($products as $index => $product) : ?>
                <?php if ($index === 0) continue; // Skip header ?>
                <tr>
                    <td><?= htmlspecialchars($product[0]) ?></td>
                    <td><?= htmlspecialchars($product[1]) ?></td>
                    <td><?= htmlspecialchars($product[2]) ?></td>
                    <td><?= htmlspecialchars($product[3]) ?></td>
                    <td>
                        <img src="<?= htmlspecialchars($product[4]) ?>" alt="<?= htmlspecialchars($product[1]) ?>" width="100">
                    </td>
                    <td>
                        <a href="?remove=true&product_id=<?= urlencode($product[0]) ?>" onclick="return confirm('Are you sure you want to remove this product?');">Remove</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else : ?>
            <tr>
                <td colspan="6">No products available.</td>
            </tr>
        <?php endif; ?>
    </table>

    <br>
    <a href="index.php">Homepage</a>

</body>
</html>
