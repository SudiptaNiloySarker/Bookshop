<?php
$servername = "localhost";  // or your server address
$username   = "root";       // DB username
$password   = "";           // DB password
$dbname     = "library";    // Desired DB name
 
// 1) Connect to MySQL server (without specifying DB yet)
$conn = new mysqli($servername, $username, $password);
 
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
 
// 2) Create database if not exists
$createDBSql = "CREATE DATABASE IF NOT EXISTS $dbname";
if (!$conn->query($createDBSql)) {
    die("Error creating database: " . $conn->error);
}
 
// 3) Select the database
$conn->select_db($dbname);
 
// 4) Create the books table if it does not exist

$conn->query($alterTableSql);
 
 
if (!$conn->query($createTableSql)) {
    die("Error creating table: " . $conn->error);
}
 
// Now $conn is ready for any other queries
?>