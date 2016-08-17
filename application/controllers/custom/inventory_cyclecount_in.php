<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Inventory_cyclecount_in extends MY_Controller 
{
	public function __construct()
	{
		parent::__construct(TRUE, 'backend');
	}
	
	public function index()
	{
		if (!is_authorized('custom/inventory_cyclecount_in', 'index')) 
			access_denied();
		
		$month = date('n');
		$year = date('Y');
		$day_num = cal_days_in_month(CAL_GREGORIAN, $month, $year);
		
		$data = array(
			'from_date'		=> date($this->config->item('server_display_date_format'), mktime(0, 0, 0, $month, date('j'), $year)),
			'to_date'		=> date($this->config->item('server_display_date_format'), mktime(0, 0, 0, $month, date('j'), $year))
		);
		
		$content = array(
			'title'		=> "Cycle Count In",
			'content' 	=> $this->load->view('custom/inventory/cyclecount_in/index', $data, TRUE)
		);
		$this->_load_layout($content);
	}
	
	public function get_list_json()
	{
		if (!is_authorized('custom/inventory_cyclecount_in', 'index')) 
			access_denied();
		
		$this->db
			->select("cc.id, cc.barcode, cc.quantity, cc.pallet, cc.carton_no, cc.date_packed")
			->select("cc.cus_m_inventory_product_id, pro.sku, pro.description")
			->select("cc.updated")
			->from('cus_m_inventory_cyclecounts cc')
			->join('cus_m_inventory_products pro', "pro.id = cc.cus_m_inventory_product_id");
		
		$from = $this->input->get_post('from');
		if ($from !== '')
			$this->db->where("cc.updated >=", $from.' 00:00:00');
		
		$to = $this->input->get_post('to');
		if ($to !== '')
			$this->db->where("cc.updated <=", $to.' 23:59:59');
		
		$this->db->where('cc.status', 1);
		
		parent::_get_list_json();
	}
	
	public function get_product_autocomplete_list_json()
	{
		if (!is_authorized('custom/inventory_cyclecount_in', 'index')) 
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
		$response->pallet = NULL;
		
		$sku = $this->input->get_post('sku');
		$barcode = $this->input->get_post('barcode');
		
		if ($sku !== '' && $barcode !== '')
		{
			$this->db
				->select('cc.*')
				->from('cus_m_inventory_cyclecounts cc')
				->join('cus_m_inventory_products pro', "pro.id = cc.cus_m_inventory_product_id")
				->where('cc.barcode', $barcode)
				->where('pro.sku', $sku);
			$table = $this->db->get();
			if ($table->num_rows() > 0)
			{
				$cus_m_inventory_cyclecount_record = $table->first_row();
				
				$response->quantity = $cus_m_inventory_cyclecount_record->quantity;
				$response->carton_no = $cus_m_inventory_cyclecount_record->carton_no;
				$date_value = date_create_from_format('Y-m-d', $cus_m_inventory_cyclecount_record->date_packed);
				if ($date_value !== FALSE)
					$response->date_packed = date_format($date_value, 'ymd');
				$response->pallet = $cus_m_inventory_cyclecount_record->pallet;
			}
		}
		
		$this->result_json($response);
	}
	
	public function insert()
	{
		if (!is_authorized('custom/inventory_cyclecount_in', 'insert')) 
			access_denied();
		
		$this->load->library('custom/lib_custom_inventory');
		
		$user_id = $this->session->userdata('user_id');
		
		$sku = $this->input->post('sku');
		$barcode = $this->input->post('barcode');
		
		$data = new stdClass();
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
		
		parent::_execute('lib_custom_inventory', 'cyclecount_update_by_sku_barcode', 
			array($sku, $barcode, $data, $user_id), 
			array(
				array('field' => 'barcode', 'label' => 'Barcode', 'rules' => 'required'),
				array('field' => 'quantity', 'label' => 'Quantity', 'rules' => 'numeric|required')
			)
		);
	}
	
	public function delete($id)
	{
		if (!is_authorized('custom/inventory_cyclecount_in', 'delete')) 
			access_denied();
		
		$this->load->library('custom/lib_custom_inventory');
		
		$user_id = $this->session->userdata('user_id');
		
		parent::_execute('lib_custom_inventory', 'cyclecount_remove', array($id, $user_id));
	}
	
	public function get_excel()
	{
		if (!is_authorized('custom/inventory_cyclecount_in', 'index')) 
			access_denied();
		
		$this->db
			->select("cc.barcode, cc.quantity, cc.pallet, cc.carton_no, cc.date_packed")
			->select("pro.sku, pro.description")
			->select("cc.updated")
			->from('cus_m_inventory_cyclecounts cc')
			->join('cus_m_inventory_products pro', "pro.id = cc.cus_m_inventory_product_id");
		
		$from = $this->input->get_post('from');
		if ($from !== '')
			$this->db->where("cc.updated >=", $from.' 00:00:00');
		
		$to = $this->input->get_post('to');
		if ($to !== '')
			$this->db->where("cc.updated <=", $to.' 23:59:59');
		
		$this->db->where('cc.status', 1);
		
		$this->load->library('jqgrid');
		
		$header_captions = array(
			'barcode' 		=> 'Barcode',
			'sku' 			=> 'SKU',
			'description' 	=> 'Description',
			'quantity' 		=> 'Quantity',
			'pallet' 		=> 'Pallet',
			'carton_no' 	=> 'Carton No',
			'date_packed' 	=> 'Date Packed',
			'updated' 		=> 'Scan Date'
		);
		
		$data = $this->jqgrid->result();
		
		$result = $data->data;
		
		$this->load->library('excel');
		$this->excel->write_stream_acos(
			array(
				'CycleCountIn' => $result
			), 
			'CycleCount_In.xls',
			array(
				'CycleCountIn' => $header_captions
			)
		);
	}
}