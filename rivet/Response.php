<?php

	class Response {

		private static $codes = array(
	        '200' => 'OK',
	        '301' => 'Moved Permanently',
	        '302' => 'Found',
	        '303' => 'See Other',
	        '304' => 'Not Modified',
	        '400' => 'Bad Request',
	        '403' => 'Forbidden',
	        '404' => 'Not Found',
	        '405' => 'Method Not Allowed',
	        '410' => 'Gone',
	        '500' => 'Internal Server Error',
	    );
		private $status = NULL;
		private $body = '';
		private $headers = array();

		function __construct($body = '', $status = '200', $headers = array())
		{
			$this->status = $status;
			$this->headers = array_merge(
				array('Content-Type' => Config::get('default_content_type')),
				$headers
			);
			$this->body = $body;
		}

		function __toString()
		{
			$this->setHeaders();
			return (string) $this->body;
		}

		private function setHeaders()
		{
			// Set the status header
			header("HTTP/1.1 ".$this->status." ".self::$codes[$this->status]);
			foreach ($this->headers as $header => $value) {
				header("$header: $value");
			}
		}
	}
