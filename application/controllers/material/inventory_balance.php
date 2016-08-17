<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Inventory_balance extends MY_Controller 
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
		if (!is_authorized('material/inventory_balance', 'index')) 
			access_denied();
		
		$data = array(
		);
		
		$content = array(
			'title'		=> "Balance Note",
			'content' 	=> $this->load->view('material/inventory_balance/index', $data, TRUE)
		);
		$this->_load_layout($content);
	}
	
	public function get_list_json()
	{
		if (!is_authorized('material/inventory_balance', 'index')) 
			access_denied();
		
		$this->load->helper('date');
		
		$from_month = $this->input->get_post('from_month');
		$from_year = $this->input->get_post('from_year');
		
		$to_month = $this->input->get_post('to_month');
		$to_year = $this->input->get_post('to_year');
		
		$this->db
			->select("ib.id, ib.code, ib.balance_date, ib.product_size, ib.harvest_sequence, ib.pic, ib.vehicle_no")
			->select($this->db->if_null("SUM(ibd.quantity)", 0) . " quantity", FALSE)
			->select("inv.m_grid_id, gri.code m_grid_code")
			->select("gri.m_warehouse_id, wh.code m_warehouse_code, wh.name m_warehouse_name")
			->select("inv.c_project_id, prj.name c_project_name")
			->from('m_inventory_balances ib')
			->join('m_inventory_balancedetails ibd', "ibd.m_inventory_balance_id = ib.id", 'left')
			->join('m_inventories inv', "inv.id = ib.m_inventory_id", 'left')
			->join('m_grids gri', "gri.id = inv.m_grid_id", 'left')
			->join('m_warehouses wh', "wh.id = gri.m_warehouse_id", 'left')
			->join('c_projects prj', "prj.id = inv.c_project_id", 'left')
			->group_by(
				array(
					'ib.id', 'ib.code', 'ib.balance_date', 'ib.product_size', 'ib.harvest_sequence', 'ib.pic', 'ib.vehicle_no',
					'inv.m_grid_id', 'gri.code',
					'gri.m_warehouse_id', 'wh.code', 'wh.name',
					'inv.c_project_id', 'prj.name'
				)
			);
		$this->db->where("ib.balance_date >=", date('Y-m-d', mktime(0, 0, 0, $from_month, 1, $from_year)));
		$this->db->where("ib.balance_date <=", add_date(date('Y-m-d', mktime(0, 0, 0, $to_month, 1, $to_year)), -1, 1));
		
		$this->lib_custom->project_query_filter('inv.c_project_id', $this->c_project_ids);
		
		parent::_get_list_json();
	}
	
	public function get_m_inventory_autocomplete_list_json()
	{
		if (!is_authorized('material/inventory_balance', 'index')) 
			access_denied();
		
		$keywords = $this->input->get_post('term');
		
		$this->db
			->select("inv.id")
			->select_concat(array("gri.code", "' ('", $this->db->cast_to_string('inv.quantity', 16), "')'"), 'value')
			->select_concat(array("gri.code", "' ('", $this->db->cast_to_string('inv.quantity', 16), "')'"), 'label')
			->from('m_inventories inv')
			->join('m_grids gri', "gri.id = inv.m_grid_id")
			->join('m_products pro', "pro.id = inv.m_product_id")
			->where_in('pro.type', array('BENUR/BIBIT'))
			->where('inv.quantity >', 0);
		
		if ($keywords)
			$this->db->where($this->db->concat(array("gri.code", "' ('", $this->db->cast_to_string('inv.quantity', 16), "')'")) . " LIKE '%" . $this->db->escape_like_str($keywords) . "%'", NULL, FALSE);
		
		$this->lib_custom->project_query_filter('inv.c_project_id', $this->c_project_ids);
		
		parent::_get_list_autocomplete_json();
	}
	
	public function get_list_detail_json()
	{
		if (!is_authorized('material/inventory_balance', 'index')) 
			access_denied();
		
		$this->db
			->select("ibd.id, ibd.quantity_box, ibd.quantity")
			->select("ibd.barcode, ibd.pallet, ibd.lot_no, ibd.carton_no, ibd.condition, ibd.packed_date, ibd.expired_date")
			->select("inv.m_product_id, pro.code m_product_code, pro.name m_product_name, pro.uom m_product_uom, pro.casing m_product_casing")
			->select("ibd.notes, ibd.created")
			->from('m_inventory_balancedetails ibd')
			->join('m_inventories inv', "inv.id = ibd.m_inventory_id", 'left')
			->join('m_products pro', "pro.id = inv.m_product_id", 'left');
		
		$id = $this->input->get_post('id');
		if ($id !== '')
			$this->db->where("ibd.m_inventory_balance_id", $id);
		
		$this->lib_custom->project_query_filter('inv.c_project_id', $this->c_project_ids);
		
		parent::_get_list_json();
	}
	
	public function form()
	{
		if (!is_authorized('material/inventory_balance', 'index')) 
			access_denied();
		
		$form_action = $this->input->get_post('form_action');
		$id = $this->input->get_post('id');
		$record = NULL;
		if ($id !== NULL)
		{
			$this->db
				->select("ib.id, ib.code, ib.balance_date, ib.product_size, ib.harvest_sequence, ib.pic, ib.vehicle_no, ib.notes")
				->select("ib.m_inventory_id")
				->select_concat(array("gri.code", "' ('", $this->db->cast_to_string('inv.quantity', 16), "')'"), 'm_inventory_text')
				->from('m_inventory_balances ib')
				->join('m_inventories inv', "inv.id = ib.m_inventory_id")
				->join('m_grids gri', "gri.id = inv.m_grid_id")
				->where('ib.id', $id);
			$table = $this->db->get();
			if ($table->num_rows() > 0)
				$record = $table->first_row();
			else
				show_error("Balance not found", 400);
		}
		
		$data = array(
			'form_action'	=> $form_action,
			'record'		=> $record
		);
		$this->load->view('material/inventory_balance/form', $data);
	}
	
	public function form_detail()
	{
		if (!is_authorized('material/inventory_balance', 'index')) 
			access_denied();
		
		$id = $this->input->get_post('id');
		$this->db
			->select("ib.id, ib.code, ib.balance_date, ib.product_size, ib.harvest_sequence, ib.pic, ib.vehicle_no, ib.notes")
			->select("ib.m_inventory_id")
			->select_concat(array("gri.code", "' ('", $this->db->cast_to_string('inv.quantity', 16), "')'"), 'm_inventory_text')
			->from('m_inventory_balances ib')
			->join('m_inventories inv', "inv.id = ib.m_inventory_id")
			->join('m_grids gri', "gri.id = inv.m_grid_id")
			->where('ib.id', $id);
		$table = $this->db->get();
		if ($table->num_rows() > 0)
			$record = $table->first_row();
		else
			show_error("Balance not found", 400);
		
		$data = array(
			'id'		=> $id,
			'record'	=> $record
		);
		$this->load->view('material/inventory_balance/form_detail', $data);
	}
	
	public function get_m_product_autocomplete_list_json()
	{
		if (!is_authorized('material/inventory_balance', 'index')) 
			access_denied();
		
		$keywords = $this->input->get_post('term');
		
		$this->db
			->select("id")
			->select_concat(array("name", "' ('", "code", "')'"), 'value')
			->select_concat(array("name", "' ('", "code", "')'"), 'label')
			->select("netto, pack")
			->select("type, casing, uom, price")
			->from('m_products')
			->where_in('type', array('UDANG'));
		
		if ($keywords)
			$this->db->where($this->db->concat(array("name", "' ('", "code", "')'")) . " LIKE '%" . $this->db->escape_like_str($keywords) . "%'", NULL, FALSE);
		
		parent::_get_list_autocomplete_json();
	}
	
	public function form_upload()
	{
		if (!is_authorized('material/inventory_balance', 'insert')) 
			access_denied();
		
		$form_action = $this->input->get_post('form_action');
		$id = $this->input->get_post('id');
		
		$data = array(
			  'form_action'	=> $form_action
			, 'id'			=> $id
		);
		$this->load->view('material/inventory_balance/form_upload', $data);
	}
	
	public function detail()
	{
		if (!is_authorized('material/inventory_balance', 'index')) 
			access_denied();
		
		$id = $this->input->get_post('id');
		$this->db
			->select("ib.id, ib.code, ib.balance_date, ib.product_size, ib.harvest_sequence, ib.pic, ib.vehicle_no, ib.notes")
			->select("ib.m_inventory_id")
			->select_concat(array("gri.code", "' ('", $this->db->cast_to_string('inv.quantity', 16), "')'"), 'm_inventory_text')
			->from('m_inventory_balances ib')
			->join('m_inventories inv', "inv.id = ib.m_inventory_id")
			->join('m_grids gri', "gri.id = inv.m_grid_id")
			->where('ib.id', $id);
		$table = $this->db->get();
		if ($table->num_rows() > 0)
			$record = $table->first_row();
		else
			show_error("Balance not found", 400);
		
		$data = array(
			'record'	=> $record
		);
		$this->load->view('material/inventory_balance/detail', $data);
	}
	
	protected function get_list_detail_full_query()
	{
		$this->db
			->select("ibd.id, ibd.quantity_box, ibd.quantity")
			->select("ibd.barcode, ibd.pallet, ibd.lot_no, ibd.carton_no, ibd.condition, ibd.packed_date, ibd.expired_date")
			->select("inv.m_product_id, pro.code m_product_code, pro.name m_product_name, pro.uom m_product_uom, pro.casing m_product_casing")
			->select("ibd.notes, ibd.created")
			->from('m_inventory_balancedetails ibd')
			->join('m_inventories inv', "inv.id = ibd.m_inventory_id", 'left')
			->join('m_products pro', "pro.id = inv.m_product_id", 'left');
		
		$this->lib_custom->project_query_filter('inv.c_project_id', $this->c_project_ids);
	}
	
	public function get_list_detail_full_json()
	{
		if (!is_authorized('material/inventory_balance', 'index')) 
			access_denied();
		
		$id = $this->input->get_post('id');
		if ($id !== '')
			$this->db->where("ibd.m_inventory_balance_id", $id);
		
		$this->get_list_detail_full_query();
		
		parent::_get_list_json();
	}
	
	public function get_list_detail_full_excel()
	{
		if (!is_authorized('material/inventory_balance', 'index')) 
			access_denied();
		
		$id = $this->input->get_post('id');
		
		$this->db
			->select("ib.id, ib.code, ib.balance_date, ib.product_size, ib.harvest_sequence, ib.pic, ib.vehicle_no, ib.notes")
			->from('m_inventory_balances ib')
			->where('ib.id', $id);
		$table = $this->db->get();
		if ($table->num_rows() == 0)
			show_error("Balance not found", 400);
		$header_record = $table->first_row();
		
		$this->db
			->where("ibd.m_inventory_balance_id", $id);
		$this->get_list_detail_full_query();
		
		$this->load->library('jqgrid');
		
		$header_captions = array(
			'm_product_name'	=> 'Product',
			'carton_no'			=> 'Carton No',
			'quantity'			=> 'Quantity',
			'm_product_uom'		=> 'UOM',
			'notes'				=> 'Notes'
		);
		
		$data = $this->jqgrid->result();
		
		$this->load->helper('file');
		$file_name = get_clear_filename($header_record->code .'__'. $header_record->balance_date);
		
		$this->load->library('excel');
		$this->excel->write_stream_acos(
			array(
				'In' => $data->data
			), 
			'BalanceDetail__'.$file_name.'.xls',
			array(
				'In' => $header_captions
			)
		);
	}
	
	public function insert()
	{
		if (!is_authorized('material/inventory_balance', 'insert')) 
			access_denied();
		
		parent::_execute('this', '_insert_m_inventory_balance', 
			array(),
			array(
				array('field' => 'balance_date', 'label' => 'Date', 'rules' => 'required'),
				array('field' => 'm_inventory_id', 'label' => 'Location', 'rules' => 'required'),
				array('field' => 'product_size', 'label' => 'Product Size', 'rules' => 'required|numeric'),
				array('field' => 'harvest_sequence', 'label' => 'Harvest Sequence', 'rules' => 'required|integer')
			)
		);
	}
	
	protected function _insert_m_inventory_balance()
	{
		$this->load->library('material/lib_inventory_in');
		
		$user_id = $this->session->userdata('user_id');
		
		$data = new stdClass();
		$data->balance_date = $this->input->post('balance_date');
		$data->m_inventory_id = $this->input->post('m_inventory_id');
		$data->product_size = $this->input->post('product_size');
		$data->harvest_sequence = $this->input->post('harvest_sequence');
		$data->pic = $this->input->post('pic');
		$data->vehicle_no = $this->input->post('vehicle_no');
		$data->notes = $this->input->post('notes');
		
		$query = $this->db
			->select("gri.code m_grid_code")
			->from('m_inventories inv')
			->join('m_grids gri', "gri.id = inv.m_grid_id")
			->where('inv.id', $data->m_inventory_id)
			->get();
		if ($query->num_rows() == 0)
			throw new Exception("Location not found");
		
		$record = $query->first_row();
		
		$prefix = "PN". date('ymd');
		$suffix = $record->m_grid_code.'-'.$data->harvest_sequence;
		$data->code = generate_code_number($prefix, $prefix."{NUMBER}-".$suffix, 3);
		
		$this->lib_inventory_in->balance_add($data, $user_id);
	}
	
	public function insert_detail()
	{
		if (!is_authorized('material/inventory_balance', 'insert')) 
			access_denied();
		
		parent::_execute('this', '_insert_detail', 
			array(),
			array(
				array('field' => 'm_inventory_balance_id', 'label' => 'Inventory Balance', 'rules' => 'integer|required'),
				array('field' => 'm_product_id', 'label' => 'Product', 'rules' => 'required'),
				array('field' => 'quantity', 'label' => 'Quantity', 'rules' => 'numeric|required'),
				array('field' => 'carton_no', 'label' => 'Carton No', 'rules' => 'required')
			)
		);
	}
	
	protected function _insert_detail()
	{
		$user_id = $this->session->userdata('user_id');
		
		$this->load->library('material/lib_inventory_in');
		
		$m_inventory_balance_id = $this->input->post('m_inventory_balance_id');
		
		$data = new stdClass();
		$data->m_inventory_balance_id = $m_inventory_balance_id;
		$data->m_product_id = $this->input->post('m_product_id');
		$data->carton_no = $this->input->post('carton_no');
		$data->quantity = $this->input->post('quantity');
		$data->notes = $this->input->post('notes');
		
		$response = new stdClass();
		$response->m_inventory_balancedetail_ids = $this->lib_inventory_in->balancedetail_add($data, $user_id);
		$response->summary = $this->_get_detail_summary($m_inventory_balance_id);
		
		return $response;
	}
	
	public function update($id)
	{
		if (!is_authorized('material/inventory_balance', 'update')) 
			access_denied();
		
		parent::_execute('this', '_update_m_inventory_balance', 
			array($id),
			array(
				array('field' => 'code', 'label' => 'No', 'rules' => 'required'),
				array('field' => 'balance_date', 'label' => 'Date', 'rules' => 'required'),
				array('field' => 'product_size', 'label' => 'Product Size', 'rules' => 'required|numeric')
			)
		);
	}
	
	protected function _update_m_inventory_balance($id)
	{
		$this->load->library('material/lib_inventory_in');
		
		$user_id = $this->session->userdata('user_id');
		
		$data = new stdClass();
		$data->code = $this->input->post('code');
		$data->balance_date = $this->input->post('balance_date');
		$data->product_size = $this->input->post('product_size');
		$data->pic = $this->input->post('pic');
		$data->vehicle_no = $this->input->post('vehicle_no');
		$data->notes = $this->input->post('notes');
		
		$this->lib_inventory_in->balance_update($id, $data, $user_id);
	}
	
	public function delete($id)
	{
		if (!is_authorized('material/inventory_balance', 'delete')) 
			access_denied();
		
		$this->load->library('material/lib_inventory_in');
		
		$user_id = $this->session->userdata('user_id');
		
		parent::_execute('lib_inventory_in', 'balance_remove', array($id, $user_id));
	}
	
	public function delete_detail($id)
	{
		if (!is_authorized('material/inventory_balance', 'delete')) 
			access_denied();
		
		parent::_execute('this', '_delete_detail', array($id));
	}
	
	protected function _delete_detail($id)
	{
		$table_record = new stdClass();
		$table_record->m_inventory_balance_id = NULL;
		
		$table = $this->db
			->select('ibd.m_inventory_balance_id')
			->from('m_inventory_balancedetails ibd')
			->where('ibd.id', $id)
			->get();
		if ($table->num_rows() > 0)
		{
			$table_record = $table->first_row();
		}
		
		$this->load->library('material/lib_inventory_in');
		
		$user_id = $this->session->userdata('user_id');
		
		$response = new stdClass();
		$response->m_inventory_balancedetail_id = $this->lib_inventory_in->balancedetail_remove($id, $user_id);
		$response->summary = $this->_get_detail_summary($table_record->m_inventory_balance_id);
		return $response;
	}
	
	public function get_detail_summary()
	{
		$m_inventory_balance_id = $this->input->get_post('m_inventory_balance_id');
		
		$summary = $this->_get_detail_summary($m_inventory_balance_id);
		
		$response = new stdClass();
		$response->response = TRUE;
		$response->value = $summary;
		$response->data = array();
		
		$this->result_json($response);
	}
	
	protected function _get_detail_summary($m_inventory_balance_id)
	{
		$result = new stdClass();
		$result->counter = 0;
		$carton_no = 0;
		$table = $this->db
			->select_if_null("COUNT(ibd.id)", 0, 'counter')
			->select("MAX(ibd.carton_no) carton_no")
			->from('m_inventory_balancedetails ibd')
			->where('ibd.m_inventory_balance_id', $m_inventory_balance_id)
			->get();
		if ($table->num_rows() > 0)
		{
			$table_record = $table->first_row();
			$result->counter = $table_record->counter;
			$carton_no = intval($table_record->carton_no);
		}
		$result->carton_no = sprintf('%03s', $carton_no + 1);
		
		return $result;
	}

	public function upload()
	{
		if (!is_authorized('material/inventory_balance', 'insert'))
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
		
		$m_inventory_balance_id = $this->input->get_post('id');
		
		$config = array(
			'upload_path'	=> './upload/wms/balance',
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
				
				$m_inventory_balancedetails = array();
				foreach ($excel_sheets as $excel_sheet_name=>$excel_sheet)
				{
					try
					{
						for ($m_inventory_balancedetail_count = 1; $m_inventory_balancedetail_count < count($excel_sheet); $m_inventory_balancedetail_count++)
						{
							$m_product_code = $this->excel->read_value($excel_sheet, $m_inventory_balancedetail_count, 0);
							$table = $this->db
								->select('pro.id')
								->from('m_products pro')
								->where_in('type', array('UDANG'))
								->where('pro.code', $m_product_code)
								->get();
							if ($table->num_rows() == 0)
								throw new Exception("Product code '".$m_product_code."' is not found!");
							$table_record = $table->first_row();
							$m_product_id = $table_record->id;
							
							$m_inventory_balancedetail = new stdClass();
							$m_inventory_balancedetail->m_inventory_balance_id = $m_inventory_balance_id;
							$m_inventory_balancedetail->m_product_id = $m_product_id;
							$m_inventory_balancedetail->carton_no = $this->excel->read_value($excel_sheet, $m_inventory_balancedetail_count, 2);
							$m_inventory_balancedetail->quantity = $this->excel->read_value($excel_sheet, $m_inventory_balancedetail_count, 3);
							$m_inventory_balancedetail->notes = $this->excel->read_value($excel_sheet, $m_inventory_balancedetail_count, 4);
							$m_inventory_balancedetails[] = $m_inventory_balancedetail;
						}
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
				
				if (count($m_inventory_balancedetails) > 0)
				{
					$this->load->library('material/lib_inventory_in');
					
					$m_inventory_balancedetail_no = 1;
					
					$this->db->trans_begin();
					try
					{
						foreach ($m_inventory_balancedetails as $m_inventory_balancedetail)
						{
							$this->lib_inventory_in->balancedetail_add($m_inventory_balancedetail, $user_id);
							$m_inventory_balancedetail_no++;
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
						throw new Exception("Error on line ".$m_inventory_balancedetail_no.":\n".$e->getMessage());
					}
				}
				
				if (count($error_messages) == 0 && count($m_inventory_balancedetails) == 0)
					$result->value = "No data uploaded.";
			}
			catch(Exception $e)
			{
				$result->value = $e->getMessage();
			}
		}
		
		$this->result_json($result);
	}

	public function detail_printout()
	{
		if (!is_authorized('material/inventory_balance', 'index')) 
			access_denied();
		
		$id = $this->input->get_post('id');
		$this->db
			->select("ib.id, ib.code, ib.balance_date, ib.product_size, ib.harvest_sequence, ib.pic, ib.vehicle_no, ib.notes")
			->select("ib.m_inventory_id")
			->select("gri.code m_grid_code")
			->from('m_inventory_balances ib')
			->join('m_inventories inv', "inv.id = ib.m_inventory_id")
			->join('m_grids gri', "gri.id = inv.m_grid_id")
			->where('ib.id', $id);
		$table = $this->db->get();
		if ($table->num_rows() > 0)
			$record = $table->first_row();
		else
			show_error("Balance not found", 400);
		
		$this->db->where("ibd.m_inventory_balance_id", $id);
		$this->get_list_detail_full_query();
		$table = $this->db
			->order_by('ibd.carton_no', 'asc')
			->get();
		$m_inventory_balancedetails = $table->result();
		
		$data = array(
			'record'						=> $record,
			'm_inventory_balancedetails'	=> $m_inventory_balancedetails
		);
		
		$html = $this->load->view('material/inventory_balance/detail_printout', $data, TRUE);
		
		// $this->output->set_output($html);
		$this->load->library('lib_dompdf');
		$this->lib_dompdf->load_as_pdf($html, 'detail_printout.pdf', 'a4', 'portrait');
	}
}