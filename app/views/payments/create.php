<?php
// app/views/payments/create.php
use App\Core\Session;

if (class_exists('App\Core\Session')) {
    // Session::start(); // Should be started by index.php
} else {
    if (session_status() === PHP_SESSION_NONE) session_start();
}

// $order, $suggestedAmount, $balanceDue are passed from PaymentController::create()
if (!isset($order) || !$order) {
    Session::setFlash('error', 'Order data not found for adding payment.');
    echo "<div class='alert alert-danger container mt-5'>Error: Order data is missing. <a href='" . BASE_URL . "/orders'>Go back to orders list.</a></div>";
    return;
}
$suggestedAmount = $suggestedAmount ?? 0.0;
$balanceDue = $balanceDue ?? 0.0;

$old_data = Session::getFlash('old_data') ?? $_POST;
$formErrors = Session::getFlash('errors') ?? [];

function getPaymentOldValue(string $key, array $oldData, $default = '') {
    return htmlspecialchars($oldData[$key] ?? $default);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Payment for Order #<?php echo htmlspecialchars($order['OrderID']); ?> - Fashion Platform</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .container { max-width: 700px; }
        .card-header { background-color: #20c997; color: white; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?php echo BASE_URL; ?>/auth/dashboard">Fashion Platform</a>
             <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link" href="<?php echo BASE_URL; ?>/auth/dashboard">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?php echo BASE_URL; ?>/orders">Orders</a></li>
                    <li class="nav-item"><a class="nav-link active" aria-current="page" href="<?php echo BASE_URL; ?>/payments/index/<?php echo $order['OrderID']; ?>">Payments for Order #<?php echo $order['OrderID']; ?></a></li>
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
                <h1 class="h3 mb-0">Add Payment for Order #<?php echo htmlspecialchars($order['OrderID']); ?></h1>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    Client: <strong><?php echo htmlspecialchars($order['ClientFullName'] ?? 'N/A'); ?></strong><br>
                    Order Total: $<?php echo htmlspecialchars(number_format((float)($order['FinalPrice'] ?? 0), 2)); ?><br>
                    Current Balance Due: <strong class="<?php echo $balanceDue > 0 ? 'text-danger' : 'text-success'; ?>">$<?php echo htmlspecialchars(number_format($balanceDue, 2)); ?></strong>
                </div>

                <?php $globalErrorMessage = Session::getFlash('error'); ?>
                <?php if ($globalErrorMessage): ?>
                    <div class="alert alert-danger" role="alert"><?php echo htmlspecialchars($globalErrorMessage); ?></div>
                <?php endif; ?>

                <form action="<?php echo BASE_URL; ?>/payments/store" method="POST">
                    <input type="hidden" name="OrderID" value="<?php echo htmlspecialchars($order['OrderID']); ?>">

                    <div class="mb-3">
                        <label for="PaymentDate" class="form-label">Payment Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control <?php echo isset($formErrors['PaymentDate']) ? 'is-invalid' : ''; ?>" id="PaymentDate" name="PaymentDate" value="<?php echo getPaymentOldValue('PaymentDate', $old_data, date('Y-m-d')); ?>" required>
                        <?php if (isset($formErrors['PaymentDate'])): ?>
                            <div class="invalid-feedback"><?php echo htmlspecialchars($formErrors['PaymentDate']); ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="AmountPaid" class="form-label">Amount Paid ($) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" class="form-control <?php echo isset($formErrors['AmountPaid']) ? 'is-invalid' : ''; ?>" id="AmountPaid" name="AmountPaid" value="<?php echo getPaymentOldValue('AmountPaid', $old_data, number_format($suggestedAmount, 2, '.', '')); ?>" required min="0.01" <?php if($balanceDue <=0) echo "max='0'"; else echo "max='" . number_format($balanceDue, 2, '.', '') . "'";?>>
                        <?php if (isset($formErrors['AmountPaid'])): ?>
                            <div class="invalid-feedback"><?php echo htmlspecialchars($formErrors['AmountPaid']); ?></div>
                        <?php endif; ?>
                         <div class="form-text">
                            <?php if ($balanceDue <= 0): ?>
                                This order is fully paid.
                            <?php else: ?>
                                Max recommended: $<?php echo number_format($balanceDue, 2); ?> (current balance due)
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="PaymentMethodNotes" class="form-label">Payment Method / Notes <span class="text-danger">*</span></label>
                        <textarea class="form-control <?php echo isset($formErrors['PaymentMethodNotes']) ? 'is-invalid' : ''; ?>" id="PaymentMethodNotes" name="PaymentMethodNotes" rows="3" required placeholder="e.g., Cash, Credit Card ending 1234, Bank Transfer Ref XYZ"><?php echo getPaymentOldValue('PaymentMethodNotes', $old_data); ?></textarea>
                        <?php if (isset($formErrors['PaymentMethodNotes'])): ?>
                            <div class="invalid-feedback"><?php echo htmlspecialchars($formErrors['PaymentMethodNotes']); ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="<?php echo BASE_URL; ?>/payments/index/<?php echo $order['OrderID']; ?>" class="btn btn-outline-secondary">
                           <i class="fas fa-arrow-left"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-success" <?php if($balanceDue <=0 && $suggestedAmount <=0) echo "disabled"; ?>>
                            <i class="fas fa-save"></i> Record Payment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
