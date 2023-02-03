<?php

/**
 * Example index controller
 */
class IndexController extends BaseController {
	const INDEX_LAYOUT = 'layout';

	/**
	 * Perform base initialization
	 * 
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct(self::INDEX_LAYOUT);
	}

	/**
	 * Handles URL: /
	 * 
	 * @param Asatru\Controller\ControllerArg $request
	 * @return Asatru\View\ViewHandler
	 */
	public function index($request)
	{
		$subs = SubsModel::getAllSubs();

		$featured = array();
		for ($i = 0; $i < $subs->count(); $i++) {
			if ($subs->get($i)->get('featured') == 1) {
				$featured[] = $subs->get($i)->get('sub_ident');
			}
		}
		
		return parent::view([
			['navbar', 'navbar'],
			['cookies', 'cookies'],
			['info', 'info'],
			['content', 'index'],
			['footer', 'footer'],
			['navdesktop', 'navdesktop']
		], [
			'subs' => $subs,
			'featured' => $featured,
			'featUsers' => FeaturedUserModel::getSelection(env('APP_FEATUREDUSERCOUNT', 3)),
			'trendUsers' => TrendingUserModel::getTrendingUsers(date('Y-m-d', strtotime('-1 week')), env('APP_TRENDINGUSERCOUNT')),
			'view_count' => UtilsModule::countAsString(ViewCountModel::acquireCount())
		]);
	}
	
	/**
	 * Handles URL: /content/fetch
	 * 
	 * @param Asatru\Controller\ControllerArg $request
	 * @return Asatru\View\JsonHandler
	 */
	public function queryContent($request)
	{
		try {
			$sub = $request->params()->query('sub');
			$sorting = $request->params()->query('sorting');
			$after = $request->params()->query('after');
			
			if (substr($sub, -1) === '/') {
				$sub = substr($sub, 0, strlen($sub) - 1);
			}

			if (strpos($sub, 'r/') === 0) {
				$subData = SubsModel::getSubData($sub);
				if ((!$subData->get(0)) || ($subData->get(0)->get('sub_ident') !== $sub)) {
					if (env('APP_ALLOWCUSTOMSUBS')) {
						if ((!isset($_COOKIE['custom_sub']) || ($_COOKIE['custom_sub'] !== $sub))) {
							throw new Exception('Sub not valid: ' . $sub);
						}
					} else {
						throw new Exception('Sub not valid: ' . $sub);
					}
				}

				$sortStyle = 'url';
				$sub .= '/';
			} else {
				$sortStyle = 'param';
			}
			
			$content = CrawlerModule::fetchContent($sub, $sorting, $after, array('.gifv', 'reddit.com/gallery/', 'https://www.reddit.com/r/', 'v.reddit.com', 'v.redd.it'), array('i.redd.it', 'i.imgur.com', 'external-preview.redd.it', 'redgifs'), $sortStyle);

			foreach ($content as &$item) {
				$item->diffForHumans = (new Carbon($item->all->created_utc))->diffForHumans();
				$item->comment_amount = UtilsModule::countAsString($item->all->num_comments);
				$item->upvote_amount = UtilsModule::countAsString($item->all->ups);
				$item->hasFavorited = FavoritesModel::hasFavorited($item->all->permalink);
			}

			return json([
				'code' => 200,
				'data' => (array)$content
			]);
		} catch (Exception $e) {
			return json([
				'code' => 500,
				'msg' => $e->getMessage()
			]);
		}
	}

	/**
	 * Handles URL: /content/sub/image
	 * 
	 * @param Asatru\Controller\ControllerArg $request
	 * @return Asatru\View\JsonHandler
	 */
	public function querySubImage($request)
	{
		try {
			$sub = $request->params()->query('sub');
			
			$content = CrawlerModule::fetchContent($sub . '/', 'hot', '', array('.gifv', 'reddit.com/gallery/', 'https://www.reddit.com/r/', 'v.reddit.com', 'v.redd.it'), array('i.redd.it', 'i.imgur.com', 'external-preview.redd.it', 'redgifs'));

			if (count($content) > 0) {
				return json([
					'code' => 200,
					'data' => [
						'sub' => $sub,
						'image' => $content[0]->all->thumbnail
					]
				]);
			}

			return json([
				'code' => 404,
				'data' => null
			]);
		} catch (Exception $e) {
			return json([
				'code' => 500,
				'msg' => $e->getMessage()
			]);
		}
	}

	/**
	 * Handles URL: /p/{sub}/{ident}/{title}
	 * 
	 * @param Asatru\Controller\ControllerArg $request
	 * @return Asatru\View\JsonHandler
	 */
	public function showPost($request)
	{
		$sub = $request->arg('sub');
		$ident = $request->arg('ident');
		$title = $request->arg('title');

		$fetchdest = '';

		$post = TwitterHistoryModel::getByIdentAndSub($ident, $sub);
		if ($post->count() > 0) {
			$fetchdest = $post->get(0)->get('permalink');
		} else {
			$fetchdest = '/r/' . $sub . '/comments/' . ((strpos($ident, 't3_') !== false) ? substr($ident, 3) : $ident);
		}

		$data = CrawlerModule::fetchContent($fetchdest, 'ignore', null, array('.gifv', 'reddit.com/gallery/', 'https://www.reddit.com/r/', 'v.reddit.com', 'v.redd.it'), array('i.redd.it', 'i.imgur.com', 'external-preview.redd.it', 'redgifs'));
		$data = $data[0];
		
		$data->diffForHumans = (new Carbon($data->all->created_utc))->diffForHumans();
		$data->comment_amount = UtilsModule::countAsString($data->all->num_comments);
		$data->upvote_amount = UtilsModule::countAsString($data->all->ups);
		$data->hasFavorited = FavoritesModel::hasFavorited($data->all->permalink);

		$media = $data->media;
		if ((strpos($media, 'redgifs.com') !== false) || (strpos($media, '.gif') !== false)) {
			$media = $data->all->thumbnail;
		}

		$additional_meta = [
			'og:title' => $data->title,
			'og:description' => env('APP_DESCRIPTION'),
			'og:url' => url('/p/' . $sub . '/' . $ident . '/' . $title),
			'og:image' => $media
		];
		
		if (env('TWITTERBOT_ENABLEMETA')) {
			$additional_meta = array_merge($additional_meta, [
				'twitter:card' => 'summary',
				'twitter:title' => $data->title,
				'twitter:site' => url('/'),
				'twitter:description' => env('APP_DESCRIPTION'),
				'twitter:image' => $media,
			]);
		}

		return parent::view([
			['navbar', 'navbar'],
			['cookies', 'cookies'],
			['info', 'info'],
			['content', 'post'],
			['footer', 'footer'],
			['navdesktop', 'navdesktop']
		], [
			'post_data' => $data,
			'additional_meta' => $additional_meta,
			'page_title' => $data->title,
			'subs' => SubsModel::getAllSubs(),
			'view_count' => UtilsModule::countAsString(ViewCountModel::acquireCount())
		]);
	}

	/**
	 * Handles URL: /p/{ident}
	 * 
	 * @deprecated Use new showPost with sub and ident
	 * @param Asatru\Controller\ControllerArg $request
	 * @return Asatru\View\JsonHandler
	 */
	public function showPostOld($request)
	{
		$ident = $request->arg('ident');
		$ident = base64_decode($ident);

		$data = CrawlerModule::fetchContent($ident, 'ignore', null, array('.gifv', 'reddit.com/gallery/', 'https://www.reddit.com/r/', 'v.reddit.com', 'v.redd.it'), array('i.redd.it', 'i.imgur.com', 'external-preview.redd.it', 'redgifs'));
		
		$data = $data[0];
		
		$data->diffForHumans = (new Carbon($data->all->created_utc))->diffForHumans();
		$data->comment_amount = UtilsModule::countAsString($data->all->num_comments);
		$data->upvote_amount = UtilsModule::countAsString($data->all->ups);

		$media = $data->media;
		if ((strpos($media, 'redgifs.com') !== false) || (strpos($media, '.gif') !== false)) {
			$media = $data->all->thumbnail;
		}
		
		if (env('TWITTERBOT_ENABLEMETA')) {
			$additional_meta = [
				'twitter:card' => 'summary',
				'twitter:title' => $data->title,
				'twitter:site' => url('/'),
				'twitter:description' => env('APP_DESCRIPTION'),
				'twitter:image' => $media,
			];
		} else {
			$additional_meta = null;
		}

		return parent::view([
			['navbar', 'navbar'],
			['cookies', 'cookies'],
			['info', 'info'],
			['content', 'post'],
			['footer', 'footer'],
			['navdesktop', 'navdesktop']
		], [
			'post_data' => $data,
			'additional_meta' => $additional_meta,
			'page_title' => $data->title,
			'subs' => SubsModel::getAllSubs(),
			'view_count' => UtilsModule::countAsString(ViewCountModel::acquireCount())
		]);
	}

	/**
	 * Handles URL: /r/{sub}
	 * 
	 * @param Asatru\Controller\ControllerArg $request
	 * @return Asatru\View\ViewHandler
	 */
	public function showSub($request)
	{
		$subs = SubsModel::getAllSubs();

		$featured = array();
		for ($i = 0; $i < $subs->count(); $i++) {
			if ($subs->get($i)->get('featured') == 1) {
				$featured[] = $subs->get($i)->get('sub_ident');
			}
		}

		$sub = $request->arg('sub');
		$sub = 'r/' . $sub;
		if (!SubsModel::subExists($sub)) {
			if ((!env('APP_ALLOWCUSTOMSUBS')) || ((!isset($_COOKIE['custom_sub']) || ($_COOKIE['custom_sub'] !== $sub)))) {
				$sub = null;
			}
		}

		return parent::view([
			['navbar', 'navbar'],
			['cookies', 'cookies'],
			['info', 'info'],
			['content', 'index'],
			['footer', 'footer'],
			['navdesktop', 'navdesktop']
		], [
			'show_sub' => $sub,
			'subs' => $subs,
			'featured' => $featured,
			'view_count' => UtilsModule::countAsString(ViewCountModel::acquireCount())
		]);
	}

	/**
	 * Handles URL: /user/{ident}
	 * 
	 * @param Asatru\Controller\ControllerArg $request
	 * @return Asatru\View\ViewHandler
	 */
	public function showUser($request)
	{
		$subs = SubsModel::getAllSubs();

		$featured = array();
		for ($i = 0; $i < $subs->count(); $i++) {
			if ($subs->get($i)->get('featured') == 1) {
				$featured[] = $subs->get($i)->get('sub_ident');
			}
		}

		$user = $request->arg('ident');
		$user = 'user/' . $user;

		if (CrawlerModule::userExists($user)) {
			TrendingUserModel::addViewCount($user);
		}

		return parent::view([
			['navbar', 'navbar'],
			['cookies', 'cookies'],
			['info', 'info'],
			['content', 'index'],
			['footer', 'footer'],
			['navdesktop', 'navdesktop']
		], [
			'show_sub' => $user,
			'subs' => $subs,
			'featured' => $featured,
			'view_count' => UtilsModule::countAsString(ViewCountModel::acquireCount())
		]);
	}

	/**
	 * Handles URL: /video
	 * 
	 * @param Asatru\Controller\ControllerArg $request
	 * @return Asatru\View\ViewHandler
	 */
	public function showVideo($request)
	{
		$subs = SubsModel::getAllSubs();

		return parent::view([
			['navbar', 'navbar'],
			['cookies', 'cookies'],
			['info', 'info'],
			['content', 'video'],
			['footer', 'footer'],
			['navdesktop', 'navdesktop']
		], [
			'page_title' => 'Video content',
			'categories' => AppSettingsModel::getCategories(),
			'subs' => $subs,
			'view_count' => UtilsModule::countAsString(ViewCountModel::acquireCount())
		]);
	}

	/**
	 * Handles URL: /content/video
	 * 
	 * @param Asatru\Controller\ControllerArg $request
	 * @return Asatru\View\JsonHandler
	 */
	public function fetchVideo($request)
	{
		try {
			$cats = explode(',', $request->params()->query('categories', array()));

			$data = CrawlerModule::queryRandomVideo($cats);

			return json([
				'code' => 200,
				'data' => $data
			]);
		} catch (\Exception $e) {
			return json([
				'code' => 500,
				'msg' => $e->getMessage()
			]);
		}
	}

	/**
	 * Handles URL: /favorites
	 * 
	 * @param Asatru\Controller\ControllerArg $request
	 * @return Asatru\View\ViewHandler
	 */
	public function favorites($request)
	{
		return parent::view([
			['navbar', 'navbar'],
			['cookies', 'cookies'],
			['info', 'info'],
			['content', 'favorites'],
			['footer', 'footer'],
			['navdesktop', 'navdesktop']
		], [
			'page_title' => 'Favorites',
			'share_token' => FavShareModel::getShareToken(),
			'categories' => AppSettingsModel::getCategories(),
			'subs' => SubsModel::getAllSubs(),
			'view_count' => UtilsModule::countAsString(ViewCountModel::acquireCount())
		]);
	}

	/**
	 * Handles URL: /favorites
	 * 
	 * @param Asatru\Controller\ControllerArg $request
	 * @return Asatru\View\JsonHandler
	 */
	public function queryFavorites($request)
	{
		try {
			$paginate = $request->params()->query('paginate', null);

			$data = array();

			$favs = FavoritesModel::queryFavorites($paginate);
			foreach ($favs as $item) {
				$itemdata = CrawlerModule::fetchContent($item->get('ident'), 'ignore', null, array('.gifv', 'reddit.com/gallery/', 'https://www.reddit.com/r/', 'v.reddit.com', 'v.redd.it'), array('i.redd.it', 'i.imgur.com', 'external-preview.redd.it', 'redgifs'));
				
				if (isset($itemdata[0])) {
					$itemdata = $itemdata[0];

					$data[] = array('id' => $item->get('id'), 'hash' => $item->get('hash'), 'content' => $itemdata);
				}
			}

			return json([
				'code' => 200,
				'data' => $data
			]);
		} catch (\Exception $e) {
			return json([
				'code' => 500,
				'msg' => $e->getMessage()
			]);
		}
	}

	/**
	 * Handles URL: /favorites/add
	 * 
	 * @param Asatru\Controller\ControllerArg $request
	 * @return Asatru\View\JsonHandler
	 */
	public function addFavorite($request)
	{
		try {
			$ident = $request->params()->query('ident', null);

			FavoritesModel::addFavorite($ident);

			return json([
				'code' => 200
			]);
		} catch (\Exception $e) {
			return json([
				'code' => 500,
				'msg' => $e->getMessage()
			]);
		}
	}

	/**
	 * Handles URL: /favorites/remove
	 * 
	 * @param Asatru\Controller\ControllerArg $request
	 * @return Asatru\View\JsonHandler
	 */
	public function removeFavorite($request)
	{
		try {
			$ident = $request->params()->query('ident', null);

			FavoritesModel::removeFavorite($ident);

			return json([
				'code' => 200
			]);
		} catch (\Exception $e) {
			return json([
				'code' => 500,
				'msg' => $e->getMessage()
			]);
		}
	}

	/**
	 * Handles URL: /favorites/share/generate
	 * 
	 * @param Asatru\Controller\ControllerArg $request
	 * @return Asatru\View\JsonHandler
	 */
	public function generateFavoriteToken($request)
	{
		try {
			if (!env('APP_ENABLEFAVSHARE')) {
				throw new \Exception('Feature is currently deactivated');
			}

			$token = FavShareModel::genShare();

			return json([
				'code' => 200,
				'token' => $token
			]);
		} catch (\Exception $e) {
			return json([
				'code' => 500,
				'msg' => $e->getMessage()
			]);
		}
	}

	/**
	 * Handles URL: /favorites/share/import
	 * 
	 * @param Asatru\Controller\ControllerArg $request
	 * @return Asatru\View\JsonHandler
	 */
	public function importFavorites($request)
	{
		try {
			if (!env('APP_ENABLEFAVSHARE')) {
				throw new \Exception('Feature is currently deactivated');
			}
			
			$token = $request->params()->query('token', null);

			FavShareModel::migrateFavorites($token);

			return json([
				'code' => 200
			]);
		} catch (\Exception $e) {
			return json([
				'code' => 500,
				'msg' => $e->getMessage()
			]);
		}
	}

	/**
	 * Handles URL: /imprint
	 * 
	 * @param Asatru\Controller\ControllerArg $request
	 * @return Asatru\View\ViewHandler
	 */
	public function imprint($request)
	{
		return parent::view([
			['navbar', 'navbar'],
			['cookies', 'cookies'],
			['info', 'info'],
			['content', 'page'],
			['footer', 'footer'],
			['navdesktop', 'navdesktop']
		], [
			'page_title' => 'Imprint',
			'page_content' => AppSettingsModel::getImprint(),
			'view_count' => UtilsModule::countAsString(ViewCountModel::acquireCount())
		]);
	}

	/**
	 * Handles URL: /privacy
	 * 
	 * @param Asatru\Controller\ControllerArg $request
	 * @return Asatru\View\ViewHandler
	 */
	public function privacy($request)
	{
		return parent::view([
			['navbar', 'navbar'],
			['cookies', 'cookies'],
			['info', 'info'],
			['content', 'page'],
			['footer', 'footer'],
			['navdesktop', 'navdesktop']
		], [
			'page_title' => 'Privacy policy',
			'page_content' => AppSettingsModel::getPrivacyPolicy(),
			'view_count' => UtilsModule::countAsString(ViewCountModel::acquireCount())
		]);
	}

	/**
	 * Handles URL: /news
	 * 
	 * @param Asatru\Controller\ControllerArg $request
	 * @return mixed
	 */
	public function news($request)
	{
		return parent::view([
			['navbar', 'navbar'],
			['cookies', 'cookies'],
			['info', 'info'],
			['content', 'news'],
			['footer', 'footer'],
			['navdesktop', 'navdesktop']
		], [
			'page_title' => 'Newsfeed',
			'view_count' => UtilsModule::countAsString(ViewCountModel::acquireCount())
		]);
	}

	/**
	 * Handles URL: /app
	 * 
	 * @param Asatru\Controller\ControllerArg $request
	 * @return Asatru\View\ViewHandler
	 */
	public function app($request)
	{
		if (!env('APP_ENABLEAPPPAGE')) {
			return redirect('/');
		}

		$app_content = str_replace('${DOWNLOAD_LINK}', asset('download/' . env('APP_APPDOWNLOADNAME')), AppSettingsModel::getAppContent());

		return parent::view([
			['navbar', 'navbar'],
			['cookies', 'cookies'],
			['info', 'info'],
			['content', 'page'],
			['footer', 'footer'],
			['navdesktop', 'navdesktop']
		], [
			'page_title' => 'App',
			'page_content' => $app_content,
			'view_count' => UtilsModule::countAsString(ViewCountModel::acquireCount())
		]);
	}

	/**
	 * Handles URL: /stats/{pw}
	 * 
	 * @param Asatru\Controller\ControllerArg $request
	 * @return Asatru\View\ViewHandler
	 */
	public function stats($request)
	{
		if ($request->arg('pw') !== env('APP_STATSPASSWORD')) {
			throw new Exception('Invalid password');
		}

		$start = date('Y-m-d', strtotime('-30 days'));
		$end = date('Y-m-d', strtotime('-1 day'));

		$predefined_dates = [
			'Last week' => date('Y-m-d', strtotime('-7 days')),
			'Last two weeks' => date('Y-m-d', strtotime('-14 days')),
			'Last month' => date('Y-m-d', strtotime('-1 month')),
			'Last three months' => date('Y-m-d', strtotime('-3 months')),
			'Last year' => date('Y-m-d', strtotime('-1 year')),
			'Lifetime' => date('Y-m-d', strtotime(ViewCountModel::getInitialStartDate()))
		];

		return parent::view([
			['navbar', 'navbar'],
			['cookies', 'cookies'],
			['info', 'info'],
			['content', 'stats'],
			['footer', 'footer'],
			['navdesktop', 'navdesktop']
		], [
			'page_title' => 'Statistics',
			'render_stats_to' => 'visitor-stats',
			'render_stats_start' => $start,
			'render_stats_end' => $end,
			'render_stats_pw' => $request->arg('pw'),
			'predefined_dates' => $predefined_dates,
			'view_count' => UtilsModule::countAsString(ViewCountModel::acquireCount()),
			'online_count' => UtilsModule::countAsString(ViewCountModel::getOnlineCount(env('APP_ONLINEMINUTELIMIT', '30')))
		]);
	}

	/**
	 * Handles URL: /stats/query/{pw}
	 * 
	 * @param Asatru\Controller\ControllerArg $request
	 * @return Asatru\View\JsonHandler
	 */
	public function queryStats($request)
	{
		try {
			if ($request->arg('pw') !== env('APP_STATSPASSWORD')) {
				throw new Exception('Invalid password');
			}

			$start = $request->params()->query('start');
			if ($start === '') {
				$start = date('Y-m-d', strtotime('-30 days'));
			}

			$end = $request->params()->query('end');
			if ($end === '') {
				$end = date('Y-m-d', strtotime('-1 day'));
			}

			$data = [];
			$visits_total = 0;

			$visits = ViewCountModel::getVisitsPerDay($start, $end);

			$dayDiff = (new DateTime($end))->diff((new DateTime($start)))->format('%a');

			for ($i = 0; $i < $visits->count(); $i++) {
				$visits_total += $visits->get($i)->get('count');

				$data[] = [
					'date' => $visits->get($i)->get('created_at'),
					'count' => $visits->get($i)->get('count')
				];
			}

			return json([
				'code' => 200,
				'data' => $data,
				'count_total' => $visits_total,
				'start' => $start,
				'end' => $end,
				'day_diff' => $dayDiff
			]);
		} catch (Exception $e) {
			return json([
				'code' => 500,
				'msg' => $e->getMessage()
			]);
		}
	}

	/**
	 * Handles URL: /stats/query/{pw}/online
	 * 
	 * @param Asatru\Controller\ControllerArg $request
	 * @return Asatru\View\JsonHandler
	 */
	public function queryOnlineCount($request)
	{
		try {
			$online_count = UtilsModule::countAsString(ViewCountModel::getOnlineCount(env('APP_ONLINEMINUTELIMIT', '30')));

			return json([
				'code' => 200,
				'count' => $online_count 
			]);
		} catch (Exception $e) {
			return json([
				'code' => 500,
				'msg' => $e->getMessage()
			]);
		}
	}

	/**
	 * Handles URL: /cronjob/errorsubs/{pw}
	 * 
	 * @param Asatru\Controller\ControllerArg $request
	 * @return Asatru\View\JsonHandler
	 */
	public function check_subs($request)
	{
		try {
			if ($request->arg('pw') !== env('APP_SUBSPASSWORD')) {
				throw new Exception('Invalid password');
			}

			$subs = SubsModel::getErrorSubs(env('APP_ERRORSUBSCHECKCOUNT', 1));
			foreach ($subs as $sub) {
				ErrorSubsModel::addToTable($sub['name'], $sub['error'], $sub['reason']);
			}

			return json([
				'code' => 200
			]);
		} catch (Exception $e) {
			return json([
				'code' => 500,
				'msg' => $e->getMessage()
			]);
		}
	}

	/**
	 * Handles URL: /cronjob/twitter/{pw}
	 * 
	 * @param Asatru\Controller\ControllerArg $request
	 * @return Asatru\View\JsonHandler
	 */
	public function twitter_cronjob($request)
	{
		try {
			if (!env('TWITTERBOT_ENABLE')) {
				throw new Exception('Twitter Bot is currently disabled');
			}

			if ($request->arg('pw') !== env('TWITTERBOT_CRONPW')) {
				throw new Exception('The passwords do not match');
			}

			$subs = SubsModel::getSubsForTwitter();
			$rndsel = rand(0, $subs->count() - 1);
			$selsub = $subs->get($rndsel)->get('sub_ident');
			
			$data = CrawlerModule::fetchContent($selsub . '/', 'hot', '', array('.gifv', 'reddit.com/gallery/', 'https://www.reddit.com/r/', 'v.reddit.com', 'v.redd.it'), array('i.redd.it', 'i.imgur.com', 'external-preview.redd.it', 'redgifs'));
			
			$posted = null;

			$selsub = substr($selsub, strpos($selsub, '/') + 1);

			for ($i = count($data) - 1; $i >= 0; $i--) {
				$lnkname = substr($data[$i]->all->name, strpos($data[$i]->all->name, '_') + 1);
				$pltitle = substr($data[$i]->all->permalink, strpos($data[$i]->all->permalink, $lnkname . '/') + strlen($lnkname . '/'));
				$pltitle = substr($pltitle, 0, strlen($pltitle) - 1);

				if (TwitterHistoryModel::addIfNotAlready($data[$i]->all->name, $selsub, $data[$i]->all->permalink, $pltitle)) {
					$url = url('/p/' . $selsub . '/' . $data[$i]->all->name . '/' . $pltitle);
					TwitterModule::postToTwitter($data[$i]->title, $url);
					$posted = $url;

					break;
				}
			}
			
			return json([
				'code' => 200,
				'posted' => $posted
			]);
		} catch (Exception $e) {
			return json([
				'code' => 500,
				'msg' => $e->getMessage()
			]);
		}
	}

	/**
	 * Handles URL: /sitemap
	 * 
	 * @param Asatru\Controller\ControllerArg $request
	 * @return Asatru\View\CustomHandler
	 */
	public function sitemap($request)
	{
		return custom('text/xml', SitemapModule::get());
	}
}
