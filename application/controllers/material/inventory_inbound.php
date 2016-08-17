<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Inventory_inbound extends MY_Controller 
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
		if (!is_authorized('material/inventory_inbound', 'index')) 
			access_denied();
		
		$data = array(
		);
		
		$content = array(
			'title'		=> "Inbound",
			'content' 	=> $this->load->view('material/inventory_inbound/index', $data, TRUE)
		);
		$this->_load_layout($content);
	}
	
	public function get_list_json()
	{
		if (!is_authorized('material/inventory_inbound', 'index')) 
			access_denied();
		
		$this->load->helper('date');
		
		$from_month = $this->input->get_post('from_month');
		$from_year = $this->input->get_post('from_year');
		
		$to_month = $this->input->get_post('to_month');
		$to_year = $this->input->get_post('to_year');
		
		$this->db
			->select("ii.id, ii.code, ii.inbound_date")
			->select($this->db->if_null("SUM(iid.quantity_box)", 0) . " quantity_box", FALSE)
			->select($this->db->if_null("SUM(iid.quantity)", 0) . " quantity", FALSE)
			->select("ir.id m_inventory_receive_id, ir.code m_inventory_receive_code, ir.receive_date m_inventory_receive_date")
			->select("ir.vehicle_no m_inventory_receive_vehicle_no, ir.vehicle_driver m_inventory_receive_vehicle_driver, ir.transport_mode m_inventory_receive_transport_mode")
			->select("oid.c_orderin_id, oi.code c_orderin_code, oi.orderin_date c_orderin_date")
			->select("oi.c_businesspartner_id, bp.name c_businesspartner_name")
			->select("oi.c_project_id, prj.name c_project_name")
			->from('m_inventory_inbounds ii')
			->join('m_inventory_inbounddetails iid', "iid.m_inventory_inbound_id = ii.id", 'left')
			->join('m_inventory_receivedetails ird', "ird.id = iid.m_inventory_receivedetail_id", 'left')
			->join('m_inventory_receives ir', "ir.id = ird.m_inventory_receive_id", 'left')
			->join('c_orderindetails oid', "oid.id = ird.c_orderindetail_id", 'left')
			->join('c_orderins oi', "oi.id = oid.c_orderin_id", 'left')
			->join('c_businesspartners bp', "bp.id = oi.c_businesspartner_id", 'left')
			->join('c_projects prj', "prj.id = oi.c_project_id", 'left')
			->group_by(
				array(
					'ii.id', 'ii.code', 'ii.inbound_date',
					'ir.id', 'ir.code', 'ir.receive_date',
					'ir.vehicle_no', 'ir.vehicle_driver', 'ir.transport_mode',
					'oid.c_orderin_id', 'oi.code', 'oi.orderin_date',
					'oi.c_businesspartner_id', 'bp.name',
					'oi.c_project_id', 'prj.name'
				)
			);
		$this->db->where("ii.inbound_date >=", date('Y-m-d', mktime(0, 0, 0, $from_month, 1, $from_year)));
		$this->db->where("ii.inbound_date <=", add_date(date('Y-m-d', mktime(0, 0, 0, $to_month, 1, $to_year)), -1, 1));
		
		$this->lib_custom->project_query_filter('oi.c_project_id', $this->c_project_ids);
		
		parent::_get_list_json();
	}
	
	public function get_list_detail_ref_json()
	{
		if (!is_authorized('material/inventory_inbound', 'index')) 
			access_denied();
		
		$this->db
			->select("ird.id, ird.condition, ird.quantity_box, ird.quantity")
			->select("ir.code m_inventory_receive_code, ir.receive_date m_inventory_receive_date")
			->select("ir.vehicle_no m_inventory_receive_vehicle_no, ir.vehicle_driver m_inventory_receive_vehicle_driver, ir.transport_mode m_inventory_receive_transport_mode")
			->select("oid.m_product_id, pro.code m_product_code, pro.name m_product_name, pro.uom m_product_uom")
			->select("oi.code c_orderin_code, oi.orderin_date c_orderin_date")
			->select("oi.c_businesspartner_id, bp.name c_businesspartner_name")
			->from('m_inventory_receivedetails ird')
			->join('m_inventory_receives ir', "ir.id = ird.m_inventory_receive_id")
			->join('c_orderindetails oid', "oid.id = ird.c_orderindetail_id")
			->join('c_orderins oi', "oi.id = oid.c_orderin_id")
			->join('c_businesspartners bp', "bp.id = oi.c_businesspartner_id", 'left')
			->join('m_products pro', "pro.id = oid.m_product_id")
			->where('ird.status_inventory_inbound <>', 'COMPLETE');
		
		$this->lib_custom->project_query_filter('oi.c_project_id', $this->c_project_ids);
		
		parent::_get_list_json();
	}
	
	public function get_list_detail_json()
	{
		if (!is_authorized('material/inventory_inbound', 'index')) 
			access_denied();
		
		$this->db
			->select("iid.id, iid.barcode, iid.quantity, iid.quantity_box, iid.pallet, iid.carton_no, iid.packed_date, iid.expired_date, iid.lot_no, iid.condition")
			->select("oid.m_product_id, pro.code product_code, pro.name product_name, pro.uom product_uom")
			->select("iid.m_grid_id, gri.code grid_code")
			->select("iid.created")
			->from('m_inventory_inbounddetails iid')
			->join('m_inventory_receivedetails ird', "ird.id = iid.m_inventory_receivedetail_id")
			->join('c_orderindetails oid', "oid.id = ird.c_orderindetail_id")
			->join('c_orderins oi', "oi.id = oid.c_orderin_id")
			->join('m_products pro', "pro.id = oid.m_product_id")
			->join('m_grids gri', "gri.id = iid.m_grid_id", 'left');
		
		$id = $this->input->get_post('id');
		if ($id !== '')
			$this->db->where("iid.m_inventory_inbound_id", $id);
		
		$this->lib_custom->project_query_filter('oi.c_project_id', $this->c_project_ids);
		
		parent::_get_list_json();
	}
	
	public function form()
	{
		if (!is_authorized('material/inventory_inbound', 'index')) 
			access_denied();
		
		$form_action = $this->input->get_post('form_action');
		$id = $this->input->get_post('id');
		$record = NULL;
		if ($id !== NULL)
		{
			$this->db
				->select("ii.id, ii.code, ii.inbound_date, ii.notes")
				->from('m_inventory_inbounds ii')
				->where('ii.id', $id);
			$table = $this->db->get();
			if ($table->num_rows() > 0)
				$record = $table->first_row();
			else
				show_error("Inbound not found", 400);
		}
		
		$data = array(
			'form_action'	=> $form_action,
			'record'		=> $record
		);
		$this->load->view('material/inventory_inbound/form', $data);
	}
	
	public function form_detail()
	{
		if (!is_authorized('material/inventory_inbound', 'index')) 
			access_denied();
		
		$id = $this->input->get_post('id');
		
		$data = array(
			'id'	=> $id
		);
		$this->load->view('material/inventory_inbound/form_detail', $data);
	}
	
	public function form_upload()
	{
		if (!is_authorized('material/inventory_inbound', 'insert')) 
			access_denied();
		
		$form_action = $this->input->get_post('form_action');
		$id = $this->input->get_post('id');
		
		$data = array(
			  'form_action'	=> $form_action
			, 'id'			=> $id
		);
		$this->load->view('material/inventory_inbound/form_upload', $data);
	}
	
	public function detail()
	{
		if (!is_authorized('material/inventory_inbound', 'index')) 
			access_denied();
		
		$id = $this->input->get_post('id');
		$this->db
			->select("ii.id, ii.code, ii.inbound_date, ii.notes")
			->from('m_inventory_inbounds ii')
			->where('ii.id', $id);
		$table = $this->db->get();
		if ($table->num_rows() > 0)
			$record = $table->first_row();
		else
			show_error("Inbound not found", 400);
		
		$data = array(
			'record'	=> $record
		);
		$this->load->view('material/inventory_inbound/detail', $data);
	}
	
	protected function get_list_detail_full_query()
	{
		$this->db
			->select("iid.id, iid.quantity, iid.quantity_box, iid.created")
			->select("iid.barcode, iid.carton_no, iid.pallet, iid.lot_no, iid.packed_date, iid.expired_date, iid.condition")
			->select("ird.m_inventory_receive_id")
			->select("ir.code m_inventory_receive_code, ir.receive_date m_inventory_receive_date")
			->select("ir.vehicle_no m_inventory_receive_vehicle_no, ir.vehicle_driver m_inventory_receive_vehicle_driver, ir.transport_mode m_inventory_receive_transport_mode")
			->select("oi.code c_orderin_code, oi.orderin_date c_orderin_date")
			->select("oi.c_businesspartner_id, bp.name c_businesspartner_name")
			->select("oid.m_product_id, pro.code m_product_code, pro.name m_product_name, pro.uom m_product_uom, pro.pack m_product_pack")
			->select("iid.m_grid_id, gri.code grid_code")
			->select("oi.c_project_id, prj.name c_project_name")
			->from('m_inventory_inbounddetails iid')
			->join('m_inventory_receivedetails ird', "ird.id = iid.m_inventory_receivedetail_id")
			->join('m_inventory_receives ir', "ir.id = ird.m_inventory_receive_id")
			->join('c_orderindetails oid', "oid.id = ird.c_orderindetail_id")
			->join('c_orderins oi', "oi.id = oid.c_orderin_id")
			->join('c_businesspartners bp', "bp.id = oi.c_businesspartner_id")
			->join('m_products pro', "pro.id = oid.m_product_id")
			->join('m_grids gri', "gri.id = iid.m_grid_id", 'left')
			->join('c_projects prj', "prj.id = oi.c_project_id", 'left');
		
		$this->lib_custom->project_query_filter('oi.c_project_id', $this->c_project_ids);
	}
	
	public function get_list_detail_full_json()
	{
		if (!is_authorized('material/inventory_inbound', 'index')) 
			access_denied();
		
		$id = $this->input->get_post('id');
		if ($id !== '')
			$this->db->where("iid.m_inventory_inbound_id", $id);
		
		$this->get_list_detail_full_query();
		
		parent::_get_list_json();
	}
	
	public function get_list_detail_full_excel()
	{
		if (!is_authorized('material/inventory_inbound', 'index')) 
			access_denied();
		
		$id = $this->input->get_post('id');
		
		$this->db
			->select("ii.id, ii.code, ii.inbound_date, ii.notes")
			->from('m_inventory_inbounds ii')
			->where('ii.id', $id);
		$table = $this->db->get();
		if ($table->num_rows() == 0)
			show_error("Inbound not found", 400);
		$header_record = $table->first_row();
		
		$this->db
			->select("oi.external_no c_orderin_external_no")
			->where("iid.m_inventory_inbound_id", $id);
		$this->get_list_detail_full_query();
		
		$this->load->library('jqgrid');
		
		$header_captions = array(
			'm_product_code'						=> 'Product Code',
			'm_product_name'						=> 'Name',
			'barcode'								=> 'Barcode', 
			'quantity_box'							=> 'Box',
			'quantity'								=> 'Quantity',
			'm_product_uom'							=> 'UOM',
			'carton_no'								=> 'Carton No',
			'packed_date'							=> 'Packed Date',
			'expired_date'							=> 'Expired Date',
			'pallet'								=> 'Pallet',
			'grid_code'								=> 'Grid',
			'lot_no'								=> 'Lot No',
			'condition'								=> 'Condition',
			'm_product_pack'						=> 'Pack',
			'created'								=> 'Scan Date',
			'm_inventory_receive_code'				=> 'Receive No', 
			'm_inventory_receive_date'				=> 'Receive Date', 
			'c_orderin_code'						=> 'Order In No', 
			'c_orderin_date'						=> 'Order In Date', 
			'c_businesspartner_name'				=> 'Business Partner',
			'c_orderin_external_no'					=> 'External No',
			'c_project_name'						=> 'Project',
			'm_inventory_receive_vehicle_no'		=> 'Vehicle No',
			'm_inventory_receive_vehicle_driver'	=> 'Driver',
			'm_inventory_receive_transport_mode'	=> 'Transport'
		);
		
		$data = $this->jqgrid->result();
		
		$result = $data->data;
		
		$records = array();
		$record_quantity_box_subtotal = 0;
		$record_quantity_subtotal = 0;
		$record_quantity_box_total = 0;
		$record_quantity_total = 0;
		foreach ($result as $record_idx=>$record)
		{
			$records[] = $record;
			
			$record_quantity_box_subtotal += $record->quantity_box;
			$record_quantity_subtotal += $record->quantity;
			
			$record_quantity_box_total += $record->quantity_box;
			$record_quantity_total += $record->quantity;
			
			$pallet = $record->pallet;
			$pallet_next = (isset($result[$record_idx + 1]->pallet) ? $result[$record_idx + 1]->pallet : NULL);
			if ($pallet != $pallet_next)
			{
				$record_new = new stdClass();
				foreach ($record as $field=>$value)
					$record_new->$field = NULL;
				$record_new->m_product_code = "Total Pallet ".$record->pallet;
				$record_new->quantity_box = $record_quantity_box_subtotal;
				$record_new->quantity = $record_quantity_subtotal;
				$record_new->m_product_uom = $record->m_product_uom;
				$records[] = $record_new;
				
				$record_quantity_box_subtotal = 0;
				$record_quantity_subtotal = 0;
			}
		}
		if (count($result) > 0)
		{
			$record_new = new stdClass();
			foreach ($result[0] as $field=>$value)
				$record_new->$field = NULL;
			$record_new->m_product_code = "Total";
			$record_new->quantity_box = $record_quantity_box_total;
			$record_new->quantity = $record_quantity_total;
			$records[] = $record_new;
		}
		
		$this->load->helper('file');
		$file_name = get_clear_filename($header_record->code .'__'. $header_record->inbound_date);
		
		$this->load->library('excel');
		$this->excel->write_stream_acos(
			array(
				'In' => $records
			), 
			'InboundDetail__'.$file_name.'.xls',
			array(
				'In' => $header_captions
			)
		);
	}
	
	public function get_product_autocomplete_list_json()
	{
		if (!is_authorized('material/inventory_inbound', 'index')) 
			access_denied();
		
		$keywords = $this->input->get_post('term');
		
		$this->db
			->select("ird.m_inventory_receive_id, oid.m_product_id")
			->select_concat(array("pro.name", "' ('", "pro.code", "' / '", "ir.code", "' / '", $this->db->cast_to_string('ird.quantity', 16), "' / '", $this->db->if_null("ird.condition", "''"), "')'"), 'value')
			->select_concat(array("pro.name", "' ('", "pro.code", "' / '", "ir.code", "' / '", $this->db->cast_to_string('ird.quantity', 16), "' / '", $this->db->if_null("ird.condition", "''"), "')'"), 'label')
			->from('m_inventory_receivedetails ird')
			->join('m_inventory_receives ir', "ir.id = ird.m_inventory_receive_id")
			->join('c_orderindetails oid', "oid.id = ird.c_orderindetail_id")
			->join('c_orderins oi', "oi.id = oid.c_orderin_id")
			->join('m_products pro', "pro.id = oid.m_product_id")
			->group_by(
				array(
					'ird.m_inventory_receive_id', 'oid.m_product_id', 
					'ird.quantity', 'ird.condition',
					'pro.name', 'pro.code',
					'ir.code'
				)
			);
		
		if ($keywords)
			$this->db->where($this->db->concat(array("pro.name", "' ('", "pro.code", "' / '", "ir.code", "' / '", $this->db->cast_to_string('ird.quantity', 16), "' / '", $this->db->if_null("ird.condition", "''"), "')'")) . " LIKE '%" . $this->db->escape_like_str($keywords) . "%'", NULL, FALSE);
		
		$this->lib_custom->project_query_filter('oi.c_project_id', $this->c_project_ids);
		
		parent::_get_list_autocomplete_json();
	}
	
	public function insert()
	{
		if (!is_authorized('material/inventory_inbound', 'insert')) 
			access_denied();
		
		$this->load->library('material/lib_inventory_in');
		
		$user_id = $this->session->userdata('user_id');
		
		$data = new stdClass();
		$data->code = $this->input->post('code');
		$data->inbound_date = $this->input->post('inbound_date');
		$data->notes = $this->input->post('notes');
		
		parent::_execute('lib_inventory_in', 'inbound_add', 
			array($data, $user_id),
			array(
				array('field' => 'code', 'label' => 'No', 'rules' => 'required'),
				array('field' => 'inbound_date', 'label' => 'Date', 'rules' => 'required')
			)
		);
	}
	
	public function parse_barcode()
	{
		if (!is_authorized('material/inventory_inbound', 'index')) 
			access_denied();
		
		$m_product_id = '';
		$m_inventory_receivedetail_id = $this->input->get_post('m_inventory_receivedetail_id');
		if ($m_inventory_receivedetail_id !== '')
		{
			$table = $this->db
				->select('oid.m_product_id')
				->from('m_inventory_receivedetails ird')
				->join('c_orderindetails oid', "oid.id = ird.c_orderindetail_id")
				->where('ird.id', $m_inventory_receivedetail_id)
				->get();
			if ($table->num_rows() > 0)
			{
				$table_record = $table->first_row();
				$m_product_id = $table_record->m_product_id;
			}
		}
		$barcode = $this->input->get_post('barcode');
		
		$this->load->library('custom/lib_custom');
		$response = $this->lib_custom->inbounddetail_parse_barcode_by_id($m_product_id, $barcode);
		if ($response->packed_date !== NULL)
		{
			$date_value = date_create_from_format('Y-m-d', $response->packed_date);
			if ($date_value !== FALSE)
				$response->packed_date = date_format($date_value, 'ymd');
		}
		
		$this->result_json($response);
	}
	
	public function insert_detail()
	{
		if (!is_authorized('material/inventory_inbound', 'insert')) 
			access_denied();
		
		parent::_execute('this', '_insert_detail', 
			array(),
			array(
				array('field' => 'm_inventory_inbound_id', 'label' => 'Inventory Inbound', 'rules' => 'integer|required'),
				array('field' => 'm_inventory_receivedetail_id', 'label' => 'Inventory Receive Detail', 'rules' => 'integer|required'),
				array('field' => 'barcode', 'label' => 'Barcode', 'rules' => 'required'),
				array('field' => 'quantity', 'label' => 'Quantity', 'rules' => 'numeric|required'),
				array('field' => 'quantity_box', 'label' => 'Quantity Box', 'rules' => 'numeric|required'),
				array('field' => 'pallet', 'label' => 'Pallet', 'rules' => 'required'),
				array('field' => 'carton_no', 'label' => 'Carton No', 'rules' => 'required')
			)
		);
	}
	
	protected function _insert_detail()
	{
		$user_id = $this->session->userdata('user_id');
		
		$this->load->library('material/lib_inventory_in');
		
		$m_inventory_inbound_id = $this->input->post('m_inventory_inbound_id');
		$pallet = $this->input->post('pallet');
		
		$data = new stdClass();
		$data->m_inventory_inbound_id = $m_inventory_inbound_id;
		$data->m_inventory_receivedetail_id = $this->input->post('m_inventory_receivedetail_id');
		$data->barcode = $this->input->post('barcode');
		$data->quantity_box = $this->input->post('quantity_box');
		if (empty($data->quantity_box))
			$data->quantity_box = 1;
		$data->quantity = $this->input->post('quantity');
		$data->pallet = $pallet;
		$data->carton_no = $this->input->post('carton_no');
		$data->lot_no = $this->input->post('lot_no');
		$data->condition = $this->input->post('condition');
		if ($this->input->post('packed_date') !== NULL)
		{
			$packed_date = $this->input->post('packed_date');
			if (!empty($packed_date))
			{
				$date_value = date_create_from_format('ymd', $packed_date);
				if ($date_value !== FALSE)
					$data->packed_date = date_format($date_value, 'Y-m-d');
				else
					$data->packed_date = $packed_date;
			}
		}
		if ($this->input->post('expired_date') !== NULL)
		{
			$expired_date = $this->input->post('expired_date');
			if (!empty($expired_date))
			{
				$date_value = date_create_from_format('ymd', $expired_date);
				if ($date_value !== FALSE)
					$data->expired_date = date_format($date_value, 'Y-m-d');
				else
					$data->expired_date = $expired_date;
			}
		}
		$m_grid_id = NULL;
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
				$m_grid_id = $table_record->id;
			}
		}
		$data->m_grid_id = $m_grid_id;
		
		$response = new stdClass();
		$response->m_inventory_inbounddetail_id = $this->lib_inventory_in->inbounddetail_add($data, $user_id);
		$response->counter = $this->_get_detail_counter($m_inventory_inbound_id, $pallet);
		return $response;
	}
	
	public function update($id)
	{
		if (!is_authorized('material/inventory_inbound', 'update')) 
			access_denied();
		
		$this->load->library('material/lib_inventory_in');
		
		$user_id = $this->session->userdata('user_id');
		
		$data = new stdClass();
		$data->code = $this->input->post('code');
		$data->inbound_date = $this->input->post('inbound_date');
		$data->notes = $this->input->post('notes');
		
		parent::_execute('lib_inventory_in', 'inbound_update', 
			array($id, $data, $user_id), 
			array(
				array('field' => 'code', 'label' => 'No', 'rules' => 'required'),
				array('field' => 'inbound_date', 'label' => 'Date', 'rules' => 'required')
			)
		);
	}
	
	public function delete($id)
	{
		if (!is_authorized('material/inventory_inbound', 'delete')) 
			access_denied();
		
		$this->load->library('material/lib_inventory_in');
		
		$user_id = $this->session->userdata('user_id');
		
		parent::_execute('lib_inventory_in', 'inbound_remove', array($id, $user_id));
	}
	
	public function delete_detail($id)
	{
		if (!is_authorized('material/inventory_inbound', 'delete')) 
			access_denied();
		
		parent::_execute('this', '_delete_detail', array($id));
	}
	
	protected function _delete_detail($id)
	{
		$table_record = new stdClass();
		$table_record->m_inventory_inbound_id = NULL;
		$table_record->pallet = NULL;
		
		$table = $this->db
			->select('iid.m_inventory_inbound_id, iid.pallet')
			->from('m_inventory_inbounddetails iid')
			->where('iid.id', $id)
			->get();
		if ($table->num_rows() > 0)
		{
			$table_record = $table->first_row();
		}
		
		$this->load->library('material/lib_inventory_in');
		
		$user_id = $this->session->userdata('user_id');
		
		$response = new stdClass();
		$response->m_inventory_inbounddetail_id = $this->lib_inventory_in->inbounddetail_remove($id, $user_id);
		$response->counter = $this->_get_detail_counter($table_record->m_inventory_inbound_id, $table_record->pallet);
		return $response;
	}
	
	public function get_detail_counter()
	{
		$m_inventory_inbound_id = $this->input->get_post('m_inventory_inbound_id');
		$pallet = $this->input->get_post('pallet');
		
		$counter = $this->_get_detail_counter($m_inventory_inbound_id, $pallet);
		
		$response = new stdClass();
		$response->response = TRUE;
		$response->value = $counter;
		$response->data = array();
		
		$this->result_json($response);
	}
	
	protected function _get_detail_counter($m_inventory_inbound_id, $pallet)
	{
		$counter = 0;
		$table = $this->db
			->select_if_null("COUNT(iid.id)", 0, 'counter')
			->from('m_inventory_inbounddetails iid')
			->where('iid.m_inventory_inbound_id', $m_inventory_inbound_id)
			->where('iid.pallet', $pallet)
			->get();
		if ($table->num_rows() > 0)
		{
			$table_record = $table->first_row();
			$counter = $table_record->counter;
		}
		return $counter;
	}

	public function label_document()
	{
		if (!is_authorized('material/inventory_inbound', 'index')) 
			access_denied();
		
		$id = $this->input->get_post('id');
		
		$this->db
			->select("prj.code c_project_code")
			->select("iid.pallet, iid.lot_no, iid.expired_date")
			->select("pro.code m_product_code, pro.name m_product_name, pro.pack m_product_pack")
			->select("oi.code c_orderin_code")
			->select("ir.receive_date m_inventory_receive_date")
			->select("gri.code m_grid_code")
			->select_sum("iid.quantity_box", 'quantity_box')
			->select_sum("iid.quantity", 'quantity')
			->from('m_inventory_inbounddetails iid')
			->join('m_inventory_receivedetails ird', "ird.id = iid.m_inventory_receivedetail_id")
			->join('m_inventory_receives ir', "ir.id = ird.m_inventory_receive_id")
			->join('c_orderindetails oid', "oid.id = ird.c_orderindetail_id")
			->join('c_orderins oi', "oi.id = oid.c_orderin_id")
			->join('m_grids gri', "gri.id = iid.m_grid_id")
			->join('m_products pro', "pro.id = oid.m_product_id")
			->join('c_projects prj', "prj.id = oi.c_project_id", 'left')
			->where("iid.m_inventory_inbound_id", $id)
			->group_by(
				array(
					  'prj.code'
					, 'iid.pallet', 'iid.lot_no', 'iid.expired_date'
					, 'pro.code', 'pro.name', 'pro.pack'
					, 'oi.code'
					, 'ir.receive_date'
					, 'gri.code'
				)
			)
			->order_by('iid.pallet', 'asc');
		
		$this->lib_custom->project_query_filter('oi.c_project_id', $this->c_project_ids);
		
		$table = $this->db->get();
		$m_inventory_inbounddetails = $table->result();
		
		$this->label_document_pdf($m_inventory_inbounddetails);
	}
	
	protected function label_document_pdf($details)
	{
		$data = array(
			'details'	=> $details
		);
		$this->load->view('material/inventory_inbound/label_document', $data);
	}
	
	public function upload()
	{
		if (!is_authorized('material/inventory_inbound', 'insert'))
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
		
		$m_inventory_inbound_id = $this->input->get_post('id');
		
		$config = array(
			'upload_path'	=> './upload/wms/inbound',
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
				
				$m_inventory_inbounddetails = array();
				foreach ($excel_sheets as $excel_sheet_name=>$excel_sheet)
				{
					try
					{
						for ($m_inventory_inbounddetail_count = 1; $m_inventory_inbounddetail_count < count($excel_sheet); $m_inventory_inbounddetail_count++)
						{
							$m_inventory_receive_code = $this->excel->read_value($excel_sheet, $m_inventory_inbounddetail_count, 0);
							$table = $this->db
								->select('ir.id')
								->from('m_inventory_receives ir')
								->where('ir.code', $m_inventory_receive_code)
								->get();
							if ($table->num_rows() == 0)
								throw new Exception("Receive code '".$m_inventory_receive_code."' is not found!");
							$table_record = $table->first_row();
							$m_inventory_receive_id = $table_record->id;
							
							$m_product_code = $this->excel->read_value($excel_sheet, $m_inventory_inbounddetail_count, 1);
							$table = $this->db
								->select('ird.id')
								->from('m_inventory_receivedetails ird')
								->join('c_orderindetails oid', "oid.id = ird.c_orderindetail_id")
								->join('m_products pro', "pro.id = oid.m_product_id")
								->where('ird.m_inventory_receive_id', $m_inventory_receive_id)
								->where('pro.code', $m_product_code)
								->get();
							if ($table->num_rows() == 0)
								throw new Exception("Product code '".$m_product_code."' is not found in receive code '".$m_inventory_receive_code."'!");
							$table_record = $table->first_row();
							$m_inventory_receivedetail_id = $table_record->id;
							
							$m_grid_id = NULL;
							$m_grid_code = $this->excel->read_value($excel_sheet, $m_inventory_inbounddetail_count, 8);
							if ($m_grid_code !== NULL && $m_grid_code !== '')
							{
								$table = $this->db
									->select('gri.id')
									->from('m_grids gri')
									->where('gri.code', $m_grid_code)
									->get();
								if ($table->num_rows() == 0)
									throw new Exception("Grid code '".$m_grid_code."' is not found!");
								$table_record = $table->first_row();
								$m_grid_id = $table_record->id;
							}
							
							$m_inventory_inbounddetail = new stdClass();
							$m_inventory_inbounddetail->m_inventory_inbound_id = $m_inventory_inbound_id;
							$m_inventory_inbounddetail->m_inventory_receivedetail_id = $m_inventory_receivedetail_id;
							$m_inventory_inbounddetail->barcode = $this->excel->read_value($excel_sheet, $m_inventory_inbounddetail_count, 3);
							$m_inventory_inbounddetail->quantity_box = $this->excel->read_value($excel_sheet, $m_inventory_inbounddetail_count, 4);
							$m_inventory_inbounddetail->quantity = $this->excel->read_value($excel_sheet, $m_inventory_inbounddetail_count, 5);
							$m_inventory_inbounddetail->carton_no = $this->excel->read_value($excel_sheet, $m_inventory_inbounddetail_count, 6);
							$m_inventory_inbounddetail->pallet = $this->excel->read_value($excel_sheet, $m_inventory_inbounddetail_count, 7);
							$m_inventory_inbounddetail->m_grid_id = $m_grid_id;
							$m_inventory_inbounddetail->packed_date = $this->excel->read_value($excel_sheet, $m_inventory_inbounddetail_count, 9, 'date');
							$m_inventory_inbounddetail->expired_date = $this->excel->read_value($excel_sheet, $m_inventory_inbounddetail_count, 10, 'date');
							$m_inventory_inbounddetail->lot_no = $this->excel->read_value($excel_sheet, $m_inventory_inbounddetail_count, 11);
							$m_inventory_inbounddetails[] = $m_inventory_inbounddetail;
						}
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
				
				if (count($m_inventory_inbounddetails) > 0)
				{
					$this->load->library('material/lib_inventory_in');
					
					$m_inventory_inbounddetail_no = 1;
					
					$this->db->trans_begin();
					try
					{
						foreach ($m_inventory_inbounddetails as $m_inventory_inbounddetail)
						{
							$this->lib_inventory_in->inbounddetail_add($m_inventory_inbounddetail, $user_id);
							$m_inventory_inbounddetail_no++;
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
						throw new Exception("Error on line ".$m_inventory_inbounddetail_no.":\n".$e->getMessage());
					}
				}
				
				if (count($error_messages) == 0 && count($m_inventory_inbounddetails) == 0)
					$result->value = "No data uploaded.";
			}
			catch(Exception $e)
			{
				$result->value = $e->getMessage();
			}
		}
		
		$this->result_json($result);
	}

	public function tally_sheet()
	{
		if (!is_authorized('material/inventory_inbound', 'index')) 
			access_denied();
		
		$id = $this->input->get_post('id');
		
		$this->db
			->select("ii.id, ii.code, ii.inbound_date, ii.notes")
			->from('m_inventory_inbounds ii')
			->where('ii.id', $id);
		$table = $this->db->get();
		if ($table->num_rows() == 0)
			show_error("Inbound not found", 400);
		$header_record = $table->first_row();
		
		$this->db
			->select("iid.id, iid.quantity, iid.quantity_box, iid.created")
			->select("iid.quantity / iid.quantity_box quantity_avg", FALSE)
			->select("iid.barcode, iid.carton_no, iid.pallet, iid.lot_no, iid.packed_date, iid.expired_date, iid.condition")
			->select("ird.m_inventory_receive_id")
			->select("ir.code m_inventory_receive_code, ir.receive_date m_inventory_receive_date")
			->select("ir.vehicle_no m_inventory_receive_vehicle_no, ir.vehicle_driver m_inventory_receive_vehicle_driver, ir.transport_mode m_inventory_receive_transport_mode")
			->select("oi.code c_orderin_code, oi.orderin_date c_orderin_date")
			->select("oi.external_no c_orderin_external_no")
			->select("oi.c_businesspartner_id, bp.name c_businesspartner_name")
			->select("oid.c_orderin_id")
			->select("oid.m_product_id, pro.code m_product_code, pro.name m_product_name, pro.uom m_product_uom, pro.pack m_product_pack")
			->select("iid.m_grid_id, gri.code m_grid_code")
			->select("oi.c_project_id, prj.name c_project_name")
			->from('m_inventory_inbounddetails iid')
			->join('m_inventory_receivedetails ird', "ird.id = iid.m_inventory_receivedetail_id")
			->join('m_inventory_receives ir', "ir.id = ird.m_inventory_receive_id")
			->join('c_orderindetails oid', "oid.id = ird.c_orderindetail_id")
			->join('c_orderins oi', "oi.id = oid.c_orderin_id")
			->join('c_businesspartners bp', "bp.id = oi.c_businesspartner_id")
			->join('m_products pro', "pro.id = oid.m_product_id")
			->join('m_grids gri', "gri.id = iid.m_grid_id", 'left')
			->join('c_projects prj', "prj.id = oi.c_project_id", 'left')
			->where("iid.m_inventory_inbound_id", $id);
		$this->lib_custom->project_query_filter('oi.c_project_id', $this->c_project_ids);
		$table = $this->db
			->order_by('m_inventory_receive_id', 'asc')
			->order_by('c_orderin_id', 'asc')
			->order_by('c_businesspartner_id', 'asc')
			->order_by('c_project_id', 'asc')
			->order_by('pallet', 'asc')
			->order_by('m_product_name', 'asc')
			->order_by('id', 'asc')
			->get();
		$records = $table->result();
		
		$grouped_records = array();
		foreach ($records as $record_idx=>$record)
		{
			$header_key = md5(
				 'IR_' .$record->m_inventory_receive_id
				.'OI_' .$record->c_orderin_id
				.'BP_' .$record->c_businesspartner_id
				.'PRJ_'.$record->c_project_id
			);
			$product_key = $record->m_product_id;
			$pallet_key = $record->pallet;
			$carton_key = $record->carton_no;
			
			if (!isset($grouped_records[$header_key]))
			{
				$grouped_header_record = new stdClass();
				$grouped_header_record->code = $header_record->code;
				$grouped_header_record->inbound_date = $header_record->inbound_date;
				$grouped_header_record->m_inventory_receive_code = $record->m_inventory_receive_code;
				$grouped_header_record->c_businesspartner_name = $record->c_businesspartner_name;
				$grouped_header_record->c_project_name = $record->c_project_name;
				$grouped_header_record->m_inventory_receive_vehicle_no = $record->m_inventory_receive_vehicle_no;
				$grouped_header_record->m_inventory_receive_vehicle_driver = $record->m_inventory_receive_vehicle_driver;
				$grouped_header_record->c_orderin_external_no = $record->c_orderin_external_no;
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
				$grouped_product_record->pallets = array();
				$grouped_records[$header_key]->products[$product_key] = $grouped_product_record;
			}
			
			if (!isset($grouped_records[$header_key]->products[$product_key]->pallets[$pallet_key]))
			{
				$grouped_pallet_record = new stdClass();
				$grouped_pallet_record->pallet = $record->pallet;
				$grouped_pallet_record->cartons = array();
				$grouped_records[$header_key]->products[$product_key]->pallets[$pallet_key] = $grouped_pallet_record;
			}
			
			for ($quantity_box = 1; $quantity_box <= $record->quantity_box; $quantity_box++)
			{
				$grouped_carton_record = new stdClass();
				$grouped_carton_record->carton_no = $record->carton_no;
				$grouped_carton_record->quantity = $record->quantity_avg;
				$grouped_records[$header_key]->products[$product_key]->pallets[$pallet_key]->cartons[] = $grouped_carton_record;
			}
		}
		
		$data = array(
			'data'	=> $grouped_records
		);
		$html = $this->load->view('material/inventory_inbound/tally_sheet', $data, TRUE);
		
		// echo $html;
		$this->load->library('lib_dompdf');
		$this->lib_dompdf->load_as_pdf($html, 'tally_sheet_in.pdf', 'a4', 'portrait');
	}
}