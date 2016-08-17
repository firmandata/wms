<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Report_out extends MY_Controller 
{
	public function __construct()
	{
		parent::__construct(TRUE, 'backend');
	}
	
	public function index()
	{
		if (!is_authorized('material/report_out', 'index')) 
			access_denied();
		
		$data = array(
		);
		
		$content = array(
			'title'		=> "Report Out",
			'content' 	=> $this->load->view('material/report_out/index', $data, TRUE)
		);
		$this->_load_layout($content);
	}
	
	protected function get_list_query()
	{
		$this->db
			->select("isp.shipment_date")
			->select("isp.code m_inventory_shipment_code")
			->select("bp.name c_businesspartner_name")
			->select("oo.external_no c_orderout_external_no")
			->select("ispd.notes")
			->select("isp.vehicle_no m_inventory_shipment_vehicle_no, isp.vehicle_driver m_inventory_shipment_vehicle_driver")
			->select("ipld.pallet m_inventory_picklistdetail_pallet")
			->select("pro.code m_product_code, pro.name m_product_name")
			->select_if_null("SUM(ispd.quantity_box)", 0, 'quantity_box')
			->select_if_null("SUM(ispd.quantity)", 0, 'quantity')
			->select("ipld.lot_no m_inventory_picklistdetail_lot_no")
			->select("oo.no_surat_jalan c_orderout_no_surat_jalan, oo.marketing_unit c_orderout_marketing_unit")
			->from('m_inventory_shipmentdetails ispd')
			->join('m_inventory_shipments isp', "isp.id = ispd.m_inventory_shipment_id")
			->join('m_inventory_pickingdetails ipgd', "ipgd.id = ispd.m_inventory_pickingdetail_id")
			->join('m_inventory_picklistdetails ipld', "ipld.id = ipgd.m_inventory_picklistdetail_id")
			->join('c_orderoutdetails ood', "ood.id = ipld.c_orderoutdetail_id")
			->join('c_orderouts oo', "oo.id = ood.c_orderout_id")
			->join('c_businesspartners bp', "bp.id = oo.c_businesspartner_id")
			->join('m_products pro', "pro.id = ipld.m_product_id", 'left')
			->group_by(
				array(
					"isp.shipment_date",
					"isp.code",
					"bp.name",
					"oo.external_no",
					"ispd.notes",
					"isp.vehicle_no", "isp.vehicle_driver",
					"ipld.pallet",
					"pro.code", "pro.name",
					"ipld.lot_no",
					"oo.no_surat_jalan", "oo.marketing_unit"
				)
			);
	}
	
	public function get_list_json()
	{
		if (!is_authorized('material/report_out', 'index')) 
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
		
		$this->db->where("isp.shipment_date >=", $date_from);
		$this->db->where("isp.shipment_date <=", $date_to);
		
		parent::_get_list_json();
	}
	
	public function get_list_excel()
	{
		if (!is_authorized('material/report_out', 'index')) 
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
		
		$this->db->where("isp.shipment_date >=", $date_from);
		$this->db->where("isp.shipment_date <=", $date_to);
		
		$this->load->library('jqgrid');
		
		$header_captions = array(
			'shipment_date'							=> 'OUTBOUND DATE',
			'm_inventory_shipment_code'				=> 'DO/SO',
			'c_businesspartner_name'				=> 'SUPPLIER/VENDOR',
			'c_orderout_external_no'				=> 'EXTERNAL DO',
			'notes'									=> 'NOTES',
			'm_inventory_shipment_vehicle_no'		=> 'NOTRUK',
			'm_inventory_shipment_vehicle_driver'	=> 'SOPIR',
			'm_inventory_picklistdetail_pallet'		=> 'PALLET',
			'm_product_code'						=> 'ITEM',
			'm_product_name'						=> 'DESC',
			'quantity_box'							=> 'QTY BOX',
			'quantity'								=> 'QTY KG',
			'm_inventory_picklistdetail_lot_no'		=> 'LOT',
			'c_orderout_no_surat_jalan'				=> 'NO FAKTUR',
			'c_orderout_marketing_unit'				=> 'MU'
		);
		
		$data = $this->jqgrid->result();
		
		$result = $data->data;
		
		$this->load->library('excel');
		$this->excel->write_stream_acos(
			array(
				'Out' => $result
			), 
			'ReportOut.xls',
			array(
				'Out' => $header_captions
			)
		);
	}
}