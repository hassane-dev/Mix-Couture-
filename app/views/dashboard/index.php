<?php
// app/views/dashboard/index.php
// Ensure session is started and user is logged in (usually handled by AuthController)
// For safety, checking again, but AuthController should prevent direct access.
// App\Core\Session::start(); // Redundant if called in index.php and AuthController checks session
use App\Core\Session;

if (!Session::isLoggedIn()) {
    // This is a fallback, AuthController should have redirected.
    Session::setFlash('error', 'Please log in.');
    header('Location: ' . BASE_URL . '/auth/login');
    exit;
}

// $fullName is passed from AuthController's dashboard method via extract($data)
$userFullName = $data['fullName'] ?? Session::get('user_fullname') ?? 'User';
$userEmail = $data['email'] ?? Session::get('user_email') ?? '';
// $userRoleName = $data['roleName'] ?? Session::get('user_role_name') ?? 'N/A';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Fashion Platform</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .navbar {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?php echo BASE_URL; ?>/auth/dashboard">Fashion Platform</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <span class="navbar-text me-3">
                            Welcome, <?php echo htmlspecialchars($userFullName); ?>!
                        </span>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-danger" href="<?php echo BASE_URL; ?>/auth/logout">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="p-5 mb-4 bg-light rounded-3">
            <div class="container-fluid py-5">
                <h1 class="display-5 fw-bold">Welcome to the Dashboard</h1>
                <p class="col-md-8 fs-4">
                    This is a basic dashboard page for the Fashion Customization Platform.
                    You are logged in as <?php echo htmlspecialchars($userEmail); ?>.
                    <?php /* Role: <?php echo htmlspecialchars($userRoleName); ?> */ ?>
                </p>
                <hr>
                <p>From here, you would typically navigate to different management modules:</p>
                <ul>
                    <li><a href="#">Manage Models</a> (Not implemented in this snippet)</li>
                    <li><a href="#">Manage Clients</a> (Not implemented in this snippet)</li>
                    <li><a href="#">Manage Orders</a> (Not implemented in this snippet)</li>
                    <li><a href="#">Manage Expenses</a> (Not implemented in this snippet)</li>
                    <?php if (Session::get('user_role_id') == 1 /* Assuming 1 is Admin RoleID */): ?>
                        <li><a href="#">Manage Staff</a> (Not implemented in this snippet)</li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle (Popper.js included) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
