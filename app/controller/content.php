<?php

/**
 * Content controller
 */
class ContentController extends BaseController {
	/**
	 * Perform base initialization
	 * 
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct($this->layout);
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

			if (env('APP_FILTERDUPLICATES', false)) {
				$content = UtilsModule::filterDuplicates($content);
			}

			foreach ($content as $key => &$item) {
				if (isset($item->author)) {
					if (UserBlacklistModel::listed($item->author)) {
						unset($content[$key]);
						continue;
					}
				}

				$item->diffForHumans = (new Carbon($item->all->created_utc))->diffForHumans();
				$item->comment_amount = UtilsModule::countAsString($item->all->num_comments);
				$item->upvote_amount = UtilsModule::countAsString($item->all->ups);
				$item->hasFavorited = FavoritesModel::hasFavorited($item->all->permalink);
			}

			return json([
				'code' => 200,
				'data' => array_values((array)$content)
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
			
			$thumbnail = CrawlerModule::queryThumbnail($sub);

			if ($thumbnail === null) {
				return json([
					'code' => 404,
					'data' => null
				]);
			}

			return json([
				'code' => 200,
				'data' => [
					'sub' => $sub,
					'image' => $thumbnail
				]
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

		if (isset($data->author)) {
			if (UserBlacklistModel::listed($data->author)) {
				return abort(404);
			}
		}
		
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

		if (isset($data->author)) {
			if (UserBlacklistModel::listed($data->author)) {
				return abort(404);
			}
		}
		
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

		$sub_status = CrawlerModule::getSubStatus($sub);

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
			'sub_status' => $sub_status,
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
}
