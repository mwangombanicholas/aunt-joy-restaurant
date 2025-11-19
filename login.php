<?php
include 'includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    $database = new Database();
    $db = $database->getConnection();
    
    $query = "SELECT * FROM users WHERE username = :username";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    
    if ($stmt->rowCount() == 1) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['full_name'] = $user['full_name'];
            
            // Show role-specific welcome message
            $welcome_messages = [
                'customer' => 'Enjoy your meal ordering experience!',
                'admin' => 'Welcome to System Administration',
                'manager' => 'Access sales reports and analytics',
                'sales' => 'Manage customer orders and updates'
            ];
            
            $_SESSION['welcome_message'] = $welcome_messages[$user['role']];
            
            // Redirect based on role
            switch ($user['role']) {
                case 'admin': header('Location: admin/index.php'); break;
                case 'manager': header('Location: manager/dashboard.php'); break;
                case 'sales': header('Location: sales/orders.php'); break;
                case 'customer': header('Location: customer/menu.php'); break;
            }
            exit();
        }
    }
    $error = "Invalid username or password!";
}
?>

<?php include 'includes/header.php'; ?>

<div class="form-container">
    <h2>Login to Your Account</h2>
    
    <?php if (isset($error)): ?>
        <div style="background: #ffebee; color: #c62828; padding: 1rem; margin-bottom: 1rem; border-radius: 4px;">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>
    
    <form method="POST" action="">
        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
        </div>
        
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
        </div>
        
        <button type="submit" class="btn" style="width: 100%;">Login</button>
    </form>
    
    <p style="margin-top: 1rem; text-align: center;">
        Don't have an account? <a href="register.php">Register here</a>
    </p>
</div>

<?php include 'includes/footer.php'; ?>