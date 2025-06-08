<?php
// app/controllers/PaymentController.php

namespace App\Controllers;

use App\Models\PaymentModel;
use App\Models\OrderModel; // To fetch order details for context
use App\Core\Session;

class PaymentController {
    private PaymentModel $paymentModel;
    private OrderModel $orderModel;

    public function __construct() {
        if (!Session::isLoggedIn()) {
            Session::setFlash('error', 'Please log in to manage payments.');
            header('Location: ' . BASE_URL . '/auth/login');
            exit;
        }
        $this->paymentModel = new PaymentModel();
        $this->orderModel = new OrderModel();
    }

    /**
     * List payments for a specific order and show balance.
     * @param int $orderId
     */
    public function index(int $orderId): void {
        $order = $this->orderModel->findById($orderId);
        if (!$order) {
            Session::setFlash('error', 'Order not found.');
            header('Location: ' . BASE_URL . '/orders');
            exit;
        }

        $payments = $this->paymentModel->getByOrderId($orderId);
        $totalPaid = $this->paymentModel->getTotalPaidForOrder($orderId);
        $balanceDue = (float)$order['FinalPrice'] - $totalPaid;

        $this->loadView('payments/index', [
            'order' => $order,
            'payments' => $payments,
            'totalPaid' => $totalPaid,
            'balanceDue' => $balanceDue
        ]);
    }

    /**
     * Show form to add a new payment for an order.
     * @param int $orderId
     */
    public function create(int $orderId): void {
        $order = $this->orderModel->findById($orderId); // Fetch basic order details for context
        if (!$order) {
            Session::setFlash('error', 'Order not found. Cannot add payment.');
            header('Location: ' . BASE_URL . '/orders');
            exit;
        }

        $totalPaid = $this->paymentModel->getTotalPaidForOrder($orderId);
        $balanceDue = (float)$order['FinalPrice'] - $totalPaid;

        // Suggest payment amount up to balance due
        $suggestedAmount = $balanceDue > 0 ? $balanceDue : 0;


        $this->loadView('payments/create', ['order' => $order, 'suggestedAmount' => $suggestedAmount, 'balanceDue' => $balanceDue]);
    }

    /**
     * Store a new payment. OrderID is part of the POST data.
     */
    public function store(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $orderId = filter_input(INPUT_POST, 'OrderID', FILTER_VALIDATE_INT);
            if (!$orderId) {
                Session::setFlash('error', 'Order ID is missing or invalid.');
                header('Location: ' . BASE_URL . '/orders'); // Redirect to a sensible default
                exit;
            }

            $order = $this->orderModel->findById($orderId);
            if (!$order) {
                Session::setFlash('error', 'Order not found for this payment.');
                header('Location: ' . BASE_URL . '/orders');
                exit;
            }

            $data = [
                'OrderID' => $orderId,
                'PaymentDate' => $_POST['PaymentDate'] ?? date('Y-m-d'),
                'AmountPaid' => filter_input(INPUT_POST, 'AmountPaid', FILTER_VALIDATE_FLOAT),
                'PaymentMethodNotes' => trim($_POST['PaymentMethodNotes'] ?? '')
            ];

            $errors = $this->validatePaymentData($data);

            // Additional check: Amount paid should not be more than balance due if strictly enforced
            // For now, just basic validation.

            if (!empty($errors)) {
                Session::setFlash('errors', $errors);
                Session::setFlash('old_data', $_POST);
                $totalPaid = $this->paymentModel->getTotalPaidForOrder($orderId);
                $balanceDue = (float)$order['FinalPrice'] - $totalPaid;
                $suggestedAmount = $balanceDue > 0 ? $balanceDue : 0;
                $this->loadView('payments/create', ['order' => $order, 'errors' => $errors, 'old_data' => $_POST, 'suggestedAmount' => $suggestedAmount, 'balanceDue' => $balanceDue]);
                return;
            }

            if ($this->paymentModel->create($data)) {
                Session::setFlash('success', 'Payment recorded successfully for Order #' . $orderId);
                header('Location: ' . BASE_URL . '/payments/index/' . $orderId);
                exit;
            } else {
                Session::setFlash('error', 'Failed to record payment.');
                Session::setFlash('old_data', $_POST);
                $totalPaid = $this->paymentModel->getTotalPaidForOrder($orderId);
                $balanceDue = (float)$order['FinalPrice'] - $totalPaid;
                $suggestedAmount = $balanceDue > 0 ? $balanceDue : 0;
                $this->loadView('payments/create', ['order' => $order, 'old_data' => $_POST, 'suggestedAmount' => $suggestedAmount, 'balanceDue' => $balanceDue]);
            }
        } else {
            // Redirect if not POST, ideally to a relevant page or show error
            header('Location: ' . BASE_URL . '/orders');
            exit;
        }
    }

    /**
     * Show form to edit an existing payment.
     * @param int $paymentId
     */
    public function edit(int $paymentId): void {
        $payment = $this->paymentModel->findById($paymentId);
        if (!$payment) {
            Session::setFlash('error', 'Payment record not found.');
            // Try to redirect to a sensible default if possible
            $fallbackOrderId = Session::get('last_viewed_order_id_for_payment_context') ?? null;
            header('Location: ' . ($fallbackOrderId ? BASE_URL . '/payments/index/' . $fallbackOrderId : BASE_URL . '/orders'));
            exit;
        }
        $order = $this->orderModel->findById($payment['OrderID']);
        if (!$order) {
            Session::setFlash('error', 'Associated order not found.');
            header('Location: ' . BASE_URL . '/orders'); // Fallback
            exit;
        }
        Session::set('last_viewed_order_id_for_payment_context', $order['OrderID']); // Store for potential fallback
        $this->loadView('payments/edit', ['payment' => $payment, 'order' => $order]);
    }

    /**
     * Update an existing payment.
     * @param int $paymentId
     */
    public function update(int $paymentId): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $existingPayment = $this->paymentModel->findById($paymentId);
            if (!$existingPayment) {
                Session::setFlash('error', 'Payment record not found for update.');
                header('Location: ' . BASE_URL . '/orders'); // Fallback
                exit;
            }
            $orderId = $existingPayment['OrderID']; // Get OrderID from existing payment

            $data = [
                'PaymentDate' => $_POST['PaymentDate'] ?? date('Y-m-d'),
                'AmountPaid' => filter_input(INPUT_POST, 'AmountPaid', FILTER_VALIDATE_FLOAT),
                'PaymentMethodNotes' => trim($_POST['PaymentMethodNotes'] ?? '')
                // OrderID is not changed during payment update
            ];

            $errors = $this->validatePaymentData($data);

            if (!empty($errors)) {
                Session::setFlash('errors', $errors);
                Session::setFlash('old_data', $_POST);
                $order = $this->orderModel->findById($orderId);
                $this->loadView('payments/edit', ['payment' => array_merge($existingPayment, $_POST), 'order' => $order, 'errors' => $errors]);
                return;
            }

            if ($this->paymentModel->update($paymentId, $data)) {
                Session::setFlash('success', 'Payment record updated successfully.');
                header('Location: ' . BASE_URL . '/payments/index/' . $orderId);
                exit;
            } else {
                Session::setFlash('error', 'Failed to update payment record.');
                Session::setFlash('old_data', $_POST);
                $order = $this->orderModel->findById($orderId);
                $this->loadView('payments/edit', ['payment' => array_merge($existingPayment, $_POST), 'order' => $order]);
            }
        } else {
            header('Location: ' . BASE_URL . '/payments/edit/' . $paymentId);
            exit;
        }
    }

    /**
     * Delete a payment record.
     * @param int $paymentId
     */
    public function destroy(int $paymentId): void {
        $payment = $this->paymentModel->findById($paymentId);
        if (!$payment) {
            Session::setFlash('error', 'Payment record not found or already deleted.');
             $fallbackOrderId = Session::get('last_viewed_order_id_for_payment_context') ?? null;
            header('Location: ' . ($fallbackOrderId ? BASE_URL . '/payments/index/' . $fallbackOrderId : BASE_URL . '/orders'));
            exit;
        }
        $orderId = $payment['OrderID']; // For redirect

        if ($this->paymentModel->delete($paymentId)) {
            Session::setFlash('success', 'Payment record deleted successfully.');
        } else {
            Session::setFlash('error', 'Failed to delete payment record.');
        }
        header('Location: ' . BASE_URL . '/payments/index/' . $orderId);
        exit;
    }

    /**
     * Validates payment data.
     */
    private function validatePaymentData(array $data): array {
        $errors = [];
        if (empty($data['PaymentDate'])) {
            $errors['PaymentDate'] = 'Payment Date is required.';
        } elseif (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $data['PaymentDate'])) {
            $errors['PaymentDate'] = 'Invalid Payment Date format. Use YYYY-MM-DD.';
        }
        if ($data['AmountPaid'] === null || $data['AmountPaid'] === false || $data['AmountPaid'] <= 0) {
            $errors['AmountPaid'] = 'Amount Paid must be a positive number.';
        }
        if (empty($data['PaymentMethodNotes'])) {
            $errors['PaymentMethodNotes'] = 'Payment Method/Notes are required.';
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
}
?>
