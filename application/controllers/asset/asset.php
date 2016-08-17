<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Asset extends MY_Controller 
{
	public function __construct()
	{
		parent::__construct(TRUE, 'backend');
		
		$this->load->library('asset/lib_asset');
	}
	
	public function index()
	{
		if (!is_authorized('asset/asset', 'index')) 
			access_denied();
		
		$data = array(
		);
		
		$content = array(
			'title'		=> "Asset",
			'content' 	=> $this->load->view('asset/asset/index', $data, TRUE)
		);
		$this->_load_layout($content);
	}
	
	public function form()
	{
		if (!is_authorized('asset/asset', 'index')) 
			access_denied();
		
		$form_action = $this->input->get_post('form_action');
		$id = $this->input->get_post('id');
		$a_assetamount_view = NULL;
		
		$record = NULL;
		if ($id !== NULL)
		{
			$table = $this->db
				->select("ast.id, ast.code, ast.name, ast.type, ast.voucher_no")
				->select("ast.m_product_id")
				->select_concat(array("pro.name", "' ('", "pro.code", "')'"), 'm_product_text')
				->select("ast.c_region_id")
				->select_concat(array("rgn.name", "' ('", "rgn.code", "')'"), 'c_region_text')
				->select("ast.c_department_id")
				->select_concat(array("dep.name", "' ('", "dep.code", "')'"), 'c_department_text')
				->select("ast.c_location_id")
				->select_concat(array("loc.name", "' ('", "loc.code", "')'"), 'c_location_text')
				->select("ast.c_businesspartner_supplier_id")
				->select_concat(array("bp_sup.name", "' ('", "bp_sup.code", "')'"), 'c_businesspartner_supplier_text')
				->select("ast.c_businesspartner_user_id")
				->select_concat(array("bp_user.name", "' ('", "bp_user.code", "')'"), 'c_businesspartner_user_text')
				->select("ast.quantity")
				->select("ast.purchase_date, ast.purchase_price, ast.currency")
				->select("ast.notes")
				->select("ast.depreciation_period_type, ast.depreciation_period_time")
				->from('a_assets ast')
				->join('m_products pro', "pro.id = ast.m_product_id")
				->join('c_regions rgn', "rgn.id = ast.c_region_id")
				->join('c_departments dep', "dep.id = ast.c_department_id")
				->join('c_locations loc', "loc.id = ast.c_location_id")
				->join('c_businesspartners bp_sup', "bp_sup.id = ast.c_businesspartner_supplier_id", 'left')
				->join('c_businesspartners bp_user', "bp_user.id = ast.c_businesspartner_user_id", 'left')
				->where('ast.id', $id)
				->get();
			if ($table->num_rows() > 0)
				$record = $table->first_row();
			else
				show_error("Asset not found", 400);
			
			// -- Asset Amounts --
			$table = $this->db
				->select("asta.id, asta.a_asset_id")
				->select("asta.depreciated_date")
				->select("asta.book_value, asta.market_value, asta.depreciated_value, asta.depreciation_accumulated")
				->from('a_assetamounts asta')
				->where('asta.a_asset_id', $record->id)
				->order_by('asta.id', 'asc');
			$a_assetamounts = $table->get();
			
			$a_assetamount_view = $this->load->view(
				'asset/asset/a_assetamount_view',
				array(
					'records'	=> $a_assetamounts->result()
				),
				TRUE
			);
		}
		
		$data = array(
			'form_action'			=> $form_action,
			'record'				=> $record,
			'a_assetamount_view'	=> $a_assetamount_view
		);
		$this->load->view('asset/asset/form', $data);
	}
	
	public function detail()
	{
		if (!is_authorized('asset/asset', 'index')) 
			access_denied();
		
		$id = $this->input->get_post('id');
		$a_assetamount_view = NULL;
		
		$record = NULL;
		if ($id !== NULL)
		{
			$table = $this->db
				->select("ast.id, ast.code, ast.name, ast.type, ast.voucher_no")
				->select("ast.m_product_id, pro.code m_product_code, pro.name m_product_name")
				->select("ast.c_region_id, rgn.code c_region_code, rgn.name c_region_name")
				->select("ast.c_department_id, dep.code c_department_code, dep.name c_department_name")
				->select("ast.c_location_id, loc.code c_location_code, loc.name c_location_name")
				->select("ast.c_businesspartner_supplier_id, bp_sup.code c_businesspartner_supplier_code, bp_sup.name c_businesspartner_supplier_name")
				->select("ast.c_businesspartner_user_id, bp_user.code c_businesspartner_user_code, bp_user.name c_businesspartner_user_name")
				->select("ast.quantity")
				->select("ast.purchase_date, ast.purchase_price, ast.currency")
				->select("ast.notes")
				->select("ast.depreciation_period_type, ast.depreciation_period_time")
				->from('a_assets ast')
				->join('m_products pro', "pro.id = ast.m_product_id")
				->join('c_regions rgn', "rgn.id = ast.c_region_id")
				->join('c_departments dep', "dep.id = ast.c_department_id")
				->join('c_locations loc', "loc.id = ast.c_location_id")
				->join('c_businesspartners bp_sup', "bp_sup.id = ast.c_businesspartner_supplier_id", 'left')
				->join('c_businesspartners bp_user', "bp_user.id = ast.c_businesspartner_user_id", 'left')
				->where('ast.id', $id)
				->get();
			if ($table->num_rows() > 0)
				$record = $table->first_row();
			else
				show_error("Asset not found", 400);
			
			// -- Asset Amounts --
			$table = $this->db
				->select("asta.id, asta.a_asset_id")
				->select("asta.depreciated_date")
				->select("asta.book_value, asta.market_value, asta.depreciated_value, asta.depreciation_accumulated")
				->from('a_assetamounts asta')
				->where('asta.a_asset_id', $record->id)
				->order_by('asta.id', 'asc');
			$a_assetamounts = $table->get();
			
			$a_assetamount_view = $this->load->view(
				'asset/asset/a_assetamount_view',
				array(
					'records'	=> $a_assetamounts->result()
				),
				TRUE
			);
		}
		
		$data = array(
			'record'				=> $record,
			'a_assetamount_view'	=> $a_assetamount_view
		);
		$this->load->view('asset/asset/detail', $data);
	}
	
	public function get_list_json()
	{
		if (!is_authorized('asset/asset', 'index')) 
			access_denied();
		
		$this->load->helper('date');
		
		$from_month = $this->input->get_post('from_month');
		$from_year = $this->input->get_post('from_year');
		
		$to_month = $this->input->get_post('to_month');
		$to_year = $this->input->get_post('to_year');
		
		$this->db
			->select("ast.id, ast.code, ast.name, ast.type, ast.voucher_no")
			->select("ast.m_product_id, pro.code m_product_code, pro.name m_product_name")
			->select("ast.c_region_id, rgn.code c_region_code, rgn.name c_region_name")
			->select("ast.c_department_id, dep.code c_department_code, dep.name c_department_name")
			->select("ast.c_location_id, loc.code c_location_code, loc.name c_location_name")
			->select("ast.c_businesspartner_supplier_id, bp_sup.code c_businesspartner_supplier_code, bp_sup.name c_businesspartner_supplier_name")
			->select("ast.c_businesspartner_user_id, bp_user.code c_businesspartner_user_code, bp_user.name c_businesspartner_user_name")
			->select("ast.quantity")
			->select("ast.purchase_date, ast.purchase_price, ast.currency")
			->from('a_assets ast')
			->join('m_products pro', "pro.id = ast.m_product_id")
			->join('c_regions rgn', "rgn.id = ast.c_region_id")
			->join('c_departments dep', "dep.id = ast.c_department_id")
			->join('c_locations loc', "loc.id = ast.c_location_id")
			->join('c_businesspartners bp_sup', "bp_sup.id = ast.c_businesspartner_supplier_id", 'left')
			->join('c_businesspartners bp_user', "bp_user.id = ast.c_businesspartner_user_id", 'left');
		
		$this->db->where("ast.purchase_date >=", date('Y-m-d', mktime(0, 0, 0, $from_month, 1, $from_year)));
		$this->db->where("ast.purchase_date <=", add_date(date('Y-m-d', mktime(0, 0, 0, $to_month, 1, $to_year)), -1, 1));
		
		parent::_get_list_json();
	}
	
	public function get_product_autocomplete_list_json()
	{
		if (!is_authorized('asset/asset', 'index')) 
			access_denied();
		
		$keywords = $this->input->get_post('term');
		
		$this->db
			->select("id")
			->select_concat(array("name", "' ('", "code", "')'"), 'value')
			->select_concat(array("name", "' ('", "code", "')'"), 'label')
			->from('m_products');
		
		if ($keywords)
			$this->db->where($this->db->concat(array("name", "' ('", "code", "')'")) . " LIKE '%" . $this->db->escape_like_str($keywords) . "%'", NULL, FALSE);
		
		parent::_get_list_autocomplete_json();
	}
	
	public function get_region_autocomplete_list_json()
	{
		if (!is_authorized('asset/asset', 'index')) 
			access_denied();
		
		$keywords = $this->input->get_post('term');
		
		$this->db
			->select("id")
			->select_concat(array("name", "' ('", "code", "')'"), 'value')
			->select_concat(array("name", "' ('", "code", "')'"), 'label')
			->from('c_regions');
		
		if ($keywords)
			$this->db->where($this->db->concat(array("name", "' ('", "code", "')'")) . " LIKE '%" . $this->db->escape_like_str($keywords) . "%'", NULL, FALSE);
		
		parent::_get_list_autocomplete_json();
	}
	
	public function get_department_autocomplete_list_json()
	{
		if (!is_authorized('asset/asset', 'index')) 
			access_denied();
		
		$keywords = $this->input->get_post('term');
		
		$this->db
			->select("id")
			->select_concat(array("name", "' ('", "code", "')'"), 'value')
			->select_concat(array("name", "' ('", "code", "')'"), 'label')
			->from('c_departments');
		
		if ($keywords)
			$this->db->where($this->db->concat(array("name", "' ('", "code", "')'")) . " LIKE '%" . $this->db->escape_like_str($keywords) . "%'", NULL, FALSE);
		
		parent::_get_list_autocomplete_json();
	}
	
	public function get_location_autocomplete_list_json()
	{
		if (!is_authorized('asset/asset', 'index')) 
			access_denied();
		
		$keywords = $this->input->get_post('term');
		
		$this->db
			->select("id")
			->select_concat(array("name", "' ('", "code", "')'"), 'value')
			->select_concat(array("name", "' ('", "code", "')'"), 'label')
			->from('c_locations');
		
		if ($keywords)
			$this->db->where($this->db->concat(array("name", "' ('", "code", "')'")) . " LIKE '%" . $this->db->escape_like_str($keywords) . "%'", NULL, FALSE);
		
		parent::_get_list_autocomplete_json();
	}
	
	public function get_businesspartner_autocomplete_list_json($type = '')
	{
		if (!is_authorized('asset/asset', 'index')) 
			access_denied();
		
		$keywords = $this->input->get_post('term');
		
		$this->db
			->select("id")
			->select_concat(array("name", "' ('", "code", "')'"), 'value')
			->select_concat(array("name", "' ('", "code", "')'"), 'label')
			->from('c_businesspartners')
			->where('type', $type);
		
		if ($keywords)
			$this->db->where($this->db->concat(array("name", "' ('", "code", "')'")) . " LIKE '%" . $this->db->escape_like_str($keywords) . "%'", NULL, FALSE);
		
		parent::_get_list_autocomplete_json();
	}
	
	public function insert()
	{
		if (!is_authorized('asset/asset', 'insert'))
			access_denied();
		
		parent::_execute('this', '_inserts', 
			array(), 
			array(
				array('field' => 'name', 'label' => 'Name', 'rules' => 'required'),
				array('field' => 'type', 'label' => 'Type', 'rules' => 'required'),
				array('field' => 'm_product_id', 'label' => 'Product', 'rules' => 'required'),
				array('field' => 'c_region_id', 'label' => 'Region', 'rules' => 'required'),
				array('field' => 'c_department_id', 'label' => 'Department', 'rules' => 'required'),
				array('field' => 'c_location_id', 'label' => 'Location', 'rules' => 'required'),
				array('field' => 'purchase_date', 'label' => 'Purchase Date', 'rules' => 'required'),
				array('field' => 'purchase_price', 'label' => 'Purchase Price', 'rules' => 'required|numeric'),
				array('field' => 'currency', 'label' => 'Currency', 'rules' => 'required'),
				array('field' => 'quantity', 'label' => 'Quantity', 'rules' => 'required|numeric'),
				array('field' => 'depreciation_period_type', 'label' => 'Period Type', 'rules' => 'required'),
				array('field' => 'depreciation_period_time', 'label' => 'Period Time', 'rules' => 'required|integer'),
				array('field' => 'duplicate_count', 'label' => 'Create Duplicate', 'rules' => 'required|integer')
			)
		);
	}
	
	protected function _inserts()
	{
		$user_id = $this->session->userdata('user_id');
		
		$duplicate_count = (int)$this->input->post('duplicate_count') + 1;
		
		for ($duplicate_counter = 1; $duplicate_counter <= $duplicate_count; $duplicate_counter++)
		{
			$data = new stdClass();
			$data->code = $this->input->post('code');
			$data->name = $this->input->post('name');
			$data->type = $this->input->post('type');
			$data->voucher_no = $this->input->post('voucher_no');
			$data->m_product_id = $this->input->post('m_product_id');
			$data->c_region_id = $this->input->post('c_region_id');
			$data->c_department_id = $this->input->post('c_department_id');
			$data->c_location_id = $this->input->post('c_location_id');
			$data->c_businesspartner_user_id = $this->input->post('c_businesspartner_user_id');
			$data->c_businesspartner_supplier_id = $this->input->post('c_businesspartner_supplier_id');
			$data->purchase_date = $this->input->post('purchase_date');
			$data->purchase_price = $this->input->post('purchase_price');
			$data->currency = $this->input->post('currency');
			$data->quantity = $this->input->post('quantity');
			$data->notes = $this->input->post('notes');
			
			$data->depreciation_period_type = $this->input->post('depreciation_period_type');
			$data->depreciation_period_time = $this->input->post('depreciation_period_time');
			
			$a_assetamounts = $this->populate_form_a_assetamounts();
			if ($a_assetamounts !== NULL)
			{
				$data->a_assetamounts = $a_assetamounts;
			}
			
			$this->lib_asset->asset_add($data, $user_id);
		}
	}
	
	public function update($id)
	{
		if (!is_authorized('asset/asset', 'update'))
			access_denied();
		
		$user_id = $this->session->userdata('user_id');
		
		$data = new stdClass();
		$data->code = $this->input->post('code');
		$data->name = $this->input->post('name');
		$data->type = $this->input->post('type');
		$data->voucher_no = $this->input->post('voucher_no');
		$data->m_product_id = $this->input->post('m_product_id');
		$data->c_region_id = $this->input->post('c_region_id');
		$data->c_department_id = $this->input->post('c_department_id');
		$data->c_location_id = $this->input->post('c_location_id');
		$data->c_businesspartner_user_id = $this->input->post('c_businesspartner_user_id');
		$data->c_businesspartner_supplier_id = $this->input->post('c_businesspartner_supplier_id');
		$data->purchase_date = $this->input->post('purchase_date');
		$data->purchase_price = $this->input->post('purchase_price');
		$data->currency = $this->input->post('currency');
		$data->quantity = $this->input->post('quantity');
		$data->notes = $this->input->post('notes');
		
		$data->depreciation_period_type = $this->input->post('depreciation_period_type');
		$data->depreciation_period_time = $this->input->post('depreciation_period_time');
		
		$a_assetamounts = $this->populate_form_a_assetamounts();
		if ($a_assetamounts !== NULL)
		{
			$data->a_assetamounts = $a_assetamounts;
		}
		
		parent::_execute('lib_asset', 'asset_update', 
			array($id, $data, $user_id), 
			array(
				array('field' => 'code', 'label' => 'Code', 'rules' => 'required'),
				array('field' => 'name', 'label' => 'Name', 'rules' => 'required'),
				array('field' => 'type', 'label' => 'Type', 'rules' => 'required'),
				array('field' => 'm_product_id', 'label' => 'Product', 'rules' => 'required'),
				array('field' => 'c_region_id', 'label' => 'Region', 'rules' => 'required'),
				array('field' => 'c_department_id', 'label' => 'Department', 'rules' => 'required'),
				array('field' => 'c_location_id', 'label' => 'Location', 'rules' => 'required'),
				array('field' => 'purchase_date', 'label' => 'Purchase Date', 'rules' => 'required'),
				array('field' => 'purchase_price', 'label' => 'Purchase Price', 'rules' => 'required|numeric'),
				array('field' => 'currency', 'label' => 'Currency', 'rules' => 'required'),
				array('field' => 'quantity', 'label' => 'Quantity', 'rules' => 'required|numeric'),
				array('field' => 'depreciation_period_type', 'label' => 'Period Type', 'rules' => 'required'),
				array('field' => 'depreciation_period_time', 'label' => 'Period Time', 'rules' => 'required|integer')
			)
		);
	}
	
	public function delete($id)
	{
		if (!is_authorized('asset/asset', 'delete')) 
			access_denied();
		
		$user_id = $this->session->userdata('user_id');
		
		parent::_execute('lib_asset', 'asset_remove', array($id, $user_id));
	}
	
	public function delete_by_ids()
	{
		if (!is_authorized('asset/asset', 'delete')) 
			access_denied();
		
		parent::_execute('this', '_delete_by_ids');
	}
	
	protected function _delete_by_ids()
	{
		$user_id = $this->session->userdata('user_id');
		
		$ids = $this->input->post('ids');
		if (!empty($ids) && is_array($ids))
		{
			foreach ($ids as $id)
			{
				$this->lib_asset->asset_remove($id, $user_id);
			}
		}
	}
	
	protected function populate_form_a_assetamounts()
	{
		$a_assetamounts_src = $this->input->post('a_assetamounts');
		if ($a_assetamounts_src === NULL)
			return NULL;
		
		if (!is_array($a_assetamounts_src))
			return NULL;
		
		$a_assetamounts = array();
		foreach ($a_assetamounts_src as $a_assetamount_src)
		{
			$a_assetamount = new stdClass();
			$a_assetamount->id = $a_assetamount_src['id'];
			$a_assetamount->depreciated_date = $a_assetamount_src['depreciated_date'];
			$a_assetamount->book_value = $a_assetamount_src['book_value'];
			$a_assetamount->market_value = $a_assetamount_src['market_value'];
			$a_assetamount->depreciated_value = $a_assetamount_src['depreciated_value'];
			$a_assetamount->depreciation_accumulated = $a_assetamount_src['depreciation_accumulated'];
			$a_assetamounts[] = $a_assetamount;
		}
		return $a_assetamounts;
	}
	
	public function get_depreciation_calculator()
	{
		if (!is_authorized('asset/asset', 'insert') && !is_authorized('asset/asset', 'update'))
			access_denied();
		
		$is_editable_mode = (int)$this->input->get_post('is_editable_mode');
		$a_asset_id = $this->input->get_post('a_asset_id');
		$purchase_date = $this->input->get_post('purchase_date');
		$purchase_price = $this->input->get_post('purchase_price');
		$type = $this->input->get_post('depreciation_period_type');
		$time = $this->input->get_post('depreciation_period_time');
		
		$depreciations = $this->lib_asset->asset_get_depreciation_list($purchase_date, $purchase_price, $type, $time, $a_asset_id);
		$this->load->view(
			($is_editable_mode ? 'asset/asset/a_assetamount_form' : 'asset/asset/a_assetamount_view'),
			array(
				'records'	=> $depreciations
			)
		);
	}
	
	public function barcode_label()
	{
		if (!is_authorized('asset/asset', 'index')) 
			access_denied();
		
		$ids = $this->input->get_post('ids');
		
		$records = array();
		
		if (!is_array($ids) && !empty($ids))
			return;
		
		$table = $this->db
			->select('ast.id, ast.code, ast.name')
			->select("rgn.code c_region_code, rgn.name c_region_name")
			->select("dep.code c_department_code, dep.name c_department_name")
			->from('a_assets ast')
			->join('c_regions rgn', "rgn.id = ast.c_region_id")
			->join('c_departments dep', "dep.id = ast.c_department_id")
			->where_in('ast.id', $ids)
			->get();
		$records = $table->result();
		
		$this->load->view('asset/asset/label_barcode', 
			array(
				'records'	=> $records
			)
		);
	}
	
	public function letter()
	{
		if (!is_authorized('asset/asset', 'index')) 
			access_denied();
		
		$ids = $this->input->get_post('ids');
		if (!is_array($ids) && !empty($ids))
			return;
		
		$header = new stdClass();
		$header->date = date('Y-m-d');
		
		$details = array();
		
		$table = $this->db
			->select("ast.id a_asset_id, ast.code a_asset_code, ast.name a_asset_name")
			->select("ast.m_product_id, pro.code m_product_code, pro.name m_product_name")
			->select("ast.c_region_id, rgn.code c_region_code, rgn.name c_region_name, rgn.address c_region_address, rgn.address_city c_region_address_city, rgn.phone_no c_region_phone_no, rgn.fax_no c_region_fax_no")
			->select("ast.c_department_id, dep.code c_department_code, dep.name c_department_name")
			->select("ast.c_businesspartner_user_id, bp_user.code c_businesspartner_user_code, bp_user.name c_businesspartner_user_name, bp_user.personal_position c_businesspartner_user_personal_position")
			->from('a_assets ast')
			->join('m_products pro', "pro.id = ast.m_product_id")
			->join('c_regions rgn', "rgn.id = ast.c_region_id")
			->join('c_departments dep', "dep.id = ast.c_department_id")
			->join('c_businesspartners bp_user', "bp_user.id = ast.c_businesspartner_user_id")
			->where_in('ast.id', $ids)
			->get();
		$records = $table->result();
		foreach ($records as $record_idx=>$record)
		{
			$detail = new stdClass();
			$detail->a_asset_code = $record->a_asset_code;
			$detail->a_asset_name = $record->a_asset_name;
			$detail->m_product_code = $record->m_product_code;
			$detail->m_product_name = $record->m_product_name;
			
			$detail->c_region_code = $record->c_region_code;
			$detail->c_region_name = $record->c_region_name;
			$detail->c_region_address = $record->c_region_address;
			$detail->c_region_address_city = $record->c_region_address_city;
			$detail->c_region_phone_no = $record->c_region_phone_no;
			$detail->c_region_fax_no = $record->c_region_fax_no;
			
			$detail->c_businesspartner_userfrom_code = NULL;
			$detail->c_businesspartner_userfrom_name = NULL;
			$detail->c_businesspartner_userfrom_personal_position = NULL;
			$detail->c_departmentfrom_code = NULL;
			$detail->c_departmentfrom_name = NULL;
			
			$detail->c_businesspartner_userto_code = $record->c_businesspartner_user_code;
			$detail->c_businesspartner_userto_name = $record->c_businesspartner_user_name;
			$detail->c_businesspartner_userto_personal_position = $record->c_businesspartner_user_personal_position;
			$detail->c_departmentto_code = $record->c_department_code;
			$detail->c_departmentto_name = $record->c_department_name;
			
			$details[] = $detail;
		}
		
		$html = $this->load->view(
			  'asset/asset_transfer/letter'
			, array(
				  'header'	=> $header
				, 'details'	=> $details
			)
			, TRUE
		);
		
		// echo $html;
		$this->load->library('lib_dompdf');
		$this->lib_dompdf->set_footer_notes("Print By : ". $this->session->userdata('name') .", Tgl Print : ". date($this->config->item('server_display_datetime_format'), strtotime(date('YmdHis'))));
		$this->lib_dompdf->load_as_pdf($html, 'transfer_letter.pdf', 'a4', 'portrait');
	}
}