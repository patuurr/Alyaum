<?php
/*session_start();
// Database connection details
$host = "localhost";
$dbname = "testdb";
$username = "root";
$password = "";
$home = "index.php";

// Connect to the database
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get form data
$user = $_POST['username'];
$pass = $_POST['password'];


// Query to check user
$sql = "SELECT * FROM users WHERE username = '$user'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    if (password_verify($pass, $row['password'])) {
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['username'] = $row['username'];
        $_SESSION['role_id'] = $row['role_id'];
        echo "Login successful! Welcome, $user.";
        header("Location: $home");
    } else {
        echo "Invalid password.";
    }
} else {
    echo "Invalid username.";
}

$conn->close();*/

session_start();

// CSV file path
$csvFile = "../data/users.csv";
$home = "../index.php";

// Get form data
$user = $_POST['username'];
$pass = $_POST['password'];

// Open the CSV file for reading
$file = fopen($csvFile, "r");
if ($file === false) {
    die("Failed to open the user data file.");
}

$isAuthenticated = false;



// Iterate through the CSV file to find the user
while (($data = fgetcsv($file)) !== false) {
    if ($data[1] === $user && password_verify($pass, $data[2])) {
        $_SESSION['username'] = $user;
        $_SESSION['user_id'] = $data[0];
        echo "Login successful! Welcome, $user.";
        fclose($file);
        header("Location: $home");
        exit();
    }
}

// Close the file after reading
fclose($file);

// If no match was found
echo "Invalid username or password.";
?>

