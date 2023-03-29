<?php

/**
 * Class CaptchaModel
 */ 
class CaptchaModel extends \Asatru\Database\Model
{
    /**
     * @param string $hash
     * @return mixed
     * @throws \Exception
     */
    public static function querySum($hash)
    {
        try {
            $result = CaptchaModel::raw('SELECT * FROM `' . self::tableName() . '` WHERE hash = ?', [$hash])->first();
            if (!$result)
                return false;

            return $result->get('sum');
        } catch (\Exception $e) {
            throw  $e;
        }
    }

    /**
     * @param string $hash
     * @return array
     * @throws \Exception
     */
    public static function createSum($hash)
    {
        try {
            $result = [
                rand(0, 10),
                rand(0, 10)
            ];

            $entry = CaptchaModel::raw('SELECT * FROM `' . self::tableName() . '` WHERE hash = ?', [$hash])->first();
            if (!$entry) {
                CaptchaModel::raw('INSERT INTO `' . self::tableName() . '` (hash, sum) VALUES(?, ?)', [$hash, strval($result[0] + $result[1])]);
            } else {
                CaptchaModel::raw('UPDATE `' . self::tableName() . '` SET sum = ? WHERE hash = ?', [strval($result[0] + $result[1]), $hash]);
            }

            return $result;
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
        return 'captcha';
    }
}