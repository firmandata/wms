<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('add_date'))
{
	function add_date($current_date_string, $day = 0, $month = 0, $year = 0)
	{
		$current_date = strtotime($current_date_string);
		$new_date = date('Y-m-d H:i:s', 
			mktime(
				date('H', $current_date),
				date('i', $current_date), 
				date('s', $current_date), 
				date('m', $current_date) + $month,
				date('d', $current_date) + $day, 
				date('Y', $current_date) + $year
			)
		);
		
		return $new_date;
	}
}

if ( ! function_exists('is_server_datetime'))
{
	function is_server_datetime($value)
	{
		if (strtotime($value) !== FALSE)
		{
			$CI =& get_instance();
			
			if (date_create_from_format($CI->config->item('server_datetime_format'), $value) !== FALSE)
				return TRUE;
			elseif (date_create_from_format($CI->config->item('server_datetime_nosecond_format'), $value) !== FALSE)
				return TRUE;
			elseif (date_create_from_format($CI->config->item('server_date_format'), $value) !== FALSE)
				return TRUE;
			elseif (date_create_from_format($CI->config->item('server_time_format'), $value) !== FALSE)
				return TRUE;
			elseif (date_create_from_format($CI->config->item('server_time_nosecond_format'), $value) !== FALSE)
				return TRUE;
			else
				return FALSE;
		}
		else
			return FALSE;
	}
}

if ( ! function_exists('convert_date_string_from_client'))
{
	function convert_date_string_from_client($value)
	{
		$value = trim($value);
		
		$CI =& get_instance();
		
		if (date_create_from_format($CI->config->item('server_display_datetime_format'), $value) !== FALSE)
		{
			$date_value = date_create_from_format($CI->config->item('server_display_datetime_format'), $value);
			return date_format($date_value, $CI->config->item('server_datetime_format'));
		}
		elseif (date_create_from_format($CI->config->item('server_display_datetime_nosecond_format'), $value) !== FALSE)
		{
			$date_value = date_create_from_format($CI->config->item('server_display_datetime_nosecond_format'), $value);
			return date_format($date_value, $CI->config->item('server_datetime_nosecond_format'));
		}
		elseif (date_create_from_format($CI->config->item('server_display_date_format'), $value) !== FALSE)
		{
			$date_value = date_create_from_format($CI->config->item('server_display_date_format'), $value);
			return date_format($date_value, $CI->config->item('server_date_format'));
		}
		elseif (date_create_from_format($CI->config->item('server_display_time_format'), $value) !== FALSE)
		{
			$date_value = date_create_from_format($CI->config->item('server_display_time_format'), $value);
			return date_format($date_value, $CI->config->item('server_time_format'));
		}
		elseif (date_create_from_format($CI->config->item('server_display_time_nosecond_format'), $value) !== FALSE)
		{
			$date_value = date_create_from_format($CI->config->item('server_display_time_nosecond_format'), $value);
			return date_format($date_value, $CI->config->item('server_time_nosecond_format'));
		}
		else
			return FALSE;
	}
}

if ( ! function_exists('date_diff_by_day'))
{
	function date_diff_by_day($from, $to)
	{
		$to_time = strtotime($to);
		$from_time = strtotime($from);
		$time_diff = $to_time - $from_time;
		return floor($time_diff / (60 * 60 * 24));
	}
}