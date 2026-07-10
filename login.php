<?php
session_start();

if (isset($_SESSION['islogged'])) {
    if ($_SESSION['role'] == 'admin') {
        header('Location: admin/index.php');
    } else {
        header('Location: index.php');
    }
    exit();
}

$redirect   = isset($_GET['redirect']) ? $_GET['redirect'] : 'index.php';
$logoExists = file_exists(__DIR__ . "/images/Cybervisionlogo.png");
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
    <title>ChairHive - Login</title>
</head>
<body>


<div class="cv-auth-page">
    <div class="cv-auth-body">
        <div class="cv-auth-box">

            <div class="cv-auth-logo">
                <?php if ($logoExists): ?>
                    <img src="images/Cybervisionlogo.png" alt="CyberVision">
                <?php endif; ?>
                <span class="cv-auth-logo-name">CyberVision</span>
            </div>

            <h2 class="cv-auth-title">Welcome back</h2>
            <p class="cv-auth-sub">Log in to your CyberVision account.</p>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="cv-alert cv-alert-danger">
                    <i class="bi bi-exclamation-circle"></i>
                    <?= htmlspecialchars($_SESSION['error']) ?>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="cv-alert cv-alert-success">
                    <i class="bi bi-check-circle"></i>
                    <?= htmlspecialchars($_SESSION['success']) ?>
                </div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['verify_link'])): ?>
                <div class="cv-alert cv-alert-warning">
                    <strong><i class="bi bi-envelope-exclamation"></i> Local Testing:</strong>
                    Mail couldn't send on localhost. Click this link to verify:<br>
                    <a href="<?= htmlspecialchars($_SESSION['verify_link']) ?>" style="word-break:break-all;font-size:0.8rem;">
                        <?= htmlspecialchars($_SESSION['verify_link']) ?>
                    </a>
                </div>
                <?php unset($_SESSION['verify_link']); ?>
            <?php endif; ?>

            <form action="checklogin.php" method="post" novalidate>
                <input type="hidden" name="redirect" value="<?= htmlspecialchars($redirect) ?>">

                <div class="cv-mb">
                    <label class="cv-form-label">Email Address</label>
                    <input type="email" name="email" class="cv-form-control">
                </div>
                <div class="cv-mb">
                    <label class="cv-form-label">Password</label>
                    <input type="password" name="password" class="cv-form-control">
                </div>
                <button type="submit" name="submit" class="btn-cv w-100 justify-content-center" style="padding:12px;">
                    <i class="bi bi-box-arrow-in-right"></i> Log In
                </button>
            </form>

            <p class="cv-auth-switch">
                No account yet? <a href="registration.php">Register here</a>
            </p>

        </div>
    </div>
    <div class="cv-auth-footer">
        <strong>Disclaimer:</strong> This website was created for educational purposes only and is a requirement for our final project.
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>
</html>
