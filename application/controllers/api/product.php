<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'/libraries/REST_Controller.php';

class Product extends REST_Controller
{
	function __construct()
    {
        parent::__construct();
		
		$user_id = $this->session->userdata('user_id');
    }
    
    function list_get()
    {
		if (!is_authorized('material/product', 'index')) 
			access_denied();
		
		$this->db
			->select("pro.id, pro.code, pro.name")
			->select("pro.uom, pro.pack, pro.origin, pro.netto, pro.minimum_stock")
			->select("pro.brand, pro.type")
			->select("prog.code m_productgroup_code, prog.name m_productgroup_name")
			->select("cpro.barcode_length c_barcode_length")
			->select("cpro.quantity_start c_quantity_start, cpro.quantity_end c_quantity_end")
			->select("cpro.quantity_point_start c_quantity_point_start, cpro.quantity_point_end c_quantity_point_end")
			->select("cpro.sku_start c_sku_start, cpro.sku_end c_sku_end")
			->select("cpro.carton_start c_carton_start, cpro.carton_end c_carton_end")
			->select("cpro.packed_date_start c_packed_date_start, cpro.packed_date_end c_packed_date_end")
			->from('m_products pro')
			->join('cus_m_products cpro', "cpro.m_product_id = pro.id", 'left')
			->join('m_productgroups prog', "prog.id = pro.m_productgroup_id", 'left');
		
		parent::_get_list_json();
    }
}