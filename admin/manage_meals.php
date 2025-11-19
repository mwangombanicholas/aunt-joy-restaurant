<?php
include '../includes/config.php';
include '../includes/auth.php';
checkAuth('admin');

$database = new Database();
$db = $database->getConnection();

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_meal'])) {
        $name = $_POST['name'];
        $description = $_POST['description'];
        $price = $_POST['price'];
        $category_id = $_POST['category_id'];
        $is_available = isset($_POST['is_available']) ? 1 : 0;
        
        $query = "INSERT INTO meals (name, description, price, category_id, is_available) 
                 VALUES (:name, :description, :price, :category_id, :is_available)";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':category_id', $category_id);
        $stmt->bindParam(':is_available', $is_available);
        
        if ($stmt->execute()) {
            $success = "Meal added successfully!";
        } else {
            $error = "Failed to add meal.";
        }
    }
    
    if (isset($_POST['update_meal'])) {
        $meal_id = $_POST['meal_id'];
        $name = $_POST['name'];
        $description = $_POST['description'];
        $price = $_POST['price'];
        $category_id = $_POST['category_id'];
        $is_available = isset($_POST['is_available']) ? 1 : 0;
        
        $query = "UPDATE meals SET name = :name, description = :description, price = :price, 
                 category_id = :category_id, is_available = :is_available WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $meal_id);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':category_id', $category_id);
        $stmt->bindParam(':is_available', $is_available);
        
        if ($stmt->execute()) {
            $success = "Meal updated successfully!";
        } else {
            $error = "Failed to update meal.";
        }
    }
    
    if (isset($_POST['delete_meal'])) {
        $meal_id = $_POST['meal_id'];
        
        $query = "DELETE FROM meals WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $meal_id);
        
        if ($stmt->execute()) {
            $success = "Meal deleted successfully!";
        } else {
            $error = "Failed to delete meal.";
        }
    }
}

// Get all meals
$query = "SELECT m.*, c.name as category_name FROM meals m LEFT JOIN categories c ON m.category_id = c.id ORDER BY m.name";
$stmt = $db->prepare($query);
$stmt->execute();
$meals = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get categories for dropdown
$categories = $db->query("SELECT * FROM categories ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include 'header.php'; ?>

<h1>Manage Meals</h1>

<?php if (isset($success)): ?>
    <div style="background: #e8f5e8; color: #2e7d32; padding: 1rem; margin-bottom: 1rem; border-radius: 4px;">
        <?php echo $success; ?>
    </div>
<?php endif; ?>

<?php if (isset($error)): ?>
    <div style="background: #ffebee; color: #c62828; padding: 1rem; margin-bottom: 1rem; border-radius: 4px;">
        <?php echo $error; ?>
    </div>
<?php endif; ?>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
    <!-- Add Meal Form -->
    <div>
        <h2>Add New Meal</h2>
        <form method="POST" action="" style="background: white; padding: 1.5rem; border-radius: 8px;">
            <div class="form-group">
                <label for="name">Meal Name:</label>
                <input type="text" id="name" name="name" required>
            </div>
            
            <div class="form-group">
                <label for="description">Description:</label>
                <textarea id="description" name="description" rows="3" required></textarea>
            </div>
            
            <div class="form-group">
                <label for="price">Price (MK):</label>
                <input type="number" id="price" name="price" step="0.01" min="0" required>
            </div>
            
            <div class="form-group">
                <label for="category_id">Category:</label>
                <select id="category_id" name="category_id" required>
                    <option value="">Select Category</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo $category['id']; ?>"><?php echo htmlspecialchars($category['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label style="display: flex; align-items: center; gap: 0.5rem;">
                    <input type="checkbox" name="is_available" value="1" checked>
                    Available for order
                </label>
            </div>
            
            <button type="submit" name="add_meal" class="btn">Add Meal</button>
        </form>
    </div>

    <!-- Meals List -->
    <div>
        <h2>Existing Meals</h2>
        <div style="background: white; border-radius: 8px; overflow: hidden;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Price</th>
                        <th>Category</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($meals as $meal): ?>
                        <tr>
                            <td>
                                <strong><?php echo htmlspecialchars($meal['name']); ?></strong>
                                <div style="font-size: 0.8rem; color: #666;"><?php echo htmlspecialchars($meal['description']); ?></div>
                            </td>
                            <td>MK <?php echo number_format($meal['price'], 2); ?></td>
                            <td><?php echo htmlspecialchars($meal['category_name']); ?></td>
                            <td>
                                <span style="color: <?php echo $meal['is_available'] ? '#27ae60' : '#e74c3c'; ?>;">
                                    <?php echo $meal['is_available'] ? 'Available' : 'Unavailable'; ?>
                                </span>
                            </td>
                            <td>
                                <div style="display: flex; gap: 0.5rem;">
                                    <button type="button" onclick="editMeal(<?php echo htmlspecialchars(json_encode($meal)); ?>)" 
                                            class="btn" style="padding: 0.25rem 0.5rem; font-size: 0.8rem;">
                                        Edit
                                    </button>
                                    <form method="POST" action="" style="display: inline;">
                                        <input type="hidden" name="meal_id" value="<?php echo $meal['id']; ?>">
                                        <button type="submit" name="delete_meal" class="btn btn-danger" 
                                                style="padding: 0.25rem 0.5rem; font-size: 0.8rem;"
                                                onclick="return confirm('Are you sure you want to delete this meal?')">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Edit Meal Modal -->
<div id="editModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); justify-content: center; align-items: center;">
    <div style="background: white; padding: 2rem; border-radius: 8px; width: 90%; max-width: 500px;">
        <h2>Edit Meal</h2>
        <form method="POST" action="" id="editForm">
            <input type="hidden" name="meal_id" id="edit_meal_id">
            
            <div class="form-group">
                <label for="edit_name">Meal Name:</label>
                <input type="text" id="edit_name" name="name" required>
            </div>
            
            <div class="form-group">
                <label for="edit_description">Description:</label>
                <textarea id="edit_description" name="description" rows="3" required></textarea>
            </div>
            
            <div class="form-group">
                <label for="edit_price">Price (MK):</label>
                <input type="number" id="edit_price" name="price" step="0.01" min="0" required>
            </div>
            
            <div class="form-group">
                <label for="edit_category_id">Category:</label>
                <select id="edit_category_id" name="category_id" required>
                    <option value="">Select Category</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo $category['id']; ?>"><?php echo htmlspecialchars($category['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label style="display: flex; align-items: center; gap: 0.5rem;">
                    <input type="checkbox" name="is_available" id="edit_is_available" value="1">
                    Available for order
                </label>
            </div>
            
            <div style="display: flex; gap: 1rem; margin-top: 1rem;">
                <button type="submit" name="update_meal" class="btn">Update Meal</button>
                <button type="button" onclick="closeEditModal()" class="btn btn-danger">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
function editMeal(meal) {
    document.getElementById('edit_meal_id').value = meal.id;
    document.getElementById('edit_name').value = meal.name;
    document.getElementById('edit_description').value = meal.description;
    document.getElementById('edit_price').value = meal.price;
    document.getElementById('edit_category_id').value = meal.category_id;
    document.getElementById('edit_is_available').checked = meal.is_available == 1;
    
    document.getElementById('editModal').style.display = 'flex';
}

function closeEditModal() {
    document.getElementById('editModal').style.display = 'none';
}

// Close modal when clicking outside
document.getElementById('editModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeEditModal();
    }
});
</script>

<?php include '../includes/footer.php'; ?>