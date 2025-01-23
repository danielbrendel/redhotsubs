<?php

/**
 * Class AuthModel_Migration
 */
class AuthModel_Migration
{
    private $database = null;
    private $connection = null;

    /**
     * Store the PDO connection handle
     * 
     * @param \PDO $pdo The PDO connection handle
     * @return void
     */
    public function __construct($pdo)
    {
        $this->connection = $pdo;
    }

    /**
     * Called when the table shall be created or modified
     * 
     * @return void
     */
    public function up()
    {
        $this->database = new Asatru\Database\Migration('auth', $this->connection);
        $this->database->drop();
        $this->database->add('id INT NOT NULL AUTO_INCREMENT PRIMARY KEY');
        $this->database->add('email VARCHAR(512) NOT NULL');
        $this->database->add('password VARCHAR(1024) NULL');
        $this->database->add('account_confirm VARCHAR(512) NULL');
        $this->database->add('password_reset VARCHAR(512) NULL');
        $this->database->add('privileged BOOLEAN NOT NULL DEFAULT 0');
        $this->database->add('created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP');
        $this->database->create();
    }

    /**
     * Called when the table shall be dropped
     * 
     * @return void
     */
    public function down()
    {
        if ($this->database)
            $this->database->drop();
    }
}