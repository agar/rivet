<?php

	class Form {

		protected $fields = array();

		private $action = '';
		private $enctype = '';
		private $errors = array();

		public function __construct($target = '', $enctype = '')
		{
			$this->action = $target;
			$this->enctype = $enctype;
		}

		/**
		 * Add a field to the form
		 *
		 * @param $field
		 */
		public function add_field(&$field)
		{
			$this->fields[] = $field;
		}
		
		public function get_fields()
		{
			return $this->fields;
		}

		public function posted()
		{
			return $_SERVER['REQUEST_METHOD'] == 'POST';
		}

		/**
		 * Validate the form
		 *
		 * @return Bool
		 */
		public function validate()
		{
			$this->errors = array();
			foreach($this->fields as $field) {
				$has_error = $this->has_error($field);
				if ($has_error) {
					$this->errors[] = $has_error;
				}
			}
			return count($this->errors) == 0;
		}

		/**
		 * Get a list of errors
		 *
		 * @return String
		 */
		public function errors()
		{
			return $this->errors;
		}

		/**
		 * Check if a field has errors
		 *
		 * @param $field
		 * @return unknown_type
		 */
		public function has_error(&$field)
		{
			try {
				$field->validate();
			} catch (Field_Validation_Exception $e) {
				return sprintf($e->getMessage(), $field->get_label());
			}
			return FALSE;
		}

		/**
		 * Render the form as HTML
		 *
		 * @return String
		 */
		public function render()
		{
			$enctype = $this->enctype ? 'enctype="'.htmlentities($this->enctype).'"' : '';
			$form = '<form action="'.htmlentities($this->action).'" method="post" '.$enctype.'>'."\n";

			if (count($this->errors)) {
				$form .= '<div class="errors">'.implode('<br />', $this->errors).'</div>';
			}

			foreach($this->fields as $field) {
				$form .= '<p>'.$field->render()."</p>\n";
			}

			$form .= '</form>';
			return $form;
		}

		public function __toString()
		{
			return $this->render();
		}
	}
