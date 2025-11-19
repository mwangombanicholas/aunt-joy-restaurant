<?php
function checkAuth($requiredRole = null) {
    if (!isset($_SESSION['user_id'])) {
        header("Location: ../login.php");
        exit();
    }
    
    if ($requiredRole && $_SESSION['user_role'] !== $requiredRole) {
        http_response_code(403);
        echo "Access Denied: You don't have permission to access this page.";
        exit();
    }
    return true;
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}
?>