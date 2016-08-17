<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Controller extends CI_Controller
{
	protected $_fields;
	protected $_login_protected;
	protected $_layout;
	protected $_ajax_style;
	
	protected $message;
	
	public function __construct($login_protected = TRUE, $layout = 'admin')
	{
		parent::__construct();
		
		$this->_ajax_style = TRUE;
		
		$this->_login_protected = $login_protected;
		$this->_layout = $layout;
		
		if ($this->_login_protected == TRUE)
		{
			if ($this->session->userdata('logged_in') !== TRUE) 
				need_login();
		}
		
		if (empty($this->message))
			$this->message = $this->session->flashdata('message');
		
		$this->form_validation->set_error_delimiters('[', ' ]');
	}
	
	/**	Pre-condition :
			- use your active record 'SELECT' function before run this function
			- don't use $this->db->limit or $this->db->get function before run this function
	*/
	protected function _get_list_json($user_query = NULL)
	{
		$this->load->library('jqgrid');
		$result = $this->jqgrid->result($user_query);
		return $this->result_json($result);
	}
	
	/**	Pre-condition :
			- use your active record 'SELECT' function before run this function
			- don't use $this->db->limit or $this->db->get function before run this function
	*/
	protected function _get_list_select2_json()
	{
		$limit = $this->input->get_post('limit');
		$page = $this->input->get_post('page');
		if ($limit !== NULL)
		{
			$limit = (int)$limit;
			if ($page !== NULL)
			{
				$start = $limit * (int)$page - $limit;
				$this->db->limit($limit, $start);
			}
			else
				$this->db->limit($limit);
		}
		$table = $this->db->get();
		$data = new stdClass();
		if ($table->num_rows() > 0)
			$data->more = TRUE;
		else
			$data->more = FALSE;
		$data->results = $table->result();
		return $this->result_json($data);
	}
	
	/**	Pre-condition :
			- use your active record 'SELECT' function before run this function
			- don't use $this->db->limit or $this->db->get function before run this function
	*/
	protected function _get_list_autocomplete_json()
	{
		$limit = $this->input->get_post('limit');
		if ($limit !== NULL)
			$this->db->limit((int)$limit);
		else
			$this->db->limit(10);
		$table = $this->db->get();
		return $this->result_json($table->result());
	}
	
	public function jqgrid_cud()
	{
		$oper = $this->input->get_post('oper');
		switch ($oper)
		{
			case 'add':
				$this->insert();
			break;
			case 'edit':
				$id = $this->input->get_post('id');
				$this->update($id);
			break;
			case 'del':
				$id = $this->input->get_post('id');
				$ids = explode(',', $id);
				if (count($ids) > 1)
				{
					foreach ($ids as $id)
					{
						$this->delete($id);
					}
				}
				else
					$this->delete($id);
			break;
		}
	}
	
	/**	Pre-condition :
			- use your active record 'SET' function before run this function
	*/
	protected function _insert($model, $function_name = '_insert', $params = array(), $fields_validation = array(), $auto_set_value = TRUE, $resource = 'get_post')
	{
		$result = new stdClass();
		$result->response = FALSE;
		$result->value = NULL;
		$result->data = array();
		
		$this->$model->remove_fields_configuration(array($this->$model->primary_key));
		
		if (count($fields_validation) > 0)
			$this->form_validation->set_rules($fields_validation);
		else
			$this->form_validation->set_rules($this->$model->get_fields_configuration());
		
		if ($this->form_validation->run() == FALSE)
			$result->value = validation_errors();
		else
		{
			$this->db->trans_begin();
			
			try
			{
				if (!empty($this->$model->unique_fields))
					$this->validate_unique_fields($model, $this->$model->unique_fields, $resource);
				
				if ($auto_set_value == TRUE)
					$this->set_model_values($model, $resource);
				
				$result->value = call_user_func_array(array($this->$model, $function_name), $params);
				
				if ($this->db->trans_status() === FALSE)
				{
					$error = $this->db->error();
					throw new Exception("Error Database, Code : " . $error['code'] . " Message : " . $error['message']);
				}
				else
				{
					$this->db->trans_commit();
					$result->response = TRUE;
				}
			}
			catch(Exception $e)
			{
				$this->db->trans_rollback();
				$result->value = $e->getMessage();
			}
		}
		
		return $this->result_json($result);
	}
	
	/**	Pre-condition :
			- use your active record 'SET' function before run this function
	*/
	protected function _update($model, $function_name = '_update', $params = array(), $fields_validation = array(), $auto_set_value = TRUE, $resource = 'get_post')
	{
		$result = new stdClass();
		$result->response = FALSE;
		$result->value = NULL;
		$result->data = array();
		
		$this->$model->remove_fields_configuration(array($this->$model->primary_key));
		
		if (count($fields_validation) > 0)
			$this->form_validation->set_rules($fields_validation);
		else
			$this->form_validation->set_rules($this->$model->get_fields_configuration());
		
		if ($this->form_validation->run() == FALSE)
			$result->value = validation_errors();
		else
		{
			$this->db->trans_begin();
			
			try
			{
				if (!empty($this->$model->unique_fields))
					$this->validate_unique_fields($model, $this->$model->unique_fields, $params[0], $resource);
				
				if ($auto_set_value == TRUE)
					$this->set_model_values($model, $resource);
				
				$result->value = call_user_func_array(array($this->$model, $function_name), $params);
				
				if ($this->db->trans_status() === FALSE)
				{
					$error = $this->db->error();
					throw new Exception("Error Database, Code : " . $error['code'] . " Message : " . $error['message']);
				}
				else
				{
					$this->db->trans_commit();
					$result->response = TRUE;
				}
			}
			catch(Exception $e)
			{
				$this->db->trans_rollback();
				$result->value = $e->getMessage();
			}
		}
		
		return $this->result_json($result);
	}
	
	/**	Pre-condition :
			- use your active record 'SET' function before run this function
	*/
	protected function _delete($model, $function_name = '_delete', $params = array())
	{
		$result = new stdClass();
		$result->response = FALSE;
		$result->value = NULL;
		$result->data = array();
		
		$this->db->trans_begin();
		
		try
		{
			$result->value = call_user_func_array(array($this->$model, $function_name), $params);
			
			if ($this->db->trans_status() === FALSE)
			{
				$error = $this->db->error();
				throw new Exception("Error Database, Code : " . $error['code'] . " Message : " . $error['message']);
			}
			else
			{
				$this->db->trans_commit();
				$result->response = TRUE;
			}
		}
		catch(Exception $e)
		{
			$this->db->trans_rollback();
			$result->value = $e->getMessage();
		}
		
		return $this->result_json($result);
	}
	
	/**	Pre-condition :
			- use your active record 'SET' function before run this function
	*/
	protected function _execute($class, $function_name, $params = array(), $fields_validation = array(), $auto_set_value = FALSE, $resource = 'get_post')
	{
		$result = new stdClass();
		$result->response = FALSE;
		$result->value = NULL;
		$result->data = array();
		
		$this->form_validation->set_rules($fields_validation);
		if (count($fields_validation) > 0 && $this->form_validation->run() == FALSE)
			$result->value = validation_errors();
		else
		{
			$this->db->trans_begin();
			
			try
			{
				if ($auto_set_value == TRUE)
					$this->set_model_values($class, $resource);
				
				if ($class == 'this')
					$result->value = call_user_func_array(array($this, $function_name), $params);
				else
					$result->value = call_user_func_array(array($this->$class, $function_name), $params);
				
				if ($this->db->trans_status() === FALSE)
				{
					$error = $this->db->error();
					throw new Exception("Error Database, Code : " . $error['code'] . " Message : " . $error['message']);
				}
				else
				{
					$this->db->trans_commit();
					$result->response = TRUE;
				}
			}
			catch(Exception $e)
			{
				$this->db->trans_rollback();
				$result->value = $e->getMessage();
			}
		}
		
		return $this->result_json($result);
	}
	
	/** Utility
	*/
	protected function set_model_values($model, $resource = 'get_post')
	{
		foreach($this->$model->get_fields_configuration() as $field)
		{
			$field_value = $this->get_field_value($field['field'], $resource);
			if ($field_value === '' || $field_value == NULL)
				$field_value = $field['value'];
			$this->$model->modify_value_field_configuration($field['field'], $field_value);
		}
	}
	
	protected function get_field_value($field, $resource = 'get_post', $source = array())
	{
		$value = NULL;
		if ($resource == 'get_post')
		{
			$value = $this->input->get_post($field);
			if ($value === NULL)
				$value = NULL;
		}
		elseif ($resource == 'post')
		{
			$value = $this->input->post($field);
			if ($value === NULL)
				$value = NULL;
		}
		elseif ($resource == 'get')
		{
			$value = $this->input->get($field);
			if ($value === NULL)
				$value = NULL;
		}
		elseif ($resource == 'array')
		{
			$value = $source[$field];
			if ($value === FALSE)
				$value = NULL;
		}
		elseif ($resource == 'object')
		{
			$value = $source->$field;
			if ($value === FALSE)
				$value = NULL;
		}
		
		return $value;
	}
	
	protected function validate_unique_fields($model, $unique_fields, $except_id = NULL, $resource = 'get_post')
	{
		if (is_array($unique_fields))
		{
			$message_values = array();
			$unique_fields_value = array();
			foreach ($unique_fields as $_unique_fields)
			{
				if (is_array($_unique_fields))
				{
					$message_values_2 = array();
					$unique_fields_value_2 = array();
					foreach ($_unique_fields as $_unique_field)
					{
						$value = $this->get_field_value($_unique_field, $resource);
						if ($value != '')
						{
							$unique_fields_value_2[$_unique_field] = $value;
							$message_values_2[] = $unique_fields_value_2[$_unique_field];
						}
					}
					if ($this->$model->is_unique($unique_fields_value_2, $except_id) == FALSE)
						throw new Exception(str_replace('%s', implode(',', $message_values_2), "%s found duplicates"));
				}
				else
				{
					$value = $this->get_field_value($_unique_fields, $resource);
					if ($value != '')
					{
						$unique_fields_value[$_unique_fields] = $value;
						$message_values[] = $unique_fields_value[$_unique_fields];
					}
				}
			}
			
			if (count($unique_fields_value) > 0)
			{
				if ($this->$model->is_unique($unique_fields_value, $except_id) == FALSE)
					throw new Exception(str_replace('%s', implode(',', $message_values), "%s found duplicates"));
			}
		}
		else
		{
			$value = $this->get_field_value($unique_fields, $resource);
			if ($value != '')
			{
				if ($this->$model->is_unique($value, $except_id) == FALSE)
					throw new Exception(str_replace('%s', $value, "%s found duplicates"));
			}
		}
	}
	
	/**	Pre-condition :
			- use your active record 'SELECT' function before run this function
			- don't use $this->db->get function before run this function
	*/
	protected function _get($model, $function_name = '_get', $params = array())
	{
		$result = new stdClass();
		$result->response = FALSE;
		$result->value = NULL;
		$result->data = array();
		
		try
		{
			$result->data = call_user_func_array(array($this->$model, $function_name), $params);
			$result->response = TRUE;
		}
		catch(Exception $e)
		{
			$result->value = $e->getMessage();
		}
		
		return $this->result_json($result);
	}
		
	protected function _load_layout($content, $layout = NULL)
	{
		if ($layout != NULL)
			$this->_layout = $layout;
		
		if (!isset($content['title']))
			$content['title'] = $this->config->item('application_title');
		
		$content['message'] = $this->message;
		
		if ($this->_login_protected == TRUE)
		{
			$content['menus'] = $this->get_menu();
			$content['accesscontrols'] = $this->get_accesscontrols();
		}
		
		$this->load->view('layouts/'.$this->_layout, $content);
	}
	
	protected function get_menu()
	{
		return $this->session->userdata('menus');
	}
	
	protected function get_accesscontrols()
	{
		return $this->session->userdata('accesscontrols');
	}
	
	protected function result_json($response, $http_code = 200, $continue = FALSE)
	{
		return $this->response($response, $http_code, $continue);
	}
	
	public function response($data = null, $http_code = null, $continue = false)
    {
		if ($this->_ajax_style == TRUE)
		{
			$this->output
				->set_content_type('application/json')
				->set_status_header($http_code)
				->set_output(json_encode($data));
		}
		
		return $data;
	}
}