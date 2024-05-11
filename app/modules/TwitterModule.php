<?php

use Abraham\TwitterOAuth\TwitterOAuth;

/**
 * Class TwitterModule
 */
class TwitterModule
{
    const MAX_TITLE_LENGTH = 43;

    /**
     * Post screenshot to Twitter feed
     * 
     * @param $title
     * @param $link
     * @return void
     * @throws Exception
     */
    public static function postToTwitter($title, $link)
    {
        try {
            $connection = new TwitterOAuth(env('TWITTERBOT_APIKEY',), env('TWITTERBOT_APISECRET'), env('TWITTERBOT_ACCESS_TOKEN'), env('TWITTERBOT_ACCESS_TOKEN_SECRET'));  
            $connection->setApiVersion(2);
            $connection->setTimeouts(30, 50);
            
            if (strlen($title) > env('TWITTERBOT_MAX_TITLE_LENGTH', self::MAX_TITLE_LENGTH)) {
                $title = substr($title, 0, env('TWITTERBOT_MAX_TITLE_LENGTH', self::MAX_TITLE_LENGTH)) . '...';
            }

            $status = $title . "\n\n" . $link . "\n\n" . env('TWITTERBOT_TAGS');

            $parameters = [
                'text' => $status
            ];

            $result = $connection->post('tweets', $parameters);
            
            if (!isset($result->data->id)) {
                throw new Exception('Failed to post status to Twitter: ' . print_r($result, true));
            }
        } catch (Exception $e) {
            throw $e;
        }
    }
}
