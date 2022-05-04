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
     * @param $sub
     * @return bool
     */
    public static function subExists($sub)
    {
        try {
            $result = SubsModel::raw('SELECT COUNT(*) AS count FROM `' . self::tableName() . '` WHERE sub_ident = ?', [$sub]);
            return $result->get(0)->get('count') > 0;
        } catch (Exception $e) {
            return false;
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