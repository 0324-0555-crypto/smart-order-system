<?php
session_start();
require_once 'classes/Auth.php';
Auth::check();

require_once 'classes/Database.php';
require_once 'classes/Customer.php';
require_once 'classes/Order.php';
require_once 'classes/User.php';

$db   = new Database();
$conn = $db->getConnection();

$customer = new Customer($conn);
$order    = new Order($conn);
$user     = new User($conn);

$totalCustomers  = $customer->countAll();
$totalOrders     = $order->countAll();
$totalRevenue    = $order->totalRevenue();
$pendingOrders   = $order->countByStatus('Pending');
$completedOrders = $order->countByStatus('Completed');

$recentStmt = $order->recentOrders(8);
$recentOrders = $recentStmt->fetchAll();

$pageTitle = 'Dashboard';
include 'includes/header.php';
?>

<div class="page-header">
    <div class="page-header-left">
        <h2>Dashboard</h2>
        <p>Welcome back, <?= htmlspecialchars($_SESSION['full_name']) ?>. Here is today's overview.</p>
    </div>
    <div>
        <a href="orders.php?action=create" class="btn-primary-custom">
            <i class="bi bi-plus-lg"></i> New Order
        </a>
    </div>
</div>

<!-- Stats Row -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-icon blue"><i class="bi bi-people-fill"></i></div>
            <div class="stat-value"><?= $totalCustomers ?></div>
            <div class="stat-label">Total Customers</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-icon green"><i class="bi bi-bag-fill"></i></div>
            <div class="stat-value"><?= $totalOrders ?></div>
            <div class="stat-label">Total Orders</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-icon orange"><i class="bi bi-clock-fill"></i></div>
            <div class="stat-value"><?= $pendingOrders ?></div>
            <div class="stat-label">Pending Orders</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-icon navy"><i class="bi bi-cash-stack"></i></div>
            <div class="stat-value">&#8369;<?= number_format($totalRevenue, 0) ?></div>
            <div class="stat-label">Revenue (Completed)</div>
        </div>
    </div>
</div>

<!-- Recent Orders Table (JOIN Demo) -->
<div class="content-card">
    <div class="card-header-custom">
        <h5><i class="bi bi-list-ul me-2" style="color:var(--electric);"></i>Recent Transactions</h5>
        <a href="orders.php" class="btn-primary-custom" style="padding:7px 14px; font-size:12px;">
            View All
        </a>
    </div>
    <div style="overflow-x:auto;">
        <table class="table-custom">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer Name</th>
                    <th>Product</th>
                    <th>Qty</th>
                    <th>Price</th>
                    <th>Status</th>
                    <th>Created By</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($recentOrders)): ?>
                    <tr>
                        <td colspan="8" style="text-align:center; color:#a0aec0; padding:30px;">
                            No orders found. <a href="orders.php?action=create">Create the first order.</a>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($recentOrders as $row): ?>
                    <tr>
                        <td><strong style="color:var(--electric);">#<?= $row['id'] ?></strong></td>
                        <td><?= htmlspecialchars($row['customer_name']) ?></td>
                        <td><?= htmlspecialchars($row['product_name']) ?></td>
                        <td><?= $row['quantity'] ?></td>
                        <td>&#8369;<?= number_format($row['price'], 2) ?></td>
                        <td>
                            <span class="status-badge status-<?= $row['status'] ?>">
                                <?= $row['status'] ?>
                            </span>
                        </td>
                        <td><?= htmlspecialchars($row['created_by']) ?></td>
                        <td style="color:#718096;"><?= date('M j, Y', strtotime($row['created_at'])) ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
