<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Category extends MY_Controller 
{
	public function __construct()
	{
		parent::__construct(TRUE, 'backend');
		
		$this->load->library('material/lib_material');
	}
	
	public function index()
	{
		if (!is_authorized('material/category', 'index')) 
			access_denied();
		
		$data = array(
		);
		
		$content = array(
			'title'		=> "Category",
			'content' 	=> $this->load->view('material/category/index', $data, TRUE)
		);
		$this->_load_layout($content);
	}
	
	public function index_simple()
	{
		if (!is_authorized('material/category', 'index')) 
			access_denied();
		
		$this->load->view('material/category/index_simple');
	}
	
	public function get_list_json()
	{
		if (!is_authorized('material/category', 'index')) 
			access_denied();
		
		$this->db
			->select("id, code, name")
			->from('m_categories');
		
		parent::_get_list_json();
	}
	
	public function insert()
	{
		if (!is_authorized('material/category', 'insert'))
			access_denied();
		
		$user_id = $this->session->userdata('user_id');
		
		$data = new stdClass();
		$data->code = $this->input->post('code');
		$data->name = $this->input->post('name');
		
		parent::_execute('lib_material', 'category_add', 
			array($data, $user_id), 
			array(
				array('field' => 'code', 'label' => 'Code', 'rules' => 'required'),
				array('field' => 'name', 'label' => 'Name', 'rules' => 'required')
			)
		);
	}
	
	public function update($id)
	{
		if (!is_authorized('material/category', 'insert'))
			access_denied();
		
		$user_id = $this->session->userdata('user_id');
		
		$data = new stdClass();
		$data->code = $this->input->post('code');
		$data->name = $this->input->post('name');
		
		parent::_execute('lib_material', 'category_update', 
			array($id, $data, $user_id), 
			array(
				array('field' => 'code', 'label' => 'Code', 'rules' => 'required'),
				array('field' => 'name', 'label' => 'Name', 'rules' => 'required')
			)
		);
	}
	
	public function delete($id)
	{
		if (!is_authorized('material/category', 'delete')) 
			access_denied();
		
		$user_id = $this->session->userdata('user_id');
		
		parent::_execute('lib_material', 'category_remove', array($id, $user_id));
	}
}