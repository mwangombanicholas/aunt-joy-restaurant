<?php
include '../includes/config.php';
include '../includes/auth.php';
checkAuth('customer');

$database = new Database();
$db = $database->getConnection();

$cart = $_SESSION['cart'] ?? [];
$cartItems = [];
$total = 0;

// Get meal details for items in cart
if (!empty($cart)) {
    $placeholders = str_repeat('?,', count($cart) - 1) . '?';
    $query = "SELECT * FROM meals WHERE id IN ($placeholders)";
    $stmt = $db->prepare($query);
    $stmt->execute(array_keys($cart));
    $meals = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($meals as $meal) {
        $quantity = $cart[$meal['id']];
        $subtotal = $meal['price'] * $quantity;
        $total += $subtotal;
        
        $cartItems[] = [
            'meal' => $meal,
            'quantity' => $quantity,
            'subtotal' => $subtotal
        ];
    }
}

// Handle cart updates
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_cart'])) {
        foreach ($_POST['quantity'] as $meal_id => $quantity) {
            if ($quantity <= 0) {
                unset($_SESSION['cart'][$meal_id]);
            } else {
                $_SESSION['cart'][$meal_id] = $quantity;
            }
        }
        header('Location: cart.php');
        exit();
    } elseif (isset($_POST['clear_cart'])) {
        $_SESSION['cart'] = [];
        header('Location: cart.php');
        exit();
    }
}
?>

<?php include 'header.php'; ?>

<h1>Shopping Cart</h1>

<?php if (empty($cartItems)): ?>
    <div style="text-align: center; padding: 3rem;">
        <h2>Your cart is empty</h2>
        <p>Browse our menu and add some delicious meals to your cart!</p>
        <p><a href="menu.php" class="btn">Browse Menu</a></p>
    </div>
<?php else: ?>
    <form method="POST" action="">
        <table class="table">
            <thead>
                <tr>
                    <th>Meal</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cartItems as $item): ?>
                    <tr>
                        <td>
                            <strong><?php echo htmlspecialchars($item['meal']['name']); ?></strong>
                            <p style="color: #666; font-size: 0.9rem; margin: 0.5rem 0 0 0;">
                                <?php echo htmlspecialchars($item['meal']['description']); ?>
                            </p>
                        </td>
                        <td>MK <?php echo number_format($item['meal']['price'], 2); ?></td>
                        <td>
                            <input type="number" name="quantity[<?php echo $item['meal']['id']; ?>]" 
                                   value="<?php echo $item['quantity']; ?>" min="0" max="10" style="width: 70px; padding: 0.5rem;">
                        </td>
                        <td>MK <?php echo number_format($item['subtotal'], 2); ?></td>
                    </tr>
                <?php endforeach; ?>
                <tr>
                    <td colspan="3" style="text-align: right; font-weight: bold; font-size: 1.1rem;">Total:</td>
                    <td style="font-weight: bold; font-size: 1.1rem;">MK <?php echo number_format($total, 2); ?></td>
                </tr>
            </tbody>
        </table>
        
        <div style="display: flex; gap: 1rem; margin-top: 2rem; flex-wrap: wrap;">
            <button type="submit" name="update_cart" class="btn">Update Cart</button>
            <a href="checkout.php" class="btn btn-success">Proceed to Checkout</a>
            <button type="submit" name="clear_cart" class="btn btn-danger" onclick="return confirm('Are you sure you want to clear your cart?')">Clear Cart</button>
            <a href="menu.php" class="btn">Continue Shopping</a>
        </div>
    </form>
<?php endif; ?>

<?php include '../includes/footer.php'; ?>