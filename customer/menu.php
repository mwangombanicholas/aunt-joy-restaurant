<?php
include '../includes/config.php';
include '../includes/auth.php';
checkAuth('customer');

$database = new Database();
$db = $database->getConnection();

// Handle Add to Cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $meal_id = $_POST['meal_id'];
    $quantity = $_POST['quantity'];
    
    // Initialize cart if not exists
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    
    // Add item to cart or update quantity
    if (isset($_SESSION['cart'][$meal_id])) {
        $_SESSION['cart'][$meal_id] += $quantity;
    } else {
        $_SESSION['cart'][$meal_id] = $quantity;
    }
    
    header('Location: menu.php?success=1');
    exit();
}

// Get all available meals with their categories
$query = "SELECT m.*, c.name as category_name 
          FROM meals m 
          LEFT JOIN categories c ON m.category_id = c.id 
          WHERE m.is_available = 1 
          ORDER BY c.name, m.name";
$stmt = $db->prepare($query);
$stmt->execute();
$meals = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Group meals by category
$mealsByCategory = [];
foreach ($meals as $meal) {
    $mealsByCategory[$meal['category_name']][] = $meal;
}
?>

<?php include 'header.php'; ?>

<!-- WELCOME MESSAGE SECTION -->
<?php if (isset($_SESSION['welcome_message'])): ?>
    <div style="background: #e8f5e8; padding: 1rem; border-radius: 4px; margin-bottom: 1rem; border-left: 4px solid #27ae60;">
        <strong>👋 Welcome, <?php echo $_SESSION['full_name']; ?>!</strong><br>
        <?php echo $_SESSION['welcome_message']; ?>
    </div>
    <?php unset($_SESSION['welcome_message']); ?>
<?php endif; ?>

<h1>Our Menu</h1>

<?php if (isset($_GET['success'])): ?>
    <div style="background: #e8f5e8; color: #2e7d32; padding: 1rem; margin-bottom: 1rem; border-radius: 4px;">
        ✅ Item added to cart successfully! <a href="cart.php" style="color: #2e7d32; text-decoration: underline;">View Cart</a>
    </div>
<?php endif; ?>

<div style="margin-bottom: 2rem; text-align: right;">
    <a href="cart.php" class="btn" style="background: #27ae60;">
        🛒 View Cart 
        <?php if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0): ?>
            (<?php echo array_sum($_SESSION['cart']); ?> items)
        <?php endif; ?>
    </a>
</div>

<div class="menu-categories">
    <?php foreach ($mealsByCategory as $category => $categoryMeals): ?>
        <div class="category-section">
            <h2><?php echo htmlspecialchars($category); ?></h2>
            <div class="menu-grid">
                <?php foreach ($categoryMeals as $meal): ?>
                    <div class="meal-card">
                        <div class="meal-image">
                            <?php if ($meal['image_url']): ?>
                                <img src="../assets/images/meals/<?php echo $meal['image_url']; ?>" alt="<?php echo htmlspecialchars($meal['name']); ?>">
                            <?php else: ?>
                                <div style="background: #ddd; height: 200px; display: flex; align-items: center; justify-content: center; border-radius: 4px;">
                                    🍽️ No Image
                                </div>
                            <?php endif; ?>
                        </div>
                        <h3><?php echo htmlspecialchars($meal['name']); ?></h3>
                        <p class="meal-description"><?php echo htmlspecialchars($meal['description']); ?></p>
                        <div class="meal-price">MK <?php echo number_format($meal['price'], 2); ?></div>
                        
                        <form method="POST" action="">
                            <input type="hidden" name="meal_id" value="<?php echo $meal['id']; ?>">
                            <input type="hidden" name="add_to_cart" value="1">
                            <div class="form-group">
                                <label for="quantity_<?php echo $meal['id']; ?>">Quantity:</label>
                                <input type="number" id="quantity_<?php echo $meal['id']; ?>" name="quantity" value="1" min="1" max="10" style="width: 80px;">
                            </div>
                            <button type="submit" class="btn">🛒 Add to Cart</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<?php include '../includes/footer.php'; ?>