<?php
session_start();
require_once '../config/database.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

$db = Database::getInstance();
$conn = $db->getConnection();

// Handle user status updates
if (isset($_POST['action']) && isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];
    $action = $_POST['action'];
    
    if ($action === 'edit' && isset($_POST['edit_username'], $_POST['edit_email'], $_POST['edit_first_name'], $_POST['edit_last_name'], $_POST['edit_contact_number'])) {
        $username = $_POST['edit_username'];
        $email = $_POST['edit_email'];
        $first_name = $_POST['edit_first_name'];
        $last_name = $_POST['edit_last_name'];
        $contact_number = $_POST['edit_contact_number'];
        
        $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, first_name = ?, last_name = ?, contact_number = ? WHERE uid = ?");
        if ($stmt) {
            $stmt->bind_param("sssssi", $username, $email, $first_name, $last_name, $contact_number, $user_id);
            
            if ($stmt->execute()) {
                $_SESSION['edit_message'] = "User information updated successfully.";
            } else {
                $_SESSION['edit_error'] = "Error updating user information: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $_SESSION['edit_error'] = "Error preparing update statement: " . $conn->error;
        }
    } elseif ($action === 'activate') {
        $stmt = $conn->prepare("UPDATE users SET status = 'active' WHERE uid = ?");
        if ($stmt) {
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $stmt->close();
        }
    } elseif ($action === 'deactivate') {
        $stmt = $conn->prepare("UPDATE users SET status = 'inactive' WHERE uid = ?");
        if ($stmt) {
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $stmt->close();
        }
    } elseif ($action === 'delete') {
        // Verify admin password before deletion
        if (isset($_POST['admin_password'])) {
            $admin_password = $_POST['admin_password'];
            // Get admin password from database - using admin_id from session
            $admin_query = $conn->prepare("SELECT password FROM users WHERE uid = ?");
            if ($admin_query) {
                $admin_query->bind_param("i", $_SESSION['admin_id']);
                $admin_query->execute();
                $admin_result = $admin_query->get_result();
                $admin_data = $admin_result->fetch_assoc();
                $admin_query->close();
                
                if ($admin_data && password_verify($admin_password, $admin_data['password'])) {
                    // Start transaction
                    $conn->begin_transaction();
                    
                    try {
                        // Delete from consultations table - using uid instead of user_id
                        $stmt_consultations = $conn->prepare("DELETE FROM consultations WHERE uid = ?");
                        if ($stmt_consultations) {
                            $stmt_consultations->bind_param("i", $user_id);
                            $stmt_consultations->execute();
                            $stmt_consultations->close();
                        }
                        
                        // Delete from user_activities table
                        $stmt_activities = $conn->prepare("DELETE FROM user_activities WHERE uid = ?");
                        if ($stmt_activities) {
                            $stmt_activities->bind_param("i", $user_id);
                            $stmt_activities->execute();
                            $stmt_activities->close();
                        }
                        
                        // Finally delete the user
                        $stmt_user = $conn->prepare("DELETE FROM users WHERE uid = ?");
                        if ($stmt_user) {
                            $stmt_user->bind_param("i", $user_id);
                            $stmt_user->execute();
                            $stmt_user->close();
                        }
                        
                        // If all deletions successful, commit the transaction
                        $conn->commit();
                        $_SESSION['delete_message'] = "User and all associated records successfully deleted.";
                    } catch (Exception $e) {
                        // If any error occurs, rollback the transaction
                        $conn->rollback();
                        $_SESSION['delete_error'] = "Error deleting user records: " . $e->getMessage();
                    }
                } else {
                    $_SESSION['delete_error'] = "Invalid admin password.";
                }
            }
        }
    }
}

// Fetch all users
$query = "SELECT uid, username, email, first_name, last_name, contact_number, CONCAT(first_name, ' ', last_name) as full_name, created_at FROM users ORDER BY created_at DESC";
$result = $conn->query($query);
$users = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - Admin Dashboard</title>
    <link rel="stylesheet" href="admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display&display=swap" rel="stylesheet">
    <style>
        .users-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: white;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .users-table th, .users-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .users-table th {
            background-color: #f8f9fa;
            font-weight: bold;
        }

        .users-table tr:hover {
            background-color: #f5f5f5;
        }

        .status-active {
            color: #28a745;
        }

        .status-inactive {
            color: #dc3545;
        }

        .action-btn {
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-right: 5px;
            color: white;
        }

        .activate-btn {
            background-color: #28a745;
        }

        .deactivate-btn {
            background-color: #ffc107;
        }

        .delete-btn {
            background-color: #dc3545;
        }

        .action-btn:hover {
            opacity: 0.8;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .search-box {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            width: 300px;
        }

        .delete-container {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .admin-password-input {
            padding: 6px;
            border: 1px solid #ddd;
            border-radius: 4px;
            width: 150px;
        }

        .alert {
            padding: 10px;
            margin: 10px 0;
            border-radius: 4px;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .edit-btn {
            background-color: #007bff;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 50%;
            border-radius: 5px;
            position: relative;
        }

        .close-modal {
            position: absolute;
            right: 10px;
            top: 10px;
            font-size: 24px;
            cursor: pointer;
        }

        .edit-form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .form-group label {
            font-weight: bold;
        }

        .form-group input {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .submit-edit {
            background-color: #007bff;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .submit-edit:hover {
            opacity: 0.8;
        }

        .edit-form-container {
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }

        .edit-form {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .form-group label {
            font-weight: bold;
        }

        .form-group input {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .submit-edit {
            background-color: #007bff;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            grid-column: span 2;
            max-width: 200px;
            margin: 0 auto;
        }

        .submit-edit:hover {
            opacity: 0.8;
        }

        .selected-user {
            background-color: #e3f2fd !important;
        }

        .no-user-selected {
            text-align: center;
            color: #666;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 5px;
            margin-bottom: 20px;
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
                <a href="appointments.php">Appointments</a>
                <a href="manage_users.php" class="active">Manage Users</a>
                <a href="logout.php" class="logout-btn" id="logoutLink"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </nav>
        </div>
    </header>

    <main>
        <div class="dashboard">
            <div class="page-header">
                <h1>Manage Users</h1>
                <input type="text" id="searchInput" class="search-box" placeholder="Search users...">
            </div>

            <?php if (isset($_SESSION['delete_message'])): ?>
                <div class="alert alert-success">
                    <?php 
                    echo $_SESSION['delete_message'];
                    unset($_SESSION['delete_message']);
                    ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['delete_error'])): ?>
                <div class="alert alert-danger">
                    <?php 
                    echo $_SESSION['delete_error'];
                    unset($_SESSION['delete_error']);
                    ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['edit_message'])): ?>
                <div class="alert alert-success">
                    <?php 
                    echo $_SESSION['edit_message'];
                    unset($_SESSION['edit_message']);
                    ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['edit_error'])): ?>
                <div class="alert alert-danger">
                    <?php 
                    echo $_SESSION['edit_error'];
                    unset($_SESSION['edit_error']);
                    ?>
                </div>
            <?php endif; ?>

            <div id="editFormContainer" class="edit-form-container" style="display: none;">
                <h2>Edit User Information</h2>
                <form method="POST" class="edit-form">
                    <input type="hidden" name="user_id" id="edit_user_id">
                    <input type="hidden" name="action" value="edit">
                    
                    <div class="form-group">
                        <label for="edit_username">Username:</label>
                        <input type="text" id="edit_username" name="edit_username" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="edit_email">Email:</label>
                        <input type="email" id="edit_email" name="edit_email" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="edit_first_name">First Name:</label>
                        <input type="text" id="edit_first_name" name="edit_first_name" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="edit_last_name">Last Name:</label>
                        <input type="text" id="edit_last_name" name="edit_last_name" required>
                    </div>

                    <div class="form-group">
                        <label for="edit_contact_number">Contact Number:</label>
                        <input type="text" id="edit_contact_number" name="edit_contact_number" required>
                    </div>
                    
                    <button type="submit" class="submit-edit">Update User</button>
                </form>
            </div>

            <div id="noUserSelected" class="no-user-selected">
                Select a user from the table below to edit their information
            </div>

            <table class="users-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                    <tr onclick="selectUser(<?php echo htmlspecialchars(json_encode($user)); ?>)" style="cursor: pointer;">
                        <td><?php echo htmlspecialchars($user['uid']); ?></td>
                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                        <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><?php echo htmlspecialchars($user['created_at']); ?></td>
                        <td>
                            <form method="POST" class="delete-container" onclick="event.stopPropagation();">
                                <input type="hidden" name="user_id" value="<?php echo $user['uid']; ?>">
                                <input type="password" name="admin_password" class="admin-password-input" placeholder="Admin password" required>
                                <button type="submit" name="action" value="delete" class="action-btn delete-btn">Delete</button>
                            </form>
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
        // Search functionality
        document.getElementById('searchInput').addEventListener('keyup', function() {
            const searchText = this.value.toLowerCase();
            const tableRows = document.querySelectorAll('.users-table tbody tr');
            
            tableRows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchText) ? '' : 'none';
            });
        });

        function selectUser(user) {
            // Remove selected class from all rows
            document.querySelectorAll('.users-table tbody tr').forEach(row => {
                row.classList.remove('selected-user');
            });
            
            // Add selected class to clicked row
            event.currentTarget.classList.add('selected-user');
            
            // Update form fields
            document.getElementById('edit_user_id').value = user.uid;
            document.getElementById('edit_username').value = user.username;
            document.getElementById('edit_email').value = user.email;
            document.getElementById('edit_first_name').value = user.first_name;
            document.getElementById('edit_last_name').value = user.last_name;
            document.getElementById('edit_contact_number').value = user.contact_number;
            
            // Show edit form and hide no user selected message
            document.getElementById('editFormContainer').style.display = 'block';
            document.getElementById('noUserSelected').style.display = 'none';
        }
    </script>
</body>
</html> 