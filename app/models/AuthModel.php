<?php

/**
 * Class AuthModel
 */ 
class AuthModel extends \Asatru\Database\Model
{
    const ACCOUNT_CONFIRMED = '_confirmed';

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

            $data = static::raw('SELECT * FROM `' . self::tableName() . '` WHERE id = ? AND account_confirm = ?', [$session->get('userId'), self::ACCOUNT_CONFIRMED])->first();
            if (!$data) {
                return null;
            }

            return $data;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * @return bool
     */
    public static function isAuthenticated()
    {
        return static::getAuthUser() !== null;
    }

    /**
     * @param $email
     * @param $password
     * @return void
     * @throws \Exception
     */
    public static function register($email, $password)
    {
        try {
            $exists = static::raw('SELECT * FROM `' . self::tableName() . '` WHERE email = ?', [$email])->first();
            if ($exists) {
                throw new \Exception('E-Mail address is already in use.');
            }

            $password = password_hash($password, PASSWORD_BCRYPT);
            $account_confirm = md5($email . date('Y-m-d H:i:s') . random_bytes(55));

            static::raw('INSERT INTO `' . self::tableName() . '` (email, password, account_confirm) VALUES(?, ?, ?)', [
                $email, $password, $account_confirm
            ]);

            $mailobj = new Asatru\SMTPMailer\SMTPMailer();
            $mailobj->setRecipient($email);
            $mailobj->setSubject('[' . env('APP_NAME') . '] Account confirmation');
            $mailobj->setView('mail/mail_confirm', [], ['token' => $account_confirm]);
            $mailobj->setProperties(mail_properties());
            $mailobj->send();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * @param $token
     * @return void
     * @throws \Exception
     */
    public static function confirm($token)
    {
        try {
            $user = static::raw('SELECT * FROM `' . self::tableName() . '` WHERE account_confirm = ?', [$token])->first();
            if (!$user) {
                throw new \Exception('No user associated with the given token.');
            }

            static::raw('UPDATE `' . self::tableName() . '` SET account_confirm = ? WHERE account_confirm = ?', [
                self::ACCOUNT_CONFIRMED, $token
            ]);
        } catch (\Exception $e) {
            throw $e;
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
            $data = static::raw('SELECT * FROM `' . self::tableName() . '` WHERE email = ? AND account_confirm = ?', [$email, self::ACCOUNT_CONFIRMED])->first();
            if (!$data) {
                throw new \Exception('E-Mail address ' . $email . ' not found or account not yet activated');
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
     * @param $email
     * @return void
     * @throws \Exception
     */
    public static function recoverPassword($email)
    {
        try {
            $user = static::raw('SELECT * FROM `' . self::tableName() . '` WHERE email = ? AND account_confirm = ?', [$email, self::ACCOUNT_CONFIRMED])->first();
            if (!$user) {
                throw new \Exception('User not found or not activated');
            }

            $password_reset = md5($email . date('Y-m-d H:i:s') . random_bytes(55));

            static::raw('UPDATE `' . self::tableName() . '` SET password_reset = ? WHERE email = ?', [
                $password_reset, $email
            ]);

            $mailobj = new Asatru\SMTPMailer\SMTPMailer();
            $mailobj->setRecipient($email);
            $mailobj->setSubject('[' . env('APP_NAME') . '] Password reset');
            $mailobj->setView('mail/mail_reset', [], ['token' => $password_reset]);
            $mailobj->setProperties(mail_properties());
            $mailobj->send();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * @param $token
     * @param $password
     * @return void
     * @throws \Exception
     */
    public static function resetPassword($token, $password)
    {
        try {
            $user = static::raw('SELECT * FROM `' . self::tableName() . '` WHERE password_reset = ? AND account_confirm = ?', [$token, self::ACCOUNT_CONFIRMED])->first();
            if (!$user) {
                throw new \Exception('User not found or not activated');
            }

            $password = password_hash($password, PASSWORD_BCRYPT);

            static::raw('UPDATE `' . self::tableName() . '` SET password = ?, password_reset = NULL WHERE password_reset = ?', [
                $password, $token
            ]);
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
     * @param $email
     * @param $password
     * @return void
     * @throws \Exception
     */
    public static function updateSettings($email, $password = null, $password_confirmation = null)
    {
        try {
            $user = static::getAuthUser();

            static::raw('UPDATE `' . self::tableName() . '` SET email = ? WHERE id = ?', [$email, $user->get('id')]);
            
            if ($password) {
				if ($password !== $password_confirmation) {
					throw new \Exception('The passwords do not match');
				}

                $password = password_hash($password, PASSWORD_BCRYPT);

				static::raw('UPDATE `' . self::tableName() . '` SET password = ? WHERE id = ?', [$password, $user->get('id')]);
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