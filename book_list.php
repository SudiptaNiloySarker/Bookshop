<?php
$con = mysqli_connect("localhost", "root", "", "library");
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

$sql = "SELECT id, book_name FROM books";
$result = $con->query($sql); // Use $con instead of $conn

if ($result->num_rows > 0) {
    echo "<table>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr><td>" . $row["id"] . "</td><td>" . $row["book_name"] . "</td></tr>";
    }
    echo "</table>";
} else {
    echo "0 results";
}

$con->close();
?>
