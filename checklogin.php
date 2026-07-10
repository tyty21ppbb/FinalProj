<?php
session_start();
require_once('db.php');

if (isset($_POST['submit'])) {

    $email    = $_POST['email'];
    $password = $_POST['password'];
    $redirect = isset($_POST['redirect']) ? $_POST['redirect'] : 'index.php';

    $email_esc = mysqli_real_escape_string($conn, $email);
    $result    = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email_esc' LIMIT 1");
    $user      = mysqli_fetch_assoc($result);

    if (!$user) {
        $_SESSION['error'] = "No account found with that email address.";
        header('Location: login.php');
        exit();
    }
    if (!password_verify($password, $user['password_hash'])) {
        $_SESSION['error'] = "Email and/or password is incorrect.";
        header('Location: login.php');
        exit();
    }
    if ((int)$user['is_active'] === 0) {
        $_SESSION['error'] = "This account has been deactivated. Please contact the store.";
        header('Location: login.php');
        exit();
    }
    if ($user['role'] == 'buyer' && (int)$user['is_verified'] === 0) {
        $_SESSION['error'] = "Please verify your email address before logging in. Check your inbox for the confirmation link.";
        header('Location: login.php');
        exit();
    }

    $_SESSION['islogged']  = true;
    $_SESSION['fullname']  = $user['full_name'];
    $_SESSION['email']     = $user['email'];
    $_SESSION['user_id']   = $user['id'];
    $_SESSION['role']      = $user['role'];
    if ($user['role'] == 'admin') $_SESSION['isadmin'] = true;

    $name_esc  = mysqli_real_escape_string($conn, $user['full_name']);
    $uid       = (int)$user['id'];
    $role_esc  = mysqli_real_escape_string($conn, $user['role']);
    mysqli_query($conn, "INSERT INTO audit_log (user_id, actor_name, actor_role, action, description)
                         VALUES ($uid, '$name_esc', '$role_esc', 'LOGIN', '$name_esc logged in.')");

    mysqli_close($conn);

    if ($user['role'] == 'admin') {
        header('Location: admin/index.php');
    } else {
        header('Location: ' . $redirect);
    }
    exit();
}

mysqli_close($conn);
header('Location: login.php');
exit();
?>
