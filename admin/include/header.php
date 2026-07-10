<?php
$logoExists = file_exists(__DIR__ . "/../../images/Cybervisionlogo.png");
$logoSrc    = "../../images/Cybervisionlogo.png";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../css/style.css">
    <title><?= $title ?> | ChairHive Admin</title>
    <style>
        .admin-sidebar { min-height:100vh; background-color:#0E7490; }
        .admin-sidebar .nav-link { color:rgba(255,255,255,0.65); border-left:3px solid transparent; border-radius:0; font-size:0.88rem; padding:10px 16px; }
        .admin-sidebar .nav-link:hover { color:#fff; background:rgba(255,255,255,0.07); }
        .admin-sidebar .nav-link.active { color:#fff; background:rgba(255,255,255,0.1); border-left-color:rgba(255,255,255,0.6); }
        .admin-sidebar .sidebar-divider { border-color:rgba(255,255,255,0.15); }
        .admin-main { background:#F0F9FF; min-height:100vh; }
        .cv-table th { background-color:#0E7490 !important; }
        a { color:#0E7490; }
        a:hover { color:#0891B2; }
        .btn-cv { background:#0E7490; color:#fff; border:none; border-radius:8px; padding:8px 16px; font-size:0.85rem; font-weight:600; cursor:pointer; display:inline-flex; align-items:center; gap:6px; font-family:inherit; text-decoration:none; transition:background 0.2s; }
        .btn-cv:hover { background:#0891B2; color:#fff; text-decoration:none; }
        .btn-cv-outline { background:transparent; color:#0E7490; border:1.5px solid #0E7490; border-radius:8px; padding:8px 16px; font-size:0.85rem; font-weight:600; cursor:pointer; display:inline-flex; align-items:center; gap:6px; font-family:inherit; text-decoration:none; transition:all 0.2s; }
        .btn-cv-outline:hover { background:#0E7490; color:#fff; text-decoration:none; }
    </style>
</head>
<body style="background:#F0F9FF;">

<!-- Group Bar - appears on ALL pages -->
<div style="background:#0E7490;color:rgba(255,255,255,0.8);text-align:center;padding:6px 16px;font-size:0.78rem;">
    <?php if ($logoExists): ?><img src="<?= $logoSrc ?>" alt="CyberVision" style="height:14px;width:auto;vertical-align:-2px;margin-right:6px;"><?php endif; ?>
    <strong>CyberVision</strong> &mdash; School Final Project
</div>

<div class="container-fluid p-0">
    <div class="row g-0">

        <!-- Sidebar -->
        <div class="col-md-2 admin-sidebar d-flex flex-column" style="min-height:calc(100vh - 30px);">
            <div class="p-3 border-bottom" style="border-color:rgba(255,255,255,0.15)!important;">
                <a href="../index.php" class="text-decoration-none text-white fw-bold d-flex align-items-center gap-2">
                    <?php if ($logoExists): ?>
                        <img src="<?= $logoSrc ?>" alt="CyberVision" style="height:26px;width:auto;">
                    <?php endif; ?>
                    <span style="font-family:'Poppins',sans-serif;">ChairHive</span>
                </a>
                <div style="font-size:0.72rem;color:rgba(255,255,255,0.45);margin-top:2px;">Admin Panel</div>
            </div>

            <div class="px-3 py-2 border-bottom" style="border-color:rgba(255,255,255,0.15)!important;">
                <div style="font-size:0.72rem;color:rgba(255,255,255,0.45);">Logged in as:</div>
                <div class="text-white fw-bold" style="font-size:0.84rem;">
                    <i class="bi bi-person-circle"></i> <?= htmlspecialchars($_SESSION['fullname'] ?? '') ?>
                </div>
            </div>

            <nav class="nav flex-column p-2 mt-1 flex-grow-1">
                <a class="nav-link mb-1 <?= (isset($adminNav) && $adminNav == 'dashboard') ? 'active' : '' ?>" href="index.php">
                    <i class="bi bi-speedometer2 me-2"></i> Dashboard
                </a>
                <a class="nav-link mb-1 <?= (isset($adminNav) && $adminNav == 'inventory') ? 'active' : '' ?>" href="inventory.php">
                    <i class="bi bi-box-seam me-2"></i> Inventory &amp; Pricing
                </a>
                <a class="nav-link mb-1 <?= (isset($adminNav) && $adminNav == 'users') ? 'active' : '' ?>" href="users.php">
                    <i class="bi bi-people me-2"></i> Admin Users
                </a>
                <a class="nav-link mb-1 <?= (isset($adminNav) && $adminNav == 'reports') ? 'active' : '' ?>" href="reports.php">
                    <i class="bi bi-bar-chart-line me-2"></i> Reports
                </a>
                <hr class="sidebar-divider my-2">
                <a class="nav-link mb-1" style="color:rgba(255,120,120,0.8);" href="logout.php">
                    <i class="bi bi-box-arrow-left me-2"></i> Logout
                </a>
                <a class="nav-link" style="color:rgba(255,255,255,0.35);font-size:0.8rem;" href="../index.php">
                    <i class="bi bi-arrow-left-circle me-2"></i> View Live Site
                </a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="col-md-10 admin-main p-4">
