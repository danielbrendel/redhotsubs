<?php

use RFCrawler\RFCrawler;

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
            $curl = curl_init();

            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_USERAGENT, env('APP_USERAGENT'));
            curl_setopt($curl, CURLOPT_HEADER, 1);
            curl_setopt($curl, CURLOPT_NOBODY, 1);

            $response = curl_exec($curl);
            $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

            curl_close($curl);
            
            return $httpcode;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * @param $url
     * @return mixed
     * @throws Exception
     */
    public static function getRemoteContents($url)
    {
        try {
            $curl = curl_init();

            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_USERAGENT, env('APP_USERAGENT'));

            $response = curl_exec($curl);

            curl_close($curl);
            
            return $response;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * @param $username
     * @return mixed
     * @throws Exception
     */
    public static function userValid($username)
    {
        try {
            $response_code = UtilsModule::getResponseCode(RFCrawler::URL_REDDIT . '/user/' . $username . '/about/.json');
            
            if ($response_code != 200) {
                return false;
            }

            if (UserBlacklistModel::listed($username)) {
                return false;
            }

            return true;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * @param $items
     * @return array
     * @throws Exception
     */
    public static function filterDuplicates($items)
    {
        try {
            $duplicates = [];

            foreach ($items as $key => &$item) {
                if (isset($item->all->thumbnail)) {
                    $hash = md5(file_get_contents($item->all->thumbnail));

                    if ($hash !== false) {
                        if (!in_array($hash, $duplicates)) {
                            $duplicates[] = $hash;
                        } else {
                            unset($items[$key]);
                            continue;
                        }
                    }
                }
            }

            return $items;
        } catch (Exception $e) {
            throw $e;
        }
    }
}
