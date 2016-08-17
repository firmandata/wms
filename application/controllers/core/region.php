<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Region extends MY_Controller 
{
	public function __construct()
	{
		parent::__construct(TRUE, 'backend');
		
		$this->load->library('core/lib_region');
	}
	
	public function index()
	{
		if (!is_authorized('core/region', 'index')) 
			access_denied();
		
		$data = array(
		);
		
		$content = array(
			'title'		=> "Region",
			'content' 	=> $this->load->view('core/region/index', $data, TRUE)
		);
		$this->_load_layout($content);
	}
	
	public function form()
	{
		if (!is_authorized('core/region', 'index')) 
			access_denied();
		
		$form_action = $this->input->get_post('form_action');
		$id = $this->input->get_post('id');
		$record = NULL;
		if ($id !== NULL)
		{
			$table = $this->db
				->select("rgn.id, rgn.code, rgn.name")
				->select("rgn.phone_no, rgn.fax_no, rgn.address, rgn.address_city, rgn.notes")
				->from('c_regions rgn')
				->where('rgn.id', $id)
				->get();
			if ($table->num_rows() > 0)
				$record = $table->first_row();
			else
				show_error("Region not found", 400);
		}
		
		$data = array(
			'form_action'	=> $form_action,
			'record'		=> $record
		);
		$this->load->view('core/region/form', $data);
	}
	
	public function get_list_json()
	{
		if (!is_authorized('core/region', 'index')) 
			access_denied();
		
		$this->db
			->select("rgn.id, rgn.code, rgn.name")
			->select("rgn.phone_no, rgn.fax_no, rgn.address, rgn.address_city, rgn.notes")
			->from('c_regions rgn');
		
		parent::_get_list_json();
	}
	
	public function insert()
	{
		if (!is_authorized('core/region', 'insert'))
			access_denied();
		
		$user_id = $this->session->userdata('user_id');
		
		$data = new stdClass();
		$data->code = $this->input->post('code');
		$data->name = $this->input->post('name');
		$data->phone_no = $this->input->post('phone_no');
		$data->fax_no = $this->input->post('fax_no');
		$data->address = $this->input->post('address');
		$data->address_city = $this->input->post('address_city');
		$data->notes = $this->input->post('notes');
		
		parent::_execute('lib_region', 'region_add', 
			array($data, $user_id), 
			array(
				array('field' => 'code', 'label' => 'Code', 'rules' => 'required'),
				array('field' => 'name', 'label' => 'Name', 'rules' => 'required')
			)
		);
	}
	
	public function update($id)
	{
		if (!is_authorized('core/region', 'insert'))
			access_denied();
		
		$user_id = $this->session->userdata('user_id');
		
		$data = new stdClass();
		$data->code = $this->input->post('code');
		$data->name = $this->input->post('name');
		$data->phone_no = $this->input->post('phone_no');
		$data->fax_no = $this->input->post('fax_no');
		$data->address = $this->input->post('address');
		$data->address_city = $this->input->post('address_city');
		$data->notes = $this->input->post('notes');
		
		parent::_execute('lib_region', 'region_update', 
			array($id, $data, $user_id), 
			array(
				array('field' => 'code', 'label' => 'Code', 'rules' => 'required'),
				array('field' => 'name', 'label' => 'Name', 'rules' => 'required')
			)
		);
	}
	
	public function delete($id)
	{
		if (!is_authorized('core/region', 'delete')) 
			access_denied();
		
		$user_id = $this->session->userdata('user_id');
		
		parent::_execute('lib_region', 'region_remove', array($id, $user_id));
	}
}