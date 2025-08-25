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

// Fetch user-specific data
// Approved Donations
$donation_sql = "SELECT COUNT(*) AS total_donations FROM donors WHERE user_id = '$id' AND status = 'Approved'";
$donation_result = $conn->query($donation_sql);
$total_donations = $donation_result->fetch_assoc()['total_donations'];

// Approved Requests
$request_sql = "SELECT COUNT(*) AS total_requests FROM requests WHERE user_id = '$id' AND rqst_status = 'Approved'";
$request_result = $conn->query($request_sql);
$total_requests = $request_result->fetch_assoc()['total_requests'];

// Total Campaigns
$campaign_sql = "SELECT COUNT(*) AS total_campaigns FROM campaign";
$campaign_result = $conn->query($campaign_sql);
$total_campaigns = $campaign_result->fetch_assoc()['total_campaigns'];

// User Reviews
$review_sql = "SELECT COUNT(*) AS total_reviews FROM reviews WHERE user_id = '$id'";
$review_result = $conn->query($review_sql);
$total_reviews = $review_result->fetch_assoc()['total_reviews'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
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
        <div class="text">Dashboard</div>

        <div class="firstboxxes">
            <div class="box1 pink">
                <div class="first-line">
                    <span>Donations</span>
                    <span><a href="donation_history.php"><button id="pink"> <i class="fa-solid fa-circle-right"></i></button></a></span>
                </div>
                <div class="second-line">
                    <i class="fa-solid fa-hand-holding-droplet" style="font-size:60px"></i>
                    <span>Total<br>
                        <p style="color:red;font-size:25px"><?php echo $total_donations; ?></p>
                    </span>
                </div>
            </div>

            <div class="box1 blue">
                <div class="first-line">
                    <span>Requests</span>
                    <span><a href="request_history.php"><button id="blue"> <i class="fa-solid fa-circle-right"></i></button></a></span>
                </div>
                <div class="second-line">
                    <i class="fa-solid fa-code-pull-request" style="font-size:60px"></i>
                    <span>Total<br>
                        <p style="color:blue;font-size:25px"><?php echo $total_requests; ?></p>
                    </span>
                </div>                
            </div>

            <div class="box1 green">
                <div class="first-line">
                    <span>View Campaigns</span>
                    <span><a href="view_campaigns.php"><button id="green"> <i class="fa-solid fa-circle-right"></i></button></a></span>
                </div>
                <div class="second-line">
                    <i class="fa-solid fa-tents" style="font-size:60px"></i>
                    <span>Total<br>
                        <p style="color:green;font-size:25px"><?php echo $total_campaigns; ?></p>
                    </span>
                </div>
            </div>
            <div class="box1 pink">
                <div class="first-line">
                    <span>Reviews</span>
                    <span><a href="view_reviews.php"><button id="pink"> <i class="fa-solid fa-circle-right"></i></button></a></span>
                </div>
                <div class="second-line">
                <i class="fa-solid fa-comment-dots" style="font-size:60px"></i>
                    <span>Total<br>
                        <p style="color:red;font-size:25px"><?php echo $total_reviews; ?></p>
                    </span>
                </div>
            </div>
</div>
    </section>

    <script src="./js/welcomefinal.js"></script>
</body>

</html>
