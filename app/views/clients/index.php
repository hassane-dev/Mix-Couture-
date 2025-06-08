<?php
// app/views/clients/index.php
use App\Core\Session;

// $clients variable is passed from ClientController::index()
// For standalone running, ensure Session is available or started
if (class_exists('App\Core\Session')) {
    // Session::start(); // Should be started by index.php
} else {
    // Fallback if running parts of this standalone (not recommended for full app)
    if (session_status() === PHP_SESSION_NONE) session_start();
}

$clients = $clients ?? []; // Ensure $clients is an array, even if empty
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Clients - Fashion Platform</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"> <!-- For icons -->
    <style>
        /* Basic styling */
        .table-actions form {
            display: inline-block;
            margin-left: 5px;
        }
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

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h2">Manage Clients</h1>
            <a href="<?php echo BASE_URL; ?>/clients/create" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add New Client
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

        <div class="card">
            <div class="card-body">
                <?php if (empty($clients)): ?>
                    <p class="text-center">No clients found. <a href="<?php echo BASE_URL; ?>/clients/create">Add one now!</a></p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Full Name</th>
                                    <th>Email</th>
                                    <th>Phone Number</th>
                                    <th>Address</th>
                                    <th>Registered At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($clients as $client): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($client['ClientID']); ?></td>
                                        <td><?php echo htmlspecialchars($client['FullName']); ?></td>
                                        <td><?php echo htmlspecialchars($client['Email']); ?></td>
                                        <td><?php echo htmlspecialchars($client['PhoneNumber']); ?></td>
                                        <td><?php echo nl2br(htmlspecialchars($client['Address'] ?? 'N/A')); ?></td>
                                        <td><?php echo htmlspecialchars(date('M j, Y H:i', strtotime($client['CreatedAt']))); ?></td>
                                        <td class="table-actions">
                                            <a href="<?php echo BASE_URL; ?>/clients/edit/<?php echo $client['ClientID']; ?>" class="btn btn-sm btn-outline-primary" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <!-- Delete form (using POST for safety, though GET with confirm is also common) -->
                                            <form action="<?php echo BASE_URL; ?>/clients/destroy/<?php echo $client['ClientID']; ?>" method="POST" onsubmit="return confirm('Are you sure you want to delete this client? This action might be irreversible.');">
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
    <?php
        // Conceptual: Include a common footer
        // require_once VIEWS_PATH . 'layout/footer.php';
    ?>
</body>
</html>
