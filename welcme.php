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
        </ul>
    </div>

    <section class="home-section">
        <div class="text">Dashboard</div>

        <div class="firstboxxes">
            <div class="box1 pink">
                <div class="first-line">
                    <span>Donations</span>
                    <span><button id="pink"> <i class="fa-solid fa-circle-right"></i></button></span>
                </div>
                <div class="second-line">
                    <i class="fa-solid fa-hand-holding-droplet" style="font-size:60px"></i>
                    <span>Pending<br>
                        <p style="color:red;font-size:25px">42</p>
                    </span>
                    <span>Approved<br>
                        <p style="color:red;font-size:25px">42</p>
                    </span>
                    <span>Rejected<br>
                        <p style="color:red;font-size:25px">42</p>
                    </span>
                </div>
            </div>

            <div class="box1 blue">
                <div class="first-line">
                    <span>Requests</span>
                    <span> <button id="blue"> <i class="fa-solid fa-circle-right"></i></button></span>
                </div>
                <div class="second-line">
                    <i class="fa-solid fa-code-pull-request" style="font-size:60px"></i>
                    <span>Pending<br>
                        <p style="color:blue;font-size:25px">10</p>
                    </span>
                    <span>Approved<br>
                        <p style="color:blue;font-size:25px">10</p>
                    </span>
                    <span>Rejected<br>
                        <p style="color:blue;font-size:25px">10</p>
                    </span>
                </div>                
            </div>

            <div class="box1 green">
                <div class="first-line">
                    <span>Blood In Stock</span>
                    <span><button id="green"> <i class="fa-solid fa-circle-right"></i></button></span>
                </div>
                <div class="second-line">
                    <i class="fa-solid fa-truck-droplet" style="font-size:60px"></i>
                    <span>Total<br>
                        <p style="color:green;font-size:25px">500</p>
                    </span>
                </div>
            </div>
        </div>

        <div class="secondboxxes">
            <div class="box2 white" id="donor-list">Latest Donors<br>
                <hr>
                <div class="peoples">
                    <div class="donor">
                        <span>Shahil Hussain<br>
                            <p style="color:purple;font-size:11px"> Male</p>
                        </span>
                        <span id="blood-type">B+</span>
                    </div>
                    <div class="donor">
                        <span>Atulesh Gautam<br>
                            <p style="color:purple;font-size:11px"> Male</p>
                        </span>
                        <span id="blood-type">O+</span>
                    </div>
                    <div class="donor">
                        <span>Sita Karki<br>
                            <p style="color:purple;font-size:11px"> Female</p>
                        </span>
                        <span id="blood-type">AB-</span>
                    </div>
                    <div class="donor">
                        <span>Bishal Panta<br>
                            <p style="color:purple;font-size:11px"> Male</p>
                        </span>
                        <span id="blood-type">A+</span>
                    </div>
                    <div class="donor">
                        <span> Rani Shrestha<br>
                            <p style="color:purple;font-size:11px"> Female</p>
                        </span>
                        <span id="blood-type">B-</span>
                    </div>
                    <div class="donor"
                        style="justify-content:center;border:0.1px solid black;border-radius:12px;margin-top:8px;">
                        <button style="background:transparent;cursor:pointer;border:none">See More</button>
                    </div>
                </div>
            </div>

            <div class="box2 orange">
                <span>View Announcements</span><br>
                <i class="fa-solid fa-bullhorn"></i>
                <p>Announcements will be made here</p>
            </div>

            <div class="box2 yellow">
                <span>View Campaigns</span><br>
                <i class="fa-solid fa-campground"></i>
                <p> Latest Campaigns are Published Here
                <p>
            </div>
        </div>
    </section>

    <script src="./js/welcomefinal.js"></script>
</body>

</html>
