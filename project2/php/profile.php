<?php
// Start the session with proper configuration
ini_set('session.gc_maxlifetime', 3600);
session_set_cookie_params([
    'lifetime' => 3600,
    'path' => '/',
    'domain' => '', // Set your domain
    'secure' => isset($_SERVER['HTTPS']),
    'httponly' => true,
]);
session_start();

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.html");
    exit;
}

// Define the path to the users CSV file
$usersFile = '../data/users.csv';

// Check if the users CSV file exists
if (!file_exists($usersFile)) {
    die("Users file not found.");
}

// Read the CSV file into an array
$usersData = array_map('str_getcsv', file($usersFile));

$userRole = null;
$userFound = false;

// Search for the logged-in user in the CSV data
foreach ($usersData as $user) {
    if ($user[0] == $_SESSION['user_id']) { // Adjust index based on CSV structure
        $userFound = true;
        $userRole = $user[3]; // Role ID is assumed to be at index 3
        break;
    }
}

// If user not found in CSV, destroy the session
if (!$userFound) {
    session_unset();
    session_destroy();
    header("Location: ../login.html");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
</head>
<body>
    <header>
        <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
    </header>
    <main>
        <p>This is your profile page.</p>
        
        <!-- Show add product link if user has a role_id of '3' -->
        <?php if ($userRole === '3'): ?>
            <a href="../add_product.php">Add Product</a>
        <?php endif; ?>
        
        <!-- Links to logout and go to the homepage -->
        <a href="logout.php">Logout</a>
        <a href="../index.php">Homepage</a>
    </main>
</body>
</html>
