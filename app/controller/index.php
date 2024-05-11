<?php

/**
 * Index controller
 */
class IndexController extends BaseController {
	/**
	 * @var array
	 */
	private $captcha = [];

	/**
	 * Perform base initialization
	 * 
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct($this->layout);

		if ($_SERVER['REQUEST_METHOD'] === 'GET') { 
			$this->captcha = CaptchaModel::createSum(session_id());
			setGlobalCaptcha($this->captcha);
		}
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
			'captcha' => $this->captcha,
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
		if (AuthModel::isAuthenticated()) {
			return redirect('/');
		}

		$view = new Asatru\View\ViewHandler();
		$view->setLayout('auth');
		$view->setVars(['view_count' => UtilsModule::countAsString(ViewCountModel::acquireCount())]);

		return $view;
	}

	/**
	 * Handles URL: /register
	 * 
	 * @param Asatru\Controller\ControllerArg $request
	 * @return Asatru\View\RedirectHandler
	 */
	public function register($request)
	{
		try {
			if (env('APP_PRIVATEMODE')) {
				throw new \Exception('Private mode is currently enabled.');
			}

			$email = $request->params()->query('email', null);
			$password = $request->params()->query('password', null);
			$password_confirmation = $request->params()->query('password_confirmation', null);
			$captcha = $request->params()->query('captcha', null);

			$sum = CaptchaModel::querySum(session_id());
			if ($sum != $captcha) {
				throw new \Exception('Please enter the correct captcha');
			}

			if ($password !== $password_confirmation) {
				throw new \Exception('The passwords do not match.');
			}
			
			AuthModel::register($email, $password);

			FlashMessage::setMsg('success', 'Welcome aboard! An e-mail was dispatched to your inbox. Please verify your e-mail address before logging in.');
			return redirect('/');
		} catch (\Exception $e) {
			FlashMessage::setMsg('error', $e->getMessage());
			return back();
		}
	}

	/**
	 * Handles URL: /user/confirm
	 * 
	 * @param Asatru\Controller\ControllerArg $request
	 * @return Asatru\View\RedirectHandler
	 */
	public function confirm_user_account($request)
	{
		try {
			if (env('APP_PRIVATEMODE')) {
				throw new \Exception('Private mode is currently enabled.');
			}

			$token = $request->params()->query('token', null);

			AuthModel::confirm($token);

			FlashMessage::setMsg('success', 'Your account was successfully verified. You can now log in with your credentials.');
			return redirect('/');
		} catch (\Exception $e) {
			FlashMessage::setMsg('error', $e->getMessage());
			return redirect('/');
		}
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
			return redirect('/auth');
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
	 * Handles URL: POST /user/recover
	 * 
	 * @param Asatru\Controller\ControllerArg $request
	 * @return Asatru\View\RedirectHandler
	 */
	public function recover_user_password($request)
	{
		try {
			$email = $request->params()->query('email', null);
			
			AuthModel::recoverPassword($email);

			FlashMessage::setMsg('success', 'A reset e-mail was dispatched to your inbox.');

			return redirect('/auth');
		} catch (\Exception $e) {
			FlashMessage::setMsg('error', $e->getMessage());
			return redirect('/auth');
		}
	}

	/**
	 * Handles URL: GET /user/reset
	 * 
	 * @param Asatru\Controller\ControllerArg $request
	 * @return Asatru\View\ViewHandler
	 */
	public function view_reset_password($request)
	{
		$token = $request->params()->query('token', null);
		
		$view = new Asatru\View\ViewHandler();
		$view->setLayout('pwreset');
		$view->setVars([
			'token' => $token,
			'view_count' => UtilsModule::countAsString(ViewCountModel::acquireCount())
		]);

		return $view;
	}

	/**
	 * Handles URL: POST /user/reset
	 * 
	 * @param Asatru\Controller\ControllerArg $request
	 * @return Asatru\View\RedirectHandler
	 */
	public function reset_user_password($request)
	{
		try {
			$token = $request->params()->query('token', null);
			$password = $request->params()->query('password', null);
			$password_confirmation = $request->params()->query('password_confirmation', null);

			if ($password !== $password_confirmation) {
				throw new \Exception('The passwords do not match');
			}
			
			AuthModel::resetPassword($token, $password);

			FlashMessage::setMsg('success', 'Your password was updated. You can now login with your new password.');

			return redirect('/');
		} catch (\Exception $e) {
			FlashMessage::setMsg('error', $e->getMessage());

			if (isset($token)) {
				return redirect('/user/reset?token=' . $token);
			} else {
				return redirect('/auth');
			}
		}
	}

	/**
	 * Handles URL: /user/settings/update
	 * 
	 * @param Asatru\Controller\ControllerArg $request
	 * @return Asatru\View\RedirectHandler
	 */
	public function update_user_settings($request)
	{
		try {
			$email = $request->params()->query('email', null);
			$password = $request->params()->query('password', null);
			$password_confirmation = $request->params()->query('password_confirmation', null);
			
			AuthModel::updateSettings($email, $password, $password_confirmation);

			FlashMessage::setMsg('success', 'Settings were updated successfully');

			return redirect('/');
		} catch (\Exception $e) {
			FlashMessage::setMsg('error', $e->getMessage());
			return redirect('/');
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
