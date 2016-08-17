<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Inventory_assembly extends MY_Controller 
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
		if (!is_authorized('material/inventory_assembly', 'index')) 
			access_denied();
		
		$data = array(
		);
		
		$content = array(
			'title'		=> "Assembly",
			'content' 	=> $this->load->view('material/inventory_assembly/index', $data, TRUE)
		);
		$this->_load_layout($content);
	}
	
	public function get_list_json()
	{
		if (!is_authorized('material/inventory_assembly', 'index')) 
			access_denied();
		
		$this->load->helper('date');
		
		$from_month = $this->input->get_post('from_month');
		$from_year = $this->input->get_post('from_year');
		
		$to_month = $this->input->get_post('to_month');
		$to_year = $this->input->get_post('to_year');
		
		$this->db
			->select("ia.id, ia.code, ia.assembly_date, ia.notes")
			->from('m_inventory_assemblies ia')
			->join('m_inventory_assemblysources iad', "iad.m_inventory_assembly_id = ia.id", 'left')
			->join('m_inventory_assemblytargets iat', "iad.m_inventory_assembly_id = ia.id", 'left')
			->group_by(
				array(
					'ia.id', 'ia.code', 'ia.assembly_date', 'ia.notes'
				)
			);
		$this->db->where("ia.assembly_date >=", date('Y-m-d', mktime(0, 0, 0, $from_month, 1, $from_year)));
		$this->db->where("ia.assembly_date <=", add_date(date('Y-m-d', mktime(0, 0, 0, $to_month, 1, $to_year)), -1, 1));
		
		$sql_invd = $this->lib_custom->project_sql_filter('iad.c_project_id', $this->c_project_ids);
		$sql_invt = $this->lib_custom->project_sql_filter('iat.c_project_id', $this->c_project_ids);
		$this->db->where("((".$sql_invd.") OR (".$sql_invt."))", NULL, FALSE);
		
		parent::_get_list_json();
	}
	
	public function form()
	{
		if (!is_authorized('material/inventory_assembly', 'index')) 
			access_denied();
		
		$form_action = $this->input->get_post('form_action');
		$id = $this->input->get_post('id');
		$record = NULL;
		if ($id !== NULL)
		{
			$this->db
				->select("ia.id, ia.code, ia.assembly_date")
				->select("ia.notes")
				->from('m_inventory_assemblies ia')
				->where('ia.id', $id);
			$table = $this->db->get();
			if ($table->num_rows() > 0)
			{
				$record = $table->first_row();
			}
			else
				show_error("Assembly not found", 400);
		}
		
		$m_inventory_assemblysources = array();
		if ($id !== NULL)
		{
			$this->db
				->select("SUM(iad.id) sum_id", FALSE)
				->select("SUM(iad.m_inventory_id) sum_m_inventory_id", FALSE)
				->select("iad.c_project_id, prj.code c_project_code, prj.name c_project_name")
				->select("iad.c_businesspartner_id, bp.code c_businesspartner_code, bp.name c_businesspartner_name")
				->select("iad.m_product_id, pro.code m_product_code, pro.name m_product_name, pro.netto m_product_netto")
				->select("iad.m_grid_id, gri.code m_grid_code")
				->select("iad.pallet, iad.barcode, iad.carton_no, iad.lot_no")
				->select("iad.volume_length, iad.volume_width, iad.volume_height")
				->select_if_null("SUM(iad.quantity_from)", 0, 'quantity_from')
				->select_if_null("SUM(iad.quantity_box_from)", 0, 'quantity_box_from')
				->select_if_null("SUM(iad.quantity_to)", 0, 'quantity_to')
				->select_if_null("SUM(iad.quantity_box_to)", 0, 'quantity_box_to')
				->from('m_inventory_assemblysources iad')
				->join('m_products pro', "pro.id = iad.m_product_id")
				->join('m_grids gri', "gri.id = iad.m_grid_id")
				->join('c_projects prj', "prj.id = iad.c_project_id", 'left')
				->join('c_businesspartners bp', "bp.id = iad.c_businesspartner_id", 'left')
				->where('iad.m_inventory_assembly_id', $id)
				->group_by(
					array(
						'iad.c_project_id', 'prj.code', 'prj.name',
						'iad.c_businesspartner_id', 'bp.code', 'bp.name',
						'iad.m_product_id', 'pro.code', 'pro.name', 'pro.netto',
						'iad.m_grid_id', 'gri.code',
						'iad.pallet', 'iad.barcode', 'iad.carton_no', 'iad.lot_no',
						'iad.volume_length', 'iad.volume_width', 'iad.volume_height'
					)
				);
			$table = $this->db->get();
			$m_inventory_assemblysources = $table->result();
			foreach ($m_inventory_assemblysources as $assemblysource_record_idx=>$assemblysource_record)
			{
				if (!(in_array($assemblysource_record->c_project_id, $this->c_project_ids) || $assemblysource_record->c_project_id === NULL))
					access_denied();
			}
		}
		
		$m_inventory_assemblytargets = array();
		if ($id !== NULL)
		{
			$this->db
				->select("iat.id")
				->select("iat.c_project_id, prj.code c_project_code, prj.name c_project_name")
				->select("prj.name c_project_text")
				->select("iat.c_businesspartner_id, bp.code c_businesspartner_code, bp.name c_businesspartner_name")
				->select_concat(array("bp.name", "' ('", "bp.code", "')'"), 'c_businesspartner_text')
				->select("iat.m_product_id, pro.code m_product_code, pro.name m_product_name, pro.netto m_product_netto")
				->select_concat(array("pro.name", "' ('", "pro.code", "')'"), 'm_product_text')
				->select("iat.m_grid_id, gri.code m_grid_code")
				->select("gri.code m_grid_text")
				->select("iat.pallet, iat.barcode, iat.carton_no, iat.lot_no")
				->select("iat.packed_date, iat.expired_date")
				->select("iat.condition")
				->select("iat.volume_length, iat.volume_width, iat.volume_height")
				->select("iat.quantity, iat.quantity_box")
				->from('m_inventory_assemblytargets iat')
				->join('m_products pro', "pro.id = iat.m_product_id")
				->join('m_grids gri', "gri.id = iat.m_grid_id")
				->join('c_projects prj', "prj.id = iat.c_project_id", 'left')
				->join('c_businesspartners bp', "bp.id = iat.c_businesspartner_id", 'left')
				->where('iat.m_inventory_assembly_id', $id);
			$table = $this->db->get();
			$m_inventory_assemblytargets = $table->result();
			foreach ($m_inventory_assemblytargets as $assemblytarget_record_idx=>$assemblytarget_record)
			{
				if (!(in_array($assemblytarget_record->c_project_id, $this->c_project_ids) || $assemblytarget_record->c_project_id === NULL))
					access_denied();
			}	
		}
		
		$data = array(
			'form_action'					=> $form_action,
			'record'						=> $record,
			'm_inventory_assemblysources'	=> $m_inventory_assemblysources,
			'm_inventory_assemblytargets'	=> $m_inventory_assemblytargets
		);
		$this->load->view('material/inventory_assembly/form', $data);
	}
	
	public function form_inventory()
	{
		if (!is_authorized('material/inventory_assembly', 'index')) 
			access_denied();
		
		$data = array(
		);
		
		$this->load->view('material/inventory_assembly/form_inventory', $data);
	}
	
	public function get_form_inventory_list_json()
	{
		if (!is_authorized('material/inventory_assembly', 'index')) 
			access_denied();
		
		$inventory_sql = 
			 "(SELECT	  inv.m_product_id, inv.m_grid_id, inv.c_project_id, inv.c_businesspartner_id "
			."			, inv.barcode, inv.pallet, inv.carton_no, inv.lot_no "
			."			, inv.volume_length, inv.volume_width, inv.volume_height "
			."			, ".$this->db->if_null("SUM(inv.quantity_box)", 0)." quantity_box_exist "
			."			, ".$this->db->if_null("SUM(inv.quantity_box_allocated)", 0)." quantity_box_allocated "
			."			, ".$this->db->if_null("SUM(inv.quantity_box_picked)", 0)." quantity_box_picked "
			."			, ".$this->db->if_null("SUM(inv.quantity_box_onhand)", 0)." quantity_box_onhand "
			."			, ".$this->db->if_null("SUM(inv.quantity)", 0)." quantity_exist "
			."			, ".$this->db->if_null("SUM(inv.quantity_allocated)", 0)." quantity_allocated "
			."			, ".$this->db->if_null("SUM(inv.quantity_picked)", 0)." quantity_picked "
			."			, ".$this->db->if_null("SUM(inv.quantity_onhand)", 0)." quantity_onhand "
			." FROM		m_inventories inv "
			." WHERE	inv.quantity_box > 0 "
			."			AND inv.quantity > 0 "
			."			AND ". $this->lib_custom->project_sql_filter('inv.c_project_id', $this->c_project_ids)
			." GROUP	BY "
			."			  inv.m_product_id, inv.m_grid_id, inv.c_project_id, inv.c_businesspartner_id "
			."			, inv.barcode, inv.pallet, inv.carton_no, inv.lot_no "
			."			, inv.volume_length, inv.volume_width, inv.volume_height "
			.") inv ";
		
		$this->db
			->select("pro.id m_product_id, pro.code m_product_code, pro.name m_product_name, pro.uom m_product_netto, pro.uom m_product_uom, pro.pack m_product_pack")
			->select("grd.id m_grid_id, grd.code m_grid_code")
			->select("wh.id m_warehouse_id, wh.code m_warehouse_code, wh.name m_warehouse_name")
			->select("prog.id m_productgroup_id, prog.code m_productgroup_code, prog.name m_productgroup_name")
			->select("inv.barcode, inv.pallet, inv.carton_no, inv.lot_no")
			->select("inv.volume_length, inv.volume_width, inv.volume_height")
			->select("prj.id c_project_id, prj.name c_project_name")
			->select("bp.id c_businesspartner_id, bp.name c_businesspartner_name")
			->select("inv.quantity_box_exist, inv.quantity_box_allocated, inv.quantity_box_picked, inv.quantity_box_onhand")
			->select("inv.quantity_exist, inv.quantity_allocated, inv.quantity_picked, inv.quantity_onhand")
			->from($inventory_sql, FALSE)
			->join('m_products pro', "pro.id = inv.m_product_id")
			->join('m_grids grd', "grd.id = inv.m_grid_id")
			->join('m_warehouses wh', "wh.id = grd.m_warehouse_id")
			->join('m_productgroups prog', "prog.id = grd.m_productgroup_id", 'left')
			->join('c_projects prj', "prj.id = inv.c_project_id", 'left')
			->join('c_businesspartners bp', "bp.id = inv.c_businesspartner_id", 'left');
		
		parent::_get_list_json();
	}
	
	public function detail()
	{
		if (!is_authorized('material/inventory_assembly', 'index')) 
			access_denied();
		
		$id = $this->input->get_post('id');
		$this->db
			->select("ia.id, ia.code, ia.assembly_date")
			->select("ia.notes")
			->from('m_inventory_assemblies ia')
			->where('ia.id', $id);
		$table = $this->db->get();
		if ($table->num_rows() > 0)
			$record = $table->first_row();
		else
			show_error("Assembly not found", 400);
		
		$m_inventory_assemblysources = array();
		if ($id !== NULL)
		{
			$this->db
				->select("iad.c_project_id, prj.code c_project_code, prj.name c_project_name")
				->select("iad.c_businesspartner_id, bp.code c_businesspartner_code, bp.name c_businesspartner_name")
				->select("iad.m_product_id, pro.code m_product_code, pro.name m_product_name, pro.netto m_product_netto")
				->select("iad.m_grid_id, gri.code m_grid_code")
				->select("iad.pallet, iad.barcode, iad.carton_no, iad.lot_no")
				->select("iad.volume_length, iad.volume_width, iad.volume_height")
				->select_if_null("SUM(iad.quantity_from)", 0, 'quantity_from')
				->select_if_null("SUM(iad.quantity_box_from)", 0, 'quantity_box_from')
				->select_if_null("SUM(iad.quantity_to)", 0, 'quantity_to')
				->select_if_null("SUM(iad.quantity_box_to)", 0, 'quantity_box_to')
				->from('m_inventory_assemblysources iad')
				->join('m_products pro', "pro.id = iad.m_product_id")
				->join('m_grids gri', "gri.id = iad.m_grid_id")
				->join('c_projects prj', "prj.id = iad.c_project_id", 'left')
				->join('c_businesspartners bp', "bp.id = iad.c_businesspartner_id", 'left')
				->where('iad.m_inventory_assembly_id', $id)
				->group_by(
					array(
						'iad.c_project_id', 'prj.code', 'prj.name',
						'iad.c_businesspartner_id', 'bp.code', 'bp.name',
						'iad.m_product_id', 'pro.code', 'pro.name', 'pro.netto',
						'iad.m_grid_id', 'gri.code',
						'iad.pallet', 'iad.barcode', 'iad.carton_no', 'iad.lot_no',
						'iad.volume_length', 'iad.volume_width', 'iad.volume_height'
					)
				);
			$table = $this->db->get();
			$m_inventory_assemblysources = $table->result();
			foreach ($m_inventory_assemblysources as $assemblysource_record_idx=>$assemblysource_record)
			{
				if (!(in_array($assemblysource_record->c_project_id, $this->c_project_ids) || $assemblysource_record->c_project_id === NULL))
					access_denied();
			}
		}
		
		$m_inventory_assemblytargets = array();
		if ($id !== NULL)
		{
			$this->db
				->select("iat.id")
				->select("iat.c_project_id, prj.code c_project_code, prj.name c_project_name")
				->select("iat.c_businesspartner_id, bp.code c_businesspartner_code, bp.name c_businesspartner_name")
				->select("iat.m_product_id, pro.code m_product_code, pro.name m_product_name, pro.netto m_product_netto")
				->select("iat.m_grid_id, gri.code m_grid_code")
				->select("iat.pallet, iat.barcode, iat.carton_no, iat.lot_no")
				->select("iat.packed_date, iat.expired_date")
				->select("iat.condition")
				->select("iat.volume_length, iat.volume_width, iat.volume_height")
				->select("iat.quantity, iat.quantity_box")
				->from('m_inventory_assemblytargets iat')
				->join('m_products pro', "pro.id = iat.m_product_id")
				->join('m_grids gri', "gri.id = iat.m_grid_id")
				->join('c_projects prj', "prj.id = iat.c_project_id", 'left')
				->join('c_businesspartners bp', "bp.id = iat.c_businesspartner_id", 'left')
				->where('iat.m_inventory_assembly_id', $id);
			$table = $this->db->get();
			$m_inventory_assemblytargets = $table->result();
			foreach ($m_inventory_assemblytargets as $assemblytarget_record_idx=>$assemblytarget_record)
			{
				if (!(in_array($assemblytarget_record->c_project_id, $this->c_project_ids) || $assemblytarget_record->c_project_id === NULL))
					access_denied();
			}
		}
		
		$data = array(
			'record'						=> $record,
			'm_inventory_assemblysources'	=> $m_inventory_assemblysources,
			'm_inventory_assemblytargets'	=> $m_inventory_assemblytargets
		);
		$this->load->view('material/inventory_assembly/detail', $data);
	}
	
	public function insert()
	{
		if (!is_authorized('material/inventory_assembly', 'insert')) 
			access_denied();
		
		parent::_execute('this', 'add_assembly_and_details', 
			array(),
			array(
				array('field' => 'code', 'label' => 'No', 'rules' => 'required'),
				array('field' => 'assembly_date', 'label' => 'Date', 'rules' => 'required')
			)
		);
	}
	
	protected function add_assembly_and_details()
	{
		$m_inventory_assemblysources = $this->input->post('m_inventory_assemblysources');
		if (!is_array($m_inventory_assemblysources))
			$m_inventory_assemblysources = array();
		if (empty($m_inventory_assemblysources))
			throw new Exception("No inventory source, please add inventory source.");
		
		$m_inventory_assemblytargets = $this->input->post('m_inventory_assemblytargets');
		if (!is_array($m_inventory_assemblytargets))
			$m_inventory_assemblytargets = array();
		if (empty($m_inventory_assemblytargets))
			throw new Exception("No inventory target, please add inventory target.");
		
		$this->load->library('material/lib_inventory_operation');
		
		$user_id = $this->session->userdata('user_id');
		
		$data_header = new stdClass();
		$data_header->code = $this->input->post('code');
		$data_header->assembly_date = $this->input->post('assembly_date');
		$data_header->notes = $this->input->post('notes');
		$id = $this->lib_inventory_operation->assembly_add($data_header, $user_id);
		
		// -- Add Assembly Sources --
		if (!empty($m_inventory_assemblysources) && is_array($m_inventory_assemblysources))
		{
			foreach ($m_inventory_assemblysources as $m_inventory_assemblysource)
			{
				$data_detail = new stdClass();
				$data_detail->m_inventory_assembly_id = $id;
				$data_detail->c_project_id = $m_inventory_assemblysource['c_project_id'];
				$data_detail->c_businesspartner_id = $m_inventory_assemblysource['c_businesspartner_id'];
				$data_detail->m_product_id = $m_inventory_assemblysource['m_product_id'];
				$data_detail->m_grid_id = $m_inventory_assemblysource['m_grid_id'];
				$data_detail->pallet = $m_inventory_assemblysource['pallet'];
				$data_detail->barcode = $m_inventory_assemblysource['barcode'];
				$data_detail->carton_no = $m_inventory_assemblysource['carton_no'];
				$data_detail->lot_no = $m_inventory_assemblysource['lot_no'];
				$data_detail->volume_length = $m_inventory_assemblysource['volume_length'];
				$data_detail->volume_width = $m_inventory_assemblysource['volume_width'];
				$data_detail->volume_height = $m_inventory_assemblysource['volume_height'];
				$data_detail->quantity = $m_inventory_assemblysource['quantity'];
				$this->lib_inventory_operation->assemblysource_add_by_properties($data_detail, $user_id);
			}
		}
		
		// -- Add Assembly Targets --
		if (!empty($m_inventory_assemblytargets) && is_array($m_inventory_assemblytargets))
		{
			foreach ($m_inventory_assemblytargets as $m_inventory_assemblytarget)
			{
				$data_detail = new stdClass();
				$data_detail->m_inventory_assembly_id = $id;
				$data_detail->c_project_id = $m_inventory_assemblytarget['c_project_id'];
				$data_detail->c_businesspartner_id = $m_inventory_assemblytarget['c_businesspartner_id'];
				$data_detail->m_product_id = $m_inventory_assemblytarget['m_product_id'];
				$data_detail->m_grid_id = $m_inventory_assemblytarget['m_grid_id'];
				$data_detail->pallet = $m_inventory_assemblytarget['pallet'];
				$data_detail->barcode = $m_inventory_assemblytarget['barcode'];
				$data_detail->carton_no = $m_inventory_assemblytarget['carton_no'];
				$data_detail->lot_no = $m_inventory_assemblytarget['lot_no'];
				$data_detail->condition = $m_inventory_assemblytarget['condition'];
				$data_detail->packed_date = $m_inventory_assemblytarget['packed_date'];
				$data_detail->expired_date = $m_inventory_assemblytarget['expired_date'];
				$data_detail->volume_length = $m_inventory_assemblytarget['volume_length'];
				$data_detail->volume_width = $m_inventory_assemblytarget['volume_width'];
				$data_detail->volume_height = $m_inventory_assemblytarget['volume_height'];
				$data_detail->quantity_box = $m_inventory_assemblytarget['quantity_box'];
				$data_detail->quantity = $m_inventory_assemblytarget['quantity'];
				$this->lib_inventory_operation->assemblytarget_add($data_detail, $user_id);
			}
		}
		
		return $id;
	}
	
	public function update($id)
	{
		if (!is_authorized('material/inventory_assembly', 'update')) 
			access_denied();
		
		parent::_execute('this', 'update_assembly_and_details', 
			array($id), 
			array(
				array('field' => 'code', 'label' => 'No', 'rules' => 'required'),
				array('field' => 'assembly_date', 'label' => 'Date', 'rules' => 'required')
			)
		);
	}
	
	protected function update_assembly_and_details($id)
	{
		$m_inventory_assemblysources = $this->input->post('m_inventory_assemblysources');
		if (!is_array($m_inventory_assemblysources))
			$m_inventory_assemblysources = array();
		if (empty($m_inventory_assemblysources))
			throw new Exception("No inventory source, please add inventory source.");
		
		$m_inventory_assemblytargets = $this->input->post('m_inventory_assemblytargets');
		if (!is_array($m_inventory_assemblytargets))
			$m_inventory_assemblytargets = array();
		if (empty($m_inventory_assemblytargets))
			throw new Exception("No inventory target, please add inventory target.");
		
		$this->load->library('material/lib_inventory_operation');
		
		$user_id = $this->session->userdata('user_id');
		
		$data_header = new stdClass();
		$data_header->code = $this->input->post('code');
		$data_header->assembly_date = $this->input->post('assembly_date');
		$data_header->notes = $this->input->post('notes');
		$updated_result = $this->lib_inventory_operation->assembly_update($id, $data_header, $user_id);
		
		// -- Assembly Source --
		$this->db
			->select("SUM(id) sum_id", FALSE)
			->select("SUM(m_inventory_id) sum_m_inventory_id", FALSE)
			->select("m_product_id")
			->select("m_grid_id")
			->select("c_project_id")
			->select("c_businesspartner_id")
			->select("pallet, barcode, carton_no, lot_no")
			->select("volume_length, volume_width, volume_height")
			->from('m_inventory_assemblysources')
			->where('m_inventory_assembly_id', $id)
			->group_by(
				array(
					  "m_product_id"
					, "m_grid_id"
					, "c_project_id"
					, "c_businesspartner_id"
					, "pallet", "barcode", "carton_no", "lot_no"
					, "volume_length", "volume_width", "volume_height"
				)
			);
		$this->lib_custom->project_query_filter('c_project_id', $this->c_project_ids);
		$table = $this->db->get();
		$m_inventory_assemblysources_existing = $table->result();
		
		// -- Remove Assembly Source --
		foreach ($m_inventory_assemblysources_existing as $m_inventory_assemblysource_existing)
		{
			$is_found_delete = TRUE;
			foreach ($m_inventory_assemblysources as $m_inventory_assemblysource)
			{
				if ($this->_is_assembly_detail_record_equal($m_inventory_assemblysource_existing, $m_inventory_assemblysource))
				{
					$is_found_delete = FALSE;
					break;
				}
			}
			if ($is_found_delete == TRUE)
			{
				$this->lib_inventory_operation->assemblysource_remove_by_properties($m_inventory_assemblysource_existing, $user_id);
			}
		}
		
		// -- Add Assembly Source --
		foreach ($m_inventory_assemblysources as $m_inventory_assemblysource)
		{
			$is_found_new = TRUE;
			foreach ($m_inventory_assemblysources_existing as $m_inventory_assemblysource_existing)
			{
				if ($this->_is_assembly_detail_record_equal($m_inventory_assemblysource_existing, $m_inventory_assemblysource))
				{
					$is_found_new = FALSE;
					break;
				}
			}
			if ($is_found_new == TRUE)
			{
				$data_detail = new stdClass();
				$data_detail->m_inventory_assembly_id = $id;
				$data_detail->c_project_id = $m_inventory_assemblysource['c_project_id'];
				$data_detail->c_businesspartner_id = $m_inventory_assemblysource['c_businesspartner_id'];
				$data_detail->m_product_id = $m_inventory_assemblysource['m_product_id'];
				$data_detail->m_grid_id = $m_inventory_assemblysource['m_grid_id'];
				$data_detail->pallet = $m_inventory_assemblysource['pallet'];
				$data_detail->barcode = $m_inventory_assemblysource['barcode'];
				$data_detail->carton_no = $m_inventory_assemblysource['carton_no'];
				$data_detail->lot_no = $m_inventory_assemblysource['lot_no'];
				$data_detail->volume_length = $m_inventory_assemblysource['volume_length'];
				$data_detail->volume_width = $m_inventory_assemblysource['volume_width'];
				$data_detail->volume_height = $m_inventory_assemblysource['volume_height'];
				$data_detail->quantity = $m_inventory_assemblysource['quantity'];
				$this->lib_inventory_operation->assemblysource_add_by_properties($data_detail, $user_id);
			}
		}
		
		// -- Assembly Target --
		$this->db
			->select("id")
			->from('m_inventory_assemblytargets')
			->where('m_inventory_assembly_id', $id);
		$this->lib_custom->project_query_filter('c_project_id', $this->c_project_ids);
		$table = $this->db->get();
		$m_inventory_assemblytargets_existing = $table->result();
		
		// -- Remove Assembly Target --
		foreach ($m_inventory_assemblytargets_existing as $m_inventory_assemblytarget_existing)
		{
			$is_found_delete = TRUE;
			foreach ($m_inventory_assemblytargets as $m_inventory_assemblytarget)
			{
				if ($m_inventory_assemblytarget_existing->id == $m_inventory_assemblytarget['id'])
				{
					$is_found_delete = FALSE;
					break;
				}
			}
			if ($is_found_delete == TRUE)
			{
				$this->lib_inventory_operation->assemblytarget_remove($m_inventory_assemblytarget_existing->id, $user_id);
			}
		}
		
		// -- Add Assembly Target --
		foreach ($m_inventory_assemblytargets as $m_inventory_assemblytarget)
		{
			$is_found_new = TRUE;
			foreach ($m_inventory_assemblytargets_existing as $m_inventory_assemblytarget_existing)
			{
				if ($m_inventory_assemblytarget_existing->id == $m_inventory_assemblytarget['id'])
				{
					$is_found_new = FALSE;
					break;
				}
			}
			if ($is_found_new == TRUE)
			{
				$data_detail = new stdClass();
				$data_detail->m_inventory_assembly_id = $id;
				$data_detail->c_project_id = $m_inventory_assemblytarget['c_project_id'];
				$data_detail->c_businesspartner_id = $m_inventory_assemblytarget['c_businesspartner_id'];
				$data_detail->m_product_id = $m_inventory_assemblytarget['m_product_id'];
				$data_detail->m_grid_id = $m_inventory_assemblytarget['m_grid_id'];
				$data_detail->pallet = $m_inventory_assemblytarget['pallet'];
				$data_detail->barcode = $m_inventory_assemblytarget['barcode'];
				$data_detail->carton_no = $m_inventory_assemblytarget['carton_no'];
				$data_detail->lot_no = $m_inventory_assemblytarget['lot_no'];
				$data_detail->condition = $m_inventory_assemblytarget['condition'];
				$data_detail->packed_date = $m_inventory_assemblytarget['packed_date'];
				$data_detail->expired_date = $m_inventory_assemblytarget['expired_date'];
				$data_detail->volume_length = $m_inventory_assemblytarget['volume_length'];
				$data_detail->volume_width = $m_inventory_assemblytarget['volume_width'];
				$data_detail->volume_height = $m_inventory_assemblytarget['volume_height'];
				$data_detail->quantity_box = $m_inventory_assemblytarget['quantity_box'];
				$data_detail->quantity = $m_inventory_assemblytarget['quantity'];
				$this->lib_inventory_operation->assemblytarget_add($data_detail, $user_id);
			}
		}
		
		return $updated_result;
	}
	
	private function _is_assembly_detail_record_equal($record, $input_array)
	{
		$is_equal = TRUE;
		foreach ($record as $field=>$value)
		{
			if (!isset($input_array[$field]))
				continue;
			
			if ($value === NULL)
				$value = '';
			
			if ($input_array[$field] === NULL)
				$input_array[$field] = '';
			
			if (strtolower(trim($input_array[$field])) != strtolower(trim($value)))
			{
				$is_equal = FALSE;
				break;
			}
		}
		return $is_equal;
	}
	
	public function delete($id)
	{
		if (!is_authorized('material/inventory_assembly', 'delete')) 
			access_denied();
		
		$this->load->library('material/lib_inventory_operation');
		
		$user_id = $this->session->userdata('user_id');
		
		parent::_execute('lib_inventory_operation', 'assembly_remove', array($id, $user_id));
	}
	
	public function get_product_autocomplete_list_json()
	{
		if (!is_authorized('material/inventory_assembly', 'insert') && !is_authorized('material/inventory_assembly', 'update')) 
			access_denied();
		
		$keywords = $this->input->get_post('term');
		
		$this->db
			->select("pro.id")
			->select_concat(array("pro.name", "' ('", "pro.code", "')'"), 'value')
			->select_concat(array("pro.name", "' ('", "pro.code", "')'"), 'label')
			->select("pro.netto netto")
			->from('m_products pro');
		
		if ($keywords)
			$this->db->where($this->db->concat(array("pro.name", "' ('", "pro.code", "')'")) . " LIKE '%" . $this->db->escape_like_str($keywords) . "%'", NULL, FALSE);
		
		parent::_get_list_autocomplete_json();
	}
	
	public function get_grid_autocomplete_list_json()
	{
		if (!is_authorized('material/inventory_assembly', 'insert') && !is_authorized('material/inventory_assembly', 'update')) 
			access_denied();
		
		$keywords = $this->input->get_post('term');
		
		$this->db
			->select("gri.id")
			->select("gri.code value")
			->select("gri.code label")
			->from('m_grids gri');
		
		if ($keywords)
			$this->db->like('gri.code', $keywords);
		
		parent::_get_list_autocomplete_json();
	}
	
	public function get_project_autocomplete_list_json()
	{
		if (!is_authorized('material/inventory_assembly', 'insert') && !is_authorized('material/inventory_assembly', 'update')) 
			access_denied();
		
		$keywords = $this->input->get_post('term');
		
		$this->db
			->select("prj.id")
			->select("prj.name value")
			->select("prj.name label")
			->from('c_projects prj');
		
		if ($keywords)
			$this->db->like('prj.name', $keywords);
		
		$this->lib_custom->project_query_filter('prj.id', $this->c_project_ids);
		
		parent::_get_list_autocomplete_json();
	}
	
	public function get_businesspartner_autocomplete_list_json()
	{
		if (!is_authorized('material/inventory_assembly', 'insert') && !is_authorized('material/inventory_assembly', 'update')) 
			access_denied();
		
		$keywords = $this->input->get_post('term');
		
		$this->db
			->select("bp.id")
			->select_concat(array("bp.name", "' ('", "bp.code", "')'"), 'value')
			->select_concat(array("bp.name", "' ('", "bp.code", "')'"), 'label')
			->from('c_businesspartners bp');
		
		if ($keywords)
			$this->db->where($this->db->concat(array("bp.name", "' ('", "bp.code", "')'")) . " LIKE '%" . $this->db->escape_like_str($keywords) . "%'", NULL, FALSE);
		
		parent::_get_list_autocomplete_json();
	}
}