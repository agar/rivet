<?php

	class Site extends Rivet
	{
		/**
		 * Define routes
		 */
		public function __construct()
		{
			$this->route('^/$', 'home', $name='home');
			$this->route('^/contact/?$', 'contact', $name='contact');
			$this->route('^/example/:num/?$', 'example', $name='example');

			parent::__construct();
		}

		/**
		 * Home page
		 */
		public function home()
		{
			return new Template('home.html');
		}

		/**
		 * Twig template example
		 */
		public function example($number)
		{
			return new Template('example.html', array(
				'var' => $number,
				'link' => $this->url('example', array(12))
			));
		}

		/**
		 * Example form page
		 */
		public function contact()
		{
			// Define the form fields
			$form = new Form();
			$form->add_field( new TextField('name', 'Name', '/^.+$/') );
			$form->add_field( new EmailField('email', 'Email') );
			$form->add_field( new RadioField('option', 'Option', array(
				'1' => 'One',
				'2' => 'Two',
				'3' => 'Three',
			)));
			$form->add_field( new SelectField('option', 'Option', array(
				'1' => 'One',
				'2' => 'Two',
				'3' => 'Three',
			)));
			$form->add_field( new SubmitField('submit', 'Submit', array('class' => 'bottom')) );

			// Check for a posted, valid form
			if ($form->posted() && $form->validate()) {

				// Send an email
				$email = new Email('emails/contact.html', 'emails/contact.txt');
				$email->set_form($form);
				$email->send(
					'mattagar@localhost',
					'mattagar@localhost',
					'Test Message'
				);

				$this->redirect('/thanks');

			} else {

				return new Template('contact.html', array(
					'var' => 'A variable for substitution',
					'form' => $form
				));
			}
		}


	}
