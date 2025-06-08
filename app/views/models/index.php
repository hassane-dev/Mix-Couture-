<?php
// app/views/models/index.php
use App\Core\Session;

// $models variable is passed from ModelController::index()
// Fallback for session
if (class_exists('App\Core\Session')) {
    // Session::start(); // Should be started by index.php
} else {
    if (session_status() === PHP_SESSION_NONE) session_start();
}

$models = $models ?? []; // Ensure $models is an array
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Clothing Models - Fashion Platform</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .model-photo-thumbnail {
            max-width: 75px;
            max-height: 75px;
            object-fit: cover;
            border-radius: 4px;
        }
        .table-actions form {
            display: inline-block;
            margin-left: 5px;
        }
        .card-header { background-color: #198754; color: white; }
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
            <h1 class="h2">Manage Clothing Models</h1>
            <a href="<?php echo BASE_URL; ?>/models/create" class="btn btn-success">
                <i class="fas fa-plus"></i> Add New Model
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
                <h5 class="mb-0">All Models</h5>
            </div>
            <div class="card-body">
                <?php if (empty($models)): ?>
                    <p class="text-center">No clothing models found. <a href="<?php echo BASE_URL; ?>/models/create">Add one now!</a></p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th>Photo</th>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Category</th>
                                    <th>Base Price</th>
                                    <th>Description</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($models as $model): ?>
                                    <tr>
                                        <td>
                                            <?php if (!empty($model['PhotoURL'])): ?>
                                                <img src="<?php echo BASE_URL . '/' . htmlspecialchars($model['PhotoURL']); ?>" alt="<?php echo htmlspecialchars($model['Name']); ?>" class="model-photo-thumbnail">
                                            <?php else: ?>
                                                <span class="text-muted">No photo</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($model['ModelID']); ?></td>
                                        <td><?php echo htmlspecialchars($model['Name']); ?></td>
                                        <td><?php echo htmlspecialchars($model['CategoryName'] ?? 'N/A'); ?></td>
                                        <td>$<?php echo htmlspecialchars(number_format((float)($model['BasePrice'] ?? 0), 2)); ?></td>
                                        <td><?php echo htmlspecialchars(substr($model['Description'] ?? '', 0, 50) . (strlen($model['Description'] ?? '') > 50 ? '...' : '')); ?></td>
                                        <td><?php echo htmlspecialchars(date('M j, Y H:i', strtotime($model['CreatedAt']))); ?></td>
                                        <td class="table-actions">
                                            <a href="<?php echo BASE_URL; ?>/models/edit/<?php echo $model['ModelID']; ?>" class="btn btn-sm btn-outline-primary" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="<?php echo BASE_URL; ?>/models/destroy/<?php echo $model['ModelID']; ?>" method="POST" onsubmit="return confirm('Are you sure you want to mark this model as deleted?');">
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete (Soft)">
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
