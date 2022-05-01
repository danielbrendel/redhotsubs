<?php

/**
 * Class ViewCountModel
 */ 
class ViewCountModel extends \Asatru\Database\Model
{
    /**
     * @param $addr
     * @return int
     */
    public static function acquireCount($addr)
    {
        $token = md5($addr);
        $exists = ViewCountModel::raw('SELECT COUNT(*) FROM `' . self::tableName() . '` WHERE token = ?', [$token]);
        
        if ($exists->get(0)->get(0) == 0) {
            ViewCountModel::raw('INSERT INTO `' . self::tableName() . '` (token) VALUES(?)', [$token]);
        }

        $count = ViewCountModel::raw('SELECT COUNT(*) FROM `' . self::tableName() . '`');
        
        return $count->get(0)->get(0);
    }

    /**
     * Return the associated table name of the migration
     * 
     * @return string
     */
    public static function tableName()
    {
        return 'viewcount';
    }
}