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
     * @param $start
     * @param $end
     * @return mixed
     */
    public static function getVisitsPerDay($start, $end)
    {
        $data = ViewCountModel::raw('SELECT DATE(created_at), COUNT(token) FROM `' . self::tableName() . '` WHERE DATE(created_at) >= ? AND DATE(created_at) <= ? GROUP BY DATE(created_at)', [$start, $end]);
        if ($data->count() === 0) {
            return null;
        }

        return $data;
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