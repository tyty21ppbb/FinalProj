<?php
session_start();
if (!isset($_SESSION['islogged']) || !isset($_SESSION['isadmin'])) { header('Location: ../login.php'); exit(); }
require_once('../db.php');

$message = "";
$type    = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id          = (int)($_POST['id'] ?? 0);
    $name        = mysqli_real_escape_string($conn, $_POST['name']);
    $category    = mysqli_real_escape_string($conn, $_POST['category']);
    $description = mysqli_real_escape_string($conn, $_POST['description'] ?? '');
    $price       = $_POST['price'] ?? '';
    $stock_qty   = $_POST['stock_qty'] ?? '';
    $is_active   = isset($_POST['is_active']) ? 1 : 0;

    $errors = array();
    if (empty($name))                                    $errors[] = "Chair name is required.";
    if (empty($category))                                $errors[] = "Category is required.";
    if (!is_numeric($price) || (float)$price < 0)       $errors[] = "Please enter a valid price.";
    if (!is_numeric($stock_qty) || (int)$stock_qty < 0) $errors[] = "Please enter a valid stock quantity.";

    if (!empty($errors)) {
        $message = implode(" ", $errors);
        $type    = "danger";
    } else {
        $price_val = (float)$price;
        $stock_val = (int)$stock_qty;

        if ($id == 0) {
            mysqli_query($conn, "INSERT INTO products (name,category,description,price,stock_qty,is_active)
                                 VALUES ('$name','$category','$description',$price_val,$stock_val,$is_active)");
            $uid  = (int)$_SESSION['user_id'];
            $aname = mysqli_real_escape_string($conn, $_SESSION['fullname']);
            $desc  = mysqli_real_escape_string($conn, $_SESSION['fullname'] . " added chair: $name (stock: $stock_val, price: ₱$price_val).");
            mysqli_query($conn, "INSERT INTO audit_log (user_id,actor_name,actor_role,action,description) VALUES ($uid,'$aname','admin','ADD_PRODUCT','$desc')");
            $message = "Chair \"$name\" was added successfully.";
            $type    = "success";
        } else {
            $old_row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT price,stock_qty FROM products WHERE id=$id"));
            mysqli_query($conn, "UPDATE products SET name='$name',category='$category',description='$description',price=$price_val,stock_qty=$stock_val,is_active=$is_active WHERE id=$id");
            $change = "";
            if ($old_row) {
                if ((float)$old_row['price'] != $price_val) $change .= " Price: ₱{$old_row['price']} → ₱$price_val.";
                if ((int)$old_row['stock_qty'] != $stock_val) $change .= " Stock: {$old_row['stock_qty']} → $stock_val.";
            }
            $uid  = (int)$_SESSION['user_id'];
            $aname = mysqli_real_escape_string($conn, $_SESSION['fullname']);
            $desc  = mysqli_real_escape_string($conn, $_SESSION['fullname'] . " updated chair: $name.$change");
            mysqli_query($conn, "INSERT INTO audit_log (user_id,actor_name,actor_role,action,description) VALUES ($uid,'$aname','admin','EDIT_PRODUCT','$desc')");
            $message = "Chair \"$name\" was updated.";
            $type    = "success";
        }
    }
}

$editingProduct = null;
if (isset($_GET['edit'])) {
    $edit_id        = (int)$_GET['edit'];
    $editingProduct = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM products WHERE id=$edit_id"));
}

$cat_result  = mysqli_query($conn, "SELECT DISTINCT category FROM products ORDER BY category");
$prod_result = mysqli_query($conn, "SELECT * FROM products ORDER BY category, name");

$title    = "Inventory & Pricing";
$adminNav = "inventory";
require('include/header.php');
?>

<div class="mb-4">
    <h4 class="fw-bold mb-0"><i class="bi bi-box-seam"></i> Inventory &amp; Pricing</h4>
    <p class="text-muted small">Add new chairs or update stock counts and prices for existing ones.</p>
</div>

<?php if ($message): ?>
    <div class="cv-alert cv-alert-<?= $type ?> mb-4"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>

<!-- Add / Edit Form -->
<div class="cv-card mb-4">
    <p class="cv-card-title"><?= $editingProduct ? 'Edit Chair' : 'Add New Chair' ?></p>
    <form action="inventory.php" method="post" novalidate>
        <input type="hidden" name="id" value="<?= $editingProduct ? (int)$editingProduct['id'] : 0 ?>">
        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label class="cv-form-label">Chair Name</label>
                <input type="text" name="name" class="cv-form-control" value="<?= htmlspecialchars($editingProduct['name'] ?? '') ?>">
            </div>
            <div class="col-md-6">
                <label class="cv-form-label">Category</label>
                <input type="text" name="category" class="cv-form-control" list="cat-list"
                       value="<?= htmlspecialchars($editingProduct['category'] ?? '') ?>">
                <datalist id="cat-list">
                    <?php while ($cat = mysqli_fetch_assoc($cat_result)): ?>
                        <option value="<?= htmlspecialchars($cat['category']) ?>">
                    <?php endwhile; ?>
                </datalist>
            </div>
        </div>
        <div class="cv-mb">
            <label class="cv-form-label">Description</label>
            <textarea name="description" class="cv-form-control" rows="2"><?= htmlspecialchars($editingProduct['description'] ?? '') ?></textarea>
        </div>
        <div class="row g-3 mb-3">
            <div class="col-md-4">
                <label class="cv-form-label">Price (&#8369;)</label>
                <input type="number" name="price" class="cv-form-control" step="0.01" min="0"
                       value="<?= htmlspecialchars($editingProduct['price'] ?? '') ?>">
            </div>
            <div class="col-md-4">
                <label class="cv-form-label">Stock Quantity</label>
                <input type="number" name="stock_qty" class="cv-form-control" min="0"
                       value="<?= htmlspecialchars($editingProduct['stock_qty'] ?? '') ?>">
            </div>
            <div class="col-md-4 d-flex align-items-end pb-1">
                <label style="display:flex;align-items:center;gap:8px;font-size:0.88rem;cursor:pointer;">
                    <input type="checkbox" name="is_active" value="1" style="width:auto;accent-color:#0E7490;"
                           <?= (!$editingProduct || $editingProduct['is_active']) ? 'checked' : '' ?>>
                    Visible in store
                </label>
            </div>
        </div>
        <div class="d-flex gap-2">
            <button type="submit" name="submit" class="btn-cv"><?= $editingProduct ? 'Save Changes' : 'Add Chair' ?></button>
            <?php if ($editingProduct): ?>
                <a href="inventory.php" class="btn-cv-outline">Cancel</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- Products Table -->
<div class="cv-card">
    <p class="cv-card-title">All Chairs</p>
    <div class="cv-table-wrap">
        <table class="cv-table">
            <thead>
                <tr><th>#</th><th>Chair Name</th><th>Category</th><th>Price</th><th>Stock</th><th>Status</th><th></th></tr>
            </thead>
            <tbody>
                <?php
                $counter = 1;
                while ($p = mysqli_fetch_assoc($prod_result)):
                ?>
                    <tr>
                        <td><?= $counter++ ?></td>
                        <td class="fw-semibold"><?= htmlspecialchars($p['name']) ?></td>
                        <td class="text-muted small"><?= htmlspecialchars($p['category']) ?></td>
                        <td>&#8369;<?= number_format($p['price'], 2) ?></td>
                        <td>
                            <?= (int)$p['stock_qty'] ?>
                            <?php if ((int)$p['stock_qty'] == 0): ?>
                                <span class="cv-badge-out">Out</span>
                            <?php elseif ((int)$p['stock_qty'] <= 5): ?>
                                <span class="cv-badge-low">Low</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($p['is_active']): ?>
                                <span class="badge" style="background:#16A34A;">Visible</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Hidden</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="inventory.php?edit=<?= (int)$p['id'] ?>" class="btn-cv-outline" style="padding:5px 12px;font-size:0.8rem;">
                                <i class="bi bi-pencil"></i> Edit
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
                <?php if (mysqli_num_rows($prod_result) == 0): ?>
                    <tr><td colspan="7" class="text-center text-muted" style="padding:24px;">No chairs found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php mysqli_close($conn); require('include/footer.php'); ?>
