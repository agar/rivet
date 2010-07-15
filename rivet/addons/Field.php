<?php

	class Field {

		const FORM_FIELD_TEXT = 'text';
		const FORM_FIELD_SELECT = 'select';
		const FORM_FIELD_CHECKBOX = 'checkbox';
		const FORM_FIELD_RADIO = 'radio';
		const FORM_FIELD_TEXTAREA = 'textarea';
		const FORM_FIELD_FILE = 'file';
		const FORM_FIELD_BUTTON = 'button';
		const FORM_FIELD_SUBMIT = 'submit';

		protected $type = self::FORM_FIELD_TEXT;
		protected $name = '';
		protected $label = '';
		protected $value = '';
		protected $default_value = '';
		protected $html = '';
		protected $attrs = '';
		protected $validation = FALSE;

		/**
		 * Class Constructor
		 *
		 * @param $name
		 * @param $label
		 * @param $validation
		 * @param $args
		 * @param $attrs
		 */
		function __construct($name, $label, $validation = FALSE, array $args = array(), array $attrs = array()) {

			// setup the field
			$this->name = $name;
			$this->label = $label;
			$this->attrs = $attrs;

			$this->validation = $validation;
			$this->id = (array_key_exists('id', $args)) ? $args['id'] : $this->name;
			$this->default_value = (array_key_exists('value', $args)) ? $args['value'] : '';

			// check for a post and update its value
			if (array_key_exists($this->name, $_POST)) {
				$this->value = $_POST[$this->name];
			} else {
				$this->value = $this->default_value;
			}

		}

		/**
		 * Render the field
		 *
		 * @return string
		 */
		public function render()
		{
			return sprintfn($this->html, array(
				'name' => $this->name,
				'label' => $this->render_label(),
			 	'id' => $this->id,
				'value' => htmlentities($this->get_value()),
				'attrs' => $this->get_attrs()
			));
		}
		public function __toString()
		{
			return $this->render();
		}

		/**
		 * Render a label for the field
		 *
		 * @return String
		 */
		public function render_label()
		{
			if ($this->label) {
				return sprintfn('<label for="%(id)s">%(label)s</label>', array(
					'label' => $this->label,
					'id' => $this->id,
				));
			} else {
				return '';
			}
		}

		/**
		 * Get the label text
		 *
		 * @return string
		 */
		public function get_label()
		{
			return $this->label;
		}

		/**
		 * Get the field name
		 *
		 * @return string
		 */
		public function get_name()
		{
			return $this->name;
		}

		/**
		 * Get the current value for the field
		 *
		 * @return string
		 */
		public function get_value()
		{
			return $this->value;
		}

		/**
		 * Get an attribute string for the field
		 *
		 * @return string
		 */
		public function get_attrs()
		{
			$attrs = '';
			if (count($this->attrs)) {
				foreach ($this->attrs as $key => $value) {
					$attrs .= ' '.$key.'="'.htmlentities($value).'" ';
				}
			}
			return $attrs;
		}

		/**
		 * Validate the field
		 *
		 * @return Bool
		 */
		public function validate()
		{
			if ($this->validation && !$this->value) {
				throw new Field_Validation_Exception();
			}

			// TODO: Customise the validation message per field...
			if ($this->validation) {

				if (!is_array($this->validation)) {
					$this->validation = array($this->validation => '%s does not compute.');
				}
				foreach($this->validation as $val => $message) {
					if (!preg_match($val, $this->value)) {
						throw new Field_Validation_Exception($message);
					}
				}
			}
			return TRUE;
		}

	}

	class TextField extends Field
	{
		protected $type = self::FORM_FIELD_TEXT;
		protected $html = '%(label)s<input type="text" name="%(name)s" id="%(id)s" value="%(value)s" %(attrs)s>';
	}

	class EmailField extends TextField
	{
		function __construct($name, $label, array $args = array(), $attrs = array())
		{
			$validation = array(
				'/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix' => '%s must be a valid email.'
			);
			parent::__construct($name, $label, $validation, $args, $attrs);
		}
	}

	class SubmitField extends Field
	{
		protected $type = self::FORM_FIELD_SUBMIT;
		protected $html = '<input type="submit" name="%(name)s" id="%(id)s" value="%(value)s" %(attrs)s>';

		function __construct($name, $label, array $attrs = array())
		{
			parent::__construct($name, '', FALSE, array('value' => $label), $attrs);
		}
	}

	class TextareaField extends Field
	{
		protected $type = self::FORM_FIELD_TEXTAREA;
		protected $html = '%(label)s<textarea name="%(name)s" id="%(id)s" %(attrs)s>%(value)s</textarea>';
	}

	class FileField extends Field
	{
		protected $type = self::FORM_FIELD_FILE;
		protected $html = '%(label)s<input type="file" name="%(name)s" id="%(id)s" %(attrs)s>';
	}

   	class RadioField extends Field
	{
		protected $type = self::FORM_FIELD_RADIO;
		protected $html = '<label><input type="radio" name="%(name)s" value="%(value)s" %(attrs)s %(checked)s>%(label)s</label>';
		private $choices;

		function __construct($name, $label, array $choices, $validation = FALSE, array $args = array(), $attrs = array())
		{
			$this->choices = $choices;
			parent::__construct($name, $label, $validation, $args, $attrs);
		}

		public function render()
		{
			$html = '';
			foreach ($this->choices as $value => $display) {
				$html .= sprintfn($this->html, array(
					'name' => $this->name,
					'id' => $this->id,
					'attrs' => $this->get_attrs(),
					'value' => $value,
					'checked' => ($value == $this->get_value()) ? 'checked="checked"' : '',
					'label' => $display
				));
			}
			return $html;
		}

		public function get_choices()
		{
			$choices = '';

		}

	}

 	class SelectField extends Field
 	{
		protected $type = self::FORM_FIELD_SELECT;
 		protected $html = '%(label)s <select name="%(name)s" id="%(id)s" %(attrs)s> %(choices)s </select>';
 		private $choice_html = '<option value="%(value)s"%(selected)s>%(label)s</option>';
 		private $choices;

		function __construct($name, $label, array $choices, $validation = FALSE, array $args = array(), $attrs = array())
		{
			$this->choices = $choices;
			parent::__construct($name, $label, $validation, $args, $attrs);
		}

		public function render()
		{
			 return sprintfn($this->html, array(
				'name' => $this->name,
				'label' => $this->render_label(),
			 	'id' => $this->id,
				'attrs' => $this->get_attrs(),
				'choices' => $this->get_choices()
			 ));
		}

		public function get_choices()
		{
			$choices = '';
			foreach ($this->choices as $value => $display) {
				$choices .= sprintfn($this->choice_html, array(
					'value' => $value,
					'selected' => ($value == $this->get_value()) ? 'selected="selected"' : '',
					'label' => $display
				));
			}
			return $choices;
		}
	}


	/**
	 * Required field error
	 *
	 * @author matt.agar
	 *
	 */
	class Field_Validation_Exception extends Exception
	{
		protected $message = '%s is a required field.';
	}


