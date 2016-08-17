<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Inventory_product extends MY_Controller 
{
	public function __construct()
	{
		parent::__construct(TRUE, 'backend');
	}
	
	public function index()
	{
		if (!is_authorized('custom/inventory_product', 'index')) 
			access_denied();
		
		$data = array(
		);
		
		$content = array(
			'title'		=> "Product",
			'content' 	=> $this->load->view('custom/inventory/product/index', $data, TRUE)
		);
		$this->_load_layout($content);
	}
	
	public function form()
	{
		if (!is_authorized('custom/inventory_product', 'index')) 
			access_denied();
		
		$form_action = $this->input->get_post('form_action');
		$id = $this->input->get_post('id');
		$record = NULL;
		if ($id !== NULL)
		{
			$table = $this->db
				->where('id', $id)
				->get('cus_m_inventory_products');
			if ($table->num_rows() > 0)
				$record = $table->first_row();
			else
				show_error("Item not found", 400);
		}
		
		$data = array(
			'form_action'	=> $form_action,
			'record'		=> $record
		);
		$this->load->view('custom/inventory/product/form', $data);
	}
	
	public function get_list_json()
	{
		if (!is_authorized('custom/inventory_product', 'index')) 
			access_denied();
		
		$this->db
			->select("pro.id, pro.sku, pro.description")
			->select("pro.barcode_length, pro.qty_start, pro.qty_end, pro.sku_start, pro.sku_end, pro.carton_start, pro.carton_end, pro.date_packed_start, pro.date_packed_end")
			->from('cus_m_inventory_products pro');
		
		parent::_get_list_json();
	}
	
	public function insert()
	{
		if (!is_authorized('custom/inventory_product', 'insert')) 
			access_denied();
		
		$this->load->library('custom/lib_custom_inventory');
		
		$user_id = $this->session->userdata('user_id');
		
		$data = new stdClass();
		$data->sku = $this->input->post('sku');
		$data->description = $this->input->post('description');
		$data->barcode_length = $this->input->post('barcode_length');
		$data->qty_start = $this->input->post('qty_start');
		$data->qty_end = $this->input->post('qty_end');
		$data->sku_start = $this->input->post('sku_start');
		$data->sku_end = $this->input->post('sku_end');
		$data->carton_start = $this->input->post('carton_start');
		$data->carton_end = $this->input->post('carton_end');
		$data->date_packed_start = $this->input->post('date_packed_start');
		$data->date_packed_end = $this->input->post('date_packed_end');
		
		parent::_execute('lib_custom_inventory', 'product_add', 
			array($data, $user_id), 
			array(
				array('field' => 'sku', 'label' => 'SKU', 'rules' => 'required'),
				array('field' => 'description', 'label' => 'Description'),
				array('field' => 'barcode_length', 'label' => 'Barcode Length', 'rules' => 'integer|required'),
				array('field' => 'qty_start', 'label' => 'Qty Start', 'rules' => 'integer|required'),
				array('field' => 'qty_end', 'label' => 'Qty End', 'rules' => 'integer|required'),
				array('field' => 'sku_start', 'label' => 'SKU Start', 'rules' => 'integer|required'),
				array('field' => 'sku_end', 'label' => 'SKU End', 'rules' => 'integer|required'),
				array('field' => 'carton_start', 'label' => 'Carton Start', 'rules' => 'integer|required'),
				array('field' => 'carton_end', 'label' => 'Carton End', 'rules' => 'integer|required'),
				array('field' => 'date_packed_start', 'label' => 'Date Packed Start', 'rules' => 'integer|required'),
				array('field' => 'date_packed_end', 'label' => 'Date Packed End', 'rules' => 'integer|required')
			)
		);
	}
	
	public function update($id)
	{
		if (!is_authorized('custom/inventory_product', 'update')) 
			access_denied();
		
		$this->load->library('custom/lib_custom_inventory');
		
		$user_id = $this->session->userdata('user_id');
		
		$data = new stdClass();
		$data->sku = $this->input->post('sku');
		$data->description = $this->input->post('description');
		$data->barcode_length = $this->input->post('barcode_length');
		$data->qty_start = $this->input->post('qty_start');
		$data->qty_end = $this->input->post('qty_end');
		$data->sku_start = $this->input->post('sku_start');
		$data->sku_end = $this->input->post('sku_end');
		$data->carton_start = $this->input->post('carton_start');
		$data->carton_end = $this->input->post('carton_end');
		$data->date_packed_start = $this->input->post('date_packed_start');
		$data->date_packed_end = $this->input->post('date_packed_end');
		
		parent::_execute('lib_custom_inventory', 'product_update', 
			array($id, $data, $user_id), 
			array(
				array('field' => 'sku', 'label' => 'SKU', 'rules' => 'required'),
				array('field' => 'description', 'label' => 'Description'),
				array('field' => 'barcode_length', 'label' => 'Barcode Length', 'rules' => 'integer|required'),
				array('field' => 'qty_start', 'label' => 'Qty Start', 'rules' => 'integer|required'),
				array('field' => 'qty_end', 'label' => 'Qty End', 'rules' => 'integer|required'),
				array('field' => 'sku_start', 'label' => 'SKU Start', 'rules' => 'integer|required'),
				array('field' => 'sku_end', 'label' => 'SKU End', 'rules' => 'integer|required'),
				array('field' => 'carton_start', 'label' => 'Carton Start', 'rules' => 'integer|required'),
				array('field' => 'carton_end', 'label' => 'Carton End', 'rules' => 'integer|required'),
				array('field' => 'date_packed_start', 'label' => 'Date Packed Start', 'rules' => 'integer|required'),
				array('field' => 'date_packed_end', 'label' => 'Date Packed End', 'rules' => 'integer|required')
			)
		);
	}
	
	public function delete($id)
	{
		if (!is_authorized('custom/inventory_product', 'delete')) 
			access_denied();
		
		$this->load->library('custom/lib_custom_inventory');
		
		$user_id = $this->session->userdata('user_id');
		
		parent::_execute('lib_custom_inventory', 'product_remove', array($id, $user_id));
	}
}