<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Department extends MY_Controller 
{
	public function __construct()
	{
		parent::__construct(TRUE, 'backend');
		
		$this->load->library('core/lib_region');
	}
	
	public function index()
	{
		if (!is_authorized('core/department', 'index')) 
			access_denied();
		
		$data = array(
		);
		
		$content = array(
			'title'		=> "Department",
			'content' 	=> $this->load->view('core/department/index', $data, TRUE)
		);
		$this->_load_layout($content);
	}
	
	public function form()
	{
		if (!is_authorized('core/department', 'index')) 
			access_denied();
		
		$form_action = $this->input->get_post('form_action');
		$id = $this->input->get_post('id');
		$record = NULL;
		if ($id !== NULL)
		{
			$table = $this->db
				->select("dep.id, dep.code, dep.name")
				->from('c_departments dep')
				->where('dep.id', $id)
				->get();
			if ($table->num_rows() > 0)
				$record = $table->first_row();
			else
				show_error("Department not found", 400);
		}
		
		$data = array(
			'form_action'	=> $form_action,
			'record'		=> $record
		);
		$this->load->view('core/department/form', $data);
	}
	
	public function get_list_json()
	{
		if (!is_authorized('core/department', 'index')) 
			access_denied();
		
		$this->db
			->select("dep.id, dep.code, dep.name")
			->from('c_departments dep');
		
		parent::_get_list_json();
	}
	
	public function insert()
	{
		if (!is_authorized('core/department', 'insert'))
			access_denied();
		
		$user_id = $this->session->userdata('user_id');
		
		$data = new stdClass();
		$data->code = $this->input->post('code');
		$data->name = $this->input->post('name');
		
		parent::_execute('lib_region', 'department_add', 
			array($data, $user_id), 
			array(
				array('field' => 'code', 'label' => 'Code', 'rules' => 'required'),
				array('field' => 'name', 'label' => 'Name', 'rules' => 'required')
			)
		);
	}
	
	public function update($id)
	{
		if (!is_authorized('core/department', 'insert'))
			access_denied();
		
		$user_id = $this->session->userdata('user_id');
		
		$data = new stdClass();
		$data->code = $this->input->post('code');
		$data->name = $this->input->post('name');
		
		parent::_execute('lib_region', 'department_update', 
			array($id, $data, $user_id), 
			array(
				array('field' => 'code', 'label' => 'Code', 'rules' => 'required'),
				array('field' => 'name', 'label' => 'Name', 'rules' => 'required')
			)
		);
	}
	
	public function delete($id)
	{
		if (!is_authorized('core/department', 'delete')) 
			access_denied();
		
		$user_id = $this->session->userdata('user_id');
		
		parent::_execute('lib_region', 'department_remove', array($id, $user_id));
	}
}