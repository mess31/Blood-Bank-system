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

// Check if review_id is provided in the URL
if(isset($_GET['review_id'])) {

    // Get review_id from the URL
    $review_id = $_GET['review_id'];

    // Prepare and execute SQL statement to delete the review
    $stmt = $conn->prepare("DELETE FROM reviews WHERE review_id = ?");
    $stmt->bind_param("i", $review_id);
    
    if ($stmt->execute()) {
        // Deletion successful
        header("Location: view_reviews.php");
        exit();
    } else {
        // Error occurred
        echo "Error deleting review: " . $conn->error;
    }
    

    // Close prepared statement and database connection
    $stmt->close();
    $conn->close();
} else {
    // If review_id is not provided in the URL
    echo "Review ID not provided.";
}
?>
