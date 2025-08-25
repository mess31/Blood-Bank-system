<?php
session_start();

// Check if user is logged in, if not, redirect to login page
if (!isset($_SESSION['userID'])) {
    header("Location: login.php");
    exit();
}

$db_host = "localhost";
$db_user = "root";
$db_password = "";
$db_name = "bloodlink";

// Create Connection
$conn = mysqli_connect($db_host, $db_user, $db_password, $db_name);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$password = ""; // Initialize the variable
$errorMsg = ""; // Initialize an empty error message variable

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    // Something was posted
    $password = $_POST['password'];

    if (!empty($password)) {

        // Sanitize user input to prevent SQL Injection
        $password = mysqli_real_escape_string($conn, $password);

        // Get the user ID from session
        $userID = $_SESSION['userID'];

        // Get the user's password from the database
        $userPasswordQuery = "SELECT password FROM users WHERE id = $userID";
        $result = mysqli_query($conn, $userPasswordQuery);

        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $hashedPassword = $row['password'];

            // Verify the password
            if (password_verify($password, $hashedPassword)) {
                // Passwords match, proceed with account deletion

                // Delete related records from child tables
                $deleteDonorsQuery = "DELETE FROM donors WHERE user_id = $userID";
                $deleteRequestsQuery = "DELETE FROM requests WHERE user_id = $userID";
                $deleteReviewQuery = "DELETE FROM reviews WHERE user_id = $userID";
                if (mysqli_query($conn, $deleteDonorsQuery) && mysqli_query($conn, $deleteRequestsQuery) && mysqli_query($conn, $deleteReviewQuery)) {
                    // Related records deleted, now delete the account
                    $deleteQuery = "DELETE FROM users WHERE id = $userID";
                    if (mysqli_query($conn, $deleteQuery)) {
                        // Account deleted, redirect to login page
                        header("Location: login.php");
                        exit(); // Terminate script execution after redirect
                    } else {
                        $errorMsg = "Failed to delete account. Please try again later.";
                    }
                } else {
                    $errorMsg = "Failed to delete account. Please try again later.";
                }
            } else {
                $errorMsg = "Incorrect password. Please, try again!!";
            }
        } else {
            $errorMsg = "Failed to retrieve user data. Please try again later.";
        }
    } else {
        $errorMsg = "Please enter your password to proceed.";
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Account</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/deleteaccount.css">
</head>

<body>
    <div class="popup-content">
        <span class="close-btn"><a href="settings.php"><i class="fas fa-times" style="color:red"></i></a></span>
        <h2>Delete Account</h2>
        <form method="post">
            <label for="password">Confirm Password</label>
            <div class="password-container">
                <input type="password" id="password" name="password" class="password-input" value="<?php echo $password; ?>">
                <i class="fa-solid fa-eye show-password"></i>
            </div>
            <span class="error">
                <?php echo $errorMsg; ?>
            </span><br>
            <p><i class="fa-solid fa-triangle-exclamation"></i>&nbsp This process is irreversible</p>
            <button id="bb" type="submit">Delete</button>
        </form>
    </div>

    <script src="js/deleteaccount.js"></script>
</body>

</html>
