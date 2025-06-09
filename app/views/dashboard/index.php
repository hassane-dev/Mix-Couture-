<?php
use App\Core\Session;

// Ensure $data is available if used, or rely on Session for user info.
// This check should ideally be in the controller redirecting to login if not logged in.
if (!Session::isLoggedIn()) {
    Session::setFlash('error', 'Please log in to view the dashboard.');
    header('Location: ' . BASE_URL . '/auth/login');
    exit;
}

$userFullName = $data['fullName'] ?? Session::get('user_fullname') ?? 'User';
$userEmail = $data['email'] ?? Session::get('user_email') ?? '';

$pageTitle = 'Dashboard';
$activeNav = 'dashboard'; // For highlighting active link in header.php
require_once VIEWS_PATH . 'layout/header.php';
?>

<!-- Page specific content starts -->
<div class="p-5 mb-4 bg-light rounded-3">
    <div class="container-fluid py-5">
        <h1 class="display-5 fw-bold">Welcome to the Dashboard</h1>
        <p class="col-md-8 fs-4">
            This is a basic dashboard page for the Fashion Customization Platform.
            You are logged in as <?php echo htmlspecialchars($userEmail); ?>.
        </p>
        <hr>
        <p>From here, you can navigate to different management modules:</p>
        <ul>
            <li><a href="<?php echo BASE_URL; ?>/models">Manage Models</a></li>
            <li><a href="<?php echo BASE_URL; ?>/clients">Manage Clients</a></li>
            <li><a href="<?php echo BASE_URL; ?>/orders">Manage Orders</a></li>
            <li><a href="<?php echo BASE_URL; ?>/expenses">Manage Expenses</a></li>
            <?php if (Session::get('user_role_id') == 1 /* Assuming 1 is Admin RoleID */): ?>
                <li><a href="<?php echo BASE_URL; ?>/users">Manage Staff</a></li>
            <?php endif; ?>
        </ul>
    </div>
</div>
<!-- Page specific content ends -->

<?php require_once VIEWS_PATH . 'layout/footer.php'; ?>
