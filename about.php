<?php
session_start();
require_once 'classes/Auth.php';
Auth::check();
$pageTitle = 'About Project';
include 'includes/header.php';
?>

<div class="page-header">
    <div class="page-header-left">
        <h2>About This Project</h2>
        <p>System overview, architecture, and technical details.</p>
    </div>
</div>

<!-- System Overview -->
<div class="content-card">
    <div class="card-header-custom">
        <h5><i class="bi bi-info-circle me-2" style="color:var(--electric);"></i>System Overview</h5>
    </div>
    <div class="card-body-custom">
        <div class="row g-4">
            <div class="col-md-8">
                <h6 style="color:var(--navy); font-weight:700; margin-bottom:10px;">Smart Order Management System</h6>
                <p style="color:#4a5568; line-height:1.8; font-size:14px;">
                    A web-based order management system developed for ITEL 203 — Web Systems and Technologies
                    at Laguna State Polytechnic University. The system solves the problems of manual business
                    record-keeping by providing a centralized, secure, and relational platform for managing
                    customers, orders, and staff transactions.
                </p>
                <div class="mt-3">
                    <span class="tech-pill"><i class="bi bi-gear-fill"></i> PHP OOP</span>
                    <span class="tech-pill green"><i class="bi bi-database-fill"></i> MySQL</span>
                    <span class="tech-pill navy"><i class="bi bi-layout-text-window"></i> Bootstrap 5</span>
                    <span class="tech-pill orange"><i class="bi bi-code-slash"></i> PDO</span>
                    <span class="tech-pill"><i class="bi bi-shield-lock"></i> Session Auth</span>
                </div>
            </div>
            <div class="col-md-4">
                <div style="background:#f8fafc; border-radius:10px; padding:18px; border:1px solid #e2e8f0;">
                    <div style="font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.5px; color:#718096; margin-bottom:12px;">Project Details</div>
                    <div style="font-size:13px; color:#374151; line-height:2;">
                        <strong>Subject:</strong> ITEL 203<br>
                        <strong>Type:</strong> Group Performance Task 2<br>
                        <strong>Section:</strong> 2C<br>
                        <strong>School:</strong> LSPU San Pablo City Campus<br>
                        <strong>Stack:</strong> PHP + MySQL + Bootstrap
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Database Table Relationships -->
<div class="content-card">
    <div class="card-header-custom">
        <h5><i class="bi bi-diagram-3 me-2" style="color:var(--electric);"></i>Database Table Relationships</h5>
    </div>
    <div class="card-body-custom">
        <div class="row g-3">
            <div class="col-md-4">
                <div style="border:1.5px solid #e2e8f0; border-radius:10px; overflow:hidden;">
                    <div style="background:var(--navy); padding:12px 16px; color:#fff; font-weight:700; font-size:13px;">
                        <i class="bi bi-table me-2"></i>users
                    </div>
                    <div style="padding:14px 16px; font-size:12.5px; color:#374151; line-height:2;">
                        <code>id</code> INT PK AUTO_INCREMENT<br>
                        <code>username</code> VARCHAR(100) UNIQUE<br>
                        <code>password</code> VARCHAR(255)<br>
                        <code>full_name</code> VARCHAR(150)<br>
                        <code>role</code> VARCHAR(50)<br>
                        <code>created_at</code> TIMESTAMP
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div style="border:1.5px solid #e2e8f0; border-radius:10px; overflow:hidden;">
                    <div style="background:var(--electric); padding:12px 16px; color:#fff; font-weight:700; font-size:13px;">
                        <i class="bi bi-table me-2"></i>customers
                    </div>
                    <div style="padding:14px 16px; font-size:12.5px; color:#374151; line-height:2;">
                        <code>id</code> INT PK AUTO_INCREMENT<br>
                        <code>name</code> VARCHAR(150)<br>
                        <code>email</code> VARCHAR(150) UNIQUE<br>
                        <code>phone</code> VARCHAR(20)<br>
                        <code>address</code> TEXT<br>
                        <code>created_at</code> TIMESTAMP
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div style="border:1.5px solid #e2e8f0; border-radius:10px; overflow:hidden;">
                    <div style="background:var(--accent); padding:12px 16px; color:#fff; font-weight:700; font-size:13px;">
                        <i class="bi bi-table me-2"></i>orders
                    </div>
                    <div style="padding:14px 16px; font-size:12.5px; color:#374151; line-height:2;">
                        <code>id</code> INT PK AUTO_INCREMENT<br>
                        <code>customer_id</code> INT FK &rarr; customers<br>
                        <code>user_id</code> INT FK &rarr; users<br>
                        <code>product_name</code> VARCHAR(200)<br>
                        <code>quantity</code> INT<br>
                        <code>price</code> DECIMAL(10,2)<br>
                        <code>status</code> ENUM<br>
                        <code>notes</code> TEXT
                    </div>
                </div>
            </div>
        </div>
        <div class="mt-3 p-3" style="background:#f0f8ff; border-radius:8px; border-left:3px solid var(--electric);">
            <strong style="font-size:12px; color:var(--electric);">Relationships:</strong>
            <span style="font-size:12.5px; color:#374151; margin-left:8px;">
                users (1) &rarr; orders (Many) &nbsp;|&nbsp; customers (1) &rarr; orders (Many) &nbsp;|&nbsp;
                Foreign Keys with ON DELETE CASCADE
            </span>
        </div>
    </div>
</div>

<!-- OOP Structure -->
<div class="content-card">
    <div class="card-header-custom">
        <h5><i class="bi bi-boxes me-2" style="color:var(--electric);"></i>OOP Class Structure</h5>
    </div>
    <div class="card-body-custom">
        <div class="row g-3">
            <?php
            $classes = [
                ['Database.php',  'PDO connection, getConnection()',            'blue'],
                ['User.php',      'readAll, readOne, create, update, delete, usernameExists', 'blue'],
                ['Auth.php',      'login, logout, check (session protection), requireAdmin',  'green'],
                ['Customer.php',  'readAll (JOIN), readOne, create, update, delete, countAll, getDropdown', 'green'],
                ['Order.php',     'readAll (JOIN), readOne, create, update, delete, countAll, totalRevenue, recentOrders', 'orange'],
            ];
            foreach ($classes as $c): ?>
            <div class="col-md-6 col-lg-4">
                <div style="border:1px solid #e2e8f0; border-radius:8px; padding:14px;">
                    <div style="font-weight:700; color:var(--navy); margin-bottom:6px; font-size:13px;">
                        <i class="bi bi-file-earmark-code me-1" style="color:var(--electric);"></i>
                        <?= $c[0] ?>
                    </div>
                    <div style="font-size:11.5px; color:#718096;"><?= $c[1] ?></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
