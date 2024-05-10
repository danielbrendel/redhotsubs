<?php

/**
 * Class FavoritesModel
 */ 
class FavoritesModel extends \Asatru\Database\Model
{
    const COUNT_PACKET = 12;

    /**
     * @param $ident
     * @return void
     */
    public static function addFavorite($ident)
    {
        $user = AuthModel::getAuthUser();

        if (FavoritesModel::hasFavorited($ident)) {
            return;
        }

        FavoritesModel::raw('INSERT INTO `' . self::tableName() . '` (userid, ident) VALUES(?, ?)', [
            $user->get('id'),
            $ident
        ]);
    }

    /**
     * @param $paginate
     * @return mixed
     */
    public static function queryFavorites($paginate = null)
    {
        $user = AuthModel::getAuthUser();

        if ($paginate !== null) {
            return FavoritesModel::raw('SELECT * FROM `' . self::tableName() . '` WHERE userid = ? AND id < ? ORDER BY id DESC LIMIT ' . self::COUNT_PACKET, [$user->get('id'), $paginate]);
        } else {
            return FavoritesModel::raw('SELECT * FROM `' . self::tableName() . '` WHERE userid = ? ORDER BY id DESC LIMIT ' . self::COUNT_PACKET, [$user->get('id')]);
        }
    }

    /**
     * @param $ident
     * @return void
     */
    public static function removeFavorite($ident)
    {
        $user = AuthModel::getAuthUser();

        FavoritesModel::raw('DELETE FROM `' . self::tableName() . '` WHERE userid = ? AND ident = ?', [
            $user->get('id'),
            $ident
        ]);
    }

    /**
     * @param $ident
     * @return bool
     */
    public static function hasFavorited($ident)
    {
        $user = AuthModel::getAuthUser();
        
        if (substr($ident, 0, 1) == '/') {
            $ident = substr($ident, 1);
        }

        if (substr($ident, strlen($ident) - 1, 1) == '/') {
            $ident = substr($ident, 0, strlen($ident) - 1);
        }
        
        $result = FavoritesModel::raw('SELECT COUNT(*) AS count FROM `' . self::tableName() . '` WHERE userid = ? AND ident = ?', [
            $user->get('id'),
            $ident
        ])->first();
        
        return intval($result->get('count')) > 0;
    }

    /**
     * @param $user
     * @return mixed
     */
    public static function getAllFavorites($user = null)
    {
        if ($user === null) {
            $user = AuthModel::getAuthUser();
        }

        return FavoritesModel::raw('SELECT * FROM `' . self::tableName() . '` WHERE userid = ? ORDER BY id ASC', [$user->get('id')]);
    }

    /**
     * Return the associated table name of the migration
     * 
     * @return string
     */
    public static function tableName()
    {
        return 'favorites';
    }
}