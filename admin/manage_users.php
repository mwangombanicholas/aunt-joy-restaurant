<?php
include '../includes/config.php';
include '../includes/auth.php';
checkAuth('admin');

$database = new Database();
$db = $database->getConnection();

// Get all users
$query = "SELECT * FROM users ORDER BY created_at DESC";
$stmt = $db->prepare($query);
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include 'header.php'; ?>

<h1>Manage Users</h1>

<div style="background: white; border-radius: 8px; overflow: hidden;">
    <table class="table">
        <thead>
            <tr>
                <th>Username</th>
                <th>Email</th>
                <th>Full Name</th>
                <th>Role</th>
                <th>Phone</th>
                <th>Registered</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td>
                        <strong><?php echo htmlspecialchars($user['username']); ?></strong>
                        <?php if ($user['id'] == $_SESSION['user_id']): ?>
                            <span style="background: #3498db; color: white; padding: 0.2rem 0.5rem; border-radius: 12px; font-size: 0.7rem; margin-left: 0.5rem;">You</span>
                        <?php endif; ?>
                    </td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                    <td>
                        <span style="background: 
                            <?php 
                                switch($user['role']) {
                                    case 'admin': echo '#e74c3c'; break;
                                    case 'manager': echo '#f39c12'; break;
                                    case 'sales': echo '#3498db'; break;
                                    default: echo '#27ae60';
                                }
                            ?>; 
                            color: white; padding: 0.3rem 0.8rem; border-radius: 15px; font-size: 0.8rem;">
                            <?php echo ucfirst($user['role']); ?>
                        </span>
                    </td>
                    <td><?php echo htmlspecialchars($user['phone'] ?? 'N/A'); ?></td>
                    <td><?php echo date('M j, Y', strtotime($user['created_at'])); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div style="margin-top: 2rem; padding: 1.5rem; background: #f8f9fa; border-radius: 8px;">
    <h3>User Roles:</h3>
    <ul style="margin-top: 1rem;">
        <li><strong>Admin:</strong> Full access to all features</li>
        <li><strong>Manager:</strong> Access to reports and analytics</li>
        <li><strong>Sales:</strong> Can view and update orders</li>
        <li><strong>Customer:</strong> Can browse menu and place orders</li>
    </ul>
</div>

<?php include '../includes/footer.php'; ?>