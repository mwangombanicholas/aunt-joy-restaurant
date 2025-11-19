<?php
include '../includes/config.php';
include '../includes/auth.php';
checkAuth('manager');

$database = new Database();
$db = $database->getConnection();

// Get filter parameters
$month = $_GET['month'] ?? date('m');
$year = $_GET['year'] ?? date('Y');

// Get sales statistics
$query = "SELECT 
            COUNT(*) as total_orders,
            COALESCE(SUM(total_amount), 0) as total_revenue,
            AVG(total_amount) as average_order_value
          FROM orders 
          WHERE MONTH(created_at) = :month AND YEAR(created_at) = :year";
$stmt = $db->prepare($query);
$stmt->bindParam(':month', $month);
$stmt->bindParam(':year', $year);
$stmt->execute();
$stats = $stmt->fetch(PDO::FETCH_ASSOC);

// Get best-selling items
$query = "SELECT m.name, SUM(oi.quantity) as total_sold, SUM(oi.quantity * oi.unit_price) as revenue
          FROM order_items oi 
          JOIN meals m ON oi.meal_id = m.id 
          JOIN orders o ON oi.order_id = o.id 
          WHERE MONTH(o.created_at) = :month AND YEAR(o.created_at) = :year 
          GROUP BY m.id 
          ORDER BY total_sold DESC 
          LIMIT 10";
$stmt = $db->prepare($query);
$stmt->bindParam(':month', $month);
$stmt->bindParam(':year', $year);
$stmt->execute();
$best_sellers = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get monthly sales data for chart
$query = "SELECT 
            DATE_FORMAT(created_at, '%Y-%m') as month,
            COUNT(*) as order_count,
            COALESCE(SUM(total_amount), 0) as revenue
          FROM orders 
          WHERE created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
          GROUP BY DATE_FORMAT(created_at, '%Y-%m')
          ORDER BY month DESC 
          LIMIT 6";
$monthly_data = $db->query($query)->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include 'header.php'; ?>

<!-- WELCOME MESSAGE SECTION -->
<?php if (isset($_SESSION['welcome_message'])): ?>
    <div style="background: #e8f4fd; padding: 1rem; border-radius: 4px; margin-bottom: 1rem; border-left: 4px solid #3498db;">
        <strong>👋 Welcome, <?php echo $_SESSION['full_name']; ?>!</strong><br>
        <?php echo $_SESSION['welcome_message']; ?>
    </div>
    <?php unset($_SESSION['welcome_message']); ?>
<?php endif; ?>

<h1>Manager Reports Dashboard</h1>

<!-- Filter Form -->
<div style="background: white; padding: 1.5rem; border-radius: 8px; margin-bottom: 2rem; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
    <h3>Filter Reports</h3>
    <form method="GET" action="" style="display: flex; gap: 1rem; align-items: end; flex-wrap: wrap;">
        <div class="form-group">
            <label for="month">Month:</label>
            <select id="month" name="month">
                <?php for ($i = 1; $i <= 12; $i++): ?>
                    <option value="<?php echo sprintf('%02d', $i); ?>" <?php echo $i == $month ? 'selected' : ''; ?>>
                        <?php echo date('F', mktime(0, 0, 0, $i, 1)); ?>
                    </option>
                <?php endfor; ?>
            </select>
        </div>
        
        <div class="form-group">
            <label for="year">Year:</label>
            <select id="year" name="year">
                <?php for ($i = date('Y'); $i >= 2020; $i--): ?>
                    <option value="<?php echo $i; ?>" <?php echo $i == $year ? 'selected' : ''; ?>>
                        <?php echo $i; ?>
                    </option>
                <?php endfor; ?>
            </select>
        </div>
        
        <button type="submit" class="btn">Generate Report</button>
        <div style="display: flex; gap: 0.5rem;">
            <button type="button" onclick="exportPDF()" class="btn" style="background: #e74c3c;">PDF</button>
            <button type="button" onclick="exportExcel()" class="btn" style="background: #27ae60;">Excel</button>
            <button type="button" onclick="exportCSV()" class="btn" style="background: #3498db;">CSV Export</button>
        </div>
    </form>
    <p style="margin-top: 1rem; font-size: 0.9rem; color: #666;">
        <strong>Note:</strong> CSV export provides all report data in universal format. 
        PDF/Excel require additional PHP libraries.
    </p>
</div>

<!-- Statistics Cards -->
<div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
    <div style="background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); text-align: center;">
        <h3 style="color: #3498db; margin-bottom: 1rem;">📦 Total Orders</h3>
        <div style="font-size: 2.5rem; font-weight: bold; color: #2c3e50;"><?php echo $stats['total_orders']; ?></div>
        <p style="color: #666; margin-top: 0.5rem;">For <?php echo date('F Y', mktime(0, 0, 0, $month, 1, $year)); ?></p>
    </div>
    
    <div style="background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); text-align: center;">
        <h3 style="color: #27ae60; margin-bottom: 1rem;">💰 Total Revenue</h3>
        <div style="font-size: 2.5rem; font-weight: bold; color: #2c3e50;">MK <?php echo number_format($stats['total_revenue'], 2); ?></div>
        <p style="color: #666; margin-top: 0.5rem;">For <?php echo date('F Y', mktime(0, 0, 0, $month, 1, $year)); ?></p>
    </div>
    
    <div style="background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); text-align: center;">
        <h3 style="color: #e74c3c; margin-bottom: 1rem;">📊 Average Order</h3>
        <div style="font-size: 2.5rem; font-weight: bold; color: #2c3e50;">MK <?php echo number_format($stats['average_order_value'] ?? 0, 2); ?></div>
        <p style="color: #666; margin-top: 0.5rem;">Per order</p>
    </div>
</div>

<!-- Two Column Layout -->
<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 2rem;">
    <!-- Best Selling Items -->
    <div style="background: white; padding: 1.5rem; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        <h3>🍽️ Best Selling Items</h3>
        <?php if (!empty($best_sellers)): ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Meal</th>
                        <th>Quantity Sold</th>
                        <th>Revenue</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($best_sellers as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['name']); ?></td>
                            <td><?php echo $item['total_sold']; ?></td>
                            <td>MK <?php echo number_format($item['revenue'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p style="text-align: center; color: #666; padding: 2rem;">No sales data for selected period</p>
        <?php endif; ?>
    </div>

    <!-- Monthly Trend -->
    <div style="background: white; padding: 1.5rem; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        <h3>📈 Sales Trend (Last 6 Months)</h3>
        <?php if (!empty($monthly_data)): ?>
            <div style="height: 300px; display: flex; align-items: end; gap: 10px; margin-top: 2rem; padding: 1rem; border: 1px solid #eee; border-radius: 4px;">
                <?php 
                $max_revenue = max(array_column($monthly_data, 'revenue'));
                foreach (array_reverse($monthly_data) as $data): 
                    $height = $max_revenue > 0 ? ($data['revenue'] / $max_revenue * 200) : 10;
                ?>
                    <div style="display: flex; flex-direction: column; align-items: center; flex: 1;">
                        <div style="background: #3498db; width: 30px; height: <?php echo $height; ?>px; border-radius: 4px 4px 0 0;"></div>
                        <div style="margin-top: 0.5rem; font-size: 0.7rem; text-align: center;">
                            <?php echo date('M Y', strtotime($data['month'])); ?><br>
                            <strong>MK <?php echo number_format($data['revenue'], 0); ?></strong>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p style="text-align: center; color: #666; padding: 2rem;">No trend data available</p>
        <?php endif; ?>
    </div>
</div>

<!-- Export Information -->
<div style="background: #e8f4fd; padding: 1.5rem; border-radius: 8px; margin-top: 2rem; border-left: 4px solid #3498db;">
    <h3>📊 Export Functionality</h3>
    <p><strong>CSV Export (Implemented):</strong> Provides all report data in universal CSV format that can be opened in Excel, Google Sheets, or any spreadsheet software.</p>
    <p><strong>PDF/Excel Export (Demonstration):</strong> Buttons are implemented but require additional PHP libraries:</p>
    <ul>
        <li><strong>PDF:</strong> Would require TCPDF or DomPDF library</li>
        <li><strong>Excel:</strong> Would require PhpSpreadsheet library</li>
    </ul>
</div>

<script>
function exportPDF() {
    alert('PDF export would require TCPDF library installation');
}

function exportExcel() {
    alert('Excel export would require PhpSpreadsheet library installation');
}

function exportCSV() {
    window.location.href = 'export_csv.php?month=<?php echo $month; ?>&year=<?php echo $year; ?>';
}
</script>

<?php include '../includes/footer.php'; ?>