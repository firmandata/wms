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

if ( ! function_exists('date_to_spelling_bahasa'))
{
	function date_to_spelling_bahasa($date)
	{
		$CI =& get_instance();
		$CI->load->helper('string');
		
		$unix_time = strtotime($date);
		$day = date('j', $unix_time);
		$day_number = date('N', $unix_time) - 1;
		$month_number = date('n', $unix_time) - 1;
		$year = date('Y', $unix_time);
		
		$array_days = array('Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu');
		$array_months = array('Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember');
		
		$spell_day_number = $array_days[$day_number];
		$spell_day = number_to_spelling_bahasa($day);
		$spell_month_number = $array_months[$month_number];
		$spell_year = number_to_spelling_bahasa($year);
		
		return array(
			  'day_number'	=> $spell_day_number
			, 'day'			=> $spell_day
			, 'month'		=> $spell_month_number
			, 'year'		=> $spell_year
		);
	}
}