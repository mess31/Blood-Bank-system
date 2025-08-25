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
$title = $description = $camp_date = $organizer = $venue = $camp_img = "";

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


if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    // Checking if 'campaign_id' parameter is set and is a numeric value
    if (!isset($_GET["campaign_id"]) || !is_numeric($_GET["campaign_id"])) {
        header("location: view_campaigns.php"); // Redirecting if parameter is missing or not numeric
        exit;
    }
    $campaign_id = $_GET["campaign_id"]; // Assigning the 'campaign_id' value to a variable
    
    // Fetch campaign details using the campaign_id
}

$starCountErr = $messageErr = ""; // Initialize error variables
// Check if the form has been submitted
$campsel_sql = "SELECT * FROM campaign WHERE campaign_id = ?";
    $stmt = $conn->prepare($campsel_sql);
    $stmt->bind_param("i", $campaign_id); // 'i' for integer
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        // Fetching campaign details
        $title = $row['title'];
        $description = $row['description'];
        $camp_date = $row['camp_date'];
        $organizer = $row['organizer'];
        $venue = $row['venue'];
        $camp_img = $row['camp_img'];
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $campaign_id = $_GET["campaign_id"];
    if(isset($_POST["submit"])){

        $campsel_sql = "SELECT * FROM campaign WHERE campaign_id = ?";
        $stmt = $conn->prepare($campsel_sql);
        $stmt->bind_param("i", $campaign_id); // 'i' for integer
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            // Fetching campaign details
            $title = $row['title'];
            $description = $row['description'];
            $camp_date = $row['camp_date'];
            $organizer = $row['organizer'];
            $venue = $row['venue'];
            $camp_img = $row['camp_img'];
    }
    // Validate and sanitize inputs
    $starCount = $_POST['star_count'];
    $message = $_POST['message'];
    
    // Check if any field is empty
    $isValid = true;
    
    if (empty($starCount)) {
        $starCountErr = "Star rating is required.";
        $isValid = false;
    }
    if (empty($message)) {
        $messageErr = "Message is required.";
        $isValid = false;
    }
    
    if ($isValid) {
        // Prepare and bind parameters
        $stmt = $conn->prepare("INSERT INTO reviews (star_count, message, user_id, item_id) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isii", $starCount, $message, $id, $campaign_id);
        
        // Execute the statement
        if ($stmt->execute()) {
            header("Location: view_reviews.php");
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }
    }
}
}
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
    <meta charset="UTF-8">
    <title>newdashboard</title>
    <link rel="stylesheet" href="./css/view_campdetails.css">
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

            <i class="fas fa-caret-down dropdown-icon" onclick="toggleDropdown()"></i>
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
            <div class="logo_name">BloodLink Network</div>
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
        <div class="camp-card">
            <div class="camp-info">
                <?php if (!empty($title || $description || $camp_date || $organizer || $venue)): ?>
                <h2><?php echo htmlspecialchars($title); ?></h2>
                <p><?php echo htmlspecialchars($description); ?></p>
              <br>  <ul style="margin-left:25px">
                    <li><strong>Date:</strong> <?php echo htmlspecialchars($camp_date); ?></li>
                    <li><strong>Organizer:</strong> <?php echo htmlspecialchars($organizer); ?></li>
                    <li><strong>Venue:</strong> <?php echo htmlspecialchars($venue); ?></li>
                    <?php endif; ?>
                </ul>

                <p>Join us at our upcoming blood donation camp to make a difference in your community!</p>             
            </div>
            <div class="photo-container">
                <img src="./images/campaignpic/<?php echo htmlspecialchars($camp_img); ?>" alt="campaign-image"> 
            </div>
        </div>

        <form id="reviewForm" method="post">
            <input type="hidden" name="campaign_id" value="<?php echo htmlspecialchars($campaign_id); ?>">
            <div class="campaign-reviews">
                <div class="star-rating">
                    <span class="star" data-value="1">&#9733;</span>
                    <span class="star" data-value="2">&#9733;</span>
                    <span class="star" data-value="3">&#9733;</span>
                    <span class="star" data-value="4">&#9733;</span>
                    <span class="star" data-value="5">&#9733;</span>
                </div>
                <!-- Hidden input field for star_count -->
                <input type="hidden" name="star_count" id="star_count">
                <span class="error"><?php echo $starCountErr; ?></span>
                <br>
                <textarea rows="8" cols="40" style="width: 500px; height: 250px; resize: none;border-radius: 12px;padding:8px;border:4px solid black;" placeholder="Write your review here" name="message"></textarea>
                <span class="error"><?php echo $messageErr; ?></span>
                <br>
                <input name="submit" type="submit" value="Post" class="post">
            </div>
        </form>
    </section>
    <script>
    document.querySelectorAll('.star').forEach(star => {
        star.addEventListener('click', event => {
            const rating = parseInt(event.target.getAttribute('data-value'));
            const starRating = event.target.parentElement;
            starRating.setAttribute('data-rating', rating);
            starRating.querySelectorAll('.star').forEach(s => {
                s.classList.remove('selected');
                if (parseInt(s.getAttribute('data-value')) <= rating) {
                    s.classList.add('selected');
                }
            });
            document.getElementById('star_count').value = rating; // Set the value of the hidden input field
        });
    });
    </script>

</body>
</html>
