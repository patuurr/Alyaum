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

// Get the cart item data from the request
$data = $_POST;
$userId = $_SESSION['user_id'];
$csvFilePath = '../data/cart.csv';

// Validate the cart item data
if (!isset($_SESSION['user_id']) || !isset($data['product_name']) || !isset($data['product_price']) || !isset($data['add_quantity'])) {
    echo "invalid request";
    exit;
}

// Generate a unique temporary file name
$tempFilePath = '../data/temp_cart_' . uniqid() . '.csv'; // Unique file name using uniqid()

// Open the cart file for reading
$file = fopen($csvFilePath, 'r');
if ($file === false) {
    die("Failed to open the cart file.");
}

// Open the temporary file for writing
$tempFile = fopen($tempFilePath, 'w');
if ($tempFile === false) {
    die("Failed to create a temporary file.");
}

$itemFound = false;

// Read the cart file line by line
while (($dataCart = fgetcsv($file)) !== false) {
    // Check if the current line matches the user and product
    if ($dataCart[0] == $userId && $dataCart[1] == $data['product_name']) {
        // Update the quantity if the item exists
        $dataCart[3] += $data['add_quantity'];
        $itemFound = true;
    }
    // Write the line to the temporary file
    fputcsv($tempFile, $dataCart);
}

// If the item was not found, add it to the cart
if (!$itemFound) {
    $cartItem = [$userId, $data['product_name'], $data['product_price'], $data['add_quantity']];
    fputcsv($tempFile, $cartItem);
}

// Close both files
fclose($file);
fclose($tempFile);

// Replace the original cart file with the updated temporary file
if (!rename($tempFilePath, $csvFilePath)) {
    die("Failed to replace the cart file.");
}

// Return a success response
echo '<script>alert("cart added successfully")</script><a href="../index.php#products" style="background-color: #344e41;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    font-size: 16px;
    cursor: pointer;
    position: fixed;">home</a>';
exit;
?>