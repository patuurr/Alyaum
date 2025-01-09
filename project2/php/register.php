<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = 1;

    // Open the file and read existing user data
    $file = fopen("../data/users.csv", "a+");

    // Read the existing data to determine the last user_id
    $usersData = array_map('str_getcsv', file("../data/users.csv"));
    
    // If the file is empty, set the first user_id to 1, else increment the last user_id
    $user_id = (!empty($usersData)) 
        ? max(array_map('intval', array_column($usersData, 0))) + 1 
        : 1;


    // Write the new user data to the CSV file
    fputcsv($file, [$user_id, $username, $password, $role]);

    // Close the file after writing
    fclose($file);

    echo "Registration successful! <a href='../login.html'>Login here</a>";
}


session_abort();    

?>
