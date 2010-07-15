<?php

	class Request implements arrayaccess {

		private $request = array();
		public $uri = '';

		public function __construct()
		{
			$this->uri = $_SERVER['REQUEST_URI'];
			$this->request = array();
			$this->request['host'] = $_SERVER['HTTP_HOST'];
			$this->request['path'] = $_SERVER['REQUEST_URI'];
			$this->request['method'] = strtolower($_SERVER['REQUEST_METHOD']);
			$this->request['GET'] = $_GET;
			$this->request['POST'] = $_POST;
			$this->request['SERVER'] = $_SERVER;
		}

		public function __toString()
		{
			return $this->uri;
		}

		public function offsetSet($offset, $value)
		{
			$this->request[$offset] = $value;
		}

		public function offsetExists($offset)
		{
			return array_key_exists($offset, $this->request);
		}

		public function offsetUnset($offset)
		{
			unset($this->request[$offset]);
		}

		public function offsetGet($offset)
		{
			return array_key_exists($offset, $this->request) ? $this->request[$offset] : null;
		}

		public function is_ajax()
		{
			return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');
		}

	}