<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.html");
    exit;
}

// Check if the cart file exists
if (!file_exists('../data/cart.csv')) {
    // Create the cart file if it doesn't exist
    $file = fopen('../data/cart.csv', 'w');
    fclose($file);
}

// Get the product name and quantity to remove from the request
$productName = $_POST['product_name'];
$removeQuantity = $_POST['remove_quantity'];

// Validate the product name and quantity
if (!isset($productName) || empty($productName) || !isset($removeQuantity) || !is_numeric($removeQuantity)) {
    echo "invalid request";
    exit;
}

// Open the cart file for reading
$file = fopen('../data/cart.csv', 'r');
if ($file === false) {
    die("Failed to open the cart file.");
}

// Read the cart file line by line
$rows = [];
while (($data = fgetcsv($file)) !== false) {
    // Check if the current line matches the product name
    if ($data[1] === $productName) {
        // Update the quantity
        $data[3] -= $removeQuantity;
        if ($data[3] <= 0) {
            // Remove the item if the quantity is 0 or less
            continue;
        }
    }
    $rows[] = $data;
}

// Close the file
fclose($file);

// Open the cart file for writing
$file = fopen('../data/cart.csv', 'w');
if ($file === false) {
    die("Failed to open the cart file for writing.");
}

// Write the updated rows to the file
foreach ($rows as $row) {
    fputcsv($file, $row);
}

// Close the file
fclose($file);

// Check if the file was updated successfully
if (file_exists('../data/cart.csv')) {
    echo '<script>alert("item quantity removed from cart successfully"")</script><a href="../index.php#products" style="background-color: #344e41;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    font-size: 16px;
    cursor: pointer;
    position: fixed;">home</a>';
} else {
    echo '<script>alert("failed to update cart file")</script><a href="../index.php#products" style="background-color: #344e41;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    font-size: 16px;
    cursor: pointer;
    position: fixed;">home</a>';
}

// Debugging statements
/*echo "Product name: $productName\n";
echo "Remove quantity: $removeQuantity\n";
echo "Rows: ";
print_r($rows);*/
?>