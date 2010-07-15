<?php

	function Rivet_error_handler($no, $str, $file, $line, $ctx)
	{
		$uri = ($_SERVER['SERVER_PORT'] == '80' ? 'http://' : 'https://').$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

		// create a formatted error string
		$error_text = '
			<html><head><title>Error Report</title>
			<style>
				body { font-family: Arial; font-size: 14px; padding: 10px 20px; line-height: 1.2; }
				strong { float: left; text-align: right; width: 120px; padding-right: 15px; }
				p { border-top: 1px solid #eee; padding: 15px 0; margin: 0;}
				h1 { font-weight: normal; }
				pre { border:1px solid #ccc; background-color:#fafafa; color: #333; margin: 0px 0px 15px;padding:20px;font: normal 11px Consolas;line-height:13px; }
				blockquote { border: 1px solid #ddd; background-color: #eee; margin: 0 0 15px;border-radius: 8px; -moz-border-radius: 8px; -webkit-border-radius: 8px;}
			</style>
			<body>
			<h1>Fail &rsaquo; <a href="'.$_SERVER['HTTP_HOST'].'">'.$_SERVER['HTTP_HOST'].'</a></h1>
			<p>Someone experienced a <abbr title="fuck up on our part" style="border-bottom: 1px dotted #999">minor issue</abbr> whilst accessing <a href="'.$uri.'">'.$uri.'</a> with '.$_SERVER['HTTP_USER_AGENT'].' from '.$_SERVER['REMOTE_ADDR'].'.</p>
		';
		$error_text .= '<p style="font-size: 15px;"><strong>Error:</strong> '.$no.' - '.$str.' <br />';
		$error_text .= '<strong>Filename:</strong> ' . $file . "<br />";
		$error_text .= '<strong>Line:</strong> ' . $line . "</p>";

		$error_text .= '<blockquote>'.file_excerpt($file, $line).'</blockquote>';

		// get a backtrace of the data
		$trace = (debug_backtrace());
		array_shift($trace);

		// gothrough each and format the output
		foreach ($trace as $k => $v) {
			if (!isset($v['file'])) {
				$v['file'] = 'unknown';
			}
			if (!isset($v['line'])) {
				$v['line'] = 'unknown';
			}
			if (!isset($v['function'])) {
				$v['function'] = 'unknown';
			}
			if (!isset($v['class'])) {
				$v['class'] = 'unknown';
			}
			$error_text .= '<p>';
			$error_text .= '<strong>File Name :</strong> ' . $v['file']. "&nbsp;<br />";
			$error_text .= '<strong>Line Number :</strong> ' . $v['line'] . "&nbsp;<br />";
			$error_text .= '<strong>Function Name :</strong> ' . $v['function'] . "&nbsp;<br />";
			$error_text .= '<strong>Class Name :</strong> ' . $v['class'] . "&nbsp;</p>";
		}

		// determine how we should log the error, test and dev display on screen, live sends via email
		if (!RIVET_DEBUG_EMAIL) {
			// clear out any output buffering for a clean message on screen
			while (ob_get_level()) {
				ob_end_clean();
			}
			echo $error_text;
		} else {
			// live will send an email to august developers
			mail (RIVET_DEBUG_EMAIL, 'Error Report: '.$_SERVER['HTTP_HOST'], $error_text, "From: ".RIVET_DEBUG_EMAIL."\nContent-Type: text/html; charset=utf-8", '-f '.RIVET_DEBUG_EMAIL);
		}
		exit;
	}

	set_error_handler('Rivet_error_handler');

	function file_excerpt($file, $line)
	{
		if (is_readable($file))
		{
			$content = preg_split('#<br />#', highlight_file($file, true));

			$lines = array();
			for ($i = max($line - 3, 1), $max = min($line + 3, count($content)); $i <= $max; $i++)
			{
				$lines[] = '<li'.($i == $line ? ' style="font-weight: bold;"' : '').'>'.$content[$i - 1].'</li>';
			}

			return '<ol start="'.max($line - 3, 1).'">'.implode("\n", $lines).'</ol></span>';
		}
		return '';
	}
