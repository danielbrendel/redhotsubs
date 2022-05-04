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
    array('/index', 'GET', 'index@index'),
	array('/content/fetch', 'ANY', 'index@queryContent'),
    array('/content/sub/image', 'ANY', 'index@querySubImage'),
    array('/p/{ident}', 'GET', 'index@showPost'),
    array('/r/{sub}', 'GET', 'index@showSub'),
    array('/imprint', 'GET', 'index@imprint'),
    array('/privacy', 'GET', 'index@privacy'),
    array('/news', 'GET', 'index@news'),
    array('/cronjob/twitter/{pw}', 'ANY', 'index@twitter_cronjob'),
    array('$404', 'ANY', 'error404@index')
];
