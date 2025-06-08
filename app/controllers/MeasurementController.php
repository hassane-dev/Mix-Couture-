<?php
// app/controllers/MeasurementController.php

namespace App\Controllers;

use App\Models\MeasurementModel;
use App\Models\Client; // Assuming Client model from previous steps
use App\Core\Session;   // Assuming Session class from previous steps

class MeasurementController {
    private MeasurementModel $measurementModel;
    private Client $clientModel; // To fetch client details

    public function __construct() {
        // Ensure user is logged in
        if (!Session::isLoggedIn()) {
            Session::setFlash('error', 'Please log in to manage measurements.');
            header('Location: ' . BASE_URL . '/auth/login');
            exit;
        }
        $this->measurementModel = new MeasurementModel();
        $this->clientModel = new Client();
    }

    /**
     * Display a listing of measurement sets for a specific client.
     * @param int $clientId
     */
    public function index(int $clientId): void {
        $client = $this->clientModel->findById($clientId);
        if (!$client) {
            Session::setFlash('error', 'Client not found.');
            header('Location: ' . BASE_URL . '/clients'); // Redirect to client list
            exit;
        }

        $measurements = $this->measurementModel->getByClientId($clientId);
        $this->loadView('measurements/index', [
            'measurements' => $measurements,
            'client' => $client
        ]);
    }

    /**
     * Show the form for creating new measurements for a client.
     * @param int $clientId
     */
    public function create(int $clientId): void {
        $client = $this->clientModel->findById($clientId);
        if (!$client) {
            Session::setFlash('error', 'Client not found.');
            header('Location: ' . BASE_URL . '/clients');
            exit;
        }
        $this->loadView('measurements/create', ['client' => $client]);
    }

    /**
     * Store a newly created measurement set in storage.
     * @param int $clientId
     */
    public function store(int $clientId): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $client = $this->clientModel->findById($clientId);
            if (!$client) {
                Session::setFlash('error', 'Client not found for these measurements.');
                header('Location: ' . BASE_URL . '/clients');
                exit;
            }

            $data = [
                'ClientID' => $clientId,
                'MeasurementName' => trim($_POST['MeasurementName'] ?? 'Standard Set ' . date('Y-m-d')),
                'DateTaken' => $_POST['DateTaken'] ?? date('Y-m-d'),
                'Bust' => filter_input(INPUT_POST, 'Bust', FILTER_VALIDATE_FLOAT, FILTER_NULL_ON_FAILURE),
                'Waist' => filter_input(INPUT_POST, 'Waist', FILTER_VALIDATE_FLOAT, FILTER_NULL_ON_FAILURE),
                'Hips' => filter_input(INPUT_POST, 'Hips', FILTER_VALIDATE_FLOAT, FILTER_NULL_ON_FAILURE),
                'ShoulderWidth' => filter_input(INPUT_POST, 'ShoulderWidth', FILTER_VALIDATE_FLOAT, FILTER_NULL_ON_FAILURE),
                'SleeveLength' => filter_input(INPUT_POST, 'SleeveLength', FILTER_VALIDATE_FLOAT, FILTER_NULL_ON_FAILURE),
                'Inseam' => filter_input(INPUT_POST, 'Inseam', FILTER_VALIDATE_FLOAT, FILTER_NULL_ON_FAILURE),
            ];

            $errors = $this->validateMeasurementData($data);

            if (!empty($errors)) {
                Session::setFlash('errors', $errors);
                Session::setFlash('old_data', $_POST);
                $this->loadView('measurements/create', ['client' => $client, 'errors' => $errors, 'old_data' => $_POST]);
                return;
            }

            if ($this->measurementModel->create($data)) {
                Session::setFlash('success', 'Measurement set created successfully for ' . htmlspecialchars($client['FullName']) . '!');
                header('Location: ' . BASE_URL . '/measurements/index/' . $clientId);
                exit;
            } else {
                Session::setFlash('error', 'Failed to create measurement set.');
                Session::setFlash('old_data', $_POST);
                $this->loadView('measurements/create', ['client' => $client, 'old_data' => $_POST]);
            }
        } else {
            header('Location: ' . BASE_URL . '/measurements/create/' . $clientId);
            exit;
        }
    }

    /**
     * Show the form for editing the specified measurement set.
     * @param int $measurementId
     */
    public function edit(int $measurementId): void {
        $measurement = $this->measurementModel->findById($measurementId);
        if (!$measurement) {
            Session::setFlash('error', 'Measurement set not found.');
            header('Location: ' . BASE_URL . '/clients'); // Or a more relevant page
            exit;
        }
        $client = $this->clientModel->findById($measurement['ClientID']);
        if (!$client) {
            Session::setFlash('error', 'Associated client not found.');
            header('Location: ' . BASE_URL . '/clients');
            exit;
        }
        $this->loadView('measurements/edit', ['measurement' => $measurement, 'client' => $client]);
    }

    /**
     * Update the specified measurement set in storage.
     * @param int $measurementId
     */
    public function update(int $measurementId): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $existingMeasurement = $this->measurementModel->findById($measurementId);
            if (!$existingMeasurement) {
                Session::setFlash('error', 'Measurement set not found for update.');
                header('Location: ' . BASE_URL . '/clients');
                exit;
            }
            // ClientID should not change during an update of measurements
            $clientId = $existingMeasurement['ClientID'];
            $client = $this->clientModel->findById($clientId);


            $data = [
                // ClientID is not updated from form, it's fixed for the measurement set
                'MeasurementName' => trim($_POST['MeasurementName'] ?? 'Standard Set ' . date('Y-m-d')),
                'DateTaken' => $_POST['DateTaken'] ?? date('Y-m-d'),
                'Bust' => filter_input(INPUT_POST, 'Bust', FILTER_VALIDATE_FLOAT, FILTER_NULL_ON_FAILURE),
                'Waist' => filter_input(INPUT_POST, 'Waist', FILTER_VALIDATE_FLOAT, FILTER_NULL_ON_FAILURE),
                'Hips' => filter_input(INPUT_POST, 'Hips', FILTER_VALIDATE_FLOAT, FILTER_NULL_ON_FAILURE),
                'ShoulderWidth' => filter_input(INPUT_POST, 'ShoulderWidth', FILTER_VALIDATE_FLOAT, FILTER_NULL_ON_FAILURE),
                'SleeveLength' => filter_input(INPUT_POST, 'SleeveLength', FILTER_VALIDATE_FLOAT, FILTER_NULL_ON_FAILURE),
                'Inseam' => filter_input(INPUT_POST, 'Inseam', FILTER_VALIDATE_FLOAT, FILTER_NULL_ON_FAILURE),
            ];

            $errors = $this->validateMeasurementData($data);

            if (!empty($errors)) {
                Session::setFlash('errors', $errors);
                Session::setFlash('old_data', $_POST);
                // Repopulate $measurement with attempted (but failed) data for the form
                $this->loadView('measurements/edit', [
                    'measurement' => array_merge($existingMeasurement, $_POST), // Show attempted data
                    'client' => $client,
                    'errors' => $errors
                ]);
                return;
            }

            if ($this->measurementModel->update($measurementId, $data)) {
                Session::setFlash('success', 'Measurement set updated successfully!');
                header('Location: ' . BASE_URL . '/measurements/index/' . $clientId);
                exit;
            } else {
                Session::setFlash('error', 'Failed to update measurement set.');
                Session::setFlash('old_data', $_POST);
                $this->loadView('measurements/edit', [
                    'measurement' => array_merge($existingMeasurement, $_POST),
                    'client' => $client
                ]);
            }
        } else {
            header('Location: ' . BASE_URL . '/measurements/edit/' . $measurementId);
            exit;
        }
    }

    /**
     * Deletes the specified measurement set.
     * @param int $measurementId
     */
    public function destroy(int $measurementId): void {
        $measurement = $this->measurementModel->findById($measurementId);
        if (!$measurement) {
            Session::setFlash('error', 'Measurement set not found or already deleted.');
            // Attempt to redirect to a sensible default if client context is lost
            header('Location: ' . (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : BASE_URL . '/clients'));
            exit;
        }
        $clientId = $measurement['ClientID']; // Get ClientID for redirect before deleting

        if ($this->measurementModel->delete($measurementId)) {
            Session::setFlash('success', 'Measurement set deleted successfully.');
        } else {
            Session::setFlash('error', 'Failed to delete measurement set.');
        }
        header('Location: ' . BASE_URL . '/measurements/index/' . $clientId);
        exit;
    }

    /**
     * Validates measurement data.
     * @param array $data
     * @return array Array of errors.
     */
    private function validateMeasurementData(array $data): array {
        $errors = [];
        if (empty($data['MeasurementName'])) {
            $errors['MeasurementName'] = 'Measurement set name/label is required.';
        }
        if (empty($data['DateTaken'])) {
            $errors['DateTaken'] = 'Date taken is required.';
        } elseif (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $data['DateTaken'])) { // Basic YYYY-MM-DD check
            $errors['DateTaken'] = 'Invalid date format. Use YYYY-MM-DD.';
        }

        // Example: Validate that numeric fields are indeed numeric if not null
        $numericFields = ['Bust', 'Waist', 'Hips', 'ShoulderWidth', 'SleeveLength', 'Inseam'];
        foreach ($numericFields as $field) {
            if (isset($_POST[$field]) && !empty($_POST[$field]) && $data[$field] === null) { // Check original POST value
                 $errors[$field] = ucfirst(preg_replace('/(?<!^)[A-Z]/', ' $0', $field)) . ' must be a valid number.';
            } elseif (isset($data[$field]) && $data[$field] !== null && $data[$field] < 0) {
                 $errors[$field] = ucfirst(preg_replace('/(?<!^)[A-Z]/', ' $0', $field)) . ' cannot be negative.';
            }
        }
        return $errors;
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
