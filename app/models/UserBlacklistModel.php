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
            $item = UserBlacklistModel::raw('SELECT COUNT(*) as count FROM `@THIS` WHERE username = ?', [$username])->first();
            if ($item) {
                return $item->get('count') > 0;
            }

            return false;
        } catch (\Exception $e) {
            throw $e;
        }
    }
}