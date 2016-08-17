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
				->select("pro.uom, pro.casing, pro.pack, pro.origin, pro.netto, pro.minimum_stock")
				->select("pro.m_productgroup_id, prog.code m_productgroup_code, prog.name m_productgroup_name")
				->select("pro.brand, pro.type")
				->select("pro.price")
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
		
		if ($record !== NULL)
		{
			$table = $this->db
				->where('m_product_id', $record->id)
				->get('m_product_categories');
			$record->m_product_categories = $table->result();
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
			->select("pro.uom, pro.casing, pro.pack, pro.origin, pro.netto, pro.minimum_stock")
			->select("pro.brand, pro.type")
			->select("pro.price")
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
	
	public function get_category_list_json()
	{
		if (!is_authorized('material/product', 'index')) 
			access_denied();
		
		$this->db->from('m_categories');
		
		parent::_get_list_json();
	}
	
	public function insert()
	{
		if (!is_authorized('material/product', 'insert')) 
			access_denied();
		
		parent::_execute('this', 'product_add_and_assign_categories', 
			array(), 
			array(
				array('field' => 'code', 'label' => 'Code', 'rules' => 'required'),
				array('field' => 'name', 'label' => 'Name', 'rules' => 'required'),
				array('field' => 'pack', 'label' => 'Pack', 'rules' => 'integer|required'),
				array('field' => 'netto', 'label' => 'Netto', 'rules' => 'numeric|required'),
				array('field' => 'minimum_stock', 'label' => 'Minimum Stock', 'rules' => 'numeric|required'),
				array('field' => 'price', 'label' => 'Price', 'rules' => 'numeric|required'),
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
	
	protected function product_add_and_assign_categories()
	{
		$this->load->library('material/lib_material');
		
		$user_id = $this->session->userdata('user_id');
		
		$data = new stdClass();
		$data->code = $this->input->post('code');
		$data->name = $this->input->post('name');
		$data->uom = $this->input->post('uom');
		$data->casing = $this->input->post('casing');
		$data->pack = $this->input->post('pack');
		$data->origin = $this->input->post('origin');
		$data->type = $this->input->post('type');
		$data->brand = $this->input->post('brand');
		$data->netto = $this->input->post('netto');
		$data->minimum_stock = $this->input->post('minimum_stock');
		$data->price = $this->input->post('price');
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
		
		$id = $this->lib_material->product_add($data, $m_productgroup_id, $user_id);
		
		$m_category_ids = $this->input->post('m_category_ids');
		if (!is_array($m_category_ids))
			$m_category_ids = array();
		
		// -- Add Category Product --
		if (!empty($m_category_ids) && is_array($m_category_ids))
		{
			$m_categories = new M_category();
			$m_categories
				->where_in('id', $m_category_ids)
				->get();
			foreach ($m_categories as $m_category)
			{
				$product_category_data = new stdClass();
				$product_category_data->m_product_id = $id;
				$product_category_data->m_category_id = $m_category->id;
				$this->lib_material->product_category_add($product_category_data, $user_id);
			}
		}
		
		return $id;
	}
	
	public function update($id)
	{
		if (!is_authorized('material/product', 'update')) 
			access_denied();
		
		parent::_execute('this', 'product_update_and_replace_categories', 
			array($id), 
			array(
				array('field' => 'code', 'label' => 'Code', 'rules' => 'required'),
				array('field' => 'name', 'label' => 'Name', 'rules' => 'required'),
				array('field' => 'pack', 'label' => 'Pack', 'rules' => 'integer|required'),
				array('field' => 'netto', 'label' => 'Netto', 'rules' => 'numeric|required'),
				array('field' => 'minimum_stock', 'label' => 'Minimum Stock', 'rules' => 'numeric|required'),
				array('field' => 'price', 'label' => 'Price', 'rules' => 'numeric|required'),
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
	
	protected function product_update_and_replace_categories($id)
	{
		$this->load->library('material/lib_material');
		
		$user_id = $this->session->userdata('user_id');
		
		$data = new stdClass();
		$data->code = $this->input->post('code');
		$data->name = $this->input->post('name');
		$data->uom = $this->input->post('uom');
		$data->casing = $this->input->post('casing');
		$data->pack = $this->input->post('pack');
		$data->origin = $this->input->post('origin');
		$data->type = $this->input->post('type');
		$data->brand = $this->input->post('brand');
		$data->netto = $this->input->post('netto');
		$data->minimum_stock = $this->input->post('minimum_stock');
		$data->price = $this->input->post('price');
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
		
		$updated_result = $this->lib_material->product_update($id, $data, $m_productgroup_id, $user_id);
		
		// -- Category Products --
		$m_category_ids = $this->input->post('m_category_ids');
		if (!is_array($m_category_ids))
			$m_category_ids = array();
		
		$m_product_categories = new M_product_category();
        $m_product_categories
			->where('m_product_id', $id)
			->get();
		
		// -- Add Category Products --
		if (!empty($m_category_ids) && is_array($m_category_ids))
		{
			foreach ($m_category_ids as $m_category_id)
			{
				$is_found_new = TRUE;
				foreach ($m_product_categories as $m_product_category)
				{
					if ($m_product_category->m_category_id == $m_category_id)
					{
						$is_found_new = FALSE;
						break;
					}
				}
				if ($is_found_new == TRUE)
				{
					$product_category_data = new stdClass();
					$product_category_data->m_product_id = $id;
					$product_category_data->m_category_id = $m_category_id;
					$this->lib_material->product_category_add($product_category_data, $user_id);
				}
			}
		}
		// -- Remove Category Products --
		foreach ($m_product_categories as $m_product_category)
		{
			$is_found_delete = TRUE;
			if (!empty($m_category_ids) && is_array($m_category_ids))
			{
				foreach ($m_category_ids as $m_category_id)
				{
					if ($m_category_id == $m_product_category->m_category_id)
					{
						$is_found_delete = FALSE;
						break;
					}
				}
			}
			if ($is_found_delete == TRUE)
			{
				$this->lib_material->product_category_remove($m_product_category->id, $user_id);
			}
		}
		
		return $updated_result;
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