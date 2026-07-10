<?php
session_start();
if (!isset($_SESSION['islogged']) || !isset($_SESSION['isadmin'])) { header('Location: ../login.php'); exit(); }
require_once('../db.php');

$message = "";
$type    = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id             = (int)($_POST['id'] ?? 0);
    $full_name      = mysqli_real_escape_string($conn, $_POST['full_name']);
    $email          = mysqli_real_escape_string($conn, $_POST['email']);
    $address        = mysqli_real_escape_string($conn, $_POST['address'] ?? '');
    $contact_number = mysqli_real_escape_string($conn, $_POST['contact_number'] ?? '');
    $password       = $_POST['password'] ?? '';
    $is_active      = isset($_POST['is_active']) ? 1 : 0;

    $errors = array();
    if (empty($full_name)) $errors[] = "Full name is required.";
    if (empty($email))     $errors[] = "Email address is required.";
    if ($id == 0 && strlen($password) < 8) $errors[] = "Password must be at least 8 characters.";
    if ($id != 0 && $password != '' && strlen($password) < 8) $errors[] = "New password must be at least 8 characters.";

    if (empty($errors)) {
        $check = mysqli_query($conn, "SELECT id FROM users WHERE email='$email' AND id!=$id LIMIT 1");
        if (mysqli_num_rows($check) > 0) $errors[] = "Another account already uses that email address.";
    }

    if (!empty($errors)) {
        $message = implode(" ", $errors);
        $type    = "danger";
    } else {
        if ($id == 0) {
            $hash = mysqli_real_escape_string($conn, password_hash($password, PASSWORD_DEFAULT));
            mysqli_query($conn, "INSERT INTO users (full_name,email,password_hash,address,contact_number,role,is_verified,is_active)
                                 VALUES ('$full_name','$email','$hash','$address','$contact_number','admin',1,$is_active)");
            $uid  = (int)$_SESSION['user_id'];
            $aname = mysqli_real_escape_string($conn, $_SESSION['fullname']);
            $desc  = mysqli_real_escape_string($conn, $_SESSION['fullname'] . " added admin: $full_name ($email).");
            mysqli_query($conn, "INSERT INTO audit_log (user_id,actor_name,actor_role,action,description) VALUES ($uid,'$aname','admin','ADD_ADMIN','$desc')");
            $message = "Admin user \"$full_name\" was added successfully.";
            $type    = "success";
        } else {
            if ($password != '') {
                $hash = mysqli_real_escape_string($conn, password_hash($password, PASSWORD_DEFAULT));
                mysqli_query($conn, "UPDATE users SET full_name='$full_name',email='$email',password_hash='$hash',address='$address',contact_number='$contact_number',is_active=$is_active WHERE id=$id AND role='admin'");
            } else {
                mysqli_query($conn, "UPDATE users SET full_name='$full_name',email='$email',address='$address',contact_number='$contact_number',is_active=$is_active WHERE id=$id AND role='admin'");
            }
            $uid  = (int)$_SESSION['user_id'];
            $aname = mysqli_real_escape_string($conn, $_SESSION['fullname']);
            $desc  = mysqli_real_escape_string($conn, $_SESSION['fullname'] . " updated admin: $full_name ($email).");
            mysqli_query($conn, "INSERT INTO audit_log (user_id,actor_name,actor_role,action,description) VALUES ($uid,'$aname','admin','EDIT_ADMIN','$desc')");
            $message = "Admin user \"$full_name\" was updated.";
            $type    = "success";
        }
    }
}

$editingUser = null;
if (isset($_GET['edit'])) {
    $edit_id     = (int)$_GET['edit'];
    $edit_result = mysqli_query($conn, "SELECT * FROM users WHERE id=$edit_id AND role='admin'");
    $editingUser = mysqli_fetch_assoc($edit_result);
}

$admins_result = mysqli_query($conn, "SELECT * FROM users WHERE role='admin' ORDER BY date_created DESC");

$title    = "Admin Users";
$adminNav = "users";
require('include/header.php');
?>

<div class="mb-4">
    <h4 class="fw-bold mb-0"><i class="bi bi-people"></i> Admin Users</h4>
    <p class="text-muted small">Add or modify accounts allowed to manage this store.</p>
</div>

<?php if ($message): ?>
    <div class="cv-alert cv-alert-<?= $type ?> mb-4"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>

<!-- Add / Edit Form -->
<div class="cv-card mb-4">
    <p class="cv-card-title"><?= $editingUser ? 'Edit Admin User' : 'Add New Admin User' ?></p>
    <form action="users.php" method="post" novalidate>
        <input type="hidden" name="id" value="<?= $editingUser ? (int)$editingUser['id'] : 0 ?>">
        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label class="cv-form-label">Full Name</label>
                <input type="text" name="full_name" class="cv-form-control" value="<?= htmlspecialchars($editingUser['full_name'] ?? '') ?>">
            </div>
            <div class="col-md-6">
                <label class="cv-form-label">Email Address</label>
                <input type="email" name="email" class="cv-form-control" value="<?= htmlspecialchars($editingUser['email'] ?? '') ?>">
            </div>
        </div>
        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label class="cv-form-label"><?= $editingUser ? 'New Password (leave blank to keep)' : 'Password' ?></label>
                <input type="password" name="password" class="cv-form-control">
            </div>
            <div class="col-md-6">
                <label class="cv-form-label">Contact Number</label>
                <input type="text" name="contact_number" class="cv-form-control" value="<?= htmlspecialchars($editingUser['contact_number'] ?? '') ?>">
            </div>
        </div>
        <div class="cv-mb">
            <label class="cv-form-label">Address</label>
            <input type="text" name="address" class="cv-form-control" value="<?= htmlspecialchars($editingUser['address'] ?? '') ?>">
        </div>
        <div class="cv-mb">
            <label style="display:flex;align-items:center;gap:8px;font-size:0.88rem;cursor:pointer;">
                <input type="checkbox" name="is_active" value="1" style="width:auto;accent-color:#0E7490;"
                       <?= (!$editingUser || $editingUser['is_active']) ? 'checked' : '' ?>>
                Active (uncheck to disable this admin's login)
            </label>
        </div>
        <div class="d-flex gap-2">
            <button type="submit" name="submit" class="btn-cv"><?= $editingUser ? 'Save Changes' : 'Add Admin User' ?></button>
            <?php if ($editingUser): ?>
                <a href="users.php" class="btn-cv-outline">Cancel</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- Table -->
<div class="cv-card">
    <p class="cv-card-title">All Admin Users</p>
    <div class="cv-table-wrap">
        <table class="cv-table">
            <thead>
                <tr><th>#</th><th>Name</th><th>Email</th><th>Contact</th><th>Status</th><th>Added</th><th></th></tr>
            </thead>
            <tbody>
                <?php
                $counter = 1;
                while ($a = mysqli_fetch_assoc($admins_result)):
                ?>
                    <tr>
                        <td><?= $counter++ ?></td>
                        <td class="fw-semibold"><?= htmlspecialchars($a['full_name']) ?></td>
                        <td><?= htmlspecialchars($a['email']) ?></td>
                        <td><?= htmlspecialchars($a['contact_number']) ?></td>
                        <td>
                            <?php if ($a['is_active']): ?>
                                <span class="badge" style="background:#16A34A;">Active</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Disabled</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-muted small"><?= date("M d, Y", strtotime($a['date_created'])) ?></td>
                        <td><a href="users.php?edit=<?= (int)$a['id'] ?>" class="btn-cv-outline" style="padding:5px 12px;font-size:0.8rem;"><i class="bi bi-pencil"></i> Edit</a></td>
                    </tr>
                <?php endwhile; ?>
                <?php if (mysqli_num_rows($admins_result) == 0): ?>
                    <tr><td colspan="7" class="text-center text-muted" style="padding:24px;">No admin users found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php mysqli_close($conn); require('include/footer.php'); ?>
