<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Report_in extends MY_Controller 
{
	public function __construct()
	{
		parent::__construct(TRUE, 'backend');
	}
	
	public function index()
	{
		if (!is_authorized('material/report_in', 'index')) 
			access_denied();
		
		$data = array(
		);
		
		$content = array(
			'title'		=> "Report In",
			'content' 	=> $this->load->view('material/report_in/index', $data, TRUE)
		);
		$this->_load_layout($content);
	}
	
	protected function get_list_query()
	{
		$this->db
			->select("ii.inbound_date")
			->select("ir.code m_inventory_receive_code")
			->select("bp.name c_businesspartner_name")
			->select("oi.external_no c_orderin_external_no")
			->select("iid.notes")
			->select("ir.vehicle_no m_inventory_receive_vehicle_no")
			->select("ir.vehicle_driver m_inventory_receive_vehicle_driver")
			->select("pro.code m_product_code, pro.name m_product_name, pro.uom m_product_uom")
			->select("iid.pallet")
			->select_if_null("SUM(iid.quantity_box)", 0, 'quantity_box')
			->select_if_null("SUM(iid.quantity)", 0, 'quantity')
			->from('m_inventory_inbounddetails iid')
			->join('m_inventory_inbounds ii', "ii.id = iid.m_inventory_inbound_id")
			->join('m_inventory_receivedetails ird', "ird.id = iid.m_inventory_receivedetail_id")
			->join('m_inventory_receives ir', "ir.id = ird.m_inventory_receive_id")
			->join('c_orderindetails oid', "oid.id = ird.c_orderindetail_id")
			->join('c_orderins oi', "oi.id = oid.c_orderin_id")
			->join('c_businesspartners bp', "bp.id = oi.c_businesspartner_id")
			->join('m_products pro', "pro.id = oid.m_product_id")
			->group_by(
				array(
					'ii.inbound_date',
					'ir.code',
					'bp.name',
					'oi.external_no',
					'iid.notes',
					'ir.vehicle_no',
					'ir.vehicle_driver',
					'pro.code', 'pro.name', 'pro.uom',
					'iid.pallet'
				)
			);
	}
	
	public function get_list_json()
	{
		if (!is_authorized('material/report_in', 'index')) 
			access_denied();
		
		$date_from = $this->input->get_post('date_from');
		if (empty($date_from))
		{
			$date_from = date($this->config->item('server_date_format'));
		}
		$date_to = $this->input->get_post('date_to');
		if (empty($date_to))
		{
			$date_to = date($this->config->item('server_date_format'));
		}
		
		$this->get_list_query();
		
		$this->db->where("ii.inbound_date >=", $date_from);
		$this->db->where("ii.inbound_date <=", $date_to);
		
		parent::_get_list_json();
	}
	
	public function get_list_excel()
	{
		if (!is_authorized('material/report_in', 'index')) 
			access_denied();
		
		$date_from = $this->input->get_post('date_from');
		if (empty($date_from))
		{
			$date_from = date($this->config->item('server_date_format'));
		}
		$date_to = $this->input->get_post('date_to');
		if (empty($date_to))
		{
			$date_to = date($this->config->item('server_date_format'));
		}
		
		$this->get_list_query();
		
		$this->db->where("ii.inbound_date >=", $date_from);
		$this->db->where("ii.inbound_date <=", $date_to);
		
		$this->load->library('jqgrid');
		
		$header_captions = array(
			'inbound_date'							=> "INBOUND DATE",
			'm_inventory_receive_code'				=> "PO/AR",
			'c_businesspartner_name'				=> "SUPPLIER/VENDOR",
			'external_no'							=> "EXTERNAL PO",
			'notes'									=> "NOTES",
			'm_inventory_receive_vehicle_no'		=> "NOTRUK",
			'm_inventory_receive_vehicle_driver'	=> "SOPIR",
			'pallet'								=> "PALLET",
			'm_product_code'						=> "ITEM",
			'm_product_name'						=> "DESC",
			'quantity_box'							=> "QTY BOX",
			'quantity'								=> "QTY KG",
			'm_product_uom'							=> "UOM"
		);
		
		$data = $this->jqgrid->result();
		
		$result = $data->data;
		
		$this->load->library('excel');
		$this->excel->write_stream_acos(
			array(
				'In' => $result
			), 
			'ReportIn.xls',
			array(
				'In' => $header_captions
			)
		);
	}
}