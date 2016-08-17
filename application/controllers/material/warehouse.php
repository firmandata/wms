<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Warehouse extends MY_Controller 
{
	public function __construct()
	{
		parent::__construct(TRUE, 'backend');
	}
	
	public function index()
	{
		if (!is_authorized('material/warehouse', 'index')) 
			access_denied();
		
		$data = array(
		);
		
		$content = array(
			'title'		=> "Warehouse",
			'content' 	=> $this->load->view('material/warehouse/index', $data, TRUE)
		);
		$this->_load_layout($content);
	}
	
	public function form()
	{
		if (!is_authorized('material/warehouse', 'index')) 
			access_denied();
		
		$form_action = $this->input->get_post('form_action');
		$id = $this->input->get_post('id');
		$record = NULL;
		if ($id !== NULL)
		{
			$this->db
				->where('id', $id);
			$table = $this->db->get('m_warehouses');
			if ($table->num_rows() > 0)
			{
				$record = $table->first_row();
				
				$this->load->library('material/lib_material');
				$record->grid_scalar = $this->lib_material->warehouse_get_grid_scalar($record->id);
			}
			else
				show_error("Warehouse not found", 400);
		}
		
		$data = array(
			'form_action'	=> $form_action,
			'record'		=> $record
		);
		$this->load->view('material/warehouse/form', $data);
	}
	
	public function get_list_json()
	{
		if (!is_authorized('material/warehouse', 'index')) 
			access_denied();
		
		$this->db
			->select("wh.id, wh.code, wh.name")
			->from('m_warehouses wh');
		
		parent::_get_list_json();
	}
	
	public function insert()
	{
		if (!is_authorized('material/warehouse', 'insert')) 
			access_denied();
		
		$this->load->library('material/lib_material');
		
		$user_id = $this->session->userdata('user_id');
		
		$is_generate_grid = $this->input->post('is_generate_grid');
		if ($is_generate_grid)
			$is_generate_grid = TRUE;
		else
			$is_generate_grid = FALSE;
		
		parent::_execute('this', 'insert_warehouse', 
			array(
				$this->input->post('code'), $this->input->post('name'), 
				$is_generate_grid, $this->input->post('rows'), $this->input->post('cols'), $this->input->post('levels'), 
				$this->input->post('types'), $this->input->post('lengths'), $this->input->post('widths'), $this->input->post('heights'), $this->input->post('statuses'),
				$user_id
			), 
			array(
				array('field' => 'code', 'label' => 'Code', 'rules' => 'required'),
				array('field' => 'name', 'label' => 'Name', 'rules' => 'required')
			)
		);
	}
	
	protected function insert_warehouse(
		$code, $name, 
		$is_generate_grid = FALSE, $grid_rows = 0, $grid_cols = 0, $grid_levels = 0, $grid_types = NULL, $grid_lengths = 0, $grid_widths = 0, $grid_heights = 0, $grid_statuses = NULL,
		$user_id
	)
	{
		$this->load->library('material/lib_material');
		
		$data = new stdClass();
		$data->code = $code;
		$data->name = $name;
		$m_warehouse_id = $this->lib_material->warehouse_add($data, $user_id);
		
		if ($is_generate_grid == TRUE)
		{
			$this->lib_material->warehouse_generate_grid(
				$m_warehouse_id, 
				$grid_rows, $grid_cols, $grid_levels, 
				$grid_types, $grid_lengths, $grid_widths, $grid_heights, $grid_statuses, 
				$user_id
			);
		}
		
		return $m_warehouse_id;
	}
	
	public function update($id)
	{
		if (!is_authorized('material/warehouse', 'update')) 
			access_denied();
		
		$this->load->library('material/lib_material');
		
		$user_id = $this->session->userdata('user_id');
		
		$is_generate_grid = $this->input->post('is_generate_grid');
		if ($is_generate_grid)
			$is_generate_grid = TRUE;
		else
			$is_generate_grid = FALSE;
		
		parent::_execute('this', 'update_warehouse', 
			array(
				$id, $this->input->post('code'), $this->input->post('name'), 
				$is_generate_grid, $this->input->post('rows'), $this->input->post('cols'), $this->input->post('levels'), 
				$this->input->post('types'), $this->input->post('lengths'), $this->input->post('widths'), $this->input->post('heights'), $this->input->post('statuses'),
				$user_id
			), 
			array(
				array('field' => 'code', 'label' => 'Code', 'rules' => 'required'),
				array('field' => 'name', 'label' => 'Name', 'rules' => 'required')
			)
		);
	}
	
	protected function update_warehouse(
		$id, $code, $name, 
		$is_generate_grid = FALSE, $grid_rows = 0, $grid_cols = 0, $grid_levels = 0, $grid_types = NULL, $grid_lengths = 0, $grid_widths = 0, $grid_heights = 0, $grid_statuses = NULL,
		$user_id
	)
	{
		$this->load->library('material/lib_material');
		
		$data = new stdClass();
		$data->code = $code;
		$data->name = $name;
		$m_warehouse_id = $this->lib_material->warehouse_update($id, $data, $user_id);
		
		if ($is_generate_grid == TRUE)
		{
			$this->lib_material->warehouse_generate_grid(
				$id, 
				$grid_rows, $grid_cols, $grid_levels, 
				$grid_types, $grid_lengths, $grid_widths, $grid_heights, $grid_statuses, 
				$user_id
			);
		}
		
		return $id;
	}
	
	public function delete($id)
	{
		if (!is_authorized('material/warehouse', 'delete')) 
			access_denied();
		
		$this->load->library('material/lib_material');
		
		$user_id = $this->session->userdata('user_id');
		
		parent::_execute('lib_material', 'warehouse_remove', array($id, $user_id));
	}
	
	public function generate_default_warehouse()
	{
		if (!is_authorized('material/warehouse', 'insert') || !is_authorized('material/warehouse', 'update')) 
			access_denied();
		
		$this->load->library('material/lib_material');
		
		$user_id = $this->session->userdata('user_id');
		
		parent::_execute('lib_material', 'warehouse_default_generate', array($user_id));
	}
}