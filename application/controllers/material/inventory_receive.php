<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Inventory_receive extends MY_Controller 
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
		if (!is_authorized('material/inventory_receive', 'index')) 
			access_denied();
		
		$data = array(
		);
		
		$content = array(
			'title'		=> "ASN",
			'content' 	=> $this->load->view('material/inventory_receive/index', $data, TRUE)
		);
		$this->_load_layout($content);
	}
	
	public function get_list_json()
	{
		if (!is_authorized('material/inventory_receive', 'index')) 
			access_denied();
		
		$this->load->helper('date');
		
		$from_month = $this->input->get_post('from_month');
		$from_year = $this->input->get_post('from_year');
		
		$to_month = $this->input->get_post('to_month');
		$to_year = $this->input->get_post('to_year');
		
		$this->db
			->select("ir.id, ir.code, ir.receive_date")
			->select("ir.vehicle_no, ir.vehicle_driver, ir.transport_mode")
			->select("oid.c_orderin_id, oi.code c_orderin_code, oi.orderin_date c_orderin_date")
			->select("oi.c_businesspartner_id, bp.name c_businesspartner_name")
			->select("oi.c_project_id, prj.name c_project_name")
			->select("ir.status_inventory_inbound")
			->from('m_inventory_receives ir')
			->join('m_inventory_receivedetails ird', "ird.m_inventory_receive_id = ir.id", 'left')
			->join('c_orderindetails oid', "oid.id = ird.c_orderindetail_id", 'left')
			->join('c_orderins oi', "oi.id = oid.c_orderin_id", 'left')
			->join('c_businesspartners bp', "bp.id = oi.c_businesspartner_id", 'left')
			->join('c_projects prj', "prj.id = oi.c_project_id", 'left')
			->group_by(
				array(
					'ir.id', 'ir.code', 'ir.receive_date',
					'ir.vehicle_no', 'ir.vehicle_driver', 'ir.transport_mode',
					'oid.c_orderin_id', 'oi.code', 'oi.orderin_date',
					'oi.c_businesspartner_id', 'bp.name',
					'oi.c_project_id', 'prj.name',
					'ir.status_inventory_inbound'
				)
			);
		$this->db->where("ir.receive_date >=", date('Y-m-d', mktime(0, 0, 0, $from_month, 1, $from_year)));
		$this->db->where("ir.receive_date <=", add_date(date('Y-m-d', mktime(0, 0, 0, $to_month, 1, $to_year)), -1, 1));
		
		$this->lib_custom->project_query_filter('oi.c_project_id', $this->c_project_ids);
		
		parent::_get_list_json();
	}
	
	public function form()
	{
		if (!is_authorized('material/inventory_receive', 'index')) 
			access_denied();
		
		$form_action = $this->input->get_post('form_action');
		$id = $this->input->get_post('id');
		$record = NULL;
		if ($id !== NULL)
		{
			$this->db
				->select("ir.id, ir.code, ir.receive_date")
				->select("ir.vehicle_no, ir.vehicle_driver, ir.transport_mode, ir.status_inventory_inbound, ir.notes")
				->from('m_inventory_receives ir')
				->where('ir.id', $id);
			$table = $this->db->get();
			if ($table->num_rows() > 0)
			{
				$record = $table->first_row();
				if ($record->status_inventory_inbound == 'COMPLETE')
					show_error("ASN was complete", 400);
			}
			else
				show_error("ASN not found", 400);
		}
		
		$m_inventory_receivedetails = array();
		if ($id !== FALSE)
		{
			$this->db
				->select("ird.id, ird.condition, ird.notes")
				->select("ird.c_orderindetail_id")
				->select("ird.m_grid_id, gri.code m_grid_code")
				->select("ird.supervisor")
				->select("pro.code m_product_code, pro.name m_product_name, pro.netto m_product_netto, pro.uom m_product_uom, pro.pack m_product_pack")
				->select_concat(array("pro.name", "' ('", "pro.code", "' / '", "oi.code", "')'"), 'm_product_text')
				->select("ird.quantity_box, ird.quantity", FALSE)
				->from('m_inventory_receivedetails ird')
				->join('c_orderindetails oid', "oid.id = ird.c_orderindetail_id")
				->join('m_grids gri', "gri.id = ird.m_grid_id")
				->join('c_orderins oi', "oi.id = oid.c_orderin_id")
				->join('m_products pro', "pro.id = oid.m_product_id")
				->where('ird.m_inventory_receive_id', $id)
				->order_by('ird.id', 'asc');
			$this->lib_custom->project_query_filter('oi.c_project_id', $this->c_project_ids);
			$table = $this->db->get();
			$m_inventory_receivedetails = $table->result();
		}
		
		$data = array(
			'form_action'					=> $form_action,
			'record'						=> $record,
			'm_inventory_receivedetails'	=> $m_inventory_receivedetails
		);
		$this->load->view('material/inventory_receive/form', $data);
	}
	
	public function form_upload()
	{
		if (!is_authorized('material/inventory_receive', 'insert')) 
			access_denied();
		
		$form_action = $this->input->get_post('form_action');
		
		$data = array(
			'form_action'	=> $form_action
		);
		$this->load->view('material/inventory_receive/form_upload', $data);
	}
	
	public function detail()
	{
		if (!is_authorized('material/inventory_receive', 'index')) 
			access_denied();
		
		$id = $this->input->get_post('id');
		$this->db
			->select("ir.id, ir.code, ir.receive_date")
			->select("ir.vehicle_no, ir.vehicle_driver, ir.transport_mode, ir.notes")
			->select("ir.status_inventory_inbound")
			->from('m_inventory_receives ir')
			->where('ir.id', $id);
		$table = $this->db->get();
		if ($table->num_rows() > 0)
			$record = $table->first_row();
		else
			show_error("ASN not found", 400);
		
		$this->db
			->select("ird.id")
			->select("ird.condition, ird.notes")
			->select("ird.m_grid_id, gri.code m_grid_code")
			->select("ird.supervisor")
			->select("oid.c_orderin_id, oid.m_product_id")
			->select("oi.code c_orderin_code, oi.orderin_date c_orderin_date")
			->select("oi.c_businesspartner_id, bp.name c_businesspartner_name")
			->select("oi.c_project_id, prj.name c_project_name")
			->select("pro.code m_product_code, pro.name m_product_name, pro.uom m_product_uom, pro.pack m_product_pack")
			->select("ird.quantity_box")
			->select_if_null('inbd.quantity_box', 0, 'quantity_box_used')
			->select("ird.quantity")
			->select_if_null('inbd.quantity', 0, 'quantity_used')
			->select("ird.status_inventory_inbound")
			->from('m_inventory_receivedetails ird')
			->join('c_orderindetails oid', "oid.id = ird.c_orderindetail_id")
			->join('m_grids gri', "gri.id = ird.m_grid_id")
			->join('c_orderins oi', "oi.id = oid.c_orderin_id")
			->join('c_businesspartners bp', "bp.id = oi.c_businesspartner_id", 'left')
			->join('c_projects prj', "prj.id = oi.c_project_id", 'left')
			->join('m_products pro', "pro.id = oid.m_product_id")
			->join(
				 "(SELECT m_inventory_receivedetail_id, "
				."		  " . $this->db->if_null("SUM(quantity_box)", 0) . " quantity_box, "
				."		  " . $this->db->if_null("SUM(quantity)", 0) . " quantity "
				."	 FROM m_inventory_inbounddetails "
				."  GROUP BY m_inventory_receivedetail_id "
				.") inbd", 
				"inbd.m_inventory_receivedetail_id = ird.id", 'left')
			->where('ird.m_inventory_receive_id', $id)
			->order_by('ird.id', 'asc');
		$this->lib_custom->project_query_filter('oi.c_project_id', $this->c_project_ids);
		$table = $this->db->get();
		$m_inventory_receivedetails = $table->result();
		
		$data = array(
			'record'						=> $record,
			'm_inventory_receivedetails'	=> $m_inventory_receivedetails
		);
		$this->load->view('material/inventory_receive/detail', $data);
	}
	
	public function get_product_autocomplete_list_json()
	{
		if (!is_authorized('material/inventory_receive', 'index')) 
			access_denied();
		
		$keywords = $this->input->get_post('term');
		
		$this->db
			->select("oid.id")
			->select_concat(array("pro.name", "' ('", "pro.code", "' / '", "oi.code", "')'"), 'value')
			->select_concat(array("pro.name", "' ('", "pro.code", "' / '", "oi.code", "')'"), 'label')
			->select("pro.netto netto, pro.price price, pro.pack pack, pro.casing casing, pro.uom uom")
			->select_sum("oid.quantity_box", "quantity_box")
			->select_sum("oid.quantity", "quantity")
			->from('c_orderindetails oid')
			->join('c_orderins oi', "oi.id = oid.c_orderin_id")
			->join('m_products pro', "pro.id = oid.m_product_id")
			->where('oid.status_inventory_receive <>', 'COMPLETE')
			->group_by(
				array(
					  'oid.id', 'oi.code'
					, 'pro.name', 'pro.code'
					, 'pro.netto', 'pro.price', 'pro.pack', 'pro.casing', 'pro.uom'
				)
			);
		
		if ($keywords)
			$this->db->where($this->db->concat(array("pro.name", "' ('", "pro.code", "' / '", "oi.code", "')'")) . " LIKE '%" . $this->db->escape_like_str($keywords) . "%'", NULL, FALSE);
		
		$this->lib_custom->project_query_filter('oi.c_project_id', $this->c_project_ids);
		
		parent::_get_list_autocomplete_json();
	}
	
	public function get_grid_autocomplete_list_json()
	{
		if (!is_authorized('material/inventory_receive', 'index')) 
			access_denied();
		
		$keywords = $this->input->get_post('term');
		$m_product_id = $this->input->get_post('m_product_id');
		
		$this->db
			->select("gri.id")
			->select("gri.code value")
			->select("gri.code label")
			->from('m_grids gri');
		
		if ($keywords)
			$this->db->like('gri.code', $keywords);
		
		parent::_get_list_autocomplete_json();
	}
	
	public function insert()
	{
		if (!is_authorized('material/inventory_receive', 'insert')) 
			access_denied();
		
		parent::_execute('this', 'add_receive_and_details', 
			array(),
			array(
				array('field' => 'receive_date', 'label' => 'Date', 'rules' => 'required')
			)
		);
	}
	
	protected function add_receive_and_details()
	{
		$this->load->library('material/lib_inventory_in');
		
		$user_id = $this->session->userdata('user_id');
		
		$data_header = new stdClass();
		$data_header->code = generate_code_number("ASN". date('ymd-'), NULL, 3);
		$data_header->receive_date = $this->input->post('receive_date');
		$data_header->vehicle_no = $this->input->post('vehicle_no');
		$data_header->vehicle_driver = $this->input->post('vehicle_driver');
		$data_header->transport_mode = $this->input->post('transport_mode');
		$data_header->notes = $this->input->post('notes');
		$id = $this->lib_inventory_in->receive_add($data_header, $user_id);
		
		$m_inventory_receivedetails = $this->input->post('m_inventory_receivedetails');
		if (!is_array($m_inventory_receivedetails))
			$m_inventory_receivedetails = array();
		
		// -- Add Receive Details --
		if (!empty($m_inventory_receivedetails) && is_array($m_inventory_receivedetails))
		{
			foreach ($m_inventory_receivedetails as $m_inventory_receivedetail)
			{
				$data_detail = new stdClass();
				$data_detail->m_inventory_receive_id = $id;
				$data_detail->c_orderindetail_id = $m_inventory_receivedetail['c_orderindetail_id'];
				$data_detail->quantity_box = $m_inventory_receivedetail['quantity_box'];
				$data_detail->quantity = $m_inventory_receivedetail['quantity'];
				$data_detail->condition = $m_inventory_receivedetail['condition'];
				
				$data_detail->m_grid_id = NULL;
				if (!empty($m_inventory_receivedetail['m_grid_code']))
				{
					$m_grids_query = $this->db
						->select("gri.id")
						->from('m_grids gri')
						->where('gri.code', $m_inventory_receivedetail['m_grid_code'])
						->get();
					if ($m_grids_query->num_rows() > 0)
					{
						$m_grids_record = $m_grids_query->first_row();
						$data_detail->m_grid_id = $m_grids_record->id;
					}
					else
						throw new Exception("Unknown location '".$m_inventory_receivedetail['m_grid_code']."'.");
				}
				
				$data_detail->supervisor = $m_inventory_receivedetail['supervisor'];
				$data_detail->notes = $m_inventory_receivedetail['notes'];
				$this->lib_inventory_in->receivedetail_add($data_detail, $user_id);
			}
		}
		
		return $id;
	}
	
	public function update($id)
	{
		if (!is_authorized('material/inventory_receive', 'update')) 
			access_denied();
		
		parent::_execute('this', 'update_receive_and_details', 
			array($id), 
			array(
				array('field' => 'code', 'label' => 'No', 'rules' => 'required'),
				array('field' => 'receive_date', 'label' => 'Date', 'rules' => 'required')
			)
		);
	}
	
	protected function update_receive_and_details($id)
	{
		$this->load->library('material/lib_inventory_in');
		
		$user_id = $this->session->userdata('user_id');
		
		$data_header = new stdClass();
		$data_header->code = $this->input->post('code');
		$data_header->receive_date = $this->input->post('receive_date');
		$data_header->vehicle_no = $this->input->post('vehicle_no');
		$data_header->vehicle_driver = $this->input->post('vehicle_driver');
		$data_header->transport_mode = $this->input->post('transport_mode');
		$data_header->notes = $this->input->post('notes');
		$updated_result = $this->lib_inventory_in->receive_update($id, $data_header, $user_id);
		
		// -- Receive Detail --
		$m_inventory_receivedetails = $this->input->post('m_inventory_receivedetails');
		if (!is_array($m_inventory_receivedetails))
			$m_inventory_receivedetails = array();
		
		$table = $this->db
			->where('m_inventory_receive_id', $id)
			->get('m_inventory_receivedetails');
		$m_inventory_receivedetails_existing = $table->result();
		
		// -- Remove Receive Detail --
		foreach ($m_inventory_receivedetails_existing as $m_inventory_receivedetail_existing)
		{
			$is_found_delete = TRUE;
			foreach ($m_inventory_receivedetails as $m_inventory_receivedetail)
			{
				if ($m_inventory_receivedetail['id'] == $m_inventory_receivedetail_existing->id)
				{
					$is_found_delete = FALSE;
					break;
				}
			}
			if ($is_found_delete == TRUE)
			{
				$this->lib_inventory_in->receivedetail_remove($m_inventory_receivedetail_existing->id, $user_id);
			}
		}
		
		// -- Add/Modify Receive Detail --
		foreach ($m_inventory_receivedetails as $m_inventory_receivedetail)
		{
			$is_found_new = TRUE;
			foreach ($m_inventory_receivedetails_existing as $m_inventory_receivedetail_existing)
			{
				if ($m_inventory_receivedetail_existing->id == $m_inventory_receivedetail['id'])
				{
					$is_found_new = FALSE;
					break;
				}
			}
			$data_detail = new stdClass();
			$data_detail->c_orderindetail_id = $m_inventory_receivedetail['c_orderindetail_id'];
			$data_detail->quantity_box = $m_inventory_receivedetail['quantity_box'];
			$data_detail->quantity = $m_inventory_receivedetail['quantity'];
			$data_detail->condition = $m_inventory_receivedetail['condition'];
			$data_detail->m_grid_id = NULL;
			if (!empty($m_inventory_receivedetail['m_grid_code']))
			{
				$m_grids_query = $this->db
					->select("gri.id")
					->from('m_grids gri')
					->where('gri.code', $m_inventory_receivedetail['m_grid_code'])
					->get();
				if ($m_grids_query->num_rows() > 0)
				{
					$m_grids_record = $m_grids_query->first_row();
					$data_detail->m_grid_id = $m_grids_record->id;
				}
				else
					throw new Exception("Unknown location '".$m_inventory_receivedetail['m_grid_code']."'.");
			}
			$data_detail->supervisor = $m_inventory_receivedetail['supervisor'];
			$data_detail->notes = $m_inventory_receivedetail['notes'];
			if ($is_found_new == TRUE)
			{
				$data_detail->m_inventory_receive_id = $id;
				$this->lib_inventory_in->receivedetail_add($data_detail, $user_id);
			}
			else
			{
				$this->lib_inventory_in->receivedetail_update($m_inventory_receivedetail['id'], $data_detail, $user_id);
			}
		}
		
		return $updated_result;
	}
	
	public function delete($id)
	{
		if (!is_authorized('material/inventory_receive', 'delete')) 
			access_denied();
		
		$this->load->library('material/lib_inventory_in');
		
		$user_id = $this->session->userdata('user_id');
		
		parent::_execute('lib_inventory_in', 'receive_remove', array($id, $user_id));
	}
	
	public function inbound($m_inventory_receive_id)
	{
		if (!is_authorized('material/inventory_inbound', 'insert')) 
			access_denied();
		
		$this->load->library('material/lib_inventory_in');
		
		$data = new stdClass();
		$data->code = generate_code_number("INB". date('ymd-'), NULL, 3);
		$data->inbound_date = $this->input->post('inbound_date');
		$data->notes = $this->input->post('notes');
		
		$user_id = $this->session->userdata('user_id');
		
		parent::_execute('lib_inventory_in', 'receive_generate_inbound', 
			array($data, $m_inventory_receive_id, $user_id),
			array(
				array('field' => 'inbound_date', 'label' => 'Date', 'rules' => 'required')
			)
		);
	}
	
	public function forecast_create($id)
	{
		if (!is_authorized('material/inventory_receive', 'insert') || !is_authorized('material/inventory_receive', 'update') || !is_authorized('material/inventory_receive', 'delete')) 
			access_denied();
		
		$this->db
			->select("ir.status_inventory_inbound")
			->from('m_inventory_receives ir')
			->where('ir.id', $id);
		$table = $this->db->get();
		if ($table->num_rows() > 0)
		{
			$record = $table->first_row();
			if ($record->status_inventory_inbound != 'NO INBOUND')
				show_error("Only NO INBOUND can be create the forecast", 400);
		}
		else
			show_error("ASN not found", 400);
		
		$user_id = $this->session->userdata('user_id');
		$is_force_regenerate = $this->input->get_post('is_force_regenerate');
		
		$this->load->library('custom/lib_custom_inventory');
		
		if (!$is_force_regenerate)
		{
			$table = $this->db
				->distinct()
				->select('ifc.id, ifc.code')
				->from('m_inventory_receivedetails ird')
				->join('cus_m_inventory_forecastdetails ifcd', "ifcd.m_inventory_receivedetail_id = ird.id")
				->join('cus_m_inventory_forecasts ifc', "ifc.id = ifcd.cus_m_inventory_forecast_id")
				->where('ird.m_inventory_receive_id', $id)
				->get();
			if ($table->num_rows() > 0)
			{
				$cus_m_inventory_forecast = $table->first_row();
				
				$result = new stdClass();
				$result->response = FALSE;
				$result->value = 'ALREADY_EXISTS';
				$result->data = $cus_m_inventory_forecast;
				
				$this->result_json($result);
			}
			else
			{
				parent::_execute('lib_custom_inventory', 'forecast_create_by_inventory_receive', array($id, $user_id));
			}
		}
		else
		{
			parent::_execute('lib_custom_inventory', 'forecast_create_by_inventory_receive', array($id, $user_id));
		}
	}
	
	public function forecast_document()
	{
		if (!is_authorized('material/inventory_receive', 'index')) 
			access_denied();
		
		$id = $this->input->get_post('id');
		
		// -- Get Header --
		$this->db
			->select("ifc.id, ifc.code")
			->select("oid.c_orderin_id, oi.code c_orderin_code")
			->select("oi.c_businesspartner_id, bp.name c_businesspartner_name")
			->select("ird.m_inventory_receive_id, ir.code m_inventory_receive_code, ir.receive_date m_inventory_receive_receive_date, ir.notes m_inventory_receive_notes")
			->from('cus_m_inventory_forecasts ifc')
			->join('cus_m_inventory_forecastdetails ifcd', "ifcd.cus_m_inventory_forecast_id = ifc.id")
			->join('m_inventory_receivedetails ird', "ird.id = ifcd.m_inventory_receivedetail_id")
			->join('m_inventory_receives ir', "ir.id = ird.m_inventory_receive_id")
			->join('c_orderindetails oid', "oid.id = ird.c_orderindetail_id")
			->join('c_orderins oi', "oi.id = oid.c_orderin_id")
			->join('c_businesspartners bp', "bp.id = oi.c_businesspartner_id", 'left')
			->where('ifc.id', $id)
			->group_by(
				array(
					'ifc.id', 'ifc.code',
					'oid.c_orderin_id', 'oi.code',
					'oi.c_businesspartner_id', 'bp.name',
					'ird.m_inventory_receive_id', 'ir.code', 'ir.receive_date', 'ir.notes'
				)
			);
		$this->lib_custom->project_query_filter('oi.c_project_id', $this->c_project_ids);
		$table = $this->db
			->get();
		if ($table->num_rows() == 0)
			show_error("Forecast not found", 400);
		$cus_m_inventory_forecast = $table->first_row();
		
		// -- Get Details -- 
		$table = $this->db
			->select("ifcd.id")
			->select("ifcd.quantity")
			->select("ifcd.m_grid_id, gri.code m_grid_code")
			->select("oid.m_product_id, pro.code m_product_code, pro.name m_product_name")
			->from('cus_m_inventory_forecastdetails ifcd')
			->join('m_inventory_receivedetails ird', "ird.id = ifcd.m_inventory_receivedetail_id")
			->join('c_orderindetails oid', "oid.id = ird.c_orderindetail_id")
			->join('m_products pro', "pro.id = oid.m_product_id")
			->join('m_grids gri', "gri.id = ifcd.m_grid_id", 'left')
			->where('ifcd.cus_m_inventory_forecast_id', $cus_m_inventory_forecast->id)
			->order_by('ifcd.id', 'asc')
			->get();
		$cus_m_inventory_forecastdetails = $table->result();
		
		// -- Get Summary --
		$table = $this->db
			->select_if_null("MIN(ifcd.id)", 0, 'id')
			->select("oid.m_product_id, pro.code m_product_code, pro.name m_product_name, pro.uom m_product_uom, pro.pack m_product_pack")
			->select("ird.quantity_box, ird.quantity")
			->from('cus_m_inventory_forecastdetails ifcd')
			->join('m_inventory_receivedetails ird', "ird.id = ifcd.m_inventory_receivedetail_id")
			->join('c_orderindetails oid', "oid.id = ird.c_orderindetail_id")
			->join('m_products pro', "pro.id = oid.m_product_id")
			->where('ifcd.cus_m_inventory_forecast_id', $cus_m_inventory_forecast->id)
			->group_by(
				array(
					'oid.m_product_id', 'pro.code', 'pro.name', 'pro.uom', 'pro.pack',
					'ird.quantity_box', 'ird.quantity'
				)
			)
			->order_by('id', 'asc')
			->get();
		$cus_m_inventory_forecast_summaries = $table->result();
		
		$this->forecast_document_pdf($cus_m_inventory_forecast, $cus_m_inventory_forecast_summaries, $cus_m_inventory_forecastdetails);
	}
	
	protected function forecast_document_pdf($header, $summaries, $details)
	{
		$data = array(
			'header'	=> $header,
			'summaries'	=> $summaries,
			'details'	=> $details
		);
		$html = $this->load->view('material/inventory_receive/forecast_document', $data, TRUE);
		
		// echo $html;
		$this->load->library('lib_dompdf');
		$this->lib_dompdf->load_as_pdf($html, 'forecast.pdf', 'a4', 'portrait');
	}
	
	protected function forecast_document_excel($header, $summaries, $details)
	{
		$this->load->helper('date');
		$file_name = str_replace("/", "_", $header->code) . '.xlsx';
		
		require_once APPPATH."third_party/phpexcel/PHPExcel.php";
		require_once APPPATH."third_party/phpexcel/PHPExcel/IOFactory.php";
		require_once APPPATH."third_party/phpexcel/PHPExcel/Cell.php";
		require_once APPPATH."third_party/phpexcel/PHPExcel/Style.php";
		
		$file_writer = NULL;
		try 
		{
			/* -- Creating the workbook -- */
			$file_excel = new PHPExcel();
			
			/* -- Activate the sheet -- */
			$file_excel->setActiveSheetIndex(0);
			$sheet = $file_excel->getActiveSheet();
			$sheet->setTitle('forecast');
			
			$row_number = 1;
			$column_first_index = 0;
			$column_last_index = 11;
			
			$table_styles = array(
				'borders' => array(
					'allborders' => array(
						'style' => PHPExcel_Style_Border::BORDER_THIN
					),
				)
			);
			
			$header_styles = array(
				'fill' => array(
					'type'		=> PHPExcel_Style_Fill::FILL_SOLID,
					'startcolor'	=> array(
						'argb'	=> 'CCCCCCCC',
					),
				),
				'font' => array(
					'color'	=> array('rgb' => '000000'),
					'bold'	=> TRUE,
				),
				'alignment' => array(
					'wrap'		=> FALSE,
					'horizontal'=> PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
					'vertical'	=> PHPExcel_Style_Alignment::VERTICAL_CENTER
				)
			);
			
			/* -- Set the title of sheet -- */
			$sheet->setCellValueByColumnAndRow($column_first_index, $row_number, "Received-Forecast");
			$column_from_name = PHPExcel_Cell::stringFromColumnIndex($column_first_index);
			$column_to_name = PHPExcel_Cell::stringFromColumnIndex($column_last_index);
			$sheet->mergeCells($column_from_name.$row_number.':'.$column_to_name.$row_number);
			$header_custom_styles = $header_styles;
			$header_custom_styles['font']['size'] = 16;
			$sheet->getStyle(
				$column_from_name.$row_number.':'.$column_to_name.$row_number
			)->applyFromArray($header_custom_styles);
			$row_number++;
			
			/* -- Write the header -- */
			$row_number += 2;
			
			$sheet->setCellValueByColumnAndRow($column_first_index, $row_number, "No Paperwork");
			$sheet->setCellValueByColumnAndRow($column_first_index + 1, $row_number, $header->code);
			$sheet->setCellValueByColumnAndRow($column_first_index + 8, $row_number, "No Received");
			$sheet->setCellValueByColumnAndRow($column_first_index + 9, $row_number, $header->m_inventory_receive_code);
			$row_number++;
			
			$sheet->setCellValueByColumnAndRow($column_first_index, $row_number, "Supplier Name");
			$sheet->setCellValueByColumnAndRow($column_first_index + 1, $row_number, $header->c_businesspartner_name);
			$sheet->setCellValueByColumnAndRow($column_first_index + 8, $row_number, "No PO");
			$sheet->setCellValueByColumnAndRow($column_first_index + 9, $row_number, $header->c_orderin_code);
			$row_number++;
			
			$sheet->setCellValueByColumnAndRow($column_first_index, $row_number, "Notes");
			$sheet->setCellValueByColumnAndRow($column_first_index + 1, $row_number, $header->m_inventory_receive_notes);
			$row_number++;
			
			$sheet->setCellValueByColumnAndRow($column_first_index, $row_number, "Tanggal");
			$sheet->setCellValueByColumnAndRow($column_first_index + 1, $row_number, (!empty($header->m_inventory_receive_receive_date) ? date($this->config->item('server_display_date_format'), strtotime($header->m_inventory_receive_receive_date)) : ''));
			$row_number++;
			
			/* -- Write the summaries -- */
			$row_number += 1;
			
			$sheet->setCellValueByColumnAndRow($column_first_index, $row_number, "Line");
			$sheet->setCellValueByColumnAndRow($column_first_index + 1, $row_number, "Item Code");
			
			$sheet->setCellValueByColumnAndRow($column_first_index + 2, $row_number, "Item Name");
			$column_from_name = PHPExcel_Cell::stringFromColumnIndex($column_first_index + 2);
			$column_to_name = PHPExcel_Cell::stringFromColumnIndex($column_first_index + 6);
			$sheet->mergeCells($column_from_name.$row_number.':'.$column_to_name.$row_number);
			
			$sheet->setCellValueByColumnAndRow($column_first_index + 7, $row_number, "UOM");
			$sheet->setCellValueByColumnAndRow($column_first_index + 8, $row_number, "Pack");
			$sheet->setCellValueByColumnAndRow($column_first_index + 9, $row_number, "QTY (CARTON)");
			$sheet->setCellValueByColumnAndRow($column_first_index + 10, $row_number, "QTY (KG)");
			$sheet->setCellValueByColumnAndRow($column_first_index + 11, $row_number, "Barcode");
			
			$column_from_name = PHPExcel_Cell::stringFromColumnIndex($column_first_index);
			$column_to_name = PHPExcel_Cell::stringFromColumnIndex($column_first_index + 11);
			$sheet->getStyle(
				$column_from_name.$row_number.':'.$column_to_name.$row_number
			)->applyFromArray($header_styles);
			
			$row_number++;
			
			foreach ($summaries as $summary_idx=>$summary)
			{
				$sheet->setCellValueByColumnAndRow($column_first_index, $row_number, $summary_idx + 1);
				$sheet->setCellValueByColumnAndRow($column_first_index + 1, $row_number, $summary->m_product_code);
				
				$sheet->setCellValueByColumnAndRow($column_first_index + 2, $row_number, $summary->m_product_name);
				$column_from_name = PHPExcel_Cell::stringFromColumnIndex($column_first_index + 2);
				$column_to_name = PHPExcel_Cell::stringFromColumnIndex($column_first_index + 6);
				$sheet->mergeCells($column_from_name.$row_number.':'.$column_to_name.$row_number);
				
				$sheet->setCellValueByColumnAndRow($column_first_index + 7, $row_number, $summary->m_product_uom);
				$sheet->setCellValueByColumnAndRow($column_first_index + 8, $row_number, $summary->m_product_pack);
				$sheet->setCellValueByColumnAndRow($column_first_index + 9, $row_number, $summary->quantity_box);
				$sheet->setCellValueByColumnAndRow($column_first_index + 10, $row_number, $summary->quantity);
				$sheet->setCellValueByColumnAndRow($column_first_index + 11, $row_number, $summary->m_product_code);
				$row_number++;
			}
			
			$column_from_name = PHPExcel_Cell::stringFromColumnIndex($column_first_index);
			$column_to_name = PHPExcel_Cell::stringFromColumnIndex($column_first_index + 11);
			$sheet->getStyle(
				$column_from_name. ($row_number - count($summaries) - 1) .':'.$column_to_name. ($row_number - 1)
			)->applyFromArray($table_styles);
			
			$row_number += 1;
			$sheet->setCellValueByColumnAndRow($column_first_index, $row_number, "Detail-Forecast");
			$column_from_name = PHPExcel_Cell::stringFromColumnIndex($column_first_index);
			$column_to_name = PHPExcel_Cell::stringFromColumnIndex($column_last_index);
			$sheet->mergeCells($column_from_name.$row_number.':'.$column_to_name.$row_number);
			$row_number++;
			
			/* -- Write the details -- */
			$row_number += 1;
			
			$sheet->setCellValueByColumnAndRow($column_first_index, $row_number, "No");
			$column_from_name = PHPExcel_Cell::stringFromColumnIndex($column_first_index);
			$sheet->mergeCells($column_from_name.$row_number.':'.$column_from_name. ($row_number + 1));
			
			$sheet->setCellValueByColumnAndRow($column_first_index + 1, $row_number, "Location");
			$column_from_name = PHPExcel_Cell::stringFromColumnIndex($column_first_index + 1);
			$sheet->mergeCells($column_from_name.$row_number.':'.$column_from_name. ($row_number + 1));
			
			$sheet->setCellValueByColumnAndRow($column_first_index + 2, $row_number, "PID");
			$column_from_name = PHPExcel_Cell::stringFromColumnIndex($column_first_index + 2);
			$sheet->mergeCells($column_from_name.$row_number.':'.$column_from_name. ($row_number + 1));
			
			$sheet->setCellValueByColumnAndRow($column_first_index + 3, $row_number, "Item");
			$column_from_name = PHPExcel_Cell::stringFromColumnIndex($column_first_index + 3);
			$sheet->mergeCells($column_from_name.$row_number.':'.$column_from_name. ($row_number + 1));
			
			$sheet->setCellValueByColumnAndRow($column_first_index + 4, $row_number, "Lot");
			$column_from_name = PHPExcel_Cell::stringFromColumnIndex($column_first_index + 4);
			$sheet->mergeCells($column_from_name.$row_number.':'.$column_from_name. ($row_number + 1));
			
			$sheet->setCellValueByColumnAndRow($column_first_index + 5, $row_number, "Description");
			$column_from_name = PHPExcel_Cell::stringFromColumnIndex($column_first_index + 5);
			$sheet->mergeCells($column_from_name.$row_number.':'.$column_from_name. ($row_number + 1));
			
			$sheet->setCellValueByColumnAndRow($column_first_index + 6, $row_number, "In");
			$column_from_name = PHPExcel_Cell::stringFromColumnIndex($column_first_index + 6);
			$column_to_name = PHPExcel_Cell::stringFromColumnIndex($column_first_index + 7);
			$sheet->mergeCells($column_from_name.$row_number.':'.$column_to_name.$row_number);
			
			$sheet->setCellValueByColumnAndRow($column_first_index + 8, $row_number, "Realisasi");
			$column_from_name = PHPExcel_Cell::stringFromColumnIndex($column_first_index + 8);
			$column_to_name = PHPExcel_Cell::stringFromColumnIndex($column_first_index + 9);
			$sheet->mergeCells($column_from_name.$row_number.':'.$column_to_name.$row_number);
			
			$sheet->setCellValueByColumnAndRow($column_first_index + 10, $row_number, "Packed Date");
			$column_from_name = PHPExcel_Cell::stringFromColumnIndex($column_first_index + 10);
			$sheet->mergeCells($column_from_name.$row_number.':'.$column_from_name. ($row_number + 1));
			
			$sheet->setCellValueByColumnAndRow($column_first_index + 11, $row_number, "Expired Date");
			$column_from_name = PHPExcel_Cell::stringFromColumnIndex($column_first_index + 11);
			$sheet->mergeCells($column_from_name.$row_number.':'.$column_from_name. ($row_number + 1));
			
			$row_number++;
			
			$sheet->setCellValueByColumnAndRow($column_first_index + 6, $row_number, "Carton");
			$sheet->setCellValueByColumnAndRow($column_first_index + 7, $row_number, "KG");
			$sheet->setCellValueByColumnAndRow($column_first_index + 8, $row_number, "Carton");
			$sheet->setCellValueByColumnAndRow($column_first_index + 9, $row_number, "KG");
			
			$column_from_name = PHPExcel_Cell::stringFromColumnIndex($column_first_index);
			$column_to_name = PHPExcel_Cell::stringFromColumnIndex($column_first_index + 11);
			$sheet->getStyle(
				$column_from_name.($row_number - 1).':'.$column_to_name.$row_number
			)->applyFromArray($header_styles);
			
			$row_number++;
			
			foreach ($details as $detail_idx=>$detail)
			{
				$sheet->setCellValueByColumnAndRow($column_first_index, $row_number, $detail_idx + 1);
				$sheet->setCellValueByColumnAndRow($column_first_index + 1, $row_number, $detail->m_grid_code);
				$sheet->setCellValueByColumnAndRow($column_first_index + 3, $row_number, $detail->m_product_code);
				$sheet->setCellValueByColumnAndRow($column_first_index + 5, $row_number, $detail->m_product_name);
				$sheet->setCellValueByColumnAndRow($column_first_index + 6, $row_number, $detail->quantity);
				$row_number++;
			}
			
			$column_from_name = PHPExcel_Cell::stringFromColumnIndex($column_first_index);
			$column_to_name = PHPExcel_Cell::stringFromColumnIndex($column_first_index + 11);
			$sheet->getStyle(
				$column_from_name. ($row_number - count($details) - 2) .':'.$column_to_name. ($row_number - 1)
			)->applyFromArray($table_styles);
			
			/* -- Write the footer -- */
			$row_number += 1;
			
			$column_from_name = PHPExcel_Cell::stringFromColumnIndex($column_first_index);
			$column_to_name = PHPExcel_Cell::stringFromColumnIndex($column_first_index + 1);
			$sheet->mergeCells($column_from_name.$row_number.':'.$column_to_name. ($row_number + 2));
			
			$column_from_name = PHPExcel_Cell::stringFromColumnIndex($column_first_index + 2);
			$column_to_name = PHPExcel_Cell::stringFromColumnIndex($column_first_index + 3);
			$sheet->mergeCells($column_from_name.$row_number.':'.$column_to_name. ($row_number + 2));
			
			$column_from_name = PHPExcel_Cell::stringFromColumnIndex($column_first_index + 4);
			$column_to_name = PHPExcel_Cell::stringFromColumnIndex($column_first_index + 5);
			$sheet->mergeCells($column_from_name.$row_number.':'.$column_to_name. ($row_number + 2));
			
			$column_from_name = PHPExcel_Cell::stringFromColumnIndex($column_first_index + 6);
			$column_to_name = PHPExcel_Cell::stringFromColumnIndex($column_first_index + 7);
			$sheet->mergeCells($column_from_name.$row_number.':'.$column_to_name. ($row_number + 2));
			
			$column_from_name = PHPExcel_Cell::stringFromColumnIndex($column_first_index + 8);
			$column_to_name = PHPExcel_Cell::stringFromColumnIndex($column_first_index + 9);
			$sheet->mergeCells($column_from_name.$row_number.':'.$column_to_name. ($row_number + 2));
			
			$row_number += 2;
			$row_number++;
			
			$sheet->setCellValueByColumnAndRow($column_first_index, $row_number, "Admin Operasional");
			$column_from_name = PHPExcel_Cell::stringFromColumnIndex($column_first_index);
			$column_to_name = PHPExcel_Cell::stringFromColumnIndex($column_first_index + 1);
			$sheet->mergeCells($column_from_name.$row_number.':'.$column_to_name.$row_number);
			
			$sheet->setCellValueByColumnAndRow($column_first_index + 2, $row_number, "Customer");
			$column_from_name = PHPExcel_Cell::stringFromColumnIndex($column_first_index + 2);
			$column_to_name = PHPExcel_Cell::stringFromColumnIndex($column_first_index + 3);
			$sheet->mergeCells($column_from_name.$row_number.':'.$column_to_name.$row_number);
			
			$sheet->setCellValueByColumnAndRow($column_first_index + 4, $row_number, "Supervisor");
			$column_from_name = PHPExcel_Cell::stringFromColumnIndex($column_first_index + 4);
			$column_to_name = PHPExcel_Cell::stringFromColumnIndex($column_first_index + 5);
			$sheet->mergeCells($column_from_name.$row_number.':'.$column_to_name.$row_number);
			
			$sheet->setCellValueByColumnAndRow($column_first_index + 6, $row_number, "Juru Tally");
			$column_from_name = PHPExcel_Cell::stringFromColumnIndex($column_first_index + 6);
			$column_to_name = PHPExcel_Cell::stringFromColumnIndex($column_first_index + 7);
			$sheet->mergeCells($column_from_name.$row_number.':'.$column_to_name.$row_number);
			
			$sheet->setCellValueByColumnAndRow($column_first_index + 8, $row_number, "Warehouse");
			$column_from_name = PHPExcel_Cell::stringFromColumnIndex($column_first_index + 8);
			$column_to_name = PHPExcel_Cell::stringFromColumnIndex($column_first_index + 9);
			$sheet->mergeCells($column_from_name.$row_number.':'.$column_to_name.$row_number);
			
			$column_from_name = PHPExcel_Cell::stringFromColumnIndex($column_first_index);
			$column_to_name = PHPExcel_Cell::stringFromColumnIndex($column_first_index + 9);
			$sheet->getStyle(
				$column_from_name. ($row_number - 3) .':'.$column_to_name.$row_number
			)->applyFromArray($table_styles);
			
			$row_number++;
			
			$row_number += 1;
			$sheet->setCellValueByColumnAndRow($column_first_index, $row_number, "Catatan");
			$column_from_name = PHPExcel_Cell::stringFromColumnIndex($column_first_index);
			$column_to_name = PHPExcel_Cell::stringFromColumnIndex($column_first_index + 9);
			$sheet->mergeCells($column_from_name.$row_number.':'.$column_to_name. ($row_number + 3));
			$table_custom_styles = $table_styles;
			$table_custom_styles['alignment']['vertical'] = PHPExcel_Style_Alignment::VERTICAL_TOP;
			$sheet->getStyle(
				$column_from_name.$row_number.':'.$column_to_name. ($row_number + 3)
			)->applyFromArray($table_custom_styles);
			
			$row_number++;
			
			$row_number += 4;
			$sheet->setCellValueByColumnAndRow($column_first_index, $row_number, "Jam In");
			$sheet->setCellValueByColumnAndRow($column_first_index + 1, $row_number, ":");
			$sheet->setCellValueByColumnAndRow($column_first_index + 2, $row_number, "WIB");
			$row_number++;
			$sheet->setCellValueByColumnAndRow($column_first_index, $row_number, "Jam Out");
			$sheet->setCellValueByColumnAndRow($column_first_index + 1, $row_number, ":");
			$sheet->setCellValueByColumnAndRow($column_first_index + 2, $row_number, "WIB");
			$row_number++;
			
			/* -- Write the file based on file type -- */
			$file_writer = PHPExcel_IOFactory::createWriter($file_excel, 'Excel2007');
		}
		catch(Exception $e)
		{
			throw new Exception($e->getMessage());
		}
		
		/* -- Output by streaming -- */
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'.$file_name.'"');
		header('Cache-Control: max-age=0');

		$file_writer->save('php://output'); 
	}
	
	public function label_document()
	{
		if (!is_authorized('material/inventory_receive', 'index')) 
			access_denied();
		
		$id = $this->input->get_post('id');
		
		// -- Get Header --
		$this->db
			->select("ir.id, ir.code, ir.receive_date, ir.vehicle_no, ir.vehicle_driver")
			->select("oid.c_orderin_id, oi.code c_orderin_code, oi.external_no c_orderin_external_no")
			->from('m_inventory_receives ir')
			->join('m_inventory_receivedetails ird', "ird.m_inventory_receive_id = ir.id")
			->join('c_orderindetails oid', "oid.id = ird.c_orderindetail_id")
			->join('c_orderins oi', "oi.id = oid.c_orderin_id")
			->where('ir.id', $id)
			->group_by(
				array(
					'ir.id', 'ir.code', 'ir.receive_date', 'ir.vehicle_no', 'ir.vehicle_driver',
					'oid.c_orderin_id', 'oi.code', 'oi.external_no'
				)
			);
		$this->lib_custom->project_query_filter('oi.c_project_id', $this->c_project_ids);
		$table = $this->db
			->get();
		if ($table->num_rows() == 0)
			show_error("ASN not found", 400);
		$m_inventory_receive = $table->first_row();
		
		// -- Get Details -- 
		$this->db
			->select_max('ird.id')
			->select("oid.m_product_id, pro.code m_product_code, pro.name m_product_name, pro.uom m_product_uom")
			->select_if_null("SUM(ird.quantity_box)", 0, 'quantity_box')
			->select_if_null("SUM(ird.quantity)", 0, 'quantity')
			->from('m_inventory_receivedetails ird')
			->join('c_orderindetails oid', "oid.id = ird.c_orderindetail_id")
			->join('c_orderins oi', "oi.id = oid.c_orderin_id")
			->join('m_products pro', "pro.id = oid.m_product_id")
			->where('ird.m_inventory_receive_id', $m_inventory_receive->id)
			->group_by(
				array(
					'oid.m_product_id', 'pro.code', 'pro.name', 'pro.uom'
				)
			)
			->order_by('id', 'asc');
		$this->lib_custom->project_query_filter('oi.c_project_id', $this->c_project_ids);
		$table = $this->db->get();
		$m_inventory_receivedetails = $table->result();
		
		$this->label_document_pdf($m_inventory_receive, $m_inventory_receivedetails);
	}
	
	protected function label_document_pdf($header, $details)
	{
		$user_name = $this->session->userdata('name');
		
		$data = array(
			'user_name'	=> $user_name,
			'header'	=> $header,
			'details'	=> $details
		);
		$html = $this->load->view('material/inventory_receive/label_document', $data, TRUE);
		
		// $this->output
			// ->set_output($html);
		
		$this->load->library('lib_dompdf');
		$this->lib_dompdf->show_page_number(TRUE);
		$this->lib_dompdf->load_as_pdf($html, 'label.pdf', 'a4', 'portrait');
	}
	
	public function upload()
	{
		if (!is_authorized('material/inventory_receive', 'insert'))
			access_denied();
		
		$user_id = $this->session->userdata('user_id');
		
		$result = new stdClass();
		$result->response = FALSE;
		$result->value = NULL;
		$result->data = array();
		
		$this->load->helper('file');
		$file_element = 'file';
		$file_full_name = $_FILES[$file_element]['name'];
		$file_name = get_file_without_extention($file_full_name).'_'. date('YmdHis') .'_'.$user_id.'.'.get_extention($file_full_name);
		
		$config = array(
			'upload_path'	=> './upload/wms/receive',
			'allowed_types'	=> 'xls|xlsx',
			'file_name'		=> $file_name,
			'overwrite'		=> TRUE
		);
		
		$this->load->library('upload', $config);
		if (!$this->upload->do_upload($file_element))
		{
			$result->value = $this->upload->display_errors();
			$result->response = FALSE;
		}
		else
		{
			$file_data = $this->upload->data();
			
			$this->load->library('excel');
			try
			{
				$excel_sheets = $this->excel->read_file($file_data['full_path']);
				
				$sheet_error_messages = array();
				
				$m_inventory_receives = array();
				foreach ($excel_sheets as $excel_sheet_name=>$excel_sheet)
				{
					try
					{
						$m_inventory_receive = new stdClass();
						
						$c_orderin_code = $this->excel->read_value($excel_sheet, 3, 2);
						$table = $this->db
							->select('id')
							->from('c_orderins')
							->where('code', $c_orderin_code)
							->get();
						if ($table->num_rows() == 0)
							throw new Exception("Order in with code '".$c_orderin_code." not found.'");
						$table_record = $table->first_row();
						$c_orderin_id = $table_record->id;
						
						$m_inventory_receive->receive_date = $this->excel->read_value($excel_sheet, 4, 2, 'date');
						$m_inventory_receive->notes = $this->excel->read_value($excel_sheet, 5, 2);
						
						$m_inventory_receive->vehicle_no = $this->excel->read_value($excel_sheet, 3, 6);
						$m_inventory_receive->vehicle_driver = $this->excel->read_value($excel_sheet, 4, 6);
						$m_inventory_receive->transport_mode = $this->excel->read_value($excel_sheet, 5, 6);
						
						$m_inventory_receive->m_inventory_receivedetails = array();
						for ($m_inventory_receivedetail_count = 11; $m_inventory_receivedetail_count < count($excel_sheet); $m_inventory_receivedetail_count++)
						{
							$m_inventory_receivedetail = new stdClass();
							
							$m_product_code = $this->excel->read_value($excel_sheet, $m_inventory_receivedetail_count, 1);
							$m_inventory_receivedetail->quantity_box = $this->excel->read_value($excel_sheet, $m_inventory_receivedetail_count, 3);
							$m_inventory_receivedetail->quantity = $this->excel->read_value($excel_sheet, $m_inventory_receivedetail_count, 4);
							$m_inventory_receivedetail->condition = $this->excel->read_value($excel_sheet, $m_inventory_receivedetail_count, 6);
							$m_grid_code = $this->excel->read_value($excel_sheet, $m_inventory_receivedetail_count, 7);
							$m_inventory_receivedetail->supervisor = $this->excel->read_value($excel_sheet, $m_inventory_receivedetail_count, 8);
							$m_inventory_receivedetail->notes = $this->excel->read_value($excel_sheet, $m_inventory_receivedetail_count, 9);
							
							$m_inventory_receivedetail->m_grid_id = NULL;
							if (!empty($m_grid_code))
							{
								$m_grids_query = $this->db
									->select("gri.id")
									->from('m_grids gri')
									->where('gri.code', $m_grid_code)
									->get();
								if ($m_grids_query->num_rows() > 0)
								{
									$m_grids_record = $m_grids_query->first_row();
									$m_inventory_receivedetail->m_grid_id = $m_grids_record->id;
								}
								else
									throw new Exception("Unknown location '".$m_grid_code."'.");
							}
							
							if (empty($m_product_code) && empty($m_inventory_receivedetail->quantity_box) && empty($m_inventory_receivedetail->quantity))
								continue;
							
							$table = $this->db
								->select('oid.id')
								->from('c_orderindetails oid')
								->join('m_products pro', "pro.id = oid.m_product_id")
								->where('oid.c_orderin_id', $c_orderin_id)
								->where('pro.code', $m_product_code)
								->get();
							if ($table->num_rows() == 0)
								throw new Exception("Product with code '".$m_product_code." in Order in '".$c_orderin_code."' not found.'");
							$table_record = $table->first_row();
							$m_inventory_receivedetail->c_orderindetail_id = $table_record->id;
							
							$m_inventory_receive->m_inventory_receivedetails[] = $m_inventory_receivedetail;
						}
						
						$m_inventory_receives[] = $m_inventory_receive;
					}
					catch(Exception $e)
					{
						$sheet_error_messages[$excel_sheet_name] = $e->getMessage();
					}
				}
				
				$error_messages = array();
				foreach ($sheet_error_messages as $sheet_error_messages_sheet=>$sheet_error_messages_message)
					$error_messages[] = "Sheet '".$sheet_error_messages_sheet."', ".$sheet_error_messages_message;
				if (count($error_messages) > 0)
					$result->value = implode(', ', $error_messages);
				
				if (count($m_inventory_receives) > 0)
				{
					$this->load->library('material/lib_inventory_in');
					
					$this->db->trans_begin();
					try
					{
						foreach ($m_inventory_receives as $m_inventory_receive)
						{
							$data_header = new stdClass();
							
							$data_header->code = generate_code_number("ASN". date('ymd-'), NULL, 3);
							$data_header->receive_date = $m_inventory_receive->receive_date;
							$data_header->vehicle_no = $m_inventory_receive->vehicle_no;
							$data_header->vehicle_driver = $m_inventory_receive->vehicle_driver;
							$data_header->transport_mode = $m_inventory_receive->transport_mode;
							$data_header->notes = $m_inventory_receive->notes;
							$id = $this->lib_inventory_in->receive_add($data_header, $user_id);
							
							foreach ($m_inventory_receive->m_inventory_receivedetails as $m_inventory_receivedetail)
							{
								$data_detail = new stdClass();
								$data_detail->m_inventory_receive_id = $id;
								$data_detail->c_orderindetail_id = $m_inventory_receivedetail->c_orderindetail_id;
								$data_detail->quantity_box = $m_inventory_receivedetail->quantity_box;
								$data_detail->quantity = $m_inventory_receivedetail->quantity;
								$data_detail->condition = $m_inventory_receivedetail->condition;
								$data_detail->m_grid_id = $m_inventory_receivedetail->m_grid_id;
								$data_detail->supervisor = $m_inventory_receivedetail->supervisor;
								$data_detail->notes = $m_inventory_receivedetail->notes;
								$this->lib_inventory_in->receivedetail_add($data_detail, $user_id);
							}
						}
						
						if ($this->db->trans_status() === FALSE)
						{
							$error = $this->db->error();
							throw new Exception("Error Database, Code : " . $error['code'] . " Message : " . $error['message']);
						}
						else
						{
							$this->db->trans_commit();
							$result->response = TRUE;
						}
					}
					catch(Exception $e)
					{
						$this->db->trans_rollback();
						throw new Exception($e->getMessage());
					}
				}
				
				if (count($error_messages) == 0 && count($m_inventory_receives) == 0)
					$result->value = "No data uploaded.";
			}
			catch(Exception $e)
			{
				$result->value = $e->getMessage();
			}
		}
		
		$this->result_json($result);
	}
}