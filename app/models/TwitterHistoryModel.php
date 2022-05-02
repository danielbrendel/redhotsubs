<?php

/**
 * Class TwitterHistoryModel
 */ 
class TwitterHistoryModel extends \Asatru\Database\Model
{
    /**
     * @param $ident
     * @return bool
     * @throws Exception
     */
    public static function addIfNotAlready($ident)
    {
        try {
            $exists = TwitterHistoryModel::raw('SELECT COUNT(*) AS `count` FROM `' . self::tableName() . '` WHERE ident = ?', [$ident]);
            
            if ($exists->get(0)->get('count') == 0) {
                TwitterHistoryModel::raw('INSERT INTO `' . self::tableName() . '` (ident) VALUES(?)', [$ident]);

                return true;
            }

            return false;
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
        return 'twitterhistory';
    }
}