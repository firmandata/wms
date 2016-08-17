<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Inventory_water extends MY_Controller 
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
		if (!is_authorized('material/inventory_water', 'index')) 
			access_denied();
		
		$data = array(
		);
		
		$content = array(
			'title'		=> "Inventory Water",
			'content' 	=> $this->load->view('material/inventory_water/index', $data, TRUE)
		);
		$this->_load_layout($content);
	}
	
	public function get_list_json()
	{
		if (!is_authorized('material/inventory_water', 'index')) 
			access_denied();
		
		$this->load->helper('date');
		
		$from_month = $this->input->get_post('from_month');
		$from_year = $this->input->get_post('from_year');
		
		$to_month = $this->input->get_post('to_month');
		$to_year = $this->input->get_post('to_year');
		
		$this->db
			->select("isw.id, isw.code, isw.water_date, isw.supervisor")
			->select("iswi.c_project_id, prj.name c_project_name")
			->from('m_inventory_waters isw')
			->join('m_inventory_waterdetails iswd', "iswd.m_inventory_water_id = isw.id", 'left')
			->join('m_inventory_waterinventories iswi', "iswi.m_inventory_waterdetail_id = iswd.id", 'left')
			->join('c_projects prj', "prj.id = iswi.c_project_id", 'left')
			->group_by(
				array(
					'isw.id', 'isw.code', 'isw.water_date', 'isw.supervisor',
					'iswi.c_project_id', 'prj.name'
				)
			);
		$this->db->where("isw.water_date >=", date('Y-m-d', mktime(0, 0, 0, $from_month, 1, $from_year)));
		$this->db->where("isw.water_date <=", add_date(date('Y-m-d', mktime(0, 0, 0, $to_month, 1, $to_year)), -1, 1));
		$this->lib_custom->project_query_filter('iswi.c_project_id', $this->c_project_ids);
		
		parent::_get_list_json();
	}
	
	public function form()
	{
		if (!is_authorized('material/inventory_water', 'index')) 
			access_denied();
		
		$form_action = $this->input->get_post('form_action');
		$id = $this->input->get_post('id');
		$record = NULL;
		if ($id !== NULL)
		{
			$this->db
				->select("isw.id, isw.code, isw.water_date, isw.supervisor, isw.notes")
				->from('m_inventory_waters isw')
				->where('isw.id', $id);
			$table = $this->db->get();
			if ($table->num_rows() > 0)
				$record = $table->first_row();
			else
				show_error("Water not found", 400);
		}
		
		$m_inventory_waterdetails = array();
		if ($id !== NULL)
		{
			$this->db
				->select("iswd.id, iswd.m_inventory_water_id")
				->select("iswd.doc, iswd.suhu, iswd.disolved_oksigen, iswd.ph, iswd.salinitas")
				->select("iswd.kecerahan, iswd.total_ammonia, iswd.total_nitrite, iswd.total_nitrate")
				->select("iswd.notes")
				->select("iswd.m_grid_id, gri.code m_grid_code")
				->from('m_inventory_waterdetails iswd')
				->join('m_inventory_waterinventories iswi', "iswi.m_inventory_waterdetail_id = iswd.id", 'left')
				->join('m_grids gri', "gri.id = iswd.m_grid_id", 'left')
				->where('iswd.m_inventory_water_id', $id)
				->group_by(
					array(
						'iswd.id', 'iswd.m_inventory_water_id',
						'iswd.doc', 'iswd.suhu', 'iswd.disolved_oksigen', 'iswd.ph', 'iswd.salinitas',
						'iswd.kecerahan', 'iswd.total_ammonia', 'iswd.total_nitrite', 'iswd.total_nitrate',
						'iswd.notes',
						'iswd.m_grid_id', 'gri.code'
					)
				)
				->order_by('iswd.id', 'asc');
			$this->lib_custom->project_query_filter('iswi.c_project_id', $this->c_project_ids);
			$table = $this->db->get();
			$m_inventory_waterdetails = $table->result();
		}
		
		$data = array(
			'form_action'				=> $form_action,
			'record'					=> $record,
			'm_inventory_waterdetails'	=> $m_inventory_waterdetails
		);
		$this->load->view('material/inventory_water/form', $data);
	}
	
	public function detail()
	{
		if (!is_authorized('material/inventory_water', 'index')) 
			access_denied();
		
		$id = $this->input->get_post('id');
		$this->db
				->select("isw.id, isw.code, isw.water_date, isw.supervisor, isw.notes")
				->from('m_inventory_waters isw')
				->where('isw.id', $id);
		$table = $this->db->get();
		if ($table->num_rows() > 0)
			$record = $table->first_row();
		else
			show_error("Water not found", 400);
		
		$this->db
			->select("iswd.id, iswd.m_inventory_water_id")
			->select("iswd.doc, iswd.suhu, iswd.disolved_oksigen, iswd.ph, iswd.salinitas")
			->select("iswd.kecerahan, iswd.total_ammonia, iswd.total_nitrite, iswd.total_nitrate")
			->select("iswd.notes")
			->select("iswd.m_grid_id, gri.code m_grid_code")
			->from('m_inventory_waterdetails iswd')
			->join('m_inventory_waterinventories iswi', "iswi.m_inventory_waterdetail_id = iswd.id", 'left')
			->join('m_grids gri', "gri.id = iswd.m_grid_id", 'left')
			->where('iswd.m_inventory_water_id', $id)
			->group_by(
				array(
					'iswd.id', 'iswd.m_inventory_water_id',
					'iswd.doc', 'iswd.suhu', 'iswd.disolved_oksigen', 'iswd.ph', 'iswd.salinitas',
					'iswd.kecerahan', 'iswd.total_ammonia', 'iswd.total_nitrite', 'iswd.total_nitrate',
					'iswd.notes',
					'iswd.m_grid_id', 'gri.code'
				)
			)
			->order_by('iswd.id', 'asc');
		$this->lib_custom->project_query_filter('iswi.c_project_id', $this->c_project_ids);
		$table = $this->db->get();
		$m_inventory_waterdetails = $table->result();
		
		$data = array(
			'record'					=> $record,
			'm_inventory_waterdetails'	=> $m_inventory_waterdetails
		);
		$this->load->view('material/inventory_water/detail', $data);
	}
	
	public function get_grid_autocomplete_list_json()
	{
		if (!is_authorized('material/inventory_water', 'index')) 
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
		if (!is_authorized('material/inventory_water', 'insert')) 
			access_denied();
		
		parent::_execute('this', 'add_inventory_water_and_details', 
			array(),
			array(
				array('field' => 'water_date', 'label' => 'Date', 'rules' => 'required')
			)
		);
	}
	
	protected function add_inventory_water_and_details()
	{
		$this->load->library('material/lib_inventory_activity');
		
		$user_id = $this->session->userdata('user_id');
		
		$data_header = new stdClass();
		$data_header->code = generate_code_number("WTR". date('ymd-'), NULL, 3);
		$data_header->water_date = $this->input->post('water_date');
		$data_header->supervisor = $this->input->post('supervisor');
		$data_header->notes = $this->input->post('notes');
		$id = $this->lib_inventory_activity->water_add($data_header, $user_id);
		
		$m_inventory_waterdetails = $this->input->post('m_inventory_waterdetails');
		if (!is_array($m_inventory_waterdetails))
			$m_inventory_waterdetails = array();
		
		// -- Add Water Details --
		if (!empty($m_inventory_waterdetails) && is_array($m_inventory_waterdetails))
		{
			foreach ($m_inventory_waterdetails as $m_inventory_waterdetail)
			{
				$data_detail = new stdClass();
				$data_detail->m_inventory_water_id = $id;
				$data_detail->m_grid_id = $m_inventory_waterdetail['m_grid_id'];
				$data_detail->doc = $m_inventory_waterdetail['doc'];
				$data_detail->suhu = $m_inventory_waterdetail['suhu'];
				$data_detail->disolved_oksigen = $m_inventory_waterdetail['disolved_oksigen'];
				$data_detail->ph = $m_inventory_waterdetail['ph'];
				$data_detail->salinitas = $m_inventory_waterdetail['salinitas'];
				$data_detail->kecerahan = $m_inventory_waterdetail['kecerahan'];
				$data_detail->total_ammonia = $m_inventory_waterdetail['total_ammonia'];
				$data_detail->total_nitrite = $m_inventory_waterdetail['total_nitrite'];
				$data_detail->total_nitrate = $m_inventory_waterdetail['total_nitrate'];
				$data_detail->notes = $m_inventory_waterdetail['notes'];
				$this->lib_inventory_activity->waterdetail_add($data_detail, $user_id);
			}
		}
		
		return $id;
	}
	
	public function update($id)
	{
		if (!is_authorized('material/inventory_water', 'update')) 
			access_denied();
		
		parent::_execute('this', 'update_inventory_water_and_details', 
			array($id), 
			array(
				array('field' => 'code', 'label' => 'No', 'rules' => 'required'),
				array('field' => 'water_date', 'label' => 'Date', 'rules' => 'required')
			)
		);
	}
	
	protected function update_inventory_water_and_details($id)
	{
		$this->load->library('material/lib_inventory_activity');
		
		$user_id = $this->session->userdata('user_id');
		
		$data_header = new stdClass();
		$data_header->code = $this->input->post('code');
		$data_header->water_date = $this->input->post('water_date');
		$data_header->supervisor = $this->input->post('supervisor');
		$data_header->notes = $this->input->post('notes');
		$updated_result = $this->lib_inventory_activity->water_update($id, $data_header, $user_id);
		
		// -- Water Detail --
		
		$m_inventory_waterdetails = $this->input->post('m_inventory_waterdetails');
		if (!is_array($m_inventory_waterdetails))
			$m_inventory_waterdetails = array();
		
		$table = $this->db
			->where('m_inventory_water_id', $id)
			->get('m_inventory_waterdetails');
		$m_inventory_waterdetails_existing = $table->result();
		
		// -- Add/Modify Water Detail --
		foreach ($m_inventory_waterdetails as $m_inventory_waterdetail)
		{
			$is_found_new = TRUE;
			foreach ($m_inventory_waterdetails_existing as $m_inventory_waterdetail_existing)
			{
				if ($m_inventory_waterdetail_existing->id == $m_inventory_waterdetail['id'])
				{
					$is_found_new = FALSE;
					break;
				}
			}
			$data_detail = new stdClass();
			$data_detail->m_grid_id = $m_inventory_waterdetail['m_grid_id'];
			$data_detail->doc = $m_inventory_waterdetail['doc'];
			$data_detail->suhu = $m_inventory_waterdetail['suhu'];
			$data_detail->disolved_oksigen = $m_inventory_waterdetail['disolved_oksigen'];
			$data_detail->ph = $m_inventory_waterdetail['ph'];
			$data_detail->salinitas = $m_inventory_waterdetail['salinitas'];
			$data_detail->kecerahan = $m_inventory_waterdetail['kecerahan'];
			$data_detail->total_ammonia = $m_inventory_waterdetail['total_ammonia'];
			$data_detail->total_nitrite = $m_inventory_waterdetail['total_nitrite'];
			$data_detail->total_nitrate = $m_inventory_waterdetail['total_nitrate'];
			$data_detail->notes = $m_inventory_waterdetail['notes'];
			if ($is_found_new == TRUE)
			{
				$data_detail->m_inventory_water_id = $id;
				$this->lib_inventory_activity->waterdetail_add($data_detail, $user_id);
			}
			else
			{
				$this->lib_inventory_activity->waterdetail_update($m_inventory_waterdetail['id'], $data_detail, $user_id);
			}
		}
		
		// -- Remove Water Detail --
		foreach ($m_inventory_waterdetails_existing as $m_inventory_waterdetail_existing)
		{
			$is_found_delete = TRUE;
			foreach ($m_inventory_waterdetails as $m_inventory_waterdetail)
			{
				if ($m_inventory_waterdetail['id'] == $m_inventory_waterdetail_existing->id)
				{
					$is_found_delete = FALSE;
					break;
				}
			}
			if ($is_found_delete == TRUE)
			{
				$this->lib_inventory_activity->waterdetail_remove($m_inventory_waterdetail_existing->id, $user_id);
			}
		}
		
		return $updated_result;
	}
	
	public function delete($id)
	{
		if (!is_authorized('material/inventory_water', 'delete')) 
			access_denied();
		
		$this->load->library('material/lib_inventory_activity');
		
		$user_id = $this->session->userdata('user_id');
		
		parent::_execute('lib_inventory_activity', 'water_remove', array($id, $user_id));
	}
}