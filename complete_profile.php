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
$sql = "SELECT name, username, email, profile_img, gender, bloodgroup, dob FROM users WHERE id = '$id'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $name = $row['name'];
    $username = $row['username'];
    $email = $row['email'];
    $profile_img = $row['profile_img'];
    $gender = $row['gender'];
    $bloodgroup = $row['bloodgroup'];
    $dob = $row['dob'];

    // Check if the profile is already complete
    if (!empty($gender) && !empty($bloodgroup) && !empty($dob)) {
        header("Location: welcome.php");
        exit();
    }
} else {
    // Handle the case where the user does not exist in the database
    // Redirect or show an error message
    header("Location: login.php");
    exit();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_SESSION['userID'];
    $gender = $_POST['gender'];
    $bloodgroup = $_POST['bloodgroup'];
    $dob = $_POST['dob'];

    // Validate required fields
    if (empty($gender) || empty($bloodgroup) || empty($dob)) {
        echo "<script>alert('Please complete all fields.');</script>";
    } else {
        // Update the users table with the new data
        $sql = "UPDATE users SET gender = ?, bloodgroup = ?, dob = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $gender, $bloodgroup, $dob, $id);

        if ($stmt->execute()) {
            // Redirect to welcome page after successful update
            header("Location: welcome.php");
            exit();
        } else {
            // Handle error during update
            echo "Error updating profile: " . $conn->error;
        }

        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complete Your Profile</title>
    <link rel="stylesheet" href="./css/complete-profile.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>

<body>
    <div class="popup-content">
        <span class="close-btn" id="closeBtn" onclick="validateForm()">&times;</span>

        <h2>Complete Your Profile</h2>

        <form method="post">
            <div class="photos">
                <div class="circleprofilediv">
                    <img src="./images/userprofilepic/<?php echo $profile_img; ?>" id="profile-pic"
                                    alt="Profile Picture">
                </div>
            </div>
            <div class="names">
                <label for="name"><b>Name:</b></label>
                <input type="text" id="name" name="name" value="<?php echo $name; ?>" readonly class="input-field">
            </div>
            <div class="names">
                <label for="username"><b>Username:</b></label>
                <input type="text" id="username" name="username" value="<?php echo $username; ?>" readonly class="input-field">
            </div>
            <div class="names">
                <label for="email"><b>Email:</b></label>
                <input type="email" id="email" name="email" value="<?php echo $email; ?>" readonly class="input-field">
            </div>
            <div class="horizontal-row">
                <div class="names" style="width: 48%;">
                    <label for="gender"><b>Gender</b></label>
                    <select id="gender" name="gender" required class="select-field">
                        <option value="" disabled selected>Select your gender</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                    </select>
                </div>
                <div class="names" style="width: 48%;">
                    <label for="bloodgroup"><b>Blood Group:</b></label>
                    <select id="bloodgroup" name="bloodgroup" required class="select-field">
                        <option value="" disabled selected>Select your blood group</option>
                        <option value="A+">A+</option>
                        <option value="A-">A-</option>
                        <option value="B+">B+</option>
                        <option value="B-">B-</option>
                        <option value="AB+">AB+</option>
                        <option value="AB-">AB-</option>
                        <option value="O+">O+</option>
                        <option value="O-">O-</option>
                    </select>
                </div>
            </div>
            <div class="names">
                <label for="dob"><b>Date of Birth:</b></label>
                <input type="date" id="dob" name="dob" required class="input-field">
            </div>
            <button id="bb" type="submit">Update Profile</button>
        </form>
    </div>
    <script src="./js/complete-profile.js"></script>
</body>

</html>
