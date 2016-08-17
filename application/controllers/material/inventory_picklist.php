<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Inventory_picklist extends MY_Controller 
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
		if (!is_authorized('material/inventory_picklist', 'index')) 
			access_denied();
		
		$data = array(
		);
		
		$content = array(
			'title'		=> "Pick List",
			'content' 	=> $this->load->view('material/inventory_picklist/index', $data, TRUE)
		);
		$this->_load_layout($content);
	}
	
	public function get_list_json()
	{
		if (!is_authorized('material/inventory_picklist', 'index')) 
			access_denied();
		
		$this->load->helper('date');
		
		$from_month = $this->input->get_post('from_month');
		$from_year = $this->input->get_post('from_year');
		
		$to_month = $this->input->get_post('to_month');
		$to_year = $this->input->get_post('to_year');
		
		$this->db
			->select("ipl.id, ipl.code, ipl.picklist_date")
			->select("ood.c_orderout_id, oo.code c_orderout_code, oo.orderout_date c_orderout_date, oo.request_arrive_date c_orderout_request_arrive_date")
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
					'ood.c_orderout_id', 'oo.code', 'oo.orderout_date', 'oo.request_arrive_date',
					'oo.c_businesspartner_id', 'bp.name',
					'oo.c_project_id', 'prj.name',
					'ipl.status_inventory_picking'
				)
			);
		$this->db->where("ipl.picklist_date >=", date('Y-m-d', mktime(0, 0, 0, $from_month, 1, $from_year)));
		$this->db->where("ipl.picklist_date <=", add_date(date('Y-m-d', mktime(0, 0, 0, $to_month, 1, $to_year)), -1, 1));
		
		$this->lib_custom->project_query_filter('ipld.c_project_id', $this->c_project_ids);
		
		parent::_get_list_json();
	}
	
	public function form()
	{
		if (!is_authorized('material/inventory_picklist', 'index')) 
			access_denied();
		
		$form_action = $this->input->get_post('form_action');
		$id = $this->input->get_post('id');
		$record = NULL;
		if ($id !== NULL)
		{
			$this->db
				->select("ipl.id, ipl.code, ipl.picklist_date, ipl.status_inventory_picking, ipl.notes")
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
		$this->load->view('material/inventory_picklist/form', $data);
	}
	
	public function form_detail()
	{
		if (!is_authorized('material/inventory_picklist', 'index')) 
			access_denied();
		
		$id = $this->input->get_post('id');
		
		$this->db
			->select("ipl.id, ipl.code, ipl.picklist_date, ipl.status_inventory_picking, ipl.notes")
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
		$this->load->view('material/inventory_picklist/form_detail', $data);
	}
	
	public function get_list_form_ref_json()
	{
		if (!is_authorized('material/inventory_picklist', 'index')) 
			access_denied();
		
		$this->db
			->select("ood.id, ood.quantity_box, ood.quantity, ood.notes")
			->select("ood.m_product_id, pro.code m_product_code, pro.name m_product_name, pro.uom m_product_uom")
			->select("oo.code c_orderout_code, oo.orderout_date c_orderout_date, oo.request_arrive_date c_orderout_request_arrive_date")
			->select("oo.c_businesspartner_id, bp.code c_businesspartner_code, bp.name c_businesspartner_name")
			->select("oo.c_project_id, prj.name c_project_name")
			->select("oo.origin c_orderout_origin, oo.marketing_unit c_orderout_marketing_unit, oo.external_no c_orderout_external_no, oo.no_surat_jalan c_orderout_no_surat_jalan")
			->from('c_orderoutdetails ood')
			->join('m_products pro', "pro.id = ood.m_product_id")
			->join('c_orderouts oo', "oo.id = ood.c_orderout_id")
			->join('c_businesspartners bp', "bp.id = oo.c_businesspartner_id")
			->join('c_projects prj', "prj.id = oo.c_project_id", 'left')
			->where('ood.status_inventory_picklist <>', 'COMPLETE');
		
		$this->lib_custom->project_query_filter('oo.c_project_id', $this->c_project_ids);
		
		parent::_get_list_json();
	}
	
	public function get_list_form_json()
	{
		if (!is_authorized('material/inventory_picklist', 'index')) 
			access_denied();
		
		$this->db
			->select("MAX(ipld.id) id", FALSE)
			->select("ipld.m_inventory_picklist_id, ipld.c_orderoutdetail_id")
			->select("ipld.m_product_id, pro.code m_product_code, pro.name m_product_name, pro.uom m_product_uom")
			->select("ipld.m_grid_id, gri.code m_grid_code")
			->select("ipld.pallet, ipld.barcode, ipld.condition")
			->select("ipld.carton_no, ipld.lot_no")
			->select("ipld.volume_length, ipld.volume_width, ipld.volume_height")
			->select("ipld.packed_date, ipld.expired_date")
			->select("ipld.received_date")
			->select_datediff_day('ipld.received_date', $this->db->getdate(), 'inventory_age')
			->select("ipld.c_project_id, prj.name c_project_name")
			->select("ipld.c_businesspartner_id, bp.name c_businesspartner_name")
			->select("SUM(ipld.quantity_box) quantity_box", FALSE)
			->select("SUM(ipld.quantity) quantity", FALSE)
			->from('m_inventory_picklistdetails ipld')
			->join('m_grids gri', "gri.id = ipld.m_grid_id")
			->join('m_products pro', "pro.id = ipld.m_product_id", 'left')
			->join('c_projects prj', "prj.id = ipld.c_project_id", 'left')
			->join('c_businesspartners bp', "bp.id = ipld.c_businesspartner_id", 'left')
			->group_by(
				array(
					'ipld.m_inventory_picklist_id', 'ipld.c_orderoutdetail_id', 
					'ipld.m_product_id', 'pro.code', 'pro.name', 'pro.uom',
					'ipld.m_grid_id', 'gri.code',
					'ipld.pallet', 'ipld.barcode', 'ipld.condition',
					'ipld.carton_no', 'ipld.lot_no',
					'ipld.volume_length', 'ipld.volume_width', 'ipld.volume_height',
					'ipld.packed_date', 'ipld.expired_date',
					'ipld.received_date',
					'ipld.c_project_id', 'prj.name',
					'ipld.c_businesspartner_id', 'bp.name'
				)
			);
		
		$id = $this->input->get_post('id');
		if ($id !== '')
			$this->db->where("ipld.m_inventory_picklist_id", $id);
		
		$this->lib_custom->project_query_filter('ipld.c_project_id', $this->c_project_ids);
		
		parent::_get_list_json();
	}
	
	public function get_form_inventory_list_json()
	{
		if (!is_authorized('material/inventory_picking', 'index')) 
			access_denied();
		
		$m_product_id = $this->input->get_post('m_product_id');
		$c_project_id = $this->input->get_post('c_project_id');
		
		$inventory_sql = 
			 "(SELECT	  inv.m_product_id, inv.m_grid_id, inv.c_project_id, inv.c_businesspartner_id "
			."			, inv.barcode, inv.pallet, inv.carton_no, inv.packed_date, inv.expired_date, inv.condition, inv.lot_no "
			."			, inv.volume_length, inv.volume_width, inv.volume_height "
			."			, inv.received_date "
			."			, inv.quantity_per_box "
			."			, ".$this->db->if_null("SUM(inv.quantity_box)", 0)." quantity_box_exist "
			."			, ".$this->db->if_null("SUM(inv.quantity_box_allocated)", 0)." quantity_box_allocated "
			."			, ".$this->db->if_null("SUM(inv.quantity_box_picked)", 0)." quantity_box_picked "
			."			, ".$this->db->if_null("SUM(inv.quantity_box_onhand)", 0)." quantity_box_onhand "
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
			."			  inv.m_product_id, inv.m_grid_id, inv.c_project_id, inv.c_businesspartner_id "
			."			, inv.barcode, inv.pallet, inv.carton_no, inv.packed_date, inv.expired_date, inv.condition, inv.lot_no "
			."			, inv.volume_length, inv.volume_width, inv.volume_height "
			."			, inv.received_date "
			."			, inv.quantity_per_box "
			.") inv ";
		
		$this->db
			->select("inv.m_product_id")
			->select("grd.id m_grid_id, grd.code m_grid_code")
			->select("wh.id m_warehouse_id, wh.code m_warehouse_code, wh.name m_warehouse_name")
			->select("inv.barcode, inv.pallet, inv.carton_no, inv.packed_date, inv.expired_date, inv.condition, inv.lot_no, inv.quantity_per_box")
			->select("inv.volume_length, inv.volume_width, inv.volume_height")
			->select("prj.id c_project_id, prj.name c_project_name")
			->select("bp.id c_businesspartner_id, bp.name c_businesspartner_name")
			->select("inv.received_date")
			->select_datediff_day('inv.received_date', $this->db->getdate(), 'inventory_age')
			->select("inv.quantity_box_exist, inv.quantity_box_allocated, inv.quantity_box_picked, inv.quantity_box_onhand")
			->select("inv.quantity_exist, inv.quantity_allocated, inv.quantity_picked, inv.quantity_onhand")
			->from($inventory_sql, FALSE)
			->join('m_grids grd', "grd.id = inv.m_grid_id")
			->join('m_warehouses wh', "wh.id = grd.m_warehouse_id")
			->join('c_projects prj', "prj.id = inv.c_project_id", 'left')
			->join('c_businesspartners bp', "bp.id = inv.c_businesspartner_id", 'left');
		
		parent::_get_list_json();
	}
	
	public function detail()
	{
		if (!is_authorized('material/inventory_picklist', 'index')) 
			access_denied();
		
		$id = $this->input->get_post('id');
		$this->db
			->select("ipl.id, ipl.code, ipl.picklist_date, ipl.status_inventory_picking, ipl.notes")
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
		$this->load->view('material/inventory_picklist/detail', $data);
	}
	
	public function get_list_detail_json()
	{
		if (!is_authorized('material/inventory_picklist', 'index')) 
			access_denied();
		
		$this->db
			->select("MAX(ipld.id) id", FALSE)
			->select("ood.c_orderout_id")
			->select("ipld.m_product_id, pro.code m_product_code, pro.name m_product_name, pro.uom m_product_uom")
			->select("ipld.m_grid_id, gri.code m_grid_code")
			->select("ipld.pallet, ipld.barcode, ipld.carton_no, ipld.lot_no, ipld.condition")
			->select("ipld.volume_length, ipld.volume_width, ipld.volume_height")
			->select("ipld.packed_date, ipld.expired_date")
			->select("ipld.received_date")
			->select_datediff_day('ipld.received_date', $this->db->getdate(), 'inventory_age')
			->select("ipld.c_project_id, prj.name c_project_name")
			->select("SUM(ipld.quantity_box) quantity_box", FALSE)
			->select("SUM(ipld.quantity) quantity", FALSE)
			->select_if_null('SUM(ipgd.quantity_box)', 0, 'quantity_box_used')
			->select_if_null('SUM(ipgd.quantity)', 0, 'quantity_used')
			->select("oo.code c_orderout_code, oo.orderout_date c_orderout_date, oo.request_arrive_date c_orderout_request_arrive_date")
			->select("ipld.c_businesspartner_id, bp.name c_businesspartner_name")
			->select("ipld.status_inventory_picking")
			->from('m_inventory_picklistdetails ipld')
			->join('c_orderoutdetails ood', "ood.id = ipld.c_orderoutdetail_id")
			->join('m_grids gri', "gri.id = ipld.m_grid_id")
			->join('c_orderouts oo', "oo.id = ood.c_orderout_id")
			->join('m_products pro', "pro.id = ipld.m_product_id", 'left')
			->join('c_projects prj', "prj.id = ipld.c_project_id", 'left')
			->join('c_businesspartners bp', "bp.id = ipld.c_businesspartner_id", 'left')
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
					'ipld.m_product_id', 'pro.code', 'pro.name', 'pro.uom',
					'ipld.m_grid_id', 'gri.code',
					'ipld.pallet', 'ipld.barcode', 'ipld.carton_no', 'ipld.lot_no', 'ipld.condition',
					'ipld.volume_length', 'ipld.volume_width', 'ipld.volume_height',
					'ipld.packed_date', 'ipld.expired_date',
					'ipld.received_date',
					'oo.code', 'oo.orderout_date', 'oo.request_arrive_date',
					'ipld.c_businesspartner_id', 'bp.name',
					'ipld.c_project_id', 'prj.name',
					'ipld.status_inventory_picking'
				)
			);
		
		$id = $this->input->get_post('id');
		if ($id !== '')
			$this->db->where("ipld.m_inventory_picklist_id", $id);
		
		$this->lib_custom->project_query_filter('ipld.c_project_id', $this->c_project_ids);
		
		parent::_get_list_json();
	}
	
	public function insert()
	{
		if (!is_authorized('material/inventory_picklist', 'insert')) 
			access_denied();
		
		$this->load->library('material/lib_inventory_out');
		
		$user_id = $this->session->userdata('user_id');
		
		$data = new stdClass();
		$data->code = $this->input->post('code');
		$data->picklist_date = $this->input->post('picklist_date');
		$data->notes = $this->input->post('notes');
		
		parent::_execute('lib_inventory_out', 'picklist_add', 
			array($data, $user_id),
			array(
				array('field' => 'code', 'label' => 'No', 'rules' => 'required'),
				array('field' => 'picklist_date', 'label' => 'Date', 'rules' => 'required')
			)
		);
	}
	
	public function insert_detail()
	{
		if (!is_authorized('material/inventory_picklist', 'insert')) 
			access_denied();
		
		$user_id = $this->session->userdata('user_id');
		
		$this->load->library('material/lib_inventory_out');
		
		$data = new stdClass();
		$data->m_inventory_picklist_id = $this->input->post('m_inventory_picklist_id');
		$data->c_orderoutdetail_id = $this->input->post('c_orderoutdetail_id');
		$data->barcode = $this->input->post('barcode');
		$data->pallet = $this->input->post('pallet');
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
		$data->condition = $this->input->post('condition');
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
		if (!is_authorized('material/inventory_picklist', 'insert')) 
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
				$data->c_businesspartner_id = $record['c_businesspartner_id'];
				$data->pallet = $record['pallet'];
				$data->barcode = $record['barcode'];
				$data->carton_no = $record['carton_no'];
				$data->lot_no = $record['lot_no'];
				$data->volume_length = $record['volume_length'];
				$data->volume_width = $record['volume_width'];
				$data->volume_height = $record['volume_height'];
				$data->condition = $record['condition'];
				$data->packed_date = $record['packed_date'];
				$data->expired_date = $record['expired_date'];
				$data->received_date = $record['received_date'];
				$data->quantity = $record['quantity'];
				$data->tolerance = 0;
				
				$this->lib_inventory_out->picklistdetail_add_by_properties($data, $user_id);
			}
		}
	}
	
	public function get_detail_counter()
	{
		if (!is_authorized('material/inventory_picklist', 'insert')) 
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
		if (!is_authorized('material/inventory_picklist', 'update')) 
			access_denied();
		
		$this->load->library('material/lib_inventory_out');
		
		$user_id = $this->session->userdata('user_id');
		
		$data = new stdClass();
		$data->code = $this->input->post('code');
		$data->picklist_date = $this->input->post('picklist_date');
		$data->notes = $this->input->post('notes');
		
		parent::_execute('lib_inventory_out', 'picklist_update', 
			array($id, $data, $user_id), 
			array(
				array('field' => 'code', 'label' => 'No', 'rules' => 'required'),
				array('field' => 'picklist_date', 'label' => 'Date', 'rules' => 'required')
			)
		);
	}
	
	public function delete($id)
	{
		if (!is_authorized('material/inventory_picklist', 'delete')) 
			access_denied();
		
		$this->load->library('material/lib_inventory_out');
		
		$user_id = $this->session->userdata('user_id');
		
		parent::_execute('lib_inventory_out', 'picklist_remove', array($id, $user_id));
	}
	
	public function delete_detail()
	{
		if (!is_authorized('material/inventory_picklist', 'delete')) 
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
		$barcode = $this->input->post('barcode');
		if ($barcode !== '')
			$data->barcode = $barcode;
		else
			$data->barcode = NULL;
		$pallet = $this->input->post('pallet');
		if ($pallet !== '')
			$data->pallet = $pallet;
		else
			$data->pallet = NULL;
		$condition = $this->input->post('condition');
		if ($condition !== '')
			$data->condition = $condition;
		else
			$data->condition = NULL;
		$carton_no = $this->input->post('carton_no');
		if ($carton_no !== '')
			$data->carton_no = $carton_no;
		else
			$data->carton_no = NULL;
		$lot_no = $this->input->post('lot_no');
		if ($lot_no !== '')
			$data->lot_no = $lot_no;
		else
			$data->lot_no = NULL;
		$volume_length = $this->input->post('volume_length');
		if ($volume_length !== '')
			$data->volume_length = $volume_length;
		else
			$data->volume_length = NULL;
		$volume_width = $this->input->post('volume_width');
		if ($volume_width !== '')
			$data->volume_width = $volume_width;
		else
			$data->volume_width = NULL;
		$volume_height = $this->input->post('volume_height');
		if ($volume_height !== '')
			$data->volume_height = $volume_height;
		else
			$data->volume_height = NULL;
		$packed_date = $this->input->post('packed_date');
		if ($packed_date !== '')
			$data->packed_date = $packed_date;
		else
			$data->packed_date = NULL;
		$expired_date = $this->input->post('expired_date');
		if ($expired_date !== '')
			$data->expired_date = $expired_date;
		else
			$data->expired_date = NULL;
		$received_date = $this->input->post('received_date');
		if ($received_date !== '')
			$data->received_date = $received_date;
		else
			$data->received_date = NULL;
		$c_project_id = $this->input->post('c_project_id');
		if ($c_project_id !== '')
			$data->c_project_id = $c_project_id;
		else
			$data->c_project_id = NULL;
		$c_businesspartner_id = $this->input->post('c_businesspartner_id');
		if ($c_businesspartner_id !== '')
			$data->c_businesspartner_id = $c_businesspartner_id;
		else
			$data->c_businesspartner_id = NULL;
		
		parent::_execute('lib_inventory_out', 'picklistdetail_remove_by_properties', 
			array($data, $user_id),
			array(
				array('field' => 'm_inventory_picklist_id', 'label' => 'Inventory Pick List', 'rules' => 'integer|required'),
				array('field' => 'c_orderoutdetail_id', 'label' => 'Order Out Detail', 'rules' => 'integer|required')
			)
		);
	}
	
	public function picking_document()
	{
		if (!is_authorized('material/inventory_picklist', 'index')) 
			access_denied();
		
		$id = $this->input->get_post('id');
		$c_orderout_id = $this->input->get_post('c_orderout_id');
		
		// -- Headers --
		$this->db
			->distinct()
			->select("ipld.m_inventory_picklist_id, ipl.code m_inventory_picklist_code, ipl.picklist_date m_inventory_picklist_date")
			->select("ood.c_orderout_id, oo.code c_orderout_code, oo.orderout_date c_orderout_date, oo.request_arrive_date c_orderout_request_arrive_date")
			->select("oo.c_businesspartner_id, bp.name c_businesspartner_name")
			->select("ipld.c_project_id, prj.name c_project_name")
			->from('m_inventory_picklistdetails ipld')
			->join('m_inventory_picklists ipl', "ipl.id = ipld.m_inventory_picklist_id")
			->join('c_orderoutdetails ood', "ood.id = ipld.c_orderoutdetail_id")
			->join('c_orderouts oo', "oo.id = ood.c_orderout_id")
			->join('c_businesspartners bp', "bp.id = oo.c_businesspartner_id")
			->join('c_projects prj', "prj.id = ipld.c_project_id", 'left')
			->where("ipld.m_inventory_picklist_id", $id);
		if ($c_orderout_id)
			$this->db
				->where("ood.c_orderout_id", $c_orderout_id);
		
		$this->lib_custom->project_query_filter('ipld.c_project_id', $this->c_project_ids);
		
		$table = $this->db
			->get();
		$documents = $table->result();
		
		// -- Summary --
		foreach ($documents as $document_idx=>$document)
		{
			$this->db
				->select("ipld.m_product_id, pro.code m_product_code, pro.name m_product_name")
				->select("SUM(ipld.quantity_box) quantity_box", FALSE)
				->select("SUM(ipld.quantity) quantity", FALSE)
				->from('m_inventory_picklistdetails ipld')
				->join('c_orderoutdetails ood', "ood.id = ipld.c_orderoutdetail_id")
				->join('m_products pro', "pro.id = ipld.m_product_id", 'left')
				->where('ipld.m_inventory_picklist_id', $document->m_inventory_picklist_id)
				->where('ood.c_orderout_id', $document->c_orderout_id);
			if (!empty($document->c_project_id))
				$this->db->where('ipld.c_project_id', $document->c_project_id);
			else
				$this->db->where("ipld.c_project_id IS NULL", NULL, FALSE);
			$table = $this->db
				->group_by(
					array(
						'ipld.m_product_id', 'pro.code', 'pro.name'
					)
				)
				->order_by('m_product_code', 'asc')
				->get();
			$documents[$document_idx]->summaries = $table->result();
		}
		
		// -- Detail --
		foreach ($documents as $document_idx=>$document)
		{
			$this->db
				->select("ipld.m_product_id, pro.code m_product_code, pro.pack m_product_pack")
				->select("ipld.m_grid_id, gri.code m_grid_code")
				->select("ipld.pallet, ipld.barcode")
				->select("SUM(ipld.quantity_box) quantity_box", FALSE)
				->select("SUM(ipld.quantity) quantity", FALSE)
				->from('m_inventory_picklistdetails ipld')
				->join('c_orderoutdetails ood', "ood.id = ipld.c_orderoutdetail_id")
				->join('m_grids gri', "gri.id = ipld.m_grid_id")
				->join('m_products pro', "pro.id = ipld.m_product_id", 'left')
				->where('ipld.m_inventory_picklist_id', $document->m_inventory_picklist_id)
				->where('ood.c_orderout_id', $document->c_orderout_id);
			if (!empty($document->c_project_id))
				$this->db->where('ipld.c_project_id', $document->c_project_id);
			else
				$this->db->where("ipld.c_project_id IS NULL", NULL, FALSE);
			$table = $this->db
				->group_by(
					array(
						'ipld.m_product_id', 'pro.code', 'pro.pack',
						'ipld.m_grid_id', 'gri.code',
						'ipld.pallet', 'ipld.barcode'
					)
				)
				->order_by('m_grid_code', 'asc')
				->order_by('pallet', 'asc')
				->order_by('barcode', 'asc')
				->order_by('m_product_code', 'asc')
				->get();
			$documents[$document_idx]->details = $table->result();
		}
		
		$data = array(
			'documents'	=> $documents
		);
		$html = $this->load->view('material/inventory_picklist/picklist_document', $data, TRUE);
		
		// echo $html;
		$this->load->library('lib_dompdf');
		$this->lib_dompdf->load_as_pdf($html, 'picklist.pdf', 'a4', 'portrait');
	}
	
	public function tally_sheet()
	{
		if (!is_authorized('material/inventory_picklist', 'index')) 
			access_denied();
		
		$id = $this->input->get_post('id');
		
		$this->db
			->select("ipl.id, ipl.code, ipl.picklist_date")
			->select("ipl.notes")
			->from('m_inventory_picklists ipl')
			->where('ipl.id', $id);
		$table = $this->db->get();
		if ($table->num_rows() == 0)
			show_error("Pick List not found", 400);
		$header_record = $table->first_row();
		
		$this->db
			->select("ipld.id")
			->select("ood.c_orderout_id")
			->select("oo.code c_orderout_code, oo.orderout_date c_orderout_date, oo.request_arrive_date c_orderout_request_arrive_date")
			->select("oo.external_no c_orderout_external_no")
			->select("oo.c_businesspartner_id, bp.name c_businesspartner_name")
			->select("ipld.c_project_id, prj.name c_project_name")
			->select("ipld.m_product_id, pro.code m_product_code, pro.name m_product_name, pro.uom m_product_uom, pro.pack m_product_pack")
			->select("ipld.pallet, ipld.barcode, ipld.carton_no")
			->select("ipld.m_grid_id, gri.code m_grid_code")
			->select("ipld.quantity_box, ipld.quantity")
			->select("ipld.quantity / ipld.quantity_box quantity_avg", FALSE)
			->from('m_inventory_picklistdetails ipld')
			->join('c_orderoutdetails ood', "ood.id = ipld.c_orderoutdetail_id")
			->join('c_orderouts oo', "oo.id = ood.c_orderout_id")
			->join('c_businesspartners bp', "bp.id = oo.c_businesspartner_id")
			->join('m_grids gri', "gri.id = ipld.m_grid_id")
			->join('m_products pro', "pro.id = ipld.m_product_id", 'left')
			->join('c_projects prj', "prj.id = ipld.c_project_id", 'left')
			->where("ipld.m_inventory_picklist_id", $id);
		$this->lib_custom->project_query_filter('ipld.c_project_id', $this->c_project_ids);
		$table = $this->db
			->order_by('c_orderout_id', 'asc')
			->order_by('c_businesspartner_id', 'asc')
			->order_by('c_project_id', 'asc')
			->order_by('m_grid_code', 'asc')
			->order_by('pallet', 'asc')
			->order_by('m_product_name', 'asc')
			->order_by('id', 'asc')
			->get();
		$records = $table->result();
		
		$grouped_records = array();
		foreach ($records as $record_idx=>$record)
		{
			$header_key = md5(
				 'OO_' .$record->c_orderout_id
				.'BP_' .$record->c_businesspartner_id
				.'PRJ_'.$record->c_project_id
			);
			$product_key = $record->m_product_id;
			$pallet_key = $record->pallet.'__'.$record->m_grid_id;
			$carton_key = $record->carton_no;
			
			if (!isset($grouped_records[$header_key]))
			{
				$grouped_header_record = new stdClass();
				$grouped_header_record->code = $header_record->code;
				$grouped_header_record->picklist_date = $header_record->picklist_date;
				$grouped_header_record->c_orderout_code = $record->c_orderout_code;
				$grouped_header_record->c_businesspartner_name = $record->c_businesspartner_name;
				$grouped_header_record->c_project_name = $record->c_project_name;
				$grouped_header_record->c_orderout_external_no = $record->c_orderout_external_no;
				$grouped_header_record->c_orderout_request_arrive_date = $record->c_orderout_request_arrive_date;
				$grouped_header_record->products = array();
				$grouped_records[$header_key] = $grouped_header_record;
			}
			
			if (!isset($grouped_records[$header_key]->products[$product_key]))
			{
				$grouped_product_record = new stdClass();
				$grouped_product_record->m_product_id = $record->m_product_id;
				$grouped_product_record->m_product_code = $record->m_product_code;
				$grouped_product_record->m_product_name = $record->m_product_name;
				$grouped_product_record->m_product_uom = $record->m_product_uom;
				$grouped_product_record->m_product_pack = $record->m_product_pack;
				$grouped_product_record->pallets = array();
				$grouped_records[$header_key]->products[$product_key] = $grouped_product_record;
			}
			
			if (!isset($grouped_records[$header_key]->products[$product_key]->pallets[$pallet_key]))
			{
				$grouped_pallet_record = new stdClass();
				$grouped_pallet_record->m_grid_code = $record->m_grid_code;
				$grouped_pallet_record->pallet = $record->pallet;
				$grouped_pallet_record->cartons = array();
				$grouped_records[$header_key]->products[$product_key]->pallets[$pallet_key] = $grouped_pallet_record;
			}
			
			for ($quantity_box = 1; $quantity_box <= $record->quantity_box; $quantity_box++)
			{
				$grouped_carton_record = new stdClass();
				$grouped_carton_record->carton_no = $record->carton_no;
				$grouped_carton_record->quantity = $record->quantity_avg;
				$grouped_carton_record->quantity_box = 1;
				$grouped_records[$header_key]->products[$product_key]->pallets[$pallet_key]->cartons[] = $grouped_carton_record;
			}
		}
		
		$data = array(
			'data'	=> $grouped_records
		);
		$html = $this->load->view('material/inventory_picklist/tally_sheet', $data, TRUE);
		
		// echo $html;
		$this->load->library('lib_dompdf');
		$this->lib_dompdf->load_as_pdf($html, 'tally_sheet_picklist.pdf', 'a4', 'portrait');
	}
}