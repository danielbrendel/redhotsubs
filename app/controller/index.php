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
			
			$content = CrawlerModule::fetchContent($sub, $sorting, $after, array('.gifv', 'reddit.com/gallery/', 'https://www.reddit.com/r/', 'v.reddit.com', 'v.redd.it'), array('i.redd.it', 'i.imgur.com', 'external-preview.redd.it', 'redgifs'));

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
		//Query and convert parameter
		$ident = $request->arg('ident');
		$ident = base64_decode($ident);

		//Fetch specific post data
		$data = CrawlerModule::fetchContent($ident, 'ignore', null, array('.gifv', 'reddit.com/gallery/', 'https://www.reddit.com/r/', 'v.reddit.com', 'v.redd.it'), array('i.redd.it', 'i.imgur.com', 'external-preview.redd.it', 'redgifs'));
		
		$data = $data[0];
		
		$data->diffForHumans = (new Carbon($data->all->created_utc))->diffForHumans();
		$data->comment_amount = UtilsModule::countAsString($data->all->num_comments);
		$data->upvote_amount = UtilsModule::countAsString($data->all->ups);
		
		//Set meta if enabled
		if (env('TWITTERBOT_ENABLEMETA')) {
			$additional_meta = [
				'twitter:card' => 'summary',
				'twitter:title' => $data->title,
				'twitter:site' => url('/'),
				'twitter:description' => env('APP_DESCRIPTION'),
				'twitter:image' => $data->media,
			];
		} else {
			$additional_meta = null;
		}

		//Generate and return a view by using the helper
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
	 * Handles URL: /imprint
	 * 
	 * @param Asatru\Controller\ControllerArg $request
	 * @return Asatru\View\ViewHandler
	 */
	public function imprint($request)
	{
		//Generate and return a view by using the helper
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
		//Generate and return a view by using the helper
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
		//Generate and return a view by using the helper
		return parent::view([
			['navbar', 'navbar'],
			['cookies', 'cookies'],
			['info', 'info'],
			['content', 'news'],
			['footer', 'footer']
		], [
			'view_count' => UtilsModule::countAsString(ViewCountModel::acquireCount($_SERVER['REMOTE_ADDR']))
		]);
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

			$subs = SubsModel::getAllSubs();
			$rndsel = rand(0, $subs->count() - 1);
			
			$data = CrawlerModule::fetchContent($subs->get($rndsel)->get('sub_ident') . '/', 'hot', '', array('.gifv', 'reddit.com/gallery/', 'https://www.reddit.com/r/', 'v.reddit.com', 'v.redd.it'), array('i.redd.it', 'i.imgur.com', 'external-preview.redd.it', 'redgifs'));
			
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
