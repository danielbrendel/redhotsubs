<?php

/**
 * Class UtilsModule
 */
class UtilsModule {
    const COUNT_MILLION = 1000000;
    const COUNT_HUNDREDTHOUSAND = 100000;
    const COUNT_TENTHOUSAND = 10000;
    const COUNT_THOUSAND = 1000;

    /**
     * @param $count
     * @return string
     * @throws Exception
     */
    public static function countAsString($count)
    {
        try {
            if ($count >= self::COUNT_MILLION) {
                return strval(round($count / self::COUNT_MILLION, 1)) . 'M';
            } else if (($count < self::COUNT_MILLION) && ($count >= self::COUNT_HUNDREDTHOUSAND)) {
                return strval(round($count / self::COUNT_THOUSAND, 1)) . 'K';
            } else if (($count < self::COUNT_HUNDREDTHOUSAND) && ($count >= self::COUNT_TENTHOUSAND)) {
                return strval(round($count / self::COUNT_THOUSAND, 1)) . 'K';
            } else if (($count < self::COUNT_TENTHOUSAND) && ($count >= self::COUNT_THOUSAND)) {
                return strval(round($count / self::COUNT_THOUSAND, 1)) . 'K';
            } else {
                return strval($count);
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * @param $url
     * @return int
     * @throws Exception
     */
    public static function getResponseCode($url)
    {
        try {
            $result = get_headers($url);
            return (int)substr($result[0], 9, 3);
        } catch (Exception $e) {
            throw $e;
        }
    }
}
