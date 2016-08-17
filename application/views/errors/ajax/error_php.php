<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$error_response = array(
	'title'		=> "PHP Error",
	'heading'	=> "A PHP Error was encountered",
	'severity'	=> $severity,
	'filepath'	=> $filepath,
	'line'		=> $line,
	'message'	=> $message
);
echo json_encode($error_response);