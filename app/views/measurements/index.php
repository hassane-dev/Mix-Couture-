<?php
// app/views/measurements/index.php
use App\Core\Session;

// $measurements and $client variables are passed from MeasurementController::index()
if (class_exists('App\Core\Session')) {
    // Session::start(); // Should be started by index.php
} else {
    if (session_status() === PHP_SESSION_NONE) session_start();
}

if (!isset($client) || !$client) {
    Session::setFlash('error', 'Client data not found for measurements list.');
    // Redirect or show error
    echo "<div class='alert alert-danger container mt-5'>Error: Client data is missing. Cannot display measurements. <a href='" . BASE_URL . "/clients'>Go back to client list.</a></div>";
    return;
}
$measurements = $measurements ?? []; // Ensure $measurements is an array
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Measurements for <?php echo htmlspecialchars($client['FullName']); ?> - Fashion Platform</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .table-actions form { display: inline-block; margin-left: 5px; }
        .card-header { background-color: #0dcaf0; color: white; }
        .key-measurement { font-size: 0.9em; color: #555; }
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

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h1 class="h2">Measurements for: <?php echo htmlspecialchars($client['FullName']); ?></h1>
                <a href="<?php echo BASE_URL; ?>/clients" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Back to All Clients
                </a>
            </div>
            <a href="<?php echo BASE_URL; ?>/measurements/create/<?php echo htmlspecialchars($client['ClientID']); ?>" class="btn btn-info text-white">
                <i class="fas fa-plus"></i> Add New Measurement Set
            </a>
        </div>

        <?php $successMessage = Session::getFlash('success'); ?>
        <?php if ($successMessage): ?>
            <div class="alert alert-success" role="alert">
                <?php echo htmlspecialchars($successMessage); ?>
            </div>
        <?php endif; ?>

        <?php $errorMessage = Session::getFlash('error'); ?>
        <?php if ($errorMessage): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo htmlspecialchars($errorMessage); ?>
            </div>
        <?php endif; ?>

        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="mb-0">All Measurement Sets</h5>
            </div>
            <div class="card-body">
                <?php if (empty($measurements)): ?>
                    <p class="text-center">No measurement sets found for this client.
                        <a href="<?php echo BASE_URL; ?>/measurements/create/<?php echo htmlspecialchars($client['ClientID']); ?>">Add one now!</a>
                    </p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle">
                            <thead class="table-info">
                                <tr>
                                    <th>Set Name/Label</th>
                                    <th>Date Taken</th>
                                    <th>Bust</th>
                                    <th>Waist</th>
                                    <th>Hips</th>
                                    <th>Shoulder Width</th>
                                    <th>Sleeve Length</th>
                                    <th>Inseam</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($measurements as $set): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($set['MeasurementName']); ?></td>
                                        <td><?php echo htmlspecialchars(date('M j, Y', strtotime($set['DateTaken']))); ?></td>
                                        <td class="key-measurement"><?php echo htmlspecialchars($set['Bust'] ?? '-'); ?></td>
                                        <td class="key-measurement"><?php echo htmlspecialchars($set['Waist'] ?? '-'); ?></td>
                                        <td class="key-measurement"><?php echo htmlspecialchars($set['Hips'] ?? '-'); ?></td>
                                        <td class="key-measurement"><?php echo htmlspecialchars($set['ShoulderWidth'] ?? '-'); ?></td>
                                        <td class="key-measurement"><?php echo htmlspecialchars($set['SleeveLength'] ?? '-'); ?></td>
                                        <td class="key-measurement"><?php echo htmlspecialchars($set['Inseam'] ?? '-'); ?></td>
                                        <td><?php echo htmlspecialchars(date('M j, Y H:i', strtotime($set['CreatedAt']))); ?></td>
                                        <td class="table-actions">
                                            <a href="<?php echo BASE_URL; ?>/measurements/edit/<?php echo $set['MeasurementID']; ?>" class="btn btn-sm btn-outline-primary" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="<?php echo BASE_URL; ?>/measurements/destroy/<?php echo $set['MeasurementID']; ?>" method="POST" onsubmit="return confirm('Are you sure you want to delete this measurement set? This action might be irreversible.');">
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
