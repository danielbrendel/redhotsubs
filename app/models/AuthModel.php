<?php

/**
 * Class AuthModel
 */ 
class AuthModel extends \Asatru\Database\Model
{
    /**
     * @return mixed
     */
    public static function getAuthUser()
    {
        try {
            $session = SessionModel::findSession(session_id());
            if (!$session) {
                return null;
            }

            $data = static::raw('SELECT * FROM `' . self::tableName() . '` WHERE id = ?', [$session->get('userId')])->first();
            if (!$data) {
                return null;
            }

            return $data;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * @param $email
     * @param $password
     * @return void
     * @throws \Exception
     */
    public static function login($email, $password)
    {
        try {
            $data = static::raw('SELECT * FROM `' . self::tableName() . '` WHERE email = ?', [$email])->first();
            if (!$data) {
                throw new \Exception('E-Mail address not found: ' . $email);
            }

            if (!password_verify($password, $data->get('password'))) {
                throw new \Exception('The passwords do not match');
            }

            SessionModel::loginSession($data->get('id'), session_id());
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * @return void
     * @throws \Exception
     */
    public static function logout()
    {
        try {
            SessionModel::logoutSession(session_id());
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
            $auth_user = static::getAuthUser();
            if (!$auth_user) {
                throw new \Exception('No authenticated user found');
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