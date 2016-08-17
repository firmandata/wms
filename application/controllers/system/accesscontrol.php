<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Accesscontrol extends MY_Controller 
{
	public function __construct()
	{
		parent::__construct(TRUE, 'backend');
	}
	
	public function index()
	{
		if (!is_authorized('system/accesscontrol', 'index')) 
			access_denied();
		
		$data = array(
		);
		
		$content = array(
			'title'		=> "Access Control",
			'content' 	=> $this->load->view('system/accesscontrol/index', $data, TRUE)
		);
		$this->_load_layout($content);
	}
	
	public function get_list_json()
	{
		if (!is_authorized('system/accesscontrol', 'index')) 
			access_denied();
		
		$this->db
			->select("usr_grp.id, usr_grp.name")
			->from('sys_usergroups usr_grp');
		
		parent::_get_list_json();
	}
	
	public function form()
	{
		if (!is_authorized('system/accesscontrol', 'index')) 
			access_denied();
		
		$form_action = $this->input->get_post('form_action');
		$id = $this->input->get_post('id');
		$record = NULL;
		if ($id !== NULL)
		{
			$table = $this->db
				->where('id', $id)
				->get('sys_usergroups');
			if ($table->num_rows() > 0)
				$record = $table->first_row();
			else
				show_error("Access Control not found.", 400);
		}
		
		$table = $this->db->get('sys_actions');
		$sys_actions = $table->result();
		
		$data = array(
			'form_action'	=> $form_action,
			'record'		=> $record,
			'sys_actions'	=> $sys_actions
		);
		$this->load->view('system/accesscontrol/form', $data);
	}
	
	public function get_control_action_list_json()
	{
		if (!is_authorized('system/accesscontrol', 'index')) 
			access_denied();
		
		$table = $this->db->get('sys_controls');
		$sys_controls = $table->result();
		
		$table = $this->db->get('sys_actions');
		$sys_actions = $table->result();
		
		$sys_accesscontrols = array();
		$sys_usergroup_id = $this->input->get_post('sys_usergroup_id');
		if ($sys_usergroup_id)
		{
			$table = $this->db
				->where('sys_usergroup_id', $sys_usergroup_id)
				->get('sys_accesscontrols');
			$sys_accesscontrols = $table->result();
		}
		
		$records = array();
		foreach ($sys_controls as $sys_control)
		{
			$record = new stdClass();
			$record->sys_control_id = $sys_control->id;
			$record->sys_control_name = $sys_control->name;
			foreach ($sys_actions as $sys_action)
			{
				$sys_action_id = $sys_action->id;
				$sys_action_name = $sys_action->name;
				$sys_action_allow_column = 'sys_action_allow_'.$sys_action_id;
				$sys_action_denied_column = 'sys_action_denied_'.$sys_action_id;
				$record->$sys_action_allow_column = 0;
				$record->$sys_action_denied_column = 0;
				foreach ($sys_accesscontrols as $sys_accesscontrol)
				{
					if ($sys_accesscontrol->sys_control_id == $sys_control->id && $sys_accesscontrol->sys_action_id == $sys_action->id)
					{
						if ($sys_accesscontrol->is_denied)
							$record->$sys_action_denied_column = 1;
						else
							$record->$sys_action_allow_column = 1;
						break;
					}
				}
			}
			$records[] = $record;
		}
		
		$result = new stdClass();
		$result->data = $records;
		$result->page = 1;
		$result->total = 1;
		$result->records = count($sys_controls);
		$result->user_data = new stdClass();
		
		$this->result_json($result);
	}
	
	public function form_control()
	{
		if (!is_authorized('system/accesscontrol', 'index')) 
			access_denied();
		
		$this->load->view('system/accesscontrol/control');
	}
	
	public function get_control_list_json()
	{
		if (!is_authorized('system/accesscontrol', 'index')) 
			access_denied();
		
		$this->db->from('sys_controls');
		
		parent::_get_list_json();
	}
	
	public function form_action()
	{
		if (!is_authorized('system/accesscontrol', 'index')) 
			access_denied();
		
		$this->load->view('system/accesscontrol/action');
	}
	
	public function get_action_list_json()
	{
		if (!is_authorized('system/accesscontrol', 'index')) 
			access_denied();
		
		$this->db->from('sys_actions');
		
		parent::_get_list_json();
	}
	
	public function insert()
	{
		if (!is_authorized('system/accesscontrol', 'insert')) 
			access_denied();
		
		parent::_execute('this', 'usergroup_add_and_assign_accesscontrols');
	}
	
	protected function usergroup_add_and_assign_accesscontrols()
	{
		$this->load->library('system/lib_user');
		
		$user_id = $this->session->userdata('user_id');
		
		$data = new stdClass();
		$data->name = $this->input->post('name');
		
		$id = $this->lib_user->usergroup_add($data, $user_id);
		
		$accesscontrols = $this->populate_accesscontrol();
		foreach ($accesscontrols as $accesscontrol)
		{
			$this->lib_user->accesscontrol_assign($id, $accesscontrol->sys_control_id, $accesscontrol->sys_action_id, $accesscontrol->is_denied, $user_id);
		}
		
		return $id;
	}
	
	public function update($id)
	{
		if (!is_authorized('system/accesscontrol', 'update')) 
			access_denied();
		
		parent::_execute('this', 'usergroup_update_and_assign_accesscontrols', array($id));
	}
	
	protected function usergroup_update_and_assign_accesscontrols($id)
	{
		$this->load->library('system/lib_user');
		
		$user_id = $this->session->userdata('user_id');
		
		$data = new stdClass();
		$data->name = $this->input->post('name');
		
		$result_updated = $this->lib_user->usergroup_update($id, $data, $user_id);
		
		$accesscontrols = $this->populate_accesscontrol();
		foreach ($accesscontrols as $accesscontrol)
		{
			$this->lib_user->accesscontrol_assign($id, $accesscontrol->sys_control_id, $accesscontrol->sys_action_id, $accesscontrol->is_denied, $user_id);
		}
		
		return $result_updated;
	}
	
	protected function populate_accesscontrol()
	{
		$accesscontrols = array();
		$accesscontrol_list = $this->input->get_post('accesscontrols');
		if ($accesscontrol_list !== FALSE && is_array($accesscontrol_list))
		{
			foreach ($accesscontrol_list as $sys_control_id=>$sys_actions)
			{
				foreach ($sys_actions as $sys_action_id=>$value)
				{
					$accesscontrol = new stdClass();
					$accesscontrol->sys_control_id = $sys_control_id;
					$accesscontrol->sys_action_id = $sys_action_id;
					if ($value == 'denied')
						$accesscontrol->is_denied = TRUE;
					else
						$accesscontrol->is_denied = FALSE;
					$accesscontrols[] = $accesscontrol;
				}
			}
		}
		
		return $accesscontrols;
	}
	
	public function delete($id)
	{
		if (!is_authorized('system/accesscontrol', 'delete')) 
			access_denied();
		
		$this->load->library('system/lib_user');
		
		$user_id = $this->session->userdata('user_id');
		
		parent::_execute('lib_user', 'usergroup_remove', array($id, $user_id));
	}
}