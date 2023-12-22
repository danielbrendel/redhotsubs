<?php

/**
 * Class RFCrawler
 */
class RFCrawler
{
    /**
	 * Reddit URL
	 */
	 public const URL_REDDIT = "https://www.reddit.com";
	
     /**
      * Fetch types
      */
     public const FETCH_TYPE_NEW = 'new';
     public const FETCH_TYPE_HOT = 'hot';
     public const FETCH_TYPE_TOP = 'top';
     public const FETCH_TYPE_IGNORE = '';
     
     /**
      * @var string
      * 
      * URL to subreddit
      */
     private string $url;
 
     /**
      * @var array
      * 
      * Array of optional URL arguments
      */
     private array $args = array();
 
     /**
      * @var string
      * 
      * Used user agent
      */
     private string $user_agent;
 
     /**
      * @var string
      * 
      * Temporary old user agent
      */
     private string $old_user_agent;

     /**
      * Constructor for instantiation
      * 
      * @param string $url
      * @param string $user_agent
      * @param array $args
      * @return void
      */
     public function __construct(string $url, string $user_agent = '', $args = array())
     {
         $this->url = self::URL_REDDIT . '/' . $url;
         $this->user_agent = $user_agent;
         $this->args = $args;
     }
 
     /**
      * Fetch subreddit posts from JSON
      * 
      * @param $type
      * @param $url_filter
      * @param $url_must_contain
      * @return array
      * @throws \Exception
      */
     public function fetchFromJson($type = self::FETCH_TYPE_IGNORE, $url_filter = array(), $url_must_contain = array())
     {
         try {
             $result = array();
 
             $this->storeUserAgent();
             
             $url = "{$this->url}{$type}/.json";
             $firstArg = false;
             
             foreach ($this->args as $key => $value) {
                 if (!$firstArg) {
                     $url .= "?{$key}={$value}";
                     $firstArg = true;
                 } else {
                     $url .= "&{$key}={$value}";
                 }
             }
             
             $data = json_decode(file_get_contents($url));
             
             if (is_array($data)) {
                 $children = $data[0]->data->children;
             } else {
                 $children = $data->data->children;
             }
             
             foreach ($children as $post) {
                 $postUrl = '';
                 $postTitle = '';
 
                 if (isset($post->data->url)) {
                     $postUrl = $post->data->url;
                 } else {
                     $postUrl = $post->data->link_url;
                 }
 
                 if (isset($post->data->title)) {
                     $postTitle = $post->data->title;
                 } else {
                     $postTitle = $post->data->link_title;
                 }
 
                 $cont = false;
                 
                 foreach ($url_filter as $uf) {
                     if (strpos($postUrl, $uf) !== false) {
                         $cont = true;
                         break;
                     }
                 }
                 
                 if ($cont === true) {
                     continue;
                 }
 
                 if (count($url_must_contain) > 0) {
                     if (!$this->containsAny($postUrl, $url_must_contain)) {
                         continue;
                     }
                 }
                 
                 $item = new \stdClass();
                 
                 $item->title = $postTitle;
                 $item->link = self::URL_REDDIT . "{$post->data->permalink}";
                 $item->media = $postUrl;
                 $item->author = $post->data->author;
 
                 if (isset($post->data->media->reddit_video)) {
                     $qmark = strpos($post->data->media->reddit_video->fallback_url, '?');
                     if ($qmark !== false) {
                         $item->media = substr($post->data->media->reddit_video->fallback_url, 0, $qmark);
                     } else {
                         $item->media = $post->data->media->reddit_video->fallback_url;
                     }
                 }
                 
                 $item->all = $post->data;
 
                 $result[] = $item;
             }
 
             $this->resetUserAgent();
             
             return $result;
         } catch (\Exception $e) {
             throw $e;
         }
     }

     /**
	 * Check if URL contains at least one of the required entries
	 * 
	 * @param string $url
	 * @param array $req
	 * @return bool
	 */
	private function containsAny(string $url, array $req)
	{
		$containsAny = false;
		
		foreach ($req as $item) {
			if (strpos($url, $item) !== false) {
				$containsAny = true;
				break;
			}
		}
		
		return $containsAny;
	}

	/**
	 * Store custom user agent and backup old
	 * 
	 * @return void
	 */
	private function storeUserAgent()
	{
		if ($this->user_agent !== '') {
			$this->old_user_agent = ini_get('user_agent');
			ini_set('user_agent', $this->user_agent);
		}
	}

	/**
	 * Restore old user agent
	 * 
	 * @return void
	 */
	private function resetUserAgent()
	{
		if ($this->user_agent !== '') {
			ini_set('user_agent', $this->old_user_agent);
		}
	}
}
