<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TESTIMONIALS - CARILLO LAW</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        .testimonials-hero {
            height: 60vh;
            background: linear-gradient(rgba(15, 63, 65, 0.8), rgba(15, 63, 65, 0.8)),
                        url('src/hero-bg.jpg') center/cover no-repeat;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: var(--white);
            margin-top: 100px;
        }

        .testimonials-hero-content {
            max-width: 800px;
            padding: 0 20px;
        }

        .testimonials-hero-text h1 {
            font-family: 'Playfair Display', serif;
            font-size: 3rem;
            margin-bottom: 20px;
            color: #FFD700;
        }

        .testimonials-hero-text p {
            font-size: 1.2rem;
            margin-bottom: 30px;
        }

        .testimonials-hero-text p2 {
            display: block;
            color: var(--secondary-color);
            font-weight: bold;
            letter-spacing: 2px;
            margin-top: 20px;
        }

        .testimonials-content {
            background: #fdf6e3;
            padding: 60px 20px;
        }

        .testimonials-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .testimonials-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 30px;
            margin-bottom: 60px;
        }

        .testimonial-card {
            background: #fff;
            border-radius: 16px;
            padding: 32px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
            transition: transform 0.3s ease;
        }

        .testimonial-card:hover {
            transform: translateY(-5px);
        }

        .testimonial-rating {
            color: #FFD700;
            font-size: 1.2rem;
            margin-bottom: 20px;
        }

        .testimonial-text {
            font-size: 1.1rem;
            line-height: 1.6;
            color: #333;
            margin-bottom: 24px;
            font-style: italic;
        }

        .testimonial-author {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .author-image {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            object-fit: cover;
        }

        .author-info h4 {
            color: var(--primary-color);
            margin: 0;
            font-size: 1.1rem;
        }

        .author-info p {
            color: #666;
            margin: 5px 0 0 0;
            font-size: 0.9rem;
        }

        .submit-testimonial,
        .login-prompt {
            display: none !important;
        }

        @media (max-width: 768px) {
            .testimonials-hero-text h1 {
                font-size: 36px;
            }
            
            .testimonials-grid {
                grid-template-columns: 1fr;
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
                <li><a href="testimonials.php" class="active">TESTIMONIALS</a></li>
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

    <section class="hero">
        <div class="hero-content">
            <div class="hero-text">
                <h1>What Our Clients Say</h1>
                <p>Read real experiences from people we've helped with our legal and notarial services.</p>
                <p2>TRUST • SATISFACTION • EXCELLENCE</p2>
            </div>
        </div>
    </section>

    <section class="testimonials-content">
        <div class="testimonials-container">
            <div class="testimonials-grid">
                <div class="testimonial-card">
                    <div class="testimonial-rating">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                    <p class="testimonial-text">"Attorney Carillo provided exceptional service during my real estate transaction. Their attention to detail and professional guidance made the process smooth and stress-free."</p>
                    <div class="testimonial-author">
                        <img src="src/reviewer1.jpg" alt="User" class="author-image">
                        <div class="author-info">
                            <h4>John Doe</h4>
                            <p>Real Estate Client</p>
                        </div>
                    </div>
                </div>

                <div class="testimonial-card">
                    <div class="testimonial-rating">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                    <p class="testimonial-text">"The notarial services were quick and professional. I appreciate the efficiency and courtesy of the staff. Highly recommended!"</p>
                    <div class="testimonial-author">
                        <img src="src/reviewer3.jpg" alt="User" class="author-image">
                        <div class="author-info">
                            <h4>Jane Smith</h4>
                            <p>Notarial Services Client</p>
                        </div>
                    </div>
                </div>

                <div class="testimonial-card">
                    <div class="testimonial-rating">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                    <p class="testimonial-text">"I was impressed by the thoroughness and expertise shown in handling my contract. The legal advice was clear and practical."</p>
                    <div class="testimonial-author">
                        <img src="src/reviewer2.jpg" alt="User" class="author-image">
                        <div class="author-info">
                            <h4>Mark Johnson</h4>
                            <p>Business Client</p>
                        </div>
                    </div>
                </div>

                <div class="testimonial-card">
                    <div class="testimonial-rating">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                    <p class="testimonial-text">"Very professional and approachable. They made sure I understood every step of the process. Will definitely recommend to friends and family!"</p>
                    <div class="testimonial-author">
                        <img src="src/reviewer4.jpg" alt="User" class="author-image">
                        <div class="author-info">
                            <h4>Maria Lopez</h4>
                            <p>Family Law Client</p>
                        </div>
                    </div>
                </div>

                <div class="testimonial-card">
                    <div class="testimonial-rating">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                    <p class="testimonial-text">"Quick response and very knowledgeable. I felt supported throughout my legal issue. Thank you, Carillo Law!"</p>
                    <div class="testimonial-author">
                        <img src="src/reviewer5.jpg" alt="User" class="author-image">
                        <div class="author-info">
                            <h4>Carlos Reyes</h4>
                            <p>Civil Case Client</p>
                        </div>
                    </div>
                </div>

                <div class="testimonial-card">
                    <div class="testimonial-rating">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                    <p class="testimonial-text">"Excellent service and attention to detail. The team was always available to answer my questions. Highly recommended!"</p>
                    <div class="testimonial-author">
                        <img src="src/reviewer6.jpg" alt="User" class="author-image">
                        <div class="author-info">
                            <h4>Angela Cruz</h4>
                            <p>Consultation Client</p>
                        </div>
                    </div>
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
        const ratingInputs = document.querySelectorAll('.rating-input input');
        const ratingLabels = document.querySelectorAll('.rating-input label');

        ratingInputs.forEach((input, index) => {
            input.addEventListener('change', () => {
                ratingLabels.forEach((label, labelIndex) => {
                    if (labelIndex <= index) {
                        label.style.color = '#FFD700';
                    } else {
                        label.style.color = '#ccc';
                    }
                });
            });
        });
        });
    </script>
</body>
</html> 