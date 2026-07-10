<?php
session_start();
if (!isset($_SESSION['islogged']) || !isset($_SESSION['isadmin'])) { header('Location: ../login.php'); exit(); }
require_once('../db.php');

$title    = "Dashboard";
$adminNav = "dashboard";

$total_products = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM products WHERE is_active=1"))['c'];
$low_stock      = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM products WHERE is_active=1 AND stock_qty<=5"))['c'];
$total_buyers   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM users WHERE role='buyer'"))['c'];
$total_admins   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM users WHERE role='admin'"))['c'];
$total_orders   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM orders"))['c'];
$total_revenue  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(SUM(total_amount),0) as s FROM orders"))['s'];

$audit_result = mysqli_query($conn, "SELECT * FROM audit_log ORDER BY date_created DESC LIMIT 10");
require('include/header.php');
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0">Dashboard</h4>
        <p class="text-muted mb-0 small">Quick overview of ChairHive.</p>
    </div>
    <span class="badge" style="background:#0E7490;font-size:0.82rem;padding:8px 14px;">
        <i class="bi bi-person-circle"></i> <?= htmlspecialchars($_SESSION['fullname']) ?>
    </span>
</div>

<!-- Stats -->
<div class="row row-cols-2 row-cols-md-3 g-3 mb-4">
    <?php
    $stats = array(
        array('label'=>'Active Chairs',     'value'=>$total_products, 'warn'=>false),
        array('label'=>'Low Stock (&le;5)', 'value'=>$low_stock,      'warn'=>$low_stock > 0),
        array('label'=>'Buyers',            'value'=>$total_buyers,   'warn'=>false),
        array('label'=>'Admin Users',       'value'=>$total_admins,   'warn'=>false),
        array('label'=>'Orders Placed',     'value'=>$total_orders,   'warn'=>false),
        array('label'=>'Total Revenue',     'value'=>'&#8369;'.number_format($total_revenue,2), 'warn'=>false, 'green'=>true),
    );
    foreach ($stats as $s):
    ?>
        <div class="col">
            <div class="cv-card h-100 <?= $s['warn'] ? 'border-danger' : '' ?>" style="<?= $s['warn'] ? 'border-color:#DC2626!important;' : '' ?>">
                <div class="text-muted small text-uppercase mb-1" style="font-size:0.72rem;letter-spacing:0.06em;"><?= $s['label'] ?></div>
                <div class="fw-bold" style="font-size:1.6rem;color:<?= $s['warn'] ? '#DC2626' : (isset($s['green']) ? '#16A34A' : '#0E7490') ?>;">
                    <?= $s['value'] ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- Recent Activity -->
<div class="cv-card">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <p class="cv-card-title mb-0">Recent Activity</p>
        <a href="reports.php" class="btn-cv-outline" style="padding:6px 14px;font-size:0.82rem;">View Full Audit Log &rarr;</a>
    </div>
    <div class="cv-table-wrap">
        <table class="cv-table">
            <thead>
                <tr><th>Date / Time</th><th>User</th><th>Action</th><th>Details</th></tr>
            </thead>
            <tbody>
                <?php
                $counter = 0;
                while ($log = mysqli_fetch_assoc($audit_result)):
                    $counter++;
                ?>
                    <tr>
                        <td class="small text-muted"><?= date("M d, Y g:i A", strtotime($log['date_created'])) ?></td>
                        <td><?= htmlspecialchars($log['actor_name']) ?></td>
                        <td><span class="badge" style="background:#0E7490;"><?= htmlspecialchars($log['action']) ?></span></td>
                        <td class="small"><?= htmlspecialchars($log['description']) ?></td>
                    </tr>
                <?php endwhile; ?>
                <?php if ($counter == 0): ?>
                    <tr><td colspan="4" class="text-center text-muted" style="padding:24px;">No activity recorded yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php mysqli_close($conn); require('include/footer.php'); ?>
