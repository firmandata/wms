<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (empty($message))
	$message = NULL;

$errors = array(
	'title'		=> "404 Page Not Found",
	'heading'	=> $heading,
	'message'	=> $message
);
echo json_encode($errors);