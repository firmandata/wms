<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'/libraries/REST_Controller.php';

class Custom_inventory_inbound extends REST_Controller
{
	public function __construct()
	{
		parent::__construct();
	}
	
	public function list_get()
	{
		if (!is_authorized('custom/inventory_inbound', 'index')) 
			access_denied();
		
		$this->db
			->select("iid.id, iid.barcode, iid.quantity, iid.pallet, iid.carton_no, iid.packed_date, iid.expired_date, iid.lot_no, iid.condition")
			->select("iid.m_product_id, pro.code product_code, pro.name product_name, pro.uom product_uom")
			->select("iid.m_grid_id, gri.code grid_code")
			->select("iid.volume_length, iid.volume_width, iid.volume_height")
			->select("iid.created")
			->from('cus_m_inventory_inbounddetails iid')
			->join('m_products pro', "pro.id = iid.m_product_id")
			->join('m_grids gri', "gri.id = iid.m_grid_id", 'left');
		
		$from = $this->input->get_post('from');
		if ($from)
			$this->db->where("iid.created >=", $from.' 00:00:00');
		
		$to = $this->input->get_post('to');
		if ($to)
			$this->db->where("iid.created <=", $to.' 23:59:59');
		
		parent::_get_list_json();
	}
	
	public function detail_get()
	{
		if (!is_authorized('custom/inventory_inbound', 'index')) 
			access_denied();
		
		$result = new stdClass();
		$result->response = FALSE;
		$result->value = NULL;
		$result->data = array();
		
		$id = $this->input->get_post('id');
		$this->db
			->select("iid.id, iid.barcode, iid.quantity, iid.pallet, iid.carton_no, iid.packed_date, iid.expired_date, iid.lot_no, iid.condition")
			->select("iid.m_product_id, pro.code product_code, pro.name product_name, pro.uom product_uom")
			->select("iid.m_grid_id, gri.code grid_code")
			->select("iid.volume_length, iid.volume_width, iid.volume_height")
			->select("iid.created")
			->from('cus_m_inventory_inbounddetails iid')
			->join('m_products pro', "pro.id = iid.m_product_id")
			->join('m_grids gri', "gri.id = iid.m_grid_id", 'left')
			->where('iid.id', $id);
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
	
	public function parse_barcode_get()
	{
		if (!is_authorized('custom/inventory_inbound', 'index')) 
			access_denied();
		
		$result = new stdClass();
		$result->response = FALSE;
		$result->value = NULL;
		$result->data = array();
		
		$product_code = $this->input->get_post('product_code');
		$barcode = $this->input->get_post('barcode');
		
		$this->load->library('custom/lib_custom');
		$result->value = $this->lib_custom->inbounddetail_parse_barcode($product_code, $barcode);
		$result->response = TRUE;
		
		$this->result_json($result);
	}
	
	public function product_properties_get()
	{
		if (!is_authorized('custom/inventory_inbound', 'index')) 
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
				$this->db
					->select("code, name")
					->select("volume_length, volume_width, volume_height")
					->from('m_products')
					->where('code', $product_code);
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
	
	public function insert_post()
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
		$data->volume_length = $this->input->post('volume_length');
		$data->volume_width = $this->input->post('volume_width');
		$data->volume_height = $this->input->post('volume_height');
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
				array('field' => 'carton_no', 'label' => 'Carton No', 'rules' => 'required'),
				array('field' => 'volume_length', 'label' => 'Length', 'rules' => 'numeric|required'),
				array('field' => 'volume_width', 'label' => 'Width', 'rules' => 'numeric|required'),
				array('field' => 'volume_height', 'label' => 'Height', 'rules' => 'numeric|required')
			)
		);
	}
	
	public function delete_post()
	{
		if (!is_authorized('custom/inventory_inbound', 'delete')) 
			access_denied();
		
		$this->load->library('custom/lib_custom');
		
		$id = $this->input->post('id');
		$user_id = $this->session->userdata('user_id');
		
		parent::_execute('lib_custom', 'inbounddetail_remove', array($id, $user_id));
	}
}