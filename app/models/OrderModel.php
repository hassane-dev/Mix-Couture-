<?php
// app/models/OrderModel.php

namespace App\Models;

use PDO;
use PDOException;

// Assuming Database class from previous examples
// class Database { ... }

class OrderModel {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    /**
     * Fetches all active orders with basic client and model info.
     * @return array An array of all active orders.
     */
    public function getAll(): array {
        $sql = "SELECT
                    o.OrderID, o.OrderDate, o.EstimatedCompletionDate, o.FinalPrice,
                    o.CustomizationNotes, o.IsDeleted, o.CreatedAt, o.UpdatedAt,
                    c.FullName AS ClientName,
                    m.Name AS ModelName,
                    os.StatusName AS OrderStatus,
                    os.OrderStatusID
                FROM Orders o
                JOIN Clients c ON o.ClientID = c.ClientID
                JOIN Models m ON o.ModelID = m.ModelID
                JOIN OrderStatuses os ON o.OrderStatusID = os.OrderStatusID
                WHERE o.IsDeleted = 0
                ORDER BY o.OrderDate DESC, o.CreatedAt DESC";
        try {
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching all orders: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Fetches a single active order by its ID with detailed info.
     * @param int $orderId The ID of the order.
     * @return array|false The order data or false if not found or deleted.
     */
    public function findById(int $orderId): array|false {
        $sql = "SELECT
                    o.OrderID, o.ClientID, o.ModelID, o.MeasurementID,
                    o.OrderDate, o.EstimatedCompletionDate, o.FinalPrice,
                    o.OrderStatusID, o.CustomizationNotes, o.IsDeleted,
                    o.CreatedAt AS OrderCreatedAt, o.UpdatedAt AS OrderUpdatedAt,
                    c.FullName AS ClientFullName, c.Email AS ClientEmail, c.PhoneNumber AS ClientPhoneNumber,
                    m.Name AS ModelName, m.Description AS ModelDescription, m.BasePrice AS ModelBasePrice, m.PhotoURL AS ModelPhotoURL,
                    mc.CategoryName AS ModelCategoryName,
                    mes.MeasurementName, mes.DateTaken AS MeasurementDateTaken,
                    mes.Bust, mes.Waist, mes.Hips, mes.ShoulderWidth, mes.SleeveLength, mes.Inseam, /* Add other measurement fields from Measurements table */
                    os.StatusName AS OrderStatusName
                FROM Orders o
                JOIN Clients c ON o.ClientID = c.ClientID
                JOIN Models m ON o.ModelID = m.ModelID
                LEFT JOIN ModelCategories mc ON m.CategoryID = mc.CategoryID
                LEFT JOIN Measurements mes ON o.MeasurementID = mes.MeasurementID /* Ensure this table is named 'Measurements' */
                JOIN OrderStatuses os ON o.OrderStatusID = os.OrderStatusID
                WHERE o.OrderID = :orderId AND o.IsDeleted = 0";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':orderId', $orderId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching order by ID {$orderId}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Creates a new order.
     * @param array $data Associative array of order data.
     *        Expected: ClientID, ModelID, MeasurementID, OrderDate, EstimatedCompletionDate, FinalPrice, OrderStatusID, CustomizationNotes.
     * @return int|false The ID of the newly created order or false on failure.
     */
    public function create(array $data): int|false {
        $sql = "INSERT INTO Orders (ClientID, ModelID, MeasurementID, OrderDate, EstimatedCompletionDate,
                                FinalPrice, OrderStatusID, CustomizationNotes,
                                IsDeleted, CreatedAt, UpdatedAt)
                VALUES (:clientId, :modelId, :measurementId, :orderDate, :estimatedCompletionDate,
                        :finalPrice, :orderStatusId, :customizationNotes,
                        0, NOW(), NOW())";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':clientId', $data['ClientID'], PDO::PARAM_INT);
            $stmt->bindParam(':modelId', $data['ModelID'], PDO::PARAM_INT);
            $stmt->bindParam(':measurementId', $data['MeasurementID'], PDO::PARAM_INT); // Can be NULL if not provided initially
            $stmt->bindParam(':orderDate', $data['OrderDate']);
            $stmt->bindParam(':estimatedCompletionDate', $data['EstimatedCompletionDate']);
            $stmt->bindParam(':finalPrice', $data['FinalPrice']);
            $stmt->bindParam(':orderStatusId', $data['OrderStatusID'], PDO::PARAM_INT);
            $stmt->bindParam(':customizationNotes', $data['CustomizationNotes']);

            $stmt->execute();
            return (int)$this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log("Error creating order: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Updates an existing order.
     * @param int $orderId The ID of the order to update.
     * @param array $data Associative array of order data to update.
     *        Allowed: EstimatedCompletionDate, FinalPrice, OrderStatusID, CustomizationNotes.
     *        Client, Model, Measurement are generally not changed post-creation through this simple update.
     * @return bool True on success, false on failure.
     */
    public function update(int $orderId, array $data): bool {
        // Build the SQL query dynamically based on provided data
        $fieldsToUpdate = [];
        if (isset($data['EstimatedCompletionDate'])) $fieldsToUpdate['EstimatedCompletionDate'] = ':estimatedCompletionDate';
        if (isset($data['FinalPrice'])) $fieldsToUpdate['FinalPrice'] = ':finalPrice';
        if (isset($data['OrderStatusID'])) $fieldsToUpdate['OrderStatusID'] = ':orderStatusId';
        if (isset($data['CustomizationNotes'])) $fieldsToUpdate['CustomizationNotes'] = ':customizationNotes';
        // Add other updatable fields if necessary

        if (empty($fieldsToUpdate)) {
            return true; // Nothing to update, consider it a success or handle as needed
        }

        $setClauses = [];
        foreach (array_keys($fieldsToUpdate) as $fieldKey) {
            $setClauses[] = "{$fieldKey} = {$fieldsToUpdate[$fieldKey]}";
        }
        $setClauses[] = "UpdatedAt = NOW()";

        $sql = "UPDATE Orders SET " . implode(', ', $setClauses) . " WHERE OrderID = :orderId AND IsDeleted = 0";

        try {
            $stmt = $this->db->prepare($sql);

            if (isset($data['EstimatedCompletionDate'])) $stmt->bindParam(':estimatedCompletionDate', $data['EstimatedCompletionDate']);
            if (isset($data['FinalPrice'])) $stmt->bindParam(':finalPrice', $data['FinalPrice']);
            if (isset($data['OrderStatusID'])) $stmt->bindParam(':orderStatusId', $data['OrderStatusID'], PDO::PARAM_INT);
            if (isset($data['CustomizationNotes'])) $stmt->bindParam(':customizationNotes', $data['CustomizationNotes']);

            $stmt->bindParam(':orderId', $orderId, PDO::PARAM_INT);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error updating order {$orderId}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Specifically updates the order's status.
     * @param int $orderId The ID of the order.
     * @param int $orderStatusId The new OrderStatusID.
     * @return bool True on success, false on failure.
     */
    public function updateStatus(int $orderId, int $orderStatusId): bool {
        $sql = "UPDATE Orders SET OrderStatusID = :orderStatusId, UpdatedAt = NOW()
                WHERE OrderID = :orderId AND IsDeleted = 0";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':orderStatusId', $orderStatusId, PDO::PARAM_INT);
            $stmt->bindParam(':orderId', $orderId, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error updating order status for {$orderId}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Fetches all available order statuses.
     * @return array An array of order statuses (OrderStatusID, StatusName).
     */
    public function getOrderStatuses(): array {
        try {
            // Assuming a table named OrderStatuses with OrderStatusID and StatusName
            $stmt = $this->db->query("SELECT OrderStatusID, StatusName FROM OrderStatuses ORDER BY SortOrder ASC, StatusName ASC"); // Added SortOrder
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching order statuses: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Soft deletes an order by setting IsDeleted = 1.
     * @param int $orderId The ID of the order to delete.
     * @return bool True on success, false on failure.
     */
    public function delete(int $orderId): bool {
        $sql = "UPDATE Orders SET IsDeleted = 1, UpdatedAt = NOW() WHERE OrderID = :orderId";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':orderId', $orderId, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error soft deleting order {$orderId}: " . $e->getMessage());
            return false;
        }
    }
}
?>
