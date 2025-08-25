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

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    // Checking if 'campaign_id' parameter is set and is a numeric value
    if (!isset($_GET["campaign_id"]) || !is_numeric($_GET["campaign_id"])) {
        header("location: campaignDft.php"); // Redirecting if parameter is missing or not numeric
        exit;
    }
    $campaign_id = $_GET["campaign_id"]; // Assigning the 'campaign_id' value to a variable

    // Fetch campaign details using the campaign_id
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
}

?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <title>Campaigns</title>
    <link rel="stylesheet" href="./css/campaign_info.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body>

<div class="header">
    <div class="logo"></div>
    <div class="user-info">
        <div class="welcome-text"><b id="text1"><?php echo $name; ?></b>
            <div class="demoimage">
                <img src="./images/userprofilepic/<?php echo $profile_img; ?>" alt="" id="default-img">
            </div>
        </div>
        <i class="fas fa-caret-down" class="dropdown-icon" onclick="toggleDropdown()"></i>
        <div class="dropdown-menu" id="dropdownMenu">
            <div class="dropdown-content">
                <a href="settings.php">Settings</a>
                <a href="adminwelcome.php">Dashboard</a>
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

<section class="home-section" >
        <div class="camp-card">
            <div class="camp-info">
                <h2><?php echo htmlspecialchars($title); ?></h2>
                <p><?php echo htmlspecialchars($description); ?></p>
              <br>  <ul style="margin-left:25px">
                    <li><strong>Date:</strong> <?php echo htmlspecialchars($camp_date); ?></li>
                    <li><strong>Organizer:</strong> <?php echo htmlspecialchars($organizer); ?></li>
                    <li><strong>Venue:</strong> <?php echo htmlspecialchars($venue); ?></li>
                </ul>

                <p>Join us at our upcoming blood donation camp to make a difference in your community!</p>              
            </div>
            <div class="photo-container">
                <img src="./images/campaignpic/<?php echo htmlspecialchars($camp_img); ?>" alt="campaign-image"> 
            </div>
        </div>
        <div class="reviews-table" id="reviewsTable">
        <h2>Reviews on this campaign</h2>
        <br>
        <br>
        <?php
        // Fetch reviews for the specific campaign
        $stmt = $conn->prepare("SELECT * FROM reviews r INNER JOIN users u ON r.user_id = u.id WHERE r.user_id = ? ORDER BY r.review_id DESC");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if ($result->num_rows > 0) {
            echo "<table class='review-data'>
                    <tr>
                        <th>S.No.</th>
                        <th>Rating</th>
                        <th>Message</th>
                        <th>Review Date</th>
                        <th>Action</th>
                    </tr>";
                
            $serialNumber = 1;
            
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>" . $serialNumber . "</td>";
                
                        echo "<td>";
                        for ($i = 1; $i <= 5; $i++) {
                            echo $i <= $row["star_count"] ? '<span class="star" data-value="' . $i . '">&#9733;</span>' : '<span class="star">&#9734;</span>';
                        }
                        echo "</td>";
        
                echo "<td class='message'>" . $row["message"] . "</td>
                        <td>" . $row["review_date"] . "</td>
                        <td>
                        <a href='delete_spfeedback.php?review_id=" . $row["review_id"] . "&campaign_id=" . $campaign_id . "' style='text-decoration: none; cursor: pointer;'><i class='fa-solid fa-trash-can'></i> Delete</a>
                        </td>
                    </tr>";
                
                $serialNumber++;
            }
            echo "</table>";
        } else {
            echo "No reviews to display.";
        }
        
        $conn->close();
        ?>
    </div>
    <a href="view_reviews.php"><button class="cancel">Go Back</button></a>
    </section>
    

</body>
</html> 
