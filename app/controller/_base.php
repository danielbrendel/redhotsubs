<?php 

/**
 * Base controller class
 * 
 * Extend or modify to fit your project needs
 */
class BaseController extends Asatru\Controller\Controller {
	/**
	 * @var string
	 */
	protected $layout = 'layout';

	/**
	 * Perform base initialization
	 * 
	 * @param $layout
	 * @return void
	 */
	public function __construct($layout = '')
	{
		if ($layout !== '') {
			$this->layout = $layout;
		}

		ViewCountModel::addToCount($_SERVER['REMOTE_ADDR']);

		if (env('APP_PRIVATEMODE', false)) {
			try {
				AuthModel::verify();
			} catch (\Exception $e) {
				$url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

				$allowed_urls = array('/getapp', '/news', '/imprint', '/privacy', '/contact');

				if (($url !== '/auth') && ($url !== '/login') && (!in_array($url, $allowed_urls)) && (strpos($url, '/cronjob/') === false)) {
					header('Location: /auth');
					exit();
				}
			}
		}
	}

	/**
	 * A more convenient view helper
	 * 
	 * @param array $yields
	 * @param array $attr
	 * @return Asatru\View\ViewHandler
	 */
	public function view($yields, $attr = array())
	{
		return view($this->layout, $yields, $attr);
	}
}