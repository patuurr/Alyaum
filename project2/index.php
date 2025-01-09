<?php
ini_set('session.gc_maxlifetime', 3600); // Set session lifetime to 1 hour
session_set_cookie_params(3600); // Set cookie lifetime to 1 hour
session_start();


// CSV file paths
$csvFile = "data/products.csv";
$usersFile = 'data/users.csv';

// Check if the users file exists
if (!file_exists($usersFile)) {
    die("Users file does not exist.");
}

$usersData = array_map('str_getcsv', file($usersFile));

// Check if the user is logged in by matching the user ID with the session data
$loggedIn = false;
$userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

// Check if the user exists in the CSV file
foreach ($usersData as $user) {
    // Assuming the first column is user_id (modify if needed)
    if ($user[0] == $userId) {
        $loggedIn = true;
        break;
    }
}

// Check if the products file exists
if (!file_exists($csvFile)) {
    die("Product file does not exist.");
}

// Fetch products from CSV
$products = [];
if (($file = fopen($csvFile, "r")) !== false) {
    // Skip the header row
    $headers = fgetcsv($file);

    // Read the product data
    while (($data = fgetcsv($file)) !== false) {
        // Assuming CSV columns: name, price, quantity, image
        $products[] = [
            'product_name' => $data[1],
            'product_price' => $data[2],
            'product_quantity' => $data[3],
            'product_image' => $data[4]
        ];
    }

    fclose($file);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AlYaum Home</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="icon" type="image/png" href="img/product1.png">
</head>
<body>
    <header class="header">
        <div class="container">
            <a href="index.php">
            <div class="logo">
                <h1 class="logo" id="yellow">Al</h1>
                <h1 class="logo">Yaum</h1>
            </div>
            </a>
        </div>
        <!-- Dynamic Button: Login or Profile -->
        <div class="container-button-login">
        <?php if (!$loggedIn): ?>
            <a href="login.html"><button>Login</button></a>
        <?php else: ?>
            <a href="php/profile.php"><button>Profile</button></a>
        <?php endif; ?>
        <button class="nav-button" id="menuButton" onclick="toggleMenu()">
            <svg height="25px" style="enable-background:new 0 0 32 32;" version="1.1" viewBox="0 0 32 32" width="25px" xmlns="http://www.w3.org/2000/svg">
                <path d="M4,10h24c1.104,0,2-0.896,2-2s-0.896-2-2-2H4C2.896,6,2,6.896,2,8S2.896,10,4,10z M28,14H4c-1.104,0-2,0.896-2,2  s0.896,2,2,2h24c1.104,0,2-0.896,2-2S29.104,14,28,14z M28,22H4c-1.104,0-2,0.896-2,2s0.896,2,2,2h24c1.104,0,2-0.896,2-2  S29.104,22,28,22z" style="fill:#fff;"/>
            </svg>
            </button>
        </div>

        <!-- Slide-in Menu -->
        <div class="overlay" id="overlay" onclick="toggleMenu()"></div>
        <div class="slide-menu" id="slideMenu">
            <a href="index.php">Home</a>
            <a href="index.php#products">Services</a>
            <a href="cart.html">Cart</a>
            <a href="contact.html">Contact</a>
            <a href="test.html">Test</a>
            <a href="test2.html">Test2</a>
            <a href="test3.html">Test3</a>
        </div>

        <!-- Overlay -->
        
    </header>

    <div class="slider-container">
        <div class="slider" id="slider">
            <img src="img/Madeena_masjid_nabawi.jpg" alt="Slide 1">
            <img src="img/product2.png" alt="Slide 2">
            <img src="img/Madeena_masjid_nabawi.jpg" alt="Slide 3">
            <img src="img/product1.png" alt="Slide 4">
        </div>
    </div>

    <section id="hero" class="hero">
        <div class="container">
            <h2>Welcome to </h2>
            <h2 id="yellow">Al</h2>
            <h2>Yaum</h2>
            <p>Tempat penyaluran sedekah dan waqaf pilihanmu</p>
            <a href="#products" class="btn">Waqaf Sekarang</a>
        </div>
    </section>

    <section id="products" class="products">
        <div class="container">
            <h2>Waqaf Products</h2>
            <div class="product-grid">
                <?php foreach ($products as $product): ?>
                    <div class="product-card">
                        <img src="<?php echo htmlspecialchars($product['product_image']); ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>">
                        <h3><?php echo htmlspecialchars($product['product_name']); ?></h3>
                        <p>Rp. <?php echo number_format($product['product_price'], 2); ?></p>
                    
                        <!-- Add to Cart -->
                        <form action="php/add_to_cart.php" method="POST">
                            <input type="hidden" name="product_name" value="<?php echo htmlspecialchars($product['product_name']); ?>">
                            <input type="hidden" name="product_price" value="<?php echo htmlspecialchars($product['product_price']); ?>">
                            <input type="number" name="add_quantity" min="1" value="1" required> 
                            <input type="hidden" name="action" value="add">
                            <button type="submit" class="add-to-cart">Add to Cart</button>
                        </form>
                    
                        <!-- Remove from Cart -->
                        <form action="php/remove_from_cart.php" method="POST" style="margin-top: 10px;">
                            <input type="hidden" name="product_name" value="<?php echo htmlspecialchars($product['product_name']); ?>">
                            <input type="hidden" name="product_price" value="<?php echo htmlspecialchars($product['product_price']); ?>">
                            <input type="number" name="remove_quantity" min="1" value="1" required> 
                            <input type="hidden" name="action" value="remove">
                            <button type="submit" style="background-color: red; add-to-cart">Remove from Cart</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section id="cart" class="cart">
        <div class="container">
            <h2>Your Cart</h2>
            <div class="cart-items">
                <?php include 'php/cart.php'; ?>
            </div>
            <div class="checkout">
                <button id="checkout-button">Checkout</button>
            </div>
        </div>
    </section>

    <footer class="footer">
        <div class="container">
            <p>&copy; 2024 Toko Buya. All rights reserved.</p>
        </div>
    </footer>

    <script src="javascript/script.js"></script>
</body>
</html>