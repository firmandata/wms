<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Businesspartner extends MY_Controller 
{
	public function __construct()
	{
		parent::__construct(TRUE, 'backend');
		
		$this->load->library('core/lib_business');
	}
	
	public function index()
	{
		if (!is_authorized('core/businesspartner', 'index')) 
			access_denied();
		
		$data = array(
		);
		
		$content = array(
			'title'		=> "Business Partner",
			'content' 	=> $this->load->view('core/businesspartner/index', $data, TRUE)
		);
		$this->_load_layout($content);
	}
	
	public function index_simple()
	{
		if (!is_authorized('core/businesspartner', 'index')) 
			access_denied();
		
		$this->load->view('core/businesspartner/index_simple');
	}
	
	public function get_list_json()
	{
		if (!is_authorized('core/businesspartner', 'index')) 
			access_denied();
		
		$this->db
			->select("id, code, name, address")
			->select("phone_no, fax_no")
			->select("type, model, credit_limit")
			->select("pic, notes")
			->from('c_businesspartners');
		
		parent::_get_list_json();
	}
	
	public function insert()
	{
		if (!is_authorized('core/businesspartner', 'insert'))
			access_denied();
		
		$user_id = $this->session->userdata('user_id');
		
		$data = new stdClass();
		$data->code = $this->input->post('code');
		$data->name = $this->input->post('name');
		$data->address = $this->input->post('address');
		$data->phone_no = $this->input->post('phone_no');
		$data->fax_no = $this->input->post('fax_no');
		$data->type = $this->input->post('type');
		$data->model = $this->input->post('model');
		$data->credit_limit = $this->input->post('credit_limit');
		$data->pic = $this->input->post('pic');
		$data->notes = $this->input->post('notes');
		
		parent::_execute('lib_business', 'businesspartner_add', 
			array($data, $user_id), 
			array(
				array('field' => 'code', 'label' => 'Code', 'rules' => 'required'),
				array('field' => 'name', 'label' => 'Name', 'rules' => 'required'),
				array('field' => 'credit_limit', 'label' => 'Credit Limit', 'rules' => 'required|numeric')
			)
		);
	}
	
	public function update($id)
	{
		if (!is_authorized('core/businesspartner', 'insert'))
			access_denied();
		
		$user_id = $this->session->userdata('user_id');
		
		$data = new stdClass();
		$data->code = $this->input->post('code');
		$data->name = $this->input->post('name');
		$data->address = $this->input->post('address');
		$data->phone_no = $this->input->post('phone_no');
		$data->fax_no = $this->input->post('fax_no');
		$data->type = $this->input->post('type');
		$data->model = $this->input->post('model');
		$data->credit_limit = $this->input->post('credit_limit');
		$data->pic = $this->input->post('pic');
		$data->notes = $this->input->post('notes');
		
		parent::_execute('lib_business', 'businesspartner_update', 
			array($id, $data, $user_id), 
			array(
				array('field' => 'code', 'label' => 'Code', 'rules' => 'required'),
				array('field' => 'name', 'label' => 'Name', 'rules' => 'required'),
				array('field' => 'credit_limit', 'label' => 'Credit Limit', 'rules' => 'required|numeric')
			)
		);
	}
	
	public function delete($id)
	{
		if (!is_authorized('core/businesspartner', 'delete')) 
			access_denied();
		
		$user_id = $this->session->userdata('user_id');
		
		parent::_execute('lib_business', 'businesspartner_remove', array($id, $user_id));
	}
}