<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Asset_transfer extends MY_Controller 
{
	public function __construct()
	{
		parent::__construct(TRUE, 'backend');
	}
	
	public function index()
	{
		if (!is_authorized('asset/asset_transfer', 'index')) 
			access_denied();
		
		$data = array(
		);
		
		$content = array(
			'title'		=> "Transfer",
			'content' 	=> $this->load->view('asset/asset_transfer/index', $data, TRUE)
		);
		$this->_load_layout($content);
	}
	
	public function get_list_json()
	{
		if (!is_authorized('asset/asset_transfer', 'index')) 
			access_denied();
		
		$this->load->helper('date');
		
		$from_month = $this->input->get_post('from_month');
		$from_year = $this->input->get_post('from_year');
		
		$to_month = $this->input->get_post('to_month');
		$to_year = $this->input->get_post('to_year');
		
		$this->db
			->select("at.id, at.code, at.transfer_date")
			->select("atd.c_businesspartner_userfrom_id, bpf.code c_businesspartner_userfrom_code, bpf.name c_businesspartner_userfrom_name")
			->select("atd.c_businesspartner_userto_id, bpt.code c_businesspartner_userto_code, bpt.name c_businesspartner_userto_name")
			->select("atd.c_departmentfrom_id, depf.code c_departmentfrom_code, depf.name c_departmentfrom_name")
			->select("atd.c_departmentto_id, dept.code c_departmentto_code, dept.name c_departmentto_name")
			->select_if_null("COUNT(DISTINCT atd.a_asset_id)", 0, 'asset')
			->from('a_asset_transfers at')
			->join('a_asset_transferdetails atd', "atd.a_asset_transfer_id = at.id", 'left')
			->join('c_businesspartners bpf', "bpf.id = atd.c_businesspartner_userfrom_id", 'left')
			->join('c_businesspartners bpt', "bpt.id = atd.c_businesspartner_userto_id", 'left')
			->join('c_departments depf', "depf.id = atd.c_departmentfrom_id", 'left')
			->join('c_departments dept', "dept.id = atd.c_departmentto_id", 'left')
			->group_by(
				array(
					'at.id', 'at.code', 'at.transfer_date',
					'atd.c_businesspartner_userfrom_id', 'bpf.code', 'bpf.name',
					'atd.c_businesspartner_userto_id', 'bpt.code', 'bpt.name',
					'atd.c_departmentfrom_id', 'depf.code', 'depf.name',
					'atd.c_departmentto_id', 'dept.code', 'dept.name'
				)
			);
		$this->db->where("at.transfer_date >=", date('Y-m-d', mktime(0, 0, 0, $from_month, 1, $from_year)));
		$this->db->where("at.transfer_date <=", add_date(date('Y-m-d', mktime(0, 0, 0, $to_month, 1, $to_year)), -1, 1));
		
		parent::_get_list_json();
	}
	
	public function get_list_detail_json()
	{
		if (!is_authorized('asset/asset_transfer', 'index')) 
			access_denied();
		
		$this->db
			->select("atd.id, atd.a_asset_transfer_id")
			->select("atd.a_asset_id, ast.code a_asset_code, ast.name a_asset_name")
			->select("atd.c_businesspartner_userfrom_id, bpf.code c_businesspartner_userfrom_code, bpf.name c_businesspartner_userfrom_name")
			->select("atd.c_businesspartner_userto_id, bpt.code c_businesspartner_userto_code, bpt.name c_businesspartner_userto_name")
			->select("atd.c_departmentfrom_id, depf.code c_departmentfrom_code, depf.name c_departmentfrom_name")
			->select("atd.c_departmentto_id, dept.code c_departmentto_code, dept.name c_departmentto_name")
			->select("ast.m_product_id, pro.code m_product_code, pro.name m_product_name")
			->select("atd.notes, atd.created")
			->from('a_asset_transferdetails atd')
			->join('a_assets ast', "ast.id = atd.a_asset_id")
			->join('c_businesspartners bpf', "bpf.id = atd.c_businesspartner_userfrom_id")
			->join('c_businesspartners bpt', "bpt.id = atd.c_businesspartner_userto_id")
			->join('c_departments depf', "depf.id = atd.c_departmentfrom_id")
			->join('c_departments dept', "dept.id = atd.c_departmentto_id")
			->join('m_products pro', "pro.id = ast.m_product_id");
		
		$id = $this->input->get_post('id');
		if ($id !== '')
			$this->db->where("atd.a_asset_transfer_id", $id);
		
		parent::_get_list_json();
	}
	
	public function form()
	{
		if (!is_authorized('asset/asset_transfer', 'index')) 
			access_denied();
		
		$form_action = $this->input->get_post('form_action');
		$id = $this->input->get_post('id');
		$record = NULL;
		if ($id !== NULL)
		{
			$this->db
				->select("at.id, at.code, at.transfer_date, at.notes")
				->from('a_asset_transfers at')
				->where('at.id', $id);
			$table = $this->db->get();
			if ($table->num_rows() > 0)
				$record = $table->first_row();
			else
				show_error("Transfer not found", 400);
		}
		
		$data = array(
			'form_action'	=> $form_action,
			'record'		=> $record
		);
		$this->load->view('asset/asset_transfer/form', $data);
	}
	
	public function form_detail()
	{
		if (!is_authorized('asset/asset_transfer', 'index')) 
			access_denied();
		
		$id = $this->input->get_post('id');
		
		$data = array(
			'id'	=> $id
		);
		$this->load->view('asset/asset_transfer/form_detail', $data);
	}
	
	public function detail()
	{
		if (!is_authorized('asset/asset_transfer', 'index')) 
			access_denied();
		
		$id = $this->input->get_post('id');
		$this->db
			->select("at.id, at.code, at.transfer_date, at.notes")
			->from('a_asset_transfers at')
			->where('at.id', $id);
		$table = $this->db->get();
		if ($table->num_rows() > 0)
			$record = $table->first_row();
		else
			show_error("Transfer not found", 400);
		
		$data = array(
			'record'	=> $record
		);
		$this->load->view('asset/asset_transfer/detail', $data);
	}
	
	public function get_list_detail_full_json()
	{
		if (!is_authorized('asset/asset_transfer', 'index')) 
			access_denied();
		
		$this->db
			->select("atd.id, atd.a_asset_transfer_id")
			->select("atd.a_asset_id, ast.code a_asset_code, ast.name a_asset_name")
			->select("atd.c_businesspartner_userfrom_id, bpf.code c_businesspartner_userfrom_code, bpf.name c_businesspartner_userfrom_name")
			->select("atd.c_businesspartner_userto_id, bpt.code c_businesspartner_userto_code, bpt.name c_businesspartner_userto_name")
			->select("atd.c_departmentfrom_id, depf.code c_departmentfrom_code, depf.name c_departmentfrom_name")
			->select("atd.c_departmentto_id, dept.code c_departmentto_code, dept.name c_departmentto_name")
			->select("ast.m_product_id, pro.code m_product_code, pro.name m_product_name")
			->select("atd.notes, atd.created")
			->from('a_asset_transferdetails atd')
			->join('a_assets ast', "ast.id = atd.a_asset_id")
			->join('c_businesspartners bpf', "bpf.id = atd.c_businesspartner_userfrom_id")
			->join('c_businesspartners bpt', "bpt.id = atd.c_businesspartner_userto_id")
			->join('c_departments depf', "depf.id = atd.c_departmentfrom_id")
			->join('c_departments dept', "dept.id = atd.c_departmentto_id")
			->join('m_products pro', "pro.id = ast.m_product_id");
		
		$id = $this->input->get_post('id');
		if ($id !== '')
			$this->db->where("atd.a_asset_transfer_id", $id);
		
		parent::_get_list_json();
	}
	
	public function get_asset_autocomplete_list_json()
	{
		if (!is_authorized('asset/asset_transfer', 'index')) 
			access_denied();
		
		$keywords = $this->input->get_post('term');
		
		$this->db
			->select("ast.code id")
			->select("ast.code value")
			->select_concat(array("ast.name", "' ('", "ast.code", "')'"), 'label')
			->select("ast.name")
			->select("ast.m_product_id, pro.code m_product_code, pro.name m_product_name")
			->select("ast.c_department_id, dep.code c_department_code, dep.name c_department_name")
			->select("ast.c_businesspartner_user_id, bp_user.code c_businesspartner_user_code, bp_user.name c_businesspartner_user_name")
			->from('a_assets ast')
			->join('m_products pro', "pro.id = ast.m_product_id")
			->join('c_departments dep', "dep.id = ast.c_department_id")
			->join('c_businesspartners bp_user', "bp_user.id = ast.c_businesspartner_user_id");
		
		if ($keywords)
			$this->db->where("(ast.code LIKE '%" . $this->db->escape_like_str($keywords) . "%' OR ast.name LIKE '%" . $this->db->escape_like_str($keywords) . "%')", NULL, FALSE);
		
		parent::_get_list_autocomplete_json();
	}
	
	public function get_product_autocomplete_list_json()
	{
		if (!is_authorized('asset/asset_transfer', 'index')) 
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
		if (!is_authorized('asset/asset_transfer', 'index')) 
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
	
	public function get_department_autocomplete_list_json()
	{
		if (!is_authorized('asset/asset_transfer', 'index')) 
			access_denied();
		
		$keywords = $this->input->get_post('term');
		
		$this->db
			->select("dep.code id")
			->select("dep.code value")
			->select_concat(array("dep.name", "' ('", "dep.code", "')'"), 'label')
			->select("dep.name")
			->from('c_departments dep');
		
		if ($keywords)
			$this->db->where("(dep.code LIKE '%" . $this->db->escape_like_str($keywords) . "%' OR dep.name LIKE '%" . $this->db->escape_like_str($keywords) . "%')", NULL, FALSE);
		
		parent::_get_list_autocomplete_json();
	}
	
	public function insert()
	{
		if (!is_authorized('asset/asset_transfer', 'insert')) 
			access_denied();
		
		$this->load->library('asset/lib_asset_operation');
		
		$user_id = $this->session->userdata('user_id');
		
		$data = new stdClass();
		$data->code = $this->input->post('code');
		$data->transfer_date = $this->input->post('transfer_date');
		$data->notes = $this->input->post('notes');
		
		parent::_execute('lib_asset_operation', 'transfer_add', 
			array($data, $user_id),
			array(
				array('field' => 'code', 'label' => 'No', 'rules' => 'required'),
				array('field' => 'transfer_date', 'label' => 'Date', 'rules' => 'required')
			)
		);
	}
	
	public function insert_detail()
	{
		if (!is_authorized('asset/asset_transfer', 'insert')) 
			access_denied();
		
		parent::_execute('this', '_insert_detail', 
			array(),
			array(
				array('field' => 'a_asset_transfer_id', 'label' => 'Asset Transfer', 'rules' => 'integer|required'),
				array('field' => 'code', 'label' => 'Asset', 'rules' => 'required'),
				array('field' => 'c_businesspartner_userto_code', 'label' => 'User To', 'rules' => 'required'),
				array('field' => 'c_departmentto_code', 'label' => 'Department To', 'rules' => 'required')
			)
		);
	}
	
	protected function _insert_detail()
	{
		$user_id = $this->session->userdata('user_id');
		
		$this->load->library('asset/lib_asset_operation');
		
		$data = new stdClass();
		$data->a_asset_transfer_id = $this->input->post('a_asset_transfer_id');
		$code = $this->input->post('code');
		if ($code !== NULL && $code !== '')
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
		
		$c_businesspartner_userfrom_code = $this->input->post('c_businesspartner_userfrom_code');
		if ($c_businesspartner_userfrom_code !== NULL && $c_businesspartner_userfrom_code !== '')
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
		if ($c_businesspartner_userto_code !== NULL && $c_businesspartner_userto_code !== '')
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
		
		$c_departmentto_code = $this->input->post('c_departmentto_code');
		if ($c_departmentto_code !== NULL && $c_departmentto_code !== '')
		{
			$table = $this->db
				->select('id')
				->from('c_departments')
				->where('code', $c_departmentto_code)
				->get();
			if ($table->num_rows() > 0)
			{
				$table_record = $table->first_row();
				$data->c_departmentto_id = $table_record->id;
			}
		}
		
		$notes = $this->input->post('notes');
		if ($notes !== NULL && $notes !== '')
			$data->notes = $notes;
		
		if (	empty($data->code)
			 && empty($data->m_product_id)
			 && empty($data->c_businesspartner_userfrom_id))
		{
			throw new Exception("Please entry the source criteria.");
		}
		
		if (	empty($data->c_businesspartner_userto_id)
			 && empty($data->c_departmentto_id))
		{
			throw new Exception("Please entry the target criteria.");
		}
		
		$this->lib_asset_operation->transferdetail_add_by_properties($data, $user_id);
	}
	
	public function update($id)
	{
		if (!is_authorized('asset/asset_transfer', 'update')) 
			access_denied();
		
		$this->load->library('asset/lib_asset_operation');
		
		$user_id = $this->session->userdata('user_id');
		
		$data = new stdClass();
		$data->code = $this->input->post('code');
		$data->transfer_date = $this->input->post('transfer_date');
		$data->notes = $this->input->post('notes');
		
		parent::_execute('lib_asset_operation', 'transfer_update', 
			array($id, $data, $user_id), 
			array(
				array('field' => 'code', 'label' => 'No', 'rules' => 'required'),
				array('field' => 'transfer_date', 'label' => 'Date', 'rules' => 'required')
			)
		);
	}
	
	public function delete($id)
	{
		if (!is_authorized('asset/asset_transfer', 'delete')) 
			access_denied();
		
		$this->load->library('asset/lib_asset_operation');
		
		$user_id = $this->session->userdata('user_id');
		
		parent::_execute('lib_asset_operation', 'transfer_remove', array($id, $user_id));
	}
	
	public function delete_detail()
	{
		if (!is_authorized('asset/asset_transfer', 'delete')) 
			access_denied();
		
		parent::_execute('this', '_delete_detail', 
			array(),
			array(
				array('field' => 'a_asset_transfer_id', 'label' => 'Asset Transfer', 'rules' => 'integer|required')
			)
		);
	}
	
	protected function _delete_detail()
	{
		if (!is_authorized('asset/asset_transfer', 'delete')) 
			access_denied();
		
		$this->load->library('asset/lib_asset_operation');
		
		$user_id = $this->session->userdata('user_id');
		
		$data = new stdClass();
		$data->a_asset_transfer_id = $this->input->post('a_asset_transfer_id');
		$data->id = $this->input->post('id');
		$data->a_asset_id = $this->input->post('a_asset_id');
		$data->c_businesspartner_userfrom_id = $this->input->post('c_businesspartner_userfrom_id');
		$data->c_businesspartner_userto_id = $this->input->post('c_businesspartner_userto_id');
		$data->c_departmentfrom_id = $this->input->post('c_departmentfrom_id');
		$data->c_departmentto_id = $this->input->post('c_departmentto_id');
		
		if (	empty($data->id)
			 && empty($data->a_asset_id)
			 && empty($data->c_businesspartner_userfrom_id)
			 && empty($data->c_businesspartner_userto_id)
			 && empty($data->c_departmentfrom_id)
			 && empty($data->c_departmentto_id))
		{
			throw new Exception("Please entry the criteria.");
		}
		
		$this->lib_asset_operation->transferdetail_remove_by_properties($data, $user_id);
	}
	
	public function letter()
	{
		if (!is_authorized('asset/asset_transfer', 'index')) 
			access_denied();
		
		$id = $this->input->get_post('id');
		$this->db
			->select("at.id, at.code, at.transfer_date, at.notes")
			->from('a_asset_transfers at')
			->where('at.id', $id);
		$table = $this->db->get();
		if ($table->num_rows() == 0)
			show_error("Transfer not found", 400);
		
		$record = $table->first_row();
		
		$header = new stdClass();
		$header->date = $record->transfer_date;
		
		$details = array();
		
		$table = $this->db
			->select("atd.a_asset_id, ast.code a_asset_code, ast.name a_asset_name")
			->select("ast.m_product_id, pro.code m_product_code, pro.name m_product_name")
			->select("ast.c_region_id, rgn.code c_region_code, rgn.name c_region_name, rgn.address c_region_address, rgn.address_city c_region_address_city, rgn.phone_no c_region_phone_no, rgn.fax_no c_region_fax_no")
			->select("atd.c_businesspartner_userfrom_id, bpf.code c_businesspartner_userfrom_code, bpf.name c_businesspartner_userfrom_name, bpf.personal_position c_businesspartner_userfrom_personal_position")
			->select("atd.c_businesspartner_userto_id, bpt.code c_businesspartner_userto_code, bpt.name c_businesspartner_userto_name, bpt.personal_position c_businesspartner_userto_personal_position")
			->select("atd.c_departmentfrom_id, depf.code c_departmentfrom_code, depf.name c_departmentfrom_name")
			->select("atd.c_departmentto_id, dept.code c_departmentto_code, dept.name c_departmentto_name")
			->from('a_assets ast')
			->join('m_products pro', "pro.id = ast.m_product_id")
			->join('c_regions rgn', "rgn.id = ast.c_region_id")
			->join('a_asset_transferdetails atd', "atd.a_asset_id = ast.id")
			->join('c_businesspartners bpf', "bpf.id = atd.c_businesspartner_userfrom_id")
			->join('c_businesspartners bpt', "bpt.id = atd.c_businesspartner_userto_id")
			->join('c_departments depf', "depf.id = atd.c_departmentfrom_id")
			->join('c_departments dept', "dept.id = atd.c_departmentto_id")
			->where('atd.a_asset_transfer_id', $record->id)
			->get();
		$records = $table->result();
		foreach ($records as $record_idx=>$record)
		{
			$detail = new stdClass();
			$detail->a_asset_code = $record->a_asset_code;
			$detail->a_asset_name = $record->a_asset_name;
			$detail->m_product_code = $record->m_product_code;
			$detail->m_product_name = $record->m_product_name;
			
			$detail->c_region_code = $record->c_region_code;
			$detail->c_region_name = $record->c_region_name;
			$detail->c_region_address = $record->c_region_address;
			$detail->c_region_address_city = $record->c_region_address_city;
			$detail->c_region_phone_no = $record->c_region_phone_no;
			$detail->c_region_fax_no = $record->c_region_fax_no;
			
			$detail->c_businesspartner_userfrom_code = $record->c_businesspartner_userfrom_code;
			$detail->c_businesspartner_userfrom_name = $record->c_businesspartner_userfrom_name;
			$detail->c_businesspartner_userfrom_personal_position = $record->c_businesspartner_userfrom_personal_position;
			$detail->c_departmentfrom_code = $record->c_departmentfrom_code;
			$detail->c_departmentfrom_name = $record->c_departmentfrom_name;
			
			$detail->c_businesspartner_userto_code = $record->c_businesspartner_userto_code;
			$detail->c_businesspartner_userto_name = $record->c_businesspartner_userto_name;
			$detail->c_businesspartner_userto_personal_position = $record->c_businesspartner_userto_personal_position;
			$detail->c_departmentto_code = $record->c_departmentto_code;
			$detail->c_departmentto_name = $record->c_departmentto_name;
			
			$details[] = $detail;
		}
		
		$html = $this->load->view(
			  'asset/asset_transfer/letter'
			, array(
				  'header'	=> $header
				, 'details'	=> $details
			)
			, TRUE
		);
		
		// echo $html;
		$this->load->library('lib_dompdf');
		$this->lib_dompdf->set_footer_notes("Print By : ". $this->session->userdata('name') .", Tgl Print : ". date($this->config->item('server_display_datetime_format'), strtotime(date('YmdHis'))));
		$this->lib_dompdf->load_as_pdf($html, 'transfer_letter.pdf', 'a4', 'portrait');
	}
}