<?php
session_start();
if (!isset($_SESSION['islogged']) || !isset($_SESSION['isadmin'])) { header('Location: ../login.php'); exit(); }
require_once('../db.php');

// Inventory report
$inv_result    = mysqli_query($conn, "SELECT * FROM products ORDER BY category, name");
$product_count = mysqli_num_rows($inv_result);
$totals        = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(stock_qty) as units, SUM(stock_qty*price) as value FROM products WHERE is_active=1"));
$total_units   = $totals['units'] ?? 0;
$total_value   = $totals['value'] ?? 0;

// Audit log report
$user_filter  = isset($_GET['user']) ? (int)$_GET['user'] : 0;
$audit_result = $user_filter > 0
    ? mysqli_query($conn, "SELECT * FROM audit_log WHERE user_id=$user_filter ORDER BY date_created DESC LIMIT 300")
    : mysqli_query($conn, "SELECT * FROM audit_log ORDER BY date_created DESC LIMIT 300");

$users_result = mysqli_query($conn, "SELECT DISTINCT user_id, actor_name FROM audit_log WHERE user_id IS NOT NULL ORDER BY actor_name");

$title    = "Reports";
$adminNav = "reports";
require('include/header.php');
?>

<div class="mb-4">
    <h4 class="fw-bold mb-0"><i class="bi bi-bar-chart-line"></i> Reports</h4>
    <p class="text-muted small">Inventory levels and the full system audit trail.</p>
</div>

<!-- Inventory Report -->
<div class="cv-card mb-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <p class="cv-card-title mb-0">Inventory Report</p>
        <span class="text-muted small">
            <?= $product_count ?> chairs &nbsp;&middot;&nbsp;
            <?= number_format($total_units) ?> units &nbsp;&middot;&nbsp;
            Total value: <strong>&#8369;<?= number_format($total_value, 2) ?></strong>
        </span>
    </div>
    <div class="cv-table-wrap">
        <table class="cv-table">
            <thead>
                <tr><th>#</th><th>Chair Name</th><th>Category</th><th>Price</th><th>Remaining Stock</th><th>Stock Value</th><th>Visibility</th></tr>
            </thead>
            <tbody>
                <?php
                $counter = 1;
                while ($p = mysqli_fetch_assoc($inv_result)):
                ?>
                    <tr>
                        <td><?= $counter++ ?></td>
                        <td class="fw-semibold"><?= htmlspecialchars($p['name']) ?></td>
                        <td class="text-muted small"><?= htmlspecialchars($p['category']) ?></td>
                        <td>&#8369;<?= number_format($p['price'], 2) ?></td>
                        <td>
                            <?= (int)$p['stock_qty'] ?>
                            <?php if ((int)$p['stock_qty'] == 0): ?>
                                <span class="cv-badge-out">Out of Stock</span>
                            <?php elseif ((int)$p['stock_qty'] <= 5): ?>
                                <span class="cv-badge-low">Low</span>
                            <?php endif; ?>
                        </td>
                        <td>&#8369;<?= number_format($p['price'] * $p['stock_qty'], 2) ?></td>
                        <td>
                            <?php if ($p['is_active']): ?>
                                <span class="badge" style="background:#16A34A;">Visible</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Hidden</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Audit Log Report -->
<div class="cv-card">
    <div class="d-flex justify-content-between align-items-center mb-1">
        <p class="cv-card-title mb-0">Audit Log Report</p>
        <span class="text-muted small">Last <?= mysqli_num_rows($audit_result) ?> activities</span>
    </div>
    <p class="text-muted small mb-3">Tracks every login, registration, order, inventory change, and admin-user change performed by whoever was logged in at the time.</p>

    <form action="reports.php" method="get" class="d-flex align-items-center gap-2 mb-3">
        <label class="cv-form-label mb-0" style="white-space:nowrap;">Filter by user:</label>
        <select name="user" class="cv-form-control" style="width:auto;" onchange="this.form.submit()">
            <option value="0" <?= $user_filter == 0 ? 'selected' : '' ?>>All Users</option>
            <?php while ($u = mysqli_fetch_assoc($users_result)): ?>
                <option value="<?= (int)$u['user_id'] ?>" <?= $user_filter == (int)$u['user_id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($u['actor_name']) ?>
                </option>
            <?php endwhile; ?>
        </select>
    </form>

    <div class="cv-table-wrap">
        <table class="cv-table">
            <thead>
                <tr><th>#</th><th>Date / Time</th><th>User</th><th>Role</th><th>Action</th><th>Details</th></tr>
            </thead>
            <tbody>
                <?php
                $counter = 1;
                while ($log = mysqli_fetch_assoc($audit_result)):
                ?>
                    <tr>
                        <td><?= $counter++ ?></td>
                        <td class="small text-muted"><?= date("M d, Y g:i A", strtotime($log['date_created'])) ?></td>
                        <td class="fw-semibold"><?= htmlspecialchars($log['actor_name']) ?></td>
                        <td>
                            <?php if ($log['actor_role'] == 'admin'): ?>
                                <span class="badge" style="background:#0E7490;">Admin</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Buyer</span>
                            <?php endif; ?>
                        </td>
                        <td><span class="badge" style="background:#0891B2;"><?= htmlspecialchars($log['action']) ?></span></td>
                        <td class="small"><?= htmlspecialchars($log['description']) ?></td>
                    </tr>
                <?php endwhile; ?>
                <?php if (mysqli_num_rows($audit_result) == 0): ?>
                    <tr><td colspan="6" class="text-center text-muted" style="padding:24px;">No activity recorded yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php mysqli_close($conn); require('include/footer.php'); ?>
