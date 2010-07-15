<?php

	class Route {

		public $name;
		private $rivet;
		private $url_pattern;
		private $handler;
		private $args;

		public function __construct(&$rivet, $name, $url_pattern, $handler)
		{
			$this->rivet =& $rivet;
			$this->name = $name;
			$this->url_pattern = '%'.str_replace(':num', '([\d]+)', str_replace(':any', '([^\/]+)', $url_pattern)).'%';
			$this->handler = $handler;
			$this->args = array();
		}

		public function __toString()
		{
			return "< Route: '$this->url_pattern' - '$this->name' >";
		}

		public function match(&$request)
		{
			if (preg_match($this->url_pattern, $request->uri, $matches)) {
				array_shift($matches);
				$this->args = $matches;
				return TRUE;
			}
			return FALSE;
		}

		public function run()
		{
			// "Before PHP version 5.1.0, instanceof would call __autoload() if
			// the class name did not exist. In addition, if the class was not
			// loaded, a fatal error would occur.  This can be worked around by
			// using a dynamic class reference, or a string variable containing
			// the class name."
			$closure = 'Closure';
			if (is_object($this->handler) && ($this->handler instanceof $closure)) {
				// PHP 5.3+ Closure
				return call_user_func_array($this->handler, $this->args);
			} else {
				// Reference to a method of $this (hopefully)
				return call_user_func_array(array($this->rivet, $this->handler), $this->args);

			}
		}

		public function is_named()
		{
			return trim($this->name) != '';
		}

		public function reverse($args = array())
		{
			$url = '/';
			$segments = explode('/', trim(substr($this->url_pattern, strpos($this->url_pattern, '/'), strrpos($this->url_pattern, '/')-1), '/'));
			foreach($segments as $pattern) {
				if ($pattern != '') {
					if (preg_match("%^\([^\)]+\)$%", $pattern)) {
						$arg = array_shift($args);
						if (preg_match("%^$pattern$%", $arg)) {
							$segment = $arg.'/';
						} else {
							trigger_error("Supplied arg: '$arg' does not match '$pattern' in Route pattern: '$this->url_pattern'", E_USER_ERROR);
						}
					} else {
						$segment = $pattern.'/';
					}
					$url .= $segment;
				}
			}
			if( substr($url, -1) != '/' ){
				$url .= '/';
			}
			return $url;
		}

	}