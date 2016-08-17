<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Product extends MY_Controller 
{
	public function __construct()
	{
		parent::__construct(TRUE, 'backend');
	}
	
	public function index()
	{
		if (!is_authorized('material/product', 'index')) 
			access_denied();
		
		$data = array(
		);
		
		$content = array(
			'title'		=> "Product",
			'content' 	=> $this->load->view('material/product/index', $data, TRUE)
		);
		$this->_load_layout($content);
	}
	
	public function form()
	{
		if (!is_authorized('material/product', 'index')) 
			access_denied();
		
		$form_action = $this->input->get_post('form_action');
		$id = $this->input->get_post('id');
		$record = NULL;
		if ($id !== NULL)
		{
			$table = $this->db
				->select("pro.id, pro.code, pro.name")
				->select("pro.uom, pro.pack, pro.origin, pro.netto, pro.minimum_stock")
				->select("pro.m_productgroup_id, prog.code m_productgroup_code, prog.name m_productgroup_name")
				->select("pro.brand, pro.type")
				->select("pro.volume_length, pro.volume_width, pro.volume_height")
				->select("cpro.barcode_length c_barcode_length")
				->select("cpro.quantity_start c_quantity_start, cpro.quantity_end c_quantity_end")
				->select("cpro.quantity_point_start c_quantity_point_start, cpro.quantity_point_end c_quantity_point_end")
				->select("cpro.quantity_divider c_quantity_divider")
				->select("cpro.sku_start c_sku_start, cpro.sku_end c_sku_end")
				->select("cpro.carton_start c_carton_start, cpro.carton_end c_carton_end")
				->select("cpro.packed_date_start c_packed_date_start, cpro.packed_date_end c_packed_date_end")
				->select_concat(array("prog.name", "' ('", "prog.code", "')'"), 'm_productgroup_text')
				->from('m_products pro')
				->join('cus_m_products cpro', "cpro.m_product_id = pro.id", 'left')
				->join('m_productgroups prog', "prog.id = pro.m_productgroup_id", 'left')
				->where('pro.id', $id)
				->get();
			if ($table->num_rows() > 0)
				$record = $table->first_row();
			else
				show_error("Product not found", 400);
		}
		
		$data = array(
			'form_action'	=> $form_action,
			'record'		=> $record
		);
		$this->load->view('material/product/form', $data);
	}
	
	public function get_list_json()
	{
		if (!is_authorized('material/product', 'index')) 
			access_denied();
		
		$this->db
			->select("pro.id, pro.code, pro.name")
			->select("pro.uom, pro.pack, pro.origin, pro.netto, pro.minimum_stock")
			->select("pro.brand, pro.type")
			->select("pro.volume_length, pro.volume_width, pro.volume_height")
			->select("prog.code m_productgroup_code, prog.name m_productgroup_name")
			->select("cpro.barcode_length c_barcode_length")
			->select("cpro.quantity_start c_quantity_start, cpro.quantity_end c_quantity_end")
			->select("cpro.quantity_point_start c_quantity_point_start, cpro.quantity_point_end c_quantity_point_end")
			->select("cpro.quantity_divider c_quantity_divider")
			->select("cpro.sku_start c_sku_start, cpro.sku_end c_sku_end")
			->select("cpro.carton_start c_carton_start, cpro.carton_end c_carton_end")
			->select("cpro.packed_date_start c_packed_date_start, cpro.packed_date_end c_packed_date_end")
			->from('m_products pro')
			->join('cus_m_products cpro', "cpro.m_product_id = pro.id", 'left')
			->join('m_productgroups prog', "prog.id = pro.m_productgroup_id", 'left');
		
		parent::_get_list_json();
	}
	
	public function form_productgroup()
	{
		if (!is_authorized('material/product', 'update')) 
			access_denied();
		
		$ids = $this->input->get_post('ids');
		
		$data = array(
			'ids'	=> $ids
		);
		
		$this->load->view('material/product/form_productgroup', $data);
	}
	
	public function get_list_productgroup_json()
	{
		if (!is_authorized('material/product', 'index')) 
			access_denied();
		
		$this->db
			->select("id, code, name")
			->from('m_productgroups');
		
		parent::_get_list_json();
	}
	
	public function get_productgroup_autocomplete_list_json()
	{
		if (!is_authorized('material/product', 'index')) 
			access_denied();
		
		$keywords = $this->input->get_post('term');
		
		$this->db
			->select("id")
			->select_concat(array("name", "' ('", "code", "')'"), 'value')
			->select_concat(array("name", "' ('", "code", "')'"), 'label')
			->from('m_productgroups');
		
		if ($keywords)
			$this->db->where($this->db->concat(array("name", "' ('", "code", "')'")) . " LIKE '%" . $this->db->escape_like_str($keywords) . "%'", NULL, FALSE);
		
		parent::_get_list_autocomplete_json();
	}
	
	public function insert()
	{
		if (!is_authorized('material/product', 'insert')) 
			access_denied();
		
		$this->load->library('material/lib_material');
		
		$user_id = $this->session->userdata('user_id');
		
		$data = new stdClass();
		$data->code = $this->input->post('code');
		$data->name = $this->input->post('name');
		$data->uom = $this->input->post('uom');
		$data->pack = $this->input->post('pack');
		$data->origin = $this->input->post('origin');
		$data->type = $this->input->post('type');
		$data->brand = $this->input->post('brand');
		$data->netto = $this->input->post('netto');
		$data->minimum_stock = $this->input->post('minimum_stock');
		$data->volume_length = $this->input->post('volume_length');
		$data->volume_width = $this->input->post('volume_width');
		$data->volume_height = $this->input->post('volume_height');
		$data->barcode_length = $this->input->post('barcode_length');
		$data->quantity_start = $this->input->post('quantity_start');
		$data->quantity_end = $this->input->post('quantity_end');
		$data->quantity_point_start = $this->input->post('quantity_point_start');
		$data->quantity_point_end = $this->input->post('quantity_point_end');
		$data->quantity_divider = $this->input->post('quantity_divider');
		$data->sku_start = $this->input->post('sku_start');
		$data->sku_end = $this->input->post('sku_end');
		$data->carton_start = $this->input->post('carton_start');
		$data->carton_end = $this->input->post('carton_end');
		$data->packed_date_start = $this->input->post('packed_date_start');
		$data->packed_date_end = $this->input->post('packed_date_end');

		$m_productgroup_id = $this->input->post('m_productgroup_id');
		
		parent::_execute('lib_material', 'product_add', 
			array($data, $m_productgroup_id, $user_id), 
			array(
				array('field' => 'code', 'label' => 'Code', 'rules' => 'required'),
				array('field' => 'name', 'label' => 'Name', 'rules' => 'required'),
				array('field' => 'pack', 'label' => 'Pack', 'rules' => 'integer|required'),
				array('field' => 'netto', 'label' => 'Netto', 'rules' => 'numeric|required'),
				array('field' => 'minimum_stock', 'label' => 'Minimum Stock', 'rules' => 'numeric|required'),
				array('field' => 'volume_length', 'label' => 'Volume Length', 'rules' => 'numeric'),
				array('field' => 'volume_width', 'label' => 'Volume Width', 'rules' => 'numeric'),
				array('field' => 'volume_height', 'label' => 'Volume Height', 'rules' => 'numeric'),
				array('field' => 'barcode_length', 'label' => 'Barcode Length', 'rules' => 'integer|required'),
				array('field' => 'quantity_start', 'label' => 'Quantity Start', 'rules' => 'integer|required'),
				array('field' => 'quantity_end', 'label' => 'Quantity End', 'rules' => 'integer|required'),
				array('field' => 'quantity_point_start', 'label' => 'Quantity Point Start', 'rules' => 'integer|required'),
				array('field' => 'quantity_point_end', 'label' => 'Quantity Point End', 'rules' => 'integer|required'),
				array('field' => 'quantity_divider', 'label' => 'Quantity Divider', 'rules' => 'numeric|required'),
				array('field' => 'sku_start', 'label' => 'SKU Start', 'rules' => 'integer|required'),
				array('field' => 'sku_end', 'label' => 'SKU End', 'rules' => 'integer|required'),
				array('field' => 'carton_start', 'label' => 'Carton Start', 'rules' => 'integer|required'),
				array('field' => 'carton_end', 'label' => 'Carton End', 'rules' => 'integer|required'),
				array('field' => 'packed_date_start', 'label' => 'Packed Date Start', 'rules' => 'integer|required'),
				array('field' => 'packed_date_end', 'label' => 'Packed Date End', 'rules' => 'integer|required')
			)
		);
	}
	
	public function update($id)
	{
		if (!is_authorized('material/product', 'update')) 
			access_denied();
		
		$this->load->library('material/lib_material');
		
		$user_id = $this->session->userdata('user_id');
		
		$data = new stdClass();
		$data->code = $this->input->post('code');
		$data->name = $this->input->post('name');
		$data->uom = $this->input->post('uom');
		$data->pack = $this->input->post('pack');
		$data->origin = $this->input->post('origin');
		$data->type = $this->input->post('type');
		$data->brand = $this->input->post('brand');
		$data->netto = $this->input->post('netto');
		$data->minimum_stock = $this->input->post('minimum_stock');
		$data->volume_length = $this->input->post('volume_length');
		$data->volume_width = $this->input->post('volume_width');
		$data->volume_height = $this->input->post('volume_height');
		$data->barcode_length = $this->input->post('barcode_length');
		$data->quantity_start = $this->input->post('quantity_start');
		$data->quantity_end = $this->input->post('quantity_end');
		$data->quantity_point_start = $this->input->post('quantity_point_start');
		$data->quantity_point_end = $this->input->post('quantity_point_end');
		$data->quantity_divider = $this->input->post('quantity_divider');
		$data->sku_start = $this->input->post('sku_start');
		$data->sku_end = $this->input->post('sku_end');
		$data->carton_start = $this->input->post('carton_start');
		$data->carton_end = $this->input->post('carton_end');
		$data->packed_date_start = $this->input->post('packed_date_start');
		$data->packed_date_end = $this->input->post('packed_date_end');
		
		$m_productgroup_id = $this->input->post('m_productgroup_id');
		
		parent::_execute('lib_material', 'product_update', 
			array($id, $data, $m_productgroup_id, $user_id), 
			array(
				array('field' => 'code', 'label' => 'Code', 'rules' => 'required'),
				array('field' => 'name', 'label' => 'Name', 'rules' => 'required'),
				array('field' => 'pack', 'label' => 'Pack', 'rules' => 'integer|required'),
				array('field' => 'netto', 'label' => 'Netto', 'rules' => 'numeric|required'),
				array('field' => 'minimum_stock', 'label' => 'Minimum Stock', 'rules' => 'numeric|required'),
				array('field' => 'volume_length', 'label' => 'Volume Length', 'rules' => 'numeric'),
				array('field' => 'volume_width', 'label' => 'Volume Width', 'rules' => 'numeric'),
				array('field' => 'volume_height', 'label' => 'Volume Height', 'rules' => 'numeric'),
				array('field' => 'barcode_length', 'label' => 'Barcode Length', 'rules' => 'integer|required'),
				array('field' => 'quantity_start', 'label' => 'Quantity Start', 'rules' => 'integer|required'),
				array('field' => 'quantity_end', 'label' => 'Quantity End', 'rules' => 'integer|required'),
				array('field' => 'quantity_point_start', 'label' => 'Quantity Point Start', 'rules' => 'integer|required'),
				array('field' => 'quantity_point_end', 'label' => 'Quantity Point End', 'rules' => 'integer|required'),
				array('field' => 'quantity_divider', 'label' => 'Quantity Divider', 'rules' => 'numeric|required'),
				array('field' => 'sku_start', 'label' => 'SKU Start', 'rules' => 'integer|required'),
				array('field' => 'sku_end', 'label' => 'SKU End', 'rules' => 'integer|required'),
				array('field' => 'carton_start', 'label' => 'Carton Start', 'rules' => 'integer|required'),
				array('field' => 'carton_end', 'label' => 'Carton End', 'rules' => 'integer|required'),
				array('field' => 'packed_date_start', 'label' => 'Packed Date Start', 'rules' => 'integer|required'),
				array('field' => 'packed_date_end', 'label' => 'Packed Date End', 'rules' => 'integer|required')
			)
		);
	}
	
	public function set_productgroup_by_ids()
	{
		if (!is_authorized('material/product', 'update')) 
			access_denied();
		
		parent::_execute('this', '_set_productgroup_by_ids');
	}
	
	protected function _set_productgroup_by_ids()
	{
		$user_id = $this->session->userdata('user_id');
		
		$ids = $this->input->get_post('ids');
		$m_productgroup_id = $this->input->get_post('m_productgroup_id');
		if (!empty($ids) && is_array($ids))
		{
			$this->load->library('material/lib_material');
			
			foreach ($ids as $id)
			{
				$this->lib_material->product_set_productgroup($id, $m_productgroup_id, $user_id);
			}
		}
	}
	
	public function delete_by_ids()
	{
		if (!is_authorized('material/product', 'delete')) 
			access_denied();
		
		parent::_execute('this', '_delete_by_ids');
	}
	
	protected function _delete_by_ids()
	{
		$user_id = $this->session->userdata('user_id');
		
		$ids = $this->input->post('ids');
		if (!empty($ids) && is_array($ids))
		{
			$this->load->library('material/lib_material');
			
			foreach ($ids as $id)
			{
				$this->lib_material->product_remove($id, $user_id);
			}
		}
	}
	
	public function delete($id)
	{
		if (!is_authorized('material/product', 'delete')) 
			access_denied();
		
		$this->load->library('material/lib_material');
		
		$user_id = $this->session->userdata('user_id');
		
		parent::_execute('lib_material', 'product_remove', array($id, $user_id));
	}
}