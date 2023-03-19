<?php

/**
 * Class AppSettingsModel_Migration
 */
class AppSettingsModel_Migration
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
        $this->database = new Asatru\Database\Migration('appsettings', $this->connection);
        $this->database->drop();
        $this->database->add('id INT NOT NULL AUTO_INCREMENT PRIMARY KEY');
        $this->database->add('imprint TEXT NOT NULL');
        $this->database->add('privacy TEXT NOT NULL');
        $this->database->add('app TEXT NOT NULL');
        $this->database->add('about TEXT NOT NULL');
        $this->database->add('age_consent TEXT NOT NULL');
        $this->database->add('info TEXT NOT NULL');
        $this->database->add('info_style VARCHAR(250) NOT NULL');
        $this->database->add('head_code TEXT NOT NULL');
        $this->database->add('categories VARCHAR(250) NOT NULL');
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