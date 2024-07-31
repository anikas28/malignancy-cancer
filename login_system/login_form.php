<?php
@include 'config.php';
session_start();

if (isset($_POST['submit'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $pass = md5(mysqli_real_escape_string($conn, $_POST['password']));
    $user_type = mysqli_real_escape_string($conn, $_POST['user_type']);
    
    $select = "SELECT * FROM user_form WHERE email = '$email' AND password = '$pass' AND user_type = '$user_type'";
    $result = mysqli_query($conn, $select);

    if ($result) {
        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_array($result);
            $_SESSION['user_name'] = $row['name'];
            $_SESSION['user_type'] = $row['user_type'];
            $_SESSION['user_id'] = $row['id']; // Assuming there's an 'id' field in your 'user_form' table
            
            if ($row['user_type'] == 'admin') {
                header('location: admin_page.php');
            } elseif ($row['user_type'] == 'doctor') {
                header('location: doctor_page.php');
            } elseif ($row['user_type'] == 'patient') {
                header('location: patient_page.php');
            }
            exit();
        } else {
            $error[] = 'Incorrect email, password, or user type!';
        }
    } else {
        $error[] = 'Database query error: ' . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>

<header class="header">
    <a href="#" class="logo"><i class="fas fa-hospital"></i> Malignancy-Cancer Prediction</a>
    <nav class="navbar">
        <a href="http://127.0.0.1:5000/#home">Home</a>
        <a href="http://127.0.0.1:5000/#book">Predict Now</a>
        <a href="http://127.0.0.1:5000/#doctors">Doctors</a>
        <a href="#footer">About Us</a>
        <a href="register_form.php">Register</a>
    </nav>
    <div id="menu-btn" class="fas fa-bars"></div>
</header>

<div class="form-container">
    <form action="login_form.php" method="post">
        <h2>Login now</h2>
        <?php
        if (isset($error)) {
            foreach ($error as $error_msg) {
                echo '<span class="error-msg">' . $error_msg . '</span>';
            }
        }
        ?>
        <input type="email" name="email" required placeholder="Enter your email">
        <input type="password" name="password" required placeholder="Enter your password">
        <select name="user_type" required>
            <option value=""></option>
            <option value="admin">Admin</option>
            <option value="doctor">Doctor</option>
            <option value="patient">Patient</option>
        </select>
        <input type="submit" name="submit" value="Login now" class="form-btn">
        <p><h2>Don't have an account? <a href="register_form.php">Register now</a></h2></p>
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
