<?php

/**
 * Class TwitterHistoryModel
 */ 
class TwitterHistoryModel extends \Asatru\Database\Model
{
    /**
     * @param $ident
     * @return bool
     * @throws Exception
     */
    public static function addIfNotAlready($ident, $sub, $permalink, $title)
    {
        try {
            $exists = TwitterHistoryModel::raw('SELECT COUNT(*) AS `count` FROM `@THIS` WHERE ident = ?', [$ident]);
            
            if ($exists->get(0)->get('count') == 0) {
                TwitterHistoryModel::raw('INSERT INTO `@THIS` (ident, sub, permalink, title) VALUES(?, ?, ?, ?)', [$ident, $sub, $permalink, $title]);

                return true;
            }

            return false;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * @param $ident
     * @param $sub
     * @return mixed
     * @throws Exception
     */
    public static function getByIdentAndSub($ident, $sub)
    {
        try {
            $result = TwitterHistoryModel::raw('SELECT * FROM `@THIS` WHERE ident = ? AND sub = ?', [$ident, $sub]);
            return $result;
        } catch (Exception $e) {
            throw $e;
        }
    }
}