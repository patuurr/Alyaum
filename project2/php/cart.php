<?php

// CSV file path for cart
$csvFile = "../data/cart.csv";

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "You need to log in first.";
    exit;
}
if (!isset($_SESSION['user_id'])) {
    die("Session not set. Please log in.");
}


// Get user ID from session
$user_id = $_SESSION['user_id'];

// Check if the cart file exists
if (!file_exists($csvFile)) {
    die("Cart file does not exist.");
}

// Retrieve cart items for the user
$cartItems = [];
if (($file = fopen($csvFile, "r")) !== false) {
    // Skip the header row
    $headers = fgetcsv($file);

    // Read the cart data
    while (($data = fgetcsv($file)) !== false) {
        // Check if the current row matches the logged-in user
        if ($data[0] == $user_id) {
            print_r($data); 
            $cartItems[] = [
                'product_id'=> $data[1],
                'product_name' => $data[2],
                'product_price' => $data[3],
                'product_quantity' => $data[4],
            ];
        }
    }

    fclose($file);
}

// Display cart items
if (count($cartItems) > 0) {
    echo "<table>";
    echo "<tr><th>Product</th><th>Price</th><th>Quantity</th></tr>";

    foreach ($cartItems as $item) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($item['product_name']) . "</td>";
        echo "<td>Rp. " . number_format($item['product_price'], 2) . "</td>";
        echo "<td>" . $item['product_quantity'] . "</td>";
        echo "</tr>";
    }

    echo "</table>";
    echo "<a href='checkout.php'>Proceed to Checkout</a>";
} else {
    echo "Your cart is empty.";
}
?>
