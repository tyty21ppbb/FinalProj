<?php
$logoExists = file_exists(__DIR__ . "/../images/Cybervisionlogo.png");
$logoSrc    = "images/Cybervisionlogo.png";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <title><?= htmlspecialchars($title) ?></title>
</head>
<body>

<!-- Group Bar - appears on ALL pages -->
<div class="cv-group-bar">
    <?php if ($logoExists): ?>
    <img src="<?= $logoSrc ?>" alt="CyberVision">
<?php endif; ?>

    <strong>CyberVision</strong> 
</div>

<!-- Navbar -->
<nav class="cv-navbar">
    <a href="index.php" class="cv-navbar-brand">
        <?php if ($logoExists): ?>
            <img src="<?= $logoSrc ?>" alt="CyberVision">
        <?php endif; ?>
        ChairHive
    </a>

    <ul class="cv-navbar-nav">
        <li><a href="index.php"  class="<?= (isset($currentPage) && $currentPage == 'home')  ? 'active' : '' ?>">Home</a></li>
        <li><a href="store.php"  class="<?= (isset($currentPage) && $currentPage == 'store') ? 'active' : '' ?>">Store</a></li>
        <li><a href="about.php"  class="<?= (isset($currentPage) && $currentPage == 'about') ? 'active' : '' ?>">About</a></li>
    </ul>

    <div class="cv-navbar-actions">
        <?php
        $cart_count = 0;
        if (isset($_SESSION['cart'])) {
            $cart_count = array_sum($_SESSION['cart']);
        }
        ?>
        <a href="cart.php" class="cv-cart-link <?= (isset($currentPage) && $currentPage == 'cart') ? 'active' : '' ?>">
            <i class="bi bi-cart3"></i> Cart
            <span class="cv-cart-badge"><?= $cart_count ?></span>
        </a>

        <?php if (isset($_SESSION['islogged']) && $_SESSION['role'] == 'admin'): ?>
            <a href="admin/index.php" class="cv-nav-admin-link">
                <i class="bi bi-speedometer2"></i> Admin
            </a>
        <?php endif; ?>

        <?php if (isset($_SESSION['islogged'])): ?>
            <span class="cv-nav-greeting">
                Hi, <?= htmlspecialchars(explode(' ', $_SESSION['fullname'])[0]) ?>!
            </span>
            <a href="logout.php" class="btn-nav-login">Logout</a>
        <?php else: ?>
            <a href="login.php"        class="btn-nav-login">Login</a>
            <a href="registration.php" class="btn-nav-register">Register</a>
        <?php endif; ?>
    </div>
</nav>
