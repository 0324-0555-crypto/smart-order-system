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

$action  = $_GET['action'] ?? 'list';
$id      = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$message = '';
$msgType = '';

// Handle POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order->customer_id  = (int)($_POST['customer_id'] ?? 0);
    $order->product_name = trim($_POST['product_name'] ?? '');
    $order->quantity     = (int)($_POST['quantity'] ?? 1);
    $order->price        = (float)($_POST['price'] ?? 0);
    $order->status       = $_POST['status'] ?? 'Pending';
    $order->notes        = trim($_POST['notes'] ?? '');
    $order->user_id      = $_SESSION['user_id'];

    if (empty($order->product_name) || $order->customer_id < 1 || $order->price <= 0) {
        $message = 'Customer, product name, and price are required.';
        $msgType = 'danger';
    } else {
        if ($_POST['form_action'] === 'create') {
            if ($order->create()) {
                header('Location: orders.php?success=Order created successfully.');
                exit;
            } else {
                $message = 'Failed to create order.';
                $msgType = 'danger';
            }
        } elseif ($_POST['form_action'] === 'update') {
            $order->id = (int)$_POST['id'];
            if ($order->update()) {
                header('Location: orders.php?success=Order updated successfully.');
                exit;
            } else {
                $message = 'Failed to update order.';
                $msgType = 'danger';
            }
        }
    }
}

// Handle delete
if ($action === 'delete' && $id > 0) {
    $order->id = $id;
    if ($order->delete()) {
        header('Location: orders.php?success=Order deleted.');
        exit;
    }
}

// Fetch edit data
$editData = null;
if ($action === 'edit' && $id > 0) {
    $order->id = $id;
    $editData  = $order->readOne();
}

// URL messages
if (isset($_GET['success'])) { $message = $_GET['success']; $msgType = 'success'; }
if (isset($_GET['error']))   { $message = $_GET['error'];   $msgType = 'danger'; }

$customers   = $customer->getDropdown();
$statusOpts  = ['Pending', 'Processing', 'Completed', 'Cancelled'];
$pageTitle   = 'Orders';
include 'includes/header.php';
?>

<div class="page-header">
    <div class="page-header-left">
        <h2><?= $action === 'create' ? 'Create Order' : ($action === 'edit' ? 'Edit Order' : 'Order Management') ?></h2>
        <p><?= $action === 'list' ? 'All orders with customer and staff relationships.' : 'Fill in the order details below.' ?></p>
    </div>
    <?php if ($action === 'list'): ?>
    <a href="orders.php?action=create" class="btn-primary-custom">
        <i class="bi bi-plus-lg"></i> New Order
    </a>
    <?php else: ?>
    <a href="orders.php" class="btn-primary-custom" style="background:linear-gradient(135deg,#718096,#4a5568);">
        <i class="bi bi-arrow-left"></i> Back
    </a>
    <?php endif; ?>
</div>

<?php if ($message): ?>
    <div class="alert-custom alert-<?= $msgType ?>">
        <i class="bi bi-<?= $msgType === 'success' ? 'check-circle-fill' : 'exclamation-triangle-fill' ?>"></i>
        <?= htmlspecialchars($message) ?>
    </div>
<?php endif; ?>

<?php if ($action === 'create' || $action === 'edit'): ?>
<div class="content-card">
    <div class="card-header-custom">
        <h5><?= $action === 'create' ? 'New Order Form' : 'Edit Order #' . $id ?></h5>
    </div>
    <div class="card-body-custom">
        <form method="POST" action="orders.php">
            <input type="hidden" name="form_action" value="<?= $action === 'edit' ? 'update' : 'create' ?>">
            <?php if ($action === 'edit'): ?>
                <input type="hidden" name="id" value="<?= $id ?>">
            <?php endif; ?>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label-custom">Customer *</label>
                    <select name="customer_id" class="form-select" required>
                        <option value="">-- Select Customer --</option>
                        <?php foreach ($customers as $c): ?>
                            <option value="<?= $c['id'] ?>"
                                <?= ($editData && $editData['customer_id'] == $c['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($c['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label-custom">Product Name *</label>
                    <input type="text" name="product_name" class="form-control"
                           value="<?= htmlspecialchars($editData['product_name'] ?? '') ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label-custom">Quantity *</label>
                    <input type="number" name="quantity" class="form-control" min="1"
                           value="<?= htmlspecialchars($editData['quantity'] ?? '1') ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label-custom">Unit Price (PHP) *</label>
                    <input type="number" name="price" step="0.01" class="form-control" min="0.01"
                           value="<?= htmlspecialchars($editData['price'] ?? '') ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label-custom">Status</label>
                    <select name="status" class="form-select">
                        <?php foreach ($statusOpts as $s): ?>
                            <option value="<?= $s ?>"
                                <?= ($editData && $editData['status'] === $s) ? 'selected' : '' ?>>
                                <?= $s ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label-custom">Notes</label>
                    <textarea name="notes" class="form-control" rows="2"><?= htmlspecialchars($editData['notes'] ?? '') ?></textarea>
                </div>
                <div class="col-12 pt-2">
                    <button type="submit" class="btn-success-custom">
                        <i class="bi bi-<?= $action === 'edit' ? 'pencil-square' : 'bag-plus' ?>"></i>
                        <?= $action === 'edit' ? 'Update Order' : 'Create Order' ?>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php else: ?>
<!-- LIST VIEW - Transaction Flow with JOIN -->
<div class="content-card">
    <div class="card-header-custom">
        <h5><i class="bi bi-bag me-2" style="color:var(--electric);"></i>All Orders — JOIN View</h5>
        <small style="color:#718096; font-size:11px;">Linked: orders + customers + users</small>
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
                    <th>Total</th>
                    <th>Status</th>
                    <th>Created By</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $result = $order->readAll();
                $rows   = $result->fetchAll();
                if (empty($rows)):
                ?>
                    <tr>
                        <td colspan="10" style="text-align:center; color:#a0aec0; padding:30px;">
                            No orders found. <a href="orders.php?action=create">Create the first order.</a>
                        </td>
                    </tr>
                <?php else: foreach ($rows as $row): ?>
                    <tr>
                        <td><strong style="color:var(--electric);">#<?= $row['id'] ?></strong></td>
                        <td><?= htmlspecialchars($row['customer_name']) ?></td>
                        <td><?= htmlspecialchars($row['product_name']) ?></td>
                        <td><?= $row['quantity'] ?></td>
                        <td>&#8369;<?= number_format($row['price'], 2) ?></td>
                        <td><strong>&#8369;<?= number_format($row['price'] * $row['quantity'], 2) ?></strong></td>
                        <td>
                            <span class="status-badge status-<?= $row['status'] ?>">
                                <?= $row['status'] ?>
                            </span>
                        </td>
                        <td>
                            <span style="font-size:12px; color:var(--accent);">
                                <i class="bi bi-person-badge"></i>
                                <?= htmlspecialchars($row['created_by']) ?>
                            </span>
                        </td>
                        <td style="color:#718096;"><?= date('M j, Y', strtotime($row['created_at'])) ?></td>
                        <td>
                            <a href="orders.php?action=edit&id=<?= $row['id'] ?>"
                               class="action-btn action-edit" title="Edit">
                                <i class="bi bi-pencil-fill"></i>
                            </a>
                            <form method="POST" action="orders.php?action=delete&id=<?= $row['id'] ?>"
                                  class="delete-form d-inline">
                                <button type="submit" class="action-btn action-delete" title="Delete">
                                    <i class="bi bi-trash-fill"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
