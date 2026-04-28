<?php
// includes/header.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) . ' | ' : '' ?>Smart Order System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>

<div class="wrapper d-flex">
    <nav id="sidebar" class="sidebar-nav">
        <div class="sidebar-brand">
            <div class="brand-icon"><i class="bi bi-grid-3x3-gap-fill"></i></div>
            <div class="brand-text">
                <span class="brand-title">SmartOrder</span>
                <span class="brand-sub">Management System</span>
            </div>
        </div>

        <div class="sidebar-user">
            <div class="user-avatar"><?= strtoupper(substr($_SESSION['full_name'], 0, 1)) ?></div>
            <div class="user-info">
                <span class="user-name"><?= htmlspecialchars($_SESSION['full_name']) ?></span>
                <span class="user-role badge-role"><?= htmlspecialchars($_SESSION['role']) ?></span>
            </div>
        </div>

        <ul class="sidebar-menu">
            <li class="menu-label"><span>Main</span></li>
            <li class="<?= basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'active' : '' ?>">
                <a href="dashboard.php"><i class="bi bi-speedometer2"></i><span> Dashboard</span></a>
            </li>
            <li class="menu-label"><span>Management</span></li>
            <li class="<?= strpos(basename($_SERVER['PHP_SELF']), 'customer') !== false ? 'active' : '' ?>">
                <a href="customers.php"><i class="bi bi-people-fill"></i><span> Customers</span></a>
            </li>
            <li class="<?= strpos(basename($_SERVER['PHP_SELF']), 'order') !== false ? 'active' : '' ?>">
                <a href="orders.php"><i class="bi bi-bag-fill"></i><span> Orders</span></a>
            </li>
            <li class="menu-label"><span>Reports</span></li>
            <li class="<?= basename($_SERVER['PHP_SELF']) === 'reports.php' ? 'active' : '' ?>">
                <a href="reports.php"><i class="bi bi-bar-chart-fill"></i><span> Reports</span></a>
            </li>
            <li class="menu-label"><span>System</span></li>
            <?php if ($_SESSION['role'] === 'admin'): ?>
            <li class="<?= strpos(basename($_SERVER['PHP_SELF']), 'user') !== false ? 'active' : '' ?>">
                <a href="users.php"><i class="bi bi-person-gear"></i><span> Staff Accounts</span></a>
            </li>
            <?php endif; ?>
            <li class="<?= basename($_SERVER['PHP_SELF']) === 'about.php' ? 'active' : '' ?>">
                <a href="about.php"><i class="bi bi-info-circle-fill"></i><span> About Project</span></a>
            </li>
            <li class="<?= basename($_SERVER['PHP_SELF']) === 'developers.php' ? 'active' : '' ?>">
                <a href="developers.php"><i class="bi bi-code-slash"></i><span> Developers</span></a>
            </li>
        </ul>

        <div class="sidebar-footer">
            <a href="logout.php" class="logout-btn"><i class="bi bi-box-arrow-left"></i><span> Logout</span></a>
        </div>
    </nav>

    <div class="main-content flex-grow-1">
        <div class="top-bar">
            <button id="sidebarToggle" class="btn-toggle">
                <i class="bi bi-list"></i>
            </button>
            <div class="top-bar-right">
                <span class="top-user"><i class="bi bi-person-circle"></i> <?= htmlspecialchars($_SESSION['full_name']) ?></span>
                <a href="logout.php" class="btn btn-sm btn-outline-danger ms-3"><i class="bi bi-box-arrow-right"></i> Logout</a>
            </div>
        </div>
        <div class="page-content">