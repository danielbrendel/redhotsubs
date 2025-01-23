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
            $data = FeaturedUserModel::raw('SELECT * FROM `@THIS` AS t1 JOIN (SELECT id FROM `@THIS` WHERE active = 1 ORDER BY RAND() LIMIT ?) as t2 ON t1.id = t2.id', [$count]);
            return $data;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * @return \Asatru\Database\Collection
     * @throws Exception
     */
    public static function getAll()
    {
        try {
            $data = FeaturedUserModel::raw('SELECT * FROM `@THIS` WHERE active = 1');
            return $data;
        } catch (Exception $e) {
            throw $e;
        }
    }
}