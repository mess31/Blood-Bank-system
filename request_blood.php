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

$id = $_SESSION['userID'];
$sql = "SELECT name, dob, gender, bloodgroup, profile_img FROM users WHERE id = '$id'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $name = $row['name'];
    $profile_img = $row['profile_img'];
    $dob = $row['dob'];
    $gender = $row['gender'];
    $bloodgroup = $row['bloodgroup'];

    // Check if any of the required fields are empty
    if (empty($dob) || empty($gender) || empty($bloodgroup)) {
        // Redirect to complete_profile.php if any of the fields are empty
        header("Location: complete_profile.php");
        exit();
    }
} else {
    // Redirect to login page if user data not found
    header("Location: login.php");
    exit();
}

$reqBloodGrp = $bloodQty = $requiredFor = $contactNum = $address = $note = "";
$errors = array(); // Array to store error messages

// Process form data when the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $reqBloodGrp = $_POST["reqbloodgrp"];
    $bloodQty = $_POST["bloodqty"];
    $requiredFor = $_POST["requiredfor"];
    $contactNum = $_POST["contact"];
    $address = $_POST["address"];
    $note = $_POST["note"];

    // Get the userID from the session
    $userID = $_SESSION['userID'];

    // Check for empty form fields
    if (empty($reqBloodGrp) || empty($bloodQty) || empty($requiredFor) || empty($contactNum) || empty($address) || empty($note)) {
        $errors[] = "All fields are required.";
    } else {
        // Prepare the insert statement
        $sql = "INSERT INTO requests (req_bloodGrp, bloodQty, requiredFor, contactNo, address, note, user_id) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssi", $reqBloodGrp, $bloodQty, $requiredFor, $contactNum, $address, $note, $userID);

        // Execute the statement
        if ($stmt->execute()) {
            // Redirect to request history page after successful submission
            header("Location: request_history.php");
            exit();
        } else {
            // Handle SQL errors
            $errors[] = "Error: " . $sql . "<br>" . $conn->error;
        }

        // Close the statement
        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Initiate Request Blood</title>
    <link rel="stylesheet" href="./css/uwelcome.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body>
    <div class="header">
        <div class="logo"></div>


        <div class="user-info">
            <div class="welcome-text"><b id="text1">
                    <?php echo $name; ?>
                </b>
                <div class="demoimage">
                    <img src="./images/userprofilepic/<?php echo $profile_img; ?>" alt="" id="default-img">
                </div>
            </div>

            <i class="fas fa-caret-down" class="dropdown-icon" onclick="toggleDropdown()"></i>
            <div class="dropdown-menu" id="dropdownMenu">
                <div class="dropdown-content">
                    <a href="settings.php">Settings</a>
                    <!-- <a href="profile.php">Profile</a> -->
                    <a href="welcome.php">Dashboard</a>
                    <a href="logout.php">Logout</a>
                </div>
            </div>
        </div>
    </div>
    <hr>

    <div class="sidebar">
        <div class="logo-details">
            <div class="logo_name"> BloodLink Network</div>
            <i class="fa-solid fa-bars" id="btn"></i>
        </div>

        <ul class="nav-list">
            <li>
                <a href="<?php echo isset($_SESSION['userID']) ? 'welcome.php' : 'login.php'; ?>">
                    <i class="fa-solid fa-gauge"></i>
                    <span class="links_name">Dashboard</span>
                </a>
                <span class="tooltip">Dashboard</span>
            </li>

            <li>
                <a href="<?php echo isset($_SESSION['userID']) ? 'donate_blood.php' : 'login.php'; ?>">
                    <i class="fa-solid fa-hand-holding-medical"></i>
                    <span class="links_name">Donate Blood</span>
                </a>
                <span class="tooltip">Donate Blood</span>
            </li>

            <li>
                <a href="<?php echo isset($_SESSION['userID']) ? 'donation_history.php' : 'login.php'; ?>">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                    <span class="links_name">Donation History</span>
                </a>
                <span class="tooltip">Donation History</span>
            </li>

            <li>
                <a href="<?php echo isset($_SESSION['userID']) ? 'request_blood.php' : 'login.php'; ?>">
                    <i class="fa-solid fa-code-pull-request"></i>
                    <span class="links_name">Initiate Blood Request</span>
                </a>
                <span class="tooltip">Blood Request</span>
            </li>

            <li>
                <a href="<?php echo isset($_SESSION['userID']) ? 'request_history.php' : 'login.php'; ?>">
                    <i class="fa-solid fa-hand"></i>
                    <span class="links_name">Request History</span>
                </a>
                <span class="tooltip">Request History</span>
            </li>

            <li>
                <a href="<?php echo isset($_SESSION['userID']) ? 'view_campaigns.php' : 'login.php'; ?>">
                    <i class="fa-solid fa-tents"></i>
                    <span class="links_name">view Campaigns</span>
                </a>
                <span class="tooltip">View Campaigns</span>
            </li>

            <li>
                    <a href="<?php echo isset($_SESSION['userID']) ? 'view_reviews.php' : 'login.php'; ?>">
                        <i class="fa-solid fa-comment-dots"></i>
                        <span class="links_name">Feedback</span>
                    </a>
                    <span class="tooltip">Feedback</span>
            </li>
        </ul>
    </div>


    <section class="requestBody">
        <h2>Initiate Blood Request</h2>
        <?php
        // Display errors, if any
        if (!empty($errors)) {
            foreach ($errors as $error) {
                echo "<div style='color:red;'>$error</div>";
            }
        }
        ?>
        <form id="requestForm" method="post">
            <label for="reqbloodgrp">Required Blood Group:</label>
            <select id="reqbloodgrp" name="reqbloodgrp">
                <option value="">Select Blood Group</option>
                <option value="A+" <?php if ($reqBloodGrp == 'A+') echo 'selected="selected"'; ?>>A+</option>
                <option value="A-" <?php if ($reqBloodGrp == 'A-') echo 'selected="selected"'; ?>>A-</option>
                <option value="B+" <?php if ($reqBloodGrp == 'B+') echo 'selected="selected"'; ?>>B+</option>
                <option value="B-" <?php if ($reqBloodGrp == 'B-') echo 'selected="selected"'; ?>>B-</option>
                <option value="AB+" <?php if ($reqBloodGrp == 'AB+') echo 'selected="selected"'; ?>>AB+</option>
                <option value="AB-" <?php if ($reqBloodGrp == 'AB-') echo 'selected="selected"'; ?>>AB-</option>
                <option value="O+" <?php if ($reqBloodGrp == 'O+') echo 'selected="selected"'; ?>>O+</option>
                <option value="O-" <?php if ($reqBloodGrp == 'O-') echo 'selected="selected"'; ?>>O-</option>
            </select><br>
    
            <label for="bloodqty">Blood Quantity (in units):</label>
            <input type="number" id="bloodqty" name="bloodqty" value="<?php echo $bloodQty; ?>"><br>
    
            <label for="requiredfor">Required For:</label>
            <input type="text" id="requiredfor" name="requiredfor" value="<?php echo $requiredFor; ?>"><br>
    
            <label for="contact">Contact Number:</label>
            <input type="number" id="contact" name="contact" value="<?php echo $contactNum; ?>"><br>
    
            <label for="address">Address:</label>
            <input type="text" id="address" name="address" value="<?php echo $address; ?>"><br>
    
            <label for="note">Note:</label>
            <textarea id="note" name="note" placeholder="Please leave a short message..." rows="4"
                      cols="30"><?php echo htmlspecialchars($note); ?></textarea><br>
    
            <input type="submit" value="Submit">
        </form>
    </section>

    <script src="./js/welcome.js"></script>
</body>

</html>
