<?php

	class Config {

		private static $settings = array(
			'debug' => FALSE,
			'template_path' => '../templates/',
			'template_debug' => FALSE,
			'template_cache' => '../cache/',
			'default_content_type' => 'text/html',
			'static_path' => '../static/',
			'error_email' => 'matt.agar@august.com.au'
		);

		public static function set($setting, $value)
		{
			self::$settings[$setting] = $value;
		}

		public static function get($setting)
		{
			if (array_key_exists($setting, self::$settings)) {
				return 	self::$settings[$setting];
			}
			return NULL;
		}

	}
