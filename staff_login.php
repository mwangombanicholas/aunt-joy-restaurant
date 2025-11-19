<?php
include 'includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    $database = new Database();
    $db = $database->getConnection();
    
    $query = "SELECT * FROM users WHERE username = :username AND role IN ('admin', 'manager', 'sales')";
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
            
            switch ($user['role']) {
                case 'admin': header('Location: admin/index.php'); break;
                case 'manager': header('Location: manager/dashboard.php'); break;
                case 'sales': header('Location: sales/orders.php'); break;
            }
            exit();
        }
    }
    $error = "Invalid staff credentials!";
}
?>

<?php include 'includes/header.php'; ?>
<div class="form-container">
    <h2>🔐 Staff Login</h2>
    <p style="text-align: center; color: #666; margin-bottom: 2rem;">Administrators, Managers & Sales Personnel</p>
    <!-- Same form but different styling -->
</div>
<?php include 'includes/footer.php'; ?>