<?php
// app/views/models/_form.php

// $model variable is expected when editing, null/empty when creating
// $categories variable is expected to be passed for the category dropdown
// $errors variable might be set from the controller on validation failure
// $old_data variable might be set from the controller to repopulate form

$modelName = $old_data['Name'] ?? $model['Name'] ?? '';
$description = $old_data['Description'] ?? $model['Description'] ?? '';
$categoryId = $old_data['CategoryID'] ?? $model['CategoryID'] ?? null;
$basePrice = $old_data['BasePrice'] ?? $model['BasePrice'] ?? '0.00';
$currentPhotoURL = $model['PhotoURL'] ?? null; // Used for displaying current photo in edit mode

$formErrors = App\Core\Session::getFlash('errors') ?? [];

// If old_data is not set (e.g. first load of edit page or after failed POST but no old_data flashed)
// and errors are present, it implies we should still try to use model data for prefill if available.
if (empty($old_data) && !empty($formErrors)) {
    $modelName = $model['Name'] ?? $modelName;
    $description = $model['Description'] ?? $description;
    $categoryId = $model['CategoryID'] ?? $categoryId;
    $basePrice = $model['BasePrice'] ?? $basePrice;
}
$categories = $categories ?? [];

?>

<div class="mb-3">
    <label for="Name" class="form-label">Model Name <span class="text-danger">*</span></label>
    <input type="text" class="form-control <?php echo isset($formErrors['Name']) ? 'is-invalid' : ''; ?>" id="Name" name="Name" value="<?php echo htmlspecialchars($modelName); ?>" required>
    <?php if (isset($formErrors['Name'])): ?>
        <div class="invalid-feedback"><?php echo htmlspecialchars($formErrors['Name']); ?></div>
    <?php endif; ?>
</div>

<div class="mb-3">
    <label for="Description" class="form-label">Description</label>
    <textarea class="form-control <?php echo isset($formErrors['Description']) ? 'is-invalid' : ''; ?>" id="Description" name="Description" rows="3"><?php echo htmlspecialchars($description); ?></textarea>
    <?php if (isset($formErrors['Description'])): ?>
        <div class="invalid-feedback"><?php echo htmlspecialchars($formErrors['Description']); ?></div>
    <?php endif; ?>
</div>

<div class="mb-3">
    <label for="CategoryID" class="form-label">Category <span class="text-danger">*</span></label>
    <select class="form-select <?php echo isset($formErrors['CategoryID']) ? 'is-invalid' : ''; ?>" id="CategoryID" name="CategoryID" required>
        <option value="">Select Category</option>
        <?php foreach ($categories as $cat): ?>
            <option value="<?php echo htmlspecialchars($cat['CategoryID']); ?>" <?php echo ($categoryId == $cat['CategoryID']) ? 'selected' : ''; ?>>
                <?php echo htmlspecialchars($cat['CategoryName']); ?>
            </option>
        <?php endforeach; ?>
    </select>
    <?php if (isset($formErrors['CategoryID'])): ?>
        <div class="invalid-feedback"><?php echo htmlspecialchars($formErrors['CategoryID']); ?></div>
    <?php endif; ?>
</div>

<div class="mb-3">
    <label for="BasePrice" class="form-label">Base Price <span class="text-danger">*</span></label>
    <input type="number" step="0.01" class="form-control <?php echo isset($formErrors['BasePrice']) ? 'is-invalid' : ''; ?>" id="BasePrice" name="BasePrice" value="<?php echo htmlspecialchars($basePrice); ?>" required min="0">
    <?php if (isset($formErrors['BasePrice'])): ?>
        <div class="invalid-feedback"><?php echo htmlspecialchars($formErrors['BasePrice']); ?></div>
    <?php endif; ?>
</div>

<?php if (isset($model['ModelID']) && $currentPhotoURL): // Display current photo only in edit mode ?>
<div class="mb-3">
    <label class="form-label">Current Photo:</label><br>
    <img src="<?php echo BASE_URL . '/' . htmlspecialchars($currentPhotoURL); ?>" alt="<?php echo htmlspecialchars($modelName); ?>" style="max-width: 150px; max-height: 150px; object-fit: cover; border-radius: 5px;">
    <p class="form-text text-muted">Current Photo Path: <?php echo htmlspecialchars($currentPhotoURL);?></p>
</div>
<?php endif; ?>

<div class="mb-3">
    <label for="PhotoURL" class="form-label"><?php echo isset($model['ModelID']) ? 'Upload New Photo (Optional)' : 'Model Photo <span class="text-danger">*</span>'; ?></label>
    <input type="file" class="form-control <?php echo isset($formErrors['PhotoURL']) ? 'is-invalid' : ''; ?>" id="PhotoURL" name="PhotoURL" <?php echo !isset($model['ModelID']) ? 'required' : ''; ?>>
    <?php if (isset($formErrors['PhotoURL'])): ?>
        <div class="invalid-feedback"><?php echo htmlspecialchars($formErrors['PhotoURL']); ?></div>
    <?php endif; ?>
    <?php if(isset($model['ModelID'])): ?>
        <div class="form-text">Leave blank to keep the current photo.</div>
    <?php endif; ?>
</div>

<div class="d-flex justify-content-between mt-4">
    <a href="<?php echo BASE_URL; ?>/models" class="btn btn-secondary">Cancel</a>
    <button type="submit" class="btn btn-primary"><?php echo isset($model['ModelID']) ? 'Update Model' : 'Create Model'; ?></button>
</div>
