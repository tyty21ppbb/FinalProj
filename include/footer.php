<?php
$logoExists = file_exists(__DIR__ . "/../images/Cybervisionlogo.png");
?>
<footer class="cv-footer">
    <div class="cv-footer-inner">
        <div>
            <?php if ($logoExists): ?>
    <img src="images/Cybervisionlogo.png" alt="CyberVision" class="cv-footer-brand-img">
<?php endif; ?>

            <div class="cv-footer-brand-name">ChairHive</div>
            <p>Office chairs built for the way you sit, work, and focus &mdash; ergonomic, executive, gaming, and more.</p>
        </div>
        <div>
            <h6>Quick Links</h6>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="store.php">Store</a></li>
                <li><a href="cart.php">Cart</a></li>
                <li><a href="about.php">About</a></li>
                <li><a href="login.php">Login</a></li>
                <li><a href="registration.php">Register</a></li>
            </ul>
        </div>
        <div>
            <h6>CyberVision</h6>
            <p>Web Development Final Project</p>
            <h6 style="margin-top:20px;">Chair Categories</h6>
            <ul>
                <li><a href="store.php#Ergonomic+Chairs">Ergonomic Chairs</a></li>
                <li><a href="store.php#Executive+Chairs">Executive Chairs</a></li>
                <li><a href="store.php#Gaming+Chairs">Gaming Chairs</a></li>
                <li><a href="store.php#Visitor+%26+Guest+Chairs">Visitor &amp; Guest</a></li>
                <li><a href="store.php#Stools+%26+Drafting+Chairs">Stools &amp; Drafting</a></li>
            </ul>
        </div>
    </div>
    <div class="cv-footer-bottom">
        <p>&copy; <?= date('Y') ?> ChairHive &mdash; CyberVision</p>
        <p class="cv-disclaimer">
            <strong>Disclaimer:</strong> This website was created for educational purposes only and is a requirement for our final project.
            No real products are sold and no real transactions are processed.
        </p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>
</html>
