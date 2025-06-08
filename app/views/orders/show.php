<?php
// app/views/orders/show.php
use App\Core\Session;

if (class_exists('App\Core\Session')) {
    // Session::start(); // Should be started by index.php
} else {
    if (session_status() === PHP_SESSION_NONE) session_start();
}

if (!isset($order) || !$order) {
    Session::setFlash('error', 'Order data not found or invalid.');
    echo "<div class='alert alert-danger container mt-5'>Error: Order data is missing. <a href='" . BASE_URL . "/orders'>Go back to orders list.</a></div>";
    // Include full HTML error page or stop execution if you have a layout system
    // In a real app, the controller would likely redirect before reaching here if order is not found.
    return;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details #<?php echo htmlspecialchars($order['OrderID']); ?> - Fashion Platform</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .card-header { background-color: #6f42c1; color: white; }
        .model-photo-detail { max-width: 200px; border-radius: 5px; margin-bottom: 15px; }
        .details-section { margin-bottom: 2rem; }
        .details-section h5 { border-bottom: 2px solid #eee; padding-bottom: 0.5rem; margin-bottom: 1rem; color: #6f42c1; }
        dt { font-weight: bold; }
        dd { margin-left: 0; margin-bottom: 0.5rem; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?php echo BASE_URL; ?>/auth/dashboard">Fashion Platform</a>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link" href="<?php echo BASE_URL; ?>/auth/dashboard">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?php echo BASE_URL; ?>/clients">Clients</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?php echo BASE_URL; ?>/models">Models</a></li>
                    <li class="nav-item"><a class="nav-link active" aria-current="page" href="<?php echo BASE_URL; ?>/orders">Orders</a></li>
                </ul>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="btn btn-danger" href="<?php echo BASE_URL; ?>/auth/logout">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h2">Order Details: #<?php echo htmlspecialchars($order['OrderID']); ?></h1>
            <div>
                <a href="<?php echo BASE_URL; ?>/orders" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Orders List
                </a>
                <a href="<?php echo BASE_URL; ?>/orders/edit/<?php echo $order['OrderID']; ?>" class="btn btn-primary ms-2" style="background-color: #6f42c1; border-color: #6f42c1;">
                    <i class="fas fa-edit"></i> Edit Order
                </a>
            </div>
        </div>

        <?php $successMessage = Session::getFlash('success'); ?>
        <?php if ($successMessage): ?>
            <div class="alert alert-success" role="alert"><?php echo htmlspecialchars($successMessage); ?></div>
        <?php endif; ?>
        <?php $errorMessage = Session::getFlash('error'); ?>
        <?php if ($errorMessage): ?>
            <div class="alert alert-danger" role="alert"><?php echo htmlspecialchars($errorMessage); ?></div>
        <?php endif; ?>

        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Order #<?php echo htmlspecialchars($order['OrderID']); ?></h5>
                <span class="badge bg-light text-dark fs-6"><?php echo htmlspecialchars($order['OrderStatusName'] ?? 'N/A'); ?></span>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Client Details Section -->
                    <div class="col-md-6 details-section">
                        <h5>Client Information</h5>
                        <dl class="row">
                            <dt class="col-sm-4">Client Name:</dt>
                            <dd class="col-sm-8"><?php echo htmlspecialchars($order['ClientFullName'] ?? 'N/A'); ?></dd>
                            <dt class="col-sm-4">Email:</dt>
                            <dd class="col-sm-8"><?php echo htmlspecialchars($order['ClientEmail'] ?? 'N/A'); ?></dd>
                            <dt class="col-sm-4">Phone:</dt>
                            <dd class="col-sm-8"><?php echo htmlspecialchars($order['ClientPhoneNumber'] ?? 'N/A'); ?></dd>
                        </dl>
                    </div>

                    <!-- Model Details Section -->
                    <div class="col-md-6 details-section">
                        <h5>Clothing Model Information</h5>
                        <?php if (!empty($order['ModelPhotoURL'])): ?>
                            <img src="<?php echo BASE_URL . '/' . htmlspecialchars($order['ModelPhotoURL']); ?>" alt="<?php echo htmlspecialchars($order['ModelName']); ?>" class="img-fluid model-photo-detail mb-2">
                        <?php endif; ?>
                        <dl class="row">
                            <dt class="col-sm-4">Model Name:</dt>
                            <dd class="col-sm-8"><?php echo htmlspecialchars($order['ModelName'] ?? 'N/A'); ?></dd>
                            <dt class="col-sm-4">Category:</dt>
                            <dd class="col-sm-8"><?php echo htmlspecialchars($order['ModelCategoryName'] ?? 'N/A'); ?></dd>
                            <dt class="col-sm-4">Base Price:</dt>
                            <dd class="col-sm-8">$<?php echo htmlspecialchars(number_format((float)($order['ModelBasePrice'] ?? 0), 2)); ?></dd>
                            <dt class="col-sm-4">Description:</dt>
                            <dd class="col-sm-8"><?php echo nl2br(htmlspecialchars($order['ModelDescription'] ?? 'N/A')); ?></dd>
                        </dl>
                    </div>
                </div>

                <hr>

                <div class="row">
                    <!-- Order Financials and Dates -->
                    <div class="col-md-6 details-section">
                        <h5>Order Financials & Dates</h5>
                        <dl class="row">
                            <dt class="col-sm-5">Order Date:</dt>
                            <dd class="col-sm-7"><?php echo htmlspecialchars(date('F j, Y', strtotime($order['OrderDate']))); ?></dd>
                            <dt class="col-sm-5">Est. Completion:</dt>
                            <dd class="col-sm-7"><?php echo $order['EstimatedCompletionDate'] ? htmlspecialchars(date('F j, Y', strtotime($order['EstimatedCompletionDate']))) : 'N/A'; ?></dd>
                            <dt class="col-sm-5">Final Price:</dt>
                            <dd class="col-sm-7 fw-bold">$<?php echo htmlspecialchars(number_format((float)($order['FinalPrice'] ?? 0), 2)); ?></dd>
                            <dt class="col-sm-5">Order Status:</dt>
                            <dd class="col-sm-7"><span class="badge bg-primary"><?php echo htmlspecialchars($order['OrderStatusName'] ?? 'N/A'); ?></span></dd>
                            <dt class="col-sm-5">Created At:</dt>
                            <dd class="col-sm-7"><?php echo htmlspecialchars(date('F j, Y H:i', strtotime($order['OrderCreatedAt']))); ?></dd>
                            <dt class="col-sm-5">Last Updated:</dt>
                            <dd class="col-sm-7"><?php echo htmlspecialchars(date('F j, Y H:i', strtotime($order['OrderUpdatedAt']))); ?></dd>
                        </dl>
                    </div>

                    <!-- Measurements Section -->
                    <div class="col-md-6 details-section">
                        <h5>Measurements Used</h5>
                        <?php if ($order['MeasurementID']): ?>
                            <dl class="row">
                                <dt class="col-sm-5">Set Name:</dt>
                                <dd class="col-sm-7"><?php echo htmlspecialchars($order['MeasurementName'] ?? 'N/A'); ?></dd>
                                <dt class="col-sm-5">Date Taken:</dt>
                                <dd class="col-sm-7"><?php echo $order['MeasurementDateTaken'] ? htmlspecialchars(date('F j, Y', strtotime($order['MeasurementDateTaken']))) : 'N/A'; ?></dd>
                                <dt class="col-sm-5">Bust:</dt>
                                <dd class="col-sm-7"><?php echo htmlspecialchars($order['Bust'] ?? '-'); ?> cm/in</dd>
                                <dt class="col-sm-5">Waist:</dt>
                                <dd class="col-sm-7"><?php echo htmlspecialchars($order['Waist'] ?? '-'); ?> cm/in</dd>
                                <dt class="col-sm-5">Hips:</dt>
                                <dd class="col-sm-7"><?php echo htmlspecialchars($order['Hips'] ?? '-'); ?> cm/in</dd>
                                <dt class="col-sm-5">Shoulder Width:</dt>
                                <dd class="col-sm-7"><?php echo htmlspecialchars($order['ShoulderWidth'] ?? '-'); ?> cm/in</dd>
                                <dt class="col-sm-5">Sleeve Length:</dt>
                                <dd class="col-sm-7"><?php echo htmlspecialchars($order['SleeveLength'] ?? '-'); ?> cm/in</dd>
                                <dt class="col-sm-5">Inseam:</dt>
                                <dd class="col-sm-7"><?php echo htmlspecialchars($order['Inseam'] ?? '-'); ?> cm/in</dd>
                                <!-- Add other measurement fields as they are in the JOIN -->
                            </dl>
                        <?php else: ?>
                            <p class="text-muted">No specific measurement set linked to this order, or measurements were not recorded.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <hr>

                <div class="details-section">
                    <h5>Customization Notes</h5>
                    <p><?php echo nl2br(htmlspecialchars($order['CustomizationNotes'] ?? 'No specific customization notes provided.')); ?></p>
                </div>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
