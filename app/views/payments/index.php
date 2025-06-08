<?php
// app/views/payments/index.php
use App\Core\Session;

if (class_exists('App\Core\Session')) {
    // Session::start(); // Should be started by index.php
} else {
    if (session_status() === PHP_SESSION_NONE) session_start();
}

// $order, $payments, $totalPaid, $balanceDue are passed from PaymentController::index()
if (!isset($order) || !$order) {
    Session::setFlash('error', 'Order data not found for payment list.');
    echo "<div class='alert alert-danger container mt-5'>Error: Order data is missing. <a href='" . BASE_URL . "/orders'>Go back to orders list.</a></div>";
    return;
}
$payments = $payments ?? [];
$totalPaid = $totalPaid ?? 0.0;
$balanceDue = $balanceDue ?? (float)($order['FinalPrice'] ?? 0.0);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payments for Order #<?php echo htmlspecialchars($order['OrderID']); ?> - Fashion Platform</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .table-actions form { display: inline-block; margin-left: 5px; }
        .card-header { background-color: #20c997; color: white; } /* Teal theme for payments */
        .summary-card { background-color: #e9ecef; }
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
                    <li class="nav-item"><a class="nav-link" href="<?php echo BASE_URL; ?>/orders">Orders</a></li>
                    <li class="nav-item"><a class="nav-link active" aria-current="page" href="<?php echo BASE_URL; ?>/payments/index/<?php echo $order['OrderID']; ?>">Payments</a></li>
                </ul>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="btn btn-danger" href="<?php echo BASE_URL; ?>/auth/logout">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h1 class="h2">Payments for Order #<?php echo htmlspecialchars($order['OrderID']); ?></h1>
                <p class="lead">Client: <?php echo htmlspecialchars($order['ClientFullName'] ?? 'N/A'); ?></p>
                <a href="<?php echo BASE_URL; ?>/orders/show/<?php echo $order['OrderID']; ?>" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Order Details
                </a>
            </div>
            <a href="<?php echo BASE_URL; ?>/payments/create/<?php echo $order['OrderID']; ?>" class="btn btn-success">
                <i class="fas fa-plus-circle"></i> Add Payment
            </a>
        </div>

        <?php $successMessage = Session::getFlash('success'); ?>
        <?php if ($successMessage): ?>
            <div class="alert alert-success" role="alert"><?php echo htmlspecialchars($successMessage); ?></div>
        <?php endif; ?>
        <?php $errorMessage = Session::getFlash('error'); ?>
        <?php if ($errorMessage): ?>
            <div class="alert alert-danger" role="alert"><?php echo htmlspecialchars($errorMessage); ?></div>
        <?php endif; ?>

        <div class="card shadow-sm mb-4 summary-card">
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-4">
                        <h4>Order Total: <span class="text-primary">$<?php echo htmlspecialchars(number_format((float)($order['FinalPrice'] ?? 0), 2)); ?></span></h4>
                    </div>
                    <div class="col-md-4">
                        <h4>Total Paid: <span class="text-success">$<?php echo htmlspecialchars(number_format($totalPaid, 2)); ?></span></h4>
                    </div>
                    <div class="col-md-4">
                        <h4>Balance Due: <span class="<?php echo $balanceDue > 0 ? 'text-danger' : 'text-success'; ?> fw-bold">$<?php echo htmlspecialchars(number_format($balanceDue, 2)); ?></span></h4>
                    </div>
                </div>
            </div>
        </div>


        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="mb-0">Payment History</h5>
            </div>
            <div class="card-body">
                <?php if (empty($payments)): ?>
                    <p class="text-center">No payments recorded for this order yet.
                        <a href="<?php echo BASE_URL; ?>/payments/create/<?php echo $order['OrderID']; ?>">Add a payment now!</a>
                    </p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle">
                            <thead class="table-light" style="border-bottom: 2px solid #20c997;">
                                <tr>
                                    <th>Payment ID</th>
                                    <th>Payment Date</th>
                                    <th>Amount Paid</th>
                                    <th>Method/Notes</th>
                                    <th>Recorded At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($payments as $payment): ?>
                                    <tr>
                                        <td>#<?php echo htmlspecialchars($payment['PaymentID']); ?></td>
                                        <td><?php echo htmlspecialchars(date('M j, Y', strtotime($payment['PaymentDate']))); ?></td>
                                        <td>$<?php echo htmlspecialchars(number_format((float)($payment['AmountPaid'] ?? 0), 2)); ?></td>
                                        <td><?php echo nl2br(htmlspecialchars($payment['PaymentMethodNotes'] ?? 'N/A')); ?></td>
                                        <td><?php echo htmlspecialchars(date('M j, Y H:i', strtotime($payment['CreatedAt']))); ?></td>
                                        <td class="table-actions">
                                            <a href="<?php echo BASE_URL; ?>/payments/edit/<?php echo $payment['PaymentID']; ?>" class="btn btn-sm btn-outline-primary" title="Edit Payment">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="<?php echo BASE_URL; ?>/payments/destroy/<?php echo $payment['PaymentID']; ?>" method="POST" onsubmit="return confirm('Are you sure you want to delete this payment record? This action cannot be undone easily.');">
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Payment">
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
