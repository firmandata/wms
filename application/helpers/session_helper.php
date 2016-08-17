<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('need_login'))
{
	function need_login()
	{
		$CI = &get_instance();
		if ($CI->input->is_ajax_request() == TRUE)
			show_error('You need login first !', 401);
		else
		{
			redirect("system/login/index?return=".site_url(uri_string()), 'refresh', 401);
			exit;
		}
	}
}

if ( ! function_exists('access_denied'))
{
	function access_denied()
	{
		show_error('Access denied !', 403);
	}
}

if ( ! function_exists('is_authorized'))
{
	function is_authorized($control, $action)
	{
		$CI = &get_instance();
		
		$result = FALSE;
		$accesscontrols = $CI->session->userdata('accesscontrols');
		if (isset($accesscontrols[$control][$action]))
		{
			if ($accesscontrols[$control][$action])
				$result = TRUE;
		}
		
		return $result;
	}
}