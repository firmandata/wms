<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Inventory_putaway extends MY_Controller 
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
		if (!is_authorized('material/inventory_putaway', 'index')) 
			access_denied();
		
		$data = array(
		);
		
		$content = array(
			'title'		=> "Putaway",
			'content' 	=> $this->load->view('material/inventory_putaway/index', $data, TRUE)
		);
		$this->_load_layout($content);
	}
	
	public function get_list_json()
	{
		if (!is_authorized('material/inventory_putaway', 'index')) 
			access_denied();
		
		$this->load->helper('date');
		
		$from_month = $this->input->get_post('from_month');
		$from_year = $this->input->get_post('from_year');
		
		$to_month = $this->input->get_post('to_month');
		$to_year = $this->input->get_post('to_year');
		
		$this->db
			->select("ipa.id, ipa.code, ipa.putaway_date")
			->select_if_null("COUNT(DISTINCT ipad.pallet)", 0, 'pallet')
			->select_if_null("SUM(ipad.quantity_box_to)", 0, 'quantity_box_to')
			->select_if_null("SUM(ipad.quantity_to)", 0, 'quantity_to')
			->from('m_inventory_putaways ipa')
			->join('m_inventory_putawaydetails ipad', "ipad.m_inventory_putaway_id = ipa.id", 'left')
			->join('m_inventories inv', "inv.id = ipad.m_inventory_id", 'left')
			->group_by(
				array(
					'ipa.id', 'ipa.code', 'ipa.putaway_date'
				)
			);
		$this->db->where("ipa.putaway_date >=", date('Y-m-d', mktime(0, 0, 0, $from_month, 1, $from_year)));
		$this->db->where("ipa.putaway_date <=", add_date(date('Y-m-d', mktime(0, 0, 0, $to_month, 1, $to_year)), -1, 1));
		
		$this->lib_custom->project_query_filter('inv.c_project_id', $this->c_project_ids);
		
		parent::_get_list_json();
	}
	
	public function get_list_detail_json()
	{
		if (!is_authorized('material/inventory_putaway', 'index')) 
			access_denied();
		
		$this->db
			->select("ipad.m_inventory_putaway_id")
			->select("MAX(ipad.id) m_inventory_putawaydetail_id", FALSE)
			->select("ipad.pallet")
			->select("ipad.m_gridto_id, gri.code m_gridto_code")
			->select_if_null("SUM(ipad.quantity_box_to)", 0, 'quantity_box_to')
			->select_if_null("SUM(ipad.quantity_to)", 0, 'quantity_to')
			->select_cast_datetime_to_date("ipad.created", 'created')
			->from('m_inventory_putawaydetails ipad')
			->join('m_inventories inv', "inv.id = ipad.m_inventory_id")
			->join('m_grids gri', "gri.id = ipad.m_gridto_id")
			->group_by(
				array(
					'ipad.m_inventory_putaway_id',
					'ipad.pallet',
					'ipad.m_gridto_id', 'gri.code',
					$this->db->cast_datetime_to_date('ipad.created')
				)
			);
		
		$id = $this->input->get_post('id');
		if ($id !== '')
			$this->db->where("ipad.m_inventory_putaway_id", $id);
		
		$this->lib_custom->project_query_filter('inv.c_project_id', $this->c_project_ids);
		
		parent::_get_list_json();
	}
	
	public function form()
	{
		if (!is_authorized('material/inventory_putaway', 'index')) 
			access_denied();
		
		$form_action = $this->input->get_post('form_action');
		$id = $this->input->get_post('id');
		$record = NULL;
		if ($id !== NULL)
		{
			$this->db
				->select("ipa.id, ipa.code, ipa.putaway_date, ipa.notes")
				->from('m_inventory_putaways ipa')
				->where('ipa.id', $id);
			$table = $this->db->get();
			if ($table->num_rows() > 0)
				$record = $table->first_row();
			else
				show_error("Putaway not found", 400);
		}
		
		$data = array(
			'form_action'	=> $form_action,
			'record'		=> $record
		);
		$this->load->view('material/inventory_putaway/form', $data);
	}
	
	public function form_detail()
	{
		if (!is_authorized('material/inventory_putaway', 'index')) 
			access_denied();
		
		$id = $this->input->get_post('id');
		
		$data = array(
			'id'	=> $id
		);
		$this->load->view('material/inventory_putaway/form_detail', $data);
	}
	
	public function detail()
	{
		if (!is_authorized('material/inventory_putaway', 'index')) 
			access_denied();
		
		$id = $this->input->get_post('id');
		$this->db
			->select("ipa.id, ipa.code, ipa.putaway_date, ipa.notes")
			->from('m_inventory_putaways ipa')
			->where('ipa.id', $id);
		$table = $this->db->get();
		if ($table->num_rows() > 0)
			$record = $table->first_row();
		else
			show_error("Putaway not found", 400);
		
		$data = array(
			'record'	=> $record
		);
		$this->load->view('material/inventory_putaway/detail', $data);
	}
	
	public function get_list_detail_full_json()
	{
		if (!is_authorized('material/inventory_putaway', 'index')) 
			access_denied();
		
		$this->db
			->select("MAX(ipad.id) m_inventory_putawaydetail_id", FALSE)
			->select("ipad.pallet, ipad.barcode, ipad.carton_no, ipad.lot_no")
			->select("ipad.m_gridto_id, gri.code m_gridto_code")
			->select("ipad.m_product_id, pro.code m_product_code, pro.name m_product_name")
			->select_if_null("SUM(ipad.quantity_box_to)", 0, 'quantity_box_to')
			->select_if_null("SUM(ipad.quantity_to)", 0, 'quantity_to')
			->select_cast_datetime_to_date("ipad.created", 'created')
			->from('m_inventory_putawaydetails ipad')
			->join('m_inventories inv', "inv.id = ipad.m_inventory_id")
			->join('m_grids gri', "gri.id = ipad.m_gridto_id")
			->join('m_products pro', "pro.id = ipad.m_product_id")
			->group_by(
				array(
					'ipad.m_inventory_putaway_id',
					'ipad.pallet', 'ipad.barcode', 'ipad.carton_no', 'ipad.lot_no',
					'ipad.m_gridto_id', 'gri.code',
					'ipad.m_product_id', 'pro.code', 'pro.name',
					$this->db->cast_datetime_to_date('ipad.created')
				)
			);
		
		$id = $this->input->get_post('id');
		if ($id !== '')
			$this->db->where("ipad.m_inventory_putaway_id", $id);
		
		$this->lib_custom->project_query_filter('inv.c_project_id', $this->c_project_ids);
		
		parent::_get_list_json();
	}
	
	public function get_grid_autocomplete_list_json()
	{
		if (!is_authorized('material/inventory_putaway', 'index')) 
			access_denied();
		
		$keywords = $this->input->get_post('term');
		
		$this->db
			->select("grd.id", FALSE)
			->select("grd.code value")
			->select("grd.code label")
			->from('m_grids grd');
		
		if ($keywords)
			$this->db->where("grd.code LIKE '%" . $this->db->escape_like_str($keywords) . "%'", NULL, FALSE);
		
		parent::_get_list_autocomplete_json();
	}
	
	public function get_grid_default()
	{
		if (!(is_authorized('material/inventory_putaway', 'insert') || is_authorized('material/inventory_putaway', 'update')))
			access_denied();
		
		$result = new stdClass();
		$result->response = FALSE;
		$result->value = NULL;
		$result->data = array();
		
		$pallet = $this->input->get_post('pallet');
		
		$table = $this->db
			->select('m_product_id')
			->from('m_inventories')
			->where('pallet', $pallet)
			->where('quantity_box >', 0)
			->limit(1)
			->get();
		if ($table->num_rows() > 0)
		{
			$table_record = $table->first_row();
			
			$this->load->library('material/lib_inventory_operation');
			$m_grid_id = $this->lib_inventory_operation->putawaydetail_get_default_grid_by_product($table_record->m_product_id);
			
			if (!empty($m_grid_id))
			{
				$table = $this->db
					->where('id', $m_grid_id)
					->get('m_grids');
				if ($table->num_rows() > 0)
				{
					$result->response = TRUE;
					$result->data[] = $table->first_row();
				}
			}
		}
		
		if ($result->response == FALSE)
			$result->value = "Sorry, not found grid match.";
		
		$this->result_json($result);
	}
	
	public function insert()
	{
		if (!is_authorized('material/inventory_putaway', 'insert')) 
			access_denied();
		
		$this->load->library('material/lib_inventory_operation');
		
		$user_id = $this->session->userdata('user_id');
		
		$data = new stdClass();
		$data->code = $this->input->post('code');
		$data->putaway_date = $this->input->post('putaway_date');
		$data->notes = $this->input->post('notes');
		
		parent::_execute('lib_inventory_operation', 'putaway_add', 
			array($data, $user_id),
			array(
				array('field' => 'code', 'label' => 'No', 'rules' => 'required'),
				array('field' => 'putaway_date', 'label' => 'Date', 'rules' => 'required')
			)
		);
	}
	
	public function insert_detail()
	{
		if (!is_authorized('material/inventory_putaway', 'insert')) 
			access_denied();
		
		parent::_execute('this', '_insert_detail', 
			array(),
			array(
				array('field' => 'm_inventory_putaway_id', 'label' => 'Inventory Putaway', 'rules' => 'integer|required'),
				array('field' => 'pallet', 'label' => 'Pallet', 'rules' => 'required'),
				array('field' => 'm_gridto_code', 'label' => 'Grid', 'rules' => 'required')
			)
		);
	}
	
	protected function _insert_detail()
	{
		$user_id = $this->session->userdata('user_id');
		
		$this->load->library('material/lib_inventory_operation');
		
		$data = new stdClass();
		$data->m_inventory_putaway_id = $this->input->post('m_inventory_putaway_id');
		$data->pallet = $this->input->post('pallet');
		$m_gridto_code = $this->input->post('m_gridto_code');
		$m_gridto_id = NULL;
		if ($m_gridto_code !== '')
		{
			$table = $this->db
				->select('id')
				->from('m_grids')
				->where('code', $m_gridto_code)
				->get();
			if ($table->num_rows() > 0)
			{
				$table_record = $table->first_row();
				$m_gridto_id = $table_record->id;
			}
		}
		$data->m_gridto_id = $m_gridto_id;
		
		if (	empty($data->pallet)
			 && empty($data->m_gridto_id))
		{
			throw new Exception("Please entry the criteria.");
		}
		
		$this->lib_inventory_operation->putawaydetail_add_by_properties($data, $user_id);
	}
	
	public function update($id)
	{
		if (!is_authorized('material/inventory_putaway', 'update')) 
			access_denied();
		
		$this->load->library('material/lib_inventory_operation');
		
		$user_id = $this->session->userdata('user_id');
		
		$data = new stdClass();
		$data->code = $this->input->post('code');
		$data->putaway_date = $this->input->post('putaway_date');
		$data->notes = $this->input->post('notes');
		
		parent::_execute('lib_inventory_operation', 'putaway_update', 
			array($id, $data, $user_id), 
			array(
				array('field' => 'code', 'label' => 'No', 'rules' => 'required'),
				array('field' => 'putaway_date', 'label' => 'Date', 'rules' => 'required')
			)
		);
	}
	
	public function delete($id)
	{
		if (!is_authorized('material/inventory_putaway', 'delete')) 
			access_denied();
		
		$this->load->library('material/lib_inventory_operation');
		
		$user_id = $this->session->userdata('user_id');
		
		parent::_execute('lib_inventory_operation', 'putaway_remove', array($id, $user_id));
	}
	
	public function delete_detail()
	{
		if (!is_authorized('material/inventory_putaway', 'delete')) 
			access_denied();
		
		parent::_execute('this', '_delete_detail', 
			array(),
			array(
				array('field' => 'm_inventory_putaway_id', 'label' => 'Inventory Putaway', 'rules' => 'integer|required'),
				array('field' => 'pallet', 'label' => 'Pallet', 'rules' => 'required'),
				array('field' => 'm_gridto_id', 'label' => 'Grid', 'rules' => 'required')
			)
		);
	}
	
	protected function _delete_detail()
	{
		if (!is_authorized('material/inventory_putaway', 'delete')) 
			access_denied();
		
		$this->load->library('material/lib_inventory_operation');
		
		$user_id = $this->session->userdata('user_id');
		
		$data = new stdClass();
		$data->m_inventory_putaway_id = $this->input->post('m_inventory_putaway_id');
		$data->pallet = $this->input->post('pallet');
		$data->m_gridto_id = $this->input->post('m_gridto_id');
		
		if (	empty($data->pallet)
			 && empty($data->m_gridto_id))
		{
			throw new Exception("Please entry the criteria.");
		}
		
		$this->lib_inventory_operation->putawaydetail_remove_by_properties($data, $user_id);
	}
}