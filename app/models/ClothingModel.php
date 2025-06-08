<?php
// app/models/ClothingModel.php

namespace App\Models;

use PDO;
use PDOException;

// Assuming Database class from previous Client model example
// class Database { ... }

class ClothingModel {
    private PDO $db;

    public function __construct() {
        // In a real application, inject PDO instance, e.g., via a service container or base model.
        $this->db = Database::getConnection(); // Using the placeholder from Client model example
    }

    /**
     * Fetches all active clothing models.
     * @return array An array of all active clothing models with their category names.
     */
    public function getAll(): array {
        $sql = "SELECT m.ModelID, m.Name, m.Description, m.BasePrice, m.PhotoURL, m.IsDeleted,
                       m.CreatedAt, m.UpdatedAt, mc.CategoryName
                FROM Models m
                LEFT JOIN ModelCategories mc ON m.CategoryID = mc.CategoryID
                WHERE m.IsDeleted = 0
                ORDER BY m.Name ASC";
        try {
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching all clothing models: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Fetches a single active clothing model by its ID.
     * @param int $modelId The ID of the model.
     * @return array|false The model data or false if not found or deleted.
     */
    public function findById(int $modelId): array|false {
        $sql = "SELECT m.ModelID, m.Name, m.Description, m.CategoryID, m.BasePrice, m.PhotoURL,
                       m.IsDeleted, m.CreatedAt, m.UpdatedAt, mc.CategoryName
                FROM Models m
                LEFT JOIN ModelCategories mc ON m.CategoryID = mc.CategoryID
                WHERE m.ModelID = :modelId AND m.IsDeleted = 0";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':modelId', $modelId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching model by ID {$modelId}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Creates a new clothing model.
     * @param array $data Associative array of model data.
     *        Expected keys: 'Name', 'Description', 'CategoryID', 'BasePrice', 'PhotoURL'.
     * @return int|false The ID of the newly created model or false on failure.
     */
    public function create(array $data): int|false {
        $sql = "INSERT INTO Models (Name, Description, CategoryID, BasePrice, PhotoURL, IsDeleted, CreatedAt, UpdatedAt)
                VALUES (:name, :description, :categoryId, :basePrice, :photoUrl, 0, NOW(), NOW())";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':name', $data['Name']);
            $stmt->bindParam(':description', $data['Description']);
            $stmt->bindParam(':categoryId', $data['CategoryID'], PDO::PARAM_INT);
            $stmt->bindParam(':basePrice', $data['BasePrice']);
            $stmt->bindParam(':photoUrl', $data['PhotoURL']);
            $stmt->execute();
            return (int)$this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log("Error creating clothing model: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Updates an existing clothing model.
     * @param int $modelId The ID of the model to update.
     * @param array $data Associative array of model data.
     *        Allowed keys: 'Name', 'Description', 'CategoryID', 'BasePrice', 'PhotoURL'.
     * @return bool True on success, false on failure.
     */
    public function update(int $modelId, array $data): bool {
        // Build the SQL query dynamically based on provided data
        $fields = [];
        if (isset($data['Name'])) $fields['Name'] = ':name';
        if (isset($data['Description'])) $fields['Description'] = ':description';
        if (isset($data['CategoryID'])) $fields['CategoryID'] = ':categoryId';
        if (isset($data['BasePrice'])) $fields['BasePrice'] = ':basePrice';
        if (isset($data['PhotoURL'])) $fields['PhotoURL'] = ':photoUrl';

        if (empty($fields)) {
            return false; // No fields to update
        }

        $setClauses = [];
        foreach (array_keys($fields) as $field) {
            $setClauses[] = "{$field} = {$fields[$field]}";
        }
        $setClauses[] = "UpdatedAt = NOW()";

        $sql = "UPDATE Models SET " . implode(', ', $setClauses) . " WHERE ModelID = :modelId AND IsDeleted = 0";

        try {
            $stmt = $this->db->prepare($sql);

            if (isset($data['Name'])) $stmt->bindParam(':name', $data['Name']);
            if (isset($data['Description'])) $stmt->bindParam(':description', $data['Description']);
            if (isset($data['CategoryID'])) $stmt->bindParam(':categoryId', $data['CategoryID'], PDO::PARAM_INT);
            if (isset($data['BasePrice'])) $stmt->bindParam(':basePrice', $data['BasePrice']);
            if (isset($data['PhotoURL'])) $stmt->bindParam(':photoUrl', $data['PhotoURL']);

            $stmt->bindParam(':modelId', $modelId, PDO::PARAM_INT);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error updating clothing model {$modelId}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Soft deletes a clothing model by setting IsDeleted = 1.
     * @param int $modelId The ID of the model to delete.
     * @return bool True on success, false on failure.
     */
    public function delete(int $modelId): bool {
        $sql = "UPDATE Models SET IsDeleted = 1, UpdatedAt = NOW() WHERE ModelID = :modelId";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':modelId', $modelId, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error soft deleting clothing model {$modelId}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Fetches all model categories.
     * @return array An array of all categories (CategoryID, CategoryName).
     */
    public function getCategories(): array {
        try {
            // Assuming a table named ModelCategories with CategoryID and CategoryName
            $stmt = $this->db->query("SELECT CategoryID, CategoryName FROM ModelCategories ORDER BY CategoryName ASC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching model categories: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Fetches the current PhotoURL for a model.
     * Useful for deleting an old photo when a new one is uploaded.
     * @param int $modelId
     * @return string|false
     */
    public function getPhotoUrl(int $modelId): string|false {
        try {
            $stmt = $this->db->prepare("SELECT PhotoURL FROM Models WHERE ModelID = :modelId AND IsDeleted = 0");
            $stmt->bindParam(':modelId', $modelId, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ? $result['PhotoURL'] : false;
        } catch (PDOException $e) {
            error_log("Error fetching photo URL for model {$modelId}: " . $e->getMessage());
            return false;
        }
    }
}
?>
