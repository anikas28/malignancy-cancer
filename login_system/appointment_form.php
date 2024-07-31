<?php
// Include the config file for database connection
@include 'config.php';

// Start session
session_start();

// Initialize an array to store errors and a success message
$errors = [];
$success_message = '';
$booking_successful = false; // Flag to check if booking is successful

// Check if the form is submitted
if (isset($_POST['submit'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $doctor = mysqli_real_escape_string($conn, $_POST['doctor']);
    $date = mysqli_real_escape_string($conn, $_POST['date']);

    // Validate the name, phone number, and date
    if (empty($name)) {
        $errors[] = 'Please enter your name!';
    }
    if (!preg_match('/^[0-9]{10}$/', $phone)) {
        $errors[] = 'Please enter a valid 10-digit contact number!';
    }
    if (empty($date)) {
        $errors[] = 'Please select a date!';
    } else {
        $formatted_date = date('Y-m-d', strtotime($date));
    }

    // If no errors, prepare a confirmation message
    if (empty($errors)) {
        $confirm_message = "Are you sure you want to book an appointment with $doctor on $formatted_date?";
    }
}

// If confirmation is accepted, proceed to insert into database
if (isset($_POST['confirm'])) {
    if ($_POST['confirm'] == 'yes') {
        // Retrieve stored values from hidden inputs
        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $phone = mysqli_real_escape_string($conn, $_POST['phone']);
        $doctor = mysqli_real_escape_string($conn, $_POST['doctor']);
        $formatted_date = mysqli_real_escape_string($conn, $_POST['formatted_date']);

        // Insert into database
        $insert_query = "INSERT INTO appointments (name, phone, doctor, date, status) 
                         VALUES ('$name', '$phone', '$doctor', '$formatted_date', 'pending')";

        if ($conn->query($insert_query) === TRUE) {
            $success_message = 'Appointment booked successfully. You will receive the details soon.';
            $booking_successful = true; // Set the flag to true on successful booking
        } else {
            $errors[] = 'Failed to book appointment. Please try again!';
            echo "Error: " . $insert_query . "<br>" . $conn->error; // For debugging
        }
    } else {
        $errors[] = 'Booking canceled. No changes made.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Malignancy-Cancer Prediction App</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="css/style2.css">
    <style>
    /* CSS styles for confirmation buttons and other elements */
    .confirm-btn,
    .cancel-btn {
        background-color: #741111;
        color: white;
        border: none;
        padding: 8px 16px; /* Adjust padding for a more compact size */
        cursor: pointer;
        border-radius: 5px;
        margin-right: 5px; /* Adjust margin as needed */
        display: inline-block;
    }

    .confirm-btn:hover,
    .cancel-btn:hover {
        background-color: #a53131;
    }

    .button-container {
        text-align: center;
        margin-top: 10px;
    }

    /* Additional styles for messages and form */
    .error-msg {
        color: #ff0000;
        margin-bottom: 10px;
        display: block;
    }

    .success-msg {
        color: #008000;
        margin-bottom: 20px;
        display: block;
        font-size: 1.5rem; /* Adjust font size to make it bigger */
    }

    .confirm-msg {
        background-color: #f2f2f2;
        padding: 10px;
        border: 1px solid #ccc;
        margin-bottom: 20px;
        font-size: 1.3rem
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
        <a href="#footer">About Us</a>
    </nav>
    <div id="menu-btn" class="fas fa-bars"></div>
</header>

<div class="form-container">
    <form action="" method="post">
        <h1>Book an Appointment</h1>
        <?php
        // Display success or error messages
        if (!empty($errors)) {
            foreach ($errors as $error) {
                echo '<span class="error-msg">' . $error . '</span>';
            }
        }

        if (isset($confirm_message)) {
            echo '<div class="confirm-msg">' . $confirm_message . '</div>';
            echo '<div class="button-container">';
            echo '<button type="submit" name="confirm" value="yes" class="confirm-btn">Yes</button>';
            echo '<button type="submit" name="confirm" value="no" class="cancel-btn">No</button>';
            echo '</div>'; // Close button-container
            // Store values in hidden inputs
            echo '<input type="hidden" name="name" value="' . htmlspecialchars($name) . '">';
            echo '<input type="hidden" name="phone" value="' . htmlspecialchars($phone) . '">';
            echo '<input type="hidden" name="doctor" value="' . htmlspecialchars($doctor) . '">';
            echo '<input type="hidden" name="formatted_date" value="' . htmlspecialchars($formatted_date) . '">';
        }

        if ($success_message) {
            echo '<span class="success-msg">' . $success_message . '</span>';
            // Redirect to patient_page.php after 3 seconds
            echo '<script>
                    setTimeout(function() {
                        window.location.href = "patient_page.php";
                    }, 3000);
                  </script>';
        }
        ?>
        <?php if (!isset($confirm_message)) : ?>
            <input type="text" name="name" required placeholder="Enter your name">
            <input type="text" name="phone" required placeholder="Enter your phone number">
            <select name="doctor" required>
                <option value="" disabled selected>Select a Doctor</option>
                <option value="Doctor 1">Doctor 1</option>
                <option value="Doctor 2">Doctor 2</option>
                <option value="Doctor 3">Doctor 3</option>
            </select>
            <input type="date" name="date" required placeholder="Select a date">
            <input type="submit" name="submit" value="Book Now" class="form-btn">
        <?php endif; ?>
    </form>
</div>

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

</body>
</html>
