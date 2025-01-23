<?php

/**
 * Stats controller
 */
class StatsController extends BaseController {
	/**
	 * Perform base initialization
	 * 
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct($this->layout);

		if (env('APP_PRIVATEMODE')) {
			if (!AuthModel::isPrivileged()) {
				http_response_code(403);
				exit('Access denied.');
			}
		}
	}

    /**
	 * Handles URL: /stats/{pw}
	 * 
	 * @param Asatru\Controller\ControllerArg $request
	 * @return Asatru\View\ViewHandler
	 */
	public function stats($request)
	{
		if ($request->arg('pw') !== env('APP_STATSPASSWORD')) {
			throw new Exception('Invalid password');
		}

		$start = date('Y-m-d', strtotime('-30 days'));
		$end = date('Y-m-d', strtotime('-1 day'));

		$predefined_dates = [
			'Last week' => date('Y-m-d', strtotime('-7 days')),
			'Last two weeks' => date('Y-m-d', strtotime('-14 days')),
			'Last month' => date('Y-m-d', strtotime('-1 month')),
			'Last three months' => date('Y-m-d', strtotime('-3 months')),
			'Last year' => date('Y-m-d', strtotime('-1 year')),
			'Lifetime' => date('Y-m-d', strtotime(ViewCountModel::getInitialStartDate()))
		];

		return parent::view([
			['navbar', 'navbar'],
			['cookies', 'cookies'],
			['info', 'info'],
			['content', 'stats'],
			['footer', 'footer'],
			['navdesktop', 'navdesktop']
		], [
			'page_title' => 'Statistics',
			'render_stats_to' => 'visitor-stats',
			'render_stats_start' => $start,
			'render_stats_end' => $end,
			'render_stats_pw' => $request->arg('pw'),
			'predefined_dates' => $predefined_dates,
			'view_count' => UtilsModule::countAsString(ViewCountModel::acquireCount()),
			'online_count' => UtilsModule::countAsString(ViewCountModel::getOnlineCount(env('APP_ONLINEMINUTELIMIT', '30')))
		]);
	}

	/**
	 * Handles URL: /stats/query/{pw}
	 * 
	 * @param Asatru\Controller\ControllerArg $request
	 * @return Asatru\View\JsonHandler
	 */
	public function queryStats($request)
	{
		try {
			if ($request->arg('pw') !== env('APP_STATSPASSWORD')) {
				throw new Exception('Invalid password');
			}

			$start = $request->params()->query('start');
			if ($start === '') {
				$start = date('Y-m-d', strtotime('-30 days'));
			}

			$end = $request->params()->query('end');
			if ($end === '') {
				$end = date('Y-m-d', strtotime('-1 day'));
			}

			$data = [];
			$visits_total = 0;

			$visits = ViewCountModel::getVisitsPerDay($start, $end);

			$dayDiff = (new DateTime($end))->diff((new DateTime($start)))->format('%a');

			for ($i = 0; $i < $visits->count(); $i++) {
				$visits_total += $visits->get($i)->get('count');

				$data[] = [
					'date' => $visits->get($i)->get('created_at'),
					'count' => $visits->get($i)->get('count')
				];
			}

			return json([
				'code' => 200,
				'data' => $data,
				'count_total' => $visits_total,
				'start' => $start,
				'end' => $end,
				'day_diff' => (int)$dayDiff
			]);
		} catch (Exception $e) {
			return json([
				'code' => 500,
				'msg' => $e->getMessage()
			]);
		}
	}

	/**
	 * Handles URL: /stats/query/{pw}/online
	 * 
	 * @param Asatru\Controller\ControllerArg $request
	 * @return Asatru\View\JsonHandler
	 */
	public function queryOnlineCount($request)
	{
		try {
			$online_count = UtilsModule::countAsString(ViewCountModel::getOnlineCount(env('APP_ONLINEMINUTELIMIT', '30')));

			return json([
				'code' => 200,
				'count' => $online_count 
			]);
		} catch (Exception $e) {
			return json([
				'code' => 500,
				'msg' => $e->getMessage()
			]);
		}
	}
}
