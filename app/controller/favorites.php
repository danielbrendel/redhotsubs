<?php


/**
 * Favorites controller
 */
class FavoritesController extends BaseController {
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
				$itemdata = CrawlerModule::queryCachedPost($item->get('ident'));

				if (isset($itemdata->author)) {
					if (UserBlacklistModel::listed($itemdata->author)) {
						continue;
					}
				}
				
				$data[] = array('id' => $item->get('id'), 'hash' => $item->get('hash'), 'content' => $itemdata);
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
}
