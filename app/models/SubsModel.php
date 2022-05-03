<?php

/**
 * Class SubsModel
 */ 
class SubsModel extends \Asatru\Database\Model
{
    const SUB_FEATURED =  true;
    const SUB_UNFEATURED = false;

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
     * @param bool $which
     * @return array
     */
    public static function  getFeatureSubs($which)
    {
        try {
            return SubsModel::raw('SELECT * FROM `' . self::tableName() . '` WHERE featured = ? ORDER BY sub_ident ASC', [$which]);
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