<?php
// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "star";

// Create connection
$connection = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

$errors = array(); // Initialize an array to store errors
// Check if the form has been submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize inputs
    // $starCount = isset($_POST['star_count']) ? intval($_POST['star_count']) : 0;
    $starCount = $_POST['star_count'];
    $message = $_POST['message'];
    // $message = isset($_POST['message']) ? htmlspecialchars($_POST['message']) : '';

    // Check if any field is empty
    if (empty($starCount) || empty($message)) {
        $errors[] = "All fields are required.";
    } else {
    // Prepare and bind parameters
    $stmt = $connection->prepare("INSERT INTO review (star_count, message) VALUES (?, ?)");
    $stmt->bind_param("is", $starCount, $message);

}
    // Execute the statement
    if ($stmt->execute()) {
        echo "New record created successfully";
    } else {
        echo "Error: " . $stmt->error;
    }
    // Close statement
    $stmt->close();
}

// Close connection
$connection->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Review</title>
    <style>
        .star-rating {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .star {
            font-size: 35px;
            cursor: pointer;
        }

        .star.selected {
            color: gold; 
        }
    </style>
</head>
<body>
    <h1>Submit Review</h1>
    <form id="reviewForm" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <div class="star-rating">
            <span class="star" data-value="1">&#9733;</span>
            <span class="star" data-value="2">&#9733;</span>
            <span class="star" data-value="3">&#9733;</span>
            <span class="star" data-value="4">&#9733;</span>
            <span class="star" data-value="5">&#9733;</span>
        </div>
        <!-- Hidden input field for star_count -->
        <input type="hidden" name="star_count" id="star_count">
        <textarea name="message" placeholder="Write your review here"></textarea><br>
        <input type="submit" value="Submit Review">
    </form>

    <script>
    let totalRating = 0;

    document.querySelectorAll('.star').forEach(star => {
        star.addEventListener('click', event => {
            console.log('Star clicked!');
            const rating = parseInt(event.target.getAttribute('data-value'));
            const starRating = event.target.parentElement;
            starRating.setAttribute('data-rating', rating);
            starRating.querySelectorAll('.star').forEach(s => {
                s.classList.remove('selected');
                if (parseInt(s.getAttribute('data-value')) <= rating) {
                    s.classList.add('selected');
                }
            });

            totalRating = rating; // Update totalRating to the clicked star rating
            const selectedStars = starRating.querySelectorAll('.star.selected').length;
            document.getElementById('star_count').value = selectedStars; // Set the value of the hidden input field
        });
    });
    </script>

</body>
</html>
