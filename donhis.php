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

// Initialize variables to avoid undefined variable warnings
$bloodType = $bloodqty = $weight = $contactNumber = $address = $disease = "";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donation History</title>
    <link rel="stylesheet" href="./css/uwelcome.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
    .sectionBody{
      display: none;
    position: absolute;
    top:35%;
    left: 30%;
    transform: translate(-50%, -50%);
    z-index: 9999; 
    background-color: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.3);
    }
    .popup-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5); /* Semi-transparent dark background */
    z-index: 9998; /* Ensure the overlay is below the popup but above other content */
    display: none;
}
.popup-container {
    position: relative;
}

.closeButton {
    position: absolute;
    right: 10px;
    cursor: pointer;
    font-size:25px;
}

    </style>
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
                    <a href="finalwelcome.php">Dashboard</a>
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

    <section class="donationHistory">
        <h2 align="center">Donation History</h2>
        <br>
        <?php
        
        // Get the userID from the session
        $userID = $_SESSION['userID'];
        
        // Fetch donation history for the user
        $sql = "SELECT d.donor_id, u.name, u.email, u.bloodgroup, d.bloodqty, d.weight, d.contact_no, d.address, d.disease, d.status, d.user_id
                FROM donors d
                INNER JOIN users u ON d.user_id = u.id
                WHERE u.id = $userID";
        
        $result = $conn->query($sql);
        
        if ($result->num_rows > 0) {
            echo "<table>
                    <tr>
                        <th>S.No.</th>
                        <th>Blood Group</th>
                        <th>Blood Quantity</th>
                        <th>Weight</th>
                        <th>Contact Number</th>
                        <th>Address</th>
                        <th>Disease</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>";

            // Initialize counter
            $serialNumber = 1;

            // Output data of each row
            while($row = $result->fetch_assoc()) {
                echo "<tr style='margin-bottom: 10px;'>
                        <td>".$serialNumber."</td>
                        <td>".$row["bloodgroup"]."</td>
                        <td>".$row["bloodqty"]."</td>
                        <td>".$row["weight"]."</td>
                        <td>".$row["contact_no"]."</td>
                        <td>".$row["address"]."</td>
                        <td>".$row["disease"]."</td>

                        <td id='status'>";
                            // Check if status data exists and display appropriate content
                            if (isset($row['status']) && $row['status'] === 'Pending') {
                                echo '<div id="pending">Pending</div>';
                            } elseif($row['status'] === 'Approved') {
                                echo '<div id="approved">Approved</div>';
                            } else {
                                echo '<div id="rejected">Rejected</div>';
                            }
                        echo "</td>

                        <td>";                    
                            // Dynamically change the action button text based on request status
                            if ($row['status'] === 'Pending') {
                                echo "<button onclick='showPopup()' style='text-decoration: none; border: none; background: none; font-size: 16px; cursor: pointer'><i class='fa-solid fa-pen-to-square'></i> Edit</button>";
                                // echo "<a href='view_donordetails.php?user_id=" . $row['user_id'] . "&donor_id=" . $row['donor_id'] . " 'style='text-decoration: none; border: none; background: none; font-size: 16px; cursor: pointer'><i class='fa-solid fa-circle-info'></i> Edit</a>&nbsp";
                            } else {
                                echo "<a href='view_donordetails.php?user_id=" . $row['user_id'] . "&donor_id=" . $row['donor_id'] . " 'style='text-decoration: none; border: none; background: none; font-size: 16px; cursor: pointer'><i class='fa-solid fa-circle-info'></i> View Details</a>&nbsp";
                                // echo "<button style='text-decoration: none; border: none; background: none; font-size: 16px; cursor: pointer'><i class='fa-solid fa-circle-info'></i> View Details</button>";
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

    </section>
<section class="sectionBody">
<div class="popup-container">
<span class="closeButton" onclick="closePopup()"><i class="fa-solid fa-times"></i></span>
    <h2>Donate Blood</h2>
    <!-- Display errors -->
    <?php if (!empty($errors)): ?>
        <div class="error">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?php echo $error; ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    <form id="donateForm" method="POST">
        <label for="bloodgroup">Blood Group:</label>
        <select id="bloodgroup" name="bloodgroup">
            <option value="">Select Blood Group</option>
            <option value="A+" <?php if ($bloodType=='A+' ) echo 'selected="selected"' ; ?>>A+</option>
            <option value="A-" <?php if ($bloodType=='A-' ) echo 'selected="selected"' ; ?>>A-</option>
            <option value="B+" <?php if ($bloodType=='B+' ) echo 'selected="selected"' ; ?>>B+</option>
            <option value="B-" <?php if ($bloodType=='B-' ) echo 'selected="selected"' ; ?>>B-</option>
            <option value="AB+" <?php if ($bloodType=='AB+' ) echo 'selected="selected"' ; ?>>AB+</option>
            <option value="AB-" <?php if ($bloodType=='AB-' ) echo 'selected="selected"' ; ?>>AB-</option>
            <option value="O+" <?php if ($bloodType=='O+' ) echo 'selected="selected"' ; ?>>O+</option>
            <option value="O-" <?php if ($bloodType=='O-' ) echo 'selected="selected"' ; ?>>O-</option>
        </select><br>

        <label for="bloodqty">Units (in ml):</label>
        <input type="number" id="bloodqty" name="bloodqty" value="<?php echo $bloodqty; ?>"><br>

        <label for="weight">Weight (in kg):</label>
        <input type="number" id="weight" name="weight" value="<?php echo $weight; ?>"><br>

        <label for="contact">Contact Number:</label>
        <input type="number" id="contact" name="contact" value="<?php echo $contactNumber; ?>"><br>

        <label for="address">Address:</label>
        <input type="text" id="address" name="address" value="<?php echo $address; ?>"><br>

        <label for="disease">Disease:</label>
        <input type="text" id="disease" name="disease" placeholder="Please mention the name of disease, if any" value="<?php echo $disease; ?>"><br>

        <input type="submit" value="Submit">
    </form>
    </div>
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
    <script src="./js/welcome.js"></script>
</body>

</html>
