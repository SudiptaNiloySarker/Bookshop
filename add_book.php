<?php
// Database connection
$servername = "localhost";
$username = "root"; // Replace with your DB username
$password = ""; // Replace with your DB password
$database = "library"; // Replace with your DB name
 
// Create a connection
$conn = new mysqli($servername, $username, $password, $database);
 
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
 
// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $bookName = htmlspecialchars($_POST['book-name']);
    $authorName = htmlspecialchars($_POST['author-name']);
    $price = (float)$_POST['price'];
    $quantity = (int)$_POST['quantity'];
    $isbn = htmlspecialchars($_POST['isbn']);
 
    // Insert the book details into the database
    $sql = "INSERT INTO books (book_name, author_name, price, quantity, isbn)
            VALUES ('$bookName', '$authorName', $price, $quantity, '$isbn')";
 
    if ($conn->query($sql) === TRUE) {
        echo "<h2 style='color: purple;position: absolute; top: 50%; left: 35%'>The book details have been successfully updated</h2>";
    } else {
        echo "<h3 style='color: red;'>Error: " . $sql . "<br>" . $conn->error . "</h3>";
    }
}
 
// Close the database connection
$conn->close();
?>