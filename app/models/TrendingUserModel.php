<?php

/**
 * This class extends the base model class and represents your associated table
 */ 
class TrendingUserModel extends \Asatru\Database\Model
{
    /**
     * Add to view count
     * 
     * @param $user
     * @return void
     * @throws \Exception
     */
    public static function addViewCount($user)
    {
        try {
            $token = md5($_SERVER['REMOTE_ADDR']);

            if (strpos($user, '/') !== false) {
                $user = substr($user, strpos($user, '/') + 1);
            }

            $exists = TrendingUserModel::raw('SELECT COUNT(*) as count FROM `' . self::tableName() . '` WHERE token = ? AND username = ? AND DATE(created_at) = CURDATE()', [
                $token,
                $user
            ])->first();
            
            if ($exists->get('count') == 0) {
                TrendingUserModel::raw('INSERT INTO `' . self::tableName() . '` (username, token, created_at) VALUES(?, ?, CURRENT_TIMESTAMP)', [
                    $user, $token
                ]);
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get trending users from date up to now
     * 
     * @param $fromDate
     * @return mixed
     * @throws \Exception
     */
    public static function getTrendingUsers($fromDate = null, $limit = 3)
    {
        try {
            if ($fromDate === null) {
                $fromDate = date('Y-m-d', strtotime('-1 week'));
            }

            $rows = TrendingUserModel::raw('SELECT COUNT(username) AS count, username FROM `' . self::tableName() . '` WHERE DATE(created_at) > ? GROUP BY username ORDER BY count DESC LIMIT ' . $limit, [
                $fromDate
            ]);

            return $rows;
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
        return 'trendinguser';
    }
}