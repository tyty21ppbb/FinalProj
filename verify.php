<?php
session_start();
require_once('db.php');

$token   = isset($_GET['token']) ? $_GET['token'] : '';
$success = false;
$message = "Invalid or expired verification link.";

if ($token != '') {
    $token_esc = mysqli_real_escape_string($conn, $token);
    $result    = mysqli_query($conn, "SELECT id, full_name, is_verified FROM users WHERE verify_token = '$token_esc' LIMIT 1");
    $user      = mysqli_fetch_assoc($result);

    if ($user) {
        if ((int)$user['is_verified'] === 1) {
            $success = true;
            $message = "This account is already verified. You can log in.";
        } else {
            $id = (int)$user['id'];
            mysqli_query($conn, "UPDATE users SET is_verified = 1, verify_token = NULL WHERE id = $id");
            $name_esc  = mysqli_real_escape_string($conn, $user['full_name']);
            mysqli_query($conn, "INSERT INTO audit_log (user_id, actor_name, actor_role, action, description)
                                 VALUES ($id, '$name_esc', 'buyer', 'VERIFY_EMAIL', '$name_esc verified their email address.')");
            $success = true;
            $message = "Your email has been confirmed! You can now log in.";
        }
    }
}
mysqli_close($conn);

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
    <title>ChairHive - Email Verification</title>
</head>
<body>
<div class="cv-group-bar">
    <?php if ($logoExists): ?><img src="images/Cybervisionlogo.png" alt="CyberVision"><?php endif; ?>
    <strong>CyberVision</strong> &mdash; School Final Project
</div>

<div class="cv-auth-page">
    <div class="cv-auth-body">
        <div class="cv-auth-box" style="text-align:center;">

            <div class="cv-auth-logo">
                <?php if ($logoExists): ?>
                    <img src="images/Cybervisionlogo.png" alt="CyberVision">
                <?php endif; ?>
                <span class="cv-auth-logo-name">ChairHive</span>
            </div>

            <?php if ($success): ?>
                <i class="bi bi-check-circle-fill text-success" style="font-size:3.5rem;color:#16A34A!important;"></i>
                <h3 class="fw-bold mt-3">Email Confirmed!</h3>
            <?php else: ?>
                <i class="bi bi-x-circle-fill" style="font-size:3.5rem;color:#DC2626;"></i>
                <h3 class="fw-bold mt-3">Verification Failed</h3>
            <?php endif; ?>

            <div class="cv-alert <?= $success ? 'cv-alert-success' : 'cv-alert-danger' ?> mt-3" style="text-align:left;">
                <?= htmlspecialchars($message) ?>
            </div>

            <a href="<?= $success ? 'login.php' : 'registration.php' ?>" class="btn-cv" style="padding:12px 28px;">
                <?= $success ? 'Go to Login' : 'Back to Registration' ?>
            </a>

        </div>
    </div>
    <div class="cv-auth-footer">
        <strong>Disclaimer:</strong> This website was created for educational purposes only and is a requirement for our final project.
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>
</html>
