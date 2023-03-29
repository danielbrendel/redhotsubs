<?php

/**
 * Class ContactModel
 */ 
class ContactModel extends \Asatru\Database\Model
{
    /**
     * @param $name
     * @param $email
     * @param $subject
     * @param $content
     * @return void
     * @throws \Exception
     */
    public static function addEntry($name, $email, $subject, $content)
    {
        try {
            ContactModel::raw('INSERT INTO `' . self::tableName() . '` (name, email, subject, content) VALUES(?, ?, ?, ?)', [
                $name, $email, $subject, $content
            ]);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Return the associated table name of the migration
     * 
     * @return string
     */
    public static function tableName()
    {
        return 'contact';
    }
}