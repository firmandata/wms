<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$error_response = array(
	'title'		=> "Error",
	'heading'	=> $heading,
	'message'	=> $message
);
echo json_encode($error_response);