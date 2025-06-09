</main> <!-- /.container -->

<footer class="footer mt-auto py-3 bg-light border-top">
    <div class="container text-center">
        <span class="text-muted">&copy; <?php echo date('Y'); ?> Fashion Platform. All rights reserved.</span>
    </div>
</footer>

<!-- Local Bootstrap Bundle JS -->
<script src="<?php echo BASE_URL; ?>/assets/js/bootstrap.bundle.min.js"></script>

<!-- Optional: Placeholder for additional page-specific JavaScript -->
<?php if (isset($pageScripts) && is_array($pageScripts)): ?>
    <?php foreach ($pageScripts as $script): ?>
        <script src="<?php echo BASE_URL . htmlspecialchars($script); ?>"></script>
    <?php endforeach; ?>
<?php endif; ?>

</body>
</html>
