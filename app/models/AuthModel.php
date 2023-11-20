<?php

/**
 * Class AuthModel
 */ 
class AuthModel extends \Asatru\Database\Model
{
    /**
     * @param $token
     * @return void
     * @throws \Exception
     */
    public static function activate($token)
    {
        try {
            $data = AuthModel::raw('SELECT * FROM `' . self::tableName() . '` WHERE token = ? AND session IS NULL LIMIT 1', [$token])->first();
            if (!$data) {
                throw new \Exception('Token not found or already in use');
            }
            
            $session = session_id();
            
            AuthModel::raw('UPDATE `' . self::tableName() . '` SET session = ? WHERE id = ?', [$session, $data->get('id')]);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * @return void
     * @throws \Exception
     */
    public static function verify()
    {
        try {
            $session = session_id();

            $data = AuthModel::raw('SELECT * FROM `' . self::tableName() . '` WHERE session = ? LIMIT 1', [$session])->first();
            if ((!$data) || ($data->get('session') !== $session)) {
                throw new \Exception('Authentication failed. Please confirm that your token is valid.');
            }
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
        return 'auth';
    }
}