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
        $exists = ViewCountModel::where('token', '=', $token)->count()->get();

        if ($exists === 0) {
            ViewCountModel::raw('INSERT INTO `' . self::tableName() . '` (token) VALUES(?)', [$token]);
        }

        return ViewCountModel::count()->get();
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