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
		//Generate and return a view by using the helper
		return parent::view([
			['navbar', 'navbar'],
			['cookies', 'cookies'],
			['content', 'index'],
			['footer', 'footer']
		], [
			'subs' => SubsModel::getAllSubs(),
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
			['content', 'news'],
			['footer', 'footer']
		], [
			'view_count' => UtilsModule::countAsString(ViewCountModel::acquireCount($_SERVER['REMOTE_ADDR']))
		]);
	}
}
