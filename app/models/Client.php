<?php
// app/models/Client.php

namespace App\Models;

use PDO;
use PDOException;

// Placeholder for database connection.
// In a real app, this would be part of your core framework or a dedicated DB class.
class Database {
    private static ?PDO $instance = null;

    public static function getConnection(): PDO {
        if (self::$instance === null) {
            $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];
            try {
                self::$instance = new PDO($dsn, DB_USER, DB_PASS, $options);
            } catch (PDOException $e) {
                // Log error and rethrow or handle gracefully
                error_log("Database Connection Error: " . $e->getMessage());
                throw new PDOException("Database connection failed. Please try again later.", (int)$e->getCode());
            }
        }
        return self::$instance;
    }
}


class Client {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection(); // Use the placeholder DB connection
    }

    /**
     * Fetches all clients.
     * @return array An array of all clients.
     */
    public function getAll(): array {
        try {
            $stmt = $this->db->query("SELECT ClientID, FullName, PhoneNumber, Email, Address, CreatedAt, UpdatedAt FROM Clients WHERE IsDeleted = 0 ORDER BY FullName ASC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching all clients: " . $e->getMessage());
            return []; // Return empty array on error
        }
    }

    /**
     * Fetches a single client by their ID.
     * @param int $clientId The ID of the client.
     * @return array|false The client data or false if not found.
     */
    public function findById(int $clientId): array|false {
        try {
            $stmt = $this->db->prepare("SELECT ClientID, FullName, PhoneNumber, Email, Address, CreatedAt, UpdatedAt FROM Clients WHERE ClientID = :clientId AND IsDeleted = 0");
            $stmt->bindParam(':clientId', $clientId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching client by ID {$clientId}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Creates a new client.
     * @param array $data Associative array of client data (FullName, Email, PhoneNumber, Address).
     * @return int|false The ID of the newly created client or false on failure.
     */
    public function create(array $data): int|false {
        $sql = "INSERT INTO Clients (FullName, Email, PhoneNumber, Address, CreatedAt, UpdatedAt, IsDeleted)
                VALUES (:fullName, :email, :phoneNumber, :address, NOW(), NOW(), 0)";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':fullName', $data['FullName']);
            $stmt->bindParam(':email', $data['Email']);
            $stmt->bindParam(':phoneNumber', $data['PhoneNumber']);
            $stmt->bindParam(':address', $data['Address']);
            $stmt->execute();
            return (int)$this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log("Error creating client: " . $e->getMessage());
            // Check for duplicate email if a unique constraint exists
            if (str_contains($e->getMessage(), 'Duplicate entry') && str_contains($e->getMessage(), 'Email')) {
                // Handle duplicate email specifically if needed, e.g., throw custom exception
            }
            return false;
        }
    }

    /**
     * Updates an existing client.
     * @param int $clientId The ID of the client to update.
     * @param array $data Associative array of client data.
     * @return bool True on success, false on failure.
     */
    public function update(int $clientId, array $data): bool {
        $sql = "UPDATE Clients SET
                FullName = :fullName,
                Email = :email,
                PhoneNumber = :phoneNumber,
                Address = :address,
                UpdatedAt = NOW()
                WHERE ClientID = :clientId AND IsDeleted = 0";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':fullName', $data['FullName']);
            $stmt->bindParam(':email', $data['Email']);
            $stmt->bindParam(':phoneNumber', $data['PhoneNumber']);
            $stmt->bindParam(':address', $data['Address']);
            $stmt->bindParam(':clientId', $clientId, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error updating client {$clientId}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Deletes a client (soft delete by setting IsDeleted = 1).
     * @param int $clientId The ID of the client to delete.
     * @return bool True on success, false on failure.
     */
    public function delete(int $clientId): bool {
        // Soft delete
        $sql = "UPDATE Clients SET IsDeleted = 1, UpdatedAt = NOW() WHERE ClientID = :clientId";
        // For hard delete, use: $sql = "DELETE FROM Clients WHERE ClientID = :clientId";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':clientId', $clientId, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error deleting client {$clientId}: " . $e->getMessage());
            return false;
        }
    }
}
?>
