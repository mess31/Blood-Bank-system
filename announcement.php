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
$sql = "SELECT name FROM users WHERE id = '$id'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  $row = $result->fetch_assoc();
  $name = $row['name'];
}
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="UTF-8">
    <title> newdashboard</title>
    <link rel="stylesheet" href="css/announcement.css">
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
        <div class="welcome-text"><b id="text1"><?php echo $name; ?></b>
            <div class="demoimage">
                <img src="abc.png" id="car">
            </div>
        </div>
        <i id="dropdownIcon" class="fas fa-caret-down dropdown-icon" onclick="toggleDropdown()"></i>
        <div class="dropdown-menu" id="dropdownMenu">
            <div class="dropdown-content">
                <a href="settings.php">Settings</a>
                <a href="profile.php">Profile</a>
                <a href="welcome.php">Dashboard</a>
                <a href="logout.php">Logout</a>
            </div>
        </div>
    </div>
</div>
    <hr>
    <div class="sidebar open" id="sidebar">
    <div class="logo-details">
        <div class="logo_name">BloodLink</div>
    </div>
    <ul class="nav-list">
        <li class="active" id="active"> 
                <a href="<?php echo isset($_SESSION['userID']) ? 'finalwelcome.php' : 'login.php'; ?>">
                <i class="fa-solid fa-gauge" ></i>
                <span class="links_name" >Dashboard</span>
            </a>
            <span class="tooltip" >Dashboard</span>
        </li>
        <li>
            <a href="<?php echo isset($_SESSION['userID']) ? 'finaldonate_blood.php' : 'login.php'; ?>">
                <i class="fa-solid fa-hand-holding-medical"></i>
                <span class="links_name">Donate Blood</span>
            </a>
            <span class="tooltip">Donate Blood</span>
        </li>
        <li>
            <a href="<?php echo isset($_SESSION['userID']) ? 'finaldonation_history.php' : 'login.php'; ?>">
                <i class="fa-solid fa-clock-rotate-left"></i>
                <span class="links_name">Donation History</span>
            </a>
            <span class="tooltip">Donation History</span>
        </li>
        <li>
            <a href="<?php echo isset($_SESSION['userID']) ? 'finalrequest_blood.php' : 'login.php'; ?>">
                <i class="fa-solid fa-code-pull-request"></i>
                <span class="links_name">Initiate Blood Request</span>
            </a>
            <span class="tooltip">Blood Request</span>
        </li>
        <li>
            <a href="<?php echo isset($_SESSION['userID']) ? 'finalrequest_history.php' : 'login.php'; ?>">
                <i class="fa-solid fa-hand"></i>
                <span class="links_name">Request History</span>
            </a>
            <span class="tooltip">Request History</span>
        </li>
        <li>
                <a href="<?php echo isset($_SESSION['userID']) ? 'announcement.php' : 'login.php'; ?>">
                
                <i class="fa-solid fa-tents" style="color:black"></i>
                <span class="links_name" style="color:black">Announcements</span>
            </a>
            <span class="tooltip">Announcements</span>
        </li>
    </ul>
</div>


  <section class="home-section">
    <div class="text">ONGOING CAMPAIGN</div>
    <div class="campaignboxxes">

        <div class="rectangular-div">
                <div class="product-image"><img src="./images/userprofilepic/Drive.svg"></div>
                     <div class="product-details">
                        <div class="product-name">Ladki</div>
            <div class="product-description"> A good english speaking girl who is fond of travelling around the world</div>
            
            <div class="product-reviews">
                <div class="star-rating" data-rating="0">
                    <span class="star" data-value="1">&#9733;</span>
                    <span class="star" data-value="2">&#9733;</span>
                    <span class="star" data-value="3">&#9733;</span>
                    <span class="star" data-value="4">&#9733;</span>
                    <span class="star" data-value="5">&#9733;</span>
                </div><br>               
            </div>
            <div class="review-stats">
                 <span class="total-reviews">0</span> ( <span class="average-rating">0</span> )
            </div>
            <div class="see-more">See More</div>
        </div>
        </div>
        <div class="rectangular-div">
                <div class="product-image"><img src="./images/userprofilepic/GitHub.svg"></div>
                     <div class="product-details">
                        <div class="product-name">Car</div>
            <div class="product-description">A superfast car that is supersonic and can travel faster than the speed of sound</div>
            <div class="see-more">See More</div>

            <div class="product-reviews">
                <div class="star-rating" data-rating="0">
                    <span class="star" data-value="1">&#9733;</span>
                    <span class="star" data-value="2">&#9733;</span>
                    <span class="star" data-value="3">&#9733;</span>
                    <span class="star" data-value="4">&#9733;</span>
                    <span class="star" data-value="5">&#9733;</span>
                </div>
            </div>
        </div>
        </div>
        <div class="rectangular-div">
                <div class="product-image"><img src="./images/userprofilepic/LinkedIn.svg"></div>
                     <div class="product-details">
                        <div class="product-name">Product Name</div>
            <div class="product-description">Product Description</div>
            <div class="product-reviews">
                <div class="star-rating" data-rating="0">
                    <span class="star" data-value="1">&#9733;</span>
                    <span class="star" data-value="2">&#9733;</span>
                    <span class="star" data-value="3">&#9733;</span>
                    <span class="star" data-value="4">&#9733;</span>
                    <span class="star" data-value="5">&#9733;</span>
                </div>
            </div>
        </div>
        </div>
        <div class="rectangular-div">
                <div class="product-image"><img src="./images/userprofilepic/Drive.svg"></div>
                     <div class="product-details">
                        <div class="product-name">Product Name</div>
            <div class="product-description">Product Description</div>
            <div class="product-reviews">
                <div class="star-rating" data-rating="0">
                    <span class="star" data-value="1">&#9733;</span>
                    <span class="star" data-value="2">&#9733;</span>
                    <span class="star" data-value="3">&#9733;</span>
                    <span class="star" data-value="4">&#9733;</span>
                    <span class="star" data-value="5">&#9733;</span>
                </div>
            </div>
        </div>
        </div>


        

       
        
    </div>
    <div class="text">PAST CAMPAIGNS</div>
    <div class="campaignboxxes">
        

        <div class="rectangular-div">
                <div class="product-image"><img src="ladki.avif"></div>
                     <div class="product-details">
                        <div class="product-name">Ladki</div>
            <div class="product-description"> A good english speaking girl who is fond of travelling around the world</div>
            
            <div class="product-reviews">
                <div class="star-rating" data-rating="0">
                    <span class="star" data-value="1">&#9733;</span>
                    <span class="star" data-value="2">&#9733;</span>
                    <span class="star" data-value="3">&#9733;</span>
                    <span class="star" data-value="4">&#9733;</span>
                    <span class="star" data-value="5">&#9733;</span>
                </div>
            </div>
            <div class="see-more">See More</div>
        </div>
        </div>
        <div class="rectangular-div">
                <div class="product-image"><img src="../images/car.jpg"></div>
                     <div class="product-details">
                        <div class="product-name">Car</div>
            <div class="product-description">A superfast car that is supersonic and can travel faster than the speed of sound</div>
            <div class="see-more">See More</div>

            <div class="product-reviews">
                <div class="star-rating" data-rating="0">
                    <span class="star" data-value="1">&#9733;</span>
                    <span class="star" data-value="2">&#9733;</span>
                    <span class="star" data-value="3">&#9733;</span>
                    <span class="star" data-value="4">&#9733;</span>
                    <span class="star" data-value="5">&#9733;</span>
                </div>
            </div>
        </div>
        </div>
        <div class="rectangular-div">
                <div class="product-image"></div>
                     <div class="product-details">
                        <div class="product-name">Product Name</div>
            <div class="product-description">Product Description</div>
            <div class="product-reviews">
                <div class="star-rating" data-rating="0">
                    <span class="star" data-value="1">&#9733;</span>
                    <span class="star" data-value="2">&#9733;</span>
                    <span class="star" data-value="3">&#9733;</span>
                    <span class="star" data-value="4">&#9733;</span>
                    <span class="star" data-value="5">&#9733;</span>
                </div>
            </div>
        </div>
        </div>
        <div class="rectangular-div">
                <div class="product-image"></div>
                     <div class="product-details">
                        <div class="product-name">Product Name</div>
            <div class="product-description">Product Description</div>
            <div class="product-reviews">
                <div class="star-rating" data-rating="0">
                    <span class="star" data-value="1">&#9733;</span>
                    <span class="star" data-value="2">&#9733;</span>
                    <span class="star" data-value="3">&#9733;</span>
                    <span class="star" data-value="4">&#9733;</span>
                    <span class="star" data-value="5">&#9733;</span>
                </div>
            </div>
        </div>
        </div>


        

       
        
    </div>
  </section>
  
  <script>
    let totalRating = 0;
        let totalReviewers = 0;

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

                totalRating += rating;
                totalReviewers++;

                updateReviewStats();
            });
        });

        function updateReviewStats() {
            const totalReviewsElement = document.querySelector('.total-reviews');
            const averageRatingElement = document.querySelector('.average-rating');

            totalReviewsElement.textContent = totalReviewers;

            const averageRating = totalReviewers > 0 ? (totalRating / totalReviewers).toFixed(1) : 0;

            averageRatingElement.textContent = averageRating;
        }
    </script>
    
</body>
</html>
