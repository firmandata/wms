<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Inventory_shipment extends MY_Controller 
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
		if (!is_authorized('material/inventory_shipment', 'index')) 
			access_denied();
		
		$data = array(
		);
		
		$content = array(
			'title'		=> "Shipment",
			'content' 	=> $this->load->view('material/inventory_shipment/index', $data, TRUE)
		);
		$this->_load_layout($content);
	}
	
	public function get_list_json()
	{
		if (!is_authorized('material/inventory_shipment', 'index')) 
			access_denied();
		
		$this->load->helper('date');
		
		$from_month = $this->input->get_post('from_month');
		$from_year = $this->input->get_post('from_year');
		
		$to_month = $this->input->get_post('to_month');
		$to_year = $this->input->get_post('to_year');
		
		$this->db
			->select("isp.id, isp.code, isp.shipment_date, isp.shipment_type, isp.shipment_to")
			->select("isp.request_arrive_date, isp.estimated_time_arrive")
			->select("isp.vehicle_no, isp.vehicle_driver, isp.transport_mode, isp.police_name")
			->select("ipgd.m_inventory_picking_id, ipg.code m_inventory_picking_code, ipg.picking_date m_inventory_picking_date")
			->select("ipld.m_inventory_picklist_id, ipl.code m_inventory_picklist_code, ipl.picklist_date m_inventory_picklist_date")
			->select("ood.c_orderout_id, oo.code c_orderout_code, oo.orderout_date c_orderout_date")
			->select("oo.c_businesspartner_id, bp.name c_businesspartner_name")
			->select("oo.c_project_id, prj.name c_project_name")
			->select($this->db->if_null("SUM(ispd.quantity_box)", 0) . " quantity_box", FALSE)
			->select($this->db->if_null("SUM(ispd.quantity)", 0) . " quantity", FALSE)
			->from('m_inventory_shipments isp')
			->join('m_inventory_shipmentdetails ispd', "ispd.m_inventory_shipment_id = isp.id", 'left')
			->join('m_inventory_pickingdetails ipgd', "ipgd.id = ispd.m_inventory_pickingdetail_id", 'left')
			->join('m_inventory_pickings ipg', "ipg.id = ipgd.m_inventory_picking_id", 'left')
			->join('m_inventory_picklistdetails ipld', "ipld.id = ipgd.m_inventory_picklistdetail_id", 'left')
			->join('m_inventory_picklists ipl', "ipl.id = ipld.m_inventory_picklist_id", 'left')
			->join('c_orderoutdetails ood', "ood.id = ipld.c_orderoutdetail_id", 'left')
			->join('c_orderouts oo', "oo.id = ood.c_orderout_id", 'left')
			->join('c_businesspartners bp', "bp.id = oo.c_businesspartner_id", 'left')
			->join('c_projects prj', "prj.id = oo.c_project_id", 'left')
			->group_by(
				array(
					'isp.id', 'isp.code', 'isp.shipment_date', 'isp.shipment_type', 'isp.shipment_to',
					'isp.request_arrive_date', 'isp.estimated_time_arrive',
					'isp.vehicle_no', 'isp.vehicle_driver', 'isp.transport_mode', 'isp.police_name',
					'ipgd.m_inventory_picking_id', 'ipg.code', 'ipg.picking_date',
					'ipld.m_inventory_picklist_id', 'ipl.code', 'ipl.picklist_date',
					'ood.c_orderout_id', 'oo.code', 'oo.orderout_date',
					'oo.c_businesspartner_id', 'bp.name',
					'oo.c_project_id', 'prj.name'
				)
			);
		$this->db->where("isp.shipment_date >=", date('Y-m-d', mktime(0, 0, 0, $from_month, 1, $from_year)));
		$this->db->where("isp.shipment_date <=", add_date(date('Y-m-d', mktime(0, 0, 0, $to_month, 1, $to_year)), -1, 1));
		
		$this->lib_custom->project_query_filter('ipld.c_project_id', $this->c_project_ids);
		
		parent::_get_list_json();
	}
	
	public function form()
	{
		if (!is_authorized('material/inventory_shipment', 'index')) 
			access_denied();
		
		$form_action = $this->input->get_post('form_action');
		$id = $this->input->get_post('id');
		$record = NULL;
		if ($id !== NULL)
		{
			$this->db
				->select("isp.id, isp.code, isp.shipment_date")
				->select("isp.shipment_type, isp.shipment_to")
				->select("isp.request_arrive_date, isp.estimated_time_arrive")
				->select("isp.vehicle_no, isp.vehicle_driver, isp.transport_mode, isp.police_name")
				->select("isp.notes")
				->from('m_inventory_shipments isp')
				->where('isp.id', $id);
			$table = $this->db->get();
			if ($table->num_rows() > 0)
				$record = $table->first_row();
			else
				show_error("Shipment not found", 400);
		}
		
		$data = array(
			'form_action'	=> $form_action,
			'record'		=> $record
		);
		$this->load->view('material/inventory_shipment/form', $data);
	}
	
	public function form_detail()
	{
		if (!is_authorized('material/inventory_shipment', 'index')) 
			access_denied();
		
		$id = $this->input->get_post('id');
		
		$data = array(
			'id'	=> $id
		);
		$this->load->view('material/inventory_shipment/form_detail', $data);
	}
	
	public function get_list_form_ref_json()
	{
		if (!is_authorized('material/inventory_shipment', 'index')) 
			access_denied();
		
		$this->db
			->select("ipg.id, ipg.code, ipg.picking_date")
			->select("ipld.m_inventory_picklist_id, ipl.code m_inventory_picklist_code, ipl.picklist_date m_inventory_picklist_date")
			->select("ood.c_orderout_id, oo.code c_orderout_code, oo.orderout_date c_orderout_date, oo.request_arrive_date c_orderout_request_arrive_date")
			->select("oo.c_businesspartner_id, bp.name c_businesspartner_name")
			->select("oo.c_project_id, prj.name c_project_name")
			->select($this->db->if_null("SUM(ipgd.quantity_box)", 0) . " quantity_box", FALSE)
			->select($this->db->if_null("SUM(ipgd.quantity)", 0) . " quantity", FALSE)
			->select("ipg.created")
			->from('m_inventory_pickings ipg')
			->join('m_inventory_pickingdetails ipgd', "ipgd.m_inventory_picking_id = ipg.id")
			->join('m_inventory_picklistdetails ipld', "ipld.id = ipgd.m_inventory_picklistdetail_id", 'left')
			->join('m_inventory_picklists ipl', "ipl.id = ipld.m_inventory_picklist_id", 'left')
			->join('c_orderoutdetails ood', "ood.id = ipld.c_orderoutdetail_id", 'left')
			->join('c_orderouts oo', "oo.id = ood.c_orderout_id", 'left')
			->join('c_businesspartners bp', "bp.id = oo.c_businesspartner_id", 'left')
			->join('c_projects prj', "prj.id = oo.c_project_id", 'left')
			->where('ipgd.status_inventory_shipment <>', 'COMPLETE')
			->group_by(
				array(
					'ipg.id', 'ipg.code', 'ipg.picking_date',
					'ipld.m_inventory_picklist_id', 'ipl.code', 'ipl.picklist_date',
					'ood.c_orderout_id', 'oo.code', 'oo.orderout_date', 'oo.request_arrive_date',
					'oo.c_businesspartner_id', 'bp.name',
					'oo.c_project_id', 'prj.name',
					'ipg.created'
				)
			);
		
		$this->lib_custom->project_query_filter('ipld.c_project_id', $this->c_project_ids);
		
		parent::_get_list_json();
	}
	
	public function get_list_form_json()
	{
		if (!is_authorized('material/inventory_shipment', 'index')) 
			access_denied();
		
		$this->db
			->select("MAX(ispd.id) id", FALSE)
			->select("ispd.m_inventory_shipment_id, ispd.packed_group")
			->select("ipgd.m_inventory_picking_id, ipg.code m_inventory_picking_code, ipg.picking_date")
			->select("ipld.m_product_id, pro.code m_product_code, pro.name m_product_name, pro.uom m_product_uom")
			->select("ipld.pallet, ipld.barcode, ipld.condition")
			->select("SUM(ispd.quantity_box) quantity_box", FALSE)
			->select("SUM(ispd.quantity) quantity", FALSE)
			->from('m_inventory_shipmentdetails ispd')
			->join('m_inventory_pickingdetails ipgd', "ipgd.id = ispd.m_inventory_pickingdetail_id")
			->join('m_inventory_pickings ipg', "ipg.id = ipgd.m_inventory_picking_id")
			->join('m_inventory_picklistdetails ipld', "ipld.id = ipgd.m_inventory_picklistdetail_id")
			->join('m_products pro', "pro.id = ipld.m_product_id", 'left')
			->group_by(
				array(
					'ispd.m_inventory_shipment_id', 'ispd.packed_group',
					'ipgd.m_inventory_picking_id', 'ipg.code', 'ipg.picking_date',
					'ipld.m_product_id', 'pro.code', 'pro.name', 'pro.uom',
					'ipld.pallet', 'ipld.barcode', 'ipld.condition'
				)
			);
		
		$id = $this->input->get_post('id');
		if ($id !== '')
			$this->db->where("ispd.m_inventory_shipment_id", $id);
		
		$this->lib_custom->project_query_filter('ipld.c_project_id', $this->c_project_ids);
		
		parent::_get_list_json();
	}
	
	public function detail()
	{
		if (!is_authorized('material/inventory_shipment', 'index')) 
			access_denied();
		
		$id = $this->input->get_post('id');
		$this->db
			->select("isp.id, isp.code, isp.shipment_date")
			->select("isp.shipment_type, isp.shipment_to")
			->select("isp.request_arrive_date, isp.estimated_time_arrive")
			->select("isp.vehicle_no, isp.vehicle_driver, isp.transport_mode, isp.police_name")
			->select("isp.notes")
			->from('m_inventory_shipments isp')
			->where('isp.id', $id);
		$table = $this->db->get();
		if ($table->num_rows() > 0)
			$record = $table->first_row();
		else
			show_error("Shipment not found", 400);
		
		$data = array(
			'record'	=> $record
		);
		$this->load->view('material/inventory_shipment/detail', $data);
	}
	
	protected function get_list_detail_query()
	{
		$this->db
			->select("MAX(ispd.id) id", FALSE)
			->select("ispd.packed_group, ipg.code m_inventory_picking_code, ipg.picking_date m_inventory_picking_date")
			->select("ipl.code m_inventory_picklist_code, ipl.picklist_date m_inventory_picklist_date")
			->select("oo.code c_orderout_code, oo.orderout_date c_orderout_date, oo.request_arrive_date c_orderout_request_arrive_date")
			->select("ipld.c_businesspartner_id, bp.name c_businesspartner_name")
			->select("ipld.c_project_id, prj.name c_project_name")
			->select("ipld.m_product_id, pro.code m_product_code, pro.name m_product_name, pro.uom m_product_uom")
			->select("ipld.pallet, ipld.barcode, ipld.condition")
			->select("SUM(ispd.quantity_box) quantity_box", FALSE)
			->select("SUM(ispd.quantity) quantity", FALSE)
			->from('m_inventory_shipmentdetails ispd')
			->join('m_inventory_pickingdetails ipgd', "ipgd.id = ispd.m_inventory_pickingdetail_id")
			->join('m_inventory_pickings ipg', "ipg.id = ipgd.m_inventory_picking_id")
			->join('m_inventory_picklistdetails ipld', "ipld.id = ipgd.m_inventory_picklistdetail_id")
			->join('m_inventory_picklists ipl', "ipl.id = ipld.m_inventory_picklist_id")
			->join('c_orderoutdetails ood', "ood.id = ipld.c_orderoutdetail_id")
			->join('c_orderouts oo', "oo.id = ood.c_orderout_id")
			->join('c_businesspartners bp', "bp.id = ipld.c_businesspartner_id", 'left')
			->join('m_products pro', "pro.id = ipld.m_product_id", 'left')
			->join('c_projects prj', "prj.id = ipld.c_project_id", 'left')
			->group_by(
				array(
					'ispd.packed_group', 'ipg.code', 'ipg.picking_date',
					'ipl.code', 'ipl.picklist_date',
					'oo.code', 'oo.orderout_date', 'oo.request_arrive_date',
					'ipld.c_businesspartner_id', 'bp.name',
					'ipld.c_project_id', 'prj.name',
					'ipld.m_product_id', 'pro.code', 'pro.name', 'pro.uom',
					'ipld.pallet', 'ipld.barcode', 'ipld.condition'
				)
			);
		
		$this->lib_custom->project_query_filter('ipld.c_project_id', $this->c_project_ids);
	}
	
	public function get_list_detail_json()
	{
		if (!is_authorized('material/inventory_shipment', 'index')) 
			access_denied();
		
		$id = $this->input->get_post('id');
		if ($id !== '')
			$this->db->where("ispd.m_inventory_shipment_id", $id);
		
		$this->get_list_detail_query();
		
		parent::_get_list_json();
	}
	
	public function get_list_detail_excel()
	{
		if (!is_authorized('material/inventory_shipment', 'index')) 
			access_denied();
		
		$id = $this->input->get_post('id');
		
		$this->db
			->select("isp.id, isp.code, isp.shipment_date")
			->select("isp.shipment_type, isp.shipment_to")
			->select("isp.request_arrive_date, isp.estimated_time_arrive")
			->select("isp.vehicle_no, isp.vehicle_driver, isp.transport_mode, isp.police_name")
			->select("isp.notes")
			->from('m_inventory_shipments isp')
			->where('isp.id', $id);
		$table = $this->db->get();
		if ($table->num_rows() == 0)
			show_error("Shipment not found", 400);
		$header_record = $table->first_row();
				
		$this->db
			->select("ipld.lot_no")
			->select("oo.external_no c_orderout_external_no, oo.marketing_unit c_orderout_marketing_unit, oo.no_surat_jalan c_orderout_no_surat_jalan")
			->select("isp.vehicle_no m_inventory_shipment_vehicle_no, isp.vehicle_driver m_inventory_shipment_vehicle_driver")
			->join('m_inventory_shipments isp', "isp.id = ispd.m_inventory_shipment_id")
			->where("ispd.m_inventory_shipment_id", $id)
			->group_by(
				array(
					'ipld.lot_no',
					'oo.external_no', 'oo.marketing_unit', 'oo.no_surat_jalan',
					'isp.vehicle_no', 'isp.vehicle_driver'
				)
			);
		$this->get_list_detail_query();
		
		$this->load->library('jqgrid');
		
		$header_captions = array(
			'm_product_code'						=> 'Product Code',
			'm_product_name'						=> 'Name',
			'barcode'								=> 'Barcode', 
			'quantity_box'							=> 'Box',
			'quantity'								=> 'Quantity',
			'm_product_uom'							=> 'UOM',
			'pallet'								=> 'Pallet',
			'lot_no'								=> 'Lot',
			'condition'								=> 'Condition',
			'packed_group'							=> 'Packed Group',
			'm_inventory_picking_code'				=> 'Picking No', 
			'm_inventory_picking_date'				=> 'Picking Date',
			'm_inventory_picklist_code'				=> 'Pick List No', 
			'm_inventory_picklist_date'				=> 'Pick List Date',
			'c_orderout_code'						=> 'Order Out No', 
			'c_orderout_date'						=> 'Order Out Date',
			'request_arrive_date'					=> 'Request Arrival', 
			'c_businesspartner_name'				=> 'Business Partner',
			'c_project_name'						=> 'Project',
			'c_orderout_external_no'				=> 'External No',
			'c_orderout_marketing_unit'				=> 'Marketing Unit',
			'c_orderout_no_surat_jalan'				=> 'No Surat Jalan',
			'm_inventory_shipment_vehicle_no'		=> 'Vechicle No',
			'm_inventory_shipment_vehicle_driver'	=> 'Vechicle Driver'
		);
		
		$data = $this->jqgrid->result();
		
		$result = $data->data;
		
		$this->load->helper('file');
		$file_name = get_clear_filename($header_record->code .'__'. $header_record->shipment_date);
		
		$this->load->library('excel');
		$this->excel->write_stream_acos(
			array(
				'Out' => $result
			), 
			'ShipmentDetail__'.$file_name.'.xls',
			array(
				'Out' => $header_captions
			)
		);
	}
	
	public function insert()
	{
		if (!is_authorized('material/inventory_shipment', 'insert')) 
			access_denied();
		
		$this->load->library('material/lib_inventory_out');
		
		$user_id = $this->session->userdata('user_id');
		
		$data = new stdClass();
		$data->code = $this->input->post('code');
		$data->shipment_date = $this->input->post('shipment_date');
		$data->shipment_type = $this->input->post('shipment_type');
		$data->request_arrive_date = $this->input->post('request_arrive_date');
		$data->estimated_time_arrive = $this->input->post('estimated_time_arrive');
		$data->shipment_to = $this->input->post('shipment_to');
		$data->vehicle_no = $this->input->post('vehicle_no');
		$data->vehicle_driver = $this->input->post('vehicle_driver');
		$data->transport_mode = $this->input->post('transport_mode');
		$data->police_name = $this->input->post('police_name');
		$data->notes = $this->input->post('notes');
		
		parent::_execute('lib_inventory_out', 'shipment_add', 
			array($data, $user_id),
			array(
				array('field' => 'code', 'label' => 'No', 'rules' => 'required'),
				array('field' => 'shipment_date', 'label' => 'Date', 'rules' => 'required')
			)
		);
	}
	
	public function insert_detail()
	{
		if (!is_authorized('material/inventory_shipment', 'insert')) 
			access_denied();
		
		$user_id = $this->session->userdata('user_id');
		
		$this->load->library('material/lib_inventory_out');
		
		$data = new stdClass();
		$data->m_inventory_shipment_id = $this->input->post('m_inventory_shipment_id');
		$data->m_inventory_picking_id = $this->input->post('m_inventory_picking_id');
		$data->barcode = $this->input->post('barcode');
		$data->pallet = $this->input->post('pallet');
		$data->condition = $this->input->post('condition');
		$data->packed_group = $this->input->post('packed_group');
		$data->repacked_group = $this->input->post('repacked_group');
		$data->quantity_box = $this->input->post('quantity_box');
		
		parent::_execute('lib_inventory_out', 'shipmentdetail_add_by_properties', 
			array($data, $user_id),
			array(
				array('field' => 'm_inventory_shipment_id', 'label' => 'Inventory Shipment', 'rules' => 'integer|required'),
				array('field' => 'm_inventory_picking_id', 'label' => 'Inventory Picking', 'rules' => 'integer|required')
			)
		);
	}
	
	public function update($id)
	{
		if (!is_authorized('material/inventory_shipment', 'update')) 
			access_denied();
		
		$this->load->library('material/lib_inventory_out');
		
		$user_id = $this->session->userdata('user_id');
		
		$data = new stdClass();
		$data->code = $this->input->post('code');
		$data->shipment_date = $this->input->post('shipment_date');
		$data->shipment_type = $this->input->post('shipment_type');
		$data->request_arrive_date = $this->input->post('request_arrive_date');
		$data->estimated_time_arrive = $this->input->post('estimated_time_arrive');
		$data->shipment_to = $this->input->post('shipment_to');
		$data->vehicle_no = $this->input->post('vehicle_no');
		$data->vehicle_driver = $this->input->post('vehicle_driver');
		$data->transport_mode = $this->input->post('transport_mode');
		$data->police_name = $this->input->post('police_name');
		$data->notes = $this->input->post('notes');
		
		parent::_execute('lib_inventory_out', 'shipment_update', 
			array($id, $data, $user_id), 
			array(
				array('field' => 'code', 'label' => 'No', 'rules' => 'required'),
				array('field' => 'shipment_date', 'label' => 'Date', 'rules' => 'required')
			)
		);
	}
	
	public function delete($id)
	{
		if (!is_authorized('material/inventory_shipment', 'delete')) 
			access_denied();
		
		$this->load->library('material/lib_inventory_out');
		
		$user_id = $this->session->userdata('user_id');
		
		parent::_execute('lib_inventory_out', 'shipment_remove', array($id, $user_id));
	}
	
	public function delete_detail()
	{
		if (!is_authorized('material/inventory_shipment', 'delete')) 
			access_denied();
		
		$user_id = $this->session->userdata('user_id');
		
		$this->load->library('material/lib_inventory_out');
		
		$data = new stdClass();
		$data->m_inventory_shipment_id = $this->input->post('m_inventory_shipment_id');
		$data->m_inventory_picking_id = $this->input->post('m_inventory_picking_id');
		$m_product_id = $this->input->post('m_product_id');
		if ($m_product_id !== '')
			$data->m_product_id = $m_product_id;
		else
			$data->m_product_id = NULL;
		$barcode = $this->input->post('barcode');
		if ($barcode !== '')
			$data->barcode = $barcode;
		else
			$data->barcode = NULL;
		$pallet = $this->input->post('pallet');
		if ($pallet !== '')
			$data->pallet = $pallet;
		else
			$data->pallet = NULL;
		$condition = $this->input->post('condition');
		if ($condition !== '')
			$data->condition = $condition;
		else
			$data->condition = NULL;
		$packed_group = $this->input->post('packed_group');
		if ($packed_group !== '')
			$data->packed_group = $packed_group;
		else
			$data->packed_group = NULL;
		
		parent::_execute('lib_inventory_out', 'shipmentdetail_remove_by_properties', 
			array($data, $user_id),
			array(
				array('field' => 'm_inventory_shipment_id', 'label' => 'Inventory Shipment', 'rules' => 'integer|required'),
				array('field' => 'm_inventory_picking_id', 'label' => 'Inventory Picking', 'rules' => 'integer|required')
			)
		);
	}

	public function tally_sheet()
	{
		if (!is_authorized('material/inventory_shipment', 'index')) 
			access_denied();
		
		$id = $this->input->get_post('id');
		
		$this->db
			->select("isp.id, isp.code, isp.shipment_date")
			->select("isp.shipment_type, isp.shipment_to")
			->select("isp.request_arrive_date, isp.estimated_time_arrive")
			->select("isp.vehicle_no, isp.vehicle_driver, isp.transport_mode, isp.police_name")
			->select("isp.notes")
			->from('m_inventory_shipments isp')
			->where('isp.id', $id);
		$table = $this->db->get();
		if ($table->num_rows() == 0)
			show_error("Shipment not found", 400);
		$header_record = $table->first_row();
		
		$this->db
			->select("ispd.m_inventory_pickingdetail_id")
			->select("ood.c_orderout_id")
			->select("oo.code c_orderout_code, oo.orderout_date c_orderout_date, oo.request_arrive_date c_orderout_request_arrive_date")
			->select("oo.external_no c_orderout_external_no")
			->select("ipld.c_businesspartner_id, bp.name c_businesspartner_name")
			->select("ipld.c_project_id, prj.name c_project_name")
			->select("ispd.packed_group")
			->select("ipld.m_product_id, pro.code m_product_code, pro.name m_product_name, pro.uom m_product_uom")
			->select("ipld.carton_no")
			->select("ispd.quantity, ispd.quantity_box")
			->select("ispd.quantity / ispd.quantity_box quantity_avg", FALSE)
			->from('m_inventory_shipmentdetails ispd')
			->join('m_inventory_pickingdetails ipgd', "ipgd.id = ispd.m_inventory_pickingdetail_id")
			->join('m_inventory_picklistdetails ipld', "ipld.id = ipgd.m_inventory_picklistdetail_id")
			->join('c_orderoutdetails ood', "ood.id = ipld.c_orderoutdetail_id")
			->join('c_orderouts oo', "oo.id = ood.c_orderout_id")
			->join('c_businesspartners bp', "bp.id = ipld.c_businesspartner_id", 'left')
			->join('m_products pro', "pro.id = ipld.m_product_id", 'left')
			->join('c_projects prj', "prj.id = ipld.c_project_id", 'left')
			->where("ispd.m_inventory_shipment_id", $id);
		$this->lib_custom->project_query_filter('ipld.c_project_id', $this->c_project_ids);
		$table = $this->db
			->order_by('c_orderout_id', 'asc')
			->order_by('c_businesspartner_id', 'asc')
			->order_by('c_project_id', 'asc')
			->order_by('m_product_name', 'asc')
			->order_by('packed_group', 'asc')
			->order_by('m_inventory_pickingdetail_id', 'asc')
			->get();
		$records = $table->result();
		
		$grouped_records = array();
		foreach ($records as $record_idx=>$record)
		{
			$header_key = md5(
				 'OO_' .$record->c_orderout_id
				.'BP_' .$record->c_businesspartner_id
				.'PRJ_'.$record->c_project_id
			);
			$product_key = $record->m_product_id;
			$packed_group_key = $record->packed_group;
			$carton_key = $record->carton_no;
			
			if (!isset($grouped_records[$header_key]))
			{
				$grouped_header_record = new stdClass();
				$grouped_header_record->code = $header_record->code;
				$grouped_header_record->shipment_date = $header_record->shipment_date;
				$grouped_header_record->c_orderout_code = $record->c_orderout_code;
				$grouped_header_record->c_businesspartner_name = $record->c_businesspartner_name;
				$grouped_header_record->c_project_name = $record->c_project_name;
				$grouped_header_record->m_inventory_shipment_vehicle_no = $header_record->vehicle_no;
				$grouped_header_record->m_inventory_shipment_vehicle_driver = $header_record->vehicle_driver;
				$grouped_header_record->c_orderout_external_no = $record->c_orderout_external_no;
				$grouped_header_record->products = array();
				$grouped_records[$header_key] = $grouped_header_record;
			}
			
			if (!isset($grouped_records[$header_key]->products[$product_key]))
			{
				$grouped_product_record = new stdClass();
				$grouped_product_record->m_product_id = $record->m_product_id;
				$grouped_product_record->m_product_code = $record->m_product_code;
				$grouped_product_record->m_product_name = $record->m_product_name;
				$grouped_product_record->m_product_uom = $record->m_product_uom;
				$grouped_product_record->packed_groups = array();
				$grouped_records[$header_key]->products[$product_key] = $grouped_product_record;
			}
			
			if (!isset($grouped_records[$header_key]->products[$product_key]->packed_groups[$packed_group_key]))
			{
				$grouped_packed_group_record = new stdClass();
				$grouped_packed_group_record->packed_group = $record->packed_group;
				$grouped_packed_group_record->cartons = array();
				$grouped_records[$header_key]->products[$product_key]->packed_groups[$packed_group_key] = $grouped_packed_group_record;
			}
			
			for ($quantity_box = 1; $quantity_box <= $record->quantity_box; $quantity_box++)
			{
				$grouped_carton_record = new stdClass();
				$grouped_carton_record->carton_no = $record->carton_no;
				$grouped_carton_record->quantity = $record->quantity_avg;
				$grouped_records[$header_key]->products[$product_key]->packed_groups[$packed_group_key]->cartons[] = $grouped_carton_record;
			}
		}
		
		$data = array(
			'data'	=> $grouped_records
		);
		$html = $this->load->view('material/inventory_shipment/tally_sheet', $data, TRUE);
		
		// echo $html;
		$this->load->library('lib_dompdf');
		$this->lib_dompdf->load_as_pdf($html, 'tally_sheet_out.pdf', 'a4', 'portrait');
	}
}