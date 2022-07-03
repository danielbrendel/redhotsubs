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
        } else {
            ViewCountModel::raw('UPDATE `' . self::tableName() . '` SET updated_at = CURRENT_TIMESTAMP WHERE token = ?', [$token]);
        }

        $count = ViewCountModel::raw('SELECT COUNT(*) FROM `' . self::tableName() . '`');
        
        return $count->get(0)->get(0);
    }

    /**
     * @param $start
     * @param $end
     * @return array
     */
    public static function getVisitsPerDay($start, $end)
    {
        $new_visits = ViewCountModel::raw('SELECT DATE(created_at) AS created_at, COUNT(token) AS count FROM `' . self::tableName() . '` WHERE DATE(created_at) >= ? AND DATE(created_at) <= ? AND DATE(created_at) = DATE(updated_at) GROUP BY DATE(created_at) ORDER BY created_at ASC', [$start, $end]);
        $recurring_visits = ViewCountModel::raw('SELECT DATE(updated_at) AS updated_at, COUNT(token) AS count FROM `' . self::tableName() . '` WHERE DATE(updated_at) >= ? AND DATE(updated_at) <= ? AND DATE(updated_at) <> DATE(created_at) GROUP BY DATE(updated_at) ORDER BY updated_at ASC', [$start, $end]);
        $total_visits = ViewCountModel::raw('SELECT DATE(updated_at) AS updated_at, COUNT(token) AS count FROM `' . self::tableName() . '` WHERE DATE(updated_at) >= ? AND DATE(updated_at) <= ? GROUP BY DATE(updated_at) ORDER BY updated_at ASC', [$start, $end]);

        return [
            'new' => $new_visits,
            'recurring' => $recurring_visits,
            'total' => $total_visits
        ];
    }

    /**
     * @param $minute_limit
     * @return int
     */
    public static function getOnlineCount($minute_limit = '30')
    {
        $date_limit = date('Y-m-d H:i:s', strtotime('-' . $minute_limit . ' minutes'));
        $data = ViewCountModel::raw('SELECT COUNT(*) AS count FROM `' . self::tableName() . '` WHERE updated_at >= ?', [$date_limit]);

        return $data->get(0)->get('count');
    }

    /**
     * @return string
     */
    public static function getInitialStartDate()
    {
        $data = ViewCountModel::raw('SELECT created_at FROM `' . self::tableName() . '` WHERE id = 1');
        return $data->get(0)->get('created_at');
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