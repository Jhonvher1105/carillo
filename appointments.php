<?php
session_start();
require_once 'config/database.php';

// Check if user is logged in
if (!isset($_SESSION['client_logged_in']) || !$_SESSION['client_logged_in']) {
    header("Location: client-login.php");
    exit();
}

$db = Database::getInstance();
$conn = $db->getConnection();

// Handle appointment cancellation
if (isset($_POST['cancel_appointment'])) {
    $consultation_id = $_POST['consultation_id'];
    $stmt = $conn->prepare("UPDATE consultations SET status = 'cancelled' WHERE consultation_id = ? AND uid = ?");
    $stmt->bind_param("ii", $consultation_id, $_SESSION['client_id']);
    $stmt->execute();
}

// Get user's appointments
$stmt = $conn->prepare("SELECT consultation_id, legal_issue, preferred_date, status, message 
                       FROM consultations 
                       WHERE uid = ? 
                       ORDER BY preferred_date DESC");
$stmt->bind_param("i", $_SESSION['client_id']);
$stmt->execute();
$result = $stmt->get_result();
$appointments = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Appointments - Carillo Law</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        .appointments-container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 20px;
        }

        .appointments-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .appointments-header h1 {
            color: #0f3f41;
            font-size: 2.5em;
            margin-bottom: 10px;
        }

        .appointments-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .status-approved {
            background-color: #cce5ff;  /* light blue background */
            color:rgb(21, 142, 163);             /* dark blue text */
        }
            .appointments-table th,
        .appointments-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        .appointments-table th {
            background-color: #0f3f41;
            color: white;
            font-weight: 500;
        }

        .appointments-table tr:hover {
            background-color: #f8f9fa;
        }

        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.9em;
            font-weight: 500;
        }

        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-completed {
            background-color: #d4edda;
            color: #155724;
        }

        .status-cancelled {
            background-color: #f8d7da;
            color: #721c24;
        }

        .status-rejected {
            background-color: #f8d7da;
            color: #721c24;
        }

        .cancel-btn {
            padding: 8px 16px;
            background-color: #dc3545;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .cancel-btn:hover {
            background-color: #c82333;
        }

        .cancel-btn:disabled {
            background-color: #6c757d;
            cursor: not-allowed;
        }

        .no-appointments {
            text-align: center;
            padding: 40px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .no-appointments p {
            color: #6c757d;
            margin-bottom: 20px;
        }

        .book-consultation-btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #0f3f41;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.2s;
        }

        .book-consultation-btn:hover {
            background-color: #0a2b2c;
        }

        /* Hero Section Styles */
        .appointments-hero{
            background-image: url(src/appointments.jpg);
            position: relative;
            width: 100%;
            height: 100vh;
            padding: 50px 20px;
            background-size: 150%; 
            background-position:center; 
            background-repeat:no-repeat;
            color: white;
            display: flex; /* Center child */
            align-items: center; /* Vertically center */
            justify-content: center; /* Horizontally center */
        }
        .appointments-hero::before{
            content: '';
            top: 0;
            left: 0;
            height: 100vh;
            width: 100%;
            position: absolute;
            background: linear-gradient(to right, rgba(27,27,27,0.4) 4%,
            rgba(27, 27, 27, 0.5) 20%, 
            rgba(27, 27, 27, 0.7) 40%, 
            rgba(17, 17, 17, 0.9) 70%, 
            #111 100%
        );
            z-index: 1; 
        }
        .appointments-hero-text{
            position: relative;
            max-width: 1000px;
            display: flex;
            flex-direction: column; /* Stack text and button vertically */
            align-items: center; /* Align items to the left */
            justify-content: center; /* Vertically center the content */
            z-index: 2;
        }
        .appointments-hero-text h1{
            color: #e6c200;
            font-size: 45px;
            margin-bottom: 20px;
        }
        .appointments-hero-text p {
            font-family: 'Playfair Display', sans-serif;
            margin-top: 20px;
            font-size: 17px;
            margin-bottom: 30px;
            line-height: 1.5;
        }
    </style>
</head>
<body>
    <div class="upperbar">
        <div class="topbar">
            <span>[📞 0973 727 8473]</span>
            <span><a href="https://mail.google.com/mail/?view=cm&fs=1&to=pcvcarillolaw@gmail.com" target="_blank" rel="noopener noreferrer" class="email-link">
                    <i class="fas fa-envelope"></i> pcvcarillolaw@gmail.com
                    </a></span>
        </div>
        <nav class="navbar">
            <div class="logo">
                <a href="index.php"><img src="src/web_logo-removebg-preview.png" alt="Carillo Law Office"></a>
                <span><a href="index.php" class="webname">CARILLO LAW</a></span>
            </div>

            <ul class="nav-links">
                <li><a href="index.php">HOME</a></li>
                <li><a href="about.php">ABOUT</a></li>
                <li><a href="services.php">SERVICES</a></li>
                <li><a href="contacts.php">CONTACTS</a></li>
                <li><a href="testimonials.php">TESTIMONIALS</a></li>
                <li><a href="appointments.php" class="active">APPOINTMENTS</a></li>
                <li>
                    <div class="user-dropdown">
                        <a href="account-settings.php" class="user-nav" style="color:#FFD700; font-weight:bold; display:flex; align-items:center; gap:6px; text-decoration:none; text-transform:none;">
                            <i class="fas fa-user-circle" style="font-size: 20px;"></i>
                            <?= htmlspecialchars($_SESSION['client_first_name']) ?>
                        </a>
                        <div class="user-dropdown-content">
                            <a href="account-settings.php" class="dropdown-btn"><i class="fas fa-cog"></i> Settings</a>
                            <a href="#" class="dropdown-btn" id="logoutLink"><i class="fas fa-sign-out-alt"></i> Logout</a>
                        </div>
                    </div>
                </li>
            </ul>
        </nav>
    </div>

    <!-- Hero Section -->
    <section class="appointments-hero">
        <div class="appointments-hero-content">
            <div class="appointments-hero-text">
                <h1>My Appointments</h1>
                <p>View and manage your scheduled consultations</p>
            </div>
        </div>
    </section>

    <div class="appointments-container">
        <?php if (empty($appointments)): ?>
            <div class="no-appointments">
                <p>You don't have any appointments scheduled.</p>
                <a href="consultation.php" class="book-consultation-btn">Book a Consultation</a>
            </div>
        <?php else: ?>
            <table class="appointments-table">
                <thead>
                    <tr>
                        <th>Legal Issue</th>
                        <th>Date & Time</th>
                        <th>Status</th>
                        <th>Message</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($appointments as $appointment): ?>
                        <tr>
                            <td><?= htmlspecialchars($appointment['legal_issue']) ?></td>
                            <td><?= date('F j, Y g:i A', strtotime($appointment['preferred_date'])) ?></td>
                            <td>
                                <span class="status-badge status-<?= $appointment['status'] ?>">
                                    <?= ucfirst($appointment['status']) ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars($appointment['message']) ?></td>
                            <td>
                                <?php if ($appointment['status'] === 'pending'): ?>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="consultation_id" value="<?= $appointment['consultation_id'] ?>">
                                        <button type="submit" name="cancel_appointment" class="cancel-btn" 
                                                onclick="return confirm('Are you sure you want to cancel this appointment?')">
                                            Cancel
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <button class="cancel-btn" disabled>Cancel</button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <footer class="footer">
        <div class="footer-container">
            <div class="footer-section about">
                <h3>Carillo Law Office</h3>
                <p>Providing trusted legal and notarial services in civil, real estate, and contract law for over 5 years.</p>
            </div>
            <div class="footer-section contact">
                <h4>Contact Us</h4>
                <p>📍 9017 C.P. Trinidad St., Concepcion, Baliuag, Philippines</p>
                <p>📞 0973 727 8473</p>
                <p><a href="https://mail.google.com/mail/?view=cm&fs=1&to=pcvcarillolaw@gmail.com" target="_blank" rel="noopener noreferrer" class="email-link">
                        <i class="fas fa-envelope"></i> pcvcarillolaw@gmail.com
                    </a>
                </p>
                <p>
                    <a href="https://www.facebook.com/profile.php?id=100083055586233" 
                        target="_blank" 
                        rel="noopener noreferrer"
                        class="facebook-link">
                        <i class="fab fa-facebook-square" style="margin-right: 5px;"></i> Facebook
                    </a>
                </p>
            </div>
            <div class="footer-section hours">
                <h4>Office Hours</h4>
                <p>Mon – Fri: 9:00 AM – 6:00 PM</p>
                <p>Sat: 8:00 AM – 5:00 PM</p>
                <p>Sun & Holidays: Closed</p>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2025 Carillo Law Office. All rights reserved.</p>
            <p>All legal services comply with Philippine law. This website is for informational purposes only and does not constitute legal advice.</p>
        </div>
    </footer>
        <script>
        document.addEventListener("DOMContentLoaded", function () {
    const logoutLink = document.getElementById('logoutLink');

    if (logoutLink) {
        logoutLink.addEventListener('click', function (e) {
            e.preventDefault();
            const confirmed = confirm("Are you sure you want to log out?");
            if (confirmed) {
                window.location.href = "logout.php";
            }
        });
    }
    });
    </script>
</body>
</html> 