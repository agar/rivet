<?php

	define('TWIG_TEMPLATE_COMPATIBLE', TRUE);
	require_once 'Twig/lib/Twig/Autoloader.php';
	Twig_Autoloader::register();

	class Template {

		private $template = NULL;
		private $path = NULL;
		private $args = array();
		private $body = '';

		public function __construct($path, array $args=array())
		{
			$this->path = $path;
			$this->args = $args;

			// html template with php 5.2.4 uses twig
			$this->load_twig_template();
		}

		public function __toString()
		{
			return $this->body;
		}

		private function load_twig_template()
		{
			$template_path = Config::get('template_path');
			$loader = new Twig_Loader_Filesystem($template_path);
			$twig = new Twig_Environment($loader, array(
				'debug' => Config::get('template_debug'),
				'cache' => Config::get('template_cache')
			));

			$this->template	= $twig->loadTemplate($this->path);
			$this->body = $this->template->render($this->args);
		}
	}
