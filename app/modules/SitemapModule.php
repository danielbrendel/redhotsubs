<?php

/**
 * Class SitemapModule
 */
class SitemapModule
{
    /**
     * @var array
     */
    private $sites = [];

    /**
     * @var string
     */
    private $xml = '';

    /**
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * @return string
     */
    private function url($to)
    {
        return env('APP_URL') . $to;
    }

    /**
     * @return void
     */
    private function generateUrls()
    {
        $this->sites = [];

        $this->sites[] = $this->url('/');
        $this->sites[] = $this->url('/imprint');
        $this->sites[] = $this->url('/privacy');

        if (env('APP_TWITTERFEED') !== null) {
            $this->sites[] = $this->url('/news');
        }

        if (env('APP_ENABLEAPPPAGE')) {
            $this->sites[] = $this->url('/app');
        }

        $subs = SubsModel::getAllSubs();
        for ($i = 0; $i < $subs->count(); $i++) {
            $this->sites[] = $this->url('/' . $subs->get($i)->get('sub_ident'));
        }
    }

    /**
     * @return void
     */
    public function generateXml()
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">{%URLS%}</urlset>';
        $node = '<url><loc>{%URL%}</loc></url>';

        $all_urls = '';

        foreach ($this->sites as $url) {
            $all_urls .= str_replace('{%URL%}', $url, $node);
        }

        $xml = str_replace('{%URLS%}', $all_urls, $xml);

        $this->xml = $xml;
    }

    /**
     * @return void
     */
    public function render()
    {
        header('Content-Type: text/xml');
        echo $this->xml;
        exit(0);
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->xml;
    }

    /**
     * @return string
     */
    public static function get()
    {
        $obj = new self();
        $obj->generateUrls();
        $obj->generateXml();

        return $obj->getContent();
    }
}
