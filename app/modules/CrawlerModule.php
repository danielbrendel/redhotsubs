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
            'new' => RFCrawler::FETCH_TYPE_NEW,
            'ignore' => RFCrawler::FETCH_TYPE_IGNORE
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
    public static function fetchContent($sub, $sorting, $after, $exclude, $include, $sortStyle = 'url')
    {
        try {
            $ft = static::fetchType($sorting);
            if ($ft === null) {
                throw new Exception('Invalid sorting type: ' . $sorting);
            }

            $args = [];
            
            if ($sortStyle === 'param') {
                if ($sorting !== '') {
                    $args['sort'] = $sorting;
                }
            }

            if ($after !== '') {
                $args['after'] = $after;
            }

            $crawler = new RFCrawler($sub, env('APP_USERAGENT'), $args);
            $content = [];

            if ($sortStyle === 'url') {
                $content = $crawler->fetchFromJson($ft, $exclude, $include);
            } else if ($sortStyle === 'param') {
                $content = $crawler->fetchFromJson(RFCrawler::FETCH_TYPE_IGNORE, $exclude, $include);
            } else {
                throw new Exception('Invalid sorting style: ' . $sortStyle);
            }
            
            return $content;
        } catch (Exception $e) {
            throw $e;
        }
    }
}
