<?php

/**
 * Class ErrorSubsModel
 */ 
class ErrorSubsModel extends \Asatru\Database\Model
{
    /**
     * @param $name
     * @param $error
     * @param $reason
     * @return void
     * @throws Exception
     */
    public static function addToTable($name, $error, $reason = null)
    {
        try {
            ErrorSubsModel::raw('INSERT INTO `' . self::tableName() . '` (subname, error, reason) VALUES(?, ?, ?)', [
                $name, $error, $reason
            ]);
        } catch (Exception $e) {
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
        return 'errorsubs';
    }
}