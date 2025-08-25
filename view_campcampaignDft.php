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

?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <title>Campaigns</title>
    <link rel="stylesheet" href="./css/campaigns.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body>

<div class="header">
        <div class="logo"></div>

        <div class="search-container">
            <input type="text" class="search-input" placeholder="Search...">
            <i class="fas fa-search search-icon"></i>
        </div>

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
        </ul>
    </div>


<section class="campaigns-table" id="campaignsTable">
    <h2>All Campaigns</h2>
    <br>
    <?php
    // Fetch all campaigns
    $sql = "SELECT * FROM campaign";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo "<table class='campaign-data'>
                <tr>
                    <th>S.No.</th>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Organizer</th>
                    <th>Venue</th>
                    <th>Date</th>
                </tr>";

        // Initialize counter
        $serialNumber = 1;

        // Output data of each row
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>" . $serialNumber . "</td>
                    <td>" . $row["title"] . "</td>
                    <td class='description'>" . $row["description"] . "</td>
                    <td>" . $row["organizer"] . "</td>
                    <td>" . $row["venue"] . "</td>
                    <td>" . $row["camp_date"] . "</td>
                </tr>";

            // Increment serial number
            $serialNumber++;
        }
        echo "</table>";
    } else {
        echo "No results to display";
    }

    $conn->close();
    ?>
</section>


<script>
function toggleDropdown() {
    var dropdownMenu = document.getElementById("dropdownMenu");
    if (dropdownMenu.style.display === "block") {
        dropdownMenu.style.display = "none";
    } else {
        dropdownMenu.style.display = "block";
    }
}


function showPopup() {
    // Show the popup
    var requestBody = document.querySelector('.sectionBody');
    requestBody.style.display = 'block';

    // Show the overlay
    var overlay = document.querySelector('.popup-overlay');
    overlay.style.display = 'block';
}

function closePopup() {
    // Hide the popup
    var requestBody = document.querySelector('.sectionBody');
    requestBody.style.display = 'none';

    // Hide the overlay
    var overlay = document.querySelector('.popup-overlay');
    overlay.style.display = 'none';
}
</script>

</body>
</html> 
