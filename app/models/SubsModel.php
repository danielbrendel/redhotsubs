<?php

/**
 * Class SubsModel
 */ 
class SubsModel extends \Asatru\Database\Model
{
    const SUB_FEATURED =  true;
    const SUB_UNFEATURED = false;

    /**
     * @return mixed
     */
    public static function getAllSubs()
    {
        try {
            return SubsModel::raw('SELECT * FROM `' . self::tableName() . '` ORDER BY cat_order, sub_ident ASC');
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * @param bool $which
     * @return mixed
     */
    public static function  getFeatureSubs($which)
    {
        try {
            return SubsModel::raw('SELECT * FROM `' . self::tableName() . '` WHERE featured = ? ORDER BY sub_ident ASC', [$which]);
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * @return mixed
     */
    public static function getSubsForTwitter()
    {
        try {
            return SubsModel::raw('SELECT * FROM `' . self::tableName() . '` WHERE twitter_posting = 1 ORDER BY sub_ident ASC');
        } catch (Exception $e) {
            return null;
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
     * @param $ident
     * @return mixed
     * @throws Exception
     */
    public static function getSubData($ident)
    {
        try {
            $result = SubsModel::raw('SELECT * FROM `' . self::tableName() . '` WHERE sub_ident = ?', [$ident]);
            return $result;
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
        return 'subs';
    }
}