<?php

/**
 * Index controller
 */
class IndexController extends BaseController {
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
	 * Handles URL: /contact
	 * 
	 * @param Asatru\Controller\ControllerArg $request
	 * @return Asatru\View\ViewHandler
	 */
	public function contact($request)
	{
		if (!env('APP_ENABLECONTACT')) {
			return redirect('/');
		}

		return parent::view([
			['navbar', 'navbar'],
			['cookies', 'cookies'],
			['info', 'info'],
			['content', 'contact'],
			['footer', 'footer'],
			['navdesktop', 'navdesktop']
		], [
			'page_title' => 'Contact',
			'captcha' => CaptchaModel::createSum(session_id()),
			'subjects' => ContactSubjectModel::getAll(),
			'view_count' => UtilsModule::countAsString(ViewCountModel::acquireCount())
		]);
	}

	/**
	 * Handles URL: /contact
	 * 
	 * @param Asatru\Controller\ControllerArg $request
	 * @return Asatru\View\JsonHandler
	 */
	public function addContact($request)
	{
		try {
			if (!env('APP_ENABLECONTACT')) {
				throw new \Exception('Contact feature is currently deactivated');
			}

			$name = $request->params()->query('name', null);
			$email = $request->params()->query('email', null);
			$subject = $request->params()->query('subject', null);
			$content = $request->params()->query('content', null);
			$captcha = $request->params()->query('captcha', null);

			$sum = CaptchaModel::querySum(session_id());
			if ($sum !== $captcha) {
				throw new \Exception('Please enter the correct captcha');
			}

			if ((is_string($name)) && (strlen($name) > 0) && (is_string($email)) && (strlen($email) > 0) && (is_string($subject)) && (strlen($subject) > 0) && (is_string($content)) && (strlen($content) > 0)) {
				ContactModel::addEntry($name, $email, $subject, $content);
			} else {
				throw new \Exception('Please fill out the entire form');
			}

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
	 * Handles URL: /auth
	 * 
	 * @param Asatru\Controller\ControllerArg $request
	 * @return Asatru\View\ViewHandler|Asatru\View\RedirectHandler
	 */
	public function view_auth($request)
	{
		if ((!env('APP_PRIVATEMODE')) || (AuthModel::getAuthUser() !== null)) {
			return redirect('/');
		}

		$view = new Asatru\View\ViewHandler();
		$view->setLayout('auth');
		$view->setVars(['view_count' => UtilsModule::countAsString(ViewCountModel::acquireCount())]);

		return $view;
	}

	/**
	 * Handles URL: /login
	 * 
	 * @param Asatru\Controller\ControllerArg $request
	 * @return Asatru\View\RedirectHandler
	 */
	public function login($request)
	{
		try {
			$email = $request->params()->query('email', null);
			$password = $request->params()->query('password', null);
			
			AuthModel::login($email, $password);

			return redirect('/');
		} catch (\Exception $e) {
			FlashMessage::setMsg('error', $e->getMessage());
			return back();
		}
	}

	/**
	 * Handles URL: /logout
	 * 
	 * @param Asatru\Controller\ControllerArg $request
	 * @return Asatru\View\RedirectHandler
	 */
	public function logout($request)
	{
		try {
			AuthModel::logout();

			return redirect('/');
		} catch (\Exception $e) {
			FlashMessage::setMsg('error', $e->getMessage());
			return back();
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
