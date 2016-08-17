<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Inventory_hold extends MY_Controller 
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
		if (!is_authorized('material/inventory_hold', 'index')) 
			access_denied();
		
		$data = array(
		);
		
		$content = array(
			'title'		=> "Hold",
			'content' 	=> $this->load->view('material/inventory_hold/index', $data, TRUE)
		);
		$this->_load_layout($content);
	}
	
	public function get_list_json()
	{
		if (!is_authorized('material/inventory_hold', 'index')) 
			access_denied();
		
		$this->load->helper('date');
		
		$from_month = $this->input->get_post('from_month');
		$from_year = $this->input->get_post('from_year');
		
		$to_month = $this->input->get_post('to_month');
		$to_year = $this->input->get_post('to_year');
		
		$this->db
			->select("ih.id, ih.code, ih.hold_date")
			->select_if_null("COUNT(DISTINCT ihd.m_product_id)", 0, 'product')
			->select_if_null("SUM(ihd.quantity_box_from)", 0, 'quantity_box_from')
			->select_if_null("SUM(ihd.quantity_from)", 0, 'quantity_from')
			->select_if_null("SUM(CASE WHEN ihd.is_hold = 0 THEN ihd.quantity_box_from ELSE 0 END)", 0, 'quantity_box_unhold')
			->select_if_null("SUM(CASE WHEN ihd.is_hold = 0 THEN ihd.quantity_from ELSE 0 END)", 0, 'quantity_unhold')
			->from('m_inventory_holds ih')
			->join('m_inventory_holddetails ihd', "ihd.m_inventory_hold_id = ih.id", 'left')
			->join('m_inventories inv', "inv.id = ihd.m_inventory_id", 'left')
			->group_by(
				array(
					'ih.id', 'ih.code', 'ih.hold_date'
				)
			);
		$this->db->where("ih.hold_date >=", date('Y-m-d', mktime(0, 0, 0, $from_month, 1, $from_year)));
		$this->db->where("ih.hold_date <=", add_date(date('Y-m-d', mktime(0, 0, 0, $to_month, 1, $to_year)), -1, 1));
		
		$this->lib_custom->project_query_filter('inv.c_project_id', $this->c_project_ids);
		
		parent::_get_list_json();
	}
	
	public function get_list_detail_json()
	{
		if (!is_authorized('material/inventory_hold', 'index')) 
			access_denied();
		
		$this->db
			->select("ihd.m_inventory_hold_id")
			->select("MAX(ihd.id) m_inventory_holddetail_id", FALSE)
			->select("ihd.pallet, ihd.barcode")
			->select("ihd.m_grid_id, gri.code m_grid_code")
			->select("ihd.m_product_id, pro.code m_product_code, pro.name m_product_name")
			->select("ihd.is_hold")
			->select_if_null("SUM(ihd.quantity_box_from)", 0, 'quantity_box_from')
			->select_if_null("SUM(ihd.quantity_from)", 0, 'quantity_from')
			->select_cast_datetime_to_date("ihd.created", 'created')
			->from('m_inventory_holddetails ihd')
			->join('m_inventories inv', "inv.id = ihd.m_inventory_id")
			->join('m_grids gri', "gri.id = ihd.m_grid_id")
			->join('m_products pro', "pro.id = ihd.m_product_id")
			->group_by(
				array(
					'ihd.m_inventory_hold_id',
					'ihd.pallet', 'ihd.barcode',
					'ihd.m_grid_id', 'gri.code',
					'ihd.m_product_id', 'pro.code', 'pro.name',
					'ihd.is_hold',
					$this->db->cast_datetime_to_date('ihd.created')
				)
			);
		
		$id = $this->input->get_post('id');
		if ($id !== '')
			$this->db->where("ihd.m_inventory_hold_id", $id);
		
		$this->lib_custom->project_query_filter('inv.c_project_id', $this->c_project_ids);
		
		parent::_get_list_json();
	}
	
	public function form()
	{
		if (!is_authorized('material/inventory_hold', 'index')) 
			access_denied();
		
		$form_action = $this->input->get_post('form_action');
		$id = $this->input->get_post('id');
		$record = NULL;
		if ($id !== NULL)
		{
			$this->db
				->select("ih.id, ih.code, ih.hold_date, ih.notes")
				->from('m_inventory_holds ih')
				->where('ih.id', $id);
			$table = $this->db->get();
			if ($table->num_rows() > 0)
				$record = $table->first_row();
			else
				show_error("Hold not found", 400);
		}
		
		$data = array(
			'form_action'	=> $form_action,
			'record'		=> $record
		);
		$this->load->view('material/inventory_hold/form', $data);
	}
	
	public function form_detail()
	{
		if (!is_authorized('material/inventory_hold', 'index')) 
			access_denied();
		
		$id = $this->input->get_post('id');
		
		$data = array(
			'id'	=> $id
		);
		$this->load->view('material/inventory_hold/form_detail', $data);
	}
	
	public function detail()
	{
		if (!is_authorized('material/inventory_hold', 'index')) 
			access_denied();
		
		$id = $this->input->get_post('id');
		$this->db
			->select("ih.id, ih.code, ih.hold_date, ih.notes")
			->from('m_inventory_holds ih')
			->where('ih.id', $id);
		$table = $this->db->get();
		if ($table->num_rows() > 0)
			$record = $table->first_row();
		else
			show_error("Hold not found", 400);
		
		$data = array(
			'record'	=> $record
		);
		$this->load->view('material/inventory_hold/detail', $data);
	}
	
	public function get_list_detail_full_json()
	{
		if (!is_authorized('material/inventory_hold', 'index')) 
			access_denied();
		
		$this->db
			->select("MAX(ihd.id) m_inventory_holddetail_id", FALSE)
			->select("ihd.pallet, ihd.barcode, ihd.carton_no, ihd.lot_no")
			->select("ihd.m_grid_id, gri.code m_grid_code")
			->select("ihd.m_product_id, pro.code m_product_code, pro.name m_product_name")
			->select("ihd.is_hold")
			->select_if_null("SUM(ihd.quantity_box_from)", 0, 'quantity_box_from')
			->select_if_null("SUM(ihd.quantity_from)", 0, 'quantity_from')
			->select_cast_datetime_to_date("ihd.created", 'created')
			->from('m_inventory_holddetails ihd')
			->join('m_inventories inv', "inv.id = ihd.m_inventory_id")
			->join('m_grids gri', "gri.id = ihd.m_grid_id")
			->join('m_products pro', "pro.id = ihd.m_product_id")
			->group_by(
				array(
					'ihd.m_inventory_hold_id',
					'ihd.pallet', 'ihd.barcode', 'ihd.carton_no', 'ihd.lot_no',
					'ihd.m_grid_id', 'gri.code',
					'ihd.m_product_id', 'pro.code', 'pro.name',
					'ihd.is_hold',
					$this->db->cast_datetime_to_date('ihd.created')
				)
			);
		
		$id = $this->input->get_post('id');
		if ($id !== '')
			$this->db->where("ihd.m_inventory_hold_id", $id);
		
		$this->lib_custom->project_query_filter('inv.c_project_id', $this->c_project_ids);
		
		parent::_get_list_json();
	}
	
	public function get_product_autocomplete_list_json()
	{
		if (!is_authorized('material/inventory_hold', 'index')) 
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
		if (!is_authorized('material/inventory_hold', 'insert')) 
			access_denied();
		
		$this->load->library('material/lib_inventory_operation');
		
		$user_id = $this->session->userdata('user_id');
		
		$data = new stdClass();
		$data->code = $this->input->post('code');
		$data->hold_date = $this->input->post('hold_date');
		$data->notes = $this->input->post('notes');
		
		parent::_execute('lib_inventory_operation', 'hold_add', 
			array($data, $user_id),
			array(
				array('field' => 'code', 'label' => 'No', 'rules' => 'required'),
				array('field' => 'hold_date', 'label' => 'Date', 'rules' => 'required')
			)
		);
	}
	
	public function insert_detail()
	{
		if (!is_authorized('material/inventory_hold', 'insert')) 
			access_denied();
		
		parent::_execute('this', '_insert_detail', 
			array(),
			array(
				array('field' => 'm_inventory_hold_id', 'label' => 'Inventory Hold', 'rules' => 'integer|required')
			)
		);
	}
	
	protected function _insert_detail()
	{
		$user_id = $this->session->userdata('user_id');
		
		$this->load->library('material/lib_inventory_operation');
		
		$data = new stdClass();
		$data->m_inventory_hold_id = $this->input->post('m_inventory_hold_id');
		$barcode = $this->input->post('barcode');
		if ($barcode !== '')
			$data->barcode = $barcode;
		$pallet = $this->input->post('pallet');
		if ($pallet !== '')
			$data->pallet = $pallet;
		
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
		
		$this->lib_inventory_operation->holddetail_add_by_properties($data, $user_id);
	}
	
	public function update($id)
	{
		if (!is_authorized('material/inventory_hold', 'update')) 
			access_denied();
		
		$this->load->library('material/lib_inventory_operation');
		
		$user_id = $this->session->userdata('user_id');
		
		$data = new stdClass();
		$data->code = $this->input->post('code');
		$data->hold_date = $this->input->post('hold_date');
		$data->notes = $this->input->post('notes');
		
		parent::_execute('lib_inventory_operation', 'hold_update', 
			array($id, $data, $user_id), 
			array(
				array('field' => 'code', 'label' => 'No', 'rules' => 'required'),
				array('field' => 'hold_date', 'label' => 'Date', 'rules' => 'required')
			)
		);
	}
	
	public function delete($id)
	{
		if (!is_authorized('material/inventory_hold', 'delete')) 
			access_denied();
		
		$this->load->library('material/lib_inventory_operation');
		
		$user_id = $this->session->userdata('user_id');
		
		parent::_execute('lib_inventory_operation', 'hold_remove', array($id, $user_id));
	}
	
	public function unhold_detail()
	{
		if (!is_authorized('material/inventory_hold', 'update')) 
			access_denied();
		
		parent::_execute('this', '_unhold_detail', 
			array(),
			array(
				array('field' => 'm_inventory_hold_id', 'label' => 'Inventory Hold', 'rules' => 'integer|required')
			)
		);
	}
	
	protected function _unhold_detail()
	{
		$user_id = $this->session->userdata('user_id');
		
		$this->load->library('material/lib_inventory_operation');
		
		$data = new stdClass();
		$data->m_inventory_hold_id = $this->input->post('m_inventory_hold_id');
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
		
		$this->lib_inventory_operation->holddetail_unhold_by_properties($data, $user_id);
	}
	
	public function rehold_detail()
	{
		if (!is_authorized('material/inventory_hold', 'update')) 
			access_denied();
		
		parent::_execute('this', '_rehold_detail', 
			array(),
			array(
				array('field' => 'm_inventory_hold_id', 'label' => 'Inventory Hold', 'rules' => 'integer|required')
			)
		);
	}
	
	protected function _rehold_detail()
	{
		$user_id = $this->session->userdata('user_id');
		
		$this->load->library('material/lib_inventory_operation');
		
		$data = new stdClass();
		$data->m_inventory_hold_id = $this->input->post('m_inventory_hold_id');
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
		
		$this->lib_inventory_operation->holddetail_rehold_by_properties($data, $user_id);
	}
	
	public function delete_detail()
	{
		if (!is_authorized('material/inventory_hold', 'delete')) 
			access_denied();
		
		parent::_execute('this', '_delete_detail', 
			array(),
			array(
				array('field' => 'm_inventory_hold_id', 'label' => 'Inventory Hold', 'rules' => 'integer|required')
			)
		);
	}
	
	protected function _delete_detail()
	{
		$user_id = $this->session->userdata('user_id');
		
		$this->load->library('material/lib_inventory_operation');
		
		$data = new stdClass();
		$data->m_inventory_hold_id = $this->input->post('m_inventory_hold_id');
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
		
		$this->lib_inventory_operation->holddetail_remove_by_properties($data, $user_id);
	}
	
	public function get_is_hold_dropdown($element_name)
	{
		$list_active = array('' => '');
		$actives = $this->config->item('boolean');
		foreach ($actives as $active_key=>$active)
			$list_active[$active_key] = $active;
		$dropdown = form_dropdown($element_name, 
			$list_active
		);
		$this->output
			->set_output($dropdown);
	}
}