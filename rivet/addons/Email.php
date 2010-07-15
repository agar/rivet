<?php

	require_once('Swiftmailer/lib/swift_required.php');

	class Email {

		// Email templates
		private $html = '';
		private $html_rendered = '';
		private $text = '';
		private $text_rendered = '';

		// Reference to a Form
		private $form = NULL;

		// The SwiftMailer instance
		protected static $mail = FALSE;

		public function __construct($html_path, $txt_path)
		{
			if (file_exists(RIVET_BASE_PATH.Config::get('template_path').$html_path)) {
				$this->html = file_get_contents(RIVET_BASE_PATH.Config::get('template_path').$html_path);
			}
			if (file_exists(RIVET_BASE_PATH.Config::get('template_path').$txt_path)) {
				$this->text = file_get_contents(RIVET_BASE_PATH.Config::get('template_path').$txt_path);
			}

			// Create the Switemailer instance
			if (Email::$mail === FALSE) {
				$transport = Swift_MailTransport::newInstance();
				Email::$mail = Swift_Mailer::newInstance($transport);
			}
		}

		public function set_form(&$form)
		{
			$this->form =& $form;
		}

		public function send($to, $from, $subject)
		{
			// Create the message
			$this->render_templates();

			$message = Swift_Message::newInstance()
			 ->setSubject($subject)
			 ->setFrom(array($from))
			 ->setTo(array($to))
			 ->setBody($this->text_rendered)
			 ->addPart($this->html_rendered, 'text/html');

			// Send the message
			return Email::$mail->send($message);
		}

		private function render_templates()
		{
			$this->html_rendered = $this->html;
			$this->text_rendered = $this->text;
			if ($this->form) {

				foreach ($this->form->get_fields() as $field) {
					$this->html_rendered = str_replace('{{ '.$field->get_name().' }}', htmlentities($field->get_value()), $this->html_rendered);
					$this->text_rendered = str_replace('{{ '.$field->get_name().' }}', htmlentities($field->get_value()), $this->text_rendered);
				}
			}
		}


	}
