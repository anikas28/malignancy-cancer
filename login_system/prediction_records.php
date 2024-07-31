<?php
@include 'config.php';
session_start();

if (!isset($_SESSION['user_name']) || $_SESSION['user_type'] !== 'patient') {
    header('location:login_form.php');
    exit();
}

if (!isset($_SESSION['user_id'])) {
    echo "User ID is not set in the session.";
    exit();
}

$patient_id = $_SESSION['user_id'];
$result = mysqli_query($conn, "SELECT * FROM prediction_records WHERE patient_id = '$patient_id' ORDER BY created_at DESC");


if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prediction Records</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="css/styles.css">
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 1.5em;
            min-width: 400px;
            border: 1px solid #dddddd;
        }

        table thead tr {
            background-color: #741111;
            color: white;
            text-align: left;
            font-weight: bold;
        }

        table th, table td {
            padding: 12px 15px;
        }

        table tbody tr {
            border-bottom: 1px solid #dddddd;
        }

        table tbody tr:nth-of-type(even) {
            background-color: #f3f3f3;
        }

        table tbody tr:last-of-type {
            border-bottom: 2px solid #741111;
        }

        table tbody tr:hover {
            background-color: #f1f1f1;
            cursor: pointer;
        }

        h1 {
            text-align: center;
            margin-bottom: 1rem;
            color: black;
            font-size: 1.8rem;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
            width: 100%;
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
        }

        .header {
            padding: 2rem 9%;
            position: fixed;
            top: 0; left: 0; right: 0;
            z-index: 1000;
            box-shadow: 0 .5rem 1.5rem rgba(0, 0, 0, .1);
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #fff;
        }

        .header .logo {
            font-size: 2.5rem;
            color: var(--black);
        }

        .header .logo i {
            color: var(--green);
        }

        .header .navbar a {
            font-size: 1.7rem;
            color: var(--light-color);
            margin-left: 2rem;
        }

        .header .navbar a:hover {
            color: var(--green);
        }

        #searchContainer {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        #menu-btn {
            font-size: 2.5rem;
            border-radius: .5rem;
            background: #eee;
            color: var(--green);
            padding: 1rem 1.5rem;
            cursor: pointer;
            display: none;
        }

        #home {
            padding-top: 10rem;
            padding-bottom: 30rem;
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
        <a href="http://127.0.0.1:5000/#doctors">Doctors</a>
        <a href="http://localhost/login_system/login_form.php">Log Out</a>
    </nav>
    <div id="menu-btn" class="fas fa-bars"></div>
    <div id="searchContainer">
    <div id="searchContainer">
            <button id="searchBtn"><i class="fas fa-search"></i></button>
            <input type="text" id="searchInput" placeholder="Search">
        </div>
    </div>
</header>
<section class="home" id="home">
    <div class="container">
        <h1>Prediction Records</h1>
        <table>
            <thead>
                <tr>
                    <th>File</th>
                    <th>Prediction Result</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td><a href='" . $row['file_path'] . "' target='_blank'>" . basename($row['file_path']) . "</a></td>";
                    echo "<td>" . $row['prediction_result'] . "</td>";
                    echo "<td>" . $row['created_at'] . "</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
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
