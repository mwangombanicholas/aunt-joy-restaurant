<?php
include '../includes/config.php';
include '../includes/auth.php';
checkAuth('manager');

$database = new Database();
$db = $database->getConnection();

// Get filter parameters
$month = $_GET['month'] ?? date('m');
$year = $_GET['year'] ?? date('Y');

// Get sales data
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
          ORDER BY total_sold DESC";
$stmt = $db->prepare($query);
$stmt->bindParam(':month', $month);
$stmt->bindParam(':year', $year);
$stmt->execute();
$best_sellers = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get daily sales breakdown
$query = "SELECT 
            DATE(created_at) as sale_date,
            COUNT(*) as daily_orders,
            COALESCE(SUM(total_amount), 0) as daily_revenue
          FROM orders 
          WHERE MONTH(created_at) = :month AND YEAR(created_at) = :year 
          GROUP BY DATE(created_at)
          ORDER BY sale_date";
$stmt = $db->prepare($query);
$stmt->bindParam(':month', $month);
$stmt->bindParam(':year', $year);
$stmt->execute();
$daily_sales = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get order status breakdown
$query = "SELECT 
            status,
            COUNT(*) as status_count,
            COALESCE(SUM(total_amount), 0) as status_revenue
          FROM orders 
          WHERE MONTH(created_at) = :month AND YEAR(created_at) = :year 
          GROUP BY status
          ORDER BY status_count DESC";
$stmt = $db->prepare($query);
$stmt->bindParam(':month', $month);
$stmt->bindParam(':year', $year);
$stmt->execute();
$status_breakdown = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Set headers for CSV download
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="aunt_joy_sales_report_' . $month . '_' . $year . '.csv"');

// Create output stream
$output = fopen('php://output', 'w');

// Add BOM for UTF-8
fputs($output, $bom = (chr(0xEF) . chr(0xBB) . chr(0xBF)));

// Report Header
fputcsv($output, ['Aunt Joy\'s Restaurant - Sales Report']);
fputcsv($output, ['Period: ' . date('F Y', mktime(0, 0, 0, $month, 1, $year))]);
fputcsv($output, ['Generated: ' . date('Y-m-d H:i:s')]);
fputcsv($output, []); // Empty line

// Summary Statistics
fputcsv($output, ['SUMMARY STATISTICS']);
fputcsv($output, ['Total Orders', $stats['total_orders']]);
fputcsv($output, ['Total Revenue', 'MK ' . number_format($stats['total_revenue'], 2)]);
fputcsv($output, ['Average Order Value', 'MK ' . number_format($stats['average_order_value'] ?? 0, 2)]);
fputcsv($output, []); // Empty line

// Order Status Breakdown
fputcsv($output, ['ORDER STATUS BREAKDOWN']);
fputcsv($output, ['Status', 'Order Count', 'Revenue']);
foreach ($status_breakdown as $status) {
    fputcsv($output, [
        ucfirst($status['status']),
        $status['status_count'],
        'MK ' . number_format($status['status_revenue'], 2)
    ]);
}
fputcsv($output, []); // Empty line

// Best Selling Items
fputcsv($output, ['BEST SELLING ITEMS']);
fputcsv($output, ['Meal Name', 'Quantity Sold', 'Revenue Generated']);
foreach ($best_sellers as $item) {
    fputcsv($output, [
        $item['name'],
        $item['total_sold'],
        'MK ' . number_format($item['revenue'], 2)
    ]);
}
fputcsv($output, []); // Empty line

// Daily Sales Breakdown
fputcsv($output, ['DAILY SALES BREAKDOWN']);
fputcsv($output, ['Date', 'Orders', 'Revenue']);
foreach ($daily_sales as $day) {
    fputcsv($output, [
        $day['sale_date'],
        $day['daily_orders'],
        'MK ' . number_format($day['daily_revenue'], 2)
    ]);
}
fputcsv($output, []); // Empty line

// System Information
fputcsv($output, ['REPORT INFORMATION']);
fputcsv($output, ['Note:', 'PDF/Excel export requires additional PHP libraries (TCPDF/PhpSpreadsheet)']);
fputcsv($output, ['Note:', 'CSV format provides same data in universal format']);
fputcsv($output, ['Note:', 'Open with Excel, Google Sheets, or any spreadsheet software']);
fputcsv($output, ['Note:', 'Report includes all key metrics: total revenue, orders, best-selling items']);

fclose($output);
exit;
?>