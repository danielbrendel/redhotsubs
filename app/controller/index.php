<?php

/**
 * Index controller
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
		if (!env('APP_PRIVATEMODE')) {
			return redirect('/');
		}

		$view = new Asatru\View\ViewHandler();
		$view->setLayout('auth');
		$view->setVars(['view_count' => UtilsModule::countAsString(ViewCountModel::acquireCount())]);

		return $view;
	}

	/**
	 * Handles URL: /auth
	 * 
	 * @param Asatru\Controller\ControllerArg $request
	 * @return Asatru\View\RedirectHandler
	 */
	public function auth($request)
	{
		try {
			$token = $request->params()->query('token', null);
			
			AuthModel::activate($token);

			return redirect('/');
		} catch (\Exception $e) {
			FlashMessage::setMsg('error', $e->getMessage());
			return back();
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
