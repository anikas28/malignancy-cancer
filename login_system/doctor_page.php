<?php
@include 'config.php';
session_start();

if (!isset($_SESSION['user_name']) || $_SESSION['user_type'] !== 'doctor') {
    header('location: prediction_doctor.php');
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="css/styles.css">

    <style>
        .btn-approve {
            background-color: #741111; /* Change button color */
            color: white; /* Text color */
            padding: 10px 20px;
            border: none;
            text-decoration: none;
            display: inline-block;
            margin: 10px;
            cursor: pointer;
            border-radius: 5px;
        }

        .btn-approve:hover {
            background-color: #d36666; /* Darker color on hover */
        }

        #searchContainer {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        #searchInput {
            display: none; /* Hide the search input by default */
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 5px 10px;
            margin-left: 5px;
        }

        #searchBtn {
            background-color: transparent;
            border: none;
            cursor: pointer;
            color: #741111;
            font-size: 1.2rem;
            padding: 5px;
            margin-left: 5px;
        }

        #searchBtn:hover {
            color: #a53131;
        }
    </style>
</head>
<body>
<header class="header">
    <a href="#" class="logo"><i class="fas fa-hospital"></i> Malignancy-Cancer Prediction</a>
    <nav class="navbar">
        <a href="http://127.0.0.1:5000/#home">Home</a>
        <a href="http://127.0.0.1:5000/#book">Predict Now</a>
        <a href="http://localhost/login_system/login_form.php">Log Out</a>
    </nav>
    <div id="menu-btn" class="fas fa-bars"></div>
        <div id="searchContainer">
            <button id="searchBtn"><i class="fas fa-search"></i></button>
            <input type="text" id="searchInput" placeholder="Search">
        </div>
    <div id="menu-btn" class="fas fa-bars"></div>
</header>

<section class="home" id="home">
    <div class="image">
        <img src="image/cancer.png" alt="Cancer Awareness">
    </div>
    <div class="content">
        <div class="container">
            <div class="welcome-message">
                <h3>Welcome <span style="color: #741111;"><?php echo "Dr. " . $_SESSION['user_name']; ?></span>!!</h3>
            </div>

            <div class="buttons-container">
                <a href="appointment_status.php" class="btn btn-approve">Appointments</a>
                <a href="http://127.0.0.1:5000/#book" class="btn btn-approve">Make Prediction</a>
                <a href="prediction_doctor.html" class="btn btn-approve">Records</a>

            

            </div>
        </div>
    </div>
</section>



<section class="footer" id="footer">
    <div class="box-container">
        <div class="box">
            <h3>Contact Info</h3>
            <a href="#"><i class="fas fa-phone"></i> +123 456 789</a>
            <a href="#"><i class="fas fa-envelope"></i> abcd@gmail.com</a>
        </div>
        <div class="box">
            <h3>Follow Us</h3>
            <a href="#"><i class="fab fa-facebook-f"></i> Facebook</a>
            <a href="#"><i class="fab fa-instagram"></i> Instagram</a>
        </div>
    </div>
    <div class="credit">Created by <span>Anika Asad</span> | All rights reserved</div>
</section>

<script>// Function to toggle the visibility of the search input field
        document.getElementById('searchBtn').addEventListener('click', function() {
            const searchInput = document.getElementById('searchInput');
            if (searchInput.style.display === 'none' || searchInput.style.display === '') {
                searchInput.style.display = 'block';
                searchInput.focus();
            } else {
                searchInput.style.display = 'none';
            }
        });

        // Function to filter doctors based on search input
        function filterDoctors(searchTerm) {
            const doctors = JSON.parse(localStorage.getItem('doctors')) || [];
            const filteredDoctors = doctors.filter(doctor => {
                return doctor.name.toLowerCase().includes(searchTerm.toLowerCase());
            });
            return filteredDoctors;
        }
</script>
</body>
</html>
