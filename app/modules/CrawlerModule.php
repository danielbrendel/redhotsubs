<?php

/** Require Reddit crawler helper component */
require_once app_path('/helper/RFCrawler.php');

/**
 * Class CrawlerModule
 */
class CrawlerModule
{
    const CACHE_DURATION = 60 * 30;

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

            $crawler = new RFCrawler($sub, env('APP_USERAGENT'), $args, [
                'user' => env('REDDIT_CLIENT_ID'),
                'password' => env('REDDIT_CLIENT_SECRET')
            ]);
            $content = [];

            if ($sortStyle === 'url') {
                $content = $crawler->fetchPost($ft, $exclude, $include);
            } else if ($sortStyle === 'param') {
                $content = $crawler->fetchPost(RFCrawler::FETCH_TYPE_IGNORE, $exclude, $include);
            } else {
                throw new Exception('Invalid sorting style: ' . $sortStyle);
            }
            
            return $content;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * @param array $cats
     * @return mixed
     */
    public static function queryRandomVideo(array $cats)
    {
        try {
            foreach ($cats as $key => $value) {
                $cats[$key] = strtolower($value);
            }

            $sub = SubsModel::getRandomFromVideoCategories($cats);
            
            $crawler = new RFCrawler($sub->get(0)->get('sub_ident') . '/', env('APP_USERAGENT'), [], [
                'user' => env('REDDIT_CLIENT_ID'),
                'password' => env('REDDIT_CLIENT_SECRET')
            ]);
            $content = [];

            $content = $crawler->fetchPost(RFCrawler::FETCH_TYPE_HOT, array('reddit.com/gallery/', 'https://www.reddit.com/r/', 'i.redd.it', 'i.imgur.com', 'external-preview.redd.it', '.gifv', 'v.reddit.com', 'v.redd.it', ), array('redgifs'));

            $attempts = 0;
            $retitem = null;

            while ($attempts < 10) {
                $retitem = $content[rand(0, count($content) - 1)];
                if ((isset($retitem->author)) && (UserBlacklistModel::listed($retitem->author))) {
                    $attempts++;
                } else {
                    break;
                }
            }
   
            return $retitem;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * @param $user
     * @return bool
     * @throws Exception
     */
    public static function userExists($user)
    {
        try {
            if (strpos($user, '/') !== false) {
                $user = substr($user, strpos($user, '/') + 1);
            }

            if (UtilsModule::getResponseCode(RFCrawler::URL_REDDIT . '/user/' . $user . '/about/.json') != 200) {
                return false;
            }

            $crawler = new RFCrawler('user/' . $user . '/about/.json', env('APP_USERAGENT'), [], [
                'user' => env('REDDIT_CLIENT_ID'),
                'password' => env('REDDIT_CLIENT_SECRET')
            ]);

            $data = $crawler->fetchUrl();

            if ($data) {
                if ((isset($data->data->name)) && ($data->data->name === $user) && (isset($data->data->total_karma)) && ($data->data->total_karma >= env('APP_TRENDINGUSERMINKARMA')) && ((!isset($data->data->is_suspended)) || (!$data->data->is_suspended))) {
                    return true;
                }
            }

            return false;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * @param $sub
     * @return mixed
     * @throws Exception
     */
    public static function getSubStatus($sub)
    {
        try {
            $crawler = new RFCrawler('r/' . $sub . '/about/.json', env('APP_USERAGENT'), [], [
                'user' => env('REDDIT_CLIENT_ID'),
                'password' => env('REDDIT_CLIENT_SECRET')
            ]);

            $data = $crawler->fetchUrl();

            return $data;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * @param $dest
     * @return string
     * @throws Exception
     */
    public static function queryThumbnail($dest)
    {
        try {
            $image = CacheModel::remember($dest . '_thumbnail', env('APP_CACHEDURATION', self::CACHE_DURATION), function() use ($dest) {
                $content = CrawlerModule::fetchContent($dest . '/', 'hot', '', array('.gifv', 'reddit.com/gallery/', 'https://www.reddit.com/r/', 'v.reddit.com', 'v.redd.it'), array('i.redd.it', 'i.imgur.com', 'external-preview.redd.it', 'redgifs'));
                if (count($content) > 0) {
                    return $content[0]->all->thumbnail;
                } else {
                    return null;
                }
            });
            
            return $image;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * @param $sub
     * @return mixed
     * @throws \Exception
     */
    public static function querySubDescription($sub)
    {
        try {
            $crawler = new RFCrawler('r/' . $sub . '/about/.json', env('APP_USERAGENT'), [], [
                'user' => env('REDDIT_CLIENT_ID'),
                'password' => env('REDDIT_CLIENT_SECRET')
            ]);

            $data = $crawler->fetchUrl();

            if (isset($data->data->title)) {
                return $data->data->title;
            }
            
            return null;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * @param $dest
     * @return mixed
     * @throws Exception
     */
    public static function queryCachedPost($dest)
    {
        try {
            $data = CacheModel::remember($dest . '_content', env('APP_CACHEDURATION', self::CACHE_DURATION), function() use ($dest) {
                $post = CrawlerModule::fetchContent($dest, 'ignore', null, array('.gifv', 'reddit.com/gallery/', 'https://www.reddit.com/r/', 'v.reddit.com', 'v.redd.it'), array('i.redd.it', 'i.imgur.com', 'external-preview.redd.it', 'redgifs'));
                
                if (isset($post[0])) {
                    return json_encode($post[0]);
                } else {
                    return null;
                }
            });

            return ($data) ? json_decode($data) : null;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * @return string
     */
    public static function getRemoteUrl()
    {
        return RFCrawler::URL_REDDIT;
    }
}
