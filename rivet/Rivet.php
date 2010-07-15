<?php

	define('RIVET_BASE_PATH', dirname(__FILE__).'/');
	define('RIVET_DEBUG_EMAIL', '');

	// Core
	require_once(RIVET_BASE_PATH.'Errorhandler.php');
	require_once(RIVET_BASE_PATH.'Config.php');
	require_once(RIVET_BASE_PATH.'Request.php');
	require_once(RIVET_BASE_PATH.'Response.php');
	require_once(RIVET_BASE_PATH.'Route.php');
	require_once(RIVET_BASE_PATH.'Helpers.php');

	// Standard add-ons
	require_once(RIVET_BASE_PATH.'addons/Template.php');
	require_once(RIVET_BASE_PATH.'addons/Form.php');
	require_once(RIVET_BASE_PATH.'addons/Field.php');
	require_once(RIVET_BASE_PATH.'addons/Email.php');

	class Rivet {

		private $request;
		private $routes = array();
		public $version = '1.0a';
		private static $instance = null;

		public function __construct()
		{
			$this->request = new Request();
			self::$instance =& $this;
		}

		final public static function get_instance()
		{
			return self::$instance;
		}

		final public function route($url_pattern, $handler, $name='')
		{
			$this->routes[] = new Route($this, $name, $url_pattern, $handler);
			return $this;
		}

		final public function dispatch($return = FALSE)
		{
			foreach($this->routes as $route) {
				if ($route->match($this->request)) {
					$view = $route->run();
					if (!($view instanceof Response)) {
						$view = new Response($view);
					}
					if ($return) {
						return $view;
					} else {
						echo $view;
						return;
					}
				}
			}

			// no matching routes, go to a default 404 page
			$view = new Response('Page not found', '404');
			if ($return) {
				return $view;
			} else {
				echo $view;
			}
		}

		final public function url($route_name, $args = array())
		{
			foreach($this->routes as $route) {
				if ($route->is_named() && $route->name == $route_name) {
					return $route->reverse($args);
				}
			}
		}

		final public function redirect($url)
		{
			header('Location: '.$url);
			exit;
		}

	}

