<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Inventory_move extends MY_Controller 
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
		if (!is_authorized('material/inventory_move', 'index')) 
			access_denied();
		
		$data = array(
		);
		
		$content = array(
			'title'		=> "Move",
			'content' 	=> $this->load->view('material/inventory_move/index', $data, TRUE)
		);
		$this->_load_layout($content);
	}
	
	public function get_list_json()
	{
		if (!is_authorized('material/inventory_move', 'index')) 
			access_denied();
		
		$this->load->helper('date');
		
		$from_month = $this->input->get_post('from_month');
		$from_year = $this->input->get_post('from_year');
		
		$to_month = $this->input->get_post('to_month');
		$to_year = $this->input->get_post('to_year');
		
		$this->db
			->select("im.id, im.code, im.move_date")
			->select_if_null("COUNT(DISTINCT imd.m_product_id)", 0, 'product')
			->select_if_null("SUM(imd.quantity_box_to)", 0, 'quantity_box_to')
			->select_if_null("SUM(imd.quantity_to)", 0, 'quantity_to')
			->from('m_inventory_moves im')
			->join('m_inventory_movedetails imd', "imd.m_inventory_move_id = im.id", 'left')
			->join('m_inventories inv', "inv.id = imd.m_inventory_id", 'left')
			->group_by(
				array(
					'im.id', 'im.code', 'im.move_date'
				)
			);
		$this->db->where("im.move_date >=", date('Y-m-d', mktime(0, 0, 0, $from_month, 1, $from_year)));
		$this->db->where("im.move_date <=", add_date(date('Y-m-d', mktime(0, 0, 0, $to_month, 1, $to_year)), -1, 1));
		
		$this->lib_custom->project_query_filter('inv.c_project_id', $this->c_project_ids);
		
		parent::_get_list_json();
	}
	
	public function get_list_detail_json()
	{
		if (!is_authorized('material/inventory_move', 'index')) 
			access_denied();
		
		$this->db
			->select("imd.m_inventory_move_id")
			->select("MAX(imd.id) m_inventory_movedetail_id", FALSE)
			->select("imd.pallet_from, imd.pallet_to, imd.barcode, imd.carton_no, imd.lot_no")
			->select("imd.m_gridfrom_id, grif.code m_gridfrom_code")
			->select("imd.m_gridto_id, grit.code m_gridto_code")
			->select("imd.m_product_id, pro.code m_product_code, pro.name m_product_name")
			->select_if_null("SUM(imd.quantity_box_to)", 0, 'quantity_box_to')
			->select_if_null("SUM(imd.quantity_to)", 0, 'quantity_to')
			->select_cast_datetime_to_date("imd.created", 'created')
			->from('m_inventory_movedetails imd')
			->join('m_inventories inv', "inv.id = imd.m_inventory_id")
			->join('m_grids grif', "grif.id = imd.m_gridfrom_id")
			->join('m_grids grit', "grit.id = imd.m_gridto_id")
			->join('m_products pro', "pro.id = imd.m_product_id")
			->group_by(
				array(
					'imd.m_inventory_move_id',
					'imd.pallet_from', 'imd.pallet_to', 'imd.barcode', 'imd.carton_no', 'imd.lot_no',
					'imd.m_gridfrom_id', 'grif.code',
					'imd.m_gridto_id', 'grit.code',
					'imd.m_product_id', 'pro.code', 'pro.name',
					$this->db->cast_datetime_to_date('imd.created')
				)
			);
		
		$id = $this->input->get_post('id');
		if ($id !== '')
			$this->db->where("imd.m_inventory_move_id", $id);
		
		$this->lib_custom->project_query_filter('inv.c_project_id', $this->c_project_ids);
		
		parent::_get_list_json();
	}
	
	public function form()
	{
		if (!is_authorized('material/inventory_move', 'index')) 
			access_denied();
		
		$form_action = $this->input->get_post('form_action');
		$id = $this->input->get_post('id');
		$record = NULL;
		if ($id !== NULL)
		{
			$this->db
				->select("im.id, im.code, im.move_date, im.notes")
				->from('m_inventory_moves im')
				->where('im.id', $id);
			$table = $this->db->get();
			if ($table->num_rows() > 0)
				$record = $table->first_row();
			else
				show_error("Move not found", 400);
		}
		
		$data = array(
			'form_action'	=> $form_action,
			'record'		=> $record
		);
		$this->load->view('material/inventory_move/form', $data);
	}
	
	public function form_detail()
	{
		if (!is_authorized('material/inventory_move', 'index')) 
			access_denied();
		
		$id = $this->input->get_post('id');
		
		$data = array(
			'id'	=> $id
		);
		$this->load->view('material/inventory_move/form_detail', $data);
	}
	
	public function detail()
	{
		if (!is_authorized('material/inventory_move', 'index')) 
			access_denied();
		
		$id = $this->input->get_post('id');
		$this->db
			->select("im.id, im.code, im.move_date, im.notes")
			->from('m_inventory_moves im')
			->where('im.id', $id);
		$table = $this->db->get();
		if ($table->num_rows() > 0)
			$record = $table->first_row();
		else
			show_error("Move not found", 400);
		
		$data = array(
			'record'	=> $record
		);
		$this->load->view('material/inventory_move/detail', $data);
	}
	
	public function get_list_detail_full_json()
	{
		if (!is_authorized('material/inventory_move', 'index')) 
			access_denied();
		
		$this->db
			->select("MAX(imd.id) m_inventory_movedetail_id", FALSE)
			->select("imd.pallet_from, imd.pallet_to, imd.barcode, imd.carton_no, imd.lot_no")
			->select("imd.m_gridfrom_id, grif.code m_gridfrom_code")
			->select("imd.m_gridto_id, grit.code m_gridto_code")
			->select("imd.m_product_id, pro.code m_product_code, pro.name m_product_name")
			->select_if_null("SUM(imd.quantity_box_to)", 0, 'quantity_box_to')
			->select_if_null("SUM(imd.quantity_to)", 0, 'quantity_to')
			->select_cast_datetime_to_date("imd.created", 'created')
			->from('m_inventory_movedetails imd')
			->join('m_inventories inv', "inv.id = imd.m_inventory_id")
			->join('m_grids grif', "grif.id = imd.m_gridfrom_id")
			->join('m_grids grit', "grit.id = imd.m_gridto_id")
			->join('m_products pro', "pro.id = imd.m_product_id")
			->group_by(
				array(
					'imd.m_inventory_move_id',
					'imd.pallet_from', 'imd.pallet_to', 'imd.barcode', 'imd.carton_no', 'imd.lot_no',
					'imd.m_gridfrom_id', 'grif.code',
					'imd.m_gridto_id', 'grit.code',
					'imd.m_product_id', 'pro.code', 'pro.name',
					$this->db->cast_datetime_to_date('imd.created')
				)
			);
		
		$id = $this->input->get_post('id');
		if ($id !== '')
			$this->db->where("imd.m_inventory_move_id", $id);
		
		$this->lib_custom->project_query_filter('inv.c_project_id', $this->c_project_ids);
		
		parent::_get_list_json();
	}
	
	public function get_grid_autocomplete_list_json()
	{
		if (!is_authorized('material/inventory_move', 'index')) 
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
	
	public function insert()
	{
		if (!is_authorized('material/inventory_move', 'insert')) 
			access_denied();
		
		$this->load->library('material/lib_inventory_operation');
		
		$user_id = $this->session->userdata('user_id');
		
		$data = new stdClass();
		$data->code = $this->input->post('code');
		$data->move_date = $this->input->post('move_date');
		$data->notes = $this->input->post('notes');
		
		parent::_execute('lib_inventory_operation', 'move_add', 
			array($data, $user_id),
			array(
				array('field' => 'code', 'label' => 'No', 'rules' => 'required'),
				array('field' => 'move_date', 'label' => 'Date', 'rules' => 'required')
			)
		);
	}
	
	public function insert_detail()
	{
		if (!is_authorized('material/inventory_move', 'insert')) 
			access_denied();
		
		parent::_execute('this', '_insert_detail', 
			array(),
			array(
				array('field' => 'm_inventory_move_id', 'label' => 'Inventory Move', 'rules' => 'integer|required')
			)
		);
	}
	
	protected function _insert_detail()
	{
		$user_id = $this->session->userdata('user_id');
		
		$this->load->library('material/lib_inventory_operation');
		
		$data = new stdClass();
		$data->m_inventory_move_id = $this->input->post('m_inventory_move_id');
		$barcode = $this->input->post('barcode');
		if ($barcode !== '')
			$data->barcode = $barcode;
		$pallet_from = $this->input->post('pallet_from');
		if ($pallet_from !== '')
			$data->pallet_from = $pallet_from;
		$pallet_to = $this->input->post('pallet_to');
		if ($pallet_to !== '')
			$data->pallet_to = $pallet_to;
		
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
		
		$m_gridfrom_code = $this->input->post('m_gridfrom_code');
		if ($m_gridfrom_code !== '')
		{
			$table = $this->db
				->select('id')
				->from('m_grids')
				->where('code', $m_gridfrom_code)
				->get();
			if ($table->num_rows() > 0)
			{
				$table_record = $table->first_row();
				$data->m_gridfrom_id = $table_record->id;
			}
		}
		
		$m_gridto_code = $this->input->post('m_gridto_code');
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
				$data->m_gridto_id = $table_record->id;
			}
		}
		
		if (	empty($data->barcode)
			 && empty($data->pallet_from)
			 && empty($data->m_product_id)
			 && empty($data->m_gridfrom_id))
		{
			throw new Exception("Please entry the source criteria.");
		}
		
		if (	empty($data->pallet_to)
			 && empty($data->m_gridto_id))
		{
			throw new Exception("Please entry the target criteria.");
		}
		
		$this->lib_inventory_operation->movedetail_add_by_properties($data, $user_id);
	}
	
	public function update($id)
	{
		if (!is_authorized('material/inventory_move', 'update')) 
			access_denied();
		
		$this->load->library('material/lib_inventory_operation');
		
		$user_id = $this->session->userdata('user_id');
		
		$data = new stdClass();
		$data->code = $this->input->post('code');
		$data->move_date = $this->input->post('move_date');
		$data->notes = $this->input->post('notes');
		
		parent::_execute('lib_inventory_operation', 'move_update', 
			array($id, $data, $user_id), 
			array(
				array('field' => 'code', 'label' => 'No', 'rules' => 'required'),
				array('field' => 'move_date', 'label' => 'Date', 'rules' => 'required')
			)
		);
	}
	
	public function delete($id)
	{
		if (!is_authorized('material/inventory_move', 'delete')) 
			access_denied();
		
		$this->load->library('material/lib_inventory_operation');
		
		$user_id = $this->session->userdata('user_id');
		
		parent::_execute('lib_inventory_operation', 'move_remove', array($id, $user_id));
	}
	
	public function delete_detail()
	{
		if (!is_authorized('material/inventory_move', 'delete')) 
			access_denied();
		
		parent::_execute('this', '_delete_detail', 
			array(),
			array(
				array('field' => 'm_inventory_move_id', 'label' => 'Inventory Move', 'rules' => 'integer|required')
			)
		);
	}
	
	protected function _delete_detail()
	{
		if (!is_authorized('material/inventory_move', 'delete')) 
			access_denied();
		
		$this->load->library('material/lib_inventory_operation');
		
		$user_id = $this->session->userdata('user_id');
		
		$data = new stdClass();
		$data->m_inventory_move_id = $this->input->post('m_inventory_move_id');
		$data->m_product_id = $this->input->post('m_product_id');
		$data->barcode = $this->input->post('barcode');
		$data->pallet_from = $this->input->post('pallet_from');
		$data->pallet_to = $this->input->post('pallet_to');
		$data->m_gridfrom_id = $this->input->post('m_gridfrom_id');
		$data->m_gridto_id = $this->input->post('m_gridto_id');
		
		if (	empty($data->barcode)
			 && empty($data->pallet_from)
			 && empty($data->m_product_id)
			 && empty($data->m_gridfrom_id)
			 && empty($data->pallet_to)
			 && empty($data->m_gridto_id))
		{
			throw new Exception("Please entry the criteria.");
		}
		
		$this->lib_inventory_operation->movedetail_remove_by_properties($data, $user_id);
	}
}