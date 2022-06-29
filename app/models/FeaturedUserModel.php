<?php

/**
 * Class FeaturedUserModel
 */ 
class FeaturedUserModel extends \Asatru\Database\Model
{
    /**
     * @param $count
     * @return \Asatru\Database\Collection
     * @throws Exception
     */
    public static function getSelection($count)
    {
        try {
            $data = FeaturedUserModel::raw('SELECT * FROM `' . self::tableName() . '` AS t1 JOIN (SELECT id FROM `' . self::tableName() . '` WHERE active = 1 ORDER BY RAND() LIMIT ?) as t2 ON t1.id = t2.id', [$count]);
            return $data;
        } catch (Exception $e) {
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
        return 'featureduser';
    }
}