<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ABOUT US - CARILLO LAW</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        .about-content {
            padding: 60px 20px;
            background: #fdf6e3;
            min-height: 100vh;
        }
        .about-container {
            max-width: 1000px;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            gap: 60px;
            background: var(--white, #fff);
            border-radius: 18px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.07);
            padding: 40px 30px;
        }
        .section-title {
            font-family: 'Playfair Display', serif;
            color: var(--primary-color);
            text-align: center;
            margin-bottom: 30px;
            font-size: 2.5rem;
        }
        .about-brief {
            text-align: center;
            max-width: 900px;
            margin: 0 auto 0 auto;
            font-size: 1.2rem;
            color: var(--text-color);
        }
        .attorney-profile {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 30px;
        }
        .attorney-image img {
            width: 100%;
            max-width: 300px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .attorney-info {
            max-width: 600px;
            text-align: center;
            color: var(--text-color);
        }
        .attorney-info h3 {
            color: var(--primary-color);
            font-family: 'Playfair Display', serif;
            margin-bottom: 15px;
            font-size: 2rem;
        }
        .credentials {
            color: var(--secondary-color);
            font-weight: bold;
            margin-bottom: 15px;
        }
        .values-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            margin-top: 20px;
        }
        .value-card {
            background-color: var(--white);
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
            text-align: center;
            color: var(--text-color);
        }
        .value-card i {
            font-size: 2.5rem;
            color: var(--primary-color);
            margin-bottom: 20px;
        }
        .value-card h3 {
            color: var(--primary-color);
            margin-bottom: 15px;
            font-family: 'Playfair Display', serif;
        }
        @media (min-width: 900px) {
            .about-container {
                gap: 80px;
            }
        }
    </style>
</head>
<body>
    <div class="upperbar">
        <div class="topbar">
            <span>[📞 0973 727 8473]</span>
            <span><a href="https://mail.google.com/mail/?view=cm&fs=1&to=pcvcarillolaw@gmail.com" target="_blank" rel="noopener noreferrer" class="email-link">
                    <i class="fas fa-envelope"></i> pcvcarillolaw@gmail.com
                    </a>
                </span>
        </div>
        <nav class="navbar">
            <div class="logo">
                <a href="index.php"><img src="src/web_logo-removebg-preview.png" alt="Carillo Law Office"></a>
                <span><a href="index.php" class="webname">CARILLO LAW</a></span>
            </div>

            <ul class="nav-links">
                <li><a href="index.php">HOME</a></li>
                <li><a href="about.php" class="active">ABOUT</a></li>
                <li><a href="services.php">SERVICES</a></li>
                <li><a href="contacts.php">CONTACTS</a></li>
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

    <section class="about-hero">
        <div class="about-hero-content">
            <div class="about-hero-text">
                <h1>About Carillo Law</h1>
                <p>Dedicated to Excellence in Legal Services</p>
                <div style="margin-top: 20px; color: var(--secondary-color); letter-spacing: 2px;">INTEGRITY • TRUST • EXCELLENCE</div>
            </div>
        </div>
    </section>

    <section class="about-content">
        <div class="about-container">
            <h2 class="section-title">Our Story</h2>
            <div class="about-brief">
                <p>Founded with a commitment to justice and client advocacy, Carillo Law Office has been serving the community for over 5 years. Our practice is built on the foundation of integrity, expertise, and personalized attention to each client's unique legal needs.</p>
            </div>

            <h2 class="section-title">Our Attorney</h2>
            <div class="attorney-profile">
                <div class="attorney-image">
                    <img src="src/attorney-profile.jpg" alt="Attorney Carillo">
                </div>
                <div class="attorney-info">
                    <h3>Attorney Palma Carillo</h3>
                    <p class="credentials">Juris Doctor, University of Santo Tomas</p>
                    <p>With extensive experience in civil law, real estate, and notarial services, Attorney Carillo brings a wealth of knowledge and dedication to every case. A graduate of the prestigious University of Santo Tomas Faculty of Civil Law, our attorney combines academic excellence with practical legal expertise.</p>
                </div>
            </div>

            <h2 class="section-title">Our Values</h2>
            <div class="values-grid">
                <div class="value-card">
                    <i class="fas fa-balance-scale"></i>
                    <h3>Integrity</h3>
                    <p>We uphold the highest ethical standards in all our legal practices.</p>
                </div>
                <div class="value-card">
                    <i class="fas fa-handshake"></i>
                    <h3>Trust</h3>
                    <p>Building lasting relationships through transparency and reliability.</p>
                </div>
                <div class="value-card">
                    <i class="fas fa-gavel"></i>
                    <h3>Excellence</h3>
                    <p>Committed to providing the highest quality legal services.</p>
                </div>
                <div class="value-card">
                    <i class="fas fa-users"></i>
                    <h3>Client Focus</h3>
                    <p>Putting our clients' needs and interests first in everything we do.</p>
                </div>
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