<?php
// core/Database.php

namespace App\Core;

use PDO;
use PDOException;

class Database {
    private static ?PDO $instance = null; // Store the single instance of PDO

    /**
     * Private constructor to prevent direct creation of object.
     * Connects to the database using constants defined in config/config.php.
     */
    private function __construct() {
        // DSN (Data Source Name)
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;

        // PDO options
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Turn on errors in the form of exceptions
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Make the default fetch be an associative array
            PDO::ATTR_EMULATE_PREPARES   => false,                  // Turn off emulation mode for real prepared statements
        ];

        try {
            // Create a new PDO instance
            // Note: The assignment to self::$instance should happen in getInstance()
            // This constructor is just for creating the PDO object that getInstance will manage.
            // However, to make this class strictly a singleton for PDO, the instance variable should be of type Database,
            // and it should store the PDO object in a property. Let's refine this slightly for typical singleton usage.
            // For this iteration, let's make getInstance() directly return the PDO object.
            // The constructor will effectively be called by getInstance() the first time.
            // The `$instance` will store the PDO object.
        } catch (PDOException $e) {
            // Log the error and/or rethrow or handle more gracefully
            // For a web app, you might want to show a generic error page
            error_log("Database Connection Error: " . $e->getMessage());
            // Depending on requirements, you might rethrow, or exit, or show a user-friendly message
            throw new PDOException("Database connection failed. Review server logs for details.", (int)$e->getCode());
        }
    }

    /**
     * Gets the singleton PDO database connection instance.
     *
     * @return PDO The PDO database connection instance.
     * @throws PDOException If the database connection fails.
     */
    public static function getInstance(): PDO {
        if (self::$instance === null) {
            // DSN (Data Source Name)
            $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;

            // PDO options
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            try {
                self::$instance = new PDO($dsn, DB_USER, DB_PASS, $options);
            } catch (PDOException $e) {
                error_log("Database Connection Error in getInstance: " . $e->getMessage());
                // Consider how to handle this in a real application.
                // Maybe throw a custom application exception or display an error page.
                throw new PDOException("Failed to connect to the database. Please check configuration and ensure the database server is running.", (int)$e->getCode());
            }
        }
        return self::$instance;
    }

    /**
     * Private clone method to prevent cloning of the instance.
     */
    private function __clone() {
    }

    /**
     * Private unserialize method to prevent unserializing of the instance.
     */
    public function __wakeup() {
        throw new \Exception("Cannot unserialize a singleton.");
    }
}
