<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Grid extends MY_Controller 
{
	public function __construct()
	{
		parent::__construct(TRUE, 'backend');
		
		$this->load->library('material/lib_material');
	}
	
	public function index($simple = 0)
	{
		if (!is_authorized('material/grid', 'index')) 
			access_denied();
		
		$m_warehouse_id = $this->input->get_post('m_warehouse_id');
		$data = array(
			'm_warehouse_id'	=> $m_warehouse_id,
			'simple'			=> $simple
		);
		
		if ($simple)
		{
			$this->load->view('material/grid/index', $data);
		}
		else
		{
			$content = array(
				'title'		=> "Kolam",
				'content' 	=> $this->load->view('material/grid/index', $data, TRUE)
			);
			$this->_load_layout($content);
		}
	}
	
	public function form()
	{
		if (!is_authorized('material/grid', 'index')) 
			access_denied();
		
		$form_action = $this->input->get_post('form_action');
		$id = $this->input->get_post('id');
		$m_warehouse_id = $this->input->get_post('m_warehouse_id');
		
		$record = NULL;
		if ($id !== NULL)
		{
			$table = $this->db
				->select("grd.id, grd.code, grd.row, grd.col, grd.level")
				->select("grd.type, grd.length, grd.width, grd.height, grd.status")
				->select("grd.m_warehouse_id")
				->select("grd.m_productgroup_id, prog.code m_productgroup_code, prog.name m_productgroup_name")
				->select_concat(array("prog.name", "' ('", "prog.code", "')'"), 'm_productgroup_text')
				->select("grd.notes")
				->from('m_grids grd')
				->join('m_productgroups prog', "prog.id = grd.m_productgroup_id", 'left')
				->where('grd.id', $id)
				->get();
			if ($table->num_rows() > 0)
				$record = $table->first_row();
			else
				show_error("Kolam not found", 400);
		}
		
		$data = array(
			'form_action'		=> $form_action,
			'm_warehouse_id'	=> $m_warehouse_id,
			'record'			=> $record
		);
		$this->load->view('material/grid/form', $data);
	}
	
	public function get_list_json()
	{
		if (!is_authorized('material/grid', 'index')) 
			access_denied();
		
		$this->db
			->select("grd.id, grd.code, grd.row, grd.col, grd.level")
			->select("grd.type, grd.length, grd.width, grd.height, grd.status")
			->select("grd.m_warehouse_id, wh.code m_warehouse_code, wh.name m_warehouse_name")
			->select("grd.m_productgroup_id, prog.code m_productgroup_code, prog.name m_productgroup_name")
			->select("grd.notes")
			->from('m_grids grd')
			->join('m_warehouses wh', "wh.id = grd.m_warehouse_id")
			->join('m_productgroups prog', "prog.id = grd.m_productgroup_id", 'left');
		
		$m_warehouse_id = $this->input->get_post('m_warehouse_id');
		if ($m_warehouse_id)
		{
			$this->db
				->where('grd.m_warehouse_id', $m_warehouse_id);
		}
		
		parent::_get_list_json();
	}
	
	public function form_productgroup()
	{
		if (!is_authorized('material/grid', 'update')) 
			access_denied();
		
		$ids = $this->input->get_post('ids');
		
		$data = array(
			'ids'	=> $ids
		);
		
		$this->load->view('material/grid/form_productgroup', $data);
	}
	
	public function get_list_productgroup_json()
	{
		if (!is_authorized('material/grid', 'index')) 
			access_denied();
		
		$this->db
			->select("id, code, name")
			->from('m_productgroups');
		
		parent::_get_list_json();
	}
	
	public function get_productgroup_autocomplete_list_json()
	{
		if (!is_authorized('material/grid', 'index')) 
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
		if (!is_authorized('material/grid', 'insert'))
			access_denied();
		
		$user_id = $this->session->userdata('user_id');
		
		$m_warehouse_id = $this->input->post('m_warehouse_id');
		$row = $this->input->post('row');
		$col = $this->input->post('col');
		$level = $this->input->post('level');
		$m_productgroup_id = $this->input->post('m_productgroup_id');
		$type = $this->input->post('type');
		$length = $this->input->post('length');
		$width = $this->input->post('width');
		$height = $this->input->post('height');
		$status = $this->input->post('status');
		$notes = $this->input->post('notes');
		
		parent::_execute('lib_material', 'grid_add', 
			array(
				$m_warehouse_id, 
				$row, $col, $level, $m_productgroup_id, 
				$type, $length, $width, $height, $status,
				$notes, $user_id
			), 
			array(
				array('field' => 'm_warehouse_id', 'label' => 'Warehouse Id', 'rules' => 'required'),
				array('field' => 'row', 'label' => 'Row', 'rules' => 'required|integer'),
				array('field' => 'col', 'label' => 'Col', 'rules' => 'required|integer'),
				array('field' => 'level', 'label' => 'Level', 'rules' => 'required|integer'),
				array('field' => 'length', 'label' => 'Length', 'rules' => 'required|numeric'),
				array('field' => 'width', 'label' => 'Width', 'rules' => 'required|numeric'),
				array('field' => 'height', 'label' => 'Height', 'rules' => 'required|numeric')
			)
		);
	}
	
	public function update($id)
	{
		if (!is_authorized('material/grid', 'insert'))
			access_denied();
		
		$user_id = $this->session->userdata('user_id');
		
		$row = $this->input->post('row');
		$col = $this->input->post('col');
		$level = $this->input->post('level');
		$m_productgroup_id = $this->input->post('m_productgroup_id');
		$type = $this->input->post('type');
		$length = $this->input->post('length');
		$width = $this->input->post('width');
		$height = $this->input->post('height');
		$status = $this->input->post('status');
		$notes = $this->input->post('notes');
		
		parent::_execute('lib_material', 'grid_update', 
			array(
				$id, 
				$row, $col, $level, $m_productgroup_id, 
				$type, $length, $width, $height, $status,
				$notes, $user_id
			), 
			array(
				array('field' => 'row', 'label' => 'Row', 'rules' => 'required|integer'),
				array('field' => 'col', 'label' => 'Col', 'rules' => 'required|integer'),
				array('field' => 'level', 'label' => 'Level', 'rules' => 'required|integer'),
				array('field' => 'length', 'label' => 'Length', 'rules' => 'required|numeric'),
				array('field' => 'width', 'label' => 'Width', 'rules' => 'required|numeric'),
				array('field' => 'height', 'label' => 'Height', 'rules' => 'required|numeric')
			)
		);
	}
	
	public function set_productgroup_by_ids()
	{
		if (!is_authorized('material/grid', 'update')) 
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
			foreach ($ids as $id)
			{
				$this->lib_material->grid_set_productgroup($id, $m_productgroup_id, $user_id);
			}
		}
	}
	
	public function delete_by_ids()
	{
		if (!is_authorized('material/grid', 'delete')) 
			access_denied();
		
		parent::_execute('this', '_delete_by_ids');
	}
	
	protected function _delete_by_ids()
	{
		$user_id = $this->session->userdata('user_id');
		
		$ids = $this->input->post('ids');
		if (!empty($ids) && is_array($ids))
		{
			foreach ($ids as $id)
			{
				$this->lib_material->grid_remove($id, $user_id);
			}
		}
	}
	
	public function delete($id)
	{
		if (!is_authorized('material/grid', 'delete')) 
			access_denied();
		
		$user_id = $this->session->userdata('user_id');
		
		parent::_execute('lib_material', 'grid_remove', array($id, $user_id));
	}
}