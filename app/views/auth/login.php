<?php
use App\Core\Session;

$pageTitle = 'Admin Login';
$activeNav = 'login'; // Or null; header logic might need to handle this state (e.g., not highlight any main nav)

// For login, we might want a simpler header/footer. For now, using the main one.
// The specific styling for login (centered form) will be affected by the main layout.
// The header includes flash message display, so no need to repeat here.
require_once VIEWS_PATH . 'layout/header.php';
?>

<!-- Page specific content for login -->
<div class="row justify-content-center">
    <div class="col-md-6 col-lg-4">
        <div class="login-container p-4 border rounded-3 shadow-sm bg-white" style="margin-top: 20px; margin-bottom: 20px;">
            <h2 class="text-center mb-4">Admin Login</h2>

            <?php /* Flash messages are now handled by header.php
            $errorMessage = Session::getFlash('error');
            if ($errorMessage): ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo htmlspecialchars($errorMessage); ?>
                </div>
            <?php endif; ?>

            <?php $successMessage = Session::getFlash('success');
            if ($successMessage): ?>
                <div class="alert alert-success" role="alert">
                    <?php echo htmlspecialchars($successMessage); ?>
                </div>
            <?php endif; */?>

            <form action="<?php echo BASE_URL; ?>/auth/handleLogin" method="POST">
                <div class="mb-3">
                    <label for="email" class="form-label">Email address</label>
                    <input type="email" class="form-control" id="email" name="email" required autofocus>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Login</button>
            </form>
        </div>
    </div>
</div>
<!-- Page specific content ends -->

<?php require_once VIEWS_PATH . 'layout/footer.php'; ?>
