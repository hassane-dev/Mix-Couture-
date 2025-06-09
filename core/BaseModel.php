<?php
// core/BaseModel.php

namespace App\Core;

use PDO; // Required to type-hint the $db property

/**
 * Class BaseModel
 * Serves as a base for all model classes.
 * It handles the database connection.
 */
abstract class BaseModel {
    protected PDO $db; // PDO database connection object

    /**
     * BaseModel constructor.
     * Gets the PDO instance from the Database class and stores it in the $db property.
     */
    public function __construct() {
        $this->db = Database::getInstance();
    }

    // Future common model methods (CRUD helpers) could be added here.
    // For example:
    // abstract public function getAll(): array;
    // abstract public function findById(int $id): ?array; // or object
    // public function delete(int $id): bool { ... }
}
