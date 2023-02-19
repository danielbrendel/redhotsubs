<?php

/*
    Asatru PHP - routes configuration file

    Add here all your needed routes.

    Schema:
        [<url>, <method>, controller_file@controller_method]
    Example:
        [/my/route, get, mycontroller@index]
        [/my/route/with/{param1}/and/{param2}, get, mycontroller@another]
    Explanation:
        Will call index() in app\controller\mycontroller.php if request is 'get'
        Every route with $ prefix is a special route
*/

return [
    array('/', 'GET', 'index@index'),
	array('/content/fetch', 'ANY', 'index@queryContent'),
    array('/content/sub/image', 'ANY', 'index@querySubImage'),
    array('/p/{sub}/{ident}/{title}', 'GET', 'index@showPost'),
    array('/p/{ident}', 'GET', 'index@showPostOld'),
    array('/r/{sub}', 'GET', 'index@showSub'),
    array('/user/{ident}', 'GET', 'index@showUser'),
    array('/video', 'GET', 'index@showVideo'),
    array('/content/video', 'GET', 'index@fetchVideo'),
    array('/favorites', 'GET', 'index@favorites'),
    array('/favorites', 'POST', 'index@queryFavorites'),
    array('/favorites/add', 'POST', 'index@addFavorite'),
    array('/favorites/remove', 'POST', 'index@removeFavorite'),
    array('/favorites/share/generate', 'POST', 'index@generateFavoriteToken'),
    array('/favorites/share/import', 'POST', 'index@importFavorites'),
    array('/creators', 'GET', 'index@creators'),
    array('/creators/fetch', 'ANY', 'index@fetchCreators'),
    array('/imprint', 'GET', 'index@imprint'),
    array('/privacy', 'GET', 'index@privacy'),
    array('/news', 'GET', 'index@news'),
    array('/getapp', 'GET', 'index@app'),
    array('/stats/{pw}', 'GET', 'index@stats'),
    array('/stats/query/{pw}', 'ANY', 'index@queryStats'),
    array('/stats/query/{pw}/online', 'ANY', 'index@queryOnlineCount'),
    array('/cronjob/twitter/{pw}', 'ANY', 'index@twitter_cronjob'),
    array('/cronjob/errorsubs/{pw}', 'ANY', 'index@check_subs'),
    array('/sitemap', 'GET', 'index@sitemap'),
    array('$404', 'ANY', 'error404@index')
];
