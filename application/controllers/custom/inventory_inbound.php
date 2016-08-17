<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Inventory_inbound extends MY_Controller 
{
	public function __construct()
	{
		parent::__construct(TRUE, 'backend');
	}
	
	public function index()
	{
		if (!is_authorized('custom/inventory_inbound', 'index')) 
			access_denied();
		
		$month = date('n');
		$year = date('Y');
		$day_num = cal_days_in_month(CAL_GREGORIAN, $month, $year);
		
		$data = array(
			'from_date'		=> date($this->config->item('server_display_date_format'), mktime(0, 0, 0, $month, date('j'), $year)),
			'to_date'		=> date($this->config->item('server_display_date_format'), mktime(0, 0, 0, $month, date('j'), $year))
		);
		
		$content = array(
			'title'		=> "Live Inbound",
			'content' 	=> $this->load->view('custom/inventory/inbound/index', $data, TRUE)
		);
		$this->_load_layout($content);
	}
	
	public function form()
	{
		if (!is_authorized('custom/inventory_inbound', 'index')) 
			access_denied();
		
		$form_action = $this->input->get_post('form_action');
		$id = $this->input->get_post('id');
		$record = NULL;
		if ($id !== NULL)
		{
			$table = $this->db
				->where('id', $id)
				->get('cus_m_inventory_inbounddetails');
			if ($table->num_rows() > 0)
				$record = $table->first_row();
			else
				show_error("Live inbound not found", 400);
		}
		
		$data = array(
			'form_action'	=> $form_action,
			'record'		=> $record
		);
		$this->load->view('custom/inventory/inbound/form', $data);
	}
	
	public function get_list_json()
	{
		if (!is_authorized('custom/inventory_inbound', 'index')) 
			access_denied();
		
		$this->db
			->select("iid.id, iid.barcode, iid.quantity, iid.pallet, iid.carton_no, iid.packed_date, iid.expired_date, iid.lot_no, iid.condition")
			->select("iid.m_product_id, pro.code product_code, pro.name product_name, pro.uom product_uom")
			->select("iid.m_grid_id, gri.code grid_code")
			->select("iid.created")
			->from('cus_m_inventory_inbounddetails iid')
			->join('m_products pro', "pro.id = iid.m_product_id")
			->join('m_grids gri', "gri.id = iid.m_grid_id", 'left');
		
		$from = $this->input->get_post('from');
		if ($from !== '')
			$this->db->where("iid.created >=", $from.' 00:00:00');
		
		$to = $this->input->get_post('to');
		if ($to !== '')
			$this->db->where("iid.created <=", $to.' 23:59:59');
		
		parent::_get_list_json();
	}
	
	public function get_product_autocomplete_list_json()
	{
		if (!is_authorized('custom/inventory_inbound', 'index')) 
			access_denied();
		
		$keywords = $this->input->get_post('term');
		
		$this->db
			->select("id, code value, code label")
			->from('m_products');
		
		if ($keywords)
			$this->db->where("code LIKE '%".$this->db->escape_like_str($keywords)."%'", NULL, FALSE);
		
		parent::_get_list_autocomplete_json();
	}
	
	public function get_grid_autocomplete_list_json()
	{
		if (!is_authorized('custom/inventory_inbound', 'index')) 
			access_denied();
		
		$keywords = $this->input->get_post('term');
		
		$this->db
			->select("id, code value, code label")
			->from('m_grids');
		
		if ($keywords)
			$this->db->where("code LIKE '%".$this->db->escape_like_str($keywords)."%'", NULL, FALSE);
		
		parent::_get_list_autocomplete_json();
	}
	
	public function parse_barcode()
	{
		if (!is_authorized('custom/inventory_inbound', 'index')) 
			access_denied();
		
		$product_code = $this->input->get_post('product_code');
		$barcode = $this->input->get_post('barcode');
		
		$this->load->library('custom/lib_custom');
		$response = $this->lib_custom->inbounddetail_parse_barcode($product_code, $barcode);
		if ($response->packed_date !== NULL)
		{
			$date_value = date_create_from_format('Y-m-d', $response->packed_date);
			if ($date_value !== FALSE)
				$response->packed_date = date_format($date_value, 'ymd');
		}
		
		$this->result_json($response);
	}
	
	public function insert()
	{
		if (!is_authorized('custom/inventory_inbound', 'insert')) 
			access_denied();
		
		$this->load->library('custom/lib_custom');
		
		$user_id = $this->session->userdata('user_id');
		
		$product_code = $this->input->post('product_code');
		$grid_code = $this->input->post('grid_code');
		if ($grid_code === '')
			$grid_code = NULL;
		
		$data = new stdClass();
		$data->barcode = $this->input->post('barcode');
		$data->pallet = $this->input->post('pallet');
		$data->quantity = $this->input->post('quantity');
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
		
		parent::_execute('lib_custom', 'inbounddetail_add_by_value', 
			array($product_code, $grid_code, $data, $user_id), 
			array(
				array('field' => 'product_code', 'label' => 'Product Code', 'rules' => 'required'),
				array('field' => 'barcode', 'label' => 'Barcode', 'rules' => 'required'),
				array('field' => 'quantity', 'label' => 'Quantity', 'rules' => 'numeric|required'),
				array('field' => 'pallet', 'label' => 'Pallet', 'rules' => 'required'),
				array('field' => 'carton_no', 'label' => 'Carton No', 'rules' => 'required')
			)
		);
	}
	
	public function delete($id)
	{
		if (!is_authorized('custom/inventory_inbound', 'delete')) 
			access_denied();
		
		$this->load->library('custom/lib_custom');
		
		$user_id = $this->session->userdata('user_id');
		
		parent::_execute('lib_custom', 'inbounddetail_remove', array($id, $user_id));
	}
	
	public function get_excel()
	{
		if (!is_authorized('custom/inventory_inbound', 'index')) 
			access_denied();
		
		$this->db
			->select("iid.barcode, iid.quantity, iid.pallet, iid.carton_no, iid.packed_date, iid.expired_date, iid.lot_no, iid.condition")
			->select("pro.code product_code, pro.name product_name, pro.uom product_uom")
			->select("gri.code grid_code")
			->select("iid.created")
			->from('cus_m_inventory_inbounddetails iid')
			->join('m_products pro', "pro.id = iid.m_product_id")
			->join('m_grids gri', "gri.id = iid.m_grid_id", 'left');
		
		$from = $this->input->get_post('from');
		if ($from !== '')
			$this->db->where("iid.created >=", $from.' 00:00:00');
		
		$to = $this->input->get_post('to');
		if ($to !== '')
			$this->db->where("iid.created <=", $to.' 23:59:59');
		
		$this->load->library('jqgrid');
		
		$header_captions = array(
			'barcode' 		=> 'Barcode',
			'product_code' 	=> 'Code',
			'product_name' 	=> 'Name',
			'quantity' 		=> 'Quantity',
			'product_uom' 	=> 'UOM',
			'pallet' 		=> 'Pallet',
			'carton_no' 	=> 'Carton No',
			'packed_date' 	=> 'Packed Date',
			'expired_date' 	=> 'Expired Date',
			'grid_code' 	=> 'Grid',
			'lot_no' 		=> 'Lot No',
			'condition' 	=> 'Condition',
			'created' 		=> 'Scan Date'
		);
		
		$data = $this->jqgrid->result();
		
		$result = $data->data;
		
		$this->load->library('excel');
		$this->excel->write_stream_acos(
			array(
				'Inbound' => $result
			), 
			'LiveInbound.xls',
			array(
				'Inbound' => $header_captions
			)
		);
	}
}