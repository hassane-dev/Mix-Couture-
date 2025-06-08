<?php
// app/views/measurements/edit.php
use App\Core\Session;

// $measurement and $client variables are passed from MeasurementController::edit()
if (class_exists('App\Core\Session')) {
    // Session::start(); // Should be started by index.php
} else {
    if (session_status() === PHP_SESSION_NONE) session_start();
}

if (!isset($measurement) || !$measurement || !isset($client) || !$client) {
    Session::setFlash('error', 'Measurement or client data not found or invalid.');
    echo "<div class='alert alert-danger container mt-5'>Error: Measurement or client data is missing. Cannot render edit form. <a href='" . BASE_URL . "/clients'>Go back to client list.</a></div>";
    return;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Measurements for <?php echo htmlspecialchars($client['FullName']); ?> - Fashion Platform</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .container { max-width: 800px; }
        .card-header { background-color: #0dcaf0; color: white; } /* Info theme for measurements */
    </style>
</head>
<body>
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
                    <li class="nav-item"><a class="nav-link active" aria-current="page" href="<?php echo BASE_URL; ?>/measurements/index/<?php echo $client['ClientID']; ?>">Measurements for <?php echo htmlspecialchars($client['FullName']);?></a></li>
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
                <h1 class="h3 mb-0">Edit Measurement Set for: <?php echo htmlspecialchars($client['FullName']); ?></h1>
                <p class="mb-0 text-white-50">Set: <?php echo htmlspecialchars($measurement['MeasurementName'] ?? 'N/A'); ?> (ID: <?php echo htmlspecialchars($measurement['MeasurementID'] ?? 0); ?>)</p>
            </div>
            <div class="card-body">
                <?php $errorMessage = Session::getFlash('error'); ?>
                <?php if ($errorMessage): ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo htmlspecialchars($errorMessage); ?>
                    </div>
                <?php endif; ?>

                <form action="<?php echo BASE_URL; ?>/measurements/update/<?php echo htmlspecialchars($measurement['MeasurementID'] ?? 0); ?>" method="POST">
                    <?php
                        // $measurement and $client are passed from the controller
                        // $errors and $old_data might be flashed by the controller
                        $old_data = Session::getFlash('old_data') ?? $_POST;
                        // $measurement is already set
                        // $client is already set
                        // $errors = Session::getFlash('errors') ?? []; // Already handled in _form.php
                        require '_form.php';
                    ?>
                </form>
            </div>
        </div>
        <div class="mt-3">
            <a href="<?php echo BASE_URL; ?>/measurements/index/<?php echo $client['ClientID']; ?>" class="btn btn-outline-secondary">&laquo; Back to Measurements for <?php echo htmlspecialchars($client['FullName']); ?></a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
