<?php

/**
 * Class UserBlacklistModel
 */ 
class UserBlacklistModel extends \Asatru\Database\Model {
    /**
     * @param $username
     * @return bool
     * @throws \Exception
     */
    public static function listed($username)
    {
        try {
            $item = UserBlacklistModel::raw('SELECT COUNT(*) as count FROM `' . self::tableName() . '` WHERE username = ?', [$username])->first();
            if ($item) {
                return $item->get('count') > 0;
            }

            return false;
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
        return 'userblacklist';
    }
}