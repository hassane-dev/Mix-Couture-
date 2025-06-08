<?php
// app/controllers/ModelController.php

namespace App\Controllers;

use App\Models\ClothingModel;
use App\Core\Session; // Assuming Session class from previous steps

class ModelController {
    private ClothingModel $model;

    // Define the upload directory relative to the public folder or a writable path
    // APP_ROOT points to project root (parent of 'app' and 'public')
    const UPLOAD_DIR = APP_ROOT . '/public/uploads/models/';


    public function __construct() {
        // Ensure user is logged in
        if (!Session::isLoggedIn()) {
            Session::setFlash('error', 'Please log in to manage clothing models.');
            header('Location: ' . BASE_URL . '/auth/login');
            exit;
        }
        $this->model = new ClothingModel();

        // Ensure upload directory exists and is writable
        if (!is_dir(self::UPLOAD_DIR)) {
            if (!mkdir(self::UPLOAD_DIR, 0775, true)) {
                // Handle error: directory cannot be created
                // This is a critical error, might want to log and die or throw exception
                error_log("Failed to create upload directory: " . self::UPLOAD_DIR);
                // For simplicity, we'll let it fail later if not writable, but production code needs robust check.
            }
        }
        if (!is_writable(self::UPLOAD_DIR)){
            error_log("Upload directory is not writable: " . self::UPLOAD_DIR);
            // Potentially set a flash message or throw an exception
        }
    }

    /**
     * Display a listing of the models.
     */
    public function index(): void {
        $models = $this->model->getAll();
        $this->loadView('models/index', ['models' => $models]);
    }

    /**
     * Show the form for creating a new model.
     */
    public function create(): void {
        $categories = $this->model->getCategories();
        $this->loadView('models/create', ['categories' => $categories]);
    }

    /**
     * Store a newly created model in storage.
     */
    public function store(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'Name' => trim($_POST['Name'] ?? ''),
                'Description' => trim($_POST['Description'] ?? ''),
                'CategoryID' => filter_input(INPUT_POST, 'CategoryID', FILTER_VALIDATE_INT),
                'BasePrice' => filter_input(INPUT_POST, 'BasePrice', FILTER_VALIDATE_FLOAT),
                'PhotoURL' => '' // Initialize, will be set by file upload
            ];

            $errors = $this->validateModelData($data, true); // true for create (photo required)
            $fileUploadResult = $this->handleFileUpload($_FILES['PhotoURL'] ?? null);

            if ($fileUploadResult['error']) {
                $errors['PhotoURL'] = $fileUploadResult['error'];
            } else {
                $data['PhotoURL'] = $fileUploadResult['filePath'];
            }

            if (!empty($errors)) {
                // If upload succeeded but other validation failed, delete the uploaded file
                if (!$fileUploadResult['error'] && !empty($fileUploadResult['filePath'])) {
                    $this->deletePhotoFile($fileUploadResult['filePath']);
                }
                Session::setFlash('errors', $errors);
                Session::setFlash('old_data', $_POST); // Send back raw POST data
                $categories = $this->model->getCategories();
                $this->loadView('models/create', ['categories' => $categories, 'errors' => $errors, 'old_data' => $_POST]);
                return;
            }

            if ($this->model->create($data)) {
                Session::setFlash('success', 'Clothing Model created successfully!');
                header('Location: ' . BASE_URL . '/models');
                exit;
            } else {
                // Database error, potentially delete uploaded file
                if (!empty($data['PhotoURL'])) {
                    $this->deletePhotoFile($data['PhotoURL']);
                }
                Session::setFlash('error', 'Failed to create model.');
                Session::setFlash('old_data', $_POST);
                $categories = $this->model->getCategories();
                $this->loadView('models/create', ['categories' => $categories, 'old_data' => $_POST]);
            }
        } else {
            header('Location: ' . BASE_URL . '/models/create');
            exit;
        }
    }

    /**
     * Show the form for editing the specified model.
     * @param int $modelId
     */
    public function edit(int $modelId): void {
        $clothingModel = $this->model->findById($modelId);
        if (!$clothingModel) {
            Session::setFlash('error', 'Model not found.');
            header('Location: ' . BASE_URL . '/models');
            exit;
        }
        $categories = $this->model->getCategories();
        $this->loadView('models/edit', ['model' => $clothingModel, 'categories' => $categories]);
    }

    /**
     * Update the specified model in storage.
     * @param int $modelId
     */
    public function update(int $modelId): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $currentModel = $this->model->findById($modelId);
            if (!$currentModel) {
                Session::setFlash('error', 'Model not found for update.');
                header('Location: ' . BASE_URL . '/models');
                exit;
            }

            $data = [
                'Name' => trim($_POST['Name'] ?? ''),
                'Description' => trim($_POST['Description'] ?? ''),
                'CategoryID' => filter_input(INPUT_POST, 'CategoryID', FILTER_VALIDATE_INT),
                'BasePrice' => filter_input(INPUT_POST, 'BasePrice', FILTER_VALIDATE_FLOAT),
                // PhotoURL might be updated or remain the same
            ];

            $errors = $this->validateModelData($data, false); // false for update (photo not required)
            $newPhotoPath = null;

            // Check if a new photo is uploaded
            if (isset($_FILES['PhotoURL']) && $_FILES['PhotoURL']['error'] !== UPLOAD_ERR_NO_FILE) {
                $fileUploadResult = $this->handleFileUpload($_FILES['PhotoURL']);
                if ($fileUploadResult['error']) {
                    $errors['PhotoURL'] = $fileUploadResult['error'];
                } else {
                    $newPhotoPath = $fileUploadResult['filePath'];
                    $data['PhotoURL'] = $newPhotoPath; // Set new photo URL for update
                }
            }

            if (!empty($errors)) {
                // If new photo uploaded but validation failed, delete the new photo
                if ($newPhotoPath) {
                    $this->deletePhotoFile($newPhotoPath);
                }
                Session::setFlash('errors', $errors);
                Session::setFlash('old_data', $_POST);
                $categories = $this->model->getCategories();
                // Pass current model data merged with attempted old_data to repopulate form correctly
                $this->loadView('models/edit', ['model' => array_merge($currentModel, $_POST), 'categories' => $categories, 'errors' => $errors]);
                return;
            }

            // If a new photo was successfully uploaded, delete the old one (if different)
            if ($newPhotoPath && !empty($currentModel['PhotoURL']) && $currentModel['PhotoURL'] !== $newPhotoPath) {
                $this->deletePhotoFile($currentModel['PhotoURL']);
            }

            // If no new photo uploaded, don't update PhotoURL in $data unless it was explicitly cleared (not handled here)
            if (!$newPhotoPath && isset($data['PhotoURL'])) {
                 unset($data['PhotoURL']); // Don't update if no new file and not explicitly clearing
            }


            if ($this->model->update($modelId, $data)) {
                Session::setFlash('success', 'Clothing Model updated successfully!');
                header('Location: ' . BASE_URL . '/models');
                exit;
            } else {
                // If update failed but a new photo was uploaded, delete the new photo
                if ($newPhotoPath) {
                    $this->deletePhotoFile($newPhotoPath);
                }
                Session::setFlash('error', 'Failed to update model.');
                Session::setFlash('old_data', $_POST);
                $categories = $this->model->getCategories();
                $this->loadView('models/edit', ['model' => array_merge($currentModel, $_POST), 'categories' => $categories]);

            }
        } else {
            header('Location: ' . BASE_URL . '/models/edit/' . $modelId);
            exit;
        }
    }

    /**
     * Soft deletes the specified model.
     * @param int $modelId
     */
    public function destroy(int $modelId): void {
        // Optional: CSRF token check
        $modelData = $this->model->findById($modelId); // Check if model exists before trying to delete
        if (!$modelData) {
            Session::setFlash('error', 'Model not found or already deleted.');
            header('Location: ' . BASE_URL . '/models');
            exit;
        }

        if ($this->model->delete($modelId)) {
            Session::setFlash('success', 'Clothing Model marked as deleted.');
            // Note: The associated photo file is NOT deleted from the server during soft delete.
            // A separate cleanup process or hard delete feature would handle file removal if needed.
        } else {
            Session::setFlash('error', 'Failed to delete model.');
        }
        header('Location: ' . BASE_URL . '/models');
        exit;
    }

    /**
     * Validates model data.
     * @param array $data
     * @param bool $isCreate Is this for creation (photo required)?
     * @return array Array of errors.
     */
    private function validateModelData(array $data, bool $isCreate = false): array {
        $errors = [];
        if (empty($data['Name'])) {
            $errors['Name'] = 'Model Name is required.';
        }
        if (!isset($data['CategoryID']) || $data['CategoryID'] === false || $data['CategoryID'] === '') { // false from filter_input
            $errors['CategoryID'] = 'Category is required.';
        }
        if (!isset($data['BasePrice']) || $data['BasePrice'] === false || $data['BasePrice'] < 0) { // false from filter_input
            $errors['BasePrice'] = 'Valid Base Price is required and must be non-negative.';
        }
        // Description is optional
        return $errors;
    }

    /**
     * Handles file upload.
     * @param array|null $fileData from $_FILES['inputName']
     * @return array ['error' => string|null, 'filePath' => string|null]
     */
    private function handleFileUpload(?array $fileData): array {
        if (empty($fileData) || $fileData['error'] === UPLOAD_ERR_NO_FILE) {
            return ['error' => null, 'filePath' => null]; // No file uploaded, not an error for updates
        }

        // Check for other upload errors
        if ($fileData['error'] !== UPLOAD_ERR_OK) {
            return ['error' => 'File upload error code: ' . $fileData['error'], 'filePath' => null];
        }

        // Basic security checks
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $fileMimeType = mime_content_type($fileData['tmp_name']);
        if (!in_array($fileMimeType, $allowedTypes)) {
            return ['error' => 'Invalid file type. Allowed: JPG, PNG, GIF, WEBP.', 'filePath' => null];
        }

        // Max file size (e.g., 2MB)
        if ($fileData['size'] > 2 * 1024 * 1024) {
            return ['error' => 'File is too large. Max 2MB allowed.', 'filePath' => null];
        }

        // Generate unique filename to prevent overwrites and sanitize
        $fileName = pathinfo($fileData['name'], PATHINFO_FILENAME);
        $fileExtension = strtolower(pathinfo($fileData['name'], PATHINFO_EXTENSION));
        $safeFileName = preg_replace("/[^A-Za-z0-9\-_]/", '_', $fileName); // Sanitize
        $uniqueFileName = $safeFileName . '_' . uniqid() . '.' . $fileExtension;

        $destinationPath = self::UPLOAD_DIR . $uniqueFileName;

        if (!is_writable(self::UPLOAD_DIR)) {
            error_log("Upload directory is not writable: " . self::UPLOAD_DIR);
            return ['error' => 'Server configuration error: Upload directory not writable.', 'filePath' => null];
        }

        if (move_uploaded_file($fileData['tmp_name'], $destinationPath)) {
            // Return path relative to public web root or a consistent internal path format
            // For simplicity, let's assume BASE_URL might not align perfectly with file system paths for web access
            // Store path that can be used with BASE_URL. E.g., 'uploads/models/' . $uniqueFileName
            return ['error' => null, 'filePath' => 'uploads/models/' . $uniqueFileName];
        } else {
            return ['error' => 'Failed to move uploaded file.', 'filePath' => null];
        }
    }

    /**
     * Deletes a photo file from the server.
     * @param string $filePath Path relative to APP_ROOT/public/
     */
    private function deletePhotoFile(string $filePath): void {
        if (empty($filePath)) return;
        $fullPath = APP_ROOT . '/public/' . $filePath; // Adjust if filePath structure is different
        if (file_exists($fullPath)) {
            unlink($fullPath);
        }
    }


    /**
     * Simple view loader.
     */
    protected function loadView(string $viewName, array $data = []): void {
        extract($data);
        $viewFile = VIEWS_PATH . $viewName . '.php';
        if (is_readable($viewFile)) {
            // Conceptual: include a common header/footer
            // require_once VIEWS_PATH . 'layout/header.php';
            require_once $viewFile;
            // require_once VIEWS_PATH . 'layout/footer.php';
        } else {
            die("Error: View '{$viewName}' not found.");
        }
    }
}
?>
