<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Inventoryudang extends MY_Controller 
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
			'title'		=> "Inventory Udang",
			'content' 	=> $this->load->view('material/inventoryudang/index', $data, TRUE)
		);
		$this->_load_layout($content);
	}
	
	public function summary()
	{
		if (!is_authorized('material/inventory', 'index')) 
			access_denied();
		
		$data = array(
		);
		
		$this->load->view('material/inventoryudang/summary', $data);
	}
	
	protected function get_summary_list_query()
	{
		$is_show_empty = $this->input->get_post('is_show_empty');
		
		$inventory_sql = 
			 "(SELECT	  inv.m_product_id, inv.m_grid_id, inv.c_project_id "
			."			, inv.received_date, inv.price_buy, inv.product_size "
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
			."			  inv.m_product_id, inv.m_grid_id, inv.c_project_id "
			."			, inv.received_date, inv.price_buy, inv.product_size "
			.") inv ";
		
		$this->db
			->select("pro.id m_product_id, pro.code m_product_code, pro.name m_product_name")
			->select("grd.id m_grid_id, grd.code m_grid_code")
			->select("prj.name c_project_name")
			->select_datediff_day('inv.received_date', $this->db->getdate(), 'inventory_age')
			->select("inv.price_buy, inv.product_size")
			->select("inv.quantity_exist, inv.quantity_allocated, inv.quantity_picked, inv.quantity_onhand")
			->from($inventory_sql, FALSE)
			->join('m_products pro', "pro.id = inv.m_product_id")
			->join('m_grids grd', "grd.id = inv.m_grid_id")
			->join('c_projects prj', "prj.id = inv.c_project_id", 'left')
			->where_in('pro.type', array('BENUR/BIBIT', 'UDANG'));
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
			'm_grid_code'			=> 'Location',
			'price_buy'				=> 'Price Buy',
			'quantity_exist'		=> 'Exist',
			'quantity_allocated'	=> 'Allocated',
			'quantity_picked'		=> 'Picked',
			'quantity_onhand'		=> 'Onhand',
			'product_size'			=> 'Size',
			'inventory_age'			=> 'Age',
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
		
		$this->load->view('material/inventoryudang/detail', $data);
	}
	
	protected function get_detail_list_query()
	{
		$is_show_empty = $this->input->get_post('is_show_empty');
		
		$inventory_sql = 
			 "(SELECT	  inv.m_product_id, inv.m_grid_id, inv.c_project_id "
			."			, inv.carton_no "
			."			, inv.received_date, inv.price_buy, inv.product_size "
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
			."			  inv.m_product_id, inv.m_grid_id, inv.c_project_id "
			."			, inv.carton_no "
			."			, inv.received_date, inv.price_buy, inv.product_size "
			.") inv ";
		
		$this->db
			->select("pro.id m_product_id, pro.code m_product_code, pro.name m_product_name, pro.uom m_product_uom, pro.pack m_product_pack")
			->select("grd.id m_grid_id, grd.code m_grid_code")
			->select("wh.id m_warehouse_id, wh.code m_warehouse_code, wh.name m_warehouse_name")
			->select("prog.id m_productgroup_id, prog.code m_productgroup_code, prog.name m_productgroup_name")
			->select("inv.carton_no")
			->select("prj.name c_project_name")
			->select_datediff_day('inv.received_date', $this->db->getdate(), 'inventory_age')
			->select("inv.price_buy, inv.product_size")
			->select("inv.quantity_exist, inv.quantity_allocated, inv.quantity_picked, inv.quantity_onhand")
			->from($inventory_sql, FALSE)
			->join('m_products pro', "pro.id = inv.m_product_id")
			->join('m_grids grd', "grd.id = inv.m_grid_id")
			->join('m_warehouses wh', "wh.id = grd.m_warehouse_id")
			->join('m_productgroups prog', "prog.id = grd.m_productgroup_id", 'left')
			->join('c_projects prj', "prj.id = inv.c_project_id", 'left')
			->where_in('pro.type', array('BENUR/BIBIT', 'UDANG'));
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
			'm_grid_code'			=> 'Location',
			'm_warehouse_name'		=> 'Location Group',
			'm_productgroup_name'	=> 'Product Group',
			'price_buy'				=> 'Price Buy',
			'quantity_exist'		=> 'Exist',
			'quantity_allocated'	=> 'Allocated',
			'quantity_picked'		=> 'Picked',
			'quantity_onhand'		=> 'Onhand',
			'm_product_uom'			=> 'UOM',
			'carton_no'				=> 'Carton No',
			'product_size'			=> 'Size',
			'inventory_age'			=> 'Age',
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
}