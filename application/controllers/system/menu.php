<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Menu extends MY_Controller 
{
	public function __construct()
	{
		parent::__construct(TRUE, 'backend');
	}
	
	public function index()
	{
		if (!is_authorized('system/menu', 'index')) 
			access_denied();
		
		$data = array(
		);
		
		$content = array(
			'title'		=> 'Menu Configuration',
			'content' 	=> $this->load->view('system/menu/index', $data, TRUE)
		);
		$this->_load_layout($content);
	}
	
	public function get_list_json()
	{
		if (!is_authorized('system/menu', 'index')) 
			access_denied();
		
		$this->load->library('system/lib_menu');
		
		$result = new stdClass();
		$result->data = $this->lib_menu->menu_get_adjacency();
		$result->page = 1;
		$result->total = 1;
		$result->records = count($result->data);
		$this->result_json($result);
	}
	
	public function form()
	{
		if (!is_authorized('system/menu', 'index')) 
			access_denied();
		
		$form_action = $this->input->get_post('form_action');
		$id = $this->input->get_post('id');
		$record = NULL;
		if ($id !== NULL)
		{
			$table = $this->db
				->where('id', $id)
				->get('sys_menus');
			if ($table->num_rows() > 0)
				$record = $table->first_row();
			else
				show_error("Menu not found.", 400);
		}
		$data = array(
			'form_action'	=> $form_action,
			'record'		=> $record
		);
		$this->load->view('system/menu/form', $data);
	}
	
	public function get_control_list_json()
	{
		if (!is_authorized('system/menu', 'index')) 
			access_denied();
		
		$this->db->from('sys_controls');
		
		parent::_get_list_json();
	}
	
	public function get_action_list_json()
	{
		if (!is_authorized('system/menu', 'index')) 
			access_denied();
		
		$this->db->from('sys_actions');
		
		parent::_get_list_json();
	}
	
	public function insert()
	{
		if (!is_authorized('system/menu', 'insert')) 
			access_denied();
		
		$this->load->library('system/lib_menu');
		
		$user_id = $this->session->userdata('user_id');
		
		$data = new stdClass();
		$data->name = $this->input->post('name');
		$data->sequence = $this->input->post('sequence');
		$data->url = $this->input->post('url');
		$data->css = $this->input->post('css');
		
		$parent_id = $this->input->post('parent_id');
		$sys_control_id = $this->input->post('sys_control_id');
		$sys_action_id = $this->input->post('sys_action_id');
		
		parent::_execute('lib_menu', 'menu_add', 
			array($data, $parent_id, $sys_control_id, $sys_action_id, $user_id), 
			array(
				array('field' => 'name', 'label' => 'Name', 'rules' => 'required'),
				array('field' => 'sequence', 'label' => 'Sequence', 'rules' => 'required|integer')
			)
		);
	}
	
	public function update($id)
	{
		if (!is_authorized('system/menu', 'update')) 
			access_denied();
		
		$this->load->library('system/lib_menu');
		
		$user_id = $this->session->userdata('user_id');
		
		$data = new stdClass();
		$data->name = $this->input->post('name');
		$data->sequence = $this->input->post('sequence');
		$data->url = $this->input->post('url');
		$data->css = $this->input->post('css');
		
		$parent_id = $this->input->post('parent_id');
		$sys_control_id = $this->input->post('sys_control_id');
		$sys_action_id = $this->input->post('sys_action_id');
		
		parent::_execute('lib_menu', 'menu_update', 
			array($id, $data, $parent_id, $sys_control_id, $sys_action_id, $user_id), 
			array(
				array('field' => 'name', 'label' => 'Name', 'rules' => 'required'),
				array('field' => 'sequence', 'label' => 'Sequence', 'rules' => 'required|integer')
			)
		);
	}
	
	public function delete($id)
	{
		if (!is_authorized('system/menu', 'delete')) 
			access_denied();
		
		$this->load->library('system/lib_menu');
		
		$user_id = $this->session->userdata('user_id');
		
		parent::_execute('lib_menu', 'menu_remove', array($id, $user_id));
	}
}