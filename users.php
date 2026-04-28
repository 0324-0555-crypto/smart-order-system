<?php
session_start();
require_once 'classes/Auth.php';
Auth::requireAdmin();

require_once 'classes/Database.php';
require_once 'classes/User.php';

$db   = new Database();
$conn = $db->getConnection();
$user = new User($conn);

$action  = $_GET['action'] ?? 'list';
$id      = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$message = '';
$msgType = '';

// Handle POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user->username  = trim($_POST['username'] ?? '');
    $user->full_name = trim($_POST['full_name'] ?? '');
    $user->role      = $_POST['role'] ?? 'staff';
    $user->password  = trim($_POST['password'] ?? '');

    if (empty($user->username) || empty($user->full_name)) {
        $message = 'Username and Full Name are required.';
        $msgType = 'danger';
    } elseif ($_POST['form_action'] === 'create' && empty($user->password)) {
        $message = 'Password is required for new accounts.';
        $msgType = 'danger';
    } else {
        if ($_POST['form_action'] === 'create') {
            if ($user->create()) {
                header('Location: users.php?success=Staff account created.');
                exit;
            } else {
                $message = 'Failed to create account. Username may already exist.';
                $msgType = 'danger';
            }
        } elseif ($_POST['form_action'] === 'update') {
            $user->id = (int)$_POST['id'];
            if ($user->update()) {
                header('Location: users.php?success=Account updated successfully.');
                exit;
            } else {
                $message = 'Failed to update account.';
                $msgType = 'danger';
            }
        }
    }
}

// Handle delete
if ($action === 'delete' && $id > 0) {
    if ($id === (int)$_SESSION['user_id']) {
        header('Location: users.php?error=You cannot delete your own account.');
        exit;
    }
    $user->id = $id;
    if ($user->delete()) {
        header('Location: users.php?success=Account deleted.');
        exit;
    }
}

// Fetch edit data
if ($action === 'edit' && $id > 0) {
    $user->id = $id;
    $user->readOne();
}

if (isset($_GET['success'])) { $message = $_GET['success']; $msgType = 'success'; }
if (isset($_GET['error']))   { $message = $_GET['error'];   $msgType = 'danger'; }

$pageTitle = 'Staff Accounts';
include 'includes/header.php';
?>

<div class="page-header">
    <div class="page-header-left">
        <h2><?= $action === 'create' ? 'Add Staff Account' : ($action === 'edit' ? 'Edit Account' : 'Staff Management') ?></h2>
        <p>Manage system users and staff accounts.</p>
    </div>
    <?php if ($action === 'list'): ?>
    <a href="users.php?action=create" class="btn-primary-custom">
        <i class="bi bi-person-plus-fill"></i> Add Staff
    </a>
    <?php else: ?>
    <a href="users.php" class="btn-primary-custom" style="background:linear-gradient(135deg,#718096,#4a5568);">
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
        <h5><?= $action === 'create' ? 'New Staff Account' : 'Edit Account' ?></h5>
    </div>
    <div class="card-body-custom">
        <form method="POST" action="users.php">
            <input type="hidden" name="form_action" value="<?= $action === 'edit' ? 'update' : 'create' ?>">
            <?php if ($action === 'edit'): ?>
                <input type="hidden" name="id" value="<?= $id ?>">
            <?php endif; ?>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label-custom">Full Name *</label>
                    <input type="text" name="full_name" class="form-control"
                           value="<?= htmlspecialchars($user->full_name ?? '') ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label-custom">Username *</label>
                    <input type="text" name="username" class="form-control"
                           value="<?= htmlspecialchars($user->username ?? '') ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label-custom">
                        Password <?= $action === 'edit' ? '(leave blank to keep current)' : '*' ?>
                    </label>
                    <input type="password" name="password" class="form-control"
                           <?= $action === 'create' ? 'required' : '' ?>>
                </div>
                <div class="col-md-6">
                    <label class="form-label-custom">Role</label>
                    <select name="role" class="form-select">
                        <option value="staff"  <?= ($user->role ?? '') === 'staff'  ? 'selected' : '' ?>>Staff</option>
                        <option value="admin"  <?= ($user->role ?? '') === 'admin'  ? 'selected' : '' ?>>Admin</option>
                    </select>
                </div>
                <div class="col-12 pt-2">
                    <button type="submit" class="btn-success-custom">
                        <i class="bi bi-<?= $action === 'edit' ? 'pencil-square' : 'person-plus' ?>"></i>
                        <?= $action === 'edit' ? 'Update Account' : 'Create Account' ?>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php else: ?>
<div class="content-card">
    <div class="card-header-custom">
        <h5><i class="bi bi-people me-2" style="color:var(--electric);"></i>All Staff Accounts</h5>
    </div>
    <div style="overflow-x:auto;">
        <table class="table-custom">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Full Name</th>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $result = $user->readAll();
                $rows   = $result->fetchAll();
                $n = 1;
                foreach ($rows as $row):
                ?>
                <tr>
                    <td style="color:#a0aec0;"><?= $n++ ?></td>
                    <td>
                        <strong><?= htmlspecialchars($row['full_name']) ?></strong>
                        <?php if ($row['id'] == $_SESSION['user_id']): ?>
                            <span class="status-badge status-Processing ms-1" style="font-size:9px;">YOU</span>
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($row['username']) ?></td>
                    <td><span class="status-badge status-<?= $row['role'] ?>"><?= ucfirst($row['role']) ?></span></td>
                    <td style="color:#718096;"><?= date('M j, Y', strtotime($row['created_at'])) ?></td>
                    <td>
                        <a href="users.php?action=edit&id=<?= $row['id'] ?>"
                           class="action-btn action-edit" title="Edit"><i class="bi bi-pencil-fill"></i></a>
                        <?php if ($row['id'] != $_SESSION['user_id']): ?>
                        <form method="POST" action="users.php?action=delete&id=<?= $row['id'] ?>"
                              class="delete-form d-inline">
                            <button type="submit" class="action-btn action-delete" title="Delete">
                                <i class="bi bi-trash-fill"></i>
                            </button>
                        </form>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
