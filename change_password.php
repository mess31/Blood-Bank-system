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

$currentPasswordErr = $newPasswordErr = $confirmNewPasswordErr = "";
$currentPassword = $newPassword = $confirmNewPassword = "";
$successMessage = $errorMessage = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validation for Current Password
    if (empty($_POST["currentPassword"])) {
        $currentPasswordErr = "Current Password is required";
    } else {
        $currentPassword = test_input($_POST["currentPassword"]);
        $username = $_SESSION["username"];
        $sql = "SELECT password FROM users WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $hashedPassword = $row["password"];
        if (!password_verify($currentPassword, $hashedPassword)) {
            $currentPasswordErr = "Incorrect current password";
        }
    }

    // Check if new password is the same as the current password
    if ($_POST["newPassword"] === $_POST["currentPassword"]) {
        $newPasswordErr = "New password should not be the same as the current password";
    }

    // Validation for New Password
    if (empty($_POST["newPassword"])) {
        $newPasswordErr = "New Password is required";
    } else {
        $newPassword = test_input($_POST["newPassword"]);
        if (!preg_match("/.{6}/", $newPassword) ||
            !preg_match("/[A-Z]/", $newPassword) ||
            !preg_match("/[a-z]/", $newPassword) ||
            !preg_match("/[0-9]/", $newPassword) ||
            !preg_match("/[^A-Za-z0-9\s]/", $newPassword)) {
            $newPasswordErr = "Password must be at least 6 characters with uppercase, lowercase, digit, special character and no spaces.";
        }
    }

    // Validation for Confirm New Password
    if (empty($_POST["confirmNewPassword"])) {
        $confirmNewPasswordErr = "Confirmation of new password is required";
    } else {
        $confirmNewPassword = test_input($_POST["confirmNewPassword"]);
        if ($confirmNewPassword !== $newPassword) {
            $confirmNewPasswordErr = "Passwords do not match";
        }
    }

    if (empty($currentPasswordErr) && empty($newPasswordErr) && empty($confirmNewPasswordErr)) {
        // Update password
        $hashedNewPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $sql = "UPDATE users SET password = ? WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $hashedNewPassword, $username);
        if ($stmt->execute()) {
            $successMessage = "Password updated successfully";
            header("Location: settings.php");
            exit();
        } else {
            $errorMessage = "Error updating password: " . $conn->error;
        }
        $stmt->close();
    }
}

function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/changepassword.css">

</head>

<body>
    <div class="popup-content">
        <span class="close-btn"><a href="settings.php"><i class="fas fa-times" style="color:red"></i></a></span>
        <h2>Change Password</h2>
        <form method="post">

            <label for="currentPassword">Current Password</label>
            <div class="password-container">
                <input type="password" name="currentPassword" placeholder="Enter your current password" id="currentPassword" class="password-input"
                    value="<?php echo $currentPassword; ?>">
                <i class="fa-solid fa-eye show-password"></i>
            </div>
            <span class="error">
                <?php echo $currentPasswordErr; ?>
            </span>

            <label for="newPassword">New Password</label>
            <div class="password-container">
                <input type="password" name="newPassword" placeholder="Enter your new password" id="newPassword" class="password-input"
                    value="<?php echo $newPassword; ?>">
                <i class="fa-solid fa-eye show-password"></i>
            </div>
            <span class="error">
                <?php echo $newPasswordErr; ?>
            </span>

            <label for="confirmNewPassword">Confirm New Password</label>
            <div class="password-container">
                <input type="password" name="confirmNewPassword" placeholder="Confirm your new password" id="confirmNewPassword" class="password-input"
                    value="<?php echo $confirmNewPassword; ?>">
                <i class="fa-solid fa-eye show-password"></i>
            </div>
            <span class="error">
                <?php echo $confirmNewPasswordErr ?>
            </span>

            <button id="bb" type="submit">Change Password</button>
        </form>
        <span class="success">
            <?php echo $successMessage; ?>
        </span>

        <span class="error">
            <?php echo $errorMessage; ?>
        </span>
    </div>

    <script src="js/changepassword.js"></script>
</body>

</html>
