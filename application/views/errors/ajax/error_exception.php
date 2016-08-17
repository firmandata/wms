<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$error_response = array(
	'title'		=> "Exception Error",
	'heading'	=> "An uncaught Exception was encountered",
	'type'		=> get_class($exception),
	'filename'	=> $exception->getFile(),
	'line'		=> $exception->getLine(),
	'message'	=> $message
);

$backtraces = array();
if (defined('SHOW_DEBUG_BACKTRACE') && SHOW_DEBUG_BACKTRACE === TRUE)
{
	foreach ($exception->getTrace() as $error)
	{
		if (isset($error['file']) && strpos($error['file'], realpath(BASEPATH)) !== 0)
		{
			$backtrace = new stdClass();
			$backtrace->file = $error['file'];
			$backtrace->line = $error['line'];
			$backtrace->function = $error['function'];
			$backtraces[] = $backtrace;
		}
	}
}
$error_response['backtraces'] = $backtraces;

echo json_encode($error_response);