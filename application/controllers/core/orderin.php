<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Orderin extends MY_Controller 
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
		if (!is_authorized('core/orderin', 'index')) 
			access_denied();
		
		$data = array(
		);
		
		$content = array(
			'title'		=> "Order In",
			'content' 	=> $this->load->view('core/orderin/index', $data, TRUE)
		);
		$this->_load_layout($content);
	}
	
	public function get_list_json()
	{
		if (!is_authorized('core/orderin', 'index')) 
			access_denied();
		
		$this->load->helper('date');
		
		$from_month = $this->input->get_post('from_month');
		$from_year = $this->input->get_post('from_year');
		
		$to_month = $this->input->get_post('to_month');
		$to_year = $this->input->get_post('to_year');
		
		$this->db
			->select("oi.id, oi.code, oi.orderin_date")
			->select("oi.origin, oi.bol_no, oi.external_no")
			->select("oi.c_businesspartner_id, bp.name c_businesspartner_name")
			->select("oi.c_project_id, prj.name c_project_name")
			->select("oi.status_inventory_receive")
			->from('c_orderins oi')
			->join('c_businesspartners bp', "bp.id = oi.c_businesspartner_id")
			->join('c_projects prj', "prj.id = oi.c_project_id", 'left');
		$this->db->where("oi.orderin_date >=", date('Y-m-d', mktime(0, 0, 0, $from_month, 1, $from_year)));
		$this->db->where("oi.orderin_date <=", add_date(date('Y-m-d', mktime(0, 0, 0, $to_month, 1, $to_year)), -1, 1));
		
		$this->lib_custom->project_query_filter('oi.c_project_id', $this->c_project_ids);
		
		parent::_get_list_json();
	}
	
	public function form()
	{
		if (!is_authorized('core/orderin', 'index')) 
			access_denied();
		
		$form_action = $this->input->get_post('form_action');
		$id = $this->input->get_post('id');
		$record = NULL;
		if ($id !== NULL)
		{
			$this->db
				->select("oi.id, oi.code, oi.orderin_date")
				->select("oi.origin, oi.bol_no, oi.external_no, oi.notes")
				->select("oi.c_businesspartner_id, bp.code c_businesspartner_code, bp.name c_businesspartner_name")
				->select_concat(array("bp.name", "' ('", "bp.code", "')'"), 'c_businesspartner_text')
				->select("oi.c_project_id, prj.code c_project_code, prj.name c_project_name")
				->select_concat(array("prj.name", "' ('", "prj.code", "')'"), 'c_project_text')
				->select("oi.status_inventory_receive")
				->from('c_orderins oi')
				->join('c_businesspartners bp', "bp.id = oi.c_businesspartner_id")
				->join('c_projects prj', "prj.id = oi.c_project_id", 'left')
				->where('oi.id', $id);
			$this->lib_custom->project_query_filter('oi.c_project_id', $this->c_project_ids);
			$table = $this->db->get();
			if ($table->num_rows() > 0)
			{
				$record = $table->first_row();
				if ($record->status_inventory_receive == 'COMPLETE')
					show_error("Order in was complete", 400);
			}
			else
				show_error("Order in not found", 400);
		}
		
		$c_orderindetails = array();
		if ($id !== FALSE)
		{
			$this->db
				->select("oid.id, oid.quantity_box, oid.quantity, oid.notes")
				->select("oid.m_product_id, pro.code m_product_code, pro.name m_product_name, pro.uom m_product_uom, pro.netto m_product_netto")
				->select_concat(array("pro.name", "' ('", "pro.code", "')'"), 'm_product_text')
				->from('c_orderindetails oid')
				->join('m_products pro', "pro.id = oid.m_product_id")
				->where('oid.c_orderin_id', $id)
				->order_by('oid.id', 'asc');
			$table = $this->db->get();
			$c_orderindetails = $table->result();
		}
		
		$data = array(
			'form_action'		=> $form_action,
			'record'			=> $record,
			'c_orderindetails'	=> $c_orderindetails
		);
		$this->load->view('core/orderin/form', $data);
	}
	
	public function form_upload()
	{
		if (!is_authorized('core/orderin', 'insert')) 
			access_denied();
		
		$form_action = $this->input->get_post('form_action');
		
		$data = array(
			'form_action'	=> $form_action
		);
		$this->load->view('core/orderin/form_upload', $data);
	}
	
	public function detail()
	{
		if (!is_authorized('core/orderin', 'index')) 
			access_denied();
		
		$id = $this->input->get_post('id');
		$this->db
			->select("oi.id, oi.code, oi.orderin_date")
			->select("oi.origin, oi.bol_no, oi.external_no, oi.notes")
			->select("oi.c_businesspartner_id, bp.code c_businesspartner_code, bp.name c_businesspartner_name")
			->select_concat(array("bp.name", "' ('", "bp.code", "')'"), 'c_businesspartner_text')
			->select("oi.c_project_id, prj.code c_project_code, prj.name c_project_name")
			->select_concat(array("prj.name", "' ('", "prj.code", "')'"), 'c_project_text')
			->select("oi.status_inventory_receive")
			->from('c_orderins oi')
			->join('c_businesspartners bp', "bp.id = oi.c_businesspartner_id")
			->join('c_projects prj', "prj.id = oi.c_project_id", 'left')
			->where('oi.id', $id);
		$this->lib_custom->project_query_filter('oi.c_project_id', $this->c_project_ids);
		$table = $this->db->get();
		if ($table->num_rows() > 0)
			$record = $table->first_row();
		else
			show_error("Order in not found", 400);
		
		$this->db
			->select("oid.id, oid.quantity_box, oid.quantity, oid.notes")
			->select("oid.m_product_id, pro.code m_product_code, pro.name m_product_name, pro.uom m_product_uom")
			->select_if_null('ird.quantity_box', 0, 'quantity_box_used')
			->select_if_null('ird.quantity', 0, 'quantity_used')
			->select("oid.status_inventory_receive")
			->from('c_orderindetails oid')
			->join('m_products pro', "pro.id = oid.m_product_id")
			->join(
				 "(SELECT c_orderindetail_id, "
				."		  " . $this->db->if_null("SUM(quantity_box)", 0) . " quantity_box, "
				."		  " . $this->db->if_null("SUM(quantity)", 0) . " quantity "
				."	 FROM m_inventory_receivedetails "
				."  GROUP BY c_orderindetail_id "
				.") ird", 
				"ird.c_orderindetail_id = oid.id", 'left')
			->where('oid.c_orderin_id', $id)
			->order_by('oid.id', 'asc');
		$table = $this->db->get();
		$c_orderindetails = $table->result();
		
		$data = array(
			'record'			=> $record,
			'c_orderindetails'	=> $c_orderindetails
		);
		$this->load->view('core/orderin/detail', $data);
	}
	
	public function get_businesspartner_autocomplete_list_json()
	{
		if (!is_authorized('core/orderin', 'index')) 
			access_denied();
		
		$keywords = $this->input->get_post('term');
		
		$this->db
			->select("id")
			->select_concat(array("name", "' ('", "code", "')'"), 'value')
			->select_concat(array("name", "' ('", "code", "')'"), 'label')
			->from('c_businesspartners');
		
		if ($keywords)
			$this->db->where($this->db->concat(array("name", "' ('", "code", "')'")) . " LIKE '%" . $this->db->escape_like_str($keywords) . "%'", NULL, FALSE);
		
		parent::_get_list_autocomplete_json();
	}
	
	public function get_project_autocomplete_list_json()
	{
		if (!is_authorized('core/orderin', 'index')) 
			access_denied();
		
		$keywords = $this->input->get_post('term');
		
		$this->db
			->select("id")
			->select_concat(array("name", "' ('", "code", "')'"), 'value')
			->select_concat(array("name", "' ('", "code", "')'"), 'label')
			->from('c_projects');
		
		if ($keywords)
			$this->db->where($this->db->concat(array("name", "' ('", "code", "')'")) . " LIKE '%" . $this->db->escape_like_str($keywords) . "%'", NULL, FALSE);
		
		$this->lib_custom->project_query_filter('id', $this->c_project_ids);
		
		parent::_get_list_autocomplete_json();
	}
	
	public function get_product_autocomplete_list_json()
	{
		if (!is_authorized('core/orderin', 'index')) 
			access_denied();
		
		$keywords = $this->input->get_post('term');
		
		$this->db
			->select("id")
			->select_concat(array("name", "' ('", "code", "')'"), 'value')
			->select_concat(array("name", "' ('", "code", "')'"), 'label')
			->select("netto")
			->from('m_products');
		
		if ($keywords)
			$this->db->where($this->db->concat(array("name", "' ('", "code", "')'")) . "LIKE '%" . $this->db->escape_like_str($keywords) . "%'", NULL, FALSE);
		
		parent::_get_list_autocomplete_json();
	}
	
	public function insert()
	{
		if (!is_authorized('core/orderin', 'insert')) 
			access_denied();
		
		parent::_execute('this', 'add_orderin_and_details', 
			array(),
			array(
				array('field' => 'code', 'label' => 'No', 'rules' => 'required'),
				array('field' => 'c_businesspartner_id', 'label' => 'Business Partner', 'rules' => 'required'),
				array('field' => 'orderin_date', 'label' => 'Date', 'rules' => 'required')
			)
		);
	}
	
	protected function add_orderin_and_details()
	{
		$this->load->library('core/lib_order');
		
		$user_id = $this->session->userdata('user_id');
		$c_businesspartner_id = $this->input->post('c_businesspartner_id');
		$c_project_id = $this->input->post('c_project_id');
		
		$data_header = new stdClass();
		$data_header->c_businesspartner_id = $c_businesspartner_id;
		$data_header->c_project_id = $c_project_id;
		$data_header->code = $this->input->post('code');
		$data_header->orderin_date = $this->input->post('orderin_date');
		$data_header->origin = $this->input->post('origin');
		$data_header->bol_no = $this->input->post('bol_no');
		$data_header->external_no = $this->input->post('external_no');
		$data_header->notes = $this->input->post('notes');
		$id = $this->lib_order->orderin_add($data_header, $user_id);
		
		$c_orderindetails = $this->input->post('c_orderindetails');
		if (!is_array($c_orderindetails))
			$c_orderindetails = array();
		
		// -- Add Order In Details --
		if (!empty($c_orderindetails) && is_array($c_orderindetails))
		{
			foreach ($c_orderindetails as $c_orderindetail)
			{
				$data_detail = new stdClass();
				$data_detail->c_orderin_id = $id;
				$data_detail->m_product_id = $c_orderindetail['m_product_id'];
				$data_detail->quantity_box = $c_orderindetail['quantity_box'];
				$data_detail->quantity = $c_orderindetail['quantity'];
				$data_detail->notes = $c_orderindetail['notes'];
				$this->lib_order->orderindetail_add($data_detail, $user_id);
			}
		}
		
		return $id;
	}
	
	public function update($id)
	{
		if (!is_authorized('core/orderin', 'update')) 
			access_denied();
		
		parent::_execute('this', 'update_orderin_and_details', 
			array($id), 
			array(
				array('field' => 'code', 'label' => 'No', 'rules' => 'required'),
				array('field' => 'c_businesspartner_id', 'label' => 'Business Partner', 'rules' => 'required'),
				array('field' => 'orderin_date', 'label' => 'Date', 'rules' => 'required')
			)
		);
	}
	
	protected function update_orderin_and_details($id)
	{
		$this->load->library('core/lib_order');
		
		$user_id = $this->session->userdata('user_id');
		$c_businesspartner_id = $this->input->post('c_businesspartner_id');
		$c_project_id = $this->input->post('c_project_id');
		
		$data_header = new stdClass();
		$data_header->c_businesspartner_id = $c_businesspartner_id;
		$data_header->c_project_id = $c_project_id;
		$data_header->code = $this->input->post('code');
		$data_header->orderin_date = $this->input->post('orderin_date');
		$data_header->origin = $this->input->post('origin');
		$data_header->bol_no = $this->input->post('bol_no');
		$data_header->external_no = $this->input->post('external_no');
		$data_header->notes = $this->input->post('notes');
		$updated_result = $this->lib_order->orderin_update($id, $data_header, $user_id);
		
		// -- Order In Detail --
		
		$c_orderindetails = $this->input->post('c_orderindetails');
		if (!is_array($c_orderindetails))
			$c_orderindetails = array();
		
		$table = $this->db
			->where('c_orderin_id', $id)
			->get('c_orderindetails');
		$c_orderindetails_existing = $table->result();
		
		// -- Add/Modify Order In Detail --
		foreach ($c_orderindetails as $c_orderindetail)
		{
			$is_found_new = TRUE;
			foreach ($c_orderindetails_existing as $c_orderindetail_existing)
			{
				if ($c_orderindetail_existing->id == $c_orderindetail['id'])
				{
					$is_found_new = FALSE;
					break;
				}
			}
			$data_detail = new stdClass();
			$data_detail->notes = $c_orderindetail['notes'];
			$data_detail->m_product_id = $c_orderindetail['m_product_id'];
			$data_detail->quantity_box = $c_orderindetail['quantity_box'];
			$data_detail->quantity = $c_orderindetail['quantity'];
			if ($is_found_new == TRUE)
			{
				$data_detail->c_orderin_id = $id;
				$this->lib_order->orderindetail_add($data_detail, $user_id);
			}
			else
			{
				$this->lib_order->orderindetail_update($c_orderindetail['id'], $data_detail, $user_id);
			}
		}
		
		// -- Remove Order In Detail --
		foreach ($c_orderindetails_existing as $c_orderindetail_existing)
		{
			$is_found_delete = TRUE;
			foreach ($c_orderindetails as $c_orderindetail)
			{
				if ($c_orderindetail['id'] == $c_orderindetail_existing->id)
				{
					$is_found_delete = FALSE;
					break;
				}
			}
			if ($is_found_delete == TRUE)
			{
				$this->lib_order->orderindetail_remove($c_orderindetail_existing->id, $user_id);
			}
		}
		
		return $updated_result;
	}
	
	public function delete($id)
	{
		if (!is_authorized('core/orderin', 'delete')) 
			access_denied();
		
		$this->load->library('core/lib_order');
		
		$user_id = $this->session->userdata('user_id');
		
		parent::_execute('lib_order', 'orderin_remove', array($id, $user_id));
	}

	public function upload()
	{
		if (!is_authorized('core/orderin', 'insert'))
			access_denied();
		
		$user_id = $this->session->userdata('user_id');
		
		$result = new stdClass();
		$result->response = FALSE;
		$result->value = NULL;
		$result->data = array();
		
		$this->load->helper('file');
		$file_element = 'file';
		$file_full_name = $_FILES[$file_element]['name'];
		$file_name = get_file_without_extention($file_full_name).'_'. date('YmdHis') .'_'.$user_id.'.'.get_extention($file_full_name);
		
		$action_source = $this->input->get_post('action');
		
		$config = array(
			'upload_path'	=> './upload/wms/order_in',
			'allowed_types'	=> 'xls|xlsx',
			'file_name'		=> $file_name,
			'overwrite'		=> TRUE
		);
		
		$this->load->library('upload', $config);
		if (!$this->upload->do_upload($file_element))
		{
			$result->value = $this->upload->display_errors();
			$result->response = FALSE;
		}
		else
		{
			$file_data = $this->upload->data();
			
			$this->load->library('excel');
			try
			{
				$excel_sheets = $this->excel->read_file($file_data['full_path']);
				
				$sheet_error_messages = array();
				
				$c_orders = array();
				foreach ($excel_sheets as $excel_sheet_name=>$excel_sheet)
				{
					try
					{
						$c_order = new stdClass();
						
						$c_order->code = $this->excel->read_value($excel_sheet, 3, 2);
						
						$c_businesspartner_code = $this->excel->read_value($excel_sheet, 4, 2);
						$table = $this->db
							->select('id')
							->from('c_businesspartners')
							->where('code', $c_businesspartner_code)
							->get();
						if ($table->num_rows() == 0)
							throw new Exception("Business partner with code '".$c_businesspartner_code." not found.'");
						$table_record = $table->first_row();
						$c_order->c_businesspartner_id = $table_record->id;
						
						$c_order->orderin_date = $this->excel->read_value($excel_sheet, 5, 2, 'date');
						$c_order->notes = $this->excel->read_value($excel_sheet, 6, 2);
						
						$c_project_code = $this->excel->read_value($excel_sheet, 3, 6);
						$table = $this->db
							->select('id')
							->from('c_projects')
							->where('code', $c_project_code)
							->get();
						if ($table->num_rows() == 0)
							throw new Exception("Project with code '".$c_project_code." not found.'");
						$table_record = $table->first_row();
						$c_order->c_project_id = $table_record->id;
						
						$c_order->origin = $this->excel->read_value($excel_sheet, 4, 6);
						$c_order->bol_no = $this->excel->read_value($excel_sheet, 5, 6);
						$c_order->external_no = $this->excel->read_value($excel_sheet, 6, 6);
						
						$c_order->c_orderindetails = array();
						for ($c_orderindetail_count = 11; $c_orderindetail_count < count($excel_sheet); $c_orderindetail_count++)
						{
							$c_orderindetail = new stdClass();
							
							$m_product_code = $this->excel->read_value($excel_sheet, $c_orderindetail_count, 1);
							$c_orderindetail->quantity_box = $this->excel->read_value($excel_sheet, $c_orderindetail_count, 3);
							$c_orderindetail->quantity = $this->excel->read_value($excel_sheet, $c_orderindetail_count, 4);
							$c_orderindetail->notes = $this->excel->read_value($excel_sheet, $c_orderindetail_count, 6);
							
							if (empty($m_product_code) && empty($c_orderindetail->quantity_box) && empty($c_orderindetail->quantity))
								continue;
							
							$table = $this->db
								->select('id')
								->from('m_products')
								->where('code', $m_product_code)
								->get();
							if ($table->num_rows() == 0)
								throw new Exception("Product with code '".$m_product_code." not found.'");
							$table_record = $table->first_row();
							$c_orderindetail->m_product_id = $table_record->id;
							
							$c_order->c_orderindetails[] = $c_orderindetail;
						}
						
						$c_orders[] = $c_order;
					}
					catch(Exception $e)
					{
						$sheet_error_messages[$excel_sheet_name] = $e->getMessage();
					}
				}
				
				$error_messages = array();
				foreach ($sheet_error_messages as $sheet_error_messages_sheet=>$sheet_error_messages_message)
					$error_messages[] = "Sheet '".$sheet_error_messages_sheet."', ".$sheet_error_messages_message;
				if (count($error_messages) > 0)
					$result->value = implode(', ', $error_messages);
				
				if (count($c_orders) > 0)
				{
					$this->load->library('core/lib_order');
					
					$this->db->trans_begin();
					try
					{
						foreach ($c_orders as $c_order)
						{
							$data_header = new stdClass();
							$data_header->c_businesspartner_id = $c_order->c_businesspartner_id;
							$data_header->c_project_id = $c_order->c_project_id;
							$data_header->code = $c_order->code;
							$data_header->orderin_date = $c_order->orderin_date;
							$data_header->origin = $c_order->origin;
							$data_header->bol_no = $c_order->bol_no;
							$data_header->external_no = $c_order->external_no;
							$data_header->notes = $c_order->notes;
							$id = $this->lib_order->orderin_add($data_header, $user_id);
							
							foreach ($c_order->c_orderindetails as $c_orderindetail)
							{
								$data_detail = new stdClass();
								$data_detail->c_orderin_id = $id;
								$data_detail->m_product_id = $c_orderindetail->m_product_id;
								$data_detail->quantity_box = $c_orderindetail->quantity_box;
								$data_detail->quantity = $c_orderindetail->quantity;
								$data_detail->notes = $c_orderindetail->notes;
								$this->lib_order->orderindetail_add($data_detail, $user_id);
							}
						}
						
						if ($this->db->trans_status() === FALSE)
						{
							$error = $this->db->error();
							throw new Exception("Error Database, Code : " . $error['code'] . " Message : " . $error['message']);
						}
						else
						{
							$this->db->trans_commit();
							$result->response = TRUE;
						}
					}
					catch(Exception $e)
					{
						$this->db->trans_rollback();
						throw new Exception($e->getMessage());
					}
				}
				
				if (count($error_messages) == 0 && count($c_orders) == 0)
					$result->value = "No data uploaded.";
			}
			catch(Exception $e)
			{
				$result->value = $e->getMessage();
			}
		}
		
		$this->result_json($result);
	}
}