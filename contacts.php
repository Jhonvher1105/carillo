<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CONTACT US - CARILLO LAW</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        .contact-content {
            background: #fdf6e3;
            padding-top: 40px;
            padding-bottom: 40px;
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
                <li><a href="contacts.php" class="active">CONTACTS</a></li>
                <li><a href="testimonials.php">TESTIMONIALS</a></li>
                <li><a href="appointments.php">APPOINTMENTS</a></li>
                <li>
                <?php if (isset($_SESSION['client_logged_in']) && $_SESSION['client_logged_in']): ?>
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
                <?php else: ?>
                    <a href="client-login.php" class="login-btn" style="color:#0f3f41;">Login</a>
                <?php endif; ?>
                </li>
            </ul>
        </nav>
    </div>

    <!-- Hero Section -->
    <section class="contact-hero">
        <div class="contact-hero-content">
            <div class="contact-hero-text">
                <h1>Contact Us</h1>
                <p>We're here to help with your legal needs</p>
            </div>
        </div>
    </section>

    <!-- Main Content Section -->
    <section class="contact-content">
        <div class="contact-container">
            <!-- Centered Contact Info Card Only -->
            <div class="contact-info">
                <h2>Get in Touch</h2>
                <div class="info-card">
                    <i class="fas fa-map-marker-alt"></i>
                    <span>Visit Us</span>
                    <div>9017 C.P. Trinidad St., Concepcion, Baliuag, Philippines</div>
                </div>
                <div class="info-card">
                    <i class="fas fa-phone"></i>
                    <span>Call Us</span>
                    <div style="margin-left: 0; margin-top: 4px;">0973 727 8473</div>
                </div>
                <div class="info-card">
                    <i class="fas fa-envelope"></i>
                    <span>Email Us</span>
                    <div><a href="https://mail.google.com/mail/?view=cm&fs=1&to=pcvcarillolaw@gmail.com" target="_blank" rel="noopener noreferrer" class="contact-email">
                    pcvcarillolaw@gmail.com
                    </a></div>
                </div>
                <div class="info-card">
                    <i class="fas fa-clock"></i>
                    <span>Office Hours</span>
                    <div>Monday - Friday: 9:00 AM - 6:00 PM</div>
                    <div>Saturday: 8:00 AM - 5:00 PM</div>
                    <div>Sunday & Holidays: Closed</div>
                </div>
                <div class="social-links">
                    <a href="https://www.facebook.com/profile.php?id=100083055586233" target="_blank" rel="noopener noreferrer" class="social-btn" style="color: #1877f3; font-weight: bold; text-decoration: none; font-size: 1.1rem;">
                        <i class="fab fa-facebook-f"></i> Follow us on Facebook
                    </a>
                </div>
            </div>
        </div>

        <!-- Map Section -->
        <div class="map-container">
            <h2>Our Location</h2>
            <div class="map">
                <iframe 
                    src="https://www.google.com/maps?q=14.949244,120.887328&z=17&output=embed" 
                    width="100%" 
                    height="450" 
                    style="border:0;" 
                    allowfullscreen="" 
                    loading="lazy" 
                    referrerpolicy="no-referrer-when-downgrade">
                </iframe>
            </div>
        </div>
    </section>

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