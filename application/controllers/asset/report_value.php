<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Report_value extends MY_Controller 
{
	public function __construct()
	{
		parent::__construct(TRUE, 'backend');
		
		$this->load->library('asset/lib_asset');
	}
	
	public function index()
	{
		if (!is_authorized('asset/report_value', 'index')) 
			access_denied();
		
		$data = array(
		);
		
		$content = array(
			'title'		=> "Asset Value",
			'content' 	=> $this->load->view('asset/report_value/index', $data, TRUE)
		);
		$this->_load_layout($content);
	}
	
	public function get_list_json()
	{
		if (!is_authorized('asset/report_value', 'index')) 
			access_denied();
		
		$this->load->helper('date');
		
		$month = (int)$this->input->get_post('month');
		$year = (int)$this->input->get_post('year');
		
		$selected_date = add_date(date('Y-m-d', mktime(0, 0, 0, $month, 1, $year)), -1, 1);
		
		$this->db
			->select("ast.id, ast.code, ast.name, ast.type, ast.voucher_no")
			->select("pro.name m_product_name")
			->select("loc.name c_location_name")
			->select("rgn.name c_region_name")
			->select("dep.name c_department_name")
			->select("bp_sup.name c_businesspartner_supplier_name")
			->select("bp_user.name c_businesspartner_user_name")
			->select("ast.quantity")
			->select("ast.purchase_date, ast.purchase_price, ast.currency")
			->select("ast.purchase_price - ".$this->db->if_null("SUM(asta.depreciated_value)", 0)." book_value", FALSE)
			->select_if_null("SUM(asta.depreciated_value)", 0, 'depreciation_accumulated')
			->select("asta2.market_value")
			->from('a_assets ast')
			->join('m_products pro', "pro.id = ast.m_product_id")
			->join('c_locations loc', "loc.id = ast.c_location_id")
			->join('c_regions rgn', "rgn.id = ast.c_region_id")
			->join('c_departments dep', "dep.id = ast.c_department_id")
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
			->where("asta.depreciated_date <=", $selected_date)
			->group_by(
				array(
					'ast.id', 'ast.code', 'ast.name', 'ast.type', 'ast.voucher_no',
					'pro.name',
					'loc.name',
					'rgn.name',
					'dep.name',
					'bp_sup.name',
					'bp_user.name',
					'ast.quantity',
					'ast.purchase_date', 'ast.purchase_price', 'ast.currency',
					'asta2.market_value'
				)
			);
		
		parent::_get_list_json();
	}
}