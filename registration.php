<?php
session_start();

$form_data  = isset($_SESSION['form_data']) ? $_SESSION['form_data'] : array();
unset($_SESSION['form_data']);
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
    <title>Register</title>
</head>
<body>

<div class="cv-auth-page">
    <div class="cv-auth-body">
        <div class="cv-auth-box wide">

            <div class="cv-auth-logo">
                <?php if ($logoExists): ?>
                    <img src="images/Cybervisionlogo.png" alt="CyberVision">
                <?php endif; ?>
                <span class="cv-auth-logo-name">ChairHive</span>
            </div>

            <h2 class="cv-auth-title">Create an Account</h2>
            <p class="cv-auth-sub">Register to start shopping at ChairHive.</p>

            <?php if (isset($_SESSION['errors'])): ?>
                <div class="cv-alert cv-alert-danger">
                    <?php foreach ($_SESSION['errors'] as $error): ?>
                        <p><i class="bi bi-exclamation-circle"></i> <?= htmlspecialchars($error) ?></p>
                    <?php endforeach; ?>
                </div>
                <?php unset($_SESSION['errors']); ?>
            <?php endif; ?>

            <form action="checkregistration.php" method="post" novalidate>

                <div class="cv-mb">
                    <label class="cv-form-label">Complete Name</label>
                    <input type="text" name="full_name" class="cv-form-control"
                           value="<?= htmlspecialchars($form_data['full_name'] ?? '') ?>">
                </div>

                <div class="cv-mb">
                    <label class="cv-form-label">Email Address</label>
                    <input type="email" name="email" class="cv-form-control"
                           value="<?= htmlspecialchars($form_data['email'] ?? '') ?>">
                </div>

                <div class="cv-form-row cv-mb">
                    <div>
                        <label class="cv-form-label">Password</label>
                        <input type="password" name="password" class="cv-form-control">
                        <p class="cv-form-hint">At least 8 characters.</p>
                    </div>
                    <div>
                        <label class="cv-form-label">Confirm Password</label>
                        <input type="password" name="confirm_password" class="cv-form-control">
                    </div>
                </div>

                <div class="cv-mb">
                    <label class="cv-form-label">Complete Address</label>
                    <textarea name="address" class="cv-form-control"><?= htmlspecialchars($form_data['address'] ?? '') ?></textarea>
                </div>

                <div class="cv-mb">
                    <label class="cv-form-label">Contact Number</label>
                    <input type="text" name="contact_number" class="cv-form-control"
                           placeholder="e.g. 0917 123 4567"
                           value="<?= htmlspecialchars($form_data['contact_number'] ?? '') ?>">
                </div>

                <button type="submit" name="submit" class="btn-cv w-100 justify-content-center" style="padding:12px;">
                    Create Account
                </button>
            </form>

            <p class="cv-auth-switch">
                Already have an account? <a href="login.php">Log in here</a>
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
