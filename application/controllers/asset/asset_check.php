<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Asset_check extends MY_Controller 
{
	public function __construct()
	{
		parent::__construct(TRUE, 'backend');
	}
	
	public function index()
	{
		if (!is_authorized('asset/asset_check', 'index')) 
			access_denied();
		
		$data = array(
		);
		
		$content = array(
			'title'		=> "Check",
			'content' 	=> $this->load->view('asset/asset_check/index', $data, TRUE)
		);
		$this->_load_layout($content);
	}
	
	public function get_list_json()
	{
		if (!is_authorized('asset/asset_check', 'index')) 
			access_denied();
		
		$this->load->helper('date');
		
		$from_month = $this->input->get_post('from_month');
		$from_year = $this->input->get_post('from_year');
		
		$to_month = $this->input->get_post('to_month');
		$to_year = $this->input->get_post('to_year');
		
		$this->db
			->select("at.id, at.code, at.check_date")
			->select("atd.c_businesspartner_userfrom_id, bpf.code c_businesspartner_userfrom_code, bpf.name c_businesspartner_userfrom_name")
			->select("atd.c_businesspartner_userto_id, bpt.code c_businesspartner_userto_code, bpt.name c_businesspartner_userto_name")
			->select_if_null("COUNT(DISTINCT atd.a_asset_id)", 0, 'asset')
			->from('a_asset_checks at')
			->join('a_asset_checkdetails atd', "atd.a_asset_check_id = at.id", 'left')
			->join('c_businesspartners bpf', "bpf.id = atd.c_businesspartner_userfrom_id", 'left')
			->join('c_businesspartners bpt', "bpt.id = atd.c_businesspartner_userto_id", 'left')
			->group_by(
				array(
					'at.id', 'at.code', 'at.check_date',
					'atd.c_businesspartner_userfrom_id', 'bpf.code', 'bpf.name',
					'atd.c_businesspartner_userto_id', 'bpt.code', 'bpt.name'
				)
			);
		$this->db->where("at.check_date >=", date('Y-m-d', mktime(0, 0, 0, $from_month, 1, $from_year)));
		$this->db->where("at.check_date <=", add_date(date('Y-m-d', mktime(0, 0, 0, $to_month, 1, $to_year)), -1, 1));
		
		parent::_get_list_json();
	}
	
	public function get_list_detail_json()
	{
		if (!is_authorized('asset/asset_check', 'index')) 
			access_denied();
		
		$this->db
			->select("atd.id, atd.a_asset_check_id")
			->select("atd.a_asset_id, ast.code a_asset_code, ast.name a_asset_name")
			->select("atd.c_businesspartner_userfrom_id, bpf.code c_businesspartner_userfrom_code, bpf.name c_businesspartner_userfrom_name")
			->select("atd.c_businesspartner_userto_id, bpt.code c_businesspartner_userto_code, bpt.name c_businesspartner_userto_name")
			->select("ast.m_product_id, pro.code m_product_code, pro.name m_product_name")
			->select("atd.created")
			->from('a_asset_checkdetails atd')
			->join('a_assets ast', "ast.id = atd.a_asset_id")
			->join('c_businesspartners bpf', "bpf.id = atd.c_businesspartner_userfrom_id")
			->join('c_businesspartners bpt', "bpt.id = atd.c_businesspartner_userto_id")
			->join('m_products pro', "pro.id = ast.m_product_id");
		
		$id = $this->input->get_post('id');
		if ($id !== '')
			$this->db->where("atd.a_asset_check_id", $id);
		
		parent::_get_list_json();
	}
	
	public function form()
	{
		if (!is_authorized('asset/asset_check', 'index')) 
			access_denied();
		
		$form_action = $this->input->get_post('form_action');
		$id = $this->input->get_post('id');
		$record = NULL;
		if ($id !== NULL)
		{
			$this->db
				->select("at.id, at.code, at.check_date, at.notes")
				->from('a_asset_checks at')
				->where('at.id', $id);
			$table = $this->db->get();
			if ($table->num_rows() > 0)
				$record = $table->first_row();
			else
				show_error("Check not found", 400);
		}
		
		$data = array(
			'form_action'	=> $form_action,
			'record'		=> $record
		);
		$this->load->view('asset/asset_check/form', $data);
	}
	
	public function form_detail()
	{
		if (!is_authorized('asset/asset_check', 'index')) 
			access_denied();
		
		$id = $this->input->get_post('id');
		
		$data = array(
			'id'	=> $id
		);
		$this->load->view('asset/asset_check/form_detail', $data);
	}
	
	public function detail()
	{
		if (!is_authorized('asset/asset_check', 'index')) 
			access_denied();
		
		$id = $this->input->get_post('id');
		$this->db
			->select("at.id, at.code, at.check_date, at.notes")
			->from('a_asset_checks at')
			->where('at.id', $id);
		$table = $this->db->get();
		if ($table->num_rows() > 0)
			$record = $table->first_row();
		else
			show_error("Check not found", 400);
		
		$data = array(
			'record'	=> $record
		);
		$this->load->view('asset/asset_check/detail', $data);
	}
	
	public function get_list_detail_full_json()
	{
		if (!is_authorized('asset/asset_check', 'index')) 
			access_denied();
		
		$this->db
			->select("atd.id, atd.a_asset_check_id")
			->select("atd.a_asset_id, ast.code a_asset_code, ast.name a_asset_name")
			->select("atd.c_businesspartner_userfrom_id, bpf.code c_businesspartner_userfrom_code, bpf.name c_businesspartner_userfrom_name")
			->select("atd.c_businesspartner_userto_id, bpt.code c_businesspartner_userto_code, bpt.name c_businesspartner_userto_name")
			->select("ast.m_product_id, pro.code m_product_code, pro.name m_product_name")
			->select("atd.created")
			->from('a_asset_checkdetails atd')
			->join('a_assets ast', "ast.id = atd.a_asset_id")
			->join('c_businesspartners bpf', "bpf.id = atd.c_businesspartner_userfrom_id")
			->join('c_businesspartners bpt', "bpt.id = atd.c_businesspartner_userto_id")
			->join('m_products pro', "pro.id = ast.m_product_id");
		
		$id = $this->input->get_post('id');
		if ($id !== '')
			$this->db->where("atd.a_asset_check_id", $id);
		
		parent::_get_list_json();
	}
	
	public function get_asset_autocomplete_list_json()
	{
		if (!is_authorized('asset/asset_check', 'index')) 
			access_denied();
		
		$keywords = $this->input->get_post('term');
		
		$this->db
			->select("ast.code id")
			->select("ast.code value")
			->select_concat(array("ast.name", "' ('", "ast.code", "')'"), 'label')
			->select("ast.name")
			->from('a_assets ast');
		
		if ($keywords)
			$this->db->where("(ast.code LIKE '%" . $this->db->escape_like_str($keywords) . "%' OR ast.name LIKE '%" . $this->db->escape_like_str($keywords) . "%')", NULL, FALSE);
		
		parent::_get_list_autocomplete_json();
	}
	
	public function get_product_autocomplete_list_json()
	{
		if (!is_authorized('asset/asset_check', 'index')) 
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
	
	public function get_businesspartner_autocomplete_list_json()
	{
		if (!is_authorized('asset/asset_check', 'index')) 
			access_denied();
		
		$keywords = $this->input->get_post('term');
		
		$this->db
			->select("bp.code id")
			->select("bp.code value")
			->select_concat(array("bp.name", "' ('", "bp.code", "')'"), 'label')
			->select("bp.name")
			->from('c_businesspartners bp');
		
		if ($keywords)
			$this->db->where("(bp.code LIKE '%" . $this->db->escape_like_str($keywords) . "%' OR bp.name LIKE '%" . $this->db->escape_like_str($keywords) . "%')", NULL, FALSE);
		
		parent::_get_list_autocomplete_json();
	}
	
	public function insert()
	{
		if (!is_authorized('asset/asset_check', 'insert')) 
			access_denied();
		
		$this->load->library('asset/lib_asset_operation');
		
		$user_id = $this->session->userdata('user_id');
		
		$data = new stdClass();
		$data->code = $this->input->post('code');
		$data->check_date = $this->input->post('check_date');
		$data->notes = $this->input->post('notes');
		
		parent::_execute('lib_asset_operation', 'check_add', 
			array($data, $user_id),
			array(
				array('field' => 'code', 'label' => 'No', 'rules' => 'required'),
				array('field' => 'check_date', 'label' => 'Date', 'rules' => 'required')
			)
		);
	}
	
	public function insert_detail()
	{
		if (!is_authorized('asset/asset_check', 'insert')) 
			access_denied();
		
		parent::_execute('this', '_insert_detail', 
			array(),
			array(
				array('field' => 'a_asset_check_id', 'label' => 'Asset Check', 'rules' => 'integer|required')
			)
		);
	}
	
	protected function _insert_detail()
	{
		$user_id = $this->session->userdata('user_id');
		
		$this->load->library('asset/lib_asset_operation');
		
		$data = new stdClass();
		$data->a_asset_check_id = $this->input->post('a_asset_check_id');
		$code = $this->input->post('code');
		if ($code !== '')
			$data->code = $code;
		
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
		
		$c_businesspartner_userfrom_code = $this->input->post('c_businesspartner_userfrom_code');
		if ($c_businesspartner_userfrom_code !== '')
		{
			$table = $this->db
				->select('id')
				->from('c_businesspartners')
				->where('code', $c_businesspartner_userfrom_code)
				->get();
			if ($table->num_rows() > 0)
			{
				$table_record = $table->first_row();
				$data->c_businesspartner_userfrom_id = $table_record->id;
			}
		}
		
		$c_businesspartner_userto_code = $this->input->post('c_businesspartner_userto_code');
		if ($c_businesspartner_userto_code !== '')
		{
			$table = $this->db
				->select('id')
				->from('c_businesspartners')
				->where('code', $c_businesspartner_userto_code)
				->get();
			if ($table->num_rows() > 0)
			{
				$table_record = $table->first_row();
				$data->c_businesspartner_userto_id = $table_record->id;
			}
		}
		
		if (	empty($data->code)
			 && empty($data->m_product_id)
			 && empty($data->c_businesspartner_userfrom_id))
		{
			throw new Exception("Please entry the source criteria.");
		}
		
		if (	empty($data->c_businesspartner_userto_id))
		{
			throw new Exception("Please entry the target criteria.");
		}
		
		$this->lib_asset_operation->checkdetail_add_by_properties($data, $user_id);
	}
	
	public function update($id)
	{
		if (!is_authorized('asset/asset_check', 'update')) 
			access_denied();
		
		$this->load->library('asset/lib_asset_operation');
		
		$user_id = $this->session->userdata('user_id');
		
		$data = new stdClass();
		$data->code = $this->input->post('code');
		$data->check_date = $this->input->post('check_date');
		$data->notes = $this->input->post('notes');
		
		parent::_execute('lib_asset_operation', 'check_update', 
			array($id, $data, $user_id), 
			array(
				array('field' => 'code', 'label' => 'No', 'rules' => 'required'),
				array('field' => 'check_date', 'label' => 'Date', 'rules' => 'required')
			)
		);
	}
	
	public function delete($id)
	{
		if (!is_authorized('asset/asset_check', 'delete')) 
			access_denied();
		
		$this->load->library('asset/lib_asset_operation');
		
		$user_id = $this->session->userdata('user_id');
		
		parent::_execute('lib_asset_operation', 'check_remove', array($id, $user_id));
	}
	
	public function delete_detail()
	{
		if (!is_authorized('asset/asset_check', 'delete')) 
			access_denied();
		
		parent::_execute('this', '_delete_detail', 
			array(),
			array(
				array('field' => 'a_asset_check_id', 'label' => 'Asset Check', 'rules' => 'integer|required')
			)
		);
	}
	
	protected function _delete_detail()
	{
		if (!is_authorized('asset/asset_check', 'delete')) 
			access_denied();
		
		$this->load->library('asset/lib_asset_operation');
		
		$user_id = $this->session->userdata('user_id');
		
		$data = new stdClass();
		$data->a_asset_check_id = $this->input->post('a_asset_check_id');
		$data->id = $this->input->post('id');
		$data->a_asset_id = $this->input->post('a_asset_id');
		$data->c_businesspartner_userfrom_id = $this->input->post('c_businesspartner_userfrom_id');
		$data->c_businesspartner_userto_id = $this->input->post('c_businesspartner_userto_id');
		
		if (	empty($data->id)
			 && empty($data->a_asset_id)
			 && empty($data->c_businesspartner_userfrom_id)
			 && empty($data->c_businesspartner_userto_id))
		{
			throw new Exception("Please entry the criteria.");
		}
		
		$this->lib_asset_operation->checkdetail_remove_by_properties($data, $user_id);
	}
}