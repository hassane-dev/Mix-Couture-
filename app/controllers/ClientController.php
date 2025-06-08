<?php
// app/controllers/ClientController.php

namespace App\Controllers;

use App\Models\Client;
use App\Core\Session; // Assuming Session class from previous step

class ClientController {
    private Client $clientModel;

    public function __construct() {
        // Ensure user is logged in to access client management
        // In a real app, this might be handled by a base controller or middleware
        if (!Session::isLoggedIn()) {
            Session::setFlash('error', 'Please log in to manage clients.');
            header('Location: ' . BASE_URL . '/auth/login');
            exit;
        }
        $this->clientModel = new Client();
    }

    /**
     * Displays a list of all clients.
     */
    public function index(): void {
        $clients = $this->clientModel->getAll();
        $this->loadView('clients/index', ['clients' => $clients]);
    }

    /**
     * Shows the form for creating a new client.
     */
    public function create(): void {
        $this->loadView('clients/create');
    }

    /**
     * Stores a new client in the database.
     */
    public function store(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'FullName' => trim($_POST['FullName'] ?? ''),
                'Email' => filter_var(trim($_POST['Email'] ?? ''), FILTER_SANITIZE_EMAIL),
                'PhoneNumber' => trim($_POST['PhoneNumber'] ?? ''),
                'Address' => trim($_POST['Address'] ?? '')
            ];

            // Basic Validation
            $errors = [];
            if (empty($data['FullName'])) {
                $errors['FullName'] = 'Full Name is required.';
            }
            if (empty($data['Email'])) {
                $errors['Email'] = 'Email is required.';
            } elseif (!filter_var($data['Email'], FILTER_VALIDATE_EMAIL)) {
                $errors['Email'] = 'Invalid Email format.';
            }
            if (empty($data['PhoneNumber'])) {
                $errors['PhoneNumber'] = 'Phone Number is required.';
            }
            // Add more validation as needed (e.g., phone number format)

            if (!empty($errors)) {
                Session::setFlash('errors', $errors);
                Session::setFlash('old_data', $data);
                $this->loadView('clients/create');
                return;
            }

            if ($this->clientModel->create($data)) {
                Session::setFlash('success', 'Client created successfully!');
                header('Location: ' . BASE_URL . '/clients'); // Adjusted to /clients instead of /clients/index
                exit;
            } else {
                Session::setFlash('error', 'Failed to create client. Email might already exist.');
                Session::setFlash('old_data', $data);
                $this->loadView('clients/create');
            }
        } else {
            header('Location: ' . BASE_URL . '/clients/create');
            exit;
        }
    }

    /**
     * Shows the form for editing an existing client.
     * @param int $clientId
     */
    public function edit(int $clientId): void {
        $client = $this->clientModel->findById($clientId);
        if (!$client) {
            Session::setFlash('error', 'Client not found.');
            header('Location: ' . BASE_URL . '/clients');
            exit;
        }
        $this->loadView('clients/edit', ['client' => $client]);
    }

    /**
     * Updates an existing client in the database.
     * @param int $clientId
     */
    public function update(int $clientId): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'FullName' => trim($_POST['FullName'] ?? ''),
                'Email' => filter_var(trim($_POST['Email'] ?? ''), FILTER_SANITIZE_EMAIL),
                'PhoneNumber' => trim($_POST['PhoneNumber'] ?? ''),
                'Address' => trim($_POST['Address'] ?? '')
            ];

            // Basic Validation
            $errors = [];
            if (empty($data['FullName'])) {
                $errors['FullName'] = 'Full Name is required.';
            }
            if (empty($data['Email'])) {
                $errors['Email'] = 'Email is required.';
            } elseif (!filter_var($data['Email'], FILTER_VALIDATE_EMAIL)) {
                $errors['Email'] = 'Invalid Email format.';
            }
             if (empty($data['PhoneNumber'])) {
                $errors['PhoneNumber'] = 'Phone Number is required.';
            }
            // Add more validation as needed

            if (!empty($errors)) {
                Session::setFlash('errors', $errors);
                Session::setFlash('old_data', $data);
                // Repopulate client for the view context if validation fails
                $client = $this->clientModel->findById($clientId);
                $this->loadView('clients/edit', ['client' => array_merge($client ?: [], $data), 'errors' => $errors]);
                return;
            }

            // Ensure client exists before attempting update
            $existingClient = $this->clientModel->findById($clientId);
            if (!$existingClient) {
                Session::setFlash('error', 'Client not found or already deleted.');
                header('Location: ' . BASE_URL . '/clients');
                exit;
            }


            if ($this->clientModel->update($clientId, $data)) {
                Session::setFlash('success', 'Client updated successfully!');
                header('Location: ' . BASE_URL . '/clients');
                exit;
            } else {
                Session::setFlash('error', 'Failed to update client. Email might already exist for another client.');
                Session::setFlash('old_data', $data);
                $client = $this->clientModel->findById($clientId); // Get original client data again
                $this->loadView('clients/edit', ['client' => array_merge($client ?: [], $data)]); // Merge to show current attempt
            }
        } else {
             header('Location: ' . BASE_URL . '/clients/edit/' . $clientId);
            exit;
        }
    }

    /**
     * Deletes a client.
     * @param int $clientId
     */
    public function destroy(int $clientId): void {
        // Optional: Add CSRF token check here for security

        if ($this->clientModel->delete($clientId)) {
            Session::setFlash('success', 'Client deleted successfully (soft delete).');
        } else {
            Session::setFlash('error', 'Failed to delete client.');
        }
        header('Location: ' . BASE_URL . '/clients');
        exit;
    }

    /**
     * Simple view loader.
     * @param string $viewName The name of the view file (e.g., 'clients/index').
     * @param array $data Data to pass to the view.
     */
    protected function loadView(string $viewName, array $data = []): void {
        // Make data available to the view
        extract($data);

        $viewFile = VIEWS_PATH . $viewName . '.php';
        if (is_readable($viewFile)) {
            // Conceptual: include a common header
            // if (file_exists(VIEWS_PATH . 'layout/header.php')) {
            //     require_once VIEWS_PATH . 'layout/header.php';
            // }

            require_once $viewFile;

            // Conceptual: include a common footer
            // if (file_exists(VIEWS_PATH . 'layout/footer.php')) {
            //     require_once VIEWS_PATH . 'layout/footer.php';
            // }
        } else {
            // In a real app, show a proper error page or log
            die("Error: View '{$viewName}' not found.");
        }
    }
}
?>
