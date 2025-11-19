<?php
include 'includes/config.php';
include 'includes/auth.php';

// Redirect users to their appropriate dashboard
if (isset($_SESSION['user_id'])) {
    switch ($_SESSION['user_role']) {
        case 'admin':
            header('Location: admin/index.php');
            exit;
        case 'sales':
            header('Location: sales/orders.php');
            exit;
        case 'manager':
            header('Location: manager/dashboard.php');
            exit;
        case 'customer':
            header('Location: customer/menu.php');
            exit;
    }
}
?>

<?php include 'includes/header.php'; ?>

<!-- Hero Section -->
<div class="hero-section" style="background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('assets/images/meals/appetizer.jpg'); background-size: cover; background-position: center; color: white; text-align: center; padding: 6rem 2rem; border-radius: 10px; margin-bottom: 3rem;">
    <h1 style="font-size: 3rem; margin-bottom: 1rem;">Welcome to Aunt Joy's Restaurant</h1>
    <p style="font-size: 1.5rem; margin-bottom: 2rem;">Taste the Best of Malawian Cuisine</p>
    <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
        <a href="register.php" class="btn" style="background: #e74c3c; padding: 1rem 2rem; font-size: 1.2rem;">Order Now</a>
        <a href="customer/menu.php" class="btn" style="background: #27ae60; padding: 1rem 2rem; font-size: 1.2rem;">View Menu</a>
    </div>
</div>

<!-- Featured Meals Section -->
<div class="featured-section" style="margin-bottom: 3rem;">
    <h2 style="text-align: center; margin-bottom: 2rem; color: #2c3e50;">Our Popular Dishes</h2>
    
    <div class="featured-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2rem;">
        <!-- Featured Meal 1 -->
        <div class="featured-meal" style="background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.1); transition: transform 0.3s;">
            <img src="assets/images/meals/nsima.jpg" alt="Nsima with Relish" style="width: 100%; height: 200px; object-fit: cover;">
            <div style="padding: 1.5rem;">
                <h3 style="color: #2c3e50; margin-bottom: 0.5rem;">Traditional Nsima</h3>
                <p style="color: #666; margin-bottom: 1rem;">Authentic Malawian staple served with your choice of relish</p>
                <a href="register.php" class="btn" style="background: #3498db;">Order Now</a>
            </div>
        </div>

        <!-- Featured Meal 2 -->
        <div class="featured-meal" style="background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.1); transition: transform 0.3s;">
            <img src="assets/images/meals/chicken.jpg" alt="Grilled Chicken" style="width: 100%; height: 200px; object-fit: cover;">
            <div style="padding: 1.5rem;">
                <h3 style="color: #2c3e50; margin-bottom: 0.5rem;">Grilled Chicken</h3>
                <p style="color: #666; margin-bottom: 1rem;">Succulent chicken with traditional Malawian spices</p>
                <a href="register.php" class="btn" style="background: #3498db;">Order Now</a>
            </div>
        </div>

        <!-- Featured Meal 3 -->
        <div class="featured-meal" style="background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.1); transition: transform 0.3s;">
            <img src="assets/images/meals/pizza.jpg" alt="Chicken Pizza" style="width: 100%; height: 200px; object-fit: cover;">
            <div style="padding: 1.5rem;">
                <h3 style="color: #2c3e50; margin-bottom: 0.5rem;">Chicken Pizza</h3>
                <p style="color: #666; margin-bottom: 1rem;">Delicious pizza with fresh local ingredients</p>
                <a href="register.php" class="btn" style="background: #3498db;">Order Now</a>
            </div>
        </div>
    </div>
</div>

<!-- Why Choose Us Section -->
<div class="features-section" style="background: #f8f9fa; padding: 3rem; border-radius: 10px; margin-bottom: 3rem;">
    <h2 style="text-align: center; margin-bottom: 2rem; color: #2c3e50;">Why Choose Aunt Joy's?</h2>
    
    <div class="features-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 2rem; text-align: center;">
        <div class="feature">
            <div style="font-size: 3rem; margin-bottom: 1rem;">🍽️</div>
            <h3 style="color: #2c3e50;">Fresh Ingredients</h3>
            <p style="color: #666;">Locally sourced, fresh ingredients in every meal</p>
        </div>
        
        <div class="feature">
            <div style="font-size: 3rem; margin-bottom: 1rem;">🚚</div>
            <h3 style="color: #2c3e50;">Fast Delivery</h3>
            <p style="color: #666;">Quick delivery across Mzuzu</p>
        </div>
        
        <div class="feature">
            <div style="font-size: 3rem; margin-bottom: 1rem;">💰</div>
            <h3 style="color: #2c3e50;">Best Prices</h3>
            <p style="color: #666;">Affordable prices for quality meals</p>
        </div>
        
        <div class="feature">
            <div style="font-size: 3rem; margin-bottom: 1rem;">⭐</div>
            <h3 style="color: #2c3e50;">Authentic Taste</h3>
            <p style="color: #666;">Traditional Malawian recipes</p>
        </div>
    </div>
</div>

<!-- Call to Action -->
<div class="cta-section" style="text-align: center; padding: 3rem; background: #2c3e50; color: white; border-radius: 10px;">
    <h2 style="margin-bottom: 1rem;">Ready to Enjoy Delicious Food?</h2>
    <p style="margin-bottom: 2rem; font-size: 1.2rem;">Join hundreds of satisfied customers in Mzuzu</p>
    <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
        <a href="register.php" class="btn" style="background: #e74c3c; padding: 1rem 2rem; font-size: 1.2rem;">Create Account</a>
        <a href="customer/menu.php" class="btn" style="background: #27ae60; padding: 1rem 2rem; font-size: 1.2rem;">Browse Menu</a>
    </div>
</div>

<style>
.featured-meal:hover {
    transform: translateY(-10px);
}

.hero-section {
    animation: fadeIn 1s ease-in;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

/* Responsive Design */
@media (max-width: 768px) {
    .hero-section {
        padding: 3rem 1rem;
    }
    
    .hero-section h1 {
        font-size: 2rem;
    }
    
    .featured-grid {
        grid-template-columns: 1fr;
    }
    
    .features-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<?php include 'includes/footer.php'; ?>