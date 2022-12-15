<?php

/**
 * This class extends the base model class and represents your associated table
 */ 
class FavShareModel extends \Asatru\Database\Model
{
    /**
     * @return string
     * @throws \Exception
     */
    public static function genShare()
    {
        try {
            $session = session_id();
            $token = md5($session . '_' . date('Y-m-d H:i:s') . '_' . uniqid('', true));
            
            $exists = FavShareModel::raw('SELECT COUNT(*) as count FROM `' . self::tableName() . '` WHERE session = ?', [$session])->first();
            if ($exists->get('count') == 0) {
                FavShareModel::raw('INSERT INTO `' . self::tableName() . '` (token, session) VALUES(?, ?)', [
                    $token,
                    $session
                ]);
            } else {
                FavShareModel::raw('UPDATE `' . self::tableName() . '` SET token = ? WHERE session = ?', [
                    $token,
                    $session
                ]);
            }

            return $token;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * @return string
     * @throws \Exception
     */
    public static function getShareToken()
    {
        try {
            $session = session_id();
            
            $data = FavShareModel::raw('SELECT * FROM `' . self::tableName() . '` WHERE session = ?', [$session])->first();
            if ($data) {
                return $data->get('token');
            }

            return '';
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * @param $token
     * @return void
     * @throws \Exception
     */
    public static function migrateFavorites($token)
    {
        try {
            $session = session_id();

            $alreadygen = FavShareModel::raw('SELECT COUNT(*) as count FROM `' . self::tableName() . '` WHERE session = ?', [$session])->first();
            if ((!$alreadygen) || ($alreadygen->get('count') == 0)) {
                FavSHareModel::genShare();
            }

            $sessionData = FavShareModel::raw('SELECT session FROM `' . self::tableName() . '` WHERE token = ?', [$token])->first();
            if ($sessionData) {
                $favs = FavoritesModel::getAllFavorites($sessionData->get('session'));

                foreach ($favs as $fav) {
                    $exists = FavoritesModel::raw('SELECT COUNT(*) as count FROM `' . FavoritesModel::tableName() . '` WHERE hash = ? AND ident = ?', [$session, $fav->get('ident')])->first();
                    if ((!$exists) || ($exists->get('count') == 0)) {
                        FavoritesModel::raw('INSERT INTO `' . FavoritesModel::tableName() . '` (hash, ident) VALUES(?, ?)', [
                            $session,
                            $fav->get('ident')
                        ]);
                    }
                }
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
        return 'favshare';
    }
}