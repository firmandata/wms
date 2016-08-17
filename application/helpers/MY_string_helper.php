<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Get extention from file address
 */
if ( ! function_exists('number_format_clear'))
{
	function number_format_clear($number, $decimals = 0, $dec_point = '.', $thousands_sep = ',')
	{
		if (intval($number) == $number || $decimals == 0)
			$value = number_format($number, 0, $dec_point, $thousands_sep);
		else
		{
			$value = number_format($number, $decimals, $dec_point, $thousands_sep);
			$value = rtrim($value, 0);
		}
		
		if ($value === '')
			$value = '0';
		
		return $value;
	}
}

/* End of file MY_string_helper.php */
/* Location: ./application/helpers/MY_string_helper.php */