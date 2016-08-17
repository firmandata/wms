<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'/libraries/REST_Controller.php';

class Inventory extends REST_Controller
{
	private $c_project_ids;
	
	function __construct()
    {
        parent::__construct();
		
		$this->load->library('custom/lib_custom');
		
		$user_id = $this->session->userdata('user_id');
		$this->c_project_ids = $this->lib_custom->project_get_ids($user_id);
    }
    
    function check_list_get()
    {
		if (!is_authorized('material/inventory', 'index')) 
			access_denied();
		
		$inventory_sql = 
			 "(SELECT	  inv.m_product_id, inv.m_grid_id, inv.c_project_id "
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
		$criterias[] = "inv.quantity_box_onhand > 0";
		$criterias[] = "inv.quantity_onhand > 0";
		$criterias[] = $this->lib_custom->project_sql_filter('inv.c_project_id', $this->c_project_ids);
		if (count($criterias))
			$inventory_sql .= " WHERE ". implode(" AND ", $criterias);
		$inventory_sql .= 
			 " GROUP	BY "
			."			  inv.m_product_id, inv.m_grid_id, inv.c_project_id "
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
			->select("prj.id c_project_id, prj.name c_project_name")
			->select_datediff_day('inv.received_date', $this->db->getdate(), 'inventory_age')
			->select("inv.quantity_box_exist, inv.quantity_box_allocated, inv.quantity_box_picked, inv.quantity_box_onhand")
			->select("inv.quantity_exist, inv.quantity_allocated, inv.quantity_picked, inv.quantity_onhand")
			->from($inventory_sql, FALSE)
			->join('m_products pro', "pro.id = inv.m_product_id")
			->join('m_grids grd', "grd.id = inv.m_grid_id")
			->join('m_warehouses wh', "wh.id = grd.m_warehouse_id")
			->join('m_productgroups prog', "prog.id = grd.m_productgroup_id", 'left')
			->join('c_projects prj', "prj.id = inv.c_project_id", 'left');
		
		parent::_get_list_json();
    }
	
	function check_summary_get()
    {
		if (!is_authorized('material/inventory', 'index')) 
			access_denied();
		
		$this->db
			->select(
				 "  ".$this->db->if_null("SUM(inv.quantity_box)", 0)." quantity_box_exist "
				.", ".$this->db->if_null("SUM(inv.quantity_box_allocated)", 0)." quantity_box_allocated "
				.", ".$this->db->if_null("SUM(inv.quantity_box_picked)", 0)." quantity_box_picked "
				.", ".$this->db->if_null("SUM(inv.quantity_box_onhand)", 0)." quantity_box_onhand "
				.", ".$this->db->if_null("SUM(inv.quantity)", 0)." quantity_exist "
				.", ".$this->db->if_null("SUM(inv.quantity_allocated)", 0)." quantity_allocated "
				.", ".$this->db->if_null("SUM(inv.quantity_picked)", 0)." quantity_picked "
				.", ".$this->db->if_null("SUM(inv.quantity_onhand)", 0)." quantity_onhand "
				, FALSE)
			->from('m_inventories inv')
			->join('m_products pro', "pro.id = inv.m_product_id")
			->join('m_grids grd', "grd.id = inv.m_grid_id")
			->join('m_warehouses wh', "wh.id = grd.m_warehouse_id")
			->join('m_productgroups prog', "prog.id = grd.m_productgroup_id", 'left')
			->join('c_projects prj', "prj.id = inv.c_project_id", 'left')
			->where("inv.quantity_box_onhand >", 0)
			->where("inv.quantity_onhand >", 0);
		
		$this->lib_custom->project_query_filter('inv.c_project_id', $this->c_project_ids);
		
		parent::_get_list_json();
    }
}