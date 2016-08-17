<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Inventory extends MY_Controller 
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
		if (!is_authorized('material/inventory', 'index')) 
			access_denied();
		
		$data = array(
		);
		
		$content = array(
			'title'		=> "Inventory",
			'content' 	=> $this->load->view('material/inventory/index', $data, TRUE)
		);
		$this->_load_layout($content);
	}
	
	public function summary()
	{
		if (!is_authorized('material/inventory', 'index')) 
			access_denied();
		
		$data = array(
		);
		
		$this->load->view('material/inventory/summary', $data);
	}
	
	protected function get_summary_list_query()
	{
		$is_show_empty = $this->input->get_post('is_show_empty');
		
		$inventory_sql = 
			 "(SELECT	  inv.m_product_id, inv.m_grid_id, inv.c_project_id, inv.c_businesspartner_id "
			."			, inv.pallet "
			."			, inv.received_date "
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
		if (!(int)$is_show_empty)
		{
			$criterias[] = "inv.quantity_box_onhand > 0";
			$criterias[] = "inv.quantity_onhand > 0";
		}
		$criterias[] = $this->lib_custom->project_sql_filter('inv.c_project_id', $this->c_project_ids);
		if (count($criterias))
			$inventory_sql .= " WHERE ". implode(" AND ", $criterias);
		$inventory_sql .= 
			 " GROUP	BY "
			."			  inv.m_product_id, inv.m_grid_id, inv.c_project_id, inv.c_businesspartner_id "
			."			, inv.pallet "
			."			, inv.received_date "
			.") inv ";
		
		$this->db
			->select("pro.id m_product_id, pro.code m_product_code, pro.name m_product_name")
			->select("grd.id m_grid_id, grd.code m_grid_code")
			->select("inv.pallet")
			->select("bp.name c_businesspartner_name")
			->select("prj.name c_project_name")
			->select_datediff_day('inv.received_date', $this->db->getdate(), 'inventory_age')
			->select("inv.quantity_box_exist, inv.quantity_box_allocated, inv.quantity_box_picked, inv.quantity_box_onhand")
			->select("inv.quantity_exist, inv.quantity_allocated, inv.quantity_picked, inv.quantity_onhand")
			->from($inventory_sql, FALSE)
			->join('m_products pro', "pro.id = inv.m_product_id")
			->join('m_grids grd', "grd.id = inv.m_grid_id")
			->join('c_projects prj', "prj.id = inv.c_project_id", 'left')
			->join('c_businesspartners bp', "bp.id = inv.c_businesspartner_id", 'left');
	}
	
	public function get_summary_list_json()
	{
		if (!is_authorized('material/inventory', 'index')) 
			access_denied();
		
		$this->get_summary_list_query();
		
		parent::_get_list_json();
	}
	
	public function get_summary_list_excel()
	{
		if (!is_authorized('material/inventory', 'index')) 
			access_denied();
		
		$this->get_summary_list_query();
		
		$this->load->library('jqgrid');
		
		$header_captions = array(
			'm_product_code'		=> 'Product Code', 
			'm_product_name'		=> 'Product Name',
			'm_grid_code'			=> 'Grid',
			'quantity_box_exist'	=> 'Box Exist',
			'quantity_box_allocated'=> 'Box Allocated',
			'quantity_box_picked'	=> 'Box Picked',
			'quantity_box_onhand'	=> 'Box Onhand',
			'quantity_exist'		=> 'Exist',
			'quantity_allocated'	=> 'Allocated',
			'quantity_picked'		=> 'Picked',
			'quantity_onhand'		=> 'Onhand',
			'pallet'				=> 'Pallet',
			'inventory_age'			=> 'Age',
			'c_businesspartner_name'=> 'Business Partner',
			'c_project_name'		=> 'Project'
		);
		
		$data = $this->jqgrid->result();
		
		$result = $data->data;
		
		$this->load->library('excel');
		$this->excel->write_stream_acos(
			array(
				'InventorySummary' => $result
			), 
			'InventorySummary.xls',
			array(
				'InventorySummary' => $header_captions
			)
		);
	}
	
	public function detail()
	{
		if (!is_authorized('material/inventory', 'index')) 
			access_denied();
		
		$data = array(
		);
		
		$this->load->view('material/inventory/detail', $data);
	}
	
	protected function get_detail_list_query()
	{
		$is_show_empty = $this->input->get_post('is_show_empty');
		
		$inventory_sql = 
			 "(SELECT	  inv.m_product_id, inv.m_grid_id, inv.c_project_id, inv.c_businesspartner_id "
			."			, inv.barcode, inv.pallet, inv.carton_no, inv.packed_date, inv.expired_date, inv.condition, inv.lot_no "
			."			, inv.volume_length, inv.volume_width, inv.volume_height "
			."			, inv.received_date "
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
		if (!(int)$is_show_empty)
		{
			$criterias[] = "inv.quantity_box_onhand > 0";
			$criterias[] = "inv.quantity_onhand > 0";
		}
		$criterias[] = $this->lib_custom->project_sql_filter('inv.c_project_id', $this->c_project_ids);
		if (count($criterias))
			$inventory_sql .= " WHERE ". implode(" AND ", $criterias);
		$inventory_sql .= 
			 " GROUP	BY "
			."			  inv.m_product_id, inv.m_grid_id, inv.c_project_id, inv.c_businesspartner_id "
			."			, inv.barcode, inv.pallet, inv.carton_no, inv.packed_date, inv.expired_date, inv.condition, inv.lot_no "
			."			, inv.volume_length, inv.volume_width, inv.volume_height "
			."			, inv.received_date "
			.") inv ";
		
		$this->db
			->select("pro.id m_product_id, pro.code m_product_code, pro.name m_product_name, pro.uom m_product_uom, pro.pack m_product_pack")
			->select("grd.id m_grid_id, grd.code m_grid_code")
			->select("wh.id m_warehouse_id, wh.code m_warehouse_code, wh.name m_warehouse_name")
			->select("prog.id m_productgroup_id, prog.code m_productgroup_code, prog.name m_productgroup_name")
			->select("inv.barcode, inv.pallet, inv.carton_no, inv.packed_date, inv.expired_date, inv.condition, inv.lot_no")
			->select("inv.volume_length, inv.volume_width, inv.volume_height")
			->select("bp.name c_businesspartner_name")
			->select("prj.name c_project_name")
			->select_datediff_day('inv.received_date', $this->db->getdate(), 'inventory_age')
			->select("inv.quantity_box_exist, inv.quantity_box_allocated, inv.quantity_box_picked, inv.quantity_box_onhand")
			->select("inv.quantity_exist, inv.quantity_allocated, inv.quantity_picked, inv.quantity_onhand")
			->from($inventory_sql, FALSE)
			->join('m_products pro', "pro.id = inv.m_product_id")
			->join('m_grids grd', "grd.id = inv.m_grid_id")
			->join('m_warehouses wh', "wh.id = grd.m_warehouse_id")
			->join('m_productgroups prog', "prog.id = grd.m_productgroup_id", 'left')
			->join('c_projects prj', "prj.id = inv.c_project_id", 'left')
			->join('c_businesspartners bp', "bp.id = inv.c_businesspartner_id", 'left');
	}
	
	public function get_detail_list_json()
	{
		if (!is_authorized('material/inventory', 'index')) 
			access_denied();
		
		$this->get_detail_list_query();
		
		parent::_get_list_json();
	}
	
	public function get_detail_list_excel()
	{
		if (!is_authorized('material/inventory', 'index')) 
			access_denied();
		
		$this->get_detail_list_query();
		
		$this->load->library('jqgrid');
		
		$header_captions = array(
			'm_product_code'		=> 'Product Code', 
			'm_product_name'		=> 'Product Name',
			'm_grid_code'			=> 'Grid',
			'm_warehouse_name'		=> 'Warehouse',
			'm_productgroup_name'	=> 'Product Group',
			'quantity_box_exist'	=> 'Box Exist',
			'quantity_box_allocated'=> 'Box Allocated',
			'quantity_box_picked'	=> 'Box Picked',
			'quantity_box_onhand'	=> 'Box Onhand',
			'quantity_exist'		=> 'Exist',
			'quantity_allocated'	=> 'Allocated',
			'quantity_picked'		=> 'Picked',
			'quantity_onhand'		=> 'Onhand',
			'm_product_uom'			=> 'UOM',
			'm_product_pack'		=> 'Pack',
			'barcode'				=> 'Barcode',
			'pallet'				=> 'Pallet',
			'carton_no'				=> 'Carton No',
			'packed_date'			=> 'Packed Date',
			'expired_date'			=> 'Expired Date',
			'lot_no'				=> 'Lot No',
			'volume_length'			=> 'Length',
			'volume_width'			=> 'Width',
			'volume_height'			=> 'Height',
			'condition'				=> 'Condition',
			'inventory_age'			=> 'Age',
			'c_businesspartner_name'=> 'Business Partner',
			'c_project_name'		=> 'Project'
		);
		
		$data = $this->jqgrid->result();
		
		$result = $data->data;
		
		$this->load->library('excel');
		$this->excel->write_stream_acos(
			array(
				'InventoryDetail' => $result
			), 
			'InventoryDetail.xls',
			array(
				'InventoryDetail' => $header_captions
			)
		);
	}
	
	protected function get_product_summary_list_query()
	{
		$inventory_sql = 
			 "(SELECT	  inv.m_product_id, inv.c_project_id "
			."			, ".$this->db->if_null("SUM(inv.quantity_box)", 0)." quantity_box_exist "
			."			, ".$this->db->if_null("SUM(inv.quantity_box_allocated)", 0)." quantity_box_allocated "
			."			, ".$this->db->if_null("SUM(inv.quantity_box_picked)", 0)." quantity_box_picked "
			."			, ".$this->db->if_null("SUM(inv.quantity_box_onhand)", 0)." quantity_box_onhand "
			."			, ".$this->db->if_null("SUM(inv.quantity)", 0)." quantity_exist "
			."			, ".$this->db->if_null("SUM(inv.quantity_allocated)", 0)." quantity_allocated "
			."			, ".$this->db->if_null("SUM(inv.quantity_picked)", 0)." quantity_picked "
			."			, ".$this->db->if_null("SUM(inv.quantity_onhand)", 0)." quantity_onhand "
			." FROM		m_inventories inv ";
		$criterias = array(
			"inv.quantity_box_onhand > 0",
			"inv.quantity_onhand > 0",
			$this->lib_custom->project_sql_filter('inv.c_project_id', $this->c_project_ids)
		);
		if (count($criterias))
			$inventory_sql .= " WHERE ". implode(" AND ", $criterias);
		$inventory_sql .= 
			 " GROUP	BY "
			."			  inv.m_product_id, inv.c_project_id "
			.") inv ";
		
		$this->db
			->select("pro.id m_product_id, pro.code m_product_code, pro.name m_product_name, pro.type m_product_type, pro.brand m_product_brand, pro.netto m_product_netto")
			->select("pro.m_productgroup_id, prog.name m_productgroup_name")
			->select("inv.c_project_id, prj.name c_project_name")
			->select("inv.quantity_box_exist, inv.quantity_box_allocated, inv.quantity_box_picked, inv.quantity_box_onhand")
			->select("inv.quantity_exist, inv.quantity_allocated, inv.quantity_picked, inv.quantity_onhand")
			->from($inventory_sql, FALSE)
			->join('m_products pro', "pro.id = inv.m_product_id")
			->join('m_productgroups prog', "prog.id = pro.m_productgroup_id", 'left')
			->join('c_projects prj', "prj.id = inv.c_project_id", 'left');
	}
	
	public function get_product_summary_list()
	{
		if (!is_authorized('material/inventory', 'index')) 
			access_denied();
		
		$this->get_product_summary_list_query();
		$this->db
			->order_by('c_project_id', 'asc')
			->order_by('m_product_type', 'asc')
			->order_by('m_productgroup_name', 'asc')
			->order_by('m_product_code', 'asc');
		$table = $this->db->get();
		
		$records = $table->result();
		
		// -- Grouping --
		$records_grouped = array();
		foreach ($records as $record_idx=>$record)
		{
			if (!isset($records_grouped[$record->c_project_id]))
			{
				$c_project_record = new stdClass();
				$c_project_record->id = $record->c_project_id;
				$c_project_record->name = $record->c_project_name;
				$c_project_record->records = array();
				$records_grouped[$record->c_project_id] = $c_project_record;
			}
			
			if (!isset($records_grouped[$record->c_project_id]->records[$record->m_product_type]))
			{
				$m_product_type_record = new stdClass();
				$m_product_type_record->name = $record->m_product_type;
				$m_product_type_record->records = array();
				$records_grouped[$record->c_project_id]->records[$record->m_product_type] = $m_product_type_record;
			}
			
			if (!isset($records_grouped[$record->c_project_id]->records[$record->m_product_type]->records[$record->m_productgroup_id]))
			{
				$m_productgroup_record = new stdClass();
				$m_productgroup_record->id = $record->m_productgroup_id;
				$m_productgroup_record->name = $record->m_productgroup_name;
				$m_productgroup_record->records = array();
				$records_grouped[$record->c_project_id]->records[$record->m_product_type]->records[$record->m_productgroup_id] = $m_productgroup_record;
			}
			
			$records_grouped[$record->c_project_id]->records[$record->m_product_type]->records[$record->m_productgroup_id]->records[] = $record;
		}

		$data = array(
			'records'	=> $records_grouped
		);
		$this->load->view('material/inventory/report_by_product', $data);
	}
	
	public function grid_usage_reload()
	{
		if (!is_authorized('material/inventory', 'update')) 
			access_denied();
		
		parent::_execute('this', 'grid_usage_reload_do');
	}
	
	protected function grid_usage_reload_do()
	{
		$user_id = $this->session->userdata('user_id');
		
		$this->load->library('custom/lib_custom_inventory');
		
		@set_time_limit(0);
		
		$this->lib_custom_inventory->grid_usage_reload($user_id);
		
		return TRUE;
	}
}