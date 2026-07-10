<?php
session_start();
require_once('../db.php');

if (isset($_SESSION['islogged'])) {
    $name_esc = mysqli_real_escape_string($conn, $_SESSION['fullname']);
    $uid      = (int)$_SESSION['user_id'];
    $role_esc = mysqli_real_escape_string($conn, $_SESSION['role']);
    mysqli_query($conn, "INSERT INTO audit_log (user_id,actor_name,actor_role,action,description)
                         VALUES ($uid,'$name_esc','$role_esc','LOGOUT','$name_esc logged out.')");
}

mysqli_close($conn);

unset($_SESSION['islogged'], $_SESSION['isadmin'], $_SESSION['fullname'],
      $_SESSION['email'], $_SESSION['user_id'], $_SESSION['role']);
session_destroy();

header('Location: ../login.php');
exit();
?>
