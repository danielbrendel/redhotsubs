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
        $hash = session_id();

        if (FavoritesModel::hasFavorited($ident)) {
            return;
        }

        FavoritesModel::raw('INSERT INTO `' . self::tableName() . '` (hash, ident) VALUES(?, ?)', [
            $hash,
            $ident
        ]);
    }

    /**
     * @param $paginate
     * @return mixed
     */
    public static function queryFavorites($paginate = null)
    {
        $hash = session_id();

        if ($paginate !== null) {
            return FavoritesModel::raw('SELECT * FROM `' . self::tableName() . '` WHERE hash = ? AND id < ? ORDER BY id DESC LIMIT ' . self::COUNT_PACKET, [$hash, $paginate]);
        } else {
            return FavoritesModel::raw('SELECT * FROM `' . self::tableName() . '` WHERE hash = ? ORDER BY id DESC LIMIT ' . self::COUNT_PACKET, [$hash]);
        }
    }

    /**
     * @param $ident
     * @return void
     */
    public static function removeFavorite($ident)
    {
        $hash = session_id();

        FavoritesModel::raw('DELETE FROM `' . self::tableName() . '` WHERE hash = ? AND ident = ?', [
            $hash,
            $ident
        ]);
    }

    /**
     * @param $ident
     * @return bool
     */
    public static function hasFavorited($ident)
    {
        $hash = session_id();
        
        if (substr($ident, 0, 1) == '/') {
            $ident = substr($ident, 1);
        }

        if (substr($ident, strlen($ident) - 1, 1) == '/') {
            $ident = substr($ident, 0, strlen($ident) - 1);
        }
        
        $result = FavoritesModel::raw('SELECT COUNT(*) AS count FROM `' . self::tableName() . '` WHERE hash = ? AND ident = ?', [
            $hash,
            $ident
        ])->first();
        
        return intval($result->get('count')) > 0;
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