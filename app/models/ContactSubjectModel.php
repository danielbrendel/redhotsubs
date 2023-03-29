<?php

/**
 * Class ContactSubjectModel
 */ 
class ContactSubjectModel extends \Asatru\Database\Model
{
    /**
     * @return mixed
     * @throws \Exception
     */
    public static function getAll()
    {
        try {
            return ContactSubjectModel::raw('SELECT * FROM `' . self::tableName() . '`');
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
        return 'contactsubject';
    }
}