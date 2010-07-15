<?php
	// Include Rivet
	require_once(dirname(__FILE__).'/rivet/Rivet.php');

	// Our site handler class
	require_once('Site.php');

	// Set some custom config options
	Config::set('debug', TRUE);
	Config::set('template_cache', dirname(__FILE__).'/cache');
	Config::set('template_path', dirname(__FILE__).'/templates/');
	Config::set('template_debug', TRUE);

	// Create and dispatch the site
	$site = new Site();
	$site->dispatch();
