<?php

/**
 * Cronjobs controller
 */
class CronjobsController extends BaseController {
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
	 * Handles URL: /cronjob/subs/errorneous/{pw}
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
	 * Handles URL: /cronjob/subs/description/{pw}
	 * 
	 * @param Asatru\Controller\ControllerArg $request
	 * @return Asatru\View\JsonHandler
	 */
	public function sub_descriptions($request)
	{
		try {
			if ($request->arg('pw') !== env('APP_SUBSPASSWORD')) {
				throw new Exception('Invalid password');
			}

			SubsModel::updateSubDescriptions(env('APP_DESCSUBSCHECKCOUNT', 1), env('APP_DESCSUBREFRESHHOURS', 24), env('APP_DESCSUBMAXLEN', 30));

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
}
