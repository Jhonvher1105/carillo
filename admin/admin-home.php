<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

$db = Database::getInstance();
$conn = $db->getConnection();

// Automatically update past appointments
$update_past = "UPDATE consultations 
                SET status = 'completed' 
                WHERE status = 'pending' 
                AND preferred_date < NOW()";
$conn->query($update_past);

$query = "SELECT COUNT(*) as pending_count FROM consultations WHERE status = 'pending'";
$result = $conn->query($query);
$row = $result->fetch_assoc();
$pending_count = $row['pending_count'];

$query = "SELECT full_name, preferred_date FROM consultations WHERE status = 'pending' ORDER BY created_at DESC LIMIT 3";
$result = $conn->query($query);
$latest_consults = $result->fetch_all(MYSQLI_ASSOC);

// Get current or requested month/year
$month = isset($_GET['month']) ? (int)$_GET['month'] : date('m');
$year = isset($_GET['year']) ? (int)$_GET['year'] : date('Y');

// Get consultation dates for the calendar highlight - only future dates
$stmt = $conn->prepare("SELECT consultation_id, preferred_date, full_name 
                       FROM consultations 
                       WHERE MONTH(preferred_date) = ? 
                       AND YEAR(preferred_date) = ? 
                       AND status = 'pending'
                       AND preferred_date >= CURDATE()");
$stmt->bind_param("ii", $month, $year);
$stmt->execute();
$result = $stmt->get_result();

$consultations_by_date = []; // key: date string, value: array of client names
while ($row = $result->fetch_assoc()) {
    $date = substr($row['preferred_date'], 0, 10); // Only the date part "YYYY-MM-DD"
    if (!isset($consultations_by_date[$date])) {
        $consultations_by_date[$date] = [];
    }
    $datetime = new DateTime($row['preferred_date']);
    $time = $datetime->format('H:i'); // format as 24-hour time

    $consultations_by_date[$date][] = [
        'id' => $row['consultation_id'],
        'name' => $row['full_name'],
        'time' => $time
    ];
}

$query = "SELECT COUNT(*) as scheduled_count 
          FROM consultations 
          WHERE preferred_date >= NOW() 
          AND status IN ('approved')";
$result = $conn->query($query);
$row = $result->fetch_assoc();
$scheduled_count = $row['scheduled_count'];

// Count active users (you may define 'active' based on your schema, e.g., status = 'active')
$query = "SELECT COUNT(*) as active_users FROM users";
$result = $conn->query($query);
$row = $result->fetch_assoc();
$active_users = $row['active_users'];

// Change function signature to accept consultations_by_date instead of consultation_dates
function build_calendar($month, $year, $consultations_by_date = []) {
    $daysOfWeek = array('Sun','Mon','Tue','Wed','Thu','Fri','Sat');
    $firstDayOfMonth = mktime(0,0,0,$month,1,$year);
    $numberDays = date('t',$firstDayOfMonth);
    $dateComponents = getdate($firstDayOfMonth);
    $monthName = $dateComponents['month'];
    $dayOfWeek = $dateComponents['wday'];

    $calendar = "<table class='calendar'>";
    $calendar .= "<caption>$monthName $year</caption>";
    $calendar .= "<tr>";

    foreach($daysOfWeek as $day) {
        $calendar .= "<th class='header'>$day</th>";
    }

    $calendar .= "</tr><tr>";

    if ($dayOfWeek > 0) { 
        $calendar .= str_repeat("<td></td>", $dayOfWeek); 
    }

    $currentDay = 1;

    while ($currentDay <= $numberDays) {
        if ($dayOfWeek == 7) {
            $dayOfWeek = 0;
            $calendar .= "</tr><tr>";
        }

        $dateString = sprintf('%04d-%02d-%02d', $year, $month, $currentDay);

        $highlight_class = '';
        $client_names_html = '';

        if (isset($consultations_by_date[$dateString])) {
            $highlight_class = ' highlight';

            // Create a list of client names for this day
            $names = $consultations_by_date[$dateString];
            $client_names_html = '<br><small>';
            foreach ($names as $client) {
                $client_name = htmlspecialchars($client['name']);
                $client_time = htmlspecialchars($client['time']);
                $client_names_html .= "$client_name at $client_time<br>";
            }
        
        }

        $data_clients = isset($consultations_by_date[$dateString]) 
        ? htmlspecialchars(json_encode($consultations_by_date[$dateString])) 
        : '';

        $calendar .= "<td class='day{$highlight_class}' data-date='$dateString'" .
            ($data_clients ? " data-clients='$data_clients'" : "") .
            ">$currentDay</td>";


        $currentDay++;
        $dayOfWeek++;
    }

    if ($dayOfWeek != 7) { 
        $remainingDays = 7 - $dayOfWeek;
        $calendar .= str_repeat("<td></td>", $remainingDays); 
    }

    $calendar .= "</tr>";
    $calendar .= "</table>";

    return $calendar;
}
?>


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display&display=swap" rel="stylesheet">
</head>
<body>
    <header>
        <div class="sidebar">
            <div class="logo">
                <img src="../src/web_logo-removebg-preview.png" alt="Carillo Law Office">
                <span>Carillo Law Admin</span>
            </div>
            <nav>
                <a href="#">Dashboard</a>
                <a href="appointments.php">Appointments</a>
                <a href="manage_users.php">Manage Users</a>
                <a href="logout.php" class="logout-btn" id="logoutLink"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </nav>
        </div>
    </header>

    <main>
        <div class="dashboard">
            <section class="welcome">
                <h1>Welcome!</h1>
                <p>Your dashboard overview.</p>
            </section>

            <section class="quick-stats">
                <div class="stat-card">
                    <h3>Pending Consultations</h3>
                    <p><?php echo $pending_count; ?></p>
                </div>
                <div class="stat-card">
                    <h3>Scheduled Consultations</h3>
                    <p><?php echo $scheduled_count; ?></p>
                </div>
                <div class="stat-card">
                    <h3>Active Users</h3>
                    <p><?php echo $active_users; ?></p>
                </div>
            </section>

            <section class="report-generation">
                <h2>Generate Reports</h2>
                <form action="generate_report.php" method="GET" class="report-form">
                    <div class="form-group">
                        <label for="report_type">Report Type:</label>
                        <select name="type" id="report_type" required>
                            <option value="consultations">Consultations Report</option>
                            <option value="users">Users Report</option>
                            <option value="activities">User Activities Report</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="start_date">Start Date:</label>
                        <input type="date" name="start_date" id="start_date" value="<?php echo date('Y-m-d', strtotime('-30 days')); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="end_date">End Date:</label>
                        <input type="date" name="end_date" id="end_date" value="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                    <div class="form-group" style="align-self: end; margin-top: 8px;">
                        <button type="submit" class="btn generate-btn">Generate PDF Report</button>
                    </div>
                </form>
            </section>

            <section class="recent-activity">
                <h2>Recent Pending Consultations</h2>
                <ul>
                    <?php foreach ($latest_consults as $consult): ?>
                        <li><?php echo htmlspecialchars($consult['full_name']); ?> requested consultation on <?php echo htmlspecialchars($consult['preferred_date']); ?></li>
                    <?php endforeach; ?>
                </ul>
            </section>

            <div class="calendar-container">
                <?php
                $prevMonth = $month - 1;
                $prevYear = $year;
                if ($prevMonth < 1) {
                    $prevMonth = 12;
                    $prevYear--;
                }

                $nextMonth = $month + 1;
                $nextYear = $year;
                if ($nextMonth > 12) {
                    $nextMonth = 1;
                    $nextYear++;
                }

                echo "<div class='calendar-nav'>";
                echo "<a href='?month=$prevMonth&year=$prevYear'>&laquo; Previous</a>";
                echo "<a href='?month=$nextMonth&year=$nextYear'>Next &raquo;</a>";
                echo "</div>";

                echo build_calendar($month, $year, $consultations_by_date);
                ?>
            </div>

            <div id="client-modal" class="modal">
                <div class="modal-content">
                    <span class="close">&times;</span>
                    <h3>Scheduled Clients</h3>
                    <div class="appointment-list">
                        <div class="appointment-actions">
                            <button id="approve-selected" class="btn approve">Approve Selected</button>
                            <button id="reject-selected" class="btn reject">Reject Selected</button>
                        </div>
                        <ul id="client-list"></ul>
                    </div>
                </div>
            </div>

            <section class="quick-actions">
                <h2>Quick Actions</h2>
                <div class="actions">
                    <a href="appointments.php" class="btn">Manage Appointments</a>
                    <a href="manage_users.php" class="btn">Manage Users</a>
                </div>
            </section>
        </div>
    </main>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const modal = document.getElementById("client-modal");
        const clientList = document.getElementById("client-list");
        const closeBtn = document.querySelector(".modal .close");
        const approveSelectedBtn = document.getElementById("approve-selected");
        const rejectSelectedBtn = document.getElementById("reject-selected");
        let currentDate = null;
        const logoutLink = document.getElementById("logoutLink");

        if (logoutLink) {
        logoutLink.addEventListener('click', function (e) {
            e.preventDefault();
            const confirmed = confirm("Are you sure you want to log out?");
            if (confirmed) {
                window.location.href = "logout.php";
            }
        });
    }

        document.querySelectorAll(".calendar td.highlight").forEach(cell => {
            cell.addEventListener("mouseenter", function () {
                const date = this.dataset.date;
                currentDate = date;
                
                // Fetch fresh data from server
                fetch(`get_appointments.php?date=${date}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.length === 0) {
                            // If no appointments, remove highlight and close modal
                            this.classList.remove('highlight');
                            this.dataset.clients = '';
                            modal.style.display = "none";
                            return;
                        }

                        // Update cell's data
                        this.dataset.clients = JSON.stringify(data);
                        
                        // Update modal content
                        clientList.innerHTML = "";
                        data.forEach(c => {
                            const li = document.createElement("li");
                            li.className = "appointment-item";
                            li.innerHTML = `
                                <div class="appointment-content">
                                    <input type="checkbox" class="appointment-checkbox" data-id="${c.id}">
                                    <div class="appointment-details">
                                        <strong>${c.name}</strong>
                                        <span class="appointment-time">at ${c.time}</span>
                                    </div>
                                </div>
                            `;
                            clientList.appendChild(li);
                        });

                        const rect = this.getBoundingClientRect();
                        modal.style.left = `${rect.left + window.scrollX + 20}px`;
                        modal.style.top = `${rect.top + window.scrollY + 20}px`;
                        modal.style.display = "block";
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
            });

            cell.addEventListener("mouseleave", function(e) {
                // Check if the mouse is moving to the modal
                const toElement = e.relatedTarget;
                if (!toElement || !modal.contains(toElement)) {
                    modal.style.display = "none";
                    currentDate = null;
                }
            });
        });

        // Add hover functionality to the modal
        modal.addEventListener("mouseenter", function() {
            // Keep modal visible when mouse enters it
        });

        modal.addEventListener("mouseleave", function(e) {
            // Check if the mouse is moving to the calendar cell
            const toElement = e.relatedTarget;
            if (!toElement || !toElement.classList.contains('highlight')) {
                modal.style.display = "none";
                currentDate = null;
            }
        });

        closeBtn.onclick = function () {
            modal.style.display = "none";
            currentDate = null;
        };

        function getSelectedAppointments() {
            const checkboxes = document.querySelectorAll('.appointment-checkbox:checked');
            return Array.from(checkboxes).map(cb => cb.dataset.id);
        }

        approveSelectedBtn.onclick = function() {
            const selectedIds = getSelectedAppointments();
            if (selectedIds.length > 0) {
                updateAppointmentStatus(selectedIds, 'approved');
            } else {
                alert('Please select at least one appointment to approve');
            }
        };

        rejectSelectedBtn.onclick = function() {
            const selectedIds = getSelectedAppointments();
            if (selectedIds.length > 0) {
                updateAppointmentStatus(selectedIds, 'rejected');
            } else {
                alert('Please select at least one appointment to reject');
            }
        };

        function updateAppointmentStatus(consultationIds, status) {
            // First update the UI to show immediate feedback
            const cells = document.querySelectorAll('.calendar td.highlight');
            cells.forEach(cell => {
                if (cell.dataset.date === currentDate) {
                    const clients = JSON.parse(cell.dataset.clients || "[]");
                    const updatedClients = clients.filter(c => !consultationIds.includes(c.id));
                    
                    // Update the cell's data immediately
                    if (updatedClients.length === 0) {
                        cell.classList.remove('highlight');
                        cell.dataset.clients = '';
                    } else {
                        cell.dataset.clients = JSON.stringify(updatedClients);
                    }

                    // Update the modal list immediately
                    clientList.innerHTML = "";
                    updatedClients.forEach(c => {
                        const li = document.createElement("li");
                        li.className = "appointment-item";
                        li.innerHTML = `
                            <div class="appointment-content">
                                <input type="checkbox" class="appointment-checkbox" data-id="${c.id}">
                                <div class="appointment-details">
                                    <strong>${c.name}</strong>
                                    <span class="appointment-time">at ${c.time}</span>
                                </div>
                            </div>
                        `;
                        clientList.appendChild(li);
                    });

                    // Update pending count immediately
                    const pendingCountElement = document.querySelector('.stat-card:first-child p');
                    if (pendingCountElement) {
                        const currentCount = parseInt(pendingCountElement.textContent);
                        pendingCountElement.textContent = Math.max(0, currentCount - consultationIds.length);
                    }

                    // Close modal if no appointments left
                    if (updatedClients.length === 0) {
                        modal.style.display = "none";
                    }
                }
            });

            // Then send the update to the server
            const promises = consultationIds.map(id => 
                fetch('update_appointment_status.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `consultation_id=${id}&status=${status}`
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (!data.success) {
                        throw new Error(data.message || 'Failed to update status');
                    }
                    return data;
                })
            );

            Promise.all(promises)
                .then(results => {
                    // Show success message
                    alert(`Selected appointments ${status} successfully`);
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error updating appointment status: ' + error.message);
                    // Refresh the page on error to ensure consistency
                    window.location.reload();
                });
        }

        // Report form logic for date fields
        const reportType = document.getElementById('report_type');
        const startDateGroup = document.getElementById('start_date').closest('.form-group');
        const endDateGroup = document.getElementById('end_date').closest('.form-group');
        const startDateInput = document.getElementById('start_date');
        const endDateInput = document.getElementById('end_date');

        function updateDateFields() {
            if (reportType.value === 'users') {
                startDateGroup.style.display = 'none';
                endDateGroup.style.display = 'none';
                startDateInput.required = false;
                endDateInput.required = false;
            } else {
                startDateGroup.style.display = '';
                endDateGroup.style.display = '';
                startDateInput.required = true;
                endDateInput.required = true;
            }
        }
        reportType.addEventListener('change', updateDateFields);
        updateDateFields(); // Initial call
    });
</script>

<style>
    .appointment-list {
        margin-top: 10px;
    }

    .appointment-actions {
        padding: 10px 0;
        margin-bottom: 10px;
        display: flex;
        gap: 8px;
        justify-content: center;
        border-bottom: 1px solid #eee;
    }

    .appointment-item {
        padding: 8px;
        border-bottom: 1px solid #eee;
        display: flex;
        align-items: center;
    }

    .appointment-item:last-child {
        border-bottom: none;
    }

    .appointment-content {
        display: flex;
        align-items: center;
        gap: 8px;
        width: 100%;
    }

    .appointment-details {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .appointment-time {
        color: #666;
        font-size: 0.85em;
    }

    .appointment-checkbox {
        margin: 0;
        width: 16px;
        height: 16px;
    }

    .modal {
        display: none;
        position: absolute;
        background-color: white;
        padding: 15px;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        z-index: 1000;
        min-width: 280px;
        max-width: 320px;
    }

    .modal h3 {
        margin: 0 0 10px 0;
        color: #333;
        text-align: center;
        font-size: 1.1em;
    }

    .close {
        position: absolute;
        right: 10px;
        top: 10px;
        cursor: pointer;
        font-size: 18px;
        color: #666;
        width: 20px;
        height: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
    }

    .close:hover {
        background-color: #f0f0f0;
        color: #333;
    }

    .btn {
        padding: 6px 12px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-weight: 500;
        font-size: 0.9em;
        transition: all 0.2s ease;
        white-space: nowrap;
    }

    .btn.approve {
        background-color: #4CAF50;
        color: white;
    }

    .btn.reject {
        background-color: #f44336;
        color: white;
    }

    .btn:hover {
        opacity: 0.9;
        transform: translateY(-1px);
    }

    .btn:active {
        transform: translateY(0);
    }

    /* THEME-MATCHED Report Form Styling */
    .report-generation {
        background: #fff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
        margin: 20px 0;
    }
    .report-generation h2 {
        color: #122D34;
        margin-bottom: 20px;
        font-family: 'Playfair Display', serif;
        font-size: 1.5rem;
    }
    .report-form {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 20px;
        align-items: end;
    }
    .report-form .form-group {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }
    .report-form label {
        font-weight: 600;
        color: #122D34;
        font-size: 0.97rem;
        margin-bottom: 2px;
    }
    .report-form input[type="date"],
    .report-form select {
        padding: 10px 12px;
        border: 1px solid #ddd;
        border-radius: 5px;
        font-size: 0.97rem;
        background-color: #fff;
        color: #333;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .report-form input[type="date"]:hover,
    .report-form select:hover {
        border-color: #f9a825;
    }
    .report-form input[type="date"]:focus,
    .report-form select:focus {
        outline: none;
        border-color: #f9a825;
        box-shadow: 0 0 0 2px rgba(249, 168, 37, 0.15);
    }
    .btn.generate-btn {
        background-color: #f9a825;
        color: white;
        border: none;
        padding: 12px 20px;
        border-radius: 5px;
        cursor: pointer;
        font-weight: 600;
        font-size: 0.97rem;
        transition: background-color 0.2s, box-shadow 0.2s, transform 0.2s;
        height: 42px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        box-shadow: 0 2px 8px rgba(249, 168, 37, 0.07);
    }
    .btn.generate-btn:hover {
        background-color: #e68900;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(230, 137, 0, 0.10);
    }
    .btn.generate-btn:active {
        transform: translateY(0);
        box-shadow: 0 2px 4px rgba(230, 137, 0, 0.10);
    }
    .btn.generate-btn::before {
        content: "\1F4C8"; /* chart emoji */
        font-size: 1.1rem;
        margin-right: 6px;
    }
    @media (max-width: 768px) {
        .report-form {
            grid-template-columns: 1fr;
        }
        .btn.generate-btn {
            width: 100%;
            margin-top: 10px;
        }
    }
</style>

</body>
</html>

