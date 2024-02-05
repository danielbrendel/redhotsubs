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
    array('/imprint', 'GET', 'index@imprint'),
    array('/privacy', 'GET', 'index@privacy'),
    array('/contact', 'GET', 'index@contact'),
    array('/contact', 'POST', 'index@addContact'),
    array('/news', 'GET', 'index@news'),
    array('/getapp', 'GET', 'index@app'),
    array('/auth', 'GET', 'index@view_auth'),
    array('/auth', 'POST', 'index@auth'),
    array('/sitemap', 'GET', 'index@sitemap'),

	array('/content/fetch', 'ANY', 'content@queryContent'),
    array('/content/sub/image', 'ANY', 'content@querySubImage'),
    array('/p/{sub}/{ident}/{title}', 'GET', 'content@showPost'),
    array('/p/{ident}', 'GET', 'content@showPostOld'),
    array('/r/{sub}', 'GET', 'content@showSub'),
    array('/user/{ident}', 'GET', 'content@showUser'),
    array('/video', 'GET', 'content@showVideo'),
    array('/content/video', 'GET', 'content@fetchVideo'),

    array('/favorites', 'GET', 'favorites@favorites'),
    array('/favorites', 'POST', 'favorites@queryFavorites'),
    array('/favorites/add', 'POST', 'favorites@addFavorite'),
    array('/favorites/remove', 'POST', 'favorites@removeFavorite'),
    array('/favorites/share/generate', 'POST', 'favorites@generateFavoriteToken'),
    array('/favorites/share/import', 'POST', 'favorites@importFavorites'),

    array('/creators', 'GET', 'creators@creators'),
    array('/creators/fetch', 'ANY', 'creators@fetchCreators'),
    
    array('/stats/{pw}', 'GET', 'stats@stats'),
    array('/stats/query/{pw}', 'ANY', 'stats@queryStats'),
    array('/stats/query/{pw}/online', 'ANY', 'stats@queryOnlineCount'),

    array('/cronjob/twitter/{pw}', 'ANY', 'cronjobs@twitter_cronjob'),
    array('/cronjob/errorsubs/{pw}', 'ANY', 'cronjobs@check_subs'),

    array('$404', 'ANY', 'error404@index')
];
