<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_DB_sqlsrv_driver extends CI_DB_sqlsrv_driver
{
	var $_is_trans_opened = FALSE;
	
	function _execute($sql)
	{
		$result = parent::_execute($sql);
		if ($result === FALSE)
		{
			if ($this->_is_trans_opened == TRUE)
			{
				$error = $this->error();
				throw new Exception("Error Database, Code : " . $error['code'] . " Message : " . $error['message']);
			}
		}
		
		return $result;
	}
	
	function trans_begin($test_mode = FALSE)
	{
		$this->_is_trans_opened = TRUE;
		return parent::trans_begin($test_mode);
	}
	
	function trans_commit()
	{
		$this->_is_trans_opened = FALSE;
		return parent::trans_commit();
	}
	
	function trans_rollback()
	{
		$this->_is_trans_opened = FALSE;
		return parent::trans_rollback();
	}
	
	function is_trans_opened()
	{
		return $this->_is_trans_opened;
	}
	
	function get_ipos_outside_bracket($haystack, $needle, $bracket_1 = '(', $bracket_2 = ')', $outside = 0)
	{
		$strs = str_split($haystack);
		
		$deep_count = 0;
		$deep_counter = 0;
		$new_text = '';
		$is_write_text = TRUE;
		foreach ($strs as $str_idx=>$str)
		{
			if ($str == $bracket_1)
			{
				if ($deep_counter >= $outside)
					$is_write_text = FALSE;
				$deep_counter++;
				
				if ($deep_counter > $deep_count)
					$deep_count = $deep_counter;
			}
			if ($is_write_text == TRUE)
			{
				$new_text .= $str;
				if (strtolower(substr($new_text, -(strlen($needle)))) == strtolower($needle))
					return $str_idx + 1 - strlen($needle);
			}
			if ($str == $bracket_2)
			{
				$deep_counter--;
				if ($deep_counter == $outside)
					$is_write_text = TRUE;
			}
		}
		
		return FALSE;
	}
	
	function _if_null($field, $default)
	{
		return "ISNULL(".$field.", ".$default.")";
	}
	
	function _abs($field)
	{
		return "ABS(".$field.")";
	}
	
	function _concat($fields)
	{
		return implode(' + ',$fields);
	}
	
	function _cast_to_string($field, $length = 255)
	{
		return "CAST(".$field." AS VARCHAR(".$length."))";
	}
	
	function _cast_datetime_to_date($field)
	{
		//return "CAST(CONVERT(VARCHAR(10), ".$field.", 112) AS DATE)";
		return "CAST(".$field." AS DATE)";
	}
	
	function _getdate()
	{
		return "GETDATE()";
	}
	
	function _datediff_day($start_field, $end_field)
	{
		return "DATEDIFF(DAY, ".$start_field.", ".$end_field.")";
	}
}