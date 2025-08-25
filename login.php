<?php

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

session_start(); // Starting the session to use session variables
$errorMsg = ""; // Initialize an empty error message variable

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    // Something was posted
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (!empty($email) && !empty($password)) {

        // Sanitize user input to prevent SQL Injection
        $email = mysqli_real_escape_string($conn, $email);
        $password = mysqli_real_escape_string($conn, $password);

        // Read from database
        $query = "SELECT * FROM users WHERE email = '$email' LIMIT 1";
        $result = mysqli_query($conn, $query);

        if ($result && mysqli_num_rows($result) > 0) {
            $user = mysqli_fetch_assoc($result);
            // Verify password
            if (password_verify($password, $user['password'])) {
                $_SESSION['userID'] = $user['id'];
                $_SESSION['username'] = $user['username'];

                // Check if gender, bloodgroup, or dob is incomplete
                if (empty($user['gender']) || empty($user['bloodgroup']) || empty($user['dob'])) {
                    header("Location: complete_profile");
                    exit();
                } else {
                    header("Location: welcome");
                    exit();
                }
            } else {
                $errorMsg = "Wrong username or password!";
            }
        } else {
            $errorMsg = "User not found!";
        }
    } else {
        $errorMsg = "Please enter both email and password!";
    }
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel='stylesheet' href='css/signup.css'>
  
</head>


<body style="background-image: url(images/mainbg.jpg);background-repeat: no-repeat;background-size: cover;">
    <div class="logo">hello</div>
    <div class="login-container" >
        <h2>LOGIN</h2>

        <!-- Display error messages at the top of the division -->
        <div class="error-message" style="font-weight:500;color:red;">
            <?php echo $errorMsg; ?>
        </div>

        <form method="post">
            <div class="form-group">
                <label for="email"><b>Email</b></label>
                <div class="password-container">
                    <input type="email" name="email" id="email" placeholder="Enter your email">
                    <i class='bx bx-envelope'></i>
                </div>
            </div>

            <div class="form-group">
                <label for="password"><b>Password</b></label>
                <div class="password-container">
                    <input type="password" name="password" id="password" placeholder="Enter your password">
                    <i class='bx bx-lock-alt'></i>
                </div>
            </div>

            <div class="form-group">
                <input type="submit" name="submit" id="submit" value="Login">
            </div>
            <div class="form-group">
                <a href="signup.php">Click to Signup</a>
            </div>
        </form>
    </div>
</body>

</html>
