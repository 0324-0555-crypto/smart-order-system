<?php
session_start();
require_once 'classes/Auth.php';
Auth::check();

require_once 'classes/Database.php';
require_once 'classes/Order.php';
require_once 'classes/Customer.php';

$db       = new Database();
$conn     = $db->getConnection();
$order    = new Order($conn);
$customer = new Customer($conn);

// Full JOIN Report
$reportStmt = $order->readAll();
$reportRows = $reportStmt->fetchAll();

// Per-customer totals (JOIN + GROUP BY)
$customerReport = $conn->query(
    "SELECT c.name AS customer_name, c.email,
            COUNT(o.id) AS total_orders,
            SUM(o.price * o.quantity) AS total_spent,
            SUM(CASE WHEN o.status='Completed' THEN o.price*o.quantity ELSE 0 END) AS completed_value
     FROM customers c
     LEFT JOIN orders o ON c.id = o.customer_id
     GROUP BY c.id
     ORDER BY total_spent DESC"
)->fetchAll();

// Per-staff totals
$staffReport = $conn->query(
    "SELECT u.full_name, u.username,
            COUNT(o.id) AS orders_processed,
            SUM(o.price * o.quantity) AS total_value
     FROM users u
     LEFT JOIN orders o ON u.id = o.user_id
     GROUP BY u.id
     ORDER BY orders_processed DESC"
)->fetchAll();

$pageTitle = 'Reports';
include 'includes/header.php';
?>

<div class="page-header">
    <div class="page-header-left">
        <h2>Reports</h2>
        <p>Transaction reports generated using JOIN queries across all related tables.</p>
    </div>
</div>

<!-- Full Transaction Report -->
<div class="content-card">
    <div class="card-header-custom">
        <h5><i class="bi bi-table me-2" style="color:var(--electric);"></i>Full Transaction Report</h5>
    </div>
    <div style="overflow-x:auto;">
        <table class="table-custom">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer Name</th>
                    <th>Customer Email</th>
                    <th>Product</th>
                    <th>Qty</th>
                    <th>Unit Price</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Created By</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($reportRows)): ?>
                    <tr>
                        <td colspan="10" style="text-align:center; color:#a0aec0; padding:24px;">No data.</td>
                    </tr>
                <?php else: foreach ($reportRows as $row): ?>
                    <tr>
                        <td><strong style="color:var(--electric);">#<?= $row['id'] ?></strong></td>
                        <td><?= htmlspecialchars($row['customer_name']) ?></td>
                        <td style="font-size:12px; color:#718096;"><?= htmlspecialchars($row['customer_email']) ?></td>
                        <td><?= htmlspecialchars($row['product_name']) ?></td>
                        <td><?= $row['quantity'] ?></td>
                        <td>&#8369;<?= number_format($row['price'], 2) ?></td>
                        <td><strong>&#8369;<?= number_format($row['price'] * $row['quantity'], 2) ?></strong></td>
                        <td><span class="status-badge status-<?= $row['status'] ?>"><?= $row['status'] ?></span></td>
                        <td style="color:var(--accent); font-size:12px;">
                            <i class="bi bi-person-badge"></i> <?= htmlspecialchars($row['created_by']) ?>
                        </td>
                        <td style="color:#718096; font-size:12px;"><?= date('M j, Y g:i A', strtotime($row['created_at'])) ?></td>
                    </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="row g-3">
<!-- Customer Report -->
<div class="col-md-7">
    <div class="content-card">
        <div class="card-header-custom">
            <h5><i class="bi bi-person-lines-fill me-2" style="color:var(--electric);"></i>Customer Summary</h5>
        </div>
        <div style="overflow-x:auto;">
            <table class="table-custom">
                <thead>
                    <tr>
                        <th>Customer</th>
                        <th>Email</th>
                        <th>Orders</th>
                        <th>Total Spent</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($customerReport as $row): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($row['customer_name']) ?></strong></td>
                        <td style="font-size:12px; color:#718096;"><?= htmlspecialchars($row['email']) ?></td>
                        <td>
                            <span class="status-badge status-Processing"><?= $row['total_orders'] ?> orders</span>
                        </td>
                        <td><strong style="color:var(--accent);">&#8369;<?= number_format($row['total_spent'] ?? 0, 2) ?></strong></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Staff Report -->
<div class="col-md-5">
    <div class="content-card">
        <div class="card-header-custom">
            <h5><i class="bi bi-person-gear me-2" style="color:var(--electric);"></i>Staff Activity</h5>
        </div>
        <div style="overflow-x:auto;">
            <table class="table-custom">
                <thead>
                    <tr>
                        <th>Staff Name</th>
                        <th>Orders Handled</th>
                        <th>Total Value</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($staffReport as $row): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($row['full_name']) ?></strong></td>
                        <td>
                            <span class="status-badge status-Completed"><?= $row['orders_processed'] ?></span>
                        </td>
                        <td style="color:var(--electric);">&#8369;<?= number_format($row['total_value'] ?? 0, 2) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</div>

<?php include 'includes/footer.php'; ?>
