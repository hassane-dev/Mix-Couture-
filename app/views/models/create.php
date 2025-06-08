<?php
// app/views/models/create.php
use App\Core\Session;

// $categories is passed from ModelController::create()
// $errors (optional) and $old_data (optional) might be flashed by the controller on validation failure

// Fallback for session if not already started (ideally handled by index.php)
if (class_exists('App\Core\Session')) {
    // Session::start(); // Should be started by index.php
} else {
    if (session_status() === PHP_SESSION_NONE) session_start();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Clothing Model - Fashion Platform</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .container { max-width: 800px; }
        .card-header { background-color: #198754; color: white; } /* Green theme for models */
    </style>
</head>
<body>
    <?php
        // Conceptual: This would be part of your main layout/header include
    ?>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?php echo BASE_URL; ?>/auth/dashboard">Fashion Platform</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link" href="<?php echo BASE_URL; ?>/auth/dashboard">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?php echo BASE_URL; ?>/clients">Clients</a></li>
                    <li class="nav-item"><a class="nav-link active" aria-current="page" href="<?php echo BASE_URL; ?>/models">Models</a></li>
                    <?php /* Add other main navigation links here */ ?>
                </ul>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="btn btn-danger" href="<?php echo BASE_URL; ?>/auth/logout">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="card shadow-sm">
            <div class="card-header">
                <h1 class="h3 mb-0">Add New Clothing Model</h1>
            </div>
            <div class="card-body">
                <?php $errorMessage = Session::getFlash('error'); ?>
                <?php if ($errorMessage): ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo htmlspecialchars($errorMessage); ?>
                    </div>
                <?php endif; ?>

                <form action="<?php echo BASE_URL; ?>/models/store" method="POST" enctype="multipart/form-data">
                    <?php
                        // Include the form partial
                        // $model is not set here (it's for edit view)
                        // $categories is passed from the controller
                        // $errors and $old_data might be flashed by the controller
                        $old_data = Session::getFlash('old_data') ?? $_POST; // Use POST directly if not flashed (e.g. JS disabled client-side validation)
                        $model = null; // Explicitly null for create form context
                        // $categories should be passed from controller and available here
                        // $errors = Session::getFlash('errors') ?? []; // Already handled in _form.php
                        require '_form.php';
                    ?>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <?php
        // Conceptual: Include a common footer
        // require_once VIEWS_PATH . 'layout/footer.php';
    ?>
</body>
</html>
