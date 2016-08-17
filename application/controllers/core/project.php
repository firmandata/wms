<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Project extends MY_Controller 
{
	public function __construct()
	{
		parent::__construct(TRUE, 'backend');
	}
	
	public function index()
	{
		if (!is_authorized('core/project', 'index')) 
			access_denied();
		
		$data = array(
		);
		
		$content = array(
			'title'		=> "Project",
			'content' 	=> $this->load->view('core/project/index', $data, TRUE)
		);
		$this->_load_layout($content);
	}
	
	public function get_list_json()
	{
		if (!is_authorized('core/project', 'index')) 
			access_denied();
		
		$this->db
			->select("pro.id, pro.code, pro.name, pro.category, pro.pic")
			->select("pro.c_businesspartner_id, bp.name c_businesspartner_name")
			->from('c_projects pro')
			->join('c_businesspartners bp', "bp.id = pro.c_businesspartner_id", 'left');
		
		parent::_get_list_json();
	}
	
	public function form()
	{
		if (!is_authorized('core/project', 'index')) 
			access_denied();
		
		$form_action = $this->input->get_post('form_action');
		$id = $this->input->get_post('id');
		$record = NULL;
		if ($id !== NULL)
		{
			$table = $this->db
				->select("pro.id, pro.code, pro.name, pro.category, pro.pic")
				->select("pro.c_businesspartner_id, bp.code c_businesspartner_code, bp.name c_businesspartner_name")
				->select_concat(array("bp.name", "' ('", "bp.code", "')'"), 'c_businesspartner_text')
				->from('c_projects pro')
				->join('c_businesspartners bp', "bp.id = pro.c_businesspartner_id", 'left')
				->where('pro.id', $id)
				->get();
			if ($table->num_rows() > 0)
				$record = $table->first_row();
			else
				show_error("Project not found", 400);
		}
		
		if ($record !== NULL)
		{
			$table = $this->db
				->where('c_project_id', $record->id)
				->get('cus_c_project_sys_usergroups');
			$record->cus_c_project_sys_usergroups = $table->result();
		}
		
		$data = array(
			'form_action'	=> $form_action,
			'record'		=> $record
		);
		$this->load->view('core/project/form', $data);
	}
	
	public function get_usergroup_list_json()
	{
		if (!is_authorized('core/project', 'index')) 
			access_denied();
		
		$this->db->from('sys_usergroups');
		
		parent::_get_list_json();
	}
	
	public function get_businesspartner_autocomplete_list_json()
	{
		if (!is_authorized('core/orderin', 'index')) 
			access_denied();
		
		$keywords = $this->input->get_post('term');
		
		$this->db
			->select("id")
			->select_concat(array("name", "' ('", "code", "')'"), 'value')
			->select_concat(array("name", "' ('", "code", "')'"), 'label')
			->from('c_businesspartners');
		
		if ($keywords)
			$this->db->where($this->db->concat(array("name", "' ('", "code", "')'")) . " LIKE '%" . $this->db->escape_like_str($keywords) . "%'", NULL, FALSE);
		
		parent::_get_list_autocomplete_json();
	}
	
	public function insert()
	{
		if (!is_authorized('core/project', 'insert')) 
			access_denied();
		
		parent::_execute('this', 'project_add_and_assign_usergroups', 
			array(), 
			array(
				array('field' => 'code', 'label' => 'Code', 'rules' => 'required'),
				array('field' => 'name', 'label' => 'Name', 'rules' => 'required')
			)
		);
	}
	
	protected function project_add_and_assign_usergroups()
	{
		$this->load->library('core/lib_project');
		
		$user_id = $this->session->userdata('user_id');
		
		$c_businesspartner_id = $this->input->post('c_businesspartner_id');
		
		$data = new stdClass();
		$data->c_businesspartner_id = $c_businesspartner_id;
		$data->code = $this->input->post('code');
		$data->name = $this->input->post('name');
		$data->category = $this->input->post('category');
		$data->pic = $this->input->post('pic');
		$id = $this->lib_project->project_add($data, $user_id);
		
		$sys_usergroup_ids = $this->input->post('sys_usergroup_ids');
		if (!is_array($sys_usergroup_ids))
			$sys_usergroup_ids = array();
		
		$this->load->library('custom/lib_custom');
		
		// -- Add UserGroup Project --
		if (!empty($sys_usergroup_ids) && is_array($sys_usergroup_ids))
		{
			$sys_usergroups = new Sys_usergroup();
			$sys_usergroups
				->where_in('id', $sys_usergroup_ids)
				->get();
			foreach ($sys_usergroups as $sys_usergroup)
			{
				$this->lib_custom->project_usergroup_add($sys_usergroup->id, $id, $user_id);
			}
		}
		
		return $id;
	}
	
	public function update($id)
	{
		if (!is_authorized('core/project', 'update')) 
			access_denied();
		
		parent::_execute('this', 'user_update_and_replace_usergroups', 
			array($id), 
			array(
				array('field' => 'code', 'label' => 'Code', 'rules' => 'required'),
				array('field' => 'name', 'label' => 'Name', 'rules' => 'required')
			)
		);
	}
	
	protected function user_update_and_replace_usergroups($id)
	{
		$this->load->library('core/lib_project');
		
		$user_id = $this->session->userdata('user_id');
		
		$c_businesspartner_id = $this->input->post('c_businesspartner_id');
		
		$data = new stdClass();
		$data->c_businesspartner_id = $c_businesspartner_id;
		$data->code = $this->input->post('code');
		$data->name = $this->input->post('name');
		$data->category = $this->input->post('category');
		$data->pic = $this->input->post('pic');
		$updated_result = $this->lib_project->project_update($id, $data, $user_id);
		
		// -- UserGroup Projects --
		$sys_usergroup_ids = $this->input->post('sys_usergroup_ids');
		if (!is_array($sys_usergroup_ids))
			$sys_usergroup_ids = array();
		
		$cus_c_project_sys_usergroups = new Cus_c_project_sys_usergroup();
        $cus_c_project_sys_usergroups
			->where('c_project_id', $id)
			->get();
		
		$this->load->library('custom/lib_custom');
		
		// -- Add UserGroup Projects --
		if (!empty($sys_usergroup_ids) && is_array($sys_usergroup_ids))
		{
			foreach ($sys_usergroup_ids as $sys_usergroup_id)
			{
				$is_found_new = TRUE;
				foreach ($cus_c_project_sys_usergroups as $cus_c_project_sys_usergroup)
				{
					if ($cus_c_project_sys_usergroup->sys_usergroup_id == $sys_usergroup_id)
					{
						$is_found_new = FALSE;
						break;
					}
				}
				if ($is_found_new == TRUE)
				{
					$this->lib_custom->project_usergroup_add($sys_usergroup_id, $id, $user_id);
				}
			}
		}
		// -- Remove UserGroup Projects --
		foreach ($cus_c_project_sys_usergroups as $cus_c_project_sys_usergroup)
		{
			$is_found_delete = TRUE;
			if (!empty($sys_usergroup_ids) && is_array($sys_usergroup_ids))
			{
				foreach ($sys_usergroup_ids as $sys_usergroup_id)
				{
					if ($sys_usergroup_id == $cus_c_project_sys_usergroup->sys_usergroup_id)
					{
						$is_found_delete = FALSE;
						break;
					}
				}
			}
			if ($is_found_delete == TRUE)
			{
				$this->lib_custom->project_usergroup_remove($cus_c_project_sys_usergroup->id, $user_id);
			}
		}
		
		return $updated_result;
	}
	
	public function delete($id)
	{
		if (!is_authorized('core/project', 'delete')) 
			access_denied();
		
		$this->load->library('core/lib_project');
		
		$user_id = $this->session->userdata('user_id');
		
		parent::_execute('lib_project', 'project_remove', array($id, $user_id));
	}
}