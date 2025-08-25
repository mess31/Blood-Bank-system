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

// Fetch campaign details from the database
$campsel_sql = "SELECT * FROM campaign";
$result = $conn->query($campsel_sql);

?>

<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
    <meta charset="UTF-8">
    <title>Campaigns</title>
    <link rel="stylesheet" href="css/view_campaigns.css">
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
            <li class="active">
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
                        <span class="links_name">View Campaigns</span>
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


    <section class="home-section">
        <div class="text">Campaigns</div>
        <div class="campaignboxxes">
        <?php
            if ($result->num_rows > 0) {
                // Output data for each row
                while($row = $result->fetch_assoc()) {
                    echo '<div class="rectangular-div">';
                    echo '<div class="campaign-image"><img src="./images/campaignpic/' . $row["camp_img"] . '"></div>';
                    echo '<div class="campaign-details">';
                    echo '<div class="campaign-title">' . htmlspecialchars($row["title"]) . '</div>';
                    echo '<div class="campaign-description">' . htmlspecialchars($row["description"]) . '</div>';
                    echo '<a class="details" href="view_campdetails.php?campaign_id=' . $row['campaign_id'] . '">View Details</a>';
                    echo '</div>';
                    echo '</div>';
                }
            } else {
                echo "No campaigns found.";
            }
            $conn->close();
            ?>
    </section>

</body>

</html>
