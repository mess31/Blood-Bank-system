<?php
session_start();

// Check if user is logged in and has access to dashboard
if (!isset($_SESSION['userID'])) {
    header("Location: login.php");
    exit();
}

// Database connection
$db_host = "localhost";
$db_user = "root";
$db_password = "";
$db_name = "bloodlink";

$conn = new mysqli($db_host, $db_user, $db_password, $db_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_SESSION['userID'];

    // Function to sanitize input
    function test_input($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    // Validate and sanitize name
    if (empty($_POST["name"])) {
        $nameErr = "Name is required";
    } else {
        $name = test_input($_POST["name"]);
        if (!preg_match("/^[a-zA-Z\s]*$/", $name)) {
            $nameErr = "Only letters and spaces allowed";
        }
    }

    // Validate and sanitize username
    if (empty($_POST["username"])) {
        $usernameErr = "Username is required";
    } else {
        $username = test_input($_POST["username"]);
        if (!preg_match("/^[a-z0-9_.]{5,30}$/", $username)) {
            $usernameErr = "Username can only use lower-case letters(a-z), numbers(0-9), underscores(_) and periods(.)";
        } 
    }

    // Validate date of birth
    $dob = test_input($_POST["dob"]);
    if (empty($dob)) {
        $dobErr = "Date of birth is required";
    } elseif (!strtotime($dob)) {
        $dobErr = "Invalid date format";
    }

    // Validate profile picture upload
    if (!empty($_FILES['profile_picture']['name'])) {
        $target_dir = "images/userprofilepic/";
        $file_name = basename($_FILES["profile_picture"]["name"]);
        $target_file = $target_dir . $file_name;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Validate file type and size
        $allowed_types = array('jpg', 'png', 'gif', 'jpeg', 'svg');
        if (!in_array($imageFileType, $allowed_types)) {
            $imageErr = "Invalid file format. Allowed formats: jpg, png, gif, jpeg, svg";
        } elseif ($_FILES["profile_picture"]["size"] > 2000000) {
            $imageErr = "File size exceeded. Maximum allowed size is 2MB";
        }
    }

    // If all fields are valid, proceed with update
    if (empty($nameErr) && empty($usernameErr) && empty($dobErr) && empty($imageErr)) {
        // If image is uploaded, move it to target directory
        if (!empty($_FILES['profile_picture']['name'])) {
            if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file)) {
                // Update profile picture in the database
                $sql = "UPDATE users SET profile_img = '$file_name' WHERE id = '$id'";
                mysqli_query($conn, $sql);
            } else {
                $imageErr = "Sorry, there was an error uploading your file.";
            }
        }

        // Update profile information
        $name = mysqli_real_escape_string($conn, $name);
        $username = mysqli_real_escape_string($conn, $username);
        $dob = mysqli_real_escape_string($conn, $dob);

        $sql = "UPDATE users SET name = '$name', username = '$username', dob = '$dob' WHERE id = '$id'";
        if (mysqli_query($conn, $sql)) {
            echo "Profile updated successfully";
        } else {
            echo "Error updating profile: " . mysqli_error($conn);
        }
    }
}

// Fetch user details from the database
// $username = $_SESSION['username'];
// $sql = "SELECT name, username, email FROM user WHERE username = '$username'";
// $result = $conn->query($sql);

// if ($result->num_rows > 0) {
//     $row = $result->fetch_assoc();
//     $name = $row['name'];
//     $username = $row['username'];
//     $email = $row['email']; 
// }

$id = $_SESSION['userID'];
$sql = "SELECT name, username, email, dob, gender, bloodgroup, profile_img, creation_date FROM users WHERE id = '$id'";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $name = $row['name'];
    $username = $row['username'];
    $email = $row['email'];
    $profile_img = $row['profile_img'];
    $dob = $row['dob'];
    $gender = $row['gender'];
    $bloodgroup = $row['bloodgroup'];
    $creation_date = date('F d, Y', strtotime($row['creation_date']));

    // Check if any of the required fields are empty
    if (empty($dob) || empty($gender) || empty($bloodgroup)) {
        // Redirect to complete_profile.php if any of the fields are empty
        header("Location: cp.php");
        exit();
    }
} else {
    // Redirect to login page if user data not found
    header("Location: login.php");
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
    
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Dashboard</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
        <link rel="stylesheet" href="css/usettings.css">
        
    </head>
    
    <body>
    <div class="header">
        <div class="logo" ></div>
        <!-- <div class="search-container">
            <input type="text" class="search-input" placeholder="Search...">
            <i class="fas fa-search search-icon"></i>
        </div> -->
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

</div>
        
            
            <div class="containner">
                <div class="top-a">
            <h1>Settings</h1>

        </div>
        <div class="mainBox">
            <div class="mainBox-top">

            <i class="fa-solid fa-circle-user"></i>
                <h3 style="display: inline-block; vertical-align: middle;">Account</h3>
                <br>
                <p>Manage your Account and Personal Information</p>
                <br>
                <hr>
            </div>
            <div class="mainBox-down">
                <!-- Account Details -->
                <div class="name">
                    <p><b>Name</b></p>
                    <p>
                        <?php echo $name; ?>
                    </p>
                </div>
                <div class="name">
                    <p><b>Username</b></p>
                    <p>
                        <?php echo $username; ?>
                    </p>
                </div>
                <div class="name">
                    <p><b>Email</b></p>
                    <p>
                        <?php echo $email; ?> <i class="fa-solid fa-lock"></i>
                    </p>
                </div>

                <div class="name">
                    <p><b>Gender</b></p>
                    <p>
                        <?php echo $gender; ?>
                    </p>
                </div>

                <div class="name">
                    <p><b>Bloodgroup</b></p>
                    <p>
                        <?php echo $bloodgroup; ?>
                    </p>
                </div>

                <div class="button">
                    <button id="editProfileBtn" style="cursor:pointer">Edit Profile</button>
                </div>

                <div class="popup" id="editProfilePopup">
                    <div class="popup-content">
                        <span class="close-btn" id="closeBtn">&times;</span>

                        <h2 style="position:relative;bottom:8px">Edit Profile</h2>


                        <form method="post" enctype="multipart/form-data">
                            <div class="circleprofilediv">
                                <img src="./images/userprofilepic/<?php echo $profile_img; ?>" id="profile-pic"
                                    alt="Profile Picture">
                            </div>
                            <div class="cameraicon" style="cursor: pointer;" onclick="uploadPicture()">
                                <i class="fa-solid fa-camera"></i>
                            </div>
                            <input type="file" id="file-input" name="profile_picture" style="display: none;"
                                accept="image/*">
                            <?php if (!empty($imageErr)) echo "<span class='error'>$imageErr</span>"; ?>

                            <label for="name"><b>Name:</b></label>
                            <input type="text" id="name" name="name" value="<?php echo $name; ?>" required
                                style="font-size: 20px;">
                            <?php if (!empty($nameErr)) echo "<span class='error'>$nameErr</span>"; ?>

                            <label for="username"><b>Username:</b></label>
                            <input type="text" id="username" name="username" value="<?php echo $username; ?>" required
                                style="font-size: 20px;">
                            <?php if (!empty($usernameErr)) echo "<span class='error'>$usernameErr</span>"; ?>

                            <label for="dob" style="margin-top:14px;"><b>Date of Birth:</b></label>
                            <input type="date" id="dob" name="dob" value="<?php echo $dob; ?>" required
                                style="font-size: 20px;">
                            <?php if (!empty($dobErr)) echo "<span class='error'>$dobErr</span>"; ?>

                            <button type="submit">Update Changes</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="mainBox">
            <div class="mainBox-top">
            <i class="fa-regular fa-bell"></i>
                <h3 style="display: inline-block; vertical-align: middle;">Account Creation Date</h3>
                <br>
                <div class="mainbox-content" style="display:flex;justify-content: space-between;" !important>
                    <span>You created your Bloodlink Network account on</span>
                    <span><b><?php echo $creation_date; ?></b></span>
                </div>
                <br>
                <hr>
            </div>
            <div class="secondBox">
            <i class="fa-regular fa-keyboard additional-class" style="position: relative; bottom: 5px;"></i>

                <h3 style="display: inline-block; vertical-align: middle;">
                    <span>Change Password</span><br>
                </h3>
                <div class="secondBox-content">
             <p>Change Current Password</p>
             <button id="changePasswordBtn" class="btn dropdown-btn" style="cursor:pointer; position:relative; bottom:12px;">Change Password</button>
              </div>

              <div id="confirmationDialog" class="confirmation-dialog">
             <div class="confirmation-dialog-content">

               <p><b>Are you sure you want to change your password?</b></p>
               <button id="yesBtn">Yes</button>
                 <button id="noBtn">No</button>
                </div>
                 </div>

                

            </div>
            <hr>


            <div class="secondBox">
            <i class="fa-regular fa-trash-can"></i>
    <h3 style="display: inline-block; vertical-align: middle; margin-top: 18px;"><span>Delete Account</span><br></h3>
    <div class="secondBox-content">
        <p>Deleting your account will permanently remove your data from the database</p>
        <button id="deleteBtn" class="btn dropdown-btn" style="position: relative; bottom: 12px; cursor: pointer;">Delete</button>
    </div>
    <div id="deleteConfirmationDialog" class="confirmation-dialog" style="display: none;">
        <div class="confirmation-dialog-content">
            <p><b>Are you sure you want to delete your account?</b></p>
            <button id="deleteYesBtn">Yes</button>
            <button id="deleteNoBtn">No</button>
        </div>
    </div>
</div>

        </div>

        <div class="popup" id="changePasswordPopup">
                    <div class="popup-content">
                        <span class="close-btn" id="closeBtn">&times;</span>
                        <h2> Delete Account</h2>
                        <form>                          
                            <label for="confirmPassword"><b>Confirm Password</b></label>
                            <input type="password" id="confirmPassword" name="confirmPassword" required
                                style="font-size: 20px;">

                            <button id="bb" type="submit" style="diplay:relative;left:2px;">Delete Account</button>
                        </form>
                    </div>
                </div>

    </div>

    <script src="js/settingsDraft.js"></script>
</body>

</html>
