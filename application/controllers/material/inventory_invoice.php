<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Inventory_invoice extends MY_Controller 
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
		if (!is_authorized('material/inventory_invoice', 'index')) 
			access_denied();
		
		$data = array(
		);
		
		$content = array(
			'title'		=> "Invoice",
			'content' 	=> $this->load->view('material/inventory_invoice/index', $data, TRUE)
		);
		$this->_load_layout($content);
	}
	
	public function get_list_json()
	{
		if (!is_authorized('material/inventory_invoice', 'index')) 
			access_denied();
		
		$this->load->helper('date');
		
		$from_month = $this->input->get_post('from_month');
		$from_year = $this->input->get_post('from_year');
		
		$to_month = $this->input->get_post('to_month');
		$to_year = $this->input->get_post('to_year');
		
		$this->db
			->select("invo.id, invo.code, invo.invoice_date")
			->select("invo.period_from, invo.period_to")
			->select("invo.c_businesspartner_id, bp.code c_businesspartner_code, bp.name c_businesspartner_name")
			->select("invo.invoice_handling_in, invo.invoice_handling_in_price")
			->select("invo.invoice_handling_out, invo.invoice_handling_out_price")
			->select("invo.invoice_handling_storage, invo.invoice_handling_storage_price")
			->select("invo.invoice_calculate")
			->select_if_null("SUM(invod.parts_num)", 0, 'parts_num')
			->select_if_null("SUM(invod.weight)", 0, 'weight')
			->select_if_null("SUM(invod.amount)", 0, 'amount')
			->from('m_inventory_invoices invo')
			->join('m_inventory_invoicedetails invod', "invod.m_inventory_invoice_id = invo.id", 'left')
			->join('c_businesspartners bp', "bp.id = invo.c_businesspartner_id", 'left')
			->group_by(
				array(
					'invo.id', 'invo.code', 'invo.invoice_date',
					'invo.period_from', 'invo.period_to',
					'invo.c_businesspartner_id', 'bp.code', 'bp.name',
					'invo.invoice_handling_in', 'invo.invoice_handling_in_price',
					'invo.invoice_handling_out', 'invo.invoice_handling_out_price',
					'invo.invoice_handling_storage', 'invo.invoice_handling_storage_price',
					'invo.invoice_calculate'
				)
			);
		$this->db->where("invo.invoice_date >=", date('Y-m-d', mktime(0, 0, 0, $from_month, 1, $from_year)));
		$this->db->where("invo.invoice_date <=", add_date(date('Y-m-d', mktime(0, 0, 0, $to_month, 1, $to_year)), -1, 1));
		
		parent::_get_list_json();
	}
	
	public function form()
	{
		if (!is_authorized('material/inventory_invoice', 'index')) 
			access_denied();
		
		$form_action = $this->input->get_post('form_action');
		$id = $this->input->get_post('id');
		$record = NULL;
		if ($id !== NULL)
		{
			$this->db
				->select("invo.id, invo.code, invo.invoice_date")
				->select("invo.period_from, invo.period_to")
				->select("invo.c_businesspartner_id, bp.code c_businesspartner_code, bp.name c_businesspartner_name")
				->select_concat(array("bp.name", "' ('", "bp.code", "')'"), 'c_businesspartner_text')
				->select("invo.invoice_handling_in, invo.invoice_handling_in_price")
				->select("invo.invoice_handling_out, invo.invoice_handling_out_price")
				->select("invo.invoice_handling_storage, invo.invoice_handling_storage_price")
				->select("invo.invoice_calculate")
				->select("invo.tax")
				->select("invo.jo_no")
				->select("invo.term_of_payment")
				->select("invo.plate_no, invo.si_sj_so_no, invo.spk_po_no")
				->select("invo.reference")
				->select("invo.bank_ac_name, invo.bank_ac_no, invo.bank_name, invo.bank_branch, invo.bank_swift_code")
				->from('m_inventory_invoices invo')
				->join('c_businesspartners bp', "bp.id = invo.c_businesspartner_id", 'left')
				->where('invo.id', $id);
			$table = $this->db->get();
			if ($table->num_rows() > 0)
				$record = $table->first_row();
			else
				show_error("Invoice not found", 400);
		}
		
		$data = array(
			'form_action'	=> $form_action,
			'record'		=> $record
		);
		$this->load->view('material/inventory_invoice/form', $data);
	}
	
	public function get_businesspartner_autocomplete_list_json()
	{
		if (!is_authorized('material/inventory_invoice', 'index')) 
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
	
	public function get_boolean_dropdown($element_name)
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
	
	public function insert()
	{
		if (!is_authorized('material/inventory_invoice', 'insert')) 
			access_denied();
		
		$this->load->library('material/lib_inventory_operation');
		
		$user_id = $this->session->userdata('user_id');
		
		$data = new stdClass();
		$data->code = generate_code_number("MAX-INV-". date('ym-'), NULL, 3);
		$data->invoice_date = $this->input->post('invoice_date');
		$data->period_from = $this->input->post('period_from');
		$data->period_to = $this->input->post('period_to');
		$data->c_businesspartner_id = $this->input->post('c_businesspartner_id');
		
		$data->invoice_handling_in = $this->input->post('invoice_handling_in');
		if ($data->invoice_handling_in === NULL)
		{
			$data->invoice_handling_in = 0;
			$data->invoice_handling_in_price = 0;
		}
		else
			$data->invoice_handling_in_price = $this->input->post('invoice_handling_in_price');
		
		$data->invoice_handling_out = $this->input->post('invoice_handling_out');
		if ($data->invoice_handling_out === NULL)
		{
			$data->invoice_handling_out = 0;
			$data->invoice_handling_out_price = 0;
		}
		else
			$data->invoice_handling_out_price = $this->input->post('invoice_handling_out_price');
		
		$data->invoice_handling_storage = $this->input->post('invoice_handling_storage');
		if ($data->invoice_handling_storage === NULL)
		{
			$data->invoice_handling_storage = 0;
			$data->invoice_handling_storage_price = 0;
		}
		else
			$data->invoice_handling_storage_price = $this->input->post('invoice_handling_storage_price');
		
		$data->invoice_calculate = $this->input->post('invoice_calculate');
		$data->tax = $this->input->post('tax');
		$data->jo_no = $this->input->post('jo_no');
		$data->term_of_payment = $this->input->post('term_of_payment');
		$data->plate_no = $this->input->post('plate_no');
		$data->si_sj_so_no = $this->input->post('si_sj_so_no');
		$data->spk_po_no = $this->input->post('spk_po_no');
		$data->reference = $this->input->post('reference');
		$data->bank_ac_name = $this->input->post('bank_ac_name');
		$data->bank_ac_no = $this->input->post('bank_ac_no');
		$data->bank_name = $this->input->post('bank_name');
		$data->bank_branch = $this->input->post('bank_branch');
		$data->bank_swift_code = $this->input->post('bank_swift_code');
		
		parent::_execute('lib_inventory_operation', 'invoice_add', 
			array($data, $user_id),
			array(
				array('field' => 'invoice_date', 'label' => 'Date', 'rules' => 'required'),
				array('field' => 'period_from', 'label' => 'Period From', 'rules' => 'required'),
				array('field' => 'period_to', 'label' => 'Period To', 'rules' => 'required'),
				array('field' => 'invoice_handling_in', 'label' => 'Handling In', 'rules' => 'integer'),
				array('field' => 'invoice_handling_in_price', 'label' => 'Handling In Price Per Unit', 'rules' => 'required|numeric'),
				array('field' => 'invoice_handling_out', 'label' => 'Handling Out', 'rules' => 'integer'),
				array('field' => 'invoice_handling_out_price', 'label' => 'Handling Out Price Per Unit', 'rules' => 'required|numeric'),
				array('field' => 'invoice_handling_storage', 'label' => 'Handling Storage', 'rules' => 'integer'),
				array('field' => 'invoice_handling_storage_price', 'label' => 'Handling Storage Price Per Unit', 'rules' => 'required|numeric'),
				array('field' => 'invoice_calculate', 'label' => 'Calculate By', 'rules' => 'required'),
				array('field' => 'tax', 'label' => 'Tax', 'rules' => 'required|numeric')
			)
		);
	}
	
	public function update($id)
	{
		if (!is_authorized('material/inventory_invoice', 'update')) 
			access_denied();
		
		$this->load->library('material/lib_inventory_operation');
		
		$user_id = $this->session->userdata('user_id');
		
		$data = new stdClass();
		$data->code = $this->input->post('code');
		$data->invoice_date = $this->input->post('invoice_date');
		$data->period_from = $this->input->post('period_from');
		$data->period_to = $this->input->post('period_to');
		$data->c_businesspartner_id = $this->input->post('c_businesspartner_id');
		
		$data->invoice_handling_in = $this->input->post('invoice_handling_in');
		if ($data->invoice_handling_in === NULL)
		{
			$data->invoice_handling_in = 0;
			$data->invoice_handling_in_price = 0;
		}
		else
			$data->invoice_handling_in_price = $this->input->post('invoice_handling_in_price');
		
		$data->invoice_handling_out = $this->input->post('invoice_handling_out');
		if ($data->invoice_handling_out === NULL)
		{
			$data->invoice_handling_out = 0;
			$data->invoice_handling_out_price = 0;
		}
		else
			$data->invoice_handling_out_price = $this->input->post('invoice_handling_out_price');
		
		$data->invoice_handling_storage = $this->input->post('invoice_handling_storage');
		if ($data->invoice_handling_storage === NULL)
		{
			$data->invoice_handling_storage = 0;
			$data->invoice_handling_storage_price = 0;
		}
		else
			$data->invoice_handling_storage_price = $this->input->post('invoice_handling_storage_price');
		
		$data->invoice_calculate = $this->input->post('invoice_calculate');
		$data->tax = $this->input->post('tax');
		$data->jo_no = $this->input->post('jo_no');
		$data->term_of_payment = $this->input->post('term_of_payment');
		$data->plate_no = $this->input->post('plate_no');
		$data->si_sj_so_no = $this->input->post('si_sj_so_no');
		$data->spk_po_no = $this->input->post('spk_po_no');
		$data->reference = $this->input->post('reference');
		$data->bank_ac_name = $this->input->post('bank_ac_name');
		$data->bank_ac_no = $this->input->post('bank_ac_no');
		$data->bank_name = $this->input->post('bank_name');
		$data->bank_branch = $this->input->post('bank_branch');
		$data->bank_swift_code = $this->input->post('bank_swift_code');
		
		parent::_execute('lib_inventory_operation', 'invoice_update', 
			array($id, $data, $user_id), 
			array(
				array('field' => 'code', 'label' => 'No', 'rules' => 'required'),
				array('field' => 'invoice_date', 'label' => 'Date', 'rules' => 'required'),
				array('field' => 'period_from', 'label' => 'Period From', 'rules' => 'required'),
				array('field' => 'period_to', 'label' => 'Period To', 'rules' => 'required'),
				array('field' => 'invoice_handling_in', 'label' => 'Handling In', 'rules' => 'integer'),
				array('field' => 'invoice_handling_in_price', 'label' => 'Handling In Price Per Unit', 'rules' => 'required|numeric'),
				array('field' => 'invoice_handling_out', 'label' => 'Handling Out', 'rules' => 'integer'),
				array('field' => 'invoice_handling_out_price', 'label' => 'Handling Out Price Per Unit', 'rules' => 'required|numeric'),
				array('field' => 'invoice_handling_storage', 'label' => 'Handling Storage', 'rules' => 'integer'),
				array('field' => 'invoice_handling_storage_price', 'label' => 'Handling Storage Price Per Unit', 'rules' => 'required|numeric'),
				array('field' => 'invoice_calculate', 'label' => 'Calculate By', 'rules' => 'required'),
				array('field' => 'tax', 'label' => 'Tax', 'rules' => 'required|numeric')
			)
		);
	}
	
	public function delete($id)
	{
		if (!is_authorized('material/inventory_invoice', 'delete')) 
			access_denied();
		
		$this->load->library('material/lib_inventory_operation');
		
		$user_id = $this->session->userdata('user_id');
		
		parent::_execute('lib_inventory_operation', 'invoice_remove', array($id, $user_id));
	}
	
	public function printout()
	{
		if (!is_authorized('material/inventory_invoice', 'index')) 
			access_denied();
		
		$id = $this->input->get_post('id');
		$this->db
			->select("invo.id, invo.code, invo.invoice_date")
			->select("invo.period_from, invo.period_to")
			->select("invo.c_businesspartner_id, bp.code c_businesspartner_code, bp.name c_businesspartner_name, bp.address c_businesspartner_address")
			->select("invo.invoice_handling_in, invo.invoice_handling_in_price")
			->select("invo.invoice_handling_out, invo.invoice_handling_out_price")
			->select("invo.invoice_handling_storage, invo.invoice_handling_storage_price")
			->select("invo.invoice_calculate")
			->select("invo.tax")
			->select("invo.jo_no")
			->select("invo.term_of_payment")
			->select("invo.plate_no, invo.si_sj_so_no, invo.spk_po_no")
			->select("invo.reference")
			->select("invo.bank_ac_name, invo.bank_ac_no, invo.bank_name, invo.bank_branch, invo.bank_swift_code")
			->from('m_inventory_invoices invo')
			->join('c_businesspartners bp', "bp.id = invo.c_businesspartner_id", 'left')
			->where('invo.id', $id);
		$table = $this->db->get();
		if ($table->num_rows() > 0)
			$record = $table->first_row();
		else
			show_error("Invoice not found", 400);
		
		$this->db
			->select("invod.id, invod.description, invod.parts_num, invod.weight, invod.amount")
			->from('m_inventory_invoicedetails invod')
			->where('invod.m_inventory_invoice_id', $record->id)
			->order_by('invod.id', 'asc');
		$table = $this->db->get();
		$record->m_inventory_invoicedetails = $table->result();
		
		$records = array();
		$records[] = $record;
		
		$data = array(
			'data'	=> $records
		);
		$html = $this->load->view('material/inventory_invoice/printout', $data, TRUE);
		
		// $this->output
			// ->set_output($html);
			
		$this->load->library('lib_dompdf');
		$this->lib_dompdf->load_as_pdf($html, 'invoice.pdf', 'a4', 'portrait');
	}
}