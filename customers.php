<?php
session_start();
require_once 'classes/Auth.php';
Auth::check();

require_once 'classes/Database.php';
require_once 'classes/Customer.php';

$db       = new Database();
$conn     = $db->getConnection();
$customer = new Customer($conn);

$action  = $_GET['action'] ?? 'list';
$id      = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$message = '';
$msgType = '';

// Handle POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer->name    = trim($_POST['name'] ?? '');
    $customer->email   = trim($_POST['email'] ?? '');
    $customer->phone   = trim($_POST['phone'] ?? '');
    $customer->address = trim($_POST['address'] ?? '');

    // Validation
    if (empty($customer->name) || empty($customer->email)) {
        $message = 'Name and Email are required.';
        $msgType = 'danger';
    } else {
        if ($_POST['form_action'] === 'create') {
            if ($customer->create()) {
                header('Location: customers.php?success=Customer added successfully.');
                exit;
            } else {
                $message = 'Failed to add customer. Email may already exist.';
                $msgType = 'danger';
            }
        } elseif ($_POST['form_action'] === 'update') {
            $customer->id = (int)$_POST['id'];
            if ($customer->update()) {
                header('Location: customers.php?success=Customer updated successfully.');
                exit;
            } else {
                $message = 'Failed to update customer.';
                $msgType = 'danger';
            }
        }
    }
}

// Handle delete
if ($action === 'delete' && $id > 0) {
    $customer->id = $id;
    if ($customer->delete()) {
        header('Location: customers.php?success=Customer deleted successfully.');
        exit;
    } else {
        header('Location: customers.php?error=Failed to delete customer.');
        exit;
    }
}

// Fetch edit data
if ($action === 'edit' && $id > 0) {
    $customer->id = $id;
    $customer->readOne();
}

// URL messages
if (isset($_GET['success'])) { $message = $_GET['success']; $msgType = 'success'; }
if (isset($_GET['error']))   { $message = $_GET['error'];   $msgType = 'danger'; }

$pageTitle = 'Customers';
include 'includes/header.php';
?>

<div class="page-header">
    <div class="page-header-left">
        <h2><?= $action === 'create' ? 'Add Customer' : ($action === 'edit' ? 'Edit Customer' : 'Customer Management') ?></h2>
        <p><?= $action === 'list' ? 'Manage all registered customers.' : 'Fill in the form below.' ?></p>
    </div>
    <?php if ($action === 'list'): ?>
    <a href="customers.php?action=create" class="btn-primary-custom">
        <i class="bi bi-plus-lg"></i> Add Customer
    </a>
    <?php else: ?>
    <a href="customers.php" class="btn-primary-custom" style="background:linear-gradient(135deg,#718096,#4a5568);">
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
<!-- CREATE / EDIT FORM -->
<div class="content-card">
    <div class="card-header-custom">
        <h5><?= $action === 'create' ? 'New Customer Form' : 'Edit Customer' ?></h5>
    </div>
    <div class="card-body-custom">
        <form method="POST" action="customers.php">
            <input type="hidden" name="form_action" value="<?= $action === 'edit' ? 'update' : 'create' ?>">
            <?php if ($action === 'edit'): ?>
                <input type="hidden" name="id" value="<?= $id ?>">
            <?php endif; ?>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label-custom">Full Name *</label>
                    <input type="text" name="name" class="form-control"
                           value="<?= htmlspecialchars($customer->name ?? '') ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label-custom">Email Address *</label>
                    <input type="email" name="email" class="form-control"
                           value="<?= htmlspecialchars($customer->email ?? '') ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label-custom">Phone Number</label>
                    <input type="text" name="phone" class="form-control"
                           value="<?= htmlspecialchars($customer->phone ?? '') ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label-custom">Address</label>
                    <input type="text" name="address" class="form-control"
                           value="<?= htmlspecialchars($customer->address ?? '') ?>">
                </div>
                <div class="col-12 pt-2">
                    <button type="submit" class="btn-success-custom">
                        <i class="bi bi-<?= $action === 'edit' ? 'pencil-square' : 'plus-circle' ?>"></i>
                        <?= $action === 'edit' ? 'Update Customer' : 'Add Customer' ?>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php else: ?>
<!-- LIST VIEW -->
<div class="content-card">
    <div class="card-header-custom">
        <h5><i class="bi bi-people me-2" style="color:var(--electric);"></i>All Customers</h5>
    </div>
    <div style="overflow-x:auto;">
        <table class="table-custom">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Address</th>
                    <th>Orders</th>
                    <th>Registered</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $result = $customer->readAll();
                $rows = $result->fetchAll();
                if (empty($rows)):
                ?>
                    <tr>
                        <td colspan="8" style="text-align:center; color:#a0aec0; padding:30px;">
                            No customers found. <a href="customers.php?action=create">Add the first one.</a>
                        </td>
                    </tr>
                <?php else: $n = 1; foreach ($rows as $row): ?>
                    <tr>
                        <td style="color:#a0aec0;"><?= $n++ ?></td>
                        <td><strong><?= htmlspecialchars($row['name']) ?></strong></td>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td><?= htmlspecialchars($row['phone'] ?? '---') ?></td>
                        <td><?= htmlspecialchars($row['address'] ?? '---') ?></td>
                        <td>
                            <span class="status-badge status-Completed">
                                <?= $row['total_orders'] ?>
                            </span>
                        </td>
                        <td style="color:#718096;"><?= date('M j, Y', strtotime($row['created_at'])) ?></td>
                        <td>
                            <a href="customers.php?action=edit&id=<?= $row['id'] ?>"
                               class="action-btn action-edit" title="Edit">
                                <i class="bi bi-pencil-fill"></i>
                            </a>
                            <form method="POST" action="customers.php?action=delete&id=<?= $row['id'] ?>"
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
