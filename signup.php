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
    
    $nameErr = $usernameErr = $emailErr = $passwordErr = $confirmPasswordErr = "";
    $name = $username = $email = $password = $confirmPassword = "";
    $successMessage = "";
    
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        
        // Validation for Name
        if (empty($_POST["name"])) {
            $nameErr = "Name is required";
        } else {
            $name = test_input($_POST["name"]);
            if (!preg_match("/^[a-zA-Z\s]*$/", $name)) {
                $nameErr = "Only letters and spaces allowed";
            }
        }
        
        // Validation for Username
        if (empty($_POST["username"])) {
            $usernameErr = "Username is required";
        } else {
            $username = test_input($_POST["username"]);
            if (!preg_match("/^[a-z0-9_.]{5,30}$/", $username)) {
                $usernameErr = "Username can only use lower-case letters(a-z), numbers(0-9), underscores(_) and periods(.)";
            } else {
                // Prepare and execute SQL query
                $sql = "SELECT * FROM users WHERE username = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("s", $username);
                $stmt->execute();
                $result = $stmt->get_result();
                
                // Check if username exists in the database
                if ($result->num_rows > 0) {
                    $usernameErr = "Username already exists";
                } else {
                    // Proceed with form submission
                    // Additional validation and processing can be done here
                    $success = "Username is available";
                }
            }
        }
        
        // Validation for Email
        if (empty($_POST["email"])) {
            $emailErr = "Email is required";
        } else {
            $email = test_input($_POST["email"]);
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $emailErr = "Invalid email format";
            } else {
                // Prepare and execute SQL query
                $sql = "SELECT * FROM users WHERE email = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $result = $stmt->get_result();
                
                // Check if username exists in the database
                if ($result->num_rows > 0) {
                    $emailErr = "Email already exists";
                }
            }
        }
        
        // Validation for Password
        if (empty($_POST["password"])) {
            $passwordErr = "Password is required";
        } else {
            $password = test_input($_POST["password"]);
            if (!preg_match("/.{6}/", $password) ||
            !preg_match("/[A-Z]/", $password) ||
            !preg_match("/[a-z]/", $password) || 
            !preg_match("/[0-9]/", $password) ||
            !preg_match("/[^A-Za-z0-9\s]/", $password)) { 
                $passwordErr = "Password must be at least 6 characters with uppercase, lowercase, digit, special character and no spaces.";
            }
        }
        
        // Validation for Confirm Password
        if (empty($_POST["confirmPassword"])) {
            $confirmPasswordErr = "Confirmation of password is required";
        } else {
            $confirmPassword = test_input($_POST["confirmPassword"]);
            if ($confirmPassword !== $password) {
                $confirmPasswordErr = "Passwords do not match";
            }
        }
        
        if (empty($nameErr) && empty($usernameErr) && empty($emailErr) && empty($passwordErr) && empty($confirmPasswordErr)) {
            // Use prepared statement with bound parameters to prevent SQL injection
            $sql = "INSERT INTO users (name, username, email, password, profile_img) VALUES (?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            
            if ($stmt) {
                // Hash the password before storing it in the database
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            
                // Default profile image path
                $defaultProfileImage = 'default_profile_img.png';
            
                // Bind parameters
                mysqli_stmt_bind_param($stmt, "sssss", $name, $username, $email, $hashedPassword, $defaultProfileImage);
            
                // Execute the statement
                if (mysqli_stmt_execute($stmt)) {
                    $successMessage = "Data inserted successfully";
                    header("Location: login.php");
                    exit;
                } else {
                    echo "Data not inserted: " . mysqli_error($conn);
                }
            
                // Close the statement
                mysqli_stmt_close($stmt);
            } else {
                echo "Prepared statement failed: " . mysqli_error($conn);
            }
    
            
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
    <title>Fill up Form</title>
    <!-- <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'> -->

    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(to bottom right, #f0f0f0, #dcdcdc);
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .logo {
            position: absolute;
            left: 5px;
            top: -30px;
            z-index: 999;
            background-image: url(images/BLN.png);
            background-repeat: no-repeat;
            background-size: cover;
            height: 100px;
            width: 210px;
        }

        .login-container {
            background-color: rgba(255, 255, 255, 0.8);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
            width: 320px;
            max-width: 90%;
            border: 3px solid;
            border-image: linear-gradient(to right, #FF512F, #DD2476, #6B1BBF, #00B1FF, #00FF8C, #FFD500);
            border-image-slice: 1;
        }

        .login-container h2 {
            text-align: center;
            margin-bottom: 10px;
            color: #FF512F;
        }

        .form-group {
            position: relative;
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #333;
            font-size: 16px;
        }

        .form-group input[type="text"],
        .form-group input[type="email"],
        .form-group input[type="password"] {
            padding: 10px 40px 10px 10px;
            border: 1px solid #ff512f;
            border-radius: 5px;
            font-size: 16px;
            width: calc(100% - 50px);
            position: relative;
        }

        .form-group input[type="submit"] {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 5px;
            background-color: #FF512F;
            color: white;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }

        .form-group input[type="submit"]:hover {
            background-color: #DD2476;
        }

        .form-group a {
            text-decoration: none;
            color: #FF512F;
            display: block;
            text-align: center;
            font-size: 14px;
        }

        /* Icon Styles */
        .form-group input+.bx {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            right: 15px;
            font-size: 20px;
            color: #888;
            pointer-events: none;
        }
    </style>
</head>

<body style="background-image: url(images/mainbg.jpg);background-repeat: no-repeat;background-size: cover;">
    <div class="logo"></div>
    <div class="login-container">
        <h2>SignUp</h2>
        <form method="post">
            <div>
                <?php if (!empty($successMessage)) : ?>
                <p class="success">
                    <?php echo $successMessage; ?>
                </p>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" name="name" id="name" placeholder="Name" value="<?php echo $name; ?>">
                <i class='bx bx-user'></i>
                <span class="error"  style="color: red !important;">
                    <?php echo $nameErr; ?>
                </span>
            </div>

            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" name="username" id="username" placeholder="Username"
                    value="<?php echo $username; ?>">
                <i class='bx bxs-user-circle'></i>
                <span class="error"  style="color: red !important;">
                    <?php echo $usernameErr; ?>
                </span>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" placeholder="Email" value="<?php echo $email; ?>">
                <i class='bx bx-envelope'></i>
                <span class="error"  style="color: red !important;">
                    <?php echo $emailErr; ?>
                </span>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" placeholder="Password"
                    value="<?php echo $password; ?>">
                <i class='bx bx-key'></i>
                <span class="error"  style="color: red !important;">
                    <?php echo $passwordErr; ?>
                </span>
            </div>

            <div class="form-group">
                <label for="confirmPassword">Confirm Password</label>
                <input type="password" name="confirmPassword" id="confirmPassword" placeholder="Confirm Password"
                    value="<?php echo $confirmPassword; ?>">
                <i class='bx bx-lock-alt'></i>
                <span class="error" style="color: red !important;">
                    <?php echo $confirmPasswordErr; ?>
                </span>
            </div>

            <div class="form-group">
                <input type="submit" name="submit" id="submit" value="Submit">
            </div>

            <div class="already">Already Have an account?<a href="login.php">Login</a>
        </form>
    </div>
</body>

</html>
