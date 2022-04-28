<?php

use RFCrawler\RFCrawler;

/**
 * Class CrawlerModule
 */
class CrawlerModule
{
    /**
     * @param $type
     * @return mixed
     */
    public static function fetchType($type)
    {
        $types = [
            'top' => RFCrawler::FETCH_TYPE_TOP,
            'hot' => RFCrawler::FETCH_TYPE_HOT,
            'new' => RFCrawler::FETCH_TYPE_NEW
        ];

        if (isset($types[$type])) {
            return $types[$type];
        }

        return null;
    }

    /**
     * @param $sub
     * @param $sorting
     * @param $after
     * @param $exclude
     * @param $include
     * @return mixed
     */
    public static function fetchContent($sub, $sorting, $after, $exclude, $include)
    {
        try {
            $ft = static::fetchType($sorting);
            if ($ft === null) {
                throw new Exception('Invalid sorting type: ' . $sorting);
            }

            $crawler = new RFCrawler($sub, env('APP_USERAGENT'));
            $content = $crawler->fetchFromJson($sorting, $after, $exclude, $include);
            
            return $content;
        } catch (Exception $e) {
            throw $e;
        }
    }
}
