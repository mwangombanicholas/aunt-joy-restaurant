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

<h1>Our Delicious Menu</h1>

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
            <h2 style="color: #2c3e50; border-bottom: 3px solid #e74c3c; padding-bottom: 0.5rem; margin-bottom: 2rem;"><?php echo htmlspecialchars($category); ?></h2>
            <div class="menu-grid">
                <?php foreach ($categoryMeals as $meal): ?>
                    <div class="meal-card" style="background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.1); transition: transform 0.3s;">
                        <div class="meal-image" style="position: relative;">
                            <?php if ($meal['image_url']): ?>
                                <img src="../assets/images/meals/<?php echo $meal['image_url']; ?>" 
                                     alt="<?php echo htmlspecialchars($meal['name']); ?>" 
                                     style="width: 100%; height: 200px; object-fit: cover;">
                            <?php else: ?>
                                <div style="background: linear-gradient(45deg, #f39c12, #e74c3c); height: 200px; display: flex; align-items: center; justify-content: center; border-radius: 4px;">
                                    <span style="color: white; font-size: 1.5rem;">🍽️</span>
                                </div>
                            <?php endif; ?>
                            <?php if (!$meal['is_available']): ?>
                                <div style="position: absolute; top: 10px; right: 10px; background: #e74c3c; color: white; padding: 0.5rem; border-radius: 5px; font-size: 0.8rem;">
                                    Out of Stock
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div style="padding: 1.5rem;">
                            <h3 style="color: #2c3e50; margin-bottom: 0.5rem; font-size: 1.2rem;"><?php echo htmlspecialchars($meal['name']); ?></h3>
                            <p class="meal-description" style="color: #666; margin-bottom: 1rem; font-size: 0.9rem; line-height: 1.4;"><?php echo htmlspecialchars($meal['description']); ?></p>
                            <div class="meal-price" style="font-size: 1.5rem; font-weight: bold; color: #27ae60; margin: 1rem 0;">MK <?php echo number_format($meal['price'], 2); ?></div>
                            
                            <?php if ($meal['is_available']): ?>
                                <form method="POST" action="">
                                    <input type="hidden" name="meal_id" value="<?php echo $meal['id']; ?>">
                                    <input type="hidden" name="add_to_cart" value="1">
                                    <div class="form-group" style="margin-bottom: 1rem;">
                                        <label for="quantity_<?php echo $meal['id']; ?>" style="display: block; margin-bottom: 0.5rem; font-weight: bold;">Quantity:</label>
                                        <input type="number" id="quantity_<?php echo $meal['id']; ?>" name="quantity" value="1" min="1" max="10" style="width: 100%; padding: 0.75rem; border: 2px solid #ddd; border-radius: 5px; font-size: 1rem;">
                                    </div>
                                    <button type="submit" class="btn" style="background: #e74c3c; width: 100%; padding: 1rem; font-size: 1.1rem; border: none; border-radius: 5px; cursor: pointer;">
                                        🛒 Add to Cart
                                    </button>
                                </form>
                            <?php else: ?>
                                <button disabled style="background: #95a5a6; color: white; width: 100%; padding: 1rem; font-size: 1.1rem; border: none; border-radius: 5px; cursor: not-allowed;">
                                    Currently Unavailable
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<style>
.meal-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.15);
}

.menu-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 2rem;
}

.category-section {
    margin-bottom: 4rem;
}

/* Responsive Design */
@media (max-width: 768px) {
    .menu-grid {
        grid-template-columns: 1fr;
    }
    
    .meal-card {
        margin-bottom: 1rem;
    }
}
</style>

<?php include '../includes/footer.php'; ?>