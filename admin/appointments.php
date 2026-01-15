<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

$db = Database::getInstance();
$conn = $db->getConnection();

// Handle status updates
if (isset($_POST['action']) && isset($_POST['consultation_id'])) {
    $consultation_id = $_POST['consultation_id'];
    $new_status = $_POST['action'] === 'complete' ? 'completed' : 'rejected';
    
    $stmt = $conn->prepare("UPDATE consultations SET status = ? WHERE consultation_id = ?");
    $stmt->bind_param("si", $new_status, $consultation_id);
    
    if ($stmt->execute()) {
        // Redirect to refresh the page and show updated status
        header("Location: " . $_SERVER['PHP_SELF'] . "?status=" . urlencode($status_filter) . "&date=" . urlencode($date_filter));
        exit();
    }
}

// Get filter parameters
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';
$date_filter = isset($_GET['date']) ? $_GET['date'] : '';

// Build the query
$query = "SELECT consultation_id, full_name, email, phone, preferred_date, status, legal_issue, message 
          FROM consultations WHERE 1=1";
if ($status_filter !== 'all') {
    $query .= " AND status = '$status_filter'";
}
if ($date_filter) {
    $query .= " AND DATE(preferred_date) = '$date_filter'";
}
$query .= " ORDER BY preferred_date DESC";

$result = $conn->query($query);
$appointments = $result->fetch_all(MYSQLI_ASSOC);

// Get counts for each status
$status_counts = [
    'pending' => 0,
    'completed' => 0,
    'rejected' => 0,
    'cancelled' => 0
];

$count_query = "SELECT status, COUNT(*) as count FROM consultations GROUP BY status";
$count_result = $conn->query($count_query);
while ($row = $count_result->fetch_assoc()) {
    $status_counts[$row['status']] = $row['count'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Appointments - Admin Dashboard</title>
    <link rel="stylesheet" href="admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display&display=swap" rel="stylesheet">
    <style>
        .appointments-container {
            padding: 20px;
        }

        .appointments-container h1 {
            padding: 20px 0;
            margin: 0;
            color: #333;
            font-size: 1.8em;
        }
        .invisible-btn {
            visibility: hidden;
            pointer-events: none;
        }
        .filters {
            margin-bottom: 20px;
            display: flex;
            gap: 20px;
            align-items: center;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .filters form {
            display: flex;
            gap: 20px;
            align-items: center;
            width: 100%;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .filter-group label {
            font-size: 0.9em;
            color: #666;
            font-weight: 500;
        }

        .filters select {
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 6px;
            background-color: white;
            font-size: 1em;
            color: #333;
            cursor: pointer;
            min-width: 200px;
            appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 10px center;
            background-size: 1em;
        }

        .filters select:hover {
            border-color: #999;
        }

        .filters select:focus {
            outline: none;
            border-color: #4a90e2;
            box-shadow: 0 0 0 2px rgba(74, 144, 226, 0.2);
        }

        .filters input[type="date"] {
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 6px;
            background-color: white;
            font-size: 1em;
            color: #333;
            cursor: pointer;
            min-width: 200px;
        }

        .filters input[type="date"]:hover {
            border-color: #999;
        }

        .filters input[type="date"]:focus {
            outline: none;
            border-color: #4a90e2;
            box-shadow: 0 0 0 2px rgba(74, 144, 226, 0.2);
        }

        .appointments-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .appointments-table th, .appointments-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        .appointments-table th {
            background-color: #f8f9fa;
            font-weight: 600;
            color: #333;
        }

        .appointments-table tr:hover {
            background-color: #f8f9fa;
        }

        .status-pending {
            color: #f39c12;
            font-weight: 500;
        }

        .status-completed {
            color: #27ae60;
            font-weight: 500;
        }

        .status-rejected {
            color: #e74c3c;
            font-weight: 500;
        }

        .status-cancelled {
            color: #95a5a6;
            font-weight: 500;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
        }

        .btn-container {
        height: 40px; /* fixed height for the button area */
        display: flex;
        align-items: center; /* vertically center buttons */
        gap: 8px; /* space between buttons */
        }

        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            color: white;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .invisible-btn {
        visibility: hidden;
        pointer-events: none;
        height: 32px; /* keep same height as buttons */
        padding: 0 12px;
        }

        .btn-complete {
            background-color: #27ae60;
        }

        .btn-reject {
            background-color: #e74c3c;
        }

        .btn:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }

        .btn:active {
            transform: translateY(0);
        }

        .message-cell {
            max-width: 200px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .message-cell:hover {
            white-space: normal;
            overflow: visible;
        }

        .status-badges {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .status-badge {
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 0.9em;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .status-badge.pending {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-badge.completed {
            background-color: #d4edda;
            color: #155724;
        }

        .status-badge.approved {
            background-color: #d1ecf1;
            color: #856404;
        }
        .status-badge.rejected {
            background-color: #f8d7da;
            color: #721c24;
        }

        .status-badge.cancelled {
            background-color: #e2e3e5;
            color: #383d41;
        }

        .status-badge .count {
            background-color: rgba(0, 0, 0, 0.1);
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 0.9em;
        }
    </style>
</head>
<body>
    <header>
        <div class="sidebar">
            <div class="logo">
                <img src="../src/web_logo-removebg-preview.png" alt="Carillo Law Office">
                <span>Carillo Law Admin</span>
            </div>
            <nav>
                <a href="admin-home.php">Dashboard</a>
                <a href="appointments.php" class="active">Appointments</a>
                <a href="manage_users.php">Manage Users</a>
                <a href="logout.php" class="logout-btn" id="logoutLink"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </nav>
        </div>
    </header>

    <main>
        <div class="appointments-container">
            <h1>Manage Appointments</h1>

            <div class="status-badges">
                <div class="status-badge pending">
                    Pending <span class="count"><?php echo $status_counts['pending']; ?></span>
                </div>
                <div class="status-badge approved">
                    Approved <span class="count"><?php echo $status_counts['approved']; ?></span>
                </div>
                <div class="status-badge completed">
                    Completed <span class="count"><?php echo $status_counts['completed']; ?></span>
                </div>
                <div class="status-badge rejected">
                    Rejected <span class="count"><?php echo $status_counts['rejected']; ?></span>
                </div>
                <div class="status-badge cancelled">
                    Cancelled <span class="count"><?php echo $status_counts['cancelled']; ?></span>
                </div>
            </div>

            <div class="filters">
                <form method="GET" action="">
                    <div class="filter-group">
                        <label for="status">Filter by Status</label>
                        <select name="status" id="status" onchange="this.form.submit()">
                            <option value="all" <?php echo $status_filter === 'all' ? 'selected' : ''; ?>>All Status</option>
                            <option value="pending" <?php echo $status_filter === 'pending' ? 'selected' : ''; ?>>Pending</option>
                            <option value="completed" <?php echo $status_filter === 'completed' ? 'selected' : ''; ?>>Completed</option>
                            <option value="rejected" <?php echo $status_filter === 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                            <option value="cancelled" <?php echo $status_filter === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label for="date">Filter by Date</label>
                        <input type="date" id="date" name="date" value="<?php echo $date_filter; ?>" onchange="this.form.submit()">
                    </div>
                </form>
            </div>

            <table class="appointments-table">
                <thead>
                    <tr>
                        <th>Client Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Legal Issue</th>
                        <th>Preferred Date</th>
                        <th>Message</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($appointments as $appointment): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($appointment['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($appointment['email']); ?></td>
                        <td><?php echo htmlspecialchars($appointment['phone']); ?></td>
                        <td><?php echo htmlspecialchars($appointment['legal_issue']); ?></td>
                        <td><?php echo date('F j, Y g:i A', strtotime($appointment['preferred_date'])); ?></td>
                        <td class="message-cell"><?php echo htmlspecialchars($appointment['message']); ?></td>
                        <td class="status-<?php echo $appointment['status']; ?>">
                            <?php echo ucfirst($appointment['status']); ?>
                        </td>
                        <td>
                        <?php if ($appointment['status'] == 'pending' || $appointment['status'] == 'approved'): ?>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="consultation_id" value="<?php echo htmlspecialchars($appointment['consultation_id']); ?>">
                                <div class="btn-container">
                                    <button type="submit" name="action" value="complete" class="btn btn-complete">Complete</button>
                                    <?php if ($appointment['status'] == 'pending'): ?>
                                        <button type="submit" name="action" value="reject" class="btn btn-reject">Reject</button>
                                    <?php else: ?>
                                        <button type="button" class="btn btn-reject invisible-btn">Reject</button>
                                    <?php endif; ?>
                                </div>
                            </form>
                        <?php else: ?>
                            <div class="btn-container">
                                <!-- Empty placeholder div to keep height -->
                                <button type="button" class="btn invisible-btn">Placeholder</button>
                                <button type="button" class="btn invisible-btn">Placeholder</button>
                            </div>
                        <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
        console.log('Page loaded, initializing...'); // Debug log
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