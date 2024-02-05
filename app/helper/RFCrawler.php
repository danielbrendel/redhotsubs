<?php

/**
 * Class RFCrawler
 */
class RFCrawler
{
    /**
	 * Reddit URL
	 */
	 public const URL_REDDIT = "https://oauth.reddit.com";
	
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
      * @var array
      * 
      * Authentication credentials
      */
     private array $credentials;

    /**
      * @var string
      * 
      * Authentication bearer token
      */
     private string $auth_bearer;

     /**
      * Constructor for instantiation
      * 
      * @param string $url
      * @param string $user_agent
      * @param array $args
      * @param $credentials
      * @return void
      */
     public function __construct(string $url, string $user_agent = '', $args = array(), $credentials = array())
     {
         $this->url = self::URL_REDDIT . '/' . $url;

         $this->user_agent = $user_agent;
         $this->args = $args;
         $this->credentials = $credentials;

         $this->auth_bearer = $this->auth($credentials);
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
     public function fetch($type = self::FETCH_TYPE_IGNORE, $url_filter = array(), $url_must_contain = array())
     {
         try {
             $result = array();
             
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
             
             $data = $this->request($url, [
                "Authorization: Bearer {$this->auth_bearer}"
             ]);
             
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
             
             return $result;
         } catch (\Exception $e) {
             throw $e;
         }
     }

     /**
      * Get bearer token
      * @param $credentials
      * @return mixed
      */
    public function auth($credentials)
    {
        $response = $this->request("https://www.reddit.com/api/v1/access_token", [
            'Authorization: Basic ' . base64_encode($this->credentials['user'] . ':' . $this->credentials['password'])
        ], 'grant_type=client_credentials');

        return ((isset($response->access_token)) ? $response->access_token : null);
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
     * Perform Reddit request
     * 
     * @param $url
     * @param $header
     * @param $data
     * @return mixed
     */
    public function request($url, $header, $data = null)
    {
        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_USERAGENT, $this->user_agent);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        
        if ($data !== null) {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }

        $response = curl_exec($ch);

        if(curl_error($ch)) {
            throw new Exception(curl_error($ch));
        }

        curl_close($ch);
        
        return json_decode($response);
    }
}
