<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Paths to the JSON files
    $tokensPath = 'token.json';
    $usedTokensPath = 'used_token.json';
 
    // Load available tokens
    $tokensData = json_decode(file_get_contents($tokensPath), true);
    $tokens = $tokensData['tokens'];
 
    // Load used tokens or initialize the used tokens structure
    $usedTokensData = file_exists($usedTokensPath) ? json_decode(file_get_contents($usedTokensPath), true) : ['used_tokens' => []];
    $usedTokens = $usedTokensData['used_tokens'];
 
    // Get the submitted token from the form
    $submittedToken = isset($_POST['token']) ? htmlspecialchars($_POST['token']) : '';
 
    // Process other form data
    $studentName = htmlspecialchars($_POST['student-name']);
    $studentId = htmlspecialchars($_POST['student-id']);
    $borrowDate = htmlspecialchars($_POST['borrow-date']);
    $bookTitle = htmlspecialchars($_POST['book-title']);
    $returnDate = htmlspecialchars($_POST['return-date']);
    $fees = htmlspecialchars($_POST['fees']);
    $email = htmlspecialchars($_POST['email']);
 
    // Validate student name
    if (!preg_match("/^[a-zA-Z\s]+$/", $studentName)) {
        echo "<h3 style='color: red;'>Invalid name. Only alphabets are allowed.</h3>";
        exit;
    }
 
    // Validate student ID
    if (!preg_match("/^\d{2}-\d{5}-\d{1}$/", $studentId)) {
        echo "<h3 style='color: red;'>Invalid ID. Format: XX-XXXXX-X.</h3>";
        exit;
    }
 
    // Validate email
    if (!preg_match("/^\d{2}-\d{5}-\d{1}@student\.aiub\.edu$/", $email)) {
        echo "<h3 style='color: red;'>Invalid email. Must be a valid AIUB student email.</h3>";
        exit;
    }
 
    // Validate borrow and return dates
    if ($borrowDate === $returnDate) {
        echo "<h1 style='color: red;position: absolute; top: 50%; left: 30%;'>The borrow date cannot be the same as the return date.</h1>";
        exit;
    }
 
    $borrowDateObj = new DateTime($borrowDate);
    $returnDateObj = new DateTime($returnDate);
    $interval = $borrowDateObj->diff($returnDateObj)->days;
 
    if ($interval > 10) {
        // Token required for borrow periods exceeding 10 days
        if (empty($submittedToken) || !in_array($submittedToken, $tokens)) {
            echo "<h1 style='color: green ; position: absolute; top: 50%; left: 30%;'>A token is required for borrow periods exceeding 10 days</h1>";
            exit;
        }
     
 
        // Move the submitted token to the used tokens list
        $usedTokens[] = $submittedToken;
 
        // Save the updated used tokens list back to used_token.json
        if (file_put_contents($usedTokensPath, json_encode(['used_tokens' => $usedTokens], JSON_PRETTY_PRINT)) === false) {
            echo "<h1 style='color: red; position: absolute; top: 50%; left: 35%;'>Unable to update the used tokens list.json.</h1>";
            exit;
        }
 
        // Remove the used token from available tokens
        $tokens = array_diff($tokens, [$submittedToken]);
        if (file_put_contents($tokensPath, json_encode(['tokens' => array_values($tokens)], JSON_PRETTY_PRINT)) === false) {
            echo "<h1 style='color: red; position: absolute; top: 50%; left: 35%;'>Failed to update token.json.</h1>";
            exit;
        }
    }
    if ($returnDateObj <= $borrowDateObj) {
        echo "<h2 style='color: green; position: absolute; top: 50%; left: 35%; '>The return date cannot be earlier than the borrow date.</h2>";
        exit;
    }
    else {
        // No token required for borrow periods of 10 days or less
        $submittedToken = ''; // Clear the token variable to avoid unnecessary checks
    }

    // Check if the book is already rented
    if (isset($_COOKIE["Book_Name"]) && $_COOKIE["Book_Name"] === $bookTitle) {
        echo "<h2 style='color: gray; position: absolute; top: 50%; left: 40%;'>This book is currently rented by  {$_COOKIE['Book_Student']}.</h2>";
        exit;
    }
 
    // Set cookies for the book and student
    setcookie("Book_Name", $bookTitle, time() + 60);
    setcookie("Book_Student", $studentName, time() + 60);
 
    // Display a success message
    echo "<h1 style='color: purple; font-style: italic; font-weight: bold; text-align: center; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);'>Form  Submission Completed!</h1>";
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="receipt-container">
        <div class="receipt-box">
            <h1>Receipt</h1>
            <p><strong>Student Name:</strong> <?php echo $studentName; ?></p>
            <p><strong>ID:          </strong> <?php echo $studentId; ?></p>
            <p><strong>Email:       </strong> <?php echo $email; ?></p>
            <p><strong>Book Titlee: </strong> <?php echo $bookTitle; ?></p>
            <p><strong>Borrow Date: </strong> <?php echo $borrowDate; ?></p>
            <p><strong>Return Date: </strong> <?php echo $returnDate; ?></p>
            <p><strong>Fees:        </strong> <?php echo $fees; ?> Taka</p>
        </div>
    </div>
</body>
</html>
