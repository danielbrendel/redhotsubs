<?php

/**
 * Class SubsModel
 */ 
class SubsModel extends \Asatru\Database\Model
{
    /**
     * @return array
     */
    public static function getAllSubs()
    {
        try {
            return SubsModel::raw('SELECT * FROM `' . self::tableName() . '` ORDER BY sub_ident ASC');
        } catch (Exception $e) {
            return array();
        }
    }

    /**
     * Return the associated table name of the migration
     * 
     * @return string
     */
    public static function tableName()
    {
        return 'subs';
    }
}