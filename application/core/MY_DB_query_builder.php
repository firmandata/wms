<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

abstract class MY_DB_query_builder extends CI_DB_query_builder
{
	public function from($from, $escape = TRUE)
	{
		foreach ((array) $from as $val)
		{
			if (strpos($val, ',') !== FALSE)
			{
				foreach (explode(',', $val) as $v)
				{
					$v = trim($v);
					$this->_track_aliases($v);

					if ($escape == TRUE)
						$this->qb_from[] = $v = $this->protect_identifiers($v, TRUE, NULL, FALSE);
					else
						$this->qb_from[] = $v;

					if ($this->qb_caching === TRUE)
					{
						$this->qb_cache_from[] = $v;
						$this->qb_cache_exists[] = 'from';
					}
				}
			}
			else
			{
				$val = trim($val);

				// Extract any aliases that might exist. We use this information
				// in the protect_identifiers to know whether to add a table prefix
				$this->_track_aliases($val);

				if ($escape == TRUE)
					$this->qb_from[] = $val = $this->protect_identifiers($val, TRUE, NULL, FALSE);
				else
					$this->qb_from[] = $val;

				if ($this->qb_caching === TRUE)
				{
					$this->qb_cache_from[] = $val;
					$this->qb_cache_exists[] = 'from';
				}
			}
		}

		return $this;
	}
	
	/**
	 * Get
	 *
	 * Compiles the select statement based on the other functions called
	 * and runs the query
	 *
	 * @param	string	the table
	 * @param	string	the limit clause
	 * @param	string	the offset clause
	 * @return	object
	 */
	public function get_query($table = '', $limit = null, $offset = null, $reset = TRUE)
	{
		if ($table !== '')
		{
			$this->_track_aliases($table);
			$this->from($table);
		}
		
		if ( ! is_null($limit))
		{
			$this->limit($limit, $offset);
		}

		$select = $this->_compile_select();

		if ($reset === TRUE)
		{
			$this->_reset_select();
		}

		return $select;
	}
	
	public function select_if_null($field, $default, $alias)
	{
		$sql = $this->_if_null($field, $default) .' AS '. $alias;
		
		$this->qb_select[] = $sql;
		$this->qb_no_escape[] = NULL;

		if ($this->qb_caching === TRUE)
		{
			$this->qb_cache_select[] = $sql;
			$this->qb_cache_exists[] = 'select';
		}

		return $this;
	}
	
	public function if_null($field, $default)
	{
		return $this->_if_null($field, $default);
	}
	
	public function select_abs($field, $alias)
	{
		$sql = $this->_abs($field) .' AS '. $alias;
		
		$this->qb_select[] = $sql;
		$this->qb_no_escape[] = NULL;

		if ($this->qb_caching === TRUE)
		{
			$this->qb_cache_select[] = $sql;
			$this->qb_cache_exists[] = 'select';
		}

		return $this;
	}
	
	public function abs($field)
	{
		return $this->_abs($field);
	}
	
	public function select_concat($fields, $alias)
	{
		$sql = $this->_concat($fields) .' AS '. $alias;
		
		$this->qb_select[] = $sql;
		$this->qb_no_escape[] = NULL;

		if ($this->qb_caching === TRUE)
		{
			$this->qb_cache_select[] = $sql;
			$this->qb_cache_exists[] = 'select';
		}

		return $this;
	}
	
	public function concat($fields)
	{
		return $this->_concat($fields);
	}
	
	public function select_cast_to_string($field, $length = 255, $alias)
	{
		$sql = $this->_cast_to_string($field, $length) .' AS '. $alias;
		
		$this->qb_select[] = $sql;
		$this->qb_no_escape[] = NULL;

		if ($this->qb_caching === TRUE)
		{
			$this->qb_cache_select[] = $sql;
			$this->qb_cache_exists[] = 'select';
		}

		return $this;
	}
	
	public function cast_to_string($field, $length = 255)
	{
		return $this->_cast_to_string($field, $length);
	}
	
	public function select_cast_datetime_to_date($field, $alias)
	{
		$sql = $this->_cast_datetime_to_date($field) .' AS '. $alias;
		
		$this->qb_select[] = $sql;
		$this->qb_no_escape[] = NULL;

		if ($this->qb_caching === TRUE)
		{
			$this->qb_cache_select[] = $sql;
			$this->qb_cache_exists[] = 'select';
		}

		return $this;
	}
	
	public function cast_datetime_to_date($field)
	{
		return $this->_cast_datetime_to_date($field);
	}
	
	public function select_getdate($alias)
	{
		$sql = $this->_getdate() .' AS '. $alias;
		
		$this->qb_select[] = $sql;
		$this->qb_no_escape[] = NULL;

		if ($this->qb_caching === TRUE)
		{
			$this->qb_cache_select[] = $sql;
			$this->qb_cache_exists[] = 'select';
		}

		return $this;
	}
	
	public function getdate()
	{
		return $this->_getdate();
	}
	
	public function select_datediff_day($start_field, $end_field, $alias)
	{
		$sql = $this->_datediff_day($start_field, $end_field) .' AS '. $alias;
		
		$this->qb_select[] = $sql;
		$this->qb_no_escape[] = NULL;

		if ($this->qb_caching === TRUE)
		{
			$this->qb_cache_select[] = $sql;
			$this->qb_cache_exists[] = 'select';
		}

		return $this;
	}
	
	public function datediff_day($start_field, $end_field)
	{
		return $this->_datediff_day($start_field, $end_field);
	}
	
	public function select_getyear($field, $alias)
	{
		$sql = $this->_getyear($field) .' AS '. $alias;
		
		$this->qb_select[] = $sql;
		$this->qb_no_escape[] = NULL;

		if ($this->qb_caching === TRUE)
		{
			$this->qb_cache_select[] = $sql;
			$this->qb_cache_exists[] = 'select';
		}

		return $this;
	}
	
	public function getyear($field)
	{
		return $this->_getyear($field);
	}
	
	public function select_getmonth($field, $alias)
	{
		$sql = $this->_getmonth($field) .' AS '. $alias;
		
		$this->qb_select[] = $sql;
		$this->qb_no_escape[] = NULL;

		if ($this->qb_caching === TRUE)
		{
			$this->qb_cache_select[] = $sql;
			$this->qb_cache_exists[] = 'select';
		}

		return $this;
	}
	
	public function getmonth($field)
	{
		return $this->_getmonth($field);
	}
	
	public function select_getday($field, $alias)
	{
		$sql = $this->_getday($field) .' AS '. $alias;
		
		$this->qb_select[] = $sql;
		$this->qb_no_escape[] = NULL;

		if ($this->qb_caching === TRUE)
		{
			$this->qb_cache_select[] = $sql;
			$this->qb_cache_exists[] = 'select';
		}

		return $this;
	}
	
	public function getday($field)
	{
		return $this->_getday($field);
	}
}