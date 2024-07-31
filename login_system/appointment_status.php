<?php
@include 'config.php';

session_start();

// Redirect to login if not logged in
if (!isset($_SESSION['user_name'])) {
    header('Location: login_form.php');
    exit();
}

// Handle approval and disapproval actions if user is a doctor
if ($_SESSION['user_type'] === 'doctor' && isset($_GET['action']) && isset($_GET['id'])) {
    $appointment_id = intval($_GET['id']);
    $action = $_GET['action'];

    if ($action == 'approve') {
        $update = "UPDATE appointments SET status='approved' WHERE id=$appointment_id";
        if ($conn->query($update) === TRUE) {
            header('Location: appointment_status.php');
            exit();
        } else {
            echo "Error updating record: " . $conn->error;
        }
    } elseif ($action == 'disapprove') {
        $update = "UPDATE appointments SET status='disapproved' WHERE id=$appointment_id";
        if ($conn->query($update) === TRUE) {
            header('Location: appointment_status.php');
            exit();
        } else {
            echo "Error updating record: " . $conn->error;
        }
    }
}

// Fetch appointments sorted by date descending
$sql = "SELECT * FROM appointments ORDER BY date DESC";
$result = $conn->query($sql);

$appointments = [];

if ($result) {
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $appointments[] = $row;
        }
    } else {
        echo "No appointments found.";
    }
} else {
    echo "Error fetching appointments: " . $conn->error;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointments</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="css/styles.css">

    <style>
    .btn-approve {
        background-color: #741111;
        color: white;
        padding: 5px 10px;
        border: none;
        text-decoration: none;
        display: inline-block;
        margin: 3px; /* Adjust margin for spacing */
        cursor: pointer;
        border-radius: 5px;
        font-size: 0.9rem;
    }

    .btn-approve:hover {
        background-color: #d36666;
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
            color: #a53131;}

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    th, td {
        padding: 8px; /* Adjust padding for cell content */
        text-align: left;
        border: 1px solid #ddd;
    }

    th {
        background-color: #f2f2f2;
    }

    /* Adjust styles for table rows */
    tbody tr:nth-child(even) {
        background-color: #f9f9f9; /* Alternate row background color */
    }

    /* Responsive adjustments */
    @media only screen and (max-width: 600px) {
        .btn-approve {
            padding: 3px 6px; /* Adjust button padding for smaller screens */
            font-size: 0.8rem; /* Decrease font size for smaller screens */
        }
        th, td {
            padding: 6px; /* Adjust cell padding for smaller screens */
        }
    }
</style>

</head>
<body>
    <header class="header">
        <a href="#" class="logo"><i class="fas fa-hospital"></i> Malignancy-Cancer Prediction</a>
        <nav class="navbar">
            <a href="http://127.0.0.1:5000/#home">Home</a>
            <a href="http://127.0.0.1:5000/#book">Predict Now</a>
            <?php if ($_SESSION['user_type'] === 'doctor'): ?>
                <!-- Hide doctor link for doctor's dashboard -->
            <?php else: ?>
                <a href="http://localhost/login_system/doctors.html">Doctors</a>
            <?php endif; ?>
            <a href="http://localhost/login_system/logout.php">Log Out</a>
        </nav>
        <div id="searchContainer">
        <div id="searchContainer">
            <button id="searchBtn"><i class="fas fa-search"></i></button>
            <input type="text" id="searchInput" placeholder="Search">
        </div>
        </div>
        <div id="menu-btn" class="fas fa-bars"></div>
    </header>

    <section class="home" id="home">
        <div class="welcome-message"></div>
    </section>

    <section class="appointment-container" id="appointment">
        <div class="appoint">
            <h1>Appointments</h1>
            <table>
                <thead>
                    <tr>
                        <th>Patient Name</th>
                        <th>Phone</th>
                        <th>Doctor</th>
                        <th>Date</th>
                        <th>Status</th>
                        <?php if ($_SESSION['user_type'] === 'doctor'): ?>
                            <th>Approve/Disapprove</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody id="appointmentsBody">
                    <?php foreach ($appointments as $appointment): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($appointment['name']); ?></td>
                            <td><?php echo htmlspecialchars($appointment['phone']); ?></td>
                            <td><?php echo htmlspecialchars($appointment['doctor']); ?></td>
                            <td><?php echo htmlspecialchars($appointment['date']); ?></td>
                            <td><?php echo htmlspecialchars($appointment['status']); ?></td>
                            <?php if ($_SESSION['user_type'] === 'doctor'): ?>
                                <td>
                                    <?php if ($appointment['status'] == 'pending'): ?>
                                        <form action="appointment_status.php" method="GET" style="display:inline-block;">
                                            <input type="hidden" name="id" value="<?php echo $appointment['id']; ?>">
                                            <input type="hidden" name="action" value="approve">
                                            <button type="submit" class="btn"><span>Approve</span></button>
                                        </form>
                                        <form action="appointment_status.php" method="GET" style="display:inline-block;">
                                            <input type="hidden" name="id" value="<?php echo $appointment['id']; ?>">
                                            <input type="hidden" name="action" value="disapprove">
                                            <button type="submit" class="btn"><span>Disapprove</span></button>
                                        </form>
                                    <?php else: ?>
                                        <?php echo ucfirst($appointment['status']); ?>
                                    <?php endif; ?>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
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
    <script>
        document.getElementById('searchBtn').addEventListener('click', function() {
            const searchInput = document.getElementById('searchInput');
            if (searchInput.style.display === 'none' || searchInput.style.display === '') {
                searchInput.style.display = 'block';
                searchInput.focus();
            } else {
                searchInput.style.display = 'none';
            }
        });

        document.getElementById('searchInput').addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('#appointmentsBody tr');
            rows.forEach(row => {
                const cells = row.getElementsByTagName('td');
                let found = false;
                for (let i = 0; i < cells.length; i++) {
                    if (cells[i].innerText.toLowerCase().includes(searchTerm)) {
                        found = true;
                        break;
                    }
                }
                if (found) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html>
