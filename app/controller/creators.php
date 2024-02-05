<?php

/**
 * Creators controller
 */
class CreatorsController extends BaseController {
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
	 * Handles URL: /creators
	 * 
	 * @param Asatru\Controller\ControllerArg $request
	 * @return Asatru\View\ViewHandler
	 */
	public function creators($request)
	{
		return parent::view([
			['navbar', 'navbar'],
			['cookies', 'cookies'],
			['info', 'info'],
			['content', 'creators'],
			['footer', 'footer'],
			['navdesktop', 'navdesktop']
		], [
			'page_title' => 'Creators',
			'categories' => AppSettingsModel::getCategories(),
			'subs' => SubsModel::getAllSubs(),
			'view_count' => UtilsModule::countAsString(ViewCountModel::acquireCount())
		]);
	}

	/**
	 * Handles URL: /creators/fetch
	 * 
	 * @param Asatru\Controller\ControllerArg $request
	 * @return Asatru\View\JsonHandler
	 */
	public function fetchCreators($request)
	{
		try {
			$paginate = $request->params()->query('paginate', null);

			$data = array();

			$creators = TrendingUserModel::getTrendingUsers(date('Y-m-d', strtotime('-1 month')), env('APP_TRENDINGUSERCOUNT'), $paginate);
			foreach ($creators as $item) {
				if (UtilsModule::userValid($item->get('username'))) {
					$data[] = array('count' => $item->get('count'), 'username' => $item->get('username'));
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
}
