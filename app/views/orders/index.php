<?php
// app/views/orders/index.php
use App\Core\Session;

if (class_exists('App\Core\Session')) {
    // Session::start(); // Should be started by index.php
} else {
    if (session_status() === PHP_SESSION_NONE) session_start();
}

$orders = $orders ?? [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders - Fashion Platform</title>
    <link href="<?php echo BASE_URL; ?>/assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/all.min.css">
    <style>
        .table-actions form { display: inline-block; margin-left: 5px; }
        .card-header { background-color: #6f42c1; color: white; } /* Purple theme for orders */
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
                    <li class="nav-item"><a class="nav-link" href="<?php echo BASE_URL; ?>/models">Models</a></li>
                    <li class="nav-item"><a class="nav-link active" aria-current="page" href="<?php echo BASE_URL; ?>/orders">Orders</a></li>
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
            <h1 class="h2">Manage Orders</h1>
            <a href="<?php echo BASE_URL; ?>/orders/create" class="btn btn-primary" style="background-color: #6f42c1; border-color: #6f42c1;">
                <i class="fas fa-plus"></i> Create New Order
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
                <h5 class="mb-0">All Orders</h5>
            </div>
            <div class="card-body">
                <?php if (empty($orders)): ?>
                    <p class="text-center">No orders found. <a href="<?php echo BASE_URL; ?>/orders/create">Create one now!</a></p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle">
                            <thead class="table-light" style="border-bottom: 2px solid #6f42c1;">
                                <tr>
                                    <th>Order ID</th>
                                    <th>Client Name</th>
                                    <th>Model Name</th>
                                    <th>Order Date</th>
                                    <th>Status</th>
                                    <th>Final Price</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orders as $order): ?>
                                    <tr>
                                        <td>#<?php echo htmlspecialchars($order['OrderID']); ?></td>
                                        <td><?php echo htmlspecialchars($order['ClientName'] ?? 'N/A'); ?></td>
                                        <td><?php echo htmlspecialchars($order['ModelName'] ?? 'N/A'); ?></td>
                                        <td><?php echo htmlspecialchars(date('M j, Y', strtotime($order['OrderDate']))); ?></td>
                                        <td><span class="badge bg-info text-dark"><?php echo htmlspecialchars($order['OrderStatus'] ?? 'N/A'); ?></span></td>
                                        <td>$<?php echo htmlspecialchars(number_format((float)($order['FinalPrice'] ?? 0), 2)); ?></td>
                                        <td><?php echo htmlspecialchars(date('M j, Y H:i', strtotime($order['CreatedAt']))); ?></td>
                                        <td class="table-actions">
                                            <a href="<?php echo BASE_URL; ?>/orders/show/<?php echo $order['OrderID']; ?>" class="btn btn-sm btn-outline-info" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="<?php echo BASE_URL; ?>/orders/edit/<?php echo $order['OrderID']; ?>" class="btn btn-sm btn-outline-primary" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="<?php echo BASE_URL; ?>/orders/destroy/<?php echo $order['OrderID']; ?>" method="POST" onsubmit="return confirm('Are you sure you want to mark this order as deleted?');">
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

    <script src="<?php echo BASE_URL; ?>/assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
