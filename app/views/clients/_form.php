<?php
// app/views/clients/_form.php

// $client variable is expected when editing, null/empty when creating
// $errors variable might be set from the controller on validation failure
// $old_data variable might be set from the controller to repopulate form on validation failure

$fullName = $old_data['FullName'] ?? $client['FullName'] ?? '';
$email = $old_data['Email'] ?? $client['Email'] ?? '';
$phoneNumber = $old_data['PhoneNumber'] ?? $client['PhoneNumber'] ?? '';
$address = $old_data['Address'] ?? $client['Address'] ?? '';

$formErrors = App\Core\Session::getFlash('errors') ?? [];
if (empty($old_data) && !empty($formErrors)) { // If errors exist but no old_data, try to get from client context if available
    $fullName = $client['FullName'] ?? $fullName;
    $email = $client['Email'] ?? $email;
    $phoneNumber = $client['PhoneNumber'] ?? $phoneNumber;
    $address = $client['Address'] ?? $address;
}


?>
<div class="mb-3">
    <label for="FullName" class="form-label">Full Name <span class="text-danger">*</span></label>
    <input type="text" class="form-control <?php echo isset($formErrors['FullName']) ? 'is-invalid' : ''; ?>" id="FullName" name="FullName" value="<?php echo htmlspecialchars($fullName); ?>" required>
    <?php if (isset($formErrors['FullName'])): ?>
        <div class="invalid-feedback">
            <?php echo htmlspecialchars($formErrors['FullName']); ?>
        </div>
    <?php endif; ?>
</div>

<div class="mb-3">
    <label for="Email" class="form-label">Email address <span class="text-danger">*</span></label>
    <input type="email" class="form-control <?php echo isset($formErrors['Email']) ? 'is-invalid' : ''; ?>" id="Email" name="Email" value="<?php echo htmlspecialchars($email); ?>" required>
    <?php if (isset($formErrors['Email'])): ?>
        <div class="invalid-feedback">
            <?php echo htmlspecialchars($formErrors['Email']); ?>
        </div>
    <?php endif; ?>
</div>

<div class="mb-3">
    <label for="PhoneNumber" class="form-label">Phone Number <span class="text-danger">*</span></label>
    <input type="tel" class="form-control <?php echo isset($formErrors['PhoneNumber']) ? 'is-invalid' : ''; ?>" id="PhoneNumber" name="PhoneNumber" value="<?php echo htmlspecialchars($phoneNumber); ?>" required>
     <?php if (isset($formErrors['PhoneNumber'])): ?>
        <div class="invalid-feedback">
            <?php echo htmlspecialchars($formErrors['PhoneNumber']); ?>
        </div>
    <?php endif; ?>
</div>

<div class="mb-3">
    <label for="Address" class="form-label">Address</label>
    <textarea class="form-control <?php echo isset($formErrors['Address']) ? 'is-invalid' : ''; ?>" id="Address" name="Address" rows="3"><?php echo htmlspecialchars($address); ?></textarea>
    <?php if (isset($formErrors['Address'])): ?>
        <div class="invalid-feedback">
            <?php echo htmlspecialchars($formErrors['Address']); ?>
        </div>
    <?php endif; ?>
</div>

<div class="d-flex justify-content-between">
    <a href="<?php echo BASE_URL; ?>/clients" class="btn btn-secondary">Cancel</a>
    <button type="submit" class="btn btn-primary"><?php echo isset($client['ClientID']) ? 'Update Client' : 'Create Client'; ?></button>
</div>
