<?php
session_start();

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401); // Unauthorized
    echo json_encode(['error' => 'User   not logged in']);
    exit;
}

// Define the path to the cart.csv file
$csvFilePath = __DIR__ . '/../data/cart.csv';

// Check if the cart file exists
if (!file_exists($csvFilePath)) {
    http_response_code(404); // Not found
    echo json_encode(['error' => 'Cart file not found']);
    exit;
}

// Read the cart data
$cartItems = [];
if (($file = fopen($csvFilePath, 'r')) !== false) {
    fgetcsv($file); // Skip the header row
    while (($data = fgetcsv($file)) !== false) {
        // Only include items for the logged-in user
        if ($data[0] == $_SESSION['user_id']) {
            $cartItems[] = [
                'name' => $data[1], // Product name
                'price' => $data[2], // Product price
                'quantity' => $data[3], // Product quantity
            ];
        }
    }
    fclose($file);
}

// Process the checkout (e.g., send an email, update database)
// For demonstration purposes, we'll just log the checkout
error_log('Checkout processed for user ' . $_SESSION['user_id']);

// Clear the cart
$file = fopen($csvFilePath, 'w');
fputcsv($file, ['user_id', 'product_name', 'product_price', 'product_quantity']);
fclose($file);

// Return a success response
echo json_encode(['success' => true]);
?>