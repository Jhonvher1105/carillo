<?php
session_start();

require_once 'config/database.php';

$full_name = $email = $phone = "";

// Get DB connection
$db = Database::getInstance();
$conn = $db->getConnection();

// Fetch all booked consultations with their status
$query = "SELECT preferred_date, status FROM consultations WHERE status IN ('pending', 'completed')";
$result = $conn->query($query);
$booked_slots = [];
while ($row = $result->fetch_assoc()) {
    $booked_slots[] = [
        'date' => $row['preferred_date'],
        'status' => $row['status']
    ];
}

if (isset($_SESSION['client_logged_in']) && $_SESSION['client_logged_in']) {
    $uid = $_SESSION['client_id'];

    $stmt = $conn->prepare("SELECT first_name, last_name, email, contact_number FROM users WHERE uid = ?");
    $stmt->bind_param("i", $uid);
    $stmt->execute();
    $stmt->bind_result($first_name, $last_name, $email, $contact_number);

    if ($stmt->fetch()) {
        $full_name = $first_name . " " . $last_name;
        $phone = $contact_number;
    }

    $stmt->close();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consultations</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        .alert {
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
            text-align: center;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .time-note {
            display: block;
            color: #666;
            font-size: 0.9em;
            margin-top: 5px;
            margin-bottom: 15px;
        }

        /* New styles for datetime input */
        input[type="datetime-local"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-family: inherit;
            background-color: #fff;
            cursor: pointer;
        }

        input[type="datetime-local"]:disabled {
            background-color: #f5f5f5;
            cursor: not-allowed;
            opacity: 0.7;
        }

        input[type="datetime-local"]:focus {
            outline: none;
            border-color: #0f3f41;
            box-shadow: 0 0 5px rgba(15, 63, 65, 0.2);
        }

        /* Style for the calendar popup */
        input[type="datetime-local"]::-webkit-calendar-picker-indicator {
            cursor: pointer;
            padding: 5px;
            margin-right: 5px;
            opacity: 0.7;
        }

        input[type="datetime-local"]::-webkit-calendar-picker-indicator:hover {
            opacity: 1;
        }

        /* Custom Calendar Styles */
        .custom-calendar {
            width: 100%;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            overflow: hidden;
        }

        .calendar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            background-color: #0f3f41;
            color: white;
        }

        .calendar-header button {
            background: none;
            border: none;
            color: white;
            cursor: pointer;
            font-size: 16px;
            padding: 5px 10px;
        }

        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 1px;
            background-color: #ddd;
        }

        .calendar-day {
            background-color: white;
            padding: 10px;
            text-align: center;
            cursor: pointer;
            min-height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .calendar-day.unavailable {
            background-color: #f5f5f5;
            color: #999;
            cursor: not-allowed;
        }

        .calendar-day.today {
            background-color: #e8f4f4;
            font-weight: bold;
        }

        .calendar-day.selected {
            background-color: #0f3f41;
            color: white;
        }

        .time-slots {
            margin-top: 20px;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
        }

        .time-slot {
            padding: 10px;
            text-align: center;
            border: 1px solid #ddd;
            border-radius: 4px;
            cursor: pointer;
        }

        .time-slot.unavailable {
            background-color: #f5f5f5;
            color: #999;
            cursor: not-allowed;
        }

        .time-slot.selected {
            background-color: #0f3f41;
            color: white;
        }

        .hidden {
            display: none;
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
                <a href = "index.php"><img src="src/web_logo-removebg-preview.png" alt="Carillo Law Office"></a>
                <span><a href = "index.php" class = "webname">CARILLO LAW</a></span>
            </div>

            <ul class="nav-links">
                <li><a href="index.php">HOME</a></li>
                <li><a href="about.php">ABOUT</a></li>
                <li><a href="services.php">SERVICES</a></li>
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

<!-- HERO BANNER -->
<section class="consul-hero">
    <div class="consul-hero-content">
        <div class="consul-hero-text">
            <h1>Legal Solutions with Integrity & Expertise</h1>
            <p>Professional legal and notarial services, committed to protecting your rights.</p>
            <p2>JUSTICE • EQUALITY • TRUTH</p2>
        </div>
    </div>
</section>

<section class="services-info">
    <div class="services-info-container">
        <h2>What to Expect from Our Legal Services</h2>
        <p>
            At Carillo Law Office, we provide professional and client-centered legal services tailored to your specific needs.
            Whether you require help with notarization, property matters, or contract drafting, our firm is here to guide you every step of the way.
            Our consultations are designed to help you understand your legal options and give you clarity before making important decisions.
        </p>
        <p>
            We take pride in offering honest advice, efficient service, and a commitment to justice. Explore our full range of services to see how we can assist you.
        </p>
        <a href="services.php" class="learn-more-btn">Explore Our Services</a>
    </div>
</section>

<!-- Consultation Booking Section -->
<div class="consultation-container">
    <h1>Book a Legal Consultation</h1>
    <p class="subtext">Fill out the form below and our legal team will contact you to confirm your appointment.</p>

    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success">
            <?php 
            echo $_SESSION['success_message'];
            unset($_SESSION['success_message']);
            ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-error">
            <?php 
            echo $_SESSION['error_message'];
            unset($_SESSION['error_message']);
            ?>
        </div>
    <?php endif; ?>

    <form action="submit-consultation.php" method="POST" class="consultation-form">
        <label for="full_name">Full Name</label>
        <input type="text" id="full_name" name="full_name" required value="<?= htmlspecialchars($full_name) ?>">

        <label for="email">Email Address</label>
        <input type="email" id="email" name="email" required value="<?= htmlspecialchars($email) ?>">

        <label for="phone">Phone Number</label>
        <input type="tel" id="phone" name="phone" required value="<?= htmlspecialchars($phone) ?>">

        <label for="legal_issue">Type of Legal Concern</label>
        <select id="legal_issue" name="legal_issue" required>s
            <option value="">-- Please Select --</option>
            <option value="Family Law">Family Law</option>s
            <option value="Criminal Law">Criminal Law</option>
            <option value="Property Law">Property Law</option>
            <option value="Contracts">Contracts</option>
            <option value="Real Estate">Real Estate</option>
            <option value="Labor and Employment">Labor and Employment</option>
            <option value="Business or Corporate">Business or Corporate</option>
            <option value="Estate and Inheritance">Estate and Inheritance</option>
            <option value="Civil Litigation">Court Representation(Civil, Criminal, Administrative)</option>
            <option value="Notarial Services">Notarial Services</option>
        </select>

        <label for="preferred_date">Preferred Date and Time</label>
        <div class="custom-calendar">
            <div class="calendar-header">
                <button id="prevMonth">&lt;</button>
                <h3 id="currentMonth"></h3>
                <button id="nextMonth">&gt;</button>
            </div>
            <div class="calendar-grid" id="calendarGrid"></div>
        </div>
        <div class="time-slots" id="timeSlots"></div>
        <input type="hidden" name="preferred_date" id="preferred_date" required>
        <small class="time-note">Available hours: Monday to Friday, 8:30 AM to 6:00 PM</small>

        <label for="message">Brief Description</label>
        <textarea id="message" name="message" rows="4" placeholder="Tell us a bit about your legal concern..." required></textarea>

        <button type="submit" class="submit-btn">Submit Consultation Request</button>
    </form>

    <p class="confidential-note">All information submitted is kept strictly confidential and used only for appointment scheduling.</p>
</div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const bookedSlots = <?php echo json_encode($booked_slots); ?>;
            const calendarGrid = document.getElementById('calendarGrid');
            const timeSlots = document.getElementById('timeSlots');
            const currentMonthElement = document.getElementById('currentMonth');
            const prevMonthBtn = document.getElementById('prevMonth');
            const nextMonthBtn = document.getElementById('nextMonth');
            const preferredDateInput = document.getElementById('preferred_date');
            
            let currentDate = new Date();
            let selectedDate = null;
            let selectedTime = null;

            // Generate time slots
            function generateTimeSlots() {
                timeSlots.innerHTML = '';
                const slots = [];
                const startHour = 8;
                const endHour = 18;
                
                // Get current time for comparison
                const now = new Date();
                const isToday = selectedDate && 
                    selectedDate.getDate() === now.getDate() && 
                    selectedDate.getMonth() === now.getMonth() && 
                    selectedDate.getFullYear() === now.getFullYear();
                
                for (let hour = startHour; hour < endHour; hour++) {
                    for (let minute of ['00', '30']) {
                        if (hour === startHour && minute === '00') continue; // Skip 8:00
                        if (hour === 8 && minute === '30') {
                            slots.push('08:30');
                            continue;
                        }
                        slots.push(`${hour.toString().padStart(2, '0')}:${minute}`);
                    }
                }

                slots.forEach(time => {
                    const slot = document.createElement('div');
                    slot.className = 'time-slot';
                    slot.textContent = time;
                    
                    // Check if this time slot is available
                    if (selectedDate) {
                        const slotDateTime = new Date(selectedDate);
                        const [hours, minutes] = time.split(':');
                        slotDateTime.setHours(parseInt(hours), parseInt(minutes));
                        
                        // Check if the time slot is in the past for today
                        const isPastTime = isToday && slotDateTime <= now;
                        
                        if (isPastTime || !isTimeSlotAvailable(slotDateTime)) {
                            slot.classList.add('unavailable');
                        } else {
                            slot.addEventListener('click', () => selectTimeSlot(slot, time));
                        }
                    } else {
                        slot.classList.add('unavailable');
                    }
                    
                    timeSlots.appendChild(slot);
                });
            }

            function selectTimeSlot(slotElement, time) {
                document.querySelectorAll('.time-slot').forEach(slot => {
                    slot.classList.remove('selected');
                });
                slotElement.classList.add('selected');
                selectedTime = time;
                updatePreferredDate();
            }

            function updatePreferredDate() {
                if (selectedDate && selectedTime) {
                    const [hours, minutes] = selectedTime.split(':');
                  
                    const date = new Date(selectedDate.getFullYear(), selectedDate.getMonth(), selectedDate.getDate(), parseInt(hours), parseInt(minutes), 0);
              
                    const year = date.getFullYear();
                    const month = String(date.getMonth() + 1).padStart(2, '0');
                    const day = String(date.getDate()).padStart(2, '0');
                    const localHours = String(date.getHours()).padStart(2, '0');
                    const localMinutes = String(date.getMinutes()).padStart(2, '0');
                    preferredDateInput.value = `${year}-${month}-${day}T${localHours}:${localMinutes}`;
                }
            }

            function isTimeSlotAvailable(dateTime) {
                const day = dateTime.getDay();
                if (day === 0 || day === 6) return false;

                const hours = dateTime.getHours();
                const minutes = dateTime.getMinutes();
                const timeInMinutes = hours * 60 + minutes;
                const startTimeInMinutes = 8 * 60 + 30;
                const endTimeInMinutes = 18 * 60;

                if (timeInMinutes < startTimeInMinutes || timeInMinutes > endTimeInMinutes) {
                    return false;
                }

                for (const bookedSlot of bookedSlots) {
                    const bookedDate = new Date(bookedSlot.date);
                    // Only check for conflicts with pending and completed appointments
                    if (bookedSlot.status === 'pending' || bookedSlot.status === 'completed') {
                        // Block 2 hours before and after the booked slot
                        const minTime = new Date(bookedDate.getTime() - 2 * 60 * 60 * 1000);
                        const maxTime = new Date(bookedDate.getTime() + 2 * 60 * 60 * 1000);
                        if (dateTime >= minTime && dateTime <= maxTime) {
                            return false;
                        }
                    }
                }

                return true;
            }

            function generateCalendar() {
                const year = currentDate.getFullYear();
                const month = currentDate.getMonth();
                
                const firstDay = new Date(year, month, 1);
                const lastDay = new Date(year, month + 1, 0);
                const startingDay = firstDay.getDay();
                const totalDays = lastDay.getDate();
                
                currentMonthElement.textContent = `${firstDay.toLocaleString('default', { month: 'long' })} ${year}`;
                
                calendarGrid.innerHTML = '';
                
              
                const days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
                days.forEach(day => {
                    const dayHeader = document.createElement('div');
                    dayHeader.className = 'calendar-day';
                    dayHeader.textContent = day;
                    calendarGrid.appendChild(dayHeader);
                });
                
              
                for (let i = 0; i < startingDay; i++) {
                    const emptyDay = document.createElement('div');
                    emptyDay.className = 'calendar-day';
                    calendarGrid.appendChild(emptyDay);
                }
                
           
                for (let day = 1; day <= totalDays; day++) {
                    const dayElement = document.createElement('div');
                    dayElement.className = 'calendar-day';
                    dayElement.textContent = day;
                    
                    const currentDayDate = new Date(year, month, day);
                    
                    // Check if the date is in the past
                    const today = new Date();
                    today.setHours(0, 0, 0, 0); // Reset time to start of day
                    
                    if (currentDayDate < today || currentDayDate.getDay() === 0 || currentDayDate.getDay() === 6) {
                        dayElement.classList.add('unavailable');
                    } else {
                        dayElement.addEventListener('click', () => selectDate(dayElement, currentDayDate));
                    }
                    
                    if (currentDayDate.toDateString() === new Date().toDateString()) {
                        dayElement.classList.add('today');
                    }
                    
                    calendarGrid.appendChild(dayElement);
                }
            }

            function selectDate(dayElement, date) {
                document.querySelectorAll('.calendar-day').forEach(day => {
                    day.classList.remove('selected');
                });
                dayElement.classList.add('selected');
                selectedDate = date;
                selectedTime = null;
                generateTimeSlots();
            }

            prevMonthBtn.addEventListener('click', () => {
                currentDate.setMonth(currentDate.getMonth() - 1);
                generateCalendar();
                generateTimeSlots();
            });

            nextMonthBtn.addEventListener('click', () => {
                currentDate.setMonth(currentDate.getMonth() + 1);
                generateCalendar();
                generateTimeSlots();
            });

            // Initialize calendar
            generateCalendar();
            generateTimeSlots();
        });
    </script>

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
                        <i class="fab fa-facebook-square" style = " margin-right: 5px;"></i>  Facebook
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