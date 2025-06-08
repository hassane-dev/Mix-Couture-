<?php
// app/views/clients/edit.php
use App\Core\Session;

// $client variable is passed from ClientController::edit()
// For standalone running, ensure Session is available or started
if (class_exists('App\Core\Session')) {
    // Session::start(); // Should be started by index.php
} else {
    // Fallback if running parts of this standalone (not recommended for full app)
    if (session_status() === PHP_SESSION_NONE) session_start();
}

if (!isset($client) || !$client) {
    Session::setFlash('error', 'Client data not found or invalid.');
    // In a real app, you might redirect or show a more specific error page.
    // For this snippet, we'll just show an error if $client isn't properly passed.
    // header('Location: ' . BASE_URL . '/clients');
    // exit;
    // Fallback if redirect isn't desired here:
    echo "<div class='alert alert-danger'>Error: Client data is missing. Cannot render edit form.</div>";
    // You might want to stop script execution or include a full HTML error page.
    return; // Stop rendering if no client data
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Client - <?php echo htmlspecialchars($client['FullName'] ?? 'Client'); ?> - Fashion Platform</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Basic styling */
        .container { max-width: 700px; }
        .card-header { background-color: #007bff; color: white; }
    </style>
</head>
<body>
    <?php
        // Conceptual: This would be part of your main layout/header include
        // Simulating a basic nav from the dashboard for context
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
                    <li class="nav-item"><a class="nav-link active" aria-current="page" href="<?php echo BASE_URL; ?>/clients">Clients</a></li>
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
        <div class="card">
            <div class="card-header">
                <h1 class="h3 mb-0">Edit Client: <?php echo htmlspecialchars($client['FullName'] ?? ''); ?></h1>
            </div>
            <div class="card-body">
                <?php $errorMessage = Session::getFlash('error'); ?>
                <?php if ($errorMessage): ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo htmlspecialchars($errorMessage); ?>
                    </div>
                <?php endif; ?>

                <form action="<?php echo BASE_URL; ?>/clients/update/<?php echo htmlspecialchars($client['ClientID'] ?? 0); ?>" method="POST">
                    <?php
                        // Include the form partial
                        // $client is passed from the controller
                        // $errors and $old_data might be flashed by the controller
                        $old_data = Session::getFlash('old_data') ?? [];
                        // $client variable is already set from controller data
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
