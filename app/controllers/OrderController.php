<?php
// app/controllers/OrderController.php

namespace App\Controllers;

use App\Models\OrderModel;
use App\Models\Client; // Assuming Client model from previous step
use App\Models\ClothingModel; // Assuming ClothingModel from previous step
use App\Models\MeasurementModel; // Assuming MeasurementModel from previous step
use App\Core\Session;

class OrderController {
    private OrderModel $orderModel;
    private Client $clientModel;
    private ClothingModel $clothingModel;
    private MeasurementModel $measurementModel;

    public function __construct() {
        if (!Session::isLoggedIn()) {
            Session::setFlash('error', 'Please log in to manage orders.');
            header('Location: ' . BASE_URL . '/auth/login');
            exit;
        }
        $this->orderModel = new OrderModel();
        $this->clientModel = new Client();
        $this->clothingModel = new ClothingModel();
        $this->measurementModel = new MeasurementModel();
    }

    /**
     * Display a listing of all orders.
     */
    public function index(): void {
        $orders = $this->orderModel->getAll();
        $this->loadView('orders/index', ['orders' => $orders]);
    }

    /**
     * Show the form for creating a new order.
     */
    public function create(): void {
        $clients = $this->clientModel->getAll(); // Assuming getAll() only gets active clients
        $models = $this->clothingModel->getAll(); // Assuming getAll() only gets active models
        $statuses = $this->orderModel->getOrderStatuses();

        $this->loadView('orders/create', [
            'clients' => $clients,
            'clothingModels' => $models,
            'orderStatuses' => $statuses
        ]);
    }

    /**
     * Store a newly created order in storage.
     */
    public function store(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'ClientID' => filter_input(INPUT_POST, 'ClientID', FILTER_VALIDATE_INT),
                'ModelID' => filter_input(INPUT_POST, 'ModelID', FILTER_VALIDATE_INT),
                'MeasurementID' => filter_input(INPUT_POST, 'MeasurementID', FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE), // Can be null
                'OrderDate' => $_POST['OrderDate'] ?? date('Y-m-d'),
                'EstimatedCompletionDate' => $_POST['EstimatedCompletionDate'] ?? null,
                'FinalPrice' => filter_input(INPUT_POST, 'FinalPrice', FILTER_VALIDATE_FLOAT),
                'OrderStatusID' => filter_input(INPUT_POST, 'OrderStatusID', FILTER_VALIDATE_INT),
                'CustomizationNotes' => trim($_POST['CustomizationNotes'] ?? '')
            ];

            $errors = $this->validateOrderData($data);

            if (!empty($errors)) {
                Session::setFlash('errors', $errors);
                Session::setFlash('old_data', $_POST);
                // Repopulate necessary data for the view
                $clients = $this->clientModel->getAll();
                $models = $this->clothingModel->getAll();
                $statuses = $this->orderModel->getOrderStatuses();
                $this->loadView('orders/create', [
                    'clients' => $clients,
                    'clothingModels' => $models,
                    'orderStatuses' => $statuses,
                    'errors' => $errors,
                    'old_data' => $_POST
                ]);
                return;
            }

            if ($this->orderModel->create($data)) {
                Session::setFlash('success', 'Order created successfully!');
                header('Location: ' . BASE_URL . '/orders');
                exit;
            } else {
                Session::setFlash('error', 'Failed to create order.');
                Session::setFlash('old_data', $_POST);
                $clients = $this->clientModel->getAll();
                $models = $this->clothingModel->getAll();
                $statuses = $this->orderModel->getOrderStatuses();
                $this->loadView('orders/create', [
                    'clients' => $clients,
                    'clothingModels' => $models,
                    'orderStatuses' => $statuses,
                    'old_data' => $_POST
                ]);
            }
        } else {
            header('Location: ' . BASE_URL . '/orders/create');
            exit;
        }
    }

    /**
     * Display the specified order.
     * @param int $orderId
     */
    public function show(int $orderId): void {
        $order = $this->orderModel->findById($orderId);
        if (!$order) {
            Session::setFlash('error', 'Order not found.');
            header('Location: ' . BASE_URL . '/orders');
            exit;
        }
        $this->loadView('orders/show', ['order' => $order]);
    }

    /**
     * Show the form for editing the specified order.
     * (Primarily for status, price, notes - not core client/model/measurements)
     * @param int $orderId
     */
    public function edit(int $orderId): void {
        $order = $this->orderModel->findById($orderId);
        if (!$order) {
            Session::setFlash('error', 'Order not found.');
            header('Location: ' . BASE_URL . '/orders');
            exit;
        }
        $statuses = $this->orderModel->getOrderStatuses();
        $this->loadView('orders/edit', ['order' => $order, 'orderStatuses' => $statuses]);
    }

    /**
     * Update the specified order in storage.
     * @param int $orderId
     */
    public function update(int $orderId): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $existingOrder = $this->orderModel->findById($orderId);
            if (!$existingOrder) {
                Session::setFlash('error', 'Order not found for update.');
                header('Location: ' . BASE_URL . '/orders');
                exit;
            }

            $data = [
                // ClientID, ModelID, MeasurementID are generally not changed here for simplicity
                'EstimatedCompletionDate' => $_POST['EstimatedCompletionDate'] ?? null,
                'FinalPrice' => filter_input(INPUT_POST, 'FinalPrice', FILTER_VALIDATE_FLOAT),
                'OrderStatusID' => filter_input(INPUT_POST, 'OrderStatusID', FILTER_VALIDATE_INT),
                'CustomizationNotes' => trim($_POST['CustomizationNotes'] ?? '')
            ];

            $errors = $this->validateOrderData($data, false); // false: not a creation, so some fields might be optional

            if (!empty($errors)) {
                Session::setFlash('errors', $errors);
                Session::setFlash('old_data', $_POST);
                $statuses = $this->orderModel->getOrderStatuses();
                $this->loadView('orders/edit', ['order' => array_merge($existingOrder, $_POST), 'orderStatuses' => $statuses, 'errors' => $errors]);
                return;
            }

            if ($this->orderModel->update($orderId, $data)) {
                Session::setFlash('success', 'Order updated successfully!');
                header('Location: ' . BASE_URL . '/orders/show/' . $orderId);
                exit;
            } else {
                Session::setFlash('error', 'Failed to update order.');
                Session::setFlash('old_data', $_POST);
                $statuses = $this->orderModel->getOrderStatuses();
                $this->loadView('orders/edit', ['order' => array_merge($existingOrder, $_POST), 'orderStatuses' => $statuses]);
            }
        } else {
            header('Location: ' . BASE_URL . '/orders/edit/' . $orderId);
            exit;
        }
    }

    /**
     * AJAX endpoint to fetch measurements for a given client.
     * @param int $clientId
     */
    public function getMeasurementsForClient(int $clientId): void {
        header('Content-Type: application/json');
        if (!$this->clientModel->findById($clientId)) {
            echo json_encode(['error' => 'Client not found']);
            return;
        }
        $measurements = $this->measurementModel->getByClientId($clientId);
        echo json_encode($measurements);
        exit;
    }

    /**
     * Validates order data.
     * @param array $data
     * @param bool $isCreate If true, some fields are more strictly required.
     * @return array Array of errors.
     */
    private function validateOrderData(array $data, bool $isCreate = true): array {
        $errors = [];

        if ($isCreate) { // Fields required only on creation
            if (empty($data['ClientID'])) $errors['ClientID'] = 'Client is required.';
            if (empty($data['ModelID'])) $errors['ModelID'] = 'Clothing Model is required.';
            // MeasurementID can be optional/null
        }

        if (empty($data['OrderDate'])) {
            $errors['OrderDate'] = 'Order Date is required.';
        } elseif (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $data['OrderDate'])) {
            $errors['OrderDate'] = 'Invalid Order Date format. Use YYYY-MM-DD.';
        }

        if (!empty($data['EstimatedCompletionDate']) && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $data['EstimatedCompletionDate'])) {
            $errors['EstimatedCompletionDate'] = 'Invalid Estimated Completion Date format. Use YYYY-MM-DD.';
        }

        if ($data['FinalPrice'] === null || $data['FinalPrice'] === false || $data['FinalPrice'] < 0) {
            $errors['FinalPrice'] = 'Valid Final Price is required and must be non-negative.';
        }
        if (empty($data['OrderStatusID'])) {
            $errors['OrderStatusID'] = 'Order Status is required.';
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
            require_once $viewFile;
        } else {
            die("Error: View '{$viewName}' not found.");
        }
    }
     /**
     * Soft deletes an order.
     * @param int $orderId
     */
    public function destroy(int $orderId): void {
        $order = $this->orderModel->findById($orderId);
        if (!$order) {
            Session::setFlash('error', 'Order not found or already deleted.');
        } else {
            if ($this->orderModel->delete($orderId)) {
                Session::setFlash('success', 'Order marked as deleted successfully.');
            } else {
                Session::setFlash('error', 'Failed to delete order.');
            }
        }
        header('Location: ' . BASE_URL . '/orders');
        exit;
    }
}
?>
