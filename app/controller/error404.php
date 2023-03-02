<?php

/**
 * Example error 404 controller
 */
class Error404Controller extends BaseController {
	/**
	 * Handles special case: $404
	 * 
	 * @param Asatru\Controller\ControllerArg $request
	 * @return Asatru\View\ViewHandler
	 */
	public function index($request)
	{
		return parent::view([
			['navbar', 'navbar'],
			['cookies', 'cookies'],
			['info', 'info'],
			['content', 'error/404'],
			['footer', 'footer'],
			['navdesktop', 'navdesktop']
		], [
			'subs' => SubsModel::getAllSubs(),
			'view_count' => UtilsModule::countAsString(ViewCountModel::acquireCount())
		]);
	}
}