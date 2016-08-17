<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'/libraries/REST_Controller.php';

class Asset extends REST_Controller
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
		if (!is_authorized('asset/asset', 'index')) 
			access_denied();
		
		$this->load->helper('date');
		
		$month = (int)date('n');
		$year = (int)date('Y');
		
		$selected_date = add_date(date('Y-m-d', mktime(0, 0, 0, $month, 1, $year)), -1, 1);
		
		$this->db
			->select("ast.id, ast.code, ast.name")
			->select("ast.m_product_id, pro.code m_product_code, pro.name m_product_name")
			->select("pro.m_productgroup_id, prog.code m_productgroup_code, prog.name m_productgroup_name")
			->select("ast.c_location_id, loc.code c_location_code, loc.name c_location_name")
			->select("loc.c_region_id, rgn.code c_region_code, rgn.name c_region_name")
			->select("ast.c_businesspartner_supplier_id, bp_sup.code c_businesspartner_supplier_code, bp_sup.name c_businesspartner_supplier_name")
			->select("ast.c_businesspartner_user_id, bp_user.code c_businesspartner_user_code, bp_user.name c_businesspartner_user_name")
			->select("ast.quantity")
			->select("ast.purchase_date, ast.purchase_price, ast.currency")
			->select("ast.purchase_price - ".$this->db->if_null("SUM(asta.depreciated_value)", 0)." book_value", FALSE)
			->select_if_null("SUM(asta.depreciated_value)", 0, 'depreciation_accumulated')
			->select("asta2.market_value")
			->from('a_assets ast')
			->join('m_products pro', "pro.id = ast.m_product_id")
			->join('c_locations loc', "loc.id = ast.c_location_id")
			->join('c_regions rgn', "rgn.id = loc.c_region_id")
			->join('a_assetamounts asta',  "asta.a_asset_id = ast.id")
			->join('a_assetamounts asta2'
				,  "asta2.a_asset_id = ast.id"
				  ." AND ("
				  ."	(ast.depreciation_period_type = 'MONTHLY' AND ".$this->db->getyear("asta2.depreciated_date")." = ".$year." AND ".$this->db->getmonth("asta2.depreciated_date")." = ".$month.") "
				  ." OR	(ast.depreciation_period_type = 'ANNUAL' AND ".$this->db->getyear("asta2.depreciated_date")." = ".$year.") "
				  ." OR	(ast.depreciation_period_type = 'DAILY' AND ".$this->db->getday("asta2.depreciated_date")." = ".$this->db->getday("ast.purchase_date")." AND ".$this->db->getyear("asta2.depreciated_date")." = ".$year." AND ".$this->db->getmonth("asta2.depreciated_date")." = ".$month.") "
				  .")"
				, 'left'
				, FALSE
			)
			->join('m_productgroups prog', "prog.id = pro.m_productgroup_id", 'left')
			->join('c_businesspartners bp_sup', "bp_sup.id = ast.c_businesspartner_supplier_id", 'left')
			->join('c_businesspartners bp_user', "bp_user.id = ast.c_businesspartner_user_id", 'left')
			->where("asta.depreciated_date <=", $selected_date)
			->group_by(
				array(
					'ast.id', 'ast.code', 'ast.name',
					'ast.m_product_id', 'pro.code', 'pro.name',
					'pro.m_productgroup_id', 'prog.code', 'prog.name',
					'ast.c_location_id', 'loc.code', 'loc.name',
					'loc.c_region_id', 'rgn.code', 'rgn.name',
					'ast.c_businesspartner_supplier_id', 'bp_sup.code', 'bp_sup.name',
					'ast.c_businesspartner_user_id', 'bp_user.code', 'bp_user.name',
					'ast.quantity',
					'ast.purchase_date', 'ast.purchase_price', 'ast.currency',
					'asta2.market_value'
				)
			);
		
		parent::_get_list_json();
    }
	
	function check_summary_get()
    {
		if (!is_authorized('asset/asset', 'index')) 
			access_denied();
		
		$this->load->helper('date');
		
		$month = (int)date('n');
		$year = (int)date('Y');
		
		$selected_date = add_date(date('Y-m-d', mktime(0, 0, 0, $month, 1, $year)), -1, 1);
		
		$this->db
			->select(
				 "  ".$this->db->if_null("COUNT(DISTINCT ast.id)", 0)." asset_count "
				.", ".$this->db->if_null("COUNT(DISTINCT ast.m_product_id)", 0)." product_count "
				.", ".$this->db->if_null("COUNT(DISTINCT ast.c_businesspartner_user_id)", 0)." c_businesspartner_user_count "
				.", ".$this->db->if_null("COUNT(DISTINCT ast.c_businesspartner_supplier_id)", 0)." c_businesspartner_supplier_count "
				.", ".$this->db->if_null("SUM(ast.purchase_price)", 0)." purchase_price "
				.", ".$this->db->if_null("SUM(ast.purchase_price)", 0)." - ".$this->db->if_null("SUM(asta.depreciated_value)", 0)." book_value "
				.", ".$this->db->if_null("SUM(asta.depreciated_value)", 0)." depreciation_accumulated "
				.", ".$this->db->if_null("SUM(asta.market_value)", 0)." market_value "
				, FALSE)
			->from('a_assets ast')
			->join('m_products pro', "pro.id = ast.m_product_id")
			->join('c_locations loc', "loc.id = ast.c_location_id")
			->join('c_regions rgn', "rgn.id = loc.c_region_id")
			->join('a_assetamounts asta',  "asta.a_asset_id = ast.id")
			->join('a_assetamounts asta2'
				,  "asta2.a_asset_id = ast.id"
				  ." AND ("
				  ."	(ast.depreciation_period_type = 'MONTHLY' AND ".$this->db->getyear("asta2.depreciated_date")." = ".$year." AND ".$this->db->getmonth("asta2.depreciated_date")." = ".$month.") "
				  ." OR	(ast.depreciation_period_type = 'ANNUAL' AND ".$this->db->getyear("asta2.depreciated_date")." = ".$year.") "
				  ." OR	(ast.depreciation_period_type = 'DAILY' AND ".$this->db->getday("asta2.depreciated_date")." = ".$this->db->getday("ast.purchase_date")." AND ".$this->db->getyear("asta2.depreciated_date")." = ".$year." AND ".$this->db->getmonth("asta2.depreciated_date")." = ".$month.") "
				  .")"
				, 'left'
				, FALSE
			)
			->join('c_businesspartners bp_sup', "bp_sup.id = ast.c_businesspartner_supplier_id", 'left')
			->join('c_businesspartners bp_user', "bp_user.id = ast.c_businesspartner_user_id", 'left')
			->where("asta.depreciated_date <=", $selected_date);
		
		parent::_get_list_json();
    }
}