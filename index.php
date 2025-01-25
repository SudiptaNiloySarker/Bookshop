<?php
// Database connection configuration
$dbHost = 'localhost';
$dbUser = 'root';
$dbPass = '';
$dbName = 'library';

function connectDatabase($host, $user, $pass, $dbName) {
    $con = mysqli_connect($host, $user, $pass, $dbName);
    if (!$con) {
        die("Connection failed: " . mysqli_connect_error());
    }
    return $con;
}

// Load tokens from JSON file
function loadTokens($filePath, $key = 'tokens') {
    if (file_exists($filePath)) {
        $jsonContent = file_get_contents($filePath);
        $data = json_decode($jsonContent, true);
        return $data[$key] ?? [];
    }
    return [];
}

// Add a token to used tokens file
function addUsedToken($filePath, $token) {
    $usedTokens = file_exists($filePath) ? json_decode(file_get_contents($filePath), true) : ['used_tokens' => []];
    if (!in_array($token, $usedTokens['used_tokens'])) {
        $usedTokens['used_tokens'][] = $token;
        file_put_contents($filePath, json_encode($usedTokens, JSON_PRETTY_PRINT));
    }
}

$tokensPath = 'token.json';
$usedTokensPath = 'used_token.json';
$tokens = loadTokens($tokensPath);
$usedTokens = loadTokens($usedTokensPath, 'used_tokens');

// Filter out used tokens from available tokens
$availableTokens = array_diff($tokens, $usedTokens);

$con = connectDatabase($dbHost, $dbUser, $dbPass, $dbName);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Borrowing Form</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <div class="box header">
        <h1>Book Borrowing Platform</h1>
        <img src="my image.png" alt="Student ID" class="top-right-image">
    </div>
    <div class="main-content-area">
        <div class="box left-sidebar">
            <h1>Already Used Tokens</h1>
            <ul>
                <?php foreach ($usedTokens as $usedToken): ?>
                    <h2><?= htmlspecialchars($usedToken) ?></h2>
                <?php endforeach; ?>
            </ul>
        </div>
        <div class="content-wrapper">
            <div class="box content1">WELCOME</div>

            <div class="box content3">
                <h2>Search For Books</h2>
                <form action="" method="get">
                    <input type="text" name="search" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" placeholder="Search by Title or Author" required>
                    <button type="submit">Search Books</button>
                </form>
                <table style="border: 1px solid black; border-collapse: collapse; width: 100%;">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Book Name</th>
                        <th>Author Name</th>
                        <th>Price</th>
                        <th>Quantity</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    if (!empty($_GET['search'])) {
                        $searchValue = mysqli_real_escape_string($con, $_GET['search']);
                        $query = "SELECT * FROM books WHERE CONCAT(book_name, author_name, price, quantity) LIKE '%$searchValue%'";
                        $result = mysqli_query($con, $query);

                        if ($result && mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<tr>
                                    <td>{$row['ID']}</td>
                                    <td>{$row['book_name']}</td>
                                    <td>{$row['author_name']}</td>
                                    <td>{$row['price']}</td>
                                    <td>{$row['quantity']}</td>
                                </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5'>No records found</td></tr>";
                        }
                    }
                    ?>
                    </tbody>
                </table>
            </div>

            <div class="box content2">
                <h2>Please Provide Book Details</h2>
                <form action="add_book.php" method="post">
                    <input type="text" name="book-name" placeholder="Enter book name" required>
                    <input type="text" name="author-name" placeholder="Enter author name" required>
                    <input type="number" name="price" placeholder="Enter price" required>
                    <input type="number" name="quantity" placeholder="Enter available quantity" required>
                    <input type="text" name="isbn" placeholder="Enter ISBN number" required>
                    <button type="submit">Add Book</button>
                </form>
            </div>

            <div class="small-content">
                <div class="box">
                    <img src="image1.png" alt="Image for Free Box 1" style="width: 100%; height: auto;">
                </div>
                <div class="box">
                    <img src="image2.png" alt="Image for Free Box 2" style="width: 100%; height: auto;">
                </div>
                <div class="box">
                    <img src="image3.png" alt="Image for Free Box 3" style="width: 100%; height: auto;">
                </div>
            </div>
        </div>
        <div class="box right-sidebar">
            <h1>Available Books</h1>
            <table style="border: 1px solid black; border-collapse: collapse; width: 100%;">
                <thead>
                <tr>
                    <th>Name</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $query = "SELECT book_name FROM books";
                $result = mysqli_query($con, $query);

                if ($result && mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr><td>{$row['book_name']}</td></tr>";
                    }
                } else {
                    echo "<tr><td>There were no books found</td></tr>";
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="footer-area">
        <div class="box footer">
            <h2>Form</h2>
            <form action="process.php" method="post">
                <input type="text" name="student-name" placeholder="Full Name" required>
                <input type="text" name="student-id" placeholder="Student ID" required>
                <input type="email" name="email" placeholder="Email" required>
                <select name="book-title" required>
                    <?php
                    $query = "SELECT book_name FROM books";
                    $result = mysqli_query($con, $query);

                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<option value='{$row['book_name']}'>{$row['book_name']}</option>";
                    }
                    ?>
                </select>
                <input type="date" name="borrow-date" required>
                <input type="date" name="return-date" required>
                <input type="text" name="token" placeholder="Token" >
                <input type="number" name="fees" placeholder="Fees in TK" required>
                <button type="submit">Submit</button>
            </form>
        </div>
        <div class="box footer2">
            <h1>Available Tokens</h1>
            <ul>
                <?php foreach ($availableTokens as $token): ?>
                    <li><?= htmlspecialchars($token) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>
</body>
</html>
<?php mysqli_close($con); ?>
