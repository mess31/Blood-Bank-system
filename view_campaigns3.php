<?php
session_start();

if (!isset($_SESSION['userID'])) {
    header("Location: login.php");
    exit();
}

$db_host = "localhost";
$db_user = "root";
$db_password = "";
$db_name = "bloodlink";

$conn = mysqli_connect($db_host, $db_user, $db_password, $db_name);

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

    if (empty($dob) || empty($gender) || empty($bloodgroup)) {
        header("Location: complete_profile.php");
        exit();
    }
} else {
    header("Location: login.php");
    exit();
}

$campsel_sql = "SELECT * FROM campaign";
$result = $conn->query($campsel_sql);

?>

<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
    <meta charset="UTF-8">
    <title> newdashboard</title>
    <link rel="stylesheet" href="./css/campaignDftt.css">
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
                        <i class="fa-solid fa-tents" style="color:black;background:white"></i>
                        <span class="links_name">View Campaigns</span>
                    </a>
                    <span class="tooltip">View Campaigns</span>
                </li>
        </ul>
    </div>


    <section class="home-section">
        <div class="text">UPCOMING CAMPAIGNS</div>
        <div class="campaignboxxes">
        <?php
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo '<div class="rectangular-div">';
                    echo '<div class="campaign-image"><img src="./images/campaignpic/' . $row["camp_img"] . '"></div>';
                    echo '<div class="campaign-details">';
                    echo '<div class="campaign-title">' . htmlspecialchars($row["title"]) . '</div>';
                    echo '<div class="campaign-description">' . htmlspecialchars($row["description"]) . '</div>';
                    echo '<div class="campaign-reviews">';
                    echo '<div class="star-rating" data-rating="0">';
                    for ($i = 1; $i <= 5; $i++) {
                        echo '<span class="star" data-value="' . $i . '">&#9733;</span>';
                    }
                    echo '</div><br>';
                    echo '</div>';
                    echo '<div class="review-stats">';
                    echo '<span class="total-reviews">0</span> ( <span class="average-rating">0</span> )';
                    echo '</div>';
                    echo '<a href="view_campdetails.php?campaign_id=' . $row['campaign_id'] . '">View Details</a>';
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
