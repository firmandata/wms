<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Inventory_cyclecount_out extends MY_Controller 
{
	public function __construct()
	{
		parent::__construct(TRUE, 'backend');
	}
	
	public function index()
	{
		if (!is_authorized('custom/inventory_cyclecount_out', 'index')) 
			access_denied();
		
		$month = date('n');
		$year = date('Y');
		$day_num = cal_days_in_month(CAL_GREGORIAN, $month, $year);
		
		$data = array(
			'from_date'		=> date($this->config->item('server_display_date_format'), mktime(0, 0, 0, $month, date('j'), $year)),
			'to_date'		=> date($this->config->item('server_display_date_format'), mktime(0, 0, 0, $month, date('j'), $year))
		);
		
		$content = array(
			'title'		=> "Cycle Count Out",
			'content' 	=> $this->load->view('custom/inventory/cyclecount_out/index', $data, TRUE)
		);
		$this->_load_layout($content);
	}
	
	public function form()
	{
		if (!is_authorized('custom/inventory_cyclecount_out', 'index')) 
			access_denied();
		
		$form_action = $this->input->get_post('form_action');
		$id = $this->input->get_post('id');
		$record = NULL;
		if ($id !== NULL)
		{
			$table = $this->db
				->where('id', $id)
				->get('cus_m_inventory_cyclecounts');
			if ($table->num_rows() > 0)
				$record = $table->first_row();
			else
				show_error("Cycle Count Out not found", 400);
		}
		
		$data = array(
			'form_action'	=> $form_action,
			'record'		=> $record
		);
		$this->load->view('custom/inventory/cyclecount_out/form', $data);
	}
	
	public function get_list_json()
	{
		if (!is_authorized('custom/inventory_cyclecount_out', 'index')) 
			access_denied();
		
		$this->db
			->select("cc.id, cc.barcode, cc.quantity, cc.pallet, cc.carton_no, cc.date_packed")
			->select("cc.cus_m_inventory_product_id, pro.sku, pro.description")
			->select("cc.created")
			->from('cus_m_inventory_cyclecounts cc')
			->join('cus_m_inventory_products pro', "pro.id = cc.cus_m_inventory_product_id");
		
		$from = $this->input->get_post('from');
		if ($from !== '')
			$this->db->where("cc.created >=", $from.' 00:00:00');
		
		$to = $this->input->get_post('to');
		if ($to !== '')
			$this->db->where("cc.created <=", $to.' 23:59:59');
		
		parent::_get_list_json();
	}
	
	public function get_product_autocomplete_list_json()
	{
		if (!is_authorized('custom/inventory_cyclecount_out', 'index')) 
			access_denied();
		
		$keywords = $this->input->get_post('term');
		
		$this->db
			->select("id, sku value, sku label")
			->from('cus_m_inventory_products');
		
		if ($keywords)
			$this->db->where("sku LIKE '%".$this->db->escape_like_str($keywords)."%'", NULL, FALSE);
		
		parent::_get_list_autocomplete_json();
	}
	
	public function parse_barcode()
	{
		$response = new stdClass();
		$response->quantity = NULL;
		$response->carton_no = NULL;
		$response->date_packed = NULL;
		
		$sku = $this->input->get_post('sku');
		$barcode = $this->input->get_post('barcode');
		
		if ($sku !== '' && $barcode !== '')
		{
			$this->db
				->from('cus_m_inventory_products')
				->where('sku', $sku);
			$table = $this->db->get();
			if ($table->num_rows() > 0)
			{
				$cus_m_inventory_product_record = $table->first_row();
				
				if ($cus_m_inventory_product_record->qty_start > 0 && $cus_m_inventory_product_record->qty_end > 0)
					$response->quantity = substr($barcode, $cus_m_inventory_product_record->qty_start - 1, $cus_m_inventory_product_record->qty_end - $cus_m_inventory_product_record->qty_start + 1);
				if ($cus_m_inventory_product_record->carton_start > 0 && $cus_m_inventory_product_record->carton_end > 0)
					$response->carton_no = substr($barcode, $cus_m_inventory_product_record->carton_start - 1, $cus_m_inventory_product_record->carton_end - $cus_m_inventory_product_record->carton_start + 1);
				if ($cus_m_inventory_product_record->date_packed_start > 0 && $cus_m_inventory_product_record->date_packed_end > 0)
					$response->date_packed = substr($barcode, $cus_m_inventory_product_record->date_packed_start - 1, $cus_m_inventory_product_record->date_packed_end - $cus_m_inventory_product_record->date_packed_start + 1);
			}
		}
		
		$this->result_json($response);
	}
	
	public function insert()
	{
		if (!is_authorized('custom/inventory_cyclecount_out', 'insert')) 
			access_denied();
		
		$this->load->library('custom/lib_custom_inventory');
		
		$user_id = $this->session->userdata('user_id');
		
		$sku = $this->input->post('sku');
		
		$data = new stdClass();
		$data->barcode = $this->input->post('barcode');
		$data->quantity = $this->input->post('quantity');
		$data->pallet = $this->input->post('pallet');
		$data->carton_no = $this->input->post('carton_no');
		if ($this->input->post('date_packed') !== NULL)
		{
			$date_packed = $this->input->post('date_packed');
			if (!empty($date_packed))
			{
				$date_value = date_create_from_format('ymd', $date_packed);
				if ($date_value !== FALSE)
					$data->date_packed = date_format($date_value, 'Y-m-d');
				else
					$data->date_packed = $date_packed;
			}
		}
		
		parent::_execute('lib_custom_inventory', 'cyclecount_add_by_sku', 
			array($sku, $data, $user_id), 
			array(
				array('field' => 'barcode', 'label' => 'Barcode', 'rules' => 'required'),
				array('field' => 'quantity', 'label' => 'Quantity', 'rules' => 'numeric|required')
			)
		);
	}
	
	public function delete($id)
	{
		if (!is_authorized('custom/inventory_cyclecount_out', 'delete')) 
			access_denied();
		
		$this->load->library('custom/lib_custom_inventory');
		
		$user_id = $this->session->userdata('user_id');
		
		parent::_execute('lib_custom_inventory', 'cyclecount_remove', array($id, $user_id));
	}
	
	public function get_excel()
	{
		if (!is_authorized('custom/inventory_cyclecount_out', 'index')) 
			access_denied();
		
		$this->db
			->select("cc.barcode, cc.quantity, cc.pallet, cc.carton_no, cc.date_packed")
			->select("pro.sku, pro.description")
			->select("cc.created")
			->from('cus_m_inventory_cyclecounts cc')
			->join('cus_m_inventory_products pro', "pro.id = cc.cus_m_inventory_product_id");
		
		$from = $this->input->get_post('from');
		if ($from !== '')
			$this->db->where("cc.created >=", $from.' 00:00:00');
		
		$to = $this->input->get_post('to');
		if ($to !== '')
			$this->db->where("cc.created <=", $to.' 23:59:59');
		
		$this->load->library('jqgrid');
		
		$header_captions = array(
			'barcode' 		=> 'Barcode',
			'sku' 			=> 'SKU',
			'description' 	=> 'Description',
			'quantity' 		=> 'Quantity',
			'pallet' 		=> 'Pallet',
			'carton_no' 	=> 'Carton No',
			'date_packed' 	=> 'Date Packed',
			'created' 		=> 'Scan Date'
		);
		
		$data = $this->jqgrid->result();
		
		$result = $data->data;
		
		$this->load->library('excel');
		$this->excel->write_stream_acos(
			array(
				'CycleCountOut' => $result
			), 
			'CycleCount_Out.xls',
			array(
				'CycleCountOut' => $header_captions
			)
		);
	}
}