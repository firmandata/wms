<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Model extends CI_Model
{
	public $fields_configuration;
	
	public $table_name;
	public $primary_key;
	public $unique_fields;
	
	public function __construct()
	{
		parent::__construct();
		
		$this->fields_configuration = array();
	}
	
	public function get_fields_configuration($fields_name = NULL, $not_exception_field = TRUE)
	{
		$results = array();
		
		if ($fields_name != NULL)
		{
			if (!is_array($fields_name))
				$fields_name = array($fields_name);
			
			foreach ($fields_name as $_field_name)
			{
				$field_configuration = array();
				$is_found = FALSE;
				foreach ($this->fields_configuration as $_index=>$_field_configuration)
				{
					if ($_field_configuration['field'] == $_field_name)
					{
						$field_configuration = $_field_configuration;
						$is_found = TRUE;
						break;
					}
				}
				
				if ($is_found == $not_exception_field)
					$results[] = $field_configuration;
			}
		}
		else
			$results = $this->fields_configuration;
		
		return $results;
	}
	
	public function add_field_configuration($field, $label, $rules = '', $value = NULL)
	{
		if (count($this->get_fields_configuration($field)) == 0)
			$this->fields_configuration[] = array('field' => $field, 'label' => $label, 'rules' => $rules, 'value' => $value);
		else
		{
			$this->modify_caption_field_configuration($field, $label);
			$this->modify_rules_field_configuration($field, $rules);
			$this->modify_value_field_configuration($field, $value);
		}
	}
	
	public function remove_fields_configuration($fields_name)
	{
		if (!is_array($fields_name))
			$fields_name = array($fields_name);
		
		foreach ($fields_name as $_field_name)
		{
			$fields_configuration = $this->fields_configuration;
			foreach ($fields_configuration as $_index=>$_field_configuration)
			{
				if ($_field_configuration['field'] == $_field_name)
				{
					unset($this->fields_configuration[$_index]);
					break;
				}
			}
		}
		
		return $this->fields_configuration;
	}
	
	public function modify_name_field_configuration($field, $name)
	{
		return $this->_set_property_field_configuration('field', $field, $name);
	}
	
	public function modify_caption_field_configuration($field, $caption)
	{
		return $this->_set_property_field_configuration('caption', $field, $caption);
	}
	
	public function modify_rules_field_configuration($field, $rules)
	{
		return $this->_set_property_field_configuration('rules', $field, $rules);
	}
	
	public function modify_value_field_configuration($field, $value)
	{
		return $this->_set_property_field_configuration('value', $field, $value);
	}
	
	public function get_value_field_configuration($field)
	{
		return $this->_get_property_field_configuration('value', $field);
	}
	
	public function add_rule_field_configuration($field, $rule)
	{
		$result = FALSE;
		$configurations = $this->get_fields_configuration($field);
		if (count($configurations) > 0)
		{
			$rules = $configurations[0]['rules'];
			if (!empty($rules))
				$rules .= '|'.$rule;
			else
				$rules = $rule;
			
			$result = $this->modify_rules_field_configuration($field, $rules);
		}
		
		return $result;
	}
	
	public function remove_rule_field_configuration($field, $rule)
	{
		$result = FALSE;
		$configurations = $this->get_fields_configuration($field);
		if (count($configurations) > 0)
		{
			$rules = str_replace(array('|'.$rule, $rule.'|', $rule), '', $configurations[0]['rules']);
			$result = $this->modify_rules_field_configuration($field, $rules);
		}
		
		return $result;
	}
	
	public function is_unique($values, $except_id = NULL)
	{
		if (!empty($this->unique_fields))
		{
			$valid = TRUE;
			if (is_array($this->unique_fields) == TRUE)
			{
				$field_values = array();
				foreach ($this->unique_fields as $fields)
				{
					if (is_array($fields) == TRUE)
					{
						$field_values_2 = array();
						foreach($fields as $field)
						{
							if (isset($values[$field]))
								$field_values_2[$field] = $values[$field];
						}
						if (count($field_values_2) > 0)
						{
							$valid = $this->_is_unique($field_values_2, $except_id);
							if ($valid == FALSE)
								break;
						}
					}
					else
					{
						if (isset($values[$fields]))
							$field_values[$fields] = $values[$fields];
					}
				}
				if (count($field_values) > 0 && $valid == TRUE)
					$valid = $this->_is_unique($field_values, $except_id);
			}
			else
				$valid = $this->_is_unique(array($this->unique_fields => $values), $except_id);
			
			return $valid;
		}
		else
			return TRUE;
	}
	
	protected function _is_unique($field_values, $except_id = NULL)
	{
		$primary_key = $this->primary_key;
		$valid = FALSE;
		
		foreach ($field_values as $field=>$value)
			$this->db->where($field, $value);
		
		$table = $this->db->get($this->table_name);
		if ($table->num_rows() > 0)
		{
			$table_row = $table->first_row();
			if ($except_id != NULL && $table_row->$primary_key == $except_id)
				$valid = TRUE;
		}
		else
			$valid = TRUE;
		return $valid;
	}
	
	protected function _set_property_field_configuration($property, $field, $value)
	{
		$is_found = FALSE;
		foreach ($this->fields_configuration as $_index=>$_field_configuration)
		{
			if ($_field_configuration['field'] == $field)
			{
				if ($property == 'value')
				{
					if ($value === '')
						$value = $_field_configuration['value'];
				}
				$this->fields_configuration[$_index][$property] = $value;
				$is_found = TRUE;
				break;
			}
		}
		
		return $is_found;
	}
	
	protected function _get_property_field_configuration($property, $field)
	{
		$value = NULL;
		foreach ($this->fields_configuration as $_index=>$_field_configuration)
		{
			if ($_field_configuration['field'] == $field)
			{
				$value = $this->fields_configuration[$_index][$property];
				break;
			}
		}
		
		return $value;
	}
	
	protected function _insert()
	{
		foreach($this->get_fields_configuration() as $field)
			$this->db->set($field['field'], $field['value']);
		
		return $this->_insert_by_table($this->table_name);
	}
	
	protected function _insert_by_table($table_name)
	{
		$this->db->set('created_date', date('Y-m-d H:i:s'));
		$this->db->set('created_by', $this->session->userdata('user_id'));
		$this->db->set('modified_date', date('Y-m-d H:i:s'));
		$this->db->set('modified_by', $this->session->userdata('user_id'));
		
		return $this->db->insert($table_name);
	}
	
	protected function _update($id)
	{
		foreach($this->get_fields_configuration() as $field)
			$this->db->set($field['field'], $field['value']);
		
		$this->db->where($this->primary_key, $id);
		return $this->_update_by_table($this->table_name);
	}
	
	protected function _update_by_table($table_name)
	{
		$this->db->set('modified_date', date('Y-m-d H:i:s'));
		$this->db->set('modified_by', $this->session->userdata('user_id'));
		return $this->db->update($table_name);
	}
	
	protected function _delete($id)
	{
		$this->db->where($this->primary_key, $id);
		return $this->_delete_by_table($this->table_name);
	}
	
	protected function _delete_by_table($table_name)
	{
		return $this->db->delete($table_name);
	}
	
	protected function _get()
	{
		$this->db->from($this->table_name);
		$result = $this->db->get();
		return $result->result();
	}
}