<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Jqgrid
{
	protected $CI;
	
	public $fields_total_sum = array();
	public $fields_total_count = array();
	public $fields_total_avg = array();
	public $fields_total_max = array();
	public $fields_total_min = array();
	
	public function __construct()
	{
		$this->CI =& get_instance();
		
		$this->CI->load->helper('date');
	}
	
    protected function generate_where($filter)
	{
		$group_op = strtolower(trim($filter['groupOp']));
		
		$criterias = array();
		foreach ($filter['rules'] as $rule)
		{
			$where_field = $rule['field'];
			$where_value = $rule['data'];
			
			// -- Check date to convert --
			$date_value = convert_date_string_from_client($where_value);
			if ($date_value !== FALSE)
				$where_value = $date_value;
			
			switch ($rule['op'])
			{
    			case "bw":
					$criterias[] = $this->CI->db->protect_identifiers($where_field, TRUE)." LIKE '".$this->CI->db->escape_like_str($where_value)."%'";
					//$this->CI->db->like($where_field, $where_value, 'after'); 
    				break;
				case "bn":
					$criterias[] = $this->CI->db->protect_identifiers($where_field, TRUE)." NOT LIKE '".$this->CI->db->escape_like_str($where_value)."%'";
					//$this->CI->db->not_like($where_field, $where_value, 'after'); 
					break;
    			case "ew":
					$criterias[] = $this->CI->db->protect_identifiers($where_field, TRUE)." LIKE '%".$this->CI->db->escape_like_str($where_value)."'";
					//$this->CI->db->like($where_field, $where_value, 'before'); 
    				break;
				case "en":
					$criterias[] = $this->CI->db->protect_identifiers($where_field, TRUE)." NOT LIKE '%".$this->CI->db->escape_like_str($where_value)."'";
					//$this->CI->db->not_like($where_field, $where_value, 'before'); 
                    break;
    			case "cn":
					$criterias[] = $this->CI->db->protect_identifiers($where_field, TRUE)." LIKE '%".$this->CI->db->escape_like_str($where_value)."%'";
					//$this->CI->db->like($where_field, $where_value);
    				break;
    			case "nc":
					$criterias[] = $this->CI->db->protect_identifiers($where_field, TRUE)." NOT LIKE '%".$this->CI->db->escape_like_str($where_value)."%'";
					//$this->CI->db->not_like($where_field, $where_value);
                    break;
                case "eq":
					$criterias[] = $this->CI->db->protect_identifiers($where_field, TRUE)." = '".$this->CI->db->escape_str($where_value)."'";
					//$this->CI->db->where($where_field, $where_value);
    				break;
    			case "ne":
					$criterias[] = $this->CI->db->protect_identifiers($where_field, TRUE)." <> '".$this->CI->db->escape_str($where_value)."'";
					//$this->CI->db->where($where_field.' <>', $where_value);
    				break;
    			case "lt":
					$criterias[] = $this->CI->db->protect_identifiers($where_field, TRUE)." < '".$this->CI->db->escape_str($where_value)."'";
					//$this->CI->db->where($where_field.' <', $where_value);
    				break;
    			case "le":
					$criterias[] = $this->CI->db->protect_identifiers($where_field, TRUE)." <= '".$this->CI->db->escape_str($where_value)."'";
					//$this->CI->db->where($where_field.' <=', $where_value);
    				break;
    			case "gt":
					$criterias[] = $this->CI->db->protect_identifiers($where_field, TRUE)." > '".$this->CI->db->escape_str($where_value)."'";
					//$this->CI->db->where($where_field.' >', $where_value);
    				break;
    			case "ge":
					$criterias[] = $this->CI->db->protect_identifiers($where_field, TRUE)." >= '".$this->CI->db->escape_str($where_value)."'";
					//$this->CI->db->where($where_field.' >=', $where_value);
    				break;
				case "nu":
					$criterias[] = $this->CI->db->protect_identifiers($where_field, TRUE)." IS NULL";
					//$this->CI->db->where($where_field, 'IS NULL', FALSE);
					break;
				case "nn":
					$criterias[] = $this->CI->db->protect_identifiers($where_field, TRUE)." IS NOT NULL";
					//$this->CI->db->where($where_field, 'IS NOT NULL', FALSE);
					break;
				case "in":
					$where_values = explode(',', $where_value);
					foreach ($where_values as $_where_index=>$_where_value)
					{
						$where_values[$_where_index] = "'".$this->CI->db->escape_str($_where_value)."'";
					}
					$criterias[] = $this->CI->db->protect_identifiers($where_field, TRUE)." IN (".implode(",", $where_values).")";
					//$where_values = explode(',', $where_value);
					//$this->CI->db->where_in($where_field, $where_values);
                    break;
                case "ni":
					$where_values = explode(',', $where_value);
					foreach ($where_values as $_where_index=>$_where_value)
					{
						$where_values[$_where_index] = "'".$this->CI->db->escape_str($_where_value)."'";
					}
					$criterias[] = $this->CI->db->protect_identifiers($where_field, TRUE)." NOT IN (".implode(",", $where_values).")";
					//$where_values = explode(',', $where_value);
					//$this->CI->db->where_not_in($where_field, $where_values);
                    break;
    		}
		}
		
		if (!empty($filter['groups']) && is_array($filter['groups']) && count($filter['groups']) > 0)
		{
			foreach ($filter['groups'] as $group)
			{
				$criterias[] = "(".$this->generate_where($group).")";
			}
		}
		
		$criteria = implode(" ".$group_op." ", $criterias);
		
		return $criteria;
	}
	
	protected function get_where($param)
	{
		$criteria = '';
		
		if (!empty($param['search']) && $param['search'] === 'true')
		{
			/* -- Simple Search -- */
			$where_field = $param['search_field'];
			$where_op = $param['search_operator'];
			$where_value = $param['search_str'];
			if (!empty($where_field) && !empty($where_value) && !empty($where_op))
			{
				$filter = array(
					'groupOp'	=> 'AND',
					'rules'		=> array(
						array(
							'field'	=> $where_field,
							'op'	=> $where_op,
							'data'	=> $where_value
						)
					)
				);
				$criteria = $this->generate_where($filter);
			}
			else
			{
				/* -- Advance Search -- */
				$filters = $param['filters'];
				if (!empty($filters))
				{
					$filter = json_decode($filters, true);
					if (is_array($filter))
					{
						$criteria = $this->generate_where($filter);
					}
				}
			}
		}
		
		return $criteria;
	}
	
	public function set_total_sum($fields_name)
	{
		$this->fields_total_sum = $fields_name;
	}
	
	protected function get_total_sum()
	{
		$results = array();
		foreach ($this->fields_total_sum as $field_name)
		{
			if (is_array($field_name))
			{
				if (isset($field_name['query']))
					$results[] = $field_name['query']." ".$this->CI->db->protect_identifiers($field_name['alias'], TRUE);
				else
					$results[] = "SUM(".$field_name['name'].") ".$this->CI->db->protect_identifiers($field_name['alias'], TRUE);
			}
			else
				$results[] = "SUM(".$this->CI->db->protect_identifiers($field_name, TRUE).") ".$this->CI->db->protect_identifiers($field_name, TRUE);
		}
		
		return implode(",", $results);
	}
	
	public function set_total_count($fields_name)
	{
		$this->fields_total_count = $fields_name;
	}
	
	protected function get_total_count()
	{
		$results = array();
		foreach ($this->fields_total_count as $field_index=>$field_name)
		{
			if (is_array($field_name))
			{
				if (isset($field_name['query']))
					$results[] = $field_name['query']." ".$this->CI->db->protect_identifiers($field_name['alias'], TRUE);
				else
					$results[] = "COUNT(".$field_name['name'].") ".$this->CI->db->protect_identifiers($field_name['alias'], TRUE);
			}
			else
				$results[] = "COUNT(".$this->CI->db->protect_identifiers($field_name, TRUE).") ".$this->CI->db->protect_identifiers($field_name, TRUE);
		}
		
		return implode(",", $results);
	}
	
	public function set_total_avg($fields_name)
	{
		$this->fields_total_avg = $fields_name;
	}
	
	protected function get_total_avg()
	{
		$results = array();
		foreach ($this->fields_total_avg as $field_name)
		{
			if (is_array($field_name))
			{
				if (isset($field_name['query']))
					$results[] = $field_name['query']." ".$this->CI->db->protect_identifiers($field_name['alias'], TRUE);
				else
					$results[] = "AVG(".$field_name['name'].") ".$this->CI->db->protect_identifiers($field_name['alias'], TRUE);
			}
			else
				$results[] = "AVG(".$this->CI->db->protect_identifiers($field_name, TRUE).") ".$this->CI->db->protect_identifiers($field_name, TRUE);
		}
		
		return implode(",", $results);
	}
	
	public function set_total_max($fields_name)
	{
		$this->fields_total_max = $fields_name;
	}
	
	protected function get_total_max()
	{
		$results = array();
		foreach ($this->fields_total_max as $field_name)
		{
			if (is_array($field_name))
			{
				if (isset($field_name['query']))
					$results[] = $field_name['query']." ".$this->CI->db->protect_identifiers($field_name['alias'], TRUE);
				else
					$results[] = "MAX(".$field_name['name'].") ".$this->CI->db->protect_identifiers($field_name['alias'], TRUE);
			}
			else
				$results[] = "MAX(".$this->CI->db->protect_identifiers($field_name, TRUE).") ".$this->CI->db->protect_identifiers($field_name, TRUE);
		}
		
		return implode(",", $results);
	}
	
	public function set_total_min($fields_name)
	{
		$this->fields_total_min = $fields_name;
	}
	
	protected function get_total_min()
	{
		$results = array();
		foreach ($this->fields_total_min as $field_name)
		{
			if (is_array($field_name))
			{
				if (isset($field_name['query']))
					$results[] = $field_name['query']." ".$this->CI->db->protect_identifiers($field_name['alias'], TRUE);
				else
					$results[] = "MIN(".$field_name['name'].") ".$this->CI->db->protect_identifiers($field_name['alias'], TRUE);
			}
			else
				$results[] = "MIN(".$this->CI->db->protect_identifiers($field_name, TRUE).") ".$this->CI->db->protect_identifiers($field_name, TRUE);
		}
		
		return implode(",", $results);
	}
	
	public function result()
	{
		$response = new stdClass();
		
		$page = (int)$this->CI->input->get_post('page', TRUE); 
		$page = ($page == 0 ? 1 : $page);
        $limit = (int)$this->CI->input->get_post('rows', TRUE); 
        $sidx = $this->CI->input->get_post('sidx', TRUE); 
        $sord = $this->CI->input->get_post('sord', TRUE); 
        
		$req_param = array(
			"search" 			=> $this->CI->input->get_post('_search', TRUE),
			"filters" 			=> $this->CI->input->get_post('filters', TRUE),
			"search_field" 		=> $this->CI->input->get_post('searchField', TRUE),
			"search_operator" 	=> $this->CI->input->get_post('searchOper', TRUE),
			"search_str" 		=> $this->CI->input->get_post('searchString', TRUE)
		);     
		
		$user_query = $this->CI->db->get_query();
		$user_where = $this->get_where($req_param);
		
		$symbol_removes = array("\n", "\r\n", "\r");
		$user_query = str_replace($symbol_removes, ' ', $user_query);
		
		/* -- SQL Keyword Sintax -- */
		$from_word = ' FROM ';
		$where_word = ' WHERE ';
		$group_by_word = ' GROUP BY ';
		$order_by_word = ' ORDER BY ';
		
		/* -- Begin Get rows data -- */
		$db_platform = $this->CI->db->platform();
		if (in_array($db_platform, array('sqlsrv', 'oci8')))
		{
			$start = $limit * $page - $limit + 1;
			$end = $start + $limit - 1;
		}
		else
		{
			$start = $limit * $page - $limit;
		}
		
		$select_query = $user_query;
		
		$query_group_by = '';
		$group_by_pos = $this->get_ipos_outside_bracket($select_query, $group_by_word);
		if ($group_by_pos !== FALSE)
		{
			$query_before_group_by = substr($select_query, 0, $group_by_pos);
			$query_group_by = substr($select_query, $group_by_pos);
			$select_query = $query_before_group_by;
		}
		$query_order_by = '';
		$order_by_pos = $this->get_ipos_outside_bracket($select_query, $order_by_word);
		if ($order_by_pos !== FALSE)
		{
			$query_before_order_by = substr($select_query, 0, $order_by_pos);
			$query_order_by = substr($select_query, $order_by_pos);
			$select_query = $query_before_order_by;
		}
		$where_pos = $this->get_ipos_outside_bracket($select_query, $where_word);
		$query_before_where = substr($select_query, 0, $where_pos);
		$query_after_where = substr($select_query, $where_pos + strlen($where_word));
		if ($where_pos !== FALSE && !empty($user_where))
			$select_query = $query_before_where. ' WHERE ('.$query_after_where.') AND ('.$user_where.') ';
		elseif (!empty($user_where))
			$select_query .= ' WHERE '.$user_where;
		$sql = $select_query.' '.$query_group_by.' '.$query_order_by;
		
		if (!empty($sidx))
		{
			$order_by_pos = $this->get_ipos_outside_bracket($sql, $order_by_word);
			$sql .= ($order_by_pos === FALSE ? ' ORDER BY ' : ',').$this->CI->db->protect_identifiers($sidx, TRUE)." $sord ";
		}
		
		// -- limit by sql platform --
		$sql_by_platform = $sql;
		if ($limit > 0)
		{
			if ($db_platform == 'sqlsrv')
			{
				$from_pos = $this->get_ipos_outside_bracket($sql, $from_word);
				$query_before_from = substr($sql, 0, $from_pos);
				$select_pos = $this->get_ipos_outside_bracket($query_before_from, "SELECT ");
				$query_after_select = substr($query_before_from, $select_pos + strlen("SELECT "));
				$query_after_from = substr($sql, $from_pos + strlen($from_word));
				$order_by_pos = $this->get_ipos_outside_bracket($query_after_from, $order_by_word);
				if ($order_by_pos !== FALSE)
				{
					if ($page == 1)
					{
						$sql_by_platform =  
							 "	SELECT 	TOP ".(int)$end
							."			".$query_after_select." "
							." 	FROM 	".$query_after_from." "
							." 	".$query_order_by." ";
					}
					else
					{
						$query_before_order_by = substr($query_after_from, 0, $order_by_pos);
						$query_order_by = substr($query_after_from, $order_by_pos);
						$query_after_from = $query_before_order_by;
						
						$sql_by_platform =  
							 " SELECT	* "
							." FROM		("
							."		SELECT	* "
							."				, ROW_NUMBER() OVER (".$query_order_by.") AS DB_ROWNUM "
							."		FROM	("
							."			SELECT 	".$query_after_select." "
							." 			FROM 	".$query_after_from." "
							."		) inner_tbl "
							." ) result_tbl "
							." WHERE DB_ROWNUM BETWEEN ".(int)$start." AND ".(int)$end." ";
					}
				}
				else
				{
					if ($page == 1)
					{
						$sql_by_platform =  
							 " SELECT 	TOP ".(int)$end
							."			".$query_after_select." "
							." FROM 	".$query_after_from." ";
					}
					else
					{
						$sql_by_platform =  
							 " SELECT 	* "
							." FROM 	( "
							."		SELECT 	".$query_after_select." "
							." 				, ROW_NUMBER() OVER (ORDER BY (SELECT 0)) AS DB_ROWNUM "
							." 		FROM 	".$query_after_from." "
							." ) result_tbl "
							." WHERE DB_ROWNUM BETWEEN ".(int)$start." AND ".(int)$end." ";
					}
					
				}
			}
			elseif ($db_platform == 'oci8')
			{
				$sql_by_platform = 
					 " SELECT 	* "
					." FROM 	( "
					."		SELECT 	  inner_query.* "
					."				, ROWNUM rnum "
					."		FROM 	(".$sql.") inner_query "
					."		WHERE 	ROWNUM <= ".(int)$end." "
					." ) "
					." WHERE rnum >= ".(int)$start;
			}
			else
			{
				$sql_by_platform = $sql . " LIMIT $start, $limit ";
			}
		}
		$simple_list_src = $this->CI->db->query($sql_by_platform);
		$response->data = $simple_list_src->result();
		/* -- End Get rows data -- */
		
		/* -- Get query of aggregates -- */
		$sql_aggregate = array();
		if (!empty($this->fields_total_sum) || !empty($this->fields_total_count) || !empty($this->fields_total_avg) || !empty($this->fields_total_max) || !empty($this->fields_total_min))
		{
			if (count($this->fields_total_sum) > 0)
				$sql_aggregate[] = $this->get_total_sum();
			if (count($this->fields_total_count) > 0)
				$sql_aggregate[] = $this->get_total_count();
			if (count($this->fields_total_avg) > 0)
				$sql_aggregate[] = $this->get_total_avg();
			if (count($this->fields_total_max) > 0)
				$sql_aggregate[] = $this->get_total_max();
			if (count($this->fields_total_min) > 0)
				$sql_aggregate[] = $this->get_total_min();
		}
		
		/* -- Begin Get total rows, aggregates and pages -- */
		$sql_by_platform = $sql;
		$count = 0;
		$group_by_pos = $this->get_ipos_outside_bracket($user_query, $group_by_word);
		if ($group_by_pos !== FALSE)
		{
			$order_by_pos = $this->get_ipos_outside_bracket($sql, $order_by_word);
			if ($order_by_pos !== FALSE)
				$sql = substr($sql, 0, $order_by_pos);
			
			$sql_by_platform = 
				 "SELECT COUNT(*) total_row ".(count($sql_aggregate) > 0 ? ', '.implode(', ', $sql_aggregate) : '')
				." FROM (".$sql.") table_row ";
		}
		else
		{
			$from_pos = $this->get_ipos_outside_bracket($user_query, $from_word);
			$query_after_from = substr($user_query, $from_pos + strlen($from_word));
			$order_by_pos = $this->get_ipos_outside_bracket($query_after_from, $order_by_word);
			if ($order_by_pos !== FALSE)
				$query_after_from = substr($query_after_from, 0, $order_by_pos);
			$where_pos = $this->get_ipos_outside_bracket($query_after_from, $where_word);
			$query_before_where = substr($query_after_from, 0, $where_pos);
			$query_after_where = substr($query_after_from, $where_pos + strlen($where_word));
			if ($where_pos !== FALSE && !empty($user_where))
				$query_after_from = $query_before_where. ' WHERE ('.$query_after_where.') AND ('.$user_where.') ';
			elseif (!empty($user_where))
				$query_after_from .= ' WHERE '.$user_where;
			$sql_by_platform = 
				 "SELECT COUNT(*) total_row ".(count($sql_aggregate) > 0 ? ', '.implode(', ', $sql_aggregate) : '')
				." FROM ".$query_after_from;
		}
		$simple_list_src = $this->CI->db->query($sql_by_platform);
		$total_row = NULL;
		if ($simple_list_src->num_rows() > 0)
		{
			$total_row = $simple_list_src->first_row();
			$count = $total_row->total_row;
		}
		// -- Set aggregates --
		if ($count > 0 && count($sql_aggregate) > 0 && isset($response->data[0]))
		{
			foreach ($response->data[0] as $_field=>$_value)
			{
				if (isset($total_row->$_field))
					$response->userdata[$_field] = $total_row->$_field;
				else
					$response->userdata[$_field] = NULL;
			}
		}
		else
		{
			foreach ($this->fields_total_sum as $field_name)
			{
				if (is_array($field_name))
					$response->userdata[$field_name['alias']] = NULL;
				else
					$response->userdata[$field_name] = NULL;
			}
			
			foreach ($this->fields_total_count as $field_name)
			{
				if (is_array($field_name))
					$response->userdata[$field_name['alias']] = NULL;
				else
					$response->userdata[$field_name] = NULL;
			}
			
			foreach ($this->fields_total_avg as $field_name)
			{
				if (is_array($field_name))
					$response->userdata[$field_name['alias']] = NULL;
				else
					$response->userdata[$field_name] = NULL;
			}
			
			foreach ($this->fields_total_max as $field_name)
			{
				if (is_array($field_name))
					$response->userdata[$field_name['alias']] = NULL;
				else
					$response->userdata[$field_name] = NULL;
			}
			
			foreach ($this->fields_total_min as $field_name)
			{
				if (is_array($field_name))
					$response->userdata[$field_name['alias']] = NULL;
				else
					$response->userdata[$field_name] = NULL;
			}
		}
        if ($count > 0)
		{
			$limit = ($limit == 0 ? $count : $limit);
		    $total_pages = ceil($count / $limit); 
		}
        else
		    $total_pages = 0; 
        $response->page = (int)$page; 
        $response->total = (int)$total_pages; 
        $response->records = (int)$count;
		/* -- End Get total rows, aggregates and pages -- */
		
		return $response;
	}
	
	protected function get_removed_in_bracket($text, $bracket_1 = '(', $bracket_2 = ')', $outside = 0)
	{
		$strs = str_split($text);
		
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
				$new_text .= $str;
			if ($str == $bracket_2)
			{
				$deep_counter--;
				if ($deep_counter == $outside)
					$is_write_text = TRUE;
			}
		}
		
		return $new_text;
	}
	
	protected function get_ipos_outside_bracket($haystack, $needle, $bracket_1 = '(', $bracket_2 = ')', $outside = 0)
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
}