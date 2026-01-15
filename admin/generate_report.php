<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

// Get report type and date range from request
$report_type = $_GET['type'] ?? '';
$start_date = $_GET['start_date'] ?? date('Y-m-d', strtotime('-30 days'));
$end_date = $_GET['end_date'] ?? date('Y-m-d');

try {
    $db = Database::getInstance();
    $conn = $db->getConnection();

    // Start HTML output
    header('Content-Type: text/html; charset=utf-8');
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Report - <?php echo ucfirst($report_type); ?></title>
        <style>
            @media print {
                @page {
                    size: A4;
                    margin: 2cm;
                }
                body {
                    margin: 0;
                    padding: 0;
                }
                .no-print {
                    display: none !important;
                }
                table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-bottom: 20px;
                }
                th, td {
                    border: 1px solid #000;
                    padding: 8px;
                    text-align: left;
                }
                th {
                    background-color: #f0f0f0 !important;
                    -webkit-print-color-adjust: exact;
                    print-color-adjust: exact;
                }
            }
            body {
                font-family: Arial, sans-serif;
                margin: 20px;
                padding: 20px;
            }
            .header {
                text-align: center;
                margin-bottom: 30px;
            }
            .header h1 {
                margin: 0;
                color: #333;
            }
            .header p {
                margin: 5px 0;
                color: #666;
            }
            .print-button {
                background-color: #4CAF50;
                color: white;
                padding: 10px 20px;
                border: none;
                border-radius: 4px;
                cursor: pointer;
                margin-bottom: 20px;
            }
            .print-button:hover {
                background-color: #45a049;
            }
            table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 20px;
            }
            th, td {
                border: 1px solid #ddd;
                padding: 12px;
                text-align: left;
            }
            th {
                background-color: #f5f5f5;
                font-weight: bold;
            }
            tr:nth-child(even) {
                background-color: #f9f9f9;
            }
            .date-range {
                color: #666;
                margin-bottom: 20px;
            }
        </style>
    </head>
    <body>
        <div class="header">
            <h1>Carillo Law Office</h1>
            <p><?php echo ucfirst($report_type); ?> Report</p>
<?php if ($report_type !== 'users'): ?>
            <p class="date-range">Period: <?php echo date('F d, Y', strtotime($start_date)); ?> to <?php echo date('F d, Y', strtotime($end_date)); ?></p>
<?php endif; ?>
            <p>Generated on: <?php echo date('F d, Y H:i:s'); ?></p>
        </div>

        <button onclick="window.print()" class="print-button no-print">Print Report</button>

        <?php
        switch ($report_type) {
            case 'consultations':
                // Get consultation data for exact dates only
                $query = "SELECT c.*, u.username 
                         FROM consultations c 
                         LEFT JOIN users u ON c.uid = u.uid 
                         WHERE DATE(c.preferred_date) BETWEEN ? AND ? 
                         ORDER BY c.preferred_date DESC";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("ss", $start_date, $end_date);
                $stmt->execute();
                $result = $stmt->get_result();
                ?>
                <table>
                    <thead>
                        <tr>
                            <th>Date & Time</th>
                            <th>Client Name</th>
                            <th>Status</th>
                            <th>Legal Issue</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo date('Y-m-d H:i', strtotime($row['preferred_date'])); ?></td>
                                <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                                <td><?php echo ucfirst($row['status']); ?></td>
                                <td><?php echo htmlspecialchars($row['legal_issue']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                <?php
                break;

            case 'users':
                // Get all user data without date filtering
                $query = "SELECT u.*, 
                         COUNT(DISTINCT c.consultation_id) as consultation_count,
                         COUNT(DISTINCT a.activity_id) as activity_count
                         FROM users u 
                         LEFT JOIN consultations c ON u.uid = c.uid
                         LEFT JOIN user_activities a ON u.uid = a.uid
                         GROUP BY u.uid
                         ORDER BY u.created_at DESC";
                $stmt = $conn->prepare($query);
                $stmt->execute();
                $result = $stmt->get_result();
                ?>
                <table>
                    <thead>
                        <tr>
                            <th>Username</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Consultations</th>
                            <th>Activities</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['username']); ?></td>
                                <td><?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['email']); ?></td>
                                <td><?php echo $row['consultation_count']; ?></td>
                                <td><?php echo $row['activity_count']; ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                <?php
                break;

            case 'activities':
                // Get user activity data
                $query = "SELECT a.*, u.username, u.first_name, u.last_name 
                         FROM user_activities a 
                         JOIN users u ON a.uid = u.uid 
                         WHERE a.created_at BETWEEN ? AND ? 
                         ORDER BY a.created_at DESC";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("ss", $start_date, $end_date);
                $stmt->execute();
                $result = $stmt->get_result();
                ?>
                <table>
                    <thead>
                        <tr>
                            <th>Username</th>
                            <th>Activity Type</th>
                            <th>Description</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['username']); ?></td>
                                <td><?php echo htmlspecialchars($row['activity_type']); ?></td>
                                <td><?php echo htmlspecialchars($row['activity_description']); ?></td>
                                <td><?php echo date('Y-m-d H:i', strtotime($row['created_at'])); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                <?php
                break;

            default:
                throw new Exception("Invalid report type");
        }
        ?>
    </body>
    </html>
    <?php
} catch (Exception $e) {
    $_SESSION['error_message'] = "Error generating report: " . $e->getMessage();
    header("Location: admin-home.php");
    exit();
}
?> 