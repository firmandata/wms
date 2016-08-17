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

/**
 * Get spelling number to bahasa
 */
if ( ! function_exists('number_to_spelling_bahasa'))
{
	function number_to_spelling_bahasa($value)
	{
	  $abil = array("", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
	  if ($value < 12)
		return " " . $abil[$value];
	  elseif ($value < 20)
		return number_to_spelling_bahasa($value - 10) . " belas";
	  elseif ($value < 100)
		return number_to_spelling_bahasa($value / 10) . " puluh" . number_to_spelling_bahasa($value % 10);
	  elseif ($value < 200)
		return " seratus" . number_to_spelling_bahasa($value - 100);
	  elseif ($value < 1000)
		return number_to_spelling_bahasa($value / 100) . " ratus" . number_to_spelling_bahasa($value % 100);
	  elseif ($value < 2000)
		return " seribu" . number_to_spelling_bahasa($value - 1000);
	  elseif ($value < 1000000)
		return number_to_spelling_bahasa($value / 1000) . " ribu" . number_to_spelling_bahasa($value % 1000);
	  elseif ($value < 1000000000)
		return number_to_spelling_bahasa($value / 1000000) . " juta" . number_to_spelling_bahasa($value % 1000000);
	}
}

/**
 * Get code number generator
 */

if ( ! function_exists('generate_code_number'))
{
	function generate_code_number($code, $format = NULL, $number_length = 3)
	{
		$CI = &get_instance();
		
		if (empty($format))
			$format = $code.'{NUMBER}';
		
		$table = $CI->db
			->from('sys_code_number')
			->where('code', $code)
			->get();
		if ($table->num_rows() == 0)
		{
			$current_number = 1;
			
			// -- Create new current number --
			$CI->db
				->set('code', $code)
				->set('current_number', $current_number)
				->set('format', $format)
				->set('number_length', $number_length)
				->insert('sys_code_number');
		}
		else
		{
			$table_record = $table->first_row();
			
			$current_number = $table_record->current_number + 1;
			$number_length = $table_record->number_length;
			
			// -- Update current number --
			$CI->db
				->set('current_number', $current_number)
				->set('format', $format)
				->where('id', $table_record->id)
				->update('sys_code_number');
		}
		
		$code_number = sprintf('%0'.$number_length.'s', $current_number);
		return str_replace('{NUMBER}', $code_number, $format);
	}
}

/* End of file MY_string_helper.php */
/* Location: ./application/helpers/MY_string_helper.php */