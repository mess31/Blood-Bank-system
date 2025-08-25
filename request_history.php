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
    <title>Request History</title>
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

    <section class="requestHistory">
        <h2 align="center">Request History</h2>
        <br>

        <?php
        // Get the userID from the session
        $userID = $_SESSION['userID'];
        
        // Fetch requests history for the user
        $sql = "SELECT b.rqst_id, b.req_bloodGrp, b.bloodQty, b.requiredFor, b.contactNo, b.address, b.note, b.rqstDate, b.rqst_status, b.user_id
                FROM requests b
                INNER JOIN users u ON b.user_id = u.id
                WHERE u.id = $userID";

        $result = $conn->query($sql);
        
        if ($result->num_rows > 0) {
            echo "<table>
                    <tr>
                        <th>S.No.</th>
                        <th>Required Blood</th>
                        <th>Blood Quantity</th>
                        <th>Required For</th>
                        <th>Contact Number</th>
                        <th>Address</th>
                        <th>Note</th>
                        <th>Request Date</th>
                        <th>Status</th>                    
                        <th>Action</th>                    
                    </tr>";

                // Initialize counter
                $serialNumber = 1;
                
            // Output data of each row
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>".$serialNumber."</td>
                        <td>" . $row["req_bloodGrp"] . "</td>
                        <td>" . $row["bloodQty"] . "</td>
                        <td>" . $row["requiredFor"] . "</td>
                        <td>" . $row["contactNo"] . "</td>
                        <td>" . $row["address"] . "</td>
                        <td>" . $row["note"] . "</td>
                        <td>" . $row["rqstDate"] . "</td>
                    
                        <td id='status'>";
                            // Check if status data exists and display appropriate content
                            if (isset($row['rqst_status']) && $row['rqst_status'] === 'Pending') {
                                echo '<div id="pending">Pending</div>'; // Display Present if status is 'p'
                            } elseif($row['rqst_status'] === 'Approved') {
                                echo '<div id="approved">Approved</div>'; // Display Absent otherwise
                            } else {
                                echo '<div id="rejected">Rejected</div>'; // Display Absent otherwise
                            }
                        echo "</td>

                        <td>";                    
                            // Dynamically change the action button text based on request status
                            if ($row['rqst_status'] === 'Pending') {
                            } else {
                                echo "<a href='view_requestdetails.php?user_id=" . $row['user_id'] . "&rqst_id=" . $row['rqst_id'] . " 'style='text-decoration: none; border: none; background: none; font-size: 16px; cursor: pointer'><i class='fa-solid fa-circle-info'></i> View Details</a>&nbsp";
                            }                            
                        echo "</td>
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

    <script src="./js/welcome.js"></script>
</body>

</html>
