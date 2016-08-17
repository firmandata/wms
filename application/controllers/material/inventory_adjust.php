<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Inventory_adjust extends MY_Controller 
{
	private $c_project_ids;
	
	public function __construct()
	{
		parent::__construct(TRUE, 'backend');
		
		$this->load->library('custom/lib_custom');
		
		$user_id = $this->session->userdata('user_id');
		$this->c_project_ids = $this->lib_custom->project_get_ids($user_id);
	}
	
	public function index()
	{
		if (!is_authorized('material/inventory_adjust', 'index')) 
			access_denied();
		
		$data = array(
		);
		
		$content = array(
			'title'		=> "Adjust",
			'content' 	=> $this->load->view('material/inventory_adjust/index', $data, TRUE)
		);
		$this->_load_layout($content);
	}
	
	public function get_list_json()
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
		$this->db->where("ia.adjust_date >=", date('Y-m-d', mktime(0, 0, 0, $from_month, 1, $from_year)));
		$this->db->where("ia.adjust_date <=", add_date(date('Y-m-d', mktime(0, 0, 0, $to_month, 1, $to_year)), -1, 1));
		
		$this->lib_custom->project_query_filter('inv.c_project_id', $this->c_project_ids);
		
		parent::_get_list_json();
	}
	
	public function get_list_detail_json()
	{
		if (!is_authorized('material/inventory_adjust', 'index')) 
			access_denied();
		
		$this->db
			->select("iad.m_inventory_adjust_id")
			->select("MAX(iad.id) m_inventory_adjustdetail_id", FALSE)
			->select("iad.pallet, iad.barcode, iad.carton_no, iad.lot_no")
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
					'iad.pallet', 'iad.barcode', 'iad.carton_no', 'iad.lot_no',
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
	
	public function form()
	{
		if (!is_authorized('material/inventory_adjust', 'index')) 
			access_denied();
		
		$form_action = $this->input->get_post('form_action');
		$id = $this->input->get_post('id');
		$record = NULL;
		if ($id !== NULL)
		{
			$this->db
				->select("ia.id, ia.code, ia.adjust_date, ia.adjust_type, ia.notes")
				->from('m_inventory_adjusts ia')
				->where('ia.id', $id);
			$table = $this->db->get();
			if ($table->num_rows() > 0)
				$record = $table->first_row();
			else
				show_error("Adjust not found", 400);
		}
		
		$data = array(
			'form_action'	=> $form_action,
			'record'		=> $record
		);
		$this->load->view('material/inventory_adjust/form', $data);
	}
	
	public function form_detail()
	{
		if (!is_authorized('material/inventory_adjust', 'index')) 
			access_denied();
		
		$id = $this->input->get_post('id');
		
		$data = array(
			'id'	=> $id
		);
		$this->load->view('material/inventory_adjust/form_detail', $data);
	}
	
	public function detail()
	{
		if (!is_authorized('material/inventory_adjust', 'index')) 
			access_denied();
		
		$id = $this->input->get_post('id');
		$this->db
			->select("ia.id, ia.code, ia.adjust_date, ia.adjust_type, ia.notes")
			->from('m_inventory_adjusts ia')
			->where('ia.id', $id);
		$table = $this->db->get();
		if ($table->num_rows() > 0)
			$record = $table->first_row();
		else
			show_error("Adjust not found", 400);
		
		$data = array(
			'record'	=> $record
		);
		$this->load->view('material/inventory_adjust/detail', $data);
	}
	
	public function get_list_detail_full_json()
	{
		if (!is_authorized('material/inventory_adjust', 'index')) 
			access_denied();
		
		$this->db
			->select("MAX(iad.id) m_inventory_adjustdetail_id", FALSE)
			->select("iad.pallet, iad.barcode, iad.carton_no, iad.lot_no")
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
					'iad.pallet', 'iad.barcode', 'iad.carton_no', 'iad.lot_no',
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
	
	public function get_product_autocomplete_list_json()
	{
		if (!is_authorized('material/inventory_adjust', 'index')) 
			access_denied();
		
		$keywords = $this->input->get_post('term');
		
		$this->db
			->select("pro.id", FALSE)
			->select_concat(array("pro.name", "' ('", "pro.code", "')'"), 'value')
			->select_concat(array("pro.name", "' ('", "pro.code", "')'"), 'label')
			->from('m_products pro');
		
		if ($keywords)
			$this->db->where($this->db->concat(array("pro.name", "' ('", "pro.code", "')'")) . " LIKE '%" . $this->db->escape_like_str($keywords) . "%'", NULL, FALSE);
		
		parent::_get_list_autocomplete_json();
	}
	
	public function insert()
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
	
	public function insert_detail()
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
	
	public function update($id)
	{
		if (!is_authorized('material/inventory_adjust', 'update')) 
			access_denied();
		
		$this->load->library('material/lib_inventory_operation');
		
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
	
	public function delete($id)
	{
		if (!is_authorized('material/inventory_adjust', 'delete')) 
			access_denied();
		
		$this->load->library('material/lib_inventory_operation');
		
		$user_id = $this->session->userdata('user_id');
		
		parent::_execute('lib_inventory_operation', 'adjust_remove', array($id, $user_id));
	}
	
	public function delete_detail()
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