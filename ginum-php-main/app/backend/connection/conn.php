<?php

// imports
require __DIR__ . '/../../vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();


/**
 * Class DBConnection
 * This class is responsible for managing the database connection.
 */

class DBConnection
{
    /**
     * @var mysqli The mysqli connection object.
     */
    public $conn;

    /**
     * @var string The database host.
     */
    private $db_host;

    /**
     * @var string The database user.
     */
    private $db_user;

    /**
     * @var string The database password.
     */
    private $db_password;

    /**
     * @var string The database name.
     */
    private $db_name;

    /**
     * Constructor method.
     * Initializes the database connection.
     *
     * @return void
     */
    public function __construct()
    {
        // Set database credentials from environment variables
        $this->db_host = $_ENV['DB_HOST'];
        $this->db_user = $_ENV['DB_USER'];
        $this->db_password = $_ENV['DB_PASSWORD'];
        $this->db_name = $_ENV['DB_NAME'];

        // Check if connection is not already established
        if (!isset($this->conn)) {
            // Create a new mysqli connection
            $this->conn = new mysqli(hostname: $this->db_host, username: $this->db_user, password: $this->db_password, database: $this->db_name);
            // Check if connection failed
            if ($this->conn->connect_error) {
                // Terminate the script and display an error message
                die("Connection failed: " . $this->conn->connect_error);
            }
        }
    }

    /**
     * Destructor method.
     * Closes the database connection when the object is destroyed.
     */
    public function __destruct()
    {
        $this->conn->close();
    }
}
