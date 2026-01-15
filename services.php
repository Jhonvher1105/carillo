<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SERVICES - CARILLO LAW</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        .services-content {
            padding: 60px 20px 30px 20px;
            background: #fdf6e3;
        }
        .services-container {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 40px;
        }
        .service-category {
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
            padding: 36px 28px 28px 28px;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            transition: transform 0.18s, box-shadow 0.18s;
            position: relative;
        }
        .service-category:hover {
            transform: translateY(-8px) scale(1.025);
            box-shadow: 0 8px 32px rgba(15,63,65,0.13);
        }
        .service-icon {
            position: absolute;
            top: -32px;
            left: 24px;
            background: var(--secondary-color);
            color: var(--primary-color);
            border-radius: 50%;
            width: 64px;
            height: 64px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.2rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.10);
            border: 4px solid #fff;
        }
        .service-category h2 {
            font-family: 'Playfair Display', serif;
            color: var(--primary-color);
            font-size: 1.5rem;
            margin: 48px 0 18px 0;
            letter-spacing: 1px;
        }
        .service-details {
            width: 100%;
        }
        .service-item h3 {
            color: var(--secondary-color);
            font-size: 1.15rem;
            margin-bottom: 8px;
            font-weight: bold;
        }
        .service-item p, .service-item ul {
            color: var(--text-color);
            font-size: 1rem;
            margin-bottom: 8px;
        }
        .service-item ul {
            padding-left: 18px;
            margin-bottom: 8px;
        }
        .service-item li {
            margin-bottom: 3px;
        }
        .service-note {
            color: var(--error, #dc3545);
            font-size: 0.95rem;
            margin-top: 6px;
        }
        .booking-section {
            max-width: 600px;
            margin: 60px auto 0 auto;
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.07);
            padding: 36px 28px 28px 28px;
            text-align: center;
        }
        .booking-section h2 {
            font-family: 'Playfair Display', serif;
            color: var(--primary-color);
            font-size: 2rem;
            margin-bottom: 10px;
        }
        .booking-section p {
            color: var(--text-color);
            margin-bottom: 18px;
        }
        .book-consultation-btn {
            display: inline-block;
            background: var(--secondary-color);
            color: var(--primary-color);
            font-weight: bold;
            padding: 12px 32px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 1.1rem;
            transition: background 0.2s;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
        .book-consultation-btn:hover {
            background: #e6c200;
        }
        @media (max-width: 700px) {
            .services-container {
                grid-template-columns: 1fr;
                gap: 24px;
            }
            .service-category {
                padding: 32px 12px 20px 12px;
            }
            .service-category h2 {
                margin-top: 40px;
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
                <li><a href="services.php" class="active">SERVICES</a></li>
                <li><a href="contacts.php">CONTACTS</a></li>
                <li><a href="testimonials.php">TESTIMONIALS</a></li>
                <li><a href="appointments.php">APPOINTMENTS</a></li>
                <li>
                <?php if (isset($_SESSION['client_logged_in']) && $_SESSION['client_logged_in']): ?>
                    <div class="user-dropdown">
                        <a href="profile.php" class="user-nav" style="color:#FFD700; font-weight:bold; display:flex; align-items:center; gap:6px; text-decoration:none; text-transform:none;">
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

    <section class="services-hero">
        <div class="services-hero-content">
            <div class="services-hero-text">
                <h1>Our Legal Services</h1>
                <p>Professional legal solutions tailored to your needs</p>
            </div>
        </div>
    </section>

  <section class="services-content">
    <div class="services-container">
        <div class="service-category">
            <div class="service-icon">
                <i class="fas fa-users"></i>
            </div>
            <h2>Family Law</h2>
            <div class="service-details">
                <div class="service-item">
                    <h3>Family Legal Matters</h3>
                    <p>Assistance in cases such as:</p>
                    <ul>
                        <li>Divorce and Annulment</li>
                        <li>Child Custody and Support</li>
                        <li>Adoption</li>
                        <li>Domestic Violence</li>
                    </ul>
                    <p class="service-note">* Consultation fee applies</p>
                </div>
            </div>
        </div>

        <div class="service-category">
            <div class="service-icon">
                <i class="fas fa-gavel"></i>
            </div>
            <h2>Criminal Law</h2>
            <div class="service-details">
                <div class="service-item">
                    <h3>Criminal Defense</h3>
                    <p>Legal representation for:</p>
                    <ul>
                        <li>Felony and Misdemeanor Charges</li>
                        <li>Arrest and Investigation Assistance</li>
                        <li>Trial and Appeals</li>
                    </ul>
                    <p class="service-note">* Initial consultation fee applies</p>
                </div>
            </div>
        </div>

        <div class="service-category">
            <div class="service-icon">
                <i class="fas fa-home"></i>
            </div>
            <h2>Property Law</h2>
            <div class="service-details">
                <div class="service-item">
                    <h3>Property Legal Services</h3>
                    <p>Support with:</p>
                    <ul>
                        <li>Title Issues</li>
                        <li>Boundary Disputes</li>
                        <li>Land Use and Zoning</li>
                    </ul>
                    <p class="service-note">* Consultation fee applies</p>
                </div>
            </div>
        </div>

        <div class="service-category">
            <div class="service-icon">
                <i class="fas fa-file-contract"></i>
            </div>
            <h2>Contracts</h2>
            <div class="service-details">
                <div class="service-item">
                    <h3>Contract Drafting & Review</h3>
                    <p>Expert drafting and review of various contracts:</p>
                    <ul>
                        <li>Business Contracts and Agreements</li>
                        <li>Employment Contracts</li>
                        <li>Lease Agreements</li>
                        <li>Partnership Agreements</li>
                    </ul>
                    <p class="service-note">* Pricing based on document complexity and length</p>
                </div>
            </div>
        </div>

        <div class="service-category">
            <div class="service-icon">
                <i class="fas fa-building"></i>
            </div>
            <h2>Real Estate</h2>
            <div class="service-details">
                <div class="service-item">
                    <h3>Property Transactions</h3>
                    <p>Comprehensive legal services for real estate matters:</p>
                    <ul>
                        <li>Property Purchase and Sale Agreements</li>
                        <li>Title Verification and Transfer</li>
                        <li>Real Estate Contracts Review</li>
                        <li>Property Dispute Resolution</li>
                    </ul>
                    <p class="service-note">* Consultation fee applies for initial assessment</p>
                </div>
            </div>
        </div>

        <div class="service-category">
            <div class="service-icon">
                <i class="fas fa-briefcase"></i>
            </div>
            <h2>Labor and Employment</h2>
            <div class="service-details">
                <div class="service-item">
                    <h3>Workplace Legal Support</h3>
                    <p>Handling:</p>
                    <ul>
                        <li>Employment Contracts</li>
                        <li>Workplace Disputes</li>
                        <li>Labor Rights Compliance</li>
                    </ul>
                    <p class="service-note">* Consultation fee applies</p>
                </div>
            </div>
        </div>

        <div class="service-category">
            <div class="service-icon">
                <i class="fas fa-building-columns"></i>
            </div>
            <h2>Business or Corporate</h2>
            <div class="service-details">
                <div class="service-item">
                    <h3>Corporate Legal Services</h3>
                    <p>Including:</p>
                    <ul>
                        <li>Business Formation and Registration</li>
                        <li>Compliance and Governance</li>
                        <li>Contracts and Agreements</li>
                    </ul>
                    <p class="service-note">* Pricing depends on business complexity</p>
                </div>
            </div>
        </div>

        <div class="service-category">
            <div class="service-icon">
                <i class="fas fa-archive"></i>
            </div>
            <h2>Estate and Inheritance</h2>
            <div class="service-details">
                <div class="service-item">
                    <h3>Wills, Trusts, and Estate Planning</h3>
                    <p>Services include:</p>
                    <ul>
                        <li>Will Drafting</li>
                        <li>Estate Administration</li>
                        <li>Inheritance Disputes</li>
                    </ul>
                    <p class="service-note">* Consultation fee applies</p>
                </div>
            </div>
        </div>

        <div class="service-category">
            <div class="service-icon">
                <i class="fas fa-balance-scale"></i>
            </div>
            <h2>Court Representation (Civil, Criminal, Administrative)</h2>
            <div class="service-details">
                <div class="service-item">
                    <h3>Legal Advocacy</h3>
                    <p>Representation in:</p>
                    <ul>
                        <li>Civil Cases</li>
                        <li>Criminal Proceedings</li>
                        <li>Administrative Hearings</li>
                    </ul>
                    <p class="service-note">* Fees vary depending on case complexity</p>
                </div>
            </div>
        </div>

        <div class="service-category">
            <div class="service-icon">
                <i class="fas fa-stamp"></i>
            </div>
            <h2>Notarial Services</h2>
            <div class="service-details">
                <div class="service-item">
                    <h3>Document Notarization</h3>
                    <p>Professional notarization of various legal documents including:</p>
                    <ul>
                        <li>Affidavits and Sworn Statements</li>
                        <li>Special Power of Attorney</li>
                        <li>Deeds of Sale and Transfer</li>
                        <li>Certification and Authentication</li>
                    </ul>
                    <p class="service-note">* Rates vary depending on document type and complexity</p>
                </div>
            </div>
        </div>
    </div>

        <div class="booking-section">
            <h2>Schedule a Consultation</h2>
            <p>Get professional legal advice tailored to your specific needs</p>
            <?php if (isset($_SESSION['client_logged_in']) && $_SESSION['client_logged_in']): ?>
                <a href="consultation.php" class="book-consultation-btn">Book Consultation</a>
            <?php else: ?>
                <a href="client-login.php" class="book-consultation-btn">Login to Book</a>
            <?php endif; ?>
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
