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
			['footer', 'footer']
		], [
			'subs' => $subs,
			'featured' => $featured,
			'view_count' => UtilsModule::countAsString(ViewCountModel::acquireCount($_SERVER['REMOTE_ADDR']))
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
					throw new Exception('Sub not valid: ' . $sub);
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
	 * Handles URL: /p/{ident}
	 * 
	 * @param Asatru\Controller\ControllerArg $request
	 * @return Asatru\View\JsonHandler
	 */
	public function showPost($request)
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
			['footer', 'footer']
		], [
			'post_data' => $data,
			'additional_meta' => $additional_meta,
			'subs' => SubsModel::getAllSubs(),
			'view_count' => UtilsModule::countAsString(ViewCountModel::acquireCount($_SERVER['REMOTE_ADDR']))
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
			$sub = null;
		}

		return parent::view([
			['navbar', 'navbar'],
			['cookies', 'cookies'],
			['info', 'info'],
			['content', 'index'],
			['footer', 'footer']
		], [
			'show_sub' => $sub,
			'subs' => $subs,
			'featured' => $featured,
			'view_count' => UtilsModule::countAsString(ViewCountModel::acquireCount($_SERVER['REMOTE_ADDR']))
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

		return parent::view([
			['navbar', 'navbar'],
			['cookies', 'cookies'],
			['info', 'info'],
			['content', 'index'],
			['footer', 'footer']
		], [
			'show_sub' => $user,
			'subs' => $subs,
			'featured' => $featured,
			'view_count' => UtilsModule::countAsString(ViewCountModel::acquireCount($_SERVER['REMOTE_ADDR']))
		]);
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
			['footer', 'footer']
		], [
			'page_title' => 'Imprint',
			'page_content' => AppSettingsModel::getImprint(),
			'view_count' => UtilsModule::countAsString(ViewCountModel::acquireCount($_SERVER['REMOTE_ADDR']))
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
			['footer', 'footer']
		], [
			'page_title' => 'Privacy policy',
			'page_content' => AppSettingsModel::getPrivacyPolicy(),
			'view_count' => UtilsModule::countAsString(ViewCountModel::acquireCount($_SERVER['REMOTE_ADDR']))
		]);
	}

	/**
	 * Handles URL: /news
	 * 
	 * @param Asatru\Controller\ControllerArg $request
	 * @return Asatru\View\ViewHandler
	 */
	public function news($request)
	{
		return parent::view([
			['navbar', 'navbar'],
			['cookies', 'cookies'],
			['info', 'info'],
			['content', 'news'],
			['footer', 'footer']
		], [
			'page_title' => 'Newsfeed',
			'view_count' => UtilsModule::countAsString(ViewCountModel::acquireCount($_SERVER['REMOTE_ADDR']))
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
		$end = date('Y-m-d');

		return parent::view([
			['navbar', 'navbar'],
			['cookies', 'cookies'],
			['info', 'info'],
			['content', 'stats'],
			['footer', 'footer']
		], [
			'render_stats_to' => 'visitor-stats',
			'render_stats_start' => $start,
			'render_stats_end' => $end,
			'render_stats_pw' => $request->arg('pw'),
			'view_count' => UtilsModule::countAsString(ViewCountModel::acquireCount($_SERVER['REMOTE_ADDR']))
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
				$end = date('Y-m-d');
			}

			$data = [];

			$visits = ViewCountModel::getVisitsPerDay($start, $end);

			$visits_new = 0;
			$visits_recurring = 0;

			for ($i = 0; $i < $visits['new']->count(); $i++) {
				$visits_new += $visits['new']->get($i)->get('count');

				$data['new'][] = [
					'date' => $visits['new']->get($i)->get('created_at'),
					'count' => $visits['new']->get($i)->get('count')
				];
			}

			for ($i = 0; $i < $visits['recurring']->count(); $i++) {
				$visits_recurring += $visits['recurring']->get($i)->get('count');

				$data['recurring'][] = [
					'date' => $visits['recurring']->get($i)->get('updated_at'),
					'count' => $visits['recurring']->get($i)->get('count')
				];
			}

			return json([
				'code' => 200,
				'data' => $data,
				'count_new' => $visits_new,
				'count_recurring' => $visits_recurring,
				'start' => $start,
				'end' => $end
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

			for ($i = 0; $i < 5; $i++) {
				$item = rand(0, count($data) - 1);
				
				if (!TwitterHistoryModel::addIfNotAlready($data[$item]->all->name)) {
					$encoded = base64_encode($data[$item]->all->permalink);
					TwitterModule::postToTwitter($data[$item]->title, url('/p/' . $encoded));
					$posted = $encoded;

					break;
				}
			}
			
			return json([
				'code' => 200,
				'sub' => $selsub,
				'posted' => $posted
			]);
		} catch (Exception $e) {
			return json([
				'code' => 500,
				'msg' => $e->getMessage()
			]);
		}
	}
}
