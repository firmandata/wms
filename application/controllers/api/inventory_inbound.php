<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'/libraries/REST_Controller.php';

class Inventory_inbound extends REST_Controller
{
	private $c_project_ids;
	
	function __construct()
    {
        parent::__construct();
		
		$this->load->library('custom/lib_custom');
		
		$user_id = $this->session->userdata('user_id');
		$this->c_project_ids = $this->lib_custom->project_get_ids($user_id);
    }
    
    function list_get()
    {
		if (!is_authorized('material/inventory_inbound', 'index')) 
			access_denied();
		
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
		
		$this->load->helper('date');
		
		$from_month = $this->input->get_post('from_month');
		$from_year = $this->input->get_post('from_year');
		
		$to_month = $this->input->get_post('to_month');
		$to_year = $this->input->get_post('to_year');
		
		if ($from_month && $from_year)
			$this->db->where("ii.inbound_date >=", date('Y-m-d', mktime(0, 0, 0, $from_month, 1, $from_year)));
		
		if ($to_month && $to_year)
			$this->db->where("ii.inbound_date <=", add_date(date('Y-m-d', mktime(0, 0, 0, $to_month, 1, $to_year)), -1, 1));
		
		$this->lib_custom->project_query_filter('oi.c_project_id', $this->c_project_ids);
		
		parent::_get_list_json();
    }
	
	function detail_get()
	{
		if (!is_authorized('material/inventory_inbound', 'index')) 
			access_denied();
		
		$result = new stdClass();
		$result->response = FALSE;
		$result->value = NULL;
		$result->data = array();
		
		$id = $this->input->get_post('id');
		$this->db
			->select("ii.id, ii.code, ii.inbound_date, ii.notes")
			->from('m_inventory_inbounds ii')
			->where('ii.id', $id);
		$table = $this->db->get();
		if ($table->num_rows() > 0)
		{
			$result->value = $table->first_row();
			$result->response = TRUE;
		}
		else
			show_error("Inbound not found", 400);
		
		$this->result_json($result);
	}
	
	function detail_list_get()
	{
		if (!is_authorized('material/inventory_inbound', 'index')) 
			access_denied();
		
		$this->db
			->select("iid.id, iid.quantity_box, iid.quantity, iid.created")
			->select("iid.barcode, iid.carton_no, iid.pallet, iid.lot_no, iid.packed_date, iid.expired_date, iid.condition")
			->select("ird.m_inventory_receive_id")
			->select("ir.code m_inventory_receive_code, ir.receive_date m_inventory_receive_date")
			->select("ir.vehicle_no m_inventory_receive_vehicle_no, ir.vehicle_driver m_inventory_receive_vehicle_driver, ir.transport_mode m_inventory_receive_transport_mode")
			->select("oi.id c_orderin_id, oi.code c_orderin_code, oi.orderin_date c_orderin_date")
			->select("oi.c_businesspartner_id, bp.name c_businesspartner_name")
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
			->join('c_projects prj', "prj.id = oi.c_project_id", 'left');
		
		$this->lib_custom->project_query_filter('oi.c_project_id', $this->c_project_ids);
		
		$id = $this->input->get_post('id');
		if ($id !== '')
			$this->db->where("iid.m_inventory_inbound_id", $id);
		
		parent::_get_list_json();
	}
	
	function detail_receive_get()
	{
		if (!is_authorized('material/inventory_inbound', 'index')) 
			access_denied();
		
		$this->db
			->select("ird.id, ird.condition, ird.quantity_box, ird.quantity")
			->select("ir.code m_inventory_receive_code, ir.receive_date m_inventory_receive_date")
			->select("ir.vehicle_no m_inventory_receive_vehicle_no, ir.vehicle_driver m_inventory_receive_vehicle_driver, ir.transport_mode m_inventory_receive_transport_mode")
			->select("oid.m_product_id, pro.code m_product_code, pro.name m_product_name, pro.uom m_product_uom")
			->select("oi.id c_orderin_id, oi.code c_orderin_code, oi.orderin_date c_orderin_date")
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
	
	function parse_barcode_get()
    {
		if (!is_authorized('material/inventory_inbound', 'index')) 
			access_denied();
		
		$result = new stdClass();
		$result->response = FALSE;
		$result->value = NULL;
		$result->data = array();
		
		$m_inventory_receivedetail_id = $this->input->get_post('m_inventory_receivedetail_id');
		$barcode = $this->input->get_post('barcode');
		
		try
		{
			$m_product_id = '';
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
			
			$this->load->library('custom/lib_custom');
			$data_parsed = $this->lib_custom->inbounddetail_parse_barcode_by_id($m_product_id, $barcode);
			
			$result->response = TRUE;
			$result->value = $data_parsed;
		}
		catch(Exception $e)
		{
			$result->value = $e->getMessage();
		}
		
		$this->result_json($result);
	}
    
	function product_properties_get()
	{
		if (!is_authorized('material/inventory_inbound', 'index')) 
			access_denied();
		
		$result = new stdClass();
		$result->response = FALSE;
		$result->value = NULL;
		$result->data = array();
		
		$product_code = $this->input->get_post('product_code');
		
		try
		{
			$result->value = "Product code is not found.";
			if ($product_code !== '')
			{
				$this->db->from('m_products');
				$this->db->where('code', $product_code);
				$table = $this->db->get();
				if ($table->num_rows() > 0)
				{
					$table_record = $table->first_row();
					$result->value = $table_record;
					$result->response = TRUE;
				}
			}
		}
		catch(Exception $e)
		{
			$result->value = $e->getMessage();
		}
		
		$this->result_json($result);
	}
	
    function insert_post()
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
	
	function update_post()
	{
		if (!is_authorized('material/inventory_inbound', 'update')) 
			access_denied();
		
		$this->load->library('material/lib_inventory_in');
		
		$id = $this->input->post('id');
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
    
    function delete_post()
    {
		if (!is_authorized('material/inventory_inbound', 'delete')) 
			access_denied();
		
		$this->load->library('material/lib_inventory_in');
		
		$id = $this->input->post('id');
		$user_id = $this->session->userdata('user_id');
		
		parent::_execute('lib_inventory_in', 'inbound_remove', array($id, $user_id));
    }
	
	function insert_detail_post()
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
		$response->m_inventory_inbounddetail_ids = $this->lib_inventory_in->inbounddetail_add($data, $user_id);
		$response->counter = $this->_get_detail_counter($m_inventory_inbound_id, $pallet);
		return $response;
	}
	
	function delete_detail_post()
	{
		if (!is_authorized('material/inventory_inbound', 'delete')) 
			access_denied();
		
		$id = $this->input->post('id');
		
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
	
	function get_detail_counter_get()
	{
		if (!is_authorized('material/inventory_inbound', 'index')) 
			access_denied();
		
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
}