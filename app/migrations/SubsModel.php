<?php

/**
 * Class SubsModel_Migration
 */
class SubsModel_Migration
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
        $this->database = new Asatru\Database\Migration('subs', $this->connection);
        $this->database->drop();
        $this->database->add('id INT NOT NULL AUTO_INCREMENT PRIMARY KEY');
        $this->database->add('sub_ident VARCHAR(512) NOT NULL');
        $this->database->add('category VARCHAR(512) NOT NULL');
        $this->database->add('cat_order INT NOT NULL');
        $this->database->add('cat_video BOOLEAN NOT NULL');
        $this->database->add('featured BOOLEAN NOT NULL DEFAULT 0');
        $this->database->add('twitter_posting BOOLEAN NOT NULL DEFAULT 0');
        $this->database->add('last_check TIMESTAMP NULL');
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