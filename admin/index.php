<?php
session_start();
require_once("db.php");

$title = "ChairHive - Home";

// Fetch active products
$result = mysqli_query($conn, "SELECT * FROM products WHERE is_active=1");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= $title ?></title>
    <link href="css/style.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<?php require("include/header.php"); ?>

<div class="container py-5">
    <h1 class="text-center mb-4">Welcome to ChairHive</h1>
    <p class="text-center text-muted mb-5">Premium seating solutions designed for comfort, productivity, and style.</p>

    <div class="row g-4">
        <?php while($row = mysqli_fetch_assoc($result)): ?>
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 shadow-sm">
                    <div class="card-body text-center">
                        <h5 class="card-title"><?= htmlspecialchars($row['name']) ?></h5>
                        <p class="text-secondary small"><?= htmlspecialchars($row['description']) ?></p>
                        <p class="fw-bold text-primary">₱<?= number_format($row['price'], 2) ?></p>
                        <form method="post" action="cart_action.php">
                            <input type="hidden" name="product_id" value="<?= $row['id'] ?>">
                            <button type="submit" class="btn btn-success">Add to Cart</button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<?php require("include/footer.php"); ?>

</body>
</html>
