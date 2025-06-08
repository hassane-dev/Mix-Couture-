<?php
// app/views/orders/edit.php
use App\Core\Session;

if (class_exists('App\Core\Session')) {
    // Session::start(); // Should be started by index.php
} else {
    if (session_status() === PHP_SESSION_NONE) session_start();
}

if (!isset($order) || !$order || !isset($orderStatuses) || !is_array($orderStatuses)) {
    Session::setFlash('error', 'Order data or statuses not found or invalid.');
    echo "<div class='alert alert-danger container mt-5'>Error: Essential data is missing. Cannot render edit form. <a href='" . BASE_URL . "/orders'>Go back to orders list.</a></div>";
    return;
}

$old_data = Session::getFlash('old_data') ?? [];
$formErrors = Session::getFlash('errors') ?? [];

// Function to get value for form field, prioritizing old flashed data, then order data
function getOrderValue(string $key, array $orderData, array $oldData) {
    return htmlspecialchars($oldData[$key] ?? $orderData[$key] ?? '');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Order #<?php echo htmlspecialchars($order['OrderID']); ?> - Fashion Platform</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .container { max-width: 900px; }
        .card-header { background-color: #6f42c1; color: white; }
        .form-section-title { border-bottom: 1px solid #eee; padding-bottom: 0.5rem; margin-bottom: 1rem; font-size: 1.2rem; color: #444; }
        .read-only-info p { margin-bottom: 0.5rem; }
        .read-only-info strong { color: #555; }
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
        <div class="card shadow-sm">
            <div class="card-header">
                <h1 class="h3 mb-0">Edit Order #<?php echo htmlspecialchars($order['OrderID']); ?></h1>
            </div>
            <div class="card-body">
                <?php $globalErrorMessage = Session::getFlash('error'); ?>
                <?php if ($globalErrorMessage): ?>
                    <div class="alert alert-danger" role="alert"><?php echo htmlspecialchars($globalErrorMessage); ?></div>
                <?php endif; ?>

                <form action="<?php echo BASE_URL; ?>/orders/update/<?php echo $order['OrderID']; ?>" method="POST">

                    <div class="row">
                        <div class="col-md-6 mb-3 read-only-info">
                            <p class="form-section-title">Key Information (Read-only)</p>
                            <p><strong>Client:</strong> <?php echo htmlspecialchars($order['ClientFullName'] ?? 'N/A'); ?></p>
                            <p><strong>Model:</strong> <?php echo htmlspecialchars($order['ModelName'] ?? 'N/A'); ?></p>
                            <p><strong>Measurements:</strong> <?php echo htmlspecialchars($order['MeasurementName'] ?? 'N/A'); ?>
                                (<?php echo $order['MeasurementDateTaken'] ? date('M j, Y', strtotime($order['MeasurementDateTaken'])) : 'N/A'; ?>)
                            </p>
                            <p><strong>Order Date:</strong> <?php echo htmlspecialchars(date('M j, Y', strtotime($order['OrderDate']))); ?></p>
                        </div>

                        <div class="col-md-6 mb-3">
                            <p class="form-section-title">Editable Details</p>
                            <div class="mb-3">
                                <label for="OrderStatusID" class="form-label">Order Status <span class="text-danger">*</span></label>
                                <select class="form-select <?php echo isset($formErrors['OrderStatusID']) ? 'is-invalid' : ''; ?>" id="OrderStatusID" name="OrderStatusID" required>
                                    <?php foreach ($orderStatuses as $status): ?>
                                        <option value="<?php echo $status['OrderStatusID']; ?>" <?php echo (getOrderValue('OrderStatusID', $order, $old_data) == $status['OrderStatusID']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($status['StatusName']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (isset($formErrors['OrderStatusID'])): ?>
                                    <div class="invalid-feedback"><?php echo htmlspecialchars($formErrors['OrderStatusID']); ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="mb-3">
                                <label for="FinalPrice" class="form-label">Final Price ($) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" class="form-control <?php echo isset($formErrors['FinalPrice']) ? 'is-invalid' : ''; ?>" id="FinalPrice" name="FinalPrice" value="<?php echo getOrderValue('FinalPrice', $order, $old_data); ?>" required min="0">
                                <?php if (isset($formErrors['FinalPrice'])): ?>
                                    <div class="invalid-feedback"><?php echo htmlspecialchars($formErrors['FinalPrice']); ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="mb-3">
                                <label for="EstimatedCompletionDate" class="form-label">Estimated Completion Date</label>
                                <input type="date" class="form-control <?php echo isset($formErrors['EstimatedCompletionDate']) ? 'is-invalid' : ''; ?>" id="EstimatedCompletionDate" name="EstimatedCompletionDate" value="<?php echo getOrderValue('EstimatedCompletionDate', $order, $old_data); ?>">
                                <?php if (isset($formErrors['EstimatedCompletionDate'])): ?>
                                    <div class="invalid-feedback"><?php echo htmlspecialchars($formErrors['EstimatedCompletionDate']); ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="mb-3">
                        <label for="CustomizationNotes" class="form-label">Customization Notes</label>
                        <textarea class="form-control <?php echo isset($formErrors['CustomizationNotes']) ? 'is-invalid' : ''; ?>" id="CustomizationNotes" name="CustomizationNotes" rows="4"><?php echo getOrderValue('CustomizationNotes', $order, $old_data); ?></textarea>
                        <?php if (isset($formErrors['CustomizationNotes'])): ?>
                            <div class="invalid-feedback"><?php echo htmlspecialchars($formErrors['CustomizationNotes']); ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="<?php echo BASE_URL; ?>/orders/show/<?php echo $order['OrderID']; ?>" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> Cancel & View Details
                        </a>
                        <button type="submit" class="btn btn-primary" style="background-color: #6f42c1; border-color: #6f42c1;">
                            <i class="fas fa-save"></i> Update Order
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
