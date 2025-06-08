<?php
// app/views/orders/create.php
use App\Core\Session;

if (class_exists('App\Core\Session')) {
    // Session::start(); // Should be started by index.php
} else {
    if (session_status() === PHP_SESSION_NONE) session_start();
}

// $clients, $clothingModels, $orderStatuses are passed from OrderController::create()
$clients = $clients ?? [];
$clothingModels = $clothingModels ?? [];
$orderStatuses = $orderStatuses ?? [];

$old_data = Session::getFlash('old_data') ?? $_POST; // Use POST if not flashed (e.g. JS disabled client validation)
$formErrors = Session::getFlash('errors') ?? [];

function getOldValue(string $key, array $oldData, $default = '') {
    return htmlspecialchars($oldData[$key] ?? $default);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Order - Fashion Platform</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
     <style>
        .container { max-width: 900px; }
        .card-header { background-color: #6f42c1; color: white; }
        .form-section-title { border-bottom: 1px solid #eee; padding-bottom: 0.5rem; margin-bottom: 1rem; font-size: 1.2rem; color: #444; }
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
                <h1 class="h3 mb-0">Create New Order</h1>
            </div>
            <div class="card-body">
                <?php $globalErrorMessage = Session::getFlash('error'); ?>
                <?php if ($globalErrorMessage): ?>
                    <div class="alert alert-danger" role="alert"><?php echo htmlspecialchars($globalErrorMessage); ?></div>
                <?php endif; ?>

                <form action="<?php echo BASE_URL; ?>/orders/store" method="POST" id="createOrderForm">
                    <p class="form-section-title">Core Details</p>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="ClientID" class="form-label">Client <span class="text-danger">*</span></label>
                            <select class="form-select <?php echo isset($formErrors['ClientID']) ? 'is-invalid' : ''; ?>" id="ClientID" name="ClientID" required>
                                <option value="">Select Client</option>
                                <?php foreach ($clients as $client): ?>
                                    <option value="<?php echo $client['ClientID']; ?>" <?php echo (getOldValue('ClientID', $old_data) == $client['ClientID']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($client['FullName']); ?> (ID: <?php echo $client['ClientID']; ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (isset($formErrors['ClientID'])): ?>
                                <div class="invalid-feedback"><?php echo htmlspecialchars($formErrors['ClientID']); ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="ModelID" class="form-label">Clothing Model <span class="text-danger">*</span></label>
                            <select class="form-select <?php echo isset($formErrors['ModelID']) ? 'is-invalid' : ''; ?>" id="ModelID" name="ModelID" required>
                                <option value="">Select Model</option>
                                <?php foreach ($clothingModels as $model): ?>
                                    <option value="<?php echo $model['ModelID']; ?>" <?php echo (getOldValue('ModelID', $old_data) == $model['ModelID']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($model['Name']); ?> ($<?php echo number_format($model['BasePrice'], 2); ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (isset($formErrors['ModelID'])): ?>
                                <div class="invalid-feedback"><?php echo htmlspecialchars($formErrors['ModelID']); ?></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="MeasurementID" class="form-label">Client's Measurement Set (Optional)</label>
                        <select class="form-select <?php echo isset($formErrors['MeasurementID']) ? 'is-invalid' : ''; ?>" id="MeasurementID" name="MeasurementID">
                            <option value="">Select Measurement Set (if applicable)</option>
                            <!-- Options will be populated by JavaScript -->
                        </select>
                        <div class="form-text">Select a client first to load their measurement sets.</div>
                        <?php if (isset($formErrors['MeasurementID'])): ?>
                            <div class="invalid-feedback"><?php echo htmlspecialchars($formErrors['MeasurementID']); ?></div>
                        <?php endif; ?>
                    </div>

                    <hr class="my-4">
                    <p class="form-section-title">Order Specifics</p>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="OrderDate" class="form-label">Order Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control <?php echo isset($formErrors['OrderDate']) ? 'is-invalid' : ''; ?>" id="OrderDate" name="OrderDate" value="<?php echo getOldValue('OrderDate', $old_data, date('Y-m-d')); ?>" required>
                            <?php if (isset($formErrors['OrderDate'])): ?>
                                <div class="invalid-feedback"><?php echo htmlspecialchars($formErrors['OrderDate']); ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="EstimatedCompletionDate" class="form-label">Estimated Completion Date</label>
                            <input type="date" class="form-control <?php echo isset($formErrors['EstimatedCompletionDate']) ? 'is-invalid' : ''; ?>" id="EstimatedCompletionDate" name="EstimatedCompletionDate" value="<?php echo getOldValue('EstimatedCompletionDate', $old_data); ?>">
                             <?php if (isset($formErrors['EstimatedCompletionDate'])): ?>
                                <div class="invalid-feedback"><?php echo htmlspecialchars($formErrors['EstimatedCompletionDate']); ?></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="FinalPrice" class="form-label">Final Price ($) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" class="form-control <?php echo isset($formErrors['FinalPrice']) ? 'is-invalid' : ''; ?>" id="FinalPrice" name="FinalPrice" value="<?php echo getOldValue('FinalPrice', $old_data, '0.00'); ?>" required min="0">
                            <?php if (isset($formErrors['FinalPrice'])): ?>
                                <div class="invalid-feedback"><?php echo htmlspecialchars($formErrors['FinalPrice']); ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="OrderStatusID" class="form-label">Order Status <span class="text-danger">*</span></label>
                            <select class="form-select <?php echo isset($formErrors['OrderStatusID']) ? 'is-invalid' : ''; ?>" id="OrderStatusID" name="OrderStatusID" required>
                                <option value="">Select Status</option>
                                <?php foreach ($orderStatuses as $status): ?>
                                    <option value="<?php echo $status['OrderStatusID']; ?>" <?php echo (getOldValue('OrderStatusID', $old_data) == $status['OrderStatusID']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($status['StatusName']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                             <?php if (isset($formErrors['OrderStatusID'])): ?>
                                <div class="invalid-feedback"><?php echo htmlspecialchars($formErrors['OrderStatusID']); ?></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="CustomizationNotes" class="form-label">Customization Notes</label>
                        <textarea class="form-control <?php echo isset($formErrors['CustomizationNotes']) ? 'is-invalid' : ''; ?>" id="CustomizationNotes" name="CustomizationNotes" rows="4"><?php echo getOldValue('CustomizationNotes', $old_data); ?></textarea>
                        <?php if (isset($formErrors['CustomizationNotes'])): ?>
                            <div class="invalid-feedback"><?php echo htmlspecialchars($formErrors['CustomizationNotes']); ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="<?php echo BASE_URL; ?>/orders" class="btn btn-outline-secondary">
                           <i class="fas fa-arrow-left"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary" style="background-color: #6f42c1; border-color: #6f42c1;">
                            <i class="fas fa-save"></i> Create Order
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const clientSelect = document.getElementById('ClientID');
            const measurementSelect = document.getElementById('MeasurementID');
            const baseApiUrl = '<?php echo BASE_URL; ?>'; // Get base URL from PHP

            // Store the MeasurementID from old flashed data if available
            const oldMeasurementID = '<?php echo getOldValue("MeasurementID", $old_data); ?>';

            function fetchMeasurements(clientId, selectedMeasurementId = null) {
                if (!clientId) {
                    measurementSelect.innerHTML = '<option value="">Select Measurement Set (if applicable)</option>';
                    measurementSelect.disabled = true;
                    return;
                }

                measurementSelect.disabled = true;
                measurementSelect.innerHTML = '<option value="">Loading measurements...</option>';

                fetch(`${baseApiUrl}/orders/getmeasurements/${clientId}`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        measurementSelect.innerHTML = '<option value="">Select Measurement Set (if applicable)</option>';
                        if (data.error) {
                            console.error('Error fetching measurements:', data.error);
                            measurementSelect.innerHTML = '<option value="">Could not load measurements</option>';
                        } else if (data.length === 0) {
                            measurementSelect.innerHTML = '<option value="">No measurement sets found for this client</option>';
                        } else {
                            data.forEach(measurement => {
                                const option = document.createElement('option');
                                option.value = measurement.MeasurementID;
                                option.textContent = `${measurement.MeasurementName} (Taken: ${new Date(measurement.DateTaken).toLocaleDateString()})`;
                                if (selectedMeasurementId && measurement.MeasurementID == selectedMeasurementId) {
                                    option.selected = true;
                                }
                                measurementSelect.appendChild(option);
                            });
                        }
                        measurementSelect.disabled = false;
                    })
                    .catch(error => {
                        console.error('Fetch error:', error);
                        measurementSelect.innerHTML = '<option value="">Error loading measurements</option>';
                        measurementSelect.disabled = false;
                    });
            }

            clientSelect.addEventListener('change', function () {
                fetchMeasurements(this.value);
            });

            // If there's an old ClientID (e.g., due to form validation error), fetch its measurements
            // and try to re-select the old MeasurementID.
            if (clientSelect.value) {
                fetchMeasurements(clientSelect.value, oldMeasurementID);
            } else {
                 measurementSelect.disabled = true;
            }
        });
    </script>
</body>
</html>
