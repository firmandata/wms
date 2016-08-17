<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User extends MY_Controller 
{
	public function __construct()
	{
		parent::__construct(TRUE, 'backend');
	}
	
	public function index()
	{
		if (!is_authorized('system/user', 'index')) 
			access_denied();
		
		$data = array(
		);
		
		$content = array(
			'title'		=> "User",
			'content' 	=> $this->load->view('system/user/index', $data, TRUE)
		);
		$this->_load_layout($content);
	}
	
	public function get_list_json()
	{
		if (!is_authorized('system/user', 'index')) 
			access_denied();
		
		$this->db
			->select("usr.id, usr.username, usr.name, usr.email, usr.is_active")
			->from('sys_users usr');
		
		parent::_get_list_json();
	}
	
	public function form()
	{
		if (!is_authorized('system/user', 'index')) 
			access_denied();
		
		$form_action = $this->input->get_post('form_action');
		$id = $this->input->get_post('id');
		$record = NULL;
		if ($id !== NULL)
		{
			$table = $this->db
				->where('id', $id)
				->get('sys_users');
			if ($table->num_rows() > 0)
				$record = $table->first_row();
			else
				show_error("User not found.", 400);
		}
		
		if ($record !== NULL)
		{
			$table = $this->db
				->where('sys_user_id', $record->id)
				->get('sys_usergroup_users');
			$record->sys_usergroup_users = $table->result();
		}
		
		$data = array(
			'form_action'	=> $form_action,
			'record'		=> $record
		);
		$this->load->view('system/user/form', $data);
	}
	
	public function get_usergroup_list_json()
	{
		if (!is_authorized('system/user', 'index')) 
			access_denied();
		
		$this->db->from('sys_usergroups');
		
		parent::_get_list_json();
	}
	
	public function get_user_active_dropdown($element_name)
	{
		$list_active = array('' => '');
		$actives = $this->config->item('boolean');
		foreach ($actives as $active_key=>$active)
			$list_active[$active_key] = $active;
		$dropdown = form_dropdown($element_name, 
			$list_active
		);
		$this->output
			->set_output($dropdown);
	}
	
	public function insert()
	{
		if (!is_authorized('system/user', 'insert')) 
			access_denied();
		
		parent::_execute('this', 'user_add_and_assign_usergroups', 
			array(),
			array(
				array('field' => 'username', 'label' => 'Username', 'rules' => 'required'),
				array('field' => 'name', 'label' => 'Name', 'rules' => 'required'),
				array('field' => 'email', 'label' => 'Email', 'rules' => 'required|valid_email'),
				array('field' => 'password', 'label' => 'Password', 'rules' => 'required'),
				array('field' => 'password_confirm', 'label' => 'Password Confirm', 'rules' => 'required|matches[password]')
			)
		);
	}
	
	protected function user_add_and_assign_usergroups()
	{
		$this->load->library('system/lib_user');
		
		$user_id = $this->session->userdata('user_id');
		
		$data = new stdClass();
		$data->username = $this->input->post('username');
		$data->name = $this->input->post('name');
		$data->email = $this->input->post('email');
		$data->password = $this->input->post('password');
		$data->password_confirm = $this->input->post('password_confirm');
		$data->is_active = $this->input->post('is_active');
		$id = $this->lib_user->user_add($data, $user_id);
		
		$sys_usergroup_ids = $this->input->post('sys_usergroup_ids');
		if (!is_array($sys_usergroup_ids))
			$sys_usergroup_ids = array();
		
		// -- Add UserGroup User --
		if (!empty($sys_usergroup_ids) && is_array($sys_usergroup_ids))
		{
			$sys_usergroups = new Sys_usergroup();
			$sys_usergroups
				->where_in('id', $sys_usergroup_ids)
				->get();
			foreach ($sys_usergroups as $sys_usergroup)
			{
				$this->lib_user->usergroup_user_add($sys_usergroup->id, $id, $user_id);
			}
		}
		
		return $id;
	}
	
	public function update($id)
	{
		if (!is_authorized('system/user', 'update')) 
			access_denied();
		
		parent::_execute('this', 'user_update_and_replace_usergroups', 
			array($id), 
			array(
				array('field' => 'username', 'label' => 'Username', 'rules' => 'required'),
				array('field' => 'name', 'label' => 'Name', 'rules' => 'required'),
				array('field' => 'email', 'label' => 'Email', 'rules' => 'required|valid_email')
			)
		);
	}
	
	protected function user_update_and_replace_usergroups($id)
	{
		$this->load->library('system/lib_user');
		
		$user_id = $this->session->userdata('user_id');
		
		$data = new stdClass();
		$data->username = $this->input->post('username');
		$data->name = $this->input->post('name');
		$data->email = $this->input->post('email');
		$data->password = $this->input->post('password');
		$data->password_confirm = $this->input->post('password_confirm');
		$data->is_active = $this->input->post('is_active');
		$updated_result = $this->lib_user->user_update($id, $data, $user_id);
		
		// -- UserGroup Users --
		$sys_usergroup_ids = $this->input->post('sys_usergroup_ids');
		if (!is_array($sys_usergroup_ids))
			$sys_usergroup_ids = array();
		
		$sys_usergroup_users = new Sys_usergroup_user();
        $sys_usergroup_users
			->where('sys_user_id', $id)
			->get();
		// -- Add UserGroup Users --
		if (!empty($sys_usergroup_ids) && is_array($sys_usergroup_ids))
		{
			foreach ($sys_usergroup_ids as $sys_usergroup_id)
			{
				$is_found_new = TRUE;
				foreach ($sys_usergroup_users as $sys_usergroup_user)
				{
					if ($sys_usergroup_user->sys_usergroup_id == $sys_usergroup_id)
					{
						$is_found_new = FALSE;
						break;
					}
				}
				if ($is_found_new == TRUE)
				{
					$this->lib_user->usergroup_user_add($sys_usergroup_id, $id, $user_id);
				}
			}
		}
		// -- Remove UserGroup Users --
		foreach ($sys_usergroup_users as $sys_usergroup_user)
		{
			$is_found_delete = TRUE;
			if (!empty($sys_usergroup_ids) && is_array($sys_usergroup_ids))
			{
				foreach ($sys_usergroup_ids as $sys_usergroup_id)
				{
					if ($sys_usergroup_id == $sys_usergroup_user->sys_usergroup_id)
					{
						$is_found_delete = FALSE;
						break;
					}
				}
			}
			if ($is_found_delete == TRUE)
			{
				$this->lib_user->usergroup_user_remove($sys_usergroup_user->id, $user_id);
			}
		}
		
		return $updated_result;
	}
	
	public function delete($id)
	{
		if (!is_authorized('system/user', 'delete')) 
			access_denied();
		
		$this->load->library('system/lib_user');
		
		$user_id = $this->session->userdata('user_id');
		
		parent::_execute('lib_user', 'user_remove', array($id, $user_id));
	}

	public function profile()
	{
		$user_id = $this->session->userdata('user_id');
		$sys_user_table = $this->db
			->where('id', $user_id)
			->get('sys_users');
		if ($sys_user_table->num_rows() == 0)
			access_denied();
		else
		{
			$data = array(
				'user'	=> $sys_user_table->first_row()
			);
			
			$content = array(
				'title'		=> "Profile",
				'content' 	=> $this->load->view('system/user/profile', $data, TRUE)
			);
			$this->_load_layout($content);
		}
	}
	
	public function profile_update()
	{
		$this->load->library('system/lib_user');
		
		$user_id = $this->session->userdata('user_id');
		
		$data = new stdClass();
		$data->name = $this->input->post('name');
		$data->email = $this->input->post('email');
		$data->password = $this->input->post('password');
		$data->password_confirm = $this->input->post('password_confirm');
		
		parent::_execute('lib_user', 'user_update', 
			array($user_id, $data, $user_id), 
			array(
				array('field' => 'name', 'label' => 'Name', 'rules' => 'required'),
				array('field' => 'email', 'label' => 'Email', 'rules' => 'required|valid_email')
			)
		);
	}
}