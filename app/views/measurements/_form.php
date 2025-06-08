<?php
// app/views/measurements/_form.php

// $measurement variable is expected when editing, null/empty when creating
// $client variable is expected to be passed for context (displaying client name, etc.)
// $errors variable might be set from the controller on validation failure
// $old_data variable might be set from the controller to repopulate form

$measurementName = $old_data['MeasurementName'] ?? $measurement['MeasurementName'] ?? 'Standard Measurements ' . date('Y-m-d');
$dateTaken = $old_data['DateTaken'] ?? $measurement['DateTaken'] ?? date('Y-m-d');

// Define the fields to handle
$fields = [
    'Bust' => 'Bust (cm/in)',
    'Waist' => 'Waist (cm/in)',
    'Hips' => 'Hips (cm/in)',
    'ShoulderWidth' => 'Shoulder Width (cm/in)',
    'SleeveLength' => 'Sleeve Length (cm/in)',
    'Inseam' => 'Inseam (cm/in)',
];

$measurementValues = [];
foreach (array_keys($fields) as $fieldKey) {
    $measurementValues[$fieldKey] = $old_data[$fieldKey] ?? $measurement[$fieldKey] ?? '';
}

$formErrors = App\Core\Session::getFlash('errors') ?? [];

// If errors exist but no old_data, try to get from measurement context if available for edit
if (empty($old_data) && !empty($formErrors) && isset($measurement)) {
    $measurementName = $measurement['MeasurementName'] ?? $measurementName;
    $dateTaken = $measurement['DateTaken'] ?? $dateTaken;
    foreach (array_keys($fields) as $fieldKey) {
        $measurementValues[$fieldKey] = $measurement[$fieldKey] ?? $measurementValues[$fieldKey];
    }
}

$clientId = $client['ClientID'] ?? $measurement['ClientID'] ?? 0; // Ensure we have client ID for form action
?>

<div class="mb-3">
    <label for="MeasurementName" class="form-label">Measurement Set Name/Label <span class="text-danger">*</span></label>
    <input type="text" class="form-control <?php echo isset($formErrors['MeasurementName']) ? 'is-invalid' : ''; ?>" id="MeasurementName" name="MeasurementName" value="<?php echo htmlspecialchars($measurementName); ?>" required>
    <?php if (isset($formErrors['MeasurementName'])): ?>
        <div class="invalid-feedback"><?php echo htmlspecialchars($formErrors['MeasurementName']); ?></div>
    <?php endif; ?>
</div>

<div class="mb-3">
    <label for="DateTaken" class="form-label">Date Taken <span class="text-danger">*</span></label>
    <input type="date" class="form-control <?php echo isset($formErrors['DateTaken']) ? 'is-invalid' : ''; ?>" id="DateTaken" name="DateTaken" value="<?php echo htmlspecialchars($dateTaken); ?>" required>
    <?php if (isset($formErrors['DateTaken'])): ?>
        <div class="invalid-feedback"><?php echo htmlspecialchars($formErrors['DateTaken']); ?></div>
    <?php endif; ?>
</div>

<hr>
<h5 class="mt-3 mb-3">Measurements:</h5>

<div class="row">
    <?php foreach ($fields as $fieldKey => $fieldLabel): ?>
    <div class="col-md-6 mb-3">
        <label for="<?php echo $fieldKey; ?>" class="form-label"><?php echo htmlspecialchars($fieldLabel); ?></label>
        <input type="number" step="0.01" class="form-control <?php echo isset($formErrors[$fieldKey]) ? 'is-invalid' : ''; ?>" id="<?php echo $fieldKey; ?>" name="<?php echo $fieldKey; ?>" value="<?php echo htmlspecialchars($measurementValues[$fieldKey]); ?>" placeholder="e.g., 92.5">
        <?php if (isset($formErrors[$fieldKey])): ?>
            <div class="invalid-feedback"><?php echo htmlspecialchars($formErrors[$fieldKey]); ?></div>
        <?php endif; ?>
    </div>
    <?php endforeach; ?>
</div>


<div class="d-flex justify-content-between mt-4">
    <a href="<?php echo BASE_URL; ?>/measurements/index/<?php echo $clientId; ?>" class="btn btn-secondary">Cancel</a>
    <button type="submit" class="btn btn-primary"><?php echo isset($measurement['MeasurementID']) ? 'Update Measurements' : 'Save Measurements'; ?></button>
</div>
