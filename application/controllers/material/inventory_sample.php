<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Inventory_sample extends MY_Controller 
{
	private $c_project_ids;
	
	public function __construct()
	{
		parent::__construct(TRUE, 'backend');
		
		$this->load->library('custom/lib_custom');
		
		$user_id = $this->session->userdata('user_id');
		$this->c_project_ids = $this->lib_custom->project_get_ids($user_id);
	}
	
	public function index()
	{
		if (!is_authorized('material/inventory_sample', 'index')) 
			access_denied();
		
		$data = array(
		);
		
		$content = array(
			'title'		=> "Inventory Sample",
			'content' 	=> $this->load->view('material/inventory_sample/index', $data, TRUE)
		);
		$this->_load_layout($content);
	}
	
	public function get_list_json()
	{
		if (!is_authorized('material/inventory_sample', 'index')) 
			access_denied();
		
		$this->load->helper('date');
		
		$from_month = $this->input->get_post('from_month');
		$from_year = $this->input->get_post('from_year');
		
		$to_month = $this->input->get_post('to_month');
		$to_year = $this->input->get_post('to_year');
		
		$this->db
			->select("isa.id, isa.code, isa.sampling_date, isa.supervisor")
			->select("isai.c_project_id, prj.name c_project_name")
			->from('m_inventory_samples isa')
			->join('m_inventory_sampledetails isad', "isad.m_inventory_sample_id = isa.id", 'left')
			->join('m_inventory_sampleinventories isai', "isai.m_inventory_sampledetail_id = isad.id", 'left')
			->join('c_projects prj', "prj.id = isai.c_project_id", 'left')
			->group_by(
				array(
					'isa.id', 'isa.code', 'isa.sampling_date', 'isa.supervisor',
					'isai.c_project_id', 'prj.name'
				)
			);
		$this->db->where("isa.sampling_date >=", date('Y-m-d', mktime(0, 0, 0, $from_month, 1, $from_year)));
		$this->db->where("isa.sampling_date <=", add_date(date('Y-m-d', mktime(0, 0, 0, $to_month, 1, $to_year)), -1, 1));
		$this->lib_custom->project_query_filter('isai.c_project_id', $this->c_project_ids);
		
		parent::_get_list_json();
	}
	
	public function form()
	{
		if (!is_authorized('material/inventory_sample', 'index')) 
			access_denied();
		
		$form_action = $this->input->get_post('form_action');
		$id = $this->input->get_post('id');
		$record = NULL;
		if ($id !== NULL)
		{
			$this->db
				->select("isa.id, isa.code, isa.sampling_date, isa.supervisor, isa.notes")
				->from('m_inventory_samples isa')
				->where('isa.id', $id);
			$table = $this->db->get();
			if ($table->num_rows() > 0)
				$record = $table->first_row();
			else
				show_error("Sample not found", 400);
		}
		
		$m_inventory_sampledetails = array();
		if ($id !== NULL)
		{
			$this->db
				->select("isad.id, isad.m_inventory_sample_id")
				->select("isad.doc, isad.adg, isad.biomass, isad.sr, isad.fcr")
				->select("isad.abw, isad.fd, isad.population, isad.fr")
				->select("isad.notes")
				->select("isad.m_grid_id, gri.code m_grid_code")
				->from('m_inventory_sampledetails isad')
				->join('m_inventory_sampleinventories isai', "isai.m_inventory_sampledetail_id = isad.id", 'left')
				->join('m_grids gri', "gri.id = isad.m_grid_id", 'left')
				->where('isad.m_inventory_sample_id', $id)
				->group_by(
					array(
						'isad.id', 'isad.m_inventory_sample_id',
						'isad.doc', 'isad.adg', 'isad.biomass', 'isad.sr', 'isad.fcr',
						'isad.abw', 'isad.fd', 'isad.population', 'isad.fr',
						'isad.notes',
						'isad.m_grid_id', 'gri.code'
					)
				)
				->order_by('isad.id', 'asc');
			$this->lib_custom->project_query_filter('isai.c_project_id', $this->c_project_ids);
			$table = $this->db->get();
			$m_inventory_sampledetails = $table->result();
		}
		
		$data = array(
			'form_action'				=> $form_action,
			'record'					=> $record,
			'm_inventory_sampledetails'	=> $m_inventory_sampledetails
		);
		$this->load->view('material/inventory_sample/form', $data);
	}
	
	public function detail()
	{
		if (!is_authorized('material/inventory_sample', 'index')) 
			access_denied();
		
		$id = $this->input->get_post('id');
		$this->db
				->select("isa.id, isa.code, isa.sampling_date, isa.supervisor, isa.notes")
				->from('m_inventory_samples isa')
				->where('isa.id', $id);
		$table = $this->db->get();
		if ($table->num_rows() > 0)
			$record = $table->first_row();
		else
			show_error("Sample not found", 400);
		
		$this->db
			->select("isad.id, isad.m_inventory_sample_id")
			->select("isad.doc, isad.adg, isad.biomass, isad.sr, isad.fcr")
			->select("isad.abw, isad.fd, isad.population, isad.fr")
			->select("isad.notes")
			->select("isad.m_grid_id, gri.code m_grid_code")
			->from('m_inventory_sampledetails isad')
			->join('m_inventory_sampleinventories isai', "isai.m_inventory_sampledetail_id = isad.id", 'left')
			->join('m_grids gri', "gri.id = isad.m_grid_id", 'left')
			->where('isad.m_inventory_sample_id', $id)
			->group_by(
				array(
					'isad.id', 'isad.m_inventory_sample_id',
					'isad.doc', 'isad.adg', 'isad.biomass', 'isad.sr', 'isad.fcr',
					'isad.abw', 'isad.fd', 'isad.population', 'isad.fr',
					'isad.notes',
					'isad.m_grid_id', 'gri.code'
				)
			)
			->order_by('isad.id', 'asc');
		$this->lib_custom->project_query_filter('isai.c_project_id', $this->c_project_ids);
		$table = $this->db->get();
		$m_inventory_sampledetails = $table->result();
		
		$data = array(
			'record'					=> $record,
			'm_inventory_sampledetails'	=> $m_inventory_sampledetails
		);
		$this->load->view('material/inventory_sample/detail', $data);
	}
	
	public function get_grid_autocomplete_list_json()
	{
		if (!is_authorized('material/inventory_sample', 'index')) 
			access_denied();
		
		$keywords = $this->input->get_post('term');
		
		$this->db
			->select("gri.id")
			->select("gri.code value")
			->select("gri.code label")
			->select_datediff_day("MAX(inv.received_date)", $this->db->getdate(), 'inventory_age')
			->from('m_inventories inv')
			->join('m_grids gri', "gri.id = inv.m_grid_id")
			->join('m_products pro', "pro.id = inv.m_product_id")
			->group_by(
				array(
					'gri.id', 'gri.code'
				)
			)
			->where_in('pro.type', array('BENUR/BIBIT'))
			->where('inv.quantity_box >', 0)
			->where('inv.quantity >', 0);
		
		if ($keywords)
			$this->db->like('gri.code', $keywords);
		
		parent::_get_list_autocomplete_json();
	}
	
	public function insert()
	{
		if (!is_authorized('material/inventory_sample', 'insert')) 
			access_denied();
		
		parent::_execute('this', 'add_inventory_sample_and_details', 
			array(),
			array(
				array('field' => 'sampling_date', 'label' => 'Date', 'rules' => 'required')
			)
		);
	}
	
	protected function add_inventory_sample_and_details()
	{
		$this->load->library('material/lib_inventory_activity');
		
		$user_id = $this->session->userdata('user_id');
		
		$data_header = new stdClass();
		$data_header->code = generate_code_number("SMP". date('ymd-'), NULL, 3);
		$data_header->sampling_date = $this->input->post('sampling_date');
		$data_header->supervisor = $this->input->post('supervisor');
		$data_header->notes = $this->input->post('notes');
		$id = $this->lib_inventory_activity->sample_add($data_header, $user_id);
		
		$m_inventory_sampledetails = $this->input->post('m_inventory_sampledetails');
		if (!is_array($m_inventory_sampledetails))
			$m_inventory_sampledetails = array();
		
		// -- Add Sample Details --
		if (!empty($m_inventory_sampledetails) && is_array($m_inventory_sampledetails))
		{
			foreach ($m_inventory_sampledetails as $m_inventory_sampledetail)
			{
				$data_detail = new stdClass();
				$data_detail->m_inventory_sample_id = $id;
				$data_detail->m_grid_id = $m_inventory_sampledetail['m_grid_id'];
				$data_detail->doc = $m_inventory_sampledetail['doc'];
				$data_detail->adg = $m_inventory_sampledetail['adg'];
				$data_detail->biomass = $m_inventory_sampledetail['biomass'];
				$data_detail->sr = $m_inventory_sampledetail['sr'];
				$data_detail->fcr = $m_inventory_sampledetail['fcr'];
				$data_detail->abw = $m_inventory_sampledetail['abw'];
				$data_detail->fd = $m_inventory_sampledetail['fd'];
				$data_detail->population = $m_inventory_sampledetail['population'];
				$data_detail->fr = $m_inventory_sampledetail['fr'];
				$data_detail->notes = $m_inventory_sampledetail['notes'];
				$this->lib_inventory_activity->sampledetail_add($data_detail, $user_id);
			}
		}
		
		return $id;
	}
	
	public function update($id)
	{
		if (!is_authorized('material/inventory_sample', 'update')) 
			access_denied();
		
		parent::_execute('this', 'update_inventory_sample_and_details', 
			array($id), 
			array(
				array('field' => 'code', 'label' => 'No', 'rules' => 'required'),
				array('field' => 'sampling_date', 'label' => 'Date', 'rules' => 'required')
			)
		);
	}
	
	protected function update_inventory_sample_and_details($id)
	{
		$this->load->library('material/lib_inventory_activity');
		
		$user_id = $this->session->userdata('user_id');
		
		$data_header = new stdClass();
		$data_header->code = $this->input->post('code');
		$data_header->sampling_date = $this->input->post('sampling_date');
		$data_header->supervisor = $this->input->post('supervisor');
		$data_header->notes = $this->input->post('notes');
		$updated_result = $this->lib_inventory_activity->sample_update($id, $data_header, $user_id);
		
		// -- Sample Detail --
		
		$m_inventory_sampledetails = $this->input->post('m_inventory_sampledetails');
		if (!is_array($m_inventory_sampledetails))
			$m_inventory_sampledetails = array();
		
		$table = $this->db
			->where('m_inventory_sample_id', $id)
			->get('m_inventory_sampledetails');
		$m_inventory_sampledetails_existing = $table->result();
		
		// -- Add/Modify Sample Detail --
		foreach ($m_inventory_sampledetails as $m_inventory_sampledetail)
		{
			$is_found_new = TRUE;
			foreach ($m_inventory_sampledetails_existing as $m_inventory_sampledetail_existing)
			{
				if ($m_inventory_sampledetail_existing->id == $m_inventory_sampledetail['id'])
				{
					$is_found_new = FALSE;
					break;
				}
			}
			$data_detail = new stdClass();
			$data_detail->m_grid_id = $m_inventory_sampledetail['m_grid_id'];
			$data_detail->doc = $m_inventory_sampledetail['doc'];
			$data_detail->adg = $m_inventory_sampledetail['adg'];
			$data_detail->biomass = $m_inventory_sampledetail['biomass'];
			$data_detail->sr = $m_inventory_sampledetail['sr'];
			$data_detail->fcr = $m_inventory_sampledetail['fcr'];
			$data_detail->abw = $m_inventory_sampledetail['abw'];
			$data_detail->fd = $m_inventory_sampledetail['fd'];
			$data_detail->population = $m_inventory_sampledetail['population'];
			$data_detail->fr = $m_inventory_sampledetail['fr'];
			$data_detail->notes = $m_inventory_sampledetail['notes'];
			if ($is_found_new == TRUE)
			{
				$data_detail->m_inventory_sample_id = $id;
				$this->lib_inventory_activity->sampledetail_add($data_detail, $user_id);
			}
			else
			{
				$this->lib_inventory_activity->sampledetail_update($m_inventory_sampledetail['id'], $data_detail, $user_id);
			}
		}
		
		// -- Remove Sample Detail --
		foreach ($m_inventory_sampledetails_existing as $m_inventory_sampledetail_existing)
		{
			$is_found_delete = TRUE;
			foreach ($m_inventory_sampledetails as $m_inventory_sampledetail)
			{
				if ($m_inventory_sampledetail['id'] == $m_inventory_sampledetail_existing->id)
				{
					$is_found_delete = FALSE;
					break;
				}
			}
			if ($is_found_delete == TRUE)
			{
				$this->lib_inventory_activity->sampledetail_remove($m_inventory_sampledetail_existing->id, $user_id);
			}
		}
		
		return $updated_result;
	}
	
	public function delete($id)
	{
		if (!is_authorized('material/inventory_sample', 'delete')) 
			access_denied();
		
		$this->load->library('material/lib_inventory_activity');
		
		$user_id = $this->session->userdata('user_id');
		
		parent::_execute('lib_inventory_activity', 'sample_remove', array($id, $user_id));
	}
}