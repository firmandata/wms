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
	
	public function form()
	{
		if (!is_authorized('core/businesspartner', 'index')) 
			access_denied();
		
		$form_action = $this->input->get_post('form_action');
		$id = $this->input->get_post('id');
		$record = NULL;
		if ($id !== NULL)
		{
			$table = $this->db
				->select("bp.id, bp.code, bp.name, bp.initial_name, bp.address")
				->select("bp.phone_no, bp.fax_no")
				->select("bp.type, bp.model, bp.credit_limit")
				->select("bp.pic, bp.personal_position, bp.notes")
				->select("bp.c_region_id")
				->select_concat(array("rgn.name", "' ('", "rgn.code", "')'"), 'c_region_text')
				->select("bp.c_department_id")
				->select_concat(array("dep.name", "' ('", "dep.code", "')'"), 'c_department_text')
				->from('c_businesspartners bp')
				->join('c_regions rgn', "rgn.id = bp.c_region_id", 'left')
				->join('c_departments dep', "dep.id = bp.c_department_id", 'left')
				->where('bp.id', $id)
				->get();
			if ($table->num_rows() > 0)
				$record = $table->first_row();
			else
				show_error("Business partner not found", 400);
		}
		
		$data = array(
			'form_action'	=> $form_action,
			'record'		=> $record
		);
		$this->load->view('core/businesspartner/form', $data);
	}
	
	public function get_list_json()
	{
		if (!is_authorized('core/businesspartner', 'index')) 
			access_denied();
		
		$this->db
			->select("bp.id, bp.code, bp.name, bp.initial_name, bp.address")
			->select("bp.phone_no, bp.fax_no")
			->select("bp.type, bp.model, bp.credit_limit")
			->select("bp.pic, bp.personal_position, bp.notes")
			->select("bp.c_region_id, rgn.code c_region_code, rgn.name c_region_name")
			->select("bp.c_department_id, dep.code c_department_code, dep.name c_department_name")
			->from('c_businesspartners bp')
			->join('c_regions rgn', "rgn.id = bp.c_region_id", 'left')
			->join('c_departments dep', "dep.id = bp.c_department_id", 'left');
		
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
		$data->initial_name = $this->input->post('initial_name');
		$data->address = $this->input->post('address');
		$data->phone_no = $this->input->post('phone_no');
		$data->fax_no = $this->input->post('fax_no');
		$data->type = $this->input->post('type');
		$data->model = $this->input->post('model');
		$data->credit_limit = $this->input->post('credit_limit');
		$data->pic = $this->input->post('pic');
		$data->personal_position = $this->input->post('personal_position');
		$data->notes = $this->input->post('notes');
		
		$data->c_region_id = $this->input->post('c_region_id');
		$data->c_department_id = $this->input->post('c_department_id');
		
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
		$data->initial_name = $this->input->post('initial_name');
		$data->address = $this->input->post('address');
		$data->phone_no = $this->input->post('phone_no');
		$data->fax_no = $this->input->post('fax_no');
		$data->type = $this->input->post('type');
		$data->model = $this->input->post('model');
		$data->credit_limit = $this->input->post('credit_limit');
		$data->pic = $this->input->post('pic');
		$data->personal_position = $this->input->post('personal_position');
		$data->notes = $this->input->post('notes');
		
		$data->c_region_id = $this->input->post('c_region_id');
		$data->c_department_id = $this->input->post('c_department_id');
		
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
	
	public function get_region_autocomplete_list_json()
	{
		if (!is_authorized('core/businesspartner', 'index')) 
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
		if (!is_authorized('core/businesspartner', 'index')) 
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