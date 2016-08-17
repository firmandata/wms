<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_DB_mysql_driver extends CI_DB_mysql_driver
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
	
	function _if_null($field, $default)
	{
		return "IFNULL(".$field.", ".$default.")";
	}
	
	function _abs($field)
	{
		return "ABS(".$field.")";
	}
	
	function _concat($fields)
	{
		return "CONCAT(".implode(', ',$fields).")";
	}
	
	function _cast_to_string($field, $length = 255)
	{
		return "CAST(".$field." AS VARCHAR(".$length."))";
	}
	
	function _cast_datetime_to_date($field)
	{
		return "CAST(".$field." AS DATE)";
	}
	
	function _getdate()
	{
		return "NOW()";
	}
	
	function _datediff_day($start_field, $end_field)
	{
		return "DATEDIFF(".$end_field.", ".$start_field.")";
	}
	
	function _getyear($field)
	{
		return "YEAR(".$field.")";
	}
	
	function _getmonth($field)
	{
		return "MONTH(".$field.")";
	}
	
	function _getday($field)
	{
		return "DAY(".$field.")";
	}
}