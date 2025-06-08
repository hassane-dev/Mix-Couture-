<?php
// app/models/PaymentModel.php

namespace App\Models;

use PDO;
use PDOException;

// Assuming Database class from previous examples
// class Database { ... }

class PaymentModel {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    /**
     * Fetches all payments for a given order.
     * @param int $orderId The ID of the order.
     * @return array An array of payments for the order.
     */
    public function getByOrderId(int $orderId): array {
        $sql = "SELECT PaymentID, OrderID, PaymentDate, AmountPaid, PaymentMethodNotes, CreatedAt, UpdatedAt
                FROM Payments
                WHERE OrderID = :orderId
                ORDER BY PaymentDate DESC, CreatedAt DESC";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':orderId', $orderId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching payments for order {$orderId}: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Fetches a single payment record by its ID.
     * @param int $paymentId The ID of the payment.
     * @return array|false The payment data or false if not found.
     */
    public function findById(int $paymentId): array|false {
        $sql = "SELECT PaymentID, OrderID, PaymentDate, AmountPaid, PaymentMethodNotes, CreatedAt, UpdatedAt
                FROM Payments
                WHERE PaymentID = :paymentId";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':paymentId', $paymentId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching payment by ID {$paymentId}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Records a new payment.
     * @param array $data Associative array of payment data.
     *        Expected keys: 'OrderID', 'PaymentDate', 'AmountPaid', 'PaymentMethodNotes'.
     * @return int|false The ID of the newly created payment record or false on failure.
     */
    public function create(array $data): int|false {
        $sql = "INSERT INTO Payments (OrderID, PaymentDate, AmountPaid, PaymentMethodNotes, CreatedAt, UpdatedAt)
                VALUES (:orderId, :paymentDate, :amountPaid, :paymentMethodNotes, NOW(), NOW())";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':orderId', $data['OrderID'], PDO::PARAM_INT);
            $stmt->bindParam(':paymentDate', $data['PaymentDate']); // Assuming YYYY-MM-DD format
            $stmt->bindParam(':amountPaid', $data['AmountPaid']);
            $stmt->bindParam(':paymentMethodNotes', $data['PaymentMethodNotes']);

            $stmt->execute();
            return (int)$this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log("Error recording payment: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Updates an existing payment record.
     * @param int $paymentId The ID of the payment to update.
     * @param array $data Associative array of payment data. OrderID should not be changed here.
     *        Allowed keys: 'PaymentDate', 'AmountPaid', 'PaymentMethodNotes'.
     * @return bool True on success, false on failure.
     */
    public function update(int $paymentId, array $data): bool {
        $sql = "UPDATE Payments SET
                    PaymentDate = :paymentDate,
                    AmountPaid = :amountPaid,
                    PaymentMethodNotes = :paymentMethodNotes,
                    UpdatedAt = NOW()
                WHERE PaymentID = :paymentId";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':paymentDate', $data['PaymentDate']);
            $stmt->bindParam(':amountPaid', $data['AmountPaid']);
            $stmt->bindParam(':paymentMethodNotes', $data['PaymentMethodNotes']);
            $stmt->bindParam(':paymentId', $paymentId, PDO::PARAM_INT);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error updating payment {$paymentId}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Deletes a payment record.
     * @param int $paymentId The ID of the payment to delete.
     * @return bool True on success, false on failure.
     */
    public function delete(int $paymentId): bool {
        // Consider implications: Deleting a payment affects order balance.
        // This is a hard delete. A soft delete might be preferable in some accounting contexts.
        $sql = "DELETE FROM Payments WHERE PaymentID = :paymentId";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':paymentId', $paymentId, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error deleting payment {$paymentId}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Calculates the total amount paid for a specific order.
     * @param int $orderId
     * @return float
     */
    public function getTotalPaidForOrder(int $orderId): float {
        $sql = "SELECT SUM(AmountPaid) as TotalPaid FROM Payments WHERE OrderID = :orderId";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':orderId', $orderId, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return (float)($result['TotalPaid'] ?? 0.0);
        } catch (PDOException $e) {
            error_log("Error calculating total paid for order {$orderId}: " . $e->getMessage());
            return 0.0;
        }
    }
}
?>
