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
    // Checking if both 'user_id' and 'donor_id' parameters are set and are numeric values
    if (!isset($_GET["user_id"]) || !is_numeric($_GET["user_id"]) || !isset($_GET["donor_id"]) || !is_numeric($_GET["donor_id"])) {
        header("location: donation_history.php"); // Redirecting if any parameter is missing or not numeric
        exit;
    }
    $user_id = $_GET["user_id"]; // Assigning the 'user_id' value to a variable
    $donor_id = $_GET["donor_id"]; // Assigning the 'donor_id' value to a variable

    // SQL query modified to use both user_id and donor_id
    $sql = "SELECT u.profile_img, u.name, u.username, u.email, u.gender, u.bloodgroup, u.dob, d.bloodqty, d.weight, d.contact_no, d.address, d.disease, d.status
            FROM users u
            INNER JOIN donors d ON u.id = d.user_id
            WHERE u.id = ? AND d.donor_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $user_id, $donor_id); // Binding both parameters
    $stmt->execute();
    $result2 = $stmt->get_result();

    if ($result2->num_rows > 0) {
        $row = $result2->fetch_assoc();
        // Fetching user and donor details
        $profile_img = $row['profile_img'];
        $name = $row['name'];
        $username = $row['username'];
        $email = $row['email'];
        $gender = $row['gender'];
        $bloodgroup = $row['bloodgroup'];
        $dob = $row['dob'];
        $bloodqty = $row['bloodqty'];
        $weight = $row['weight'];
        $contact_no = $row['contact_no'];
        $address = $row['address'];
        $disease = $row['disease'];
        $status = $row['status'];
    } else {
        echo "No user found.";
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Initiate Request Blood</title>
    <link rel="stylesheet" href="./css/donors_info.css">
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

    <section class="home-section">
        <div class="text">Donor Details</div>
        <div class="user-details">

            <div class="text-column">

                <div class="first-column">
                    <table>
                        <tr>
                            <td>Name</td>
                            <td><?php echo htmlspecialchars($name); ?></td>
                        </tr>
                        <tr>
                            <td>Username</td>
                            <td><?php echo htmlspecialchars($username); ?></td>
                        </tr>
    
                        <tr>
                            <td>Email</td>
                            <td><?php echo htmlspecialchars($email); ?></td>
                        </tr>
    
                        <tr>
                            <td>Gender</td>
                            <td><?php echo htmlspecialchars($gender); ?></td>
                        </tr>
    
                        <tr>
                            <td>Blood Group</td>
                            <td><?php echo htmlspecialchars($bloodgroup); ?></td>
                        </tr>
    
                        <tr>
                            <td>Date of Birth</td>
                            <td><?php echo htmlspecialchars($dob); ?></td>
                        </tr>
                    </table>
                </div>
                <div class="second-column">
                    <table>

                        <tr>
                            <td>Blood Quantity</td>
                            <td><?php echo htmlspecialchars($bloodqty); ?> ml</td>
                        </tr>
    
                        <tr>
                            <td>Weight</td>
                            <td><?php echo htmlspecialchars($weight); ?> kg</td>
                        </tr>
    
                        <tr>
                            <td>Contact Number</td>
                            <td><?php echo htmlspecialchars($contact_no); ?></td>
                        </tr>
    
                        <tr>
                            <td>Address</td>
                            <td><?php echo htmlspecialchars($address); ?></td>
                        </tr>
    
                        <tr>
                            <td>Disease</td>
                            <td><?php echo htmlspecialchars($disease); ?></td>
                        </tr>
    
                        <!-- <tr>
                            <td>Status</td>
                            <td>< php echo htmlspecialchars($status); ?></td>
                        </tr> -->
    
                        <tr>
                            <td id="status">Status</td>
                            <td>
                                <?php if (htmlspecialchars($status) === 'Pending'): ?>
                                    <div id="pending">Pending</div>
                                <?php elseif(htmlspecialchars($status) === 'Approved'): ?>
                                    <div id="approved">Approved</div>
                                <?php else: ?>
                                    <div id="rejected">Rejected</div>
                                <?php endif; ?>
                            </td>
                        </tr>

                    </table>
                </div>
            </div>


            <div class="third-column">
                <div class="pic">
                    <img src="./images/userprofilepic/<?php echo $profile_img; ?>" id="profile-pic"
                                    alt="Profile Picture">
                </div>
                <a href="donation_history.php"><button class="cancel">Go Back</button></a>

            </div>
        </div>
    </section>
    <script src=".js/welcome.js"></script>


</body>

</html>
