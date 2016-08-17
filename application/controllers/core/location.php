<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Location extends MY_Controller 
{
	public function __construct()
	{
		parent::__construct(TRUE, 'backend');
		
		$this->load->library('core/lib_region');
	}
	
	public function index()
	{
		if (!is_authorized('core/location', 'index')) 
			access_denied();
		
		$data = array(
		);
		
		$content = array(
			'title'		=> "Location",
			'content' 	=> $this->load->view('core/location/index', $data, TRUE)
		);
		$this->_load_layout($content);
	}
	
	public function form()
	{
		if (!is_authorized('core/location', 'index')) 
			access_denied();
		
		$form_action = $this->input->get_post('form_action');
		$id = $this->input->get_post('id');
		$record = NULL;
		if ($id !== NULL)
		{
			$table = $this->db
				->select("loc.id, loc.code, loc.name")
				->select("loc.c_region_id")
				->select_concat(array("rgn.name", "' ('", "rgn.code", "')'"), 'c_region_text')
				->select("loc.c_department_id")
				->select_concat(array("dep.name", "' ('", "dep.code", "')'"), 'c_department_text')
				->select("loc.address_floor")
				->from('c_locations loc')
				->join('c_regions rgn', "rgn.id = loc.c_region_id")
				->join('c_departments dep', "dep.id = loc.c_department_id", 'left')
				->where('loc.id', $id)
				->get();
			if ($table->num_rows() > 0)
				$record = $table->first_row();
			else
				show_error("Location not found", 400);
		}
		
		$data = array(
			'form_action'	=> $form_action,
			'record'		=> $record
		);
		$this->load->view('core/location/form', $data);
	}
	
	public function get_list_json()
	{
		if (!is_authorized('core/location', 'index')) 
			access_denied();
		
		$this->db
			->select("loc.id, loc.code, loc.name")
			->select("loc.c_region_id, rgn.code c_region_code, rgn.name c_region_name")
			->select("loc.c_department_id, dep.code c_department_code, dep.name c_department_name")
			->select("loc.address_floor")
			->from('c_locations loc')
			->join('c_regions rgn', "rgn.id = loc.c_region_id")
			->join('c_departments dep', "dep.id = loc.c_department_id", 'left');
		
		parent::_get_list_json();
	}
	
	public function insert()
	{
		if (!is_authorized('core/location', 'insert'))
			access_denied();
		
		$user_id = $this->session->userdata('user_id');
		
		$data = new stdClass();
		$data->code = $this->input->post('code');
		$data->name = $this->input->post('name');
		
		$data->c_region_id = $this->input->post('c_region_id');
		$data->c_department_id = $this->input->post('c_department_id');
		
		$data->address_floor = $this->input->post('address_floor');
		
		parent::_execute('lib_region', 'location_add', 
			array($data, $user_id), 
			array(
				array('field' => 'code', 'label' => 'Code', 'rules' => 'required'),
				array('field' => 'name', 'label' => 'Name', 'rules' => 'required'),
				array('field' => 'c_region_id', 'label' => 'Region', 'rules' => 'required')
			)
		);
	}
	
	public function update($id)
	{
		if (!is_authorized('core/location', 'insert'))
			access_denied();
		
		$user_id = $this->session->userdata('user_id');
		
		$data = new stdClass();
		$data->code = $this->input->post('code');
		$data->name = $this->input->post('name');
		
		$data->c_region_id = $this->input->post('c_region_id');
		$data->c_department_id = $this->input->post('c_department_id');
		
		$data->address_floor = $this->input->post('address_floor');
		
		parent::_execute('lib_region', 'location_update', 
			array($id, $data, $user_id), 
			array(
				array('field' => 'code', 'label' => 'Code', 'rules' => 'required'),
				array('field' => 'name', 'label' => 'Name', 'rules' => 'required'),
				array('field' => 'c_region_id', 'label' => 'Region', 'rules' => 'required')
			)
		);
	}
	
	public function delete($id)
	{
		if (!is_authorized('core/location', 'delete')) 
			access_denied();
		
		$user_id = $this->session->userdata('user_id');
		
		parent::_execute('lib_region', 'location_remove', array($id, $user_id));
	}
	
	public function get_region_autocomplete_list_json()
	{
		if (!is_authorized('core/location', 'index')) 
			access_denied();
		
		$keywords = $this->input->get_post('term');
		
		$this->db
			->select("id")
			->select_concat(array("name", "' ('", "code", "')'"), 'value')
			->select_concat(array("name", "' ('", "code", "')'"), 'label')
			->from('c_regions');
		
		if ($keywords)
			$this->db->where($this->db->concat(array("name", "' ('", "code", "')'")) . " LIKE '%" . $this->db->escape_like_str($keywords) . "%'", NULL, FALSE);
		
		parent::_get_list_autocomplete_json();
	}
	
	public function get_department_autocomplete_list_json()
	{
		if (!is_authorized('core/location', 'index')) 
			access_denied();
		
		$keywords = $this->input->get_post('term');
		
		$this->db
			->select("id")
			->select_concat(array("name", "' ('", "code", "')'"), 'value')
			->select_concat(array("name", "' ('", "code", "')'"), 'label')
			->from('c_departments');
		
		if ($keywords)
			$this->db->where($this->db->concat(array("name", "' ('", "code", "')'")) . " LIKE '%" . $this->db->escape_like_str($keywords) . "%'", NULL, FALSE);
		
		parent::_get_list_autocomplete_json();
	}
}