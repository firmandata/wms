<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Asset_move extends MY_Controller 
{
	public function __construct()
	{
		parent::__construct(TRUE, 'backend');
	}
	
	public function index()
	{
		if (!is_authorized('asset/asset_move', 'index')) 
			access_denied();
		
		$data = array(
		);
		
		$content = array(
			'title'		=> "Move",
			'content' 	=> $this->load->view('asset/asset_move/index', $data, TRUE)
		);
		$this->_load_layout($content);
	}
	
	public function get_list_json()
	{
		if (!is_authorized('asset/asset_move', 'index')) 
			access_denied();
		
		$this->load->helper('date');
		
		$from_month = $this->input->get_post('from_month');
		$from_year = $this->input->get_post('from_year');
		
		$to_month = $this->input->get_post('to_month');
		$to_year = $this->input->get_post('to_year');
		
		$this->db
			->select("am.id, am.code, am.move_date")
			->select("amd.c_locationfrom_id, locf.code c_locationfrom_code, locf.name c_locationfrom_name")
			->select("amd.c_locationto_id, loct.code c_locationto_code, loct.name c_locationto_name")
			->select_if_null("COUNT(DISTINCT amd.a_asset_id)", 0, 'asset')
			->from('a_asset_moves am')
			->join('a_asset_movedetails amd', "amd.a_asset_move_id = am.id", 'left')
			->join('c_locations locf', "locf.id = amd.c_locationfrom_id", 'left')
			->join('c_locations loct', "loct.id = amd.c_locationto_id", 'left')
			->group_by(
				array(
					'am.id', 'am.code', 'am.move_date',
					'amd.c_locationfrom_id', 'locf.code', 'locf.name',
					'amd.c_locationto_id', 'loct.code', 'loct.name'
				)
			);
		$this->db->where("am.move_date >=", date('Y-m-d', mktime(0, 0, 0, $from_month, 1, $from_year)));
		$this->db->where("am.move_date <=", add_date(date('Y-m-d', mktime(0, 0, 0, $to_month, 1, $to_year)), -1, 1));
		
		parent::_get_list_json();
	}
	
	public function get_list_detail_json()
	{
		if (!is_authorized('asset/asset_move', 'index')) 
			access_denied();
		
		$this->db
			->select("amd.id, amd.a_asset_move_id")
			->select("amd.a_asset_id, ast.code a_asset_code, ast.name a_asset_name")
			->select("amd.c_locationfrom_id, locf.code c_locationfrom_code, locf.name c_locationfrom_name")
			->select("amd.c_locationto_id, loct.code c_locationto_code, loct.name c_locationto_name")
			->select("ast.m_product_id, pro.code m_product_code, pro.name m_product_name")
			->select("amd.created")
			->from('a_asset_movedetails amd')
			->join('a_assets ast', "ast.id = amd.a_asset_id")
			->join('c_locations locf', "locf.id = amd.c_locationfrom_id")
			->join('c_locations loct', "loct.id = amd.c_locationto_id")
			->join('m_products pro', "pro.id = ast.m_product_id");
		
		$id = $this->input->get_post('id');
		if ($id !== '')
			$this->db->where("amd.a_asset_move_id", $id);
		
		parent::_get_list_json();
	}
	
	public function form()
	{
		if (!is_authorized('asset/asset_move', 'index')) 
			access_denied();
		
		$form_action = $this->input->get_post('form_action');
		$id = $this->input->get_post('id');
		$record = NULL;
		if ($id !== NULL)
		{
			$this->db
				->select("am.id, am.code, am.move_date, am.notes")
				->from('a_asset_moves am')
				->where('am.id', $id);
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
		$this->load->view('asset/asset_move/form', $data);
	}
	
	public function form_detail()
	{
		if (!is_authorized('asset/asset_move', 'index')) 
			access_denied();
		
		$id = $this->input->get_post('id');
		
		$data = array(
			'id'	=> $id
		);
		$this->load->view('asset/asset_move/form_detail', $data);
	}
	
	public function detail()
	{
		if (!is_authorized('asset/asset_move', 'index')) 
			access_denied();
		
		$id = $this->input->get_post('id');
		$this->db
			->select("am.id, am.code, am.move_date, am.notes")
			->from('a_asset_moves am')
			->where('am.id', $id);
		$table = $this->db->get();
		if ($table->num_rows() > 0)
			$record = $table->first_row();
		else
			show_error("Move not found", 400);
		
		$data = array(
			'record'	=> $record
		);
		$this->load->view('asset/asset_move/detail', $data);
	}
	
	public function get_list_detail_full_json()
	{
		if (!is_authorized('asset/asset_move', 'index')) 
			access_denied();
		
		$this->db
			->select("amd.id, amd.a_asset_move_id")
			->select("amd.a_asset_id, ast.code a_asset_code, ast.name a_asset_name")
			->select("amd.c_locationfrom_id, locf.code c_locationfrom_code, locf.name c_locationfrom_name")
			->select("amd.c_locationto_id, loct.code c_locationto_code, loct.name c_locationto_name")
			->select("ast.m_product_id, pro.code m_product_code, pro.name m_product_name")
			->select("amd.created")
			->from('a_asset_movedetails amd')
			->join('a_assets ast', "ast.id = amd.a_asset_id")
			->join('c_locations locf', "locf.id = amd.c_locationfrom_id")
			->join('c_locations loct', "loct.id = amd.c_locationto_id")
			->join('m_products pro', "pro.id = ast.m_product_id");
		
		$id = $this->input->get_post('id');
		if ($id !== '')
			$this->db->where("amd.a_asset_move_id", $id);
		
		parent::_get_list_json();
	}
	
	public function get_asset_autocomplete_list_json()
	{
		if (!is_authorized('asset/asset_move', 'index')) 
			access_denied();
		
		$keywords = $this->input->get_post('term');
		
		$this->db
			->select("ast.code id")
			->select("ast.code value")
			->select_concat(array("ast.name", "' ('", "ast.code", "')'"), 'label')
			->select("ast.name")
			->select("ast.m_product_id, pro.code m_product_code, pro.name m_product_name")
			->select("ast.c_location_id, loc.code c_location_code, loc.name c_location_name")
			->from('a_assets ast')
			->join('m_products pro', "pro.id = ast.m_product_id")
			->join('c_locations loc', "loc.id = ast.c_location_id");
		
		if ($keywords)
			$this->db->where("(ast.code LIKE '%" . $this->db->escape_like_str($keywords) . "%' OR ast.name LIKE '%" . $this->db->escape_like_str($keywords) . "%')", NULL, FALSE);
		
		parent::_get_list_autocomplete_json();
	}
	
	public function get_product_autocomplete_list_json()
	{
		if (!is_authorized('asset/asset_move', 'index')) 
			access_denied();
		
		$keywords = $this->input->get_post('term');
		
		$this->db
			->select("pro.code id")
			->select("pro.code value")
			->select_concat(array("pro.name", "' ('", "pro.code", "')'"), 'label')
			->select("pro.name")
			->from('m_products pro');
		
		if ($keywords)
			$this->db->where("(pro.code LIKE '%" . $this->db->escape_like_str($keywords) . "%' OR pro.name LIKE '%" . $this->db->escape_like_str($keywords) . "%')", NULL, FALSE);
		
		parent::_get_list_autocomplete_json();
	}
	
	public function get_location_autocomplete_list_json()
	{
		if (!is_authorized('asset/asset_move', 'index')) 
			access_denied();
		
		$keywords = $this->input->get_post('term');
		
		$this->db
			->select("loc.code id")
			->select("loc.code value")
			->select_concat(array("loc.name", "' ('", "loc.code", "')'"), 'label')
			->select("loc.name")
			->from('c_locations loc');
		
		if ($keywords)
			$this->db->where("(loc.code LIKE '%" . $this->db->escape_like_str($keywords) . "%' OR loc.name LIKE '%" . $this->db->escape_like_str($keywords) . "%')", NULL, FALSE);
		
		parent::_get_list_autocomplete_json();
	}
	
	public function insert()
	{
		if (!is_authorized('asset/asset_move', 'insert')) 
			access_denied();
		
		$this->load->library('asset/lib_asset_operation');
		
		$user_id = $this->session->userdata('user_id');
		
		$data = new stdClass();
		$data->code = $this->input->post('code');
		$data->move_date = $this->input->post('move_date');
		$data->notes = $this->input->post('notes');
		
		parent::_execute('lib_asset_operation', 'move_add', 
			array($data, $user_id),
			array(
				array('field' => 'code', 'label' => 'No', 'rules' => 'required'),
				array('field' => 'move_date', 'label' => 'Date', 'rules' => 'required')
			)
		);
	}
	
	public function insert_detail()
	{
		if (!is_authorized('asset/asset_move', 'insert')) 
			access_denied();
		
		parent::_execute('this', '_insert_detail', 
			array(),
			array(
				array('field' => 'a_asset_move_id', 'label' => 'Asset Move', 'rules' => 'integer|required'),
				array('field' => 'code', 'label' => 'Asset', 'rules' => 'required'),
				array('field' => 'c_locationto_code', 'label' => 'Location To', 'rules' => 'required')
			)
		);
	}
	
	protected function _insert_detail()
	{
		$user_id = $this->session->userdata('user_id');
		
		$this->load->library('asset/lib_asset_operation');
		
		$data = new stdClass();
		$data->a_asset_move_id = $this->input->post('a_asset_move_id');
		$code = $this->input->post('code');
		if ($code !== '')
			$data->code = $code;
		
		$m_product_code = $this->input->post('m_product_code');
		if ($m_product_code !== NULL && $m_product_code !== '')
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
		
		$c_locationfrom_code = $this->input->post('c_locationfrom_code');
		if ($c_locationfrom_code !== NULL && $c_locationfrom_code !== '')
		{
			$table = $this->db
				->select('id')
				->from('c_locations')
				->where('code', $c_locationfrom_code)
				->get();
			if ($table->num_rows() > 0)
			{
				$table_record = $table->first_row();
				$data->c_locationfrom_id = $table_record->id;
			}
		}
		
		$c_locationto_code = $this->input->post('c_locationto_code');
		if ($c_locationto_code !== '')
		{
			$table = $this->db
				->select('id')
				->from('c_locations')
				->where('code', $c_locationto_code)
				->get();
			if ($table->num_rows() > 0)
			{
				$table_record = $table->first_row();
				$data->c_locationto_id = $table_record->id;
			}
		}
		
		if (	empty($data->code)
			 && empty($data->m_product_id)
			 && empty($data->c_locationfrom_id))
		{
			throw new Exception("Please entry the source criteria.");
		}
		
		if (	empty($data->c_locationto_id))
		{
			throw new Exception("Please entry the target criteria.");
		}
		
		$this->lib_asset_operation->movedetail_add_by_properties($data, $user_id);
	}
	
	public function update($id)
	{
		if (!is_authorized('asset/asset_move', 'update')) 
			access_denied();
		
		$this->load->library('asset/lib_asset_operation');
		
		$user_id = $this->session->userdata('user_id');
		
		$data = new stdClass();
		$data->code = $this->input->post('code');
		$data->move_date = $this->input->post('move_date');
		$data->notes = $this->input->post('notes');
		
		parent::_execute('lib_asset_operation', 'move_update', 
			array($id, $data, $user_id), 
			array(
				array('field' => 'code', 'label' => 'No', 'rules' => 'required'),
				array('field' => 'move_date', 'label' => 'Date', 'rules' => 'required')
			)
		);
	}
	
	public function delete($id)
	{
		if (!is_authorized('asset/asset_move', 'delete')) 
			access_denied();
		
		$this->load->library('asset/lib_asset_operation');
		
		$user_id = $this->session->userdata('user_id');
		
		parent::_execute('lib_asset_operation', 'move_remove', array($id, $user_id));
	}
	
	public function delete_detail()
	{
		if (!is_authorized('asset/asset_move', 'delete')) 
			access_denied();
		
		parent::_execute('this', '_delete_detail', 
			array(),
			array(
				array('field' => 'a_asset_move_id', 'label' => 'Asset Move', 'rules' => 'integer|required')
			)
		);
	}
	
	protected function _delete_detail()
	{
		if (!is_authorized('asset/asset_move', 'delete')) 
			access_denied();
		
		$this->load->library('asset/lib_asset_operation');
		
		$user_id = $this->session->userdata('user_id');
		
		$data = new stdClass();
		$data->a_asset_move_id = $this->input->post('a_asset_move_id');
		$data->id = $this->input->post('id');
		$data->a_asset_id = $this->input->post('a_asset_id');
		$data->c_locationfrom_id = $this->input->post('c_locationfrom_id');
		$data->c_locationto_id = $this->input->post('c_locationto_id');
		
		if (	empty($data->id)
			 && empty($data->a_asset_id)
			 && empty($data->c_locationfrom_id)
			 && empty($data->c_locationto_id))
		{
			throw new Exception("Please entry the criteria.");
		}
		
		$this->lib_asset_operation->movedetail_remove_by_properties($data, $user_id);
	}
}