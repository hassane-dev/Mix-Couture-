<?php
// app/models/MeasurementModel.php

namespace App\Models;

use PDO;
use PDOException;

// Assuming Database class from previous examples
// class Database { ... }

class MeasurementModel {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    /**
     * Fetches all measurement sets for a given client.
     * @param int $clientId The ID of the client.
     * @return array An array of measurement sets for the client.
     */
    public function getByClientId(int $clientId): array {
        $sql = "SELECT MeasurementID, ClientID, MeasurementName, DateTaken,
                       Bust, Waist, Hips, ShoulderWidth, SleeveLength, Inseam,
                       CreatedAt, UpdatedAt
                FROM Measurements
                WHERE ClientID = :clientId
                ORDER BY DateTaken DESC, CreatedAt DESC";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':clientId', $clientId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching measurements for client {$clientId}: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Fetches a single measurement set by its ID.
     * @param int $measurementId The ID of the measurement set.
     * @return array|false The measurement set data or false if not found.
     */
    public function findById(int $measurementId): array|false {
        $sql = "SELECT MeasurementID, ClientID, MeasurementName, DateTaken,
                       Bust, Waist, Hips, ShoulderWidth, SleeveLength, Inseam,
                       CreatedAt, UpdatedAt
                FROM Measurements
                WHERE MeasurementID = :measurementId";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':measurementId', $measurementId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching measurement by ID {$measurementId}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Creates a new measurement set.
     * @param array $data Associative array of measurement data.
     *        Expected keys: 'ClientID', 'MeasurementName', 'DateTaken',
     *                       'Bust', 'Waist', 'Hips', 'ShoulderWidth', 'SleeveLength', 'Inseam'.
     * @return int|false The ID of the newly created measurement set or false on failure.
     */
    public function create(array $data): int|false {
        $sql = "INSERT INTO Measurements (ClientID, MeasurementName, DateTaken,
                                      Bust, Waist, Hips, ShoulderWidth, SleeveLength, Inseam,
                                      CreatedAt, UpdatedAt)
                VALUES (:clientId, :measurementName, :dateTaken,
                        :bust, :waist, :hips, :shoulderWidth, :sleeveLength, :inseam,
                        NOW(), NOW())";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':clientId', $data['ClientID'], PDO::PARAM_INT);
            $stmt->bindParam(':measurementName', $data['MeasurementName']);
            $stmt->bindParam(':dateTaken', $data['DateTaken']); // Assuming YYYY-MM-DD format
            $stmt->bindParam(':bust', $data['Bust']);
            $stmt->bindParam(':waist', $data['Waist']);
            $stmt->bindParam(':hips', $data['Hips']);
            $stmt->bindParam(':shoulderWidth', $data['ShoulderWidth']);
            $stmt->bindParam(':sleeveLength', $data['SleeveLength']);
            $stmt->bindParam(':inseam', $data['Inseam']);

            $stmt->execute();
            return (int)$this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log("Error creating measurement set: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Updates an existing measurement set.
     * @param int $measurementId The ID of the measurement set to update.
     * @param array $data Associative array of measurement data. ClientID should not be changed here.
     * @return bool True on success, false on failure.
     */
    public function update(int $measurementId, array $data): bool {
        $sql = "UPDATE Measurements SET
                    MeasurementName = :measurementName,
                    DateTaken = :dateTaken,
                    Bust = :bust,
                    Waist = :waist,
                    Hips = :hips,
                    ShoulderWidth = :shoulderWidth,
                    SleeveLength = :sleeveLength,
                    Inseam = :inseam,
                    UpdatedAt = NOW()
                WHERE MeasurementID = :measurementId";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':measurementName', $data['MeasurementName']);
            $stmt->bindParam(':dateTaken', $data['DateTaken']);
            $stmt->bindParam(':bust', $data['Bust']);
            $stmt->bindParam(':waist', $data['Waist']);
            $stmt->bindParam(':hips', $data['Hips']);
            $stmt->bindParam(':shoulderWidth', $data['ShoulderWidth']);
            $stmt->bindParam(':sleeveLength', $data['SleeveLength']);
            $stmt->bindParam(':inseam', $data['Inseam']);
            $stmt->bindParam(':measurementId', $measurementId, PDO::PARAM_INT);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error updating measurement set {$measurementId}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Deletes a measurement set (hard delete).
     * @param int $measurementId The ID of the measurement set to delete.
     * @return bool True on success, false on failure.
     */
    public function delete(int $measurementId): bool {
        // To implement soft delete, you would add an IsDeleted column to the Measurements table
        // and update it here:
        // $sql = "UPDATE Measurements SET IsDeleted = 1, UpdatedAt = NOW() WHERE MeasurementID = :measurementId";

        $sql = "DELETE FROM Measurements WHERE MeasurementID = :measurementId";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':measurementId', $measurementId, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error deleting measurement set {$measurementId}: " . $e->getMessage());
            return false;
        }
    }
}
?>
