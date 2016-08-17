<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'/libraries/REST_Controller.php';

class Inventory_adjust extends REST_Controller 
{
	private $c_project_ids;
	
	public function __construct()
	{
		parent::__construct();
		
		$this->load->library('custom/lib_custom');
		
		$user_id = $this->session->userdata('user_id');
		$this->c_project_ids = $this->lib_custom->project_get_ids($user_id);
	}
	
	public function list_get()
	{
		if (!is_authorized('material/inventory_adjust', 'index')) 
			access_denied();
		
		$this->load->helper('date');
		
		$from_month = $this->input->get_post('from_month');
		$from_year = $this->input->get_post('from_year');
		
		$to_month = $this->input->get_post('to_month');
		$to_year = $this->input->get_post('to_year');
		
		$this->db
			->select("ia.id, ia.code, ia.adjust_date, ia.adjust_type")
			->select_if_null("COUNT(DISTINCT iad.m_product_id)", 0, 'product')
			->select_if_null("SUM(iad.quantity_box_from)", 0, 'quantity_box_from')
			->select_if_null("SUM(iad.quantity_box_to)", 0, 'quantity_box_to')
			->select_if_null("SUM(iad.quantity_from)", 0, 'quantity_from')
			->select_if_null("SUM(iad.quantity_to)", 0, 'quantity_to')
			->from('m_inventory_adjusts ia')
			->join('m_inventory_adjustdetails iad', "iad.m_inventory_adjust_id = ia.id", 'left')
			->join('m_inventories inv', "inv.id = iad.m_inventory_id", 'left')
			->group_by(
				array(
					'ia.id', 'ia.code', 'ia.adjust_date', 'ia.adjust_type'
				)
			);
		
		if ($from_month && $from_year)
			$this->db->where("ia.adjust_date >=", date('Y-m-d', mktime(0, 0, 0, $from_month, 1, $from_year)));
		
		if ($to_month && $to_year)
			$this->db->where("ia.adjust_date <=", add_date(date('Y-m-d', mktime(0, 0, 0, $to_month, 1, $to_year)), -1, 1));
		
		$this->lib_custom->project_query_filter('inv.c_project_id', $this->c_project_ids);
		
		parent::_get_list_json();
	}
	
	public function detail_get()
	{
		if (!is_authorized('material/inventory_adjust', 'index')) 
			access_denied();
		
		$result = new stdClass();
		$result->response = FALSE;
		$result->value = NULL;
		$result->data = array();
		
		$id = $this->input->get_post('id');
		$this->db
			->select("ia.id, ia.code, ia.adjust_date, ia.adjust_type, ia.notes")
			->from('m_inventory_adjusts ia')
			->where('ia.id', $id);
		$table = $this->db->get();
		if ($table->num_rows() > 0)
		{
			$result->value = $table->first_row();
			$result->response = TRUE;
		}
		else
			show_error("Adjust not found", 400);
		
		$this->result_json($result);
	}
	
	public function detail_list_get()
	{
		if (!is_authorized('material/inventory_adjust', 'index')) 
			access_denied();
		
		$this->db
			->select("iad.m_inventory_adjust_id")
			->select("MAX(iad.id) m_inventory_adjustdetail_id", FALSE)
			->select("iad.pallet, iad.barcode")
			->select("iad.m_grid_id, gri.code m_grid_code")
			->select("iad.m_product_id, pro.code m_product_code, pro.name m_product_name")
			->select_if_null("SUM(iad.quantity_box_to)", 0, 'quantity_box_to')
			->select_if_null("SUM(iad.quantity_box_from)", 0, 'quantity_box_from')
			->select_if_null("SUM(iad.quantity_to)", 0, 'quantity_to')
			->select_if_null("SUM(iad.quantity_from)", 0, 'quantity_from')
			->select_cast_datetime_to_date("iad.created", 'created')
			->from('m_inventory_adjustdetails iad')
			->join('m_inventories inv', "inv.id = iad.m_inventory_id")
			->join('m_grids gri', "gri.id = iad.m_grid_id")
			->join('m_products pro', "pro.id = iad.m_product_id")
			->group_by(
				array(
					'iad.m_inventory_adjust_id',
					'iad.pallet', 'iad.barcode',
					'iad.m_grid_id', 'gri.code',
					'iad.m_product_id', 'pro.code', 'pro.name',
					$this->db->cast_datetime_to_date('iad.created')
				)
			);
		
		$id = $this->input->get_post('id');
		if ($id !== '')
			$this->db->where("iad.m_inventory_adjust_id", $id);
		
		$this->lib_custom->project_query_filter('inv.c_project_id', $this->c_project_ids);
		
		parent::_get_list_json();
	}
	
	public function insert_post()
	{
		if (!is_authorized('material/inventory_adjust', 'insert')) 
			access_denied();
		
		$this->load->library('material/lib_inventory_operation');
		
		$user_id = $this->session->userdata('user_id');
		
		$data = new stdClass();
		$data->code = $this->input->post('code');
		$data->adjust_date = $this->input->post('adjust_date');
		$data->adjust_type = $this->input->post('adjust_type');
		$data->notes = $this->input->post('notes');
		
		parent::_execute('lib_inventory_operation', 'adjust_add', 
			array($data, $user_id),
			array(
				array('field' => 'code', 'label' => 'No', 'rules' => 'required'),
				array('field' => 'adjust_date', 'label' => 'Date', 'rules' => 'required')
			)
		);
	}
	
	public function insert_detail_post()
	{
		if (!is_authorized('material/inventory_adjust', 'insert')) 
			access_denied();
		
		parent::_execute('this', '_insert_detail', 
			array(),
			array(
				array('field' => 'm_inventory_adjust_id', 'label' => 'Inventory Adjust', 'rules' => 'integer|required')
			)
		);
	}
	
	protected function _insert_detail()
	{
		$user_id = $this->session->userdata('user_id');
		
		$this->load->library('material/lib_inventory_operation');
		
		$data = new stdClass();
		$data->m_inventory_adjust_id = $this->input->post('m_inventory_adjust_id');
		$barcode = $this->input->post('barcode');
		if ($barcode !== '')
			$data->barcode = $barcode;
		$pallet = $this->input->post('pallet');
		if ($pallet !== '')
			$data->pallet = $pallet;
		$quantity = $this->input->post('quantity');
		if ($quantity !== '')
			$data->quantity = $quantity;
		
		$m_product_code = $this->input->post('m_product_code');
		if ($m_product_code !== '')
		{
			$table = $this->db
				->select('id')
				->from('m_products')
				->where('code', $m_product_code)
				->get();
			if ($table->num_rows() > 0)
			{
				$table_record = $table->first_row();
				$data->m_product_id = $table_record->id;
			}
		}
		
		$m_grid_code = $this->input->post('m_grid_code');
		if ($m_grid_code !== '')
		{
			$table = $this->db
				->select('id')
				->from('m_grids')
				->where('code', $m_grid_code)
				->get();
			if ($table->num_rows() > 0)
			{
				$table_record = $table->first_row();
				$data->m_grid_id = $table_record->id;
			}
		}
		
		if (	empty($data->barcode)
			 && empty($data->pallet)
			 && empty($data->m_product_id)
			 && empty($data->m_grid_id))
		{
			throw new Exception("Please entry the criteria.");
		}
		
		$this->lib_inventory_operation->adjustdetail_add_by_properties($data, $user_id);
	}
	
	public function update_post()
	{
		if (!is_authorized('material/inventory_adjust', 'update')) 
			access_denied();
		
		$this->load->library('material/lib_inventory_operation');
		
		$id = $this->input->post('id');
		$user_id = $this->session->userdata('user_id');
		
		$data = new stdClass();
		$data->code = $this->input->post('code');
		$data->adjust_date = $this->input->post('adjust_date');
		$data->adjust_type = $this->input->post('adjust_type');
		$data->notes = $this->input->post('notes');
		
		parent::_execute('lib_inventory_operation', 'adjust_update', 
			array($id, $data, $user_id), 
			array(
				array('field' => 'code', 'label' => 'No', 'rules' => 'required'),
				array('field' => 'adjust_date', 'label' => 'Date', 'rules' => 'required')
			)
		);
	}
	
	public function delete_post()
	{
		if (!is_authorized('material/inventory_adjust', 'delete')) 
			access_denied();
		
		$this->load->library('material/lib_inventory_operation');
		
		$id = $this->input->post('id');
		$user_id = $this->session->userdata('user_id');
		
		parent::_execute('lib_inventory_operation', 'adjust_remove', array($id, $user_id));
	}
	
	public function delete_detail_post()
	{
		if (!is_authorized('material/inventory_adjust', 'delete')) 
			access_denied();
		
		parent::_execute('this', '_delete_detail', 
			array(),
			array(
				array('field' => 'm_inventory_adjust_id', 'label' => 'Inventory Adjust', 'rules' => 'integer|required')
			)
		);
	}
	
	protected function _delete_detail()
	{
		$user_id = $this->session->userdata('user_id');
		
		$this->load->library('material/lib_inventory_operation');
		
		$data = new stdClass();
		$data->m_inventory_adjust_id = $this->input->post('m_inventory_adjust_id');
		$barcode = $this->input->post('barcode');
		if ($barcode !== '')
			$data->barcode = $barcode;
		$pallet = $this->input->post('pallet');
		if ($pallet !== '')
			$data->pallet = $pallet;
		$m_product_id = $this->input->post('m_product_id');
		if ($m_product_id !== '')
			$data->m_product_id = $m_product_id;
		$m_grid_id = $this->input->post('m_grid_id');
		if ($m_grid_id !== '')
			$data->m_grid_id = $m_grid_id;
		
		if (	empty($data->barcode)
			 && empty($data->pallet)
			 && empty($data->m_product_id)
			 && empty($data->m_grid_id))
		{
			throw new Exception("Please entry the criteria.");
		}
		
		$this->lib_inventory_operation->adjustdetail_remove_by_properties($data, $user_id);
	}
}