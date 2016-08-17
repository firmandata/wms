<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Inventory_picklist_panen extends MY_Controller 
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
		if (!is_authorized('material/inventory_picklist_panen', 'index')) 
			access_denied();
		
		$data = array(
		);
		
		$content = array(
			'title'		=> "Shipment",
			'content' 	=> $this->load->view('material/inventory_picklist_panen/index', $data, TRUE)
		);
		$this->_load_layout($content);
	}
	
	public function get_list_json()
	{
		if (!is_authorized('material/inventory_picklist_panen', 'index')) 
			access_denied();
		
		$this->load->helper('date');
		
		$from_month = $this->input->get_post('from_month');
		$from_year = $this->input->get_post('from_year');
		
		$to_month = $this->input->get_post('to_month');
		$to_year = $this->input->get_post('to_year');
		
		$this->db
			->select("ipl.id, ipl.code, ipl.picklist_date")
			->select("ipl.supervisor, ipl.schedule_phase, ipl.schedule_time")
			->select("ipl.shipment_type, ipl.transport_mode, ipl.shipment_to, ipl.vehicle_no, ipl.vehicle_driver")
			->select("ood.c_orderout_id, oo.code c_orderout_code, oo.orderout_date c_orderout_date, oo.estimation_harvest_time c_orderout_estimation_harvest_time")
			->select("oo.c_businesspartner_id, bp.name c_businesspartner_name")
			->select("oo.c_project_id, prj.name c_project_name")
			->select("ipl.status_inventory_picking")
			->from('m_inventory_picklists ipl')
			->join('m_inventory_picklistdetails ipld', "ipld.m_inventory_picklist_id = ipl.id", 'left')
			->join('c_orderoutdetails ood', "ood.id = ipld.c_orderoutdetail_id", 'left')
			->join('c_orderouts oo', "oo.id = ood.c_orderout_id", 'left')
			->join('c_businesspartners bp', "bp.id = oo.c_businesspartner_id", 'left')
			->join('c_projects prj', "prj.id = oo.c_project_id", 'left')
			->group_by(
				array(
					'ipl.id', 'ipl.code', 'ipl.picklist_date',
					'ipl.supervisor', 'ipl.schedule_phase', 'ipl.schedule_time',
					'ipl.shipment_type', 'ipl.transport_mode', 'ipl.shipment_to', 'ipl.vehicle_no', 'ipl.vehicle_driver',
					'ood.c_orderout_id', 'oo.code', 'oo.orderout_date', 'oo.estimation_harvest_time',
					'oo.c_businesspartner_id', 'bp.name',
					'oo.c_project_id', 'prj.name',
					'ipl.status_inventory_picking'
				)
			);
		$this->db->where("ipl.picklist_date >=", date('Y-m-d', mktime(0, 0, 0, $from_month, 1, $from_year)));
		$this->db->where("ipl.picklist_date <=", add_date(date('Y-m-d', mktime(0, 0, 0, $to_month, 1, $to_year)), -1, 1));
		$this->db->where("ipl.picklist_orderout_type", 0);
		
		$this->lib_custom->project_query_filter('oo.c_project_id', $this->c_project_ids);
		
		parent::_get_list_json();
	}
	
	public function form()
	{
		if (!is_authorized('material/inventory_picklist_panen', 'index')) 
			access_denied();
		
		$form_action = $this->input->get_post('form_action');
		$id = $this->input->get_post('id');
		$record = NULL;
		if ($id !== NULL)
		{
			$this->db
				->select("ipl.id, ipl.code, ipl.picklist_date")
				->select("ipl.shipment_type, ipl.transport_mode, ipl.shipment_to, ipl.vehicle_no, ipl.vehicle_driver")
				->select("ipl.status_inventory_picking, ipl.notes")
				->from('m_inventory_picklists ipl')
				->where('ipl.id', $id);
			$table = $this->db->get();
			if ($table->num_rows() > 0)
			{
				$record = $table->first_row();
				if ($record->status_inventory_picking == 'COMPLETE')
					show_error("Pick list was complete", 400);
			}
			else
				show_error("Pick list not found", 400);
		}
		
		$data = array(
			'form_action'	=> $form_action,
			'record'		=> $record
		);
		$this->load->view('material/inventory_picklist_panen/form', $data);
	}
	
	public function form_detail()
	{
		if (!is_authorized('material/inventory_picklist_panen', 'index')) 
			access_denied();
		
		$id = $this->input->get_post('id');
		
		$this->db
			->select("ipl.id, ipl.code, ipl.picklist_date")
			->select("ipl.shipment_type, ipl.transport_mode, ipl.shipment_to, ipl.vehicle_no, ipl.vehicle_driver")
			->select("ipl.status_inventory_picking, ipl.notes")
			->from('m_inventory_picklists ipl')
			->where('ipl.id', $id);
		$table = $this->db->get();
		if ($table->num_rows() > 0)
		{
			$record = $table->first_row();
			if ($record->status_inventory_picking == 'COMPLETE')
				show_error("Pick list was complete", 400);
		}
		else
			show_error("Pick list not found", 400);
		
		$data = array(
			'id'	=> $id
		);
		$this->load->view('material/inventory_picklist_panen/form_detail', $data);
	}
	
	public function get_list_form_ref_json()
	{
		if (!is_authorized('material/inventory_picklist_panen', 'index')) 
			access_denied();
		
		$this->db
			->select("ood.id, ood.quantity_box, ood.quantity, ood.size, ood.price, ood.notes")
			->select("ood.m_product_id, pro.code m_product_code, pro.name m_product_name, pro.uom m_product_uom, pro.type m_product_type, pro.casing m_product_casing")
			->select("oo.code c_orderout_code, oo.orderout_date c_orderout_date, oo.estimation_harvest_time c_orderout_estimation_harvest_time")
			->select("oo.c_businesspartner_id, bp.code c_businesspartner_code, bp.name c_businesspartner_name")
			->select("oo.c_project_id, prj.name c_project_name")
			->select("oo.origin c_orderout_origin, oo.marketing_unit c_orderout_marketing_unit, oo.external_no c_orderout_external_no, oo.no_surat_jalan c_orderout_no_surat_jalan")
			->from('c_orderoutdetails ood')
			->join('m_products pro', "pro.id = ood.m_product_id")
			->join('c_orderouts oo', "oo.id = ood.c_orderout_id")
			->join('c_businesspartners bp', "bp.id = oo.c_businesspartner_id")
			->join('c_projects prj', "prj.id = oo.c_project_id", 'left')
			->where('ood.status_inventory_picklist <>', 'COMPLETE')
			->where('oo.orderout_type', 0);
		
		$this->lib_custom->project_query_filter('oo.c_project_id', $this->c_project_ids);
		
		parent::_get_list_json();
	}
	
	public function get_list_form_json()
	{
		if (!is_authorized('material/inventory_picklist_panen', 'index')) 
			access_denied();
		
		$this->db
			->select("MAX(ipld.id) id", FALSE)
			->select("ipld.m_inventory_picklist_id, ipld.c_orderoutdetail_id")
			->select("ipld.m_product_id, pro.code m_product_code, pro.name m_product_name, pro.uom m_product_uom, pro.type m_product_type, pro.casing m_product_casing")
			->select("ipld.m_grid_id, gri.code m_grid_code")
			->select("ipld.carton_no, ipld.product_size")
			->select("ipld.c_project_id, prj.name c_project_name")
			->select("SUM(ipld.quantity_box) quantity_box", FALSE)
			->select("SUM(ipld.quantity) quantity", FALSE)
			->from('m_inventory_picklistdetails ipld')
			->join('c_orderoutdetails ood', "ood.id = ipld.c_orderoutdetail_id", 'left')
			->join('c_orderouts oo', "oo.id = ood.c_orderout_id", 'left')
			->join('m_grids gri', "gri.id = ipld.m_grid_id")
			->join('m_products pro', "pro.id = ipld.m_product_id", 'left')
			->join('c_projects prj', "prj.id = ipld.c_project_id", 'left')
			->group_by(
				array(
					'ipld.m_inventory_picklist_id', 'ipld.c_orderoutdetail_id', 
					'ipld.m_product_id', 'pro.code', 'pro.name', 'pro.uom', 'pro.type', 'pro.casing',
					'ipld.m_grid_id', 'gri.code',
					'ipld.carton_no', 'ipld.product_size',
					'ipld.c_project_id', 'prj.name'
				)
			);
		
		$id = $this->input->get_post('id');
		if ($id !== '')
			$this->db->where("ipld.m_inventory_picklist_id", $id);
		
		$this->lib_custom->project_query_filter('oo.c_project_id', $this->c_project_ids);
		
		parent::_get_list_json();
	}
	
	public function get_form_inventory_list_json()
	{
		if (!is_authorized('material/inventory_picking', 'index')) 
			access_denied();
		
		$m_product_id = $this->input->get_post('m_product_id');
		$c_project_id = $this->input->get_post('c_project_id');
		
		$inventory_sql = 
			 "(SELECT	  inv.m_product_id, inv.m_grid_id, inv.c_project_id "
			."			, inv.carton_no, inv.product_size "
			."			, ".$this->db->if_null("SUM(inv.quantity)", 0)." quantity_exist "
			."			, ".$this->db->if_null("SUM(inv.quantity_allocated)", 0)." quantity_allocated "
			."			, ".$this->db->if_null("SUM(inv.quantity_picked)", 0)." quantity_picked "
			."			, ".$this->db->if_null("SUM(inv.quantity_onhand)", 0)." quantity_onhand "
			." FROM		m_inventories inv ";
		$criterias = array();
		if ($m_product_id)
			$criterias[] = "inv.m_product_id = ". (int) $m_product_id;
		else
			$criterias[] = "inv.m_product_id = 0";
		$criterias[] = "inv.quantity_box > 0";
		$criterias[] = "inv.quantity > 0";
		if ($c_project_id && in_array($c_project_id, $this->c_project_ids))
			$criterias[] = "(inv.c_project_id = ". (int) $c_project_id . " OR inv.c_project_id IS NULL)";
		else
			$criterias[] = $this->lib_custom->project_sql_filter('inv.c_project_id', $this->c_project_ids);
		if (count($criterias))
			$inventory_sql .= " WHERE ". implode(" AND ", $criterias);
		$inventory_sql .= 
			 " GROUP	BY "
			."			  inv.m_product_id, inv.m_grid_id, inv.c_project_id "
			."			, inv.carton_no, inv.product_size "
			.") inv ";
		
		$this->db
			->select("inv.m_product_id")
			->select("grd.id m_grid_id, grd.code m_grid_code")
			->select("wh.id m_warehouse_id, wh.code m_warehouse_code, wh.name m_warehouse_name")
			->select("inv.carton_no, inv.product_size")
			->select("prj.id c_project_id, prj.name c_project_name")
			->select("inv.quantity_exist, inv.quantity_allocated, inv.quantity_picked, inv.quantity_onhand")
			->from($inventory_sql, FALSE)
			->join('m_grids grd', "grd.id = inv.m_grid_id")
			->join('m_warehouses wh', "wh.id = grd.m_warehouse_id")
			->join('c_projects prj', "prj.id = inv.c_project_id", 'left');
		
		parent::_get_list_json();
	}
	
	public function detail()
	{
		if (!is_authorized('material/inventory_picklist_panen', 'index')) 
			access_denied();
		
		$id = $this->input->get_post('id');
		$this->db
			->select("ipl.id, ipl.code, ipl.picklist_date")
			->select("ipl.shipment_type, ipl.transport_mode, ipl.shipment_to, ipl.vehicle_no, ipl.vehicle_driver")
			->select("ipl.status_inventory_picking, ipl.notes")
			->from('m_inventory_picklists ipl')
			->where('ipl.id', $id);
		$table = $this->db->get();
		if ($table->num_rows() > 0)
			$record = $table->first_row();
		else
			show_error("Pick list not found", 400);
		
		$data = array(
			'record'	=> $record
		);
		$this->load->view('material/inventory_picklist_panen/detail', $data);
	}
	
	public function get_list_detail_json()
	{
		if (!is_authorized('material/inventory_picklist_panen', 'index')) 
			access_denied();
		
		$this->db
			->select("MAX(ipld.id) id", FALSE)
			->select("ood.c_orderout_id")
			->select("ipld.m_product_id, pro.code m_product_code, pro.name m_product_name, pro.uom m_product_uom, pro.type m_product_type, pro.casing m_product_casing")
			->select("ipld.m_grid_id, gri.code m_grid_code")
			->select("ipld.carton_no, ipld.product_size")
			->select("ipld.c_project_id, prj.name c_project_name")
			->select("SUM(ipld.quantity_box) quantity_box", FALSE)
			->select("SUM(ipld.quantity) quantity", FALSE)
			->select_if_null('SUM(ipgd.quantity_box)', 0, 'quantity_box_used')
			->select_if_null('SUM(ipgd.quantity)', 0, 'quantity_used')
			->select("oo.code c_orderout_code, oo.orderout_date c_orderout_date, oo.estimation_harvest_time c_orderout_estimation_harvest_time")
			->select("oo.c_businesspartner_id, bp.name c_businesspartner_name")
			->select("ipld.status_inventory_picking")
			->from('m_inventory_picklistdetails ipld')
			->join('c_orderoutdetails ood', "ood.id = ipld.c_orderoutdetail_id")
			->join('m_grids gri', "gri.id = ipld.m_grid_id")
			->join('c_orderouts oo', "oo.id = ood.c_orderout_id")
			->join('c_businesspartners bp', "bp.id = oo.c_businesspartner_id")
			->join('m_products pro', "pro.id = ipld.m_product_id", 'left')
			->join('c_projects prj', "prj.id = ipld.c_project_id", 'left')
			->join(
				 "(SELECT m_inventory_picklistdetail_id, "
				."		  " . $this->db->if_null("SUM(quantity_box)", 0) . " quantity_box, "
				."		  " . $this->db->if_null("SUM(quantity)", 0) . " quantity "
				."	 FROM m_inventory_pickingdetails "
				."  GROUP BY m_inventory_picklistdetail_id "
				.") ipgd", 
				"ipgd.m_inventory_picklistdetail_id = ipld.id", 'left')
			->group_by(
				array(
					'ood.c_orderout_id', 
					'ipld.m_product_id', 'pro.code', 'pro.name', 'pro.uom', 'pro.type', 'pro.casing',
					'ipld.m_grid_id', 'gri.code',
					'ipld.carton_no', 'ipld.product_size',
					'oo.code', 'oo.orderout_date', 'oo.estimation_harvest_time',
					'oo.c_businesspartner_id', 'bp.name',
					'ipld.c_project_id', 'prj.name',
					'ipld.status_inventory_picking'
				)
			);
		
		$id = $this->input->get_post('id');
		if ($id !== '')
			$this->db->where("ipld.m_inventory_picklist_id", $id);
		
		$this->lib_custom->project_query_filter('oo.c_project_id', $this->c_project_ids);
		
		parent::_get_list_json();
	}
	
	public function insert()
	{
		if (!is_authorized('material/inventory_picklist_panen', 'insert')) 
			access_denied();
		
		$this->load->library('material/lib_inventory_out');
		
		$user_id = $this->session->userdata('user_id');
		
		$data = new stdClass();
		$data->code = generate_code_number("DPP". date('ymd-'), NULL, 3);
		$data->picklist_date = $this->input->post('picklist_date');
		$data->picklist_orderout_type = 0;
		$data->shipment_type = $this->input->post('shipment_type');
		$data->transport_mode = $this->input->post('transport_mode');
		$data->shipment_to = $this->input->post('shipment_to');
		$data->vehicle_no = $this->input->post('vehicle_no');
		$data->vehicle_driver = $this->input->post('vehicle_driver');
		$data->notes = $this->input->post('notes');
		
		parent::_execute('lib_inventory_out', 'picklist_add', 
			array($data, $user_id),
			array(
				array('field' => 'picklist_date', 'label' => 'Date', 'rules' => 'required')
			)
		);
	}
	
	public function insert_detail()
	{
		if (!is_authorized('material/inventory_picklist_panen', 'insert')) 
			access_denied();
		
		$user_id = $this->session->userdata('user_id');
		
		$this->load->library('material/lib_inventory_out');
		
		$data = new stdClass();
		$data->m_inventory_picklist_id = $this->input->post('m_inventory_picklist_id');
		$data->c_orderoutdetail_id = $this->input->post('c_orderoutdetail_id');
		$grid_code = $this->input->post('grid_code');
		if ($grid_code !== '')
		{
			$table = $this->db
				->select('id')
				->from('m_grids')
				->where('code', $grid_code)
				->get();
			if ($table->num_rows() > 0)
			{
				$table_record = $table->first_row();
				$data->m_grid_id = $table_record->id;
			}
		}
		$data->quantity = $this->input->post('quantity');
		$data->tolerance = $this->input->post('tolerance');
		
		parent::_execute('lib_inventory_out', 'picklistdetail_add_by_properties', 
			array($data, $user_id),
			array(
				array('field' => 'm_inventory_picklist_id', 'label' => 'Inventory Pick List', 'rules' => 'integer|required'),
				array('field' => 'c_orderoutdetail_id', 'label' => 'Order Out Detail', 'rules' => 'integer|required')
			)
		);
	}
	
	public function insert_detail_manual()
	{
		if (!is_authorized('material/inventory_picklist_panen', 'insert')) 
			access_denied();
		
		parent::_execute('this', '_insert_detail_manual', 
			array(),
			array(
				array('field' => 'm_inventory_picklist_id', 'label' => 'Inventory Pick List', 'rules' => 'integer|required'),
				array('field' => 'c_orderoutdetail_id', 'label' => 'Order Out Detail', 'rules' => 'integer|required')
			)
		);
	}
	
	protected function _insert_detail_manual()
	{
		$user_id = $this->session->userdata('user_id');
		
		$this->load->library('material/lib_inventory_out');
		
		$records = $this->input->post('records');
		if (!empty($records) && is_array($records))
		{
			foreach ($records as $record_idx=>$record)
			{
				$data = new stdClass();
				$data->m_inventory_picklist_id = $this->input->post('m_inventory_picklist_id');
				$data->c_orderoutdetail_id = $this->input->post('c_orderoutdetail_id');
				$data->m_product_id = $record['m_product_id'];
				$data->m_grid_id = $record['m_grid_id'];
				$data->carton_no = $record['carton_no'];
				$data->product_size = $record['product_size'];
				$data->quantity = $record['quantity'];
				$data->tolerance = 0;
				
				$this->lib_inventory_out->picklistdetail_add_by_properties($data, $user_id);
			}
		}
	}
	
	public function get_detail_counter()
	{
		if (!is_authorized('material/inventory_picklist_panen', 'insert')) 
			access_denied();
		
		$pickedlist_total = new stdClass();
		
		$pickedlist_total->c_order_out = new stdClass();
		$pickedlist_total->c_order_out->quantity_box = 0;
		$pickedlist_total->c_order_out->quantity = 0;
		$pickedlist_total->c_order_out->quantity_box_used = 0;
		$pickedlist_total->c_order_out->quantity_used = 0;
		
		$pickedlist_total->m_inventory_picklistdetail = new stdClass();
		$pickedlist_total->m_inventory_picklistdetail->quantity_box = 0;
		$pickedlist_total->m_inventory_picklistdetail->quantity = 0;
		
		$c_orderoutdetail_id = $this->input->get_post('c_orderoutdetail_id');
		if (!empty($c_orderoutdetail_id))
		{
			$table = $this->db
				->select('ood.quantity_box')
				->select('ood.quantity')
				->from('c_orderoutdetails ood')
				->where('ood.id', $c_orderoutdetail_id)
				->get();
			if ($table->num_rows() > 0)
			{
				$record = $table->first_row();
				$pickedlist_total->c_order_out->quantity_box = $record->quantity_box;
				$pickedlist_total->c_order_out->quantity = $record->quantity;
			}
			
			$table = $this->db
				->select_if_null('SUM(ipld.quantity_box)', 0, 'quantity_box')
				->select_if_null('SUM(ipld.quantity)', 0, 'quantity')
				->from('m_inventory_picklistdetails ipld')
				->where('ipld.c_orderoutdetail_id', $c_orderoutdetail_id)
				->get();
			if ($table->num_rows() > 0)
			{
				$record = $table->first_row();
				$pickedlist_total->c_order_out->quantity_box_used = $record->quantity_box;
				$pickedlist_total->c_order_out->quantity_used = $record->quantity;
			}
		}
		
		$m_inventory_picklist_id = $this->input->get_post('m_inventory_picklist_id');
		if (!empty($m_inventory_picklist_id))
		{
			$table = $this->db
				->select_if_null('SUM(ipld.quantity_box)', 0, 'quantity_box')
				->select_if_null('SUM(ipld.quantity)', 0, 'quantity')
				->from('m_inventory_picklistdetails ipld')
				->where('ipld.m_inventory_picklist_id', $m_inventory_picklist_id)
				->get();
			if ($table->num_rows() > 0)
			{
				$record = $table->first_row();
				$pickedlist_total->m_inventory_picklistdetail->quantity_box = $record->quantity_box;
				$pickedlist_total->m_inventory_picklistdetail->quantity = $record->quantity;
			}
		}
		
		$response = new stdClass();
		$response->response = TRUE;
		$response->value = $pickedlist_total;
		$response->data = array();
		
		$this->result_json($response);
	}
	
	public function update($id)
	{
		if (!is_authorized('material/inventory_picklist_panen', 'update')) 
			access_denied();
		
		$this->load->library('material/lib_inventory_out');
		
		$user_id = $this->session->userdata('user_id');
		
		$data = new stdClass();
		$data->code = $this->input->post('code');
		$data->picklist_date = $this->input->post('picklist_date');
		$data->picklist_orderout_type = 0;
		$data->shipment_type = $this->input->post('shipment_type');
		$data->transport_mode = $this->input->post('transport_mode');
		$data->shipment_to = $this->input->post('shipment_to');
		$data->vehicle_no = $this->input->post('vehicle_no');
		$data->vehicle_driver = $this->input->post('vehicle_driver');
		$data->notes = $this->input->post('notes');
		
		parent::_execute('lib_inventory_out', 'picklist_update', 
			array($id, $data, $user_id), 
			array(
				array('field' => 'code', 'label' => 'No', 'rules' => 'required'),
				array('field' => 'picklist_date', 'label' => 'Date', 'rules' => 'required')
			)
		);
	}
	
	public function picking_shipment($id)
	{
		if (!is_authorized('material/inventory_picklist_panen', 'update')) 
			access_denied();
		
		$this->load->library('material/lib_inventory_out');
		
		$user_id = $this->session->userdata('user_id');
		
		parent::_execute('lib_inventory_out', 'picklist_generate_picking_shipment', 
			array($id, $user_id)
		);
	}
	
	public function delete($id)
	{
		if (!is_authorized('material/inventory_picklist_panen', 'delete')) 
			access_denied();
		
		$this->load->library('material/lib_inventory_out');
		
		$user_id = $this->session->userdata('user_id');
		
		parent::_execute('lib_inventory_out', 'picklist_remove', array($id, $user_id));
	}
	
	public function delete_detail()
	{
		if (!is_authorized('material/inventory_picklist_panen', 'delete')) 
			access_denied();
		
		$user_id = $this->session->userdata('user_id');
		
		$this->load->library('material/lib_inventory_out');
		
		$data = new stdClass();
		$data->m_inventory_picklist_id = $this->input->post('m_inventory_picklist_id');
		$data->c_orderoutdetail_id = $this->input->post('c_orderoutdetail_id');
		$m_product_id = $this->input->post('m_product_id');
		if ($m_product_id !== '')
			$data->m_product_id = $m_product_id;
		else
			$data->m_product_id = NULL;
		$m_grid_id = $this->input->post('m_grid_id');
		if ($m_grid_id !== '')
			$data->m_grid_id = $m_grid_id;
		else
			$data->m_grid_id = NULL;
		$carton_no = $this->input->post('carton_no');
		if ($carton_no !== '')
			$data->carton_no = $carton_no;
		else
			$data->carton_no = NULL;
		$product_size = $this->input->post('product_size');
		if ($product_size !== '')
			$data->product_size = $product_size;
		else
			$data->product_size = NULL;
		$c_project_id = $this->input->post('c_project_id');
		if ($c_project_id !== '')
			$data->c_project_id = $c_project_id;
		else
			$data->c_project_id = NULL;
		
		parent::_execute('lib_inventory_out', 'picklistdetail_remove_by_properties', 
			array($data, $user_id),
			array(
				array('field' => 'm_inventory_picklist_id', 'label' => 'Inventory Pick List', 'rules' => 'integer|required'),
				array('field' => 'c_orderoutdetail_id', 'label' => 'Order Out Detail', 'rules' => 'integer|required')
			)
		);
	}

	public function invoice_printout()
	{
		if (!is_authorized('material/inventory_picklist_panen', 'index')) 
			access_denied();
		
		$id = $this->input->get_post('id');
		$this->db
			->select("ipl.id, ipl.code, ipl.picklist_date")
			->select("ipl.shipment_type, ipl.transport_mode, ipl.shipment_to, ipl.vehicle_no, ipl.vehicle_driver")
			->select("ipl.status_inventory_picking, ipl.notes")
			->from('m_inventory_picklists ipl')
			->where('ipl.id', $id);
		$table = $this->db->get();
		if ($table->num_rows() > 0)
			$record = $table->first_row();
		else
			show_error("Pick list not found", 400);
		
		$this->db
			->select("ood.c_orderout_id")
			->select("oo.code c_orderout_code, oo.orderout_date c_orderout_date, oo.estimation_harvest_time c_orderout_estimation_harvest_time")
			->select("oo.c_businesspartner_id, bp.name c_businesspartner_name, bp.address c_businesspartner_address, bp.pic c_businesspartner_pic")
			->select("ipld.m_inventory_picklist_id")
			->select("oo.c_project_id, prj.name c_project_name")
			->from('m_inventory_picklistdetails ipld')
			->join('c_orderoutdetails ood', "ood.id = ipld.c_orderoutdetail_id")
			->join('c_orderouts oo', "oo.id = ood.c_orderout_id")
			->join('c_businesspartners bp', "bp.id = oo.c_businesspartner_id")
			->join('c_projects prj', "prj.id = oo.c_project_id", 'left')
			->group_by(
				array(
					'ood.c_orderout_id', 
					'oo.code', 'oo.orderout_date', 'oo.estimation_harvest_time',
					'oo.c_businesspartner_id', 'bp.name', 'bp.address', 'bp.pic',
					'ipld.m_inventory_picklist_id',
					'oo.c_project_id', 'prj.name'
				)
			)
			->where("ipld.m_inventory_picklist_id", $id);
		$this->lib_custom->project_query_filter('oo.c_project_id', $this->c_project_ids);
		$table = $this->db->get();
		$c_orderouts = $table->result();
		foreach ($c_orderouts as $c_orderout_idx=>$c_orderout)
		{
			$this->db
				->select("MAX(ipld.id) id", FALSE)
				->select("ipld.m_product_id, pro.code m_product_code, pro.name m_product_name, pro.uom m_product_uom, pro.type m_product_type, pro.casing m_product_casing")
				->select("ipld.m_grid_id, gri.code m_grid_code")
				->select("ipld.carton_no, ipld.product_size")
				->select("ood.price")
				->select("SUM(ipld.quantity_box) quantity_box", FALSE)
				->select("SUM(ipld.quantity) quantity", FALSE)
				->select("ipld.status_inventory_picking")
				->from('m_inventory_picklistdetails ipld')
				->join('c_orderoutdetails ood', "ood.id = ipld.c_orderoutdetail_id")
				->join('m_grids gri', "gri.id = ipld.m_grid_id")
				->join('m_products pro', "pro.id = ipld.m_product_id", 'left')
				->group_by(
					array(
						'ipld.m_product_id', 'pro.code', 'pro.name', 'pro.uom', 'pro.type', 'pro.casing',
						'ipld.m_grid_id', 'gri.code',
						'ipld.carton_no', 'ipld.product_size',
						'ipld.status_inventory_picking',
						'ood.price'
					)
				)
				->where("ipld.m_inventory_picklist_id", $c_orderout->m_inventory_picklist_id)
				->where("ipld.c_project_id", $c_orderout->c_project_id);
			$table = $this->db->get();
			$m_inventory_picklistdetails = $table->result();
			
			$c_orderouts[$c_orderout_idx]->m_inventory_picklistdetails = $m_inventory_picklistdetails;
		}
		
		$data = array(
			'record'		=> $record,
			'c_orderouts'	=> $c_orderouts
		);
		$html = $this->load->view('material/inventory_picklist_panen/detail_printout', $data, TRUE);
		
		// $this->output->set_output($html);
		$this->load->library('lib_dompdf');
		$this->lib_dompdf->load_as_pdf($html, 'invoice_printout.pdf', 'a4', 'portrait');
	}

	public function balancenote_printout()
	{
		if (!is_authorized('material/inventory_picklist_panen', 'index')) 
			access_denied();
		
		$id = $this->input->get_post('id');
		
		// -- Get balance id from picklist detail --
		$this->db
			->select("ood.c_orderout_id")
			->select("oo.code c_orderout_code, oo.orderout_date c_orderout_date, oo.estimation_harvest_time c_orderout_estimation_harvest_time")
			->select("oo.c_businesspartner_id, bp.name c_businesspartner_name, bp.address c_businesspartner_address, bp.pic c_businesspartner_pic")
			->select("ipld.m_inventory_picklist_id")
			->select("oo.c_project_id, prj.name c_project_name")
			->from('m_inventory_picklistdetails ipld')
			->join('c_orderoutdetails ood', "ood.id = ipld.c_orderoutdetail_id")
			->join('c_orderouts oo', "oo.id = ood.c_orderout_id")
			->join('c_businesspartners bp', "bp.id = oo.c_businesspartner_id")
			->join('c_projects prj', "prj.id = oo.c_project_id", 'left')
			->group_by(
				array(
					'ood.c_orderout_id', 
					'oo.code', 'oo.orderout_date', 'oo.estimation_harvest_time',
					'oo.c_businesspartner_id', 'bp.name', 'bp.address', 'bp.pic',
					'ipld.m_inventory_picklist_id',
					'oo.c_project_id', 'prj.name'
				)
			)
			->where("ipld.m_inventory_picklist_id", $id);
		$this->lib_custom->project_query_filter('oo.c_project_id', $this->c_project_ids);
		$table = $this->db->get();
		$c_orderouts = $table->result();
		foreach ($c_orderouts as $c_orderout_idx=>$c_orderout)
		{
		}
		$this->db
			->select("ib.id, ib.code, ib.balance_date, ib.product_size, ib.harvest_sequence, ib.pic, ib.vehicle_no, ib.notes")
			->select("ib.m_inventory_id")
			->select("gri.code m_grid_code")
			->from('m_inventory_balances ib')
			->join('m_inventories inv', "inv.id = ib.m_inventory_id")
			->join('m_grids gri', "gri.id = inv.m_grid_id")
			->where('ib.id', $id);
		$table = $this->db->get();
		if ($table->num_rows() > 0)
			$record = $table->first_row();
		else
			show_error("Balance not found", 400);
		
		$this->db
			->select("ibd.id, ibd.quantity_box, ibd.quantity")
			->select("ibd.barcode, ibd.pallet, ibd.lot_no, ibd.carton_no, ibd.condition, ibd.packed_date, ibd.expired_date")
			->select("inv.m_product_id, pro.code m_product_code, pro.name m_product_name, pro.uom m_product_uom, pro.casing m_product_casing")
			->select("ibd.notes, ibd.created")
			->from('m_inventory_balancedetails ibd')
			->join('m_inventories inv', "inv.id = ibd.m_inventory_id", 'left')
			->join('m_products pro', "pro.id = inv.m_product_id", 'left')
			->where("ibd.m_inventory_balance_id", $id);
		$this->lib_custom->project_query_filter('inv.c_project_id', $this->c_project_ids);
		$table = $this->db
			->order_by('ibd.carton_no', 'asc')
			->get();
		$m_inventory_balancedetails = $table->result();
		
		$data = array(
			'record'						=> $record,
			'm_inventory_balancedetails'	=> $m_inventory_balancedetails
		);
		
		$html = $this->load->view('material/inventory_balance/detail_printout', $data, TRUE);
		
		// $this->output->set_output($html);
		$this->load->library('lib_dompdf');
		$this->lib_dompdf->load_as_pdf($html, 'detail_printout.pdf', 'a4', 'portrait');
	}
}