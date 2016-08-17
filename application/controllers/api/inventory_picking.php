<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'/libraries/REST_Controller.php';

class Inventory_picking extends REST_Controller
{
	private $c_project_ids;
	
	function __construct()
	{
		parent::__construct();
		
		$this->load->library('custom/lib_custom');
		
		$user_id = $this->session->userdata('user_id');
		$this->c_project_ids = $this->lib_custom->project_get_ids($user_id);
	}
	
	function list_get()
	{
		if (!is_authorized('material/inventory_picking', 'index')) 
			access_denied();
		
		$this->load->helper('date');
		
		$from_month = $this->input->get_post('from_month');
		$from_year = $this->input->get_post('from_year');
		
		$to_month = $this->input->get_post('to_month');
		$to_year = $this->input->get_post('to_year');
		
		$this->db
			->select("ipg.id, ipg.code, ipg.picking_date")
			->select("ipld.m_inventory_picklist_id, ipl.code m_inventory_picklist_code, ipl.picklist_date m_inventory_picklist_date")
			->select("ood.c_orderout_id, oo.code c_orderout_code, oo.orderout_date c_orderout_date, oo.request_arrive_date c_orderout_request_arrive_date")
			->select("oo.c_businesspartner_id, bp.name c_businesspartner_name")
			->select("oo.c_project_id, prj.name c_project_name")
			->select("ipg.status_inventory_shipment")
			->from('m_inventory_pickings ipg')
			->join('m_inventory_pickingdetails ipgd', "ipgd.m_inventory_picking_id = ipg.id", 'left')
			->join('m_inventory_picklistdetails ipld', "ipld.id = ipgd.m_inventory_picklistdetail_id", 'left')
			->join('m_inventory_picklists ipl', "ipl.id = ipld.m_inventory_picklist_id", 'left')
			->join('c_orderoutdetails ood', "ood.id = ipld.c_orderoutdetail_id", 'left')
			->join('c_orderouts oo', "oo.id = ood.c_orderout_id", 'left')
			->join('c_businesspartners bp', "bp.id = oo.c_businesspartner_id", 'left')
			->join('c_projects prj', "prj.id = oo.c_project_id", 'left')
			->group_by(
				array(
					'ipg.id', 'ipg.code', 'ipg.picking_date',
					'ipld.m_inventory_picklist_id', 'ipl.code', 'ipl.picklist_date',
					'ood.c_orderout_id', 'oo.code', 'oo.orderout_date', 'oo.request_arrive_date',
					'oo.c_businesspartner_id', 'bp.name',
					'oo.c_project_id', 'prj.name',
					'ipg.status_inventory_shipment'
				)
			);
		
		if ($from_month && $from_year)
			$this->db->where("ipg.picking_date >=", date('Y-m-d', mktime(0, 0, 0, $from_month, 1, $from_year)));
		
		if ($to_month && $to_year)
			$this->db->where("ipg.picking_date <=", add_date(date('Y-m-d', mktime(0, 0, 0, $to_month, 1, $to_year)), -1, 1));
		
		$this->lib_custom->project_query_filter('ipld.c_project_id', $this->c_project_ids);
		
		parent::_get_list_json();
	}
	
	function detail_get()
	{
		if (!is_authorized('material/inventory_picking', 'index')) 
			access_denied();
		
		$result = new stdClass();
		$result->response = FALSE;
		$result->value = NULL;
		$result->data = array();
		
		$id = $this->input->get_post('id');
		$this->db
			->select("pck.id, pck.code, pck.picking_date, pck.status_inventory_shipment, pck.notes")
			->from('m_inventory_pickings pck')
			->where('pck.id', $id);
		$table = $this->db->get();
		if ($table->num_rows() > 0)
		{
			$result->value = $table->first_row();
			$result->response = TRUE;
		}
		else
			show_error("Picking not found", 400);
		
		$this->result_json($result);
	}
	
	function detail_list_get()
	{
		if (!is_authorized('material/inventory_picking', 'index')) 
			access_denied();
		
		$this->db
			->select("MAX(ipgd.id) id", FALSE)
			->select("ipld.m_inventory_picklist_id, ipgd.packed_group, ipl.code m_inventory_picklist_code, ipl.picklist_date m_inventory_picklist_date")
			->select("ood.c_orderout_id, oo.code c_orderout_code, oo.orderout_date c_orderout_date, oo.request_arrive_date c_orderout_request_arrive_date")
			->select("ipld.c_businesspartner_id, bp.name c_businesspartner_name")
			->select("ipld.c_project_id, prj.name c_project_name")
			->select("ipld.m_product_id, pro.code m_product_code, pro.name m_product_name, pro.uom m_product_uom")
			->select("ipld.m_grid_id, gri.code m_grid_code")
			->select("ipld.pallet, ipld.barcode, ipld.carton_no, ipld.lot_no, ipld.condition")
			->select("SUM(ipgd.quantity_box) quantity_box", FALSE)
			->select("SUM(ipgd.quantity) quantity", FALSE)
			->select_if_null('SUM(ishd.quantity_box)', 0, 'quantity_box_used')
			->select_if_null('SUM(ishd.quantity)', 0, 'quantity_used')
			->select("ipgd.status_inventory_shipment")
			->from('m_inventory_pickingdetails ipgd')
			->join('m_inventory_picklistdetails ipld', "ipld.id = ipgd.m_inventory_picklistdetail_id")
			->join('m_inventory_picklists ipl', "ipl.id = ipld.m_inventory_picklist_id")
			->join('c_orderoutdetails ood', "ood.id = ipld.c_orderoutdetail_id")
			->join('c_orderouts oo', "oo.id = ood.c_orderout_id")
			->join('m_grids gri', "gri.id = ipld.m_grid_id")
			->join('c_businesspartners bp', "bp.id = ipld.c_businesspartner_id", 'left')
			->join('m_products pro', "pro.id = ipld.m_product_id", 'left')
			->join('c_projects prj', "prj.id = ipld.c_project_id", 'left')
			->join(
				 "(SELECT m_inventory_pickingdetail_id, "
				."		  ". $this->db->if_null("SUM(quantity_box)", 0) . " quantity_box, "
				."		  ". $this->db->if_null("SUM(quantity)", 0) . " quantity "
				."	 FROM m_inventory_shipmentdetails "
				."  GROUP BY m_inventory_pickingdetail_id "
				.") ishd", 
				"ishd.m_inventory_pickingdetail_id = ipgd.id", 'left')
			->group_by(
				array(
					'ipld.m_inventory_picklist_id', 'ipgd.packed_group', 'ipl.code', 'ipl.picklist_date',
					'ood.c_orderout_id', 'oo.code', 'oo.orderout_date', 'oo.request_arrive_date',
					'ipld.c_businesspartner_id', 'bp.name',
					'ipld.c_project_id', 'prj.name',
					'ipld.m_product_id', 'pro.code', 'pro.name', 'pro.uom',
					'ipld.m_grid_id', 'gri.code',
					'ipld.pallet', 'ipld.barcode', 'ipld.carton_no', 'ipld.lot_no', 'ipld.condition',
					'ipgd.status_inventory_shipment'
				)
			);
		
		$id = $this->input->get_post('id');
		if ($id !== '')
			$this->db->where("ipgd.m_inventory_picking_id", $id);
		
		$this->lib_custom->project_query_filter('ipld.c_project_id', $this->c_project_ids);
		
		parent::_get_list_json();
	}
	
	function detail_picklist_get()
	{
		if (!is_authorized('material/inventory_picking', 'index')) 
			access_denied();
		
		$this->db
			->select("ipl.id, ipl.code, ipl.picklist_date")
			->select("ood.c_orderout_id, oo.code c_orderout_code, oo.orderout_date c_orderout_date, oo.request_arrive_date c_orderout_request_arrive_date")
			->select("oo.c_businesspartner_id, bp.name c_businesspartner_name")
			->select("oo.c_project_id, prj.name c_project_name")
			->select($this->db->if_null("SUM(ipld.quantity_box)", 0) . " quantity_box", FALSE)
			->select($this->db->if_null("SUM(ipld.quantity)", 0) . " quantity", FALSE)
			->select("ipl.created")
			->from('m_inventory_picklists ipl')
			->join('m_inventory_picklistdetails ipld', "ipld.m_inventory_picklist_id = ipl.id")
			->join('c_orderoutdetails ood', "ood.id = ipld.c_orderoutdetail_id", 'left')
			->join('c_orderouts oo', "oo.id = ood.c_orderout_id", 'left')
			->join('c_businesspartners bp', "bp.id = oo.c_businesspartner_id", 'left')
			->join('c_projects prj', "prj.id = oo.c_project_id", 'left')
			->where('ipld.status_inventory_picking <>', 'COMPLETE')
			->group_by(
				array(
					'ipl.id', 'ipl.code', 'ipl.picklist_date',
					'ood.c_orderout_id', 'oo.code', 'oo.orderout_date', 'oo.request_arrive_date',
					'oo.c_businesspartner_id', 'bp.name',
					'oo.c_project_id', 'prj.name',
					'ipl.created'
				)
			);
		
		$this->lib_custom->project_query_filter('ipld.c_project_id', $this->c_project_ids);
		
		parent::_get_list_json();
	}
	
	function insert_post()
	{
		if (!is_authorized('material/inventory_picking', 'insert')) 
			access_denied();
		
		$this->load->library('material/lib_inventory_out');
		
		$user_id = $this->session->userdata('user_id');
		
		$data = new stdClass();
		$data->code = $this->input->post('code');
		$data->picking_date = $this->input->post('picking_date');
		$data->notes = $this->input->post('notes');
		
		parent::_execute('lib_inventory_out', 'picking_add', 
			array($data, $user_id),
			array(
				array('field' => 'code', 'label' => 'No', 'rules' => 'required'),
				array('field' => 'picking_date', 'label' => 'Date', 'rules' => 'required')
			)
		);
	}
	
	function update_post()
	{
		if (!is_authorized('material/inventory_picking', 'update')) 
			access_denied();
		
		$this->load->library('material/lib_inventory_out');
		
		$id = $this->input->post('id');
		$user_id = $this->session->userdata('user_id');
		
		$data = new stdClass();
		$data->code = $this->input->post('code');
		$data->picking_date = $this->input->post('picking_date');
		$data->notes = $this->input->post('notes');
		
		parent::_execute('lib_inventory_out', 'picking_update', 
			array($id, $data, $user_id), 
			array(
				array('field' => 'code', 'label' => 'No', 'rules' => 'required'),
				array('field' => 'picking_date', 'label' => 'Date', 'rules' => 'required')
			)
		);
	}
	
	function delete_post()
	{
		if (!is_authorized('material/inventory_picking', 'delete')) 
			access_denied();
		
		$this->load->library('material/lib_inventory_out');
		
		$id = $this->input->post('id');
		$user_id = $this->session->userdata('user_id');
		
		parent::_execute('lib_inventory_out', 'picking_remove', array($id, $user_id));
	}
	
	function insert_detail_post()
	{
		if (!is_authorized('material/inventory_picking', 'insert')) 
			access_denied();
		
		$user_id = $this->session->userdata('user_id');
		
		$this->load->library('material/lib_inventory_out');
		
		$data = new stdClass();
		$data->m_inventory_picking_id = $this->input->post('m_inventory_picking_id');
		$data->m_inventory_picklist_id = $this->input->post('m_inventory_picklist_id');
		$data->barcode = $this->input->post('barcode');
		$data->pallet = $this->input->post('pallet');
		$grid_code = $this->input->post('grid_code');
		if ($grid_code !== '')
		{
			$table = $this->db
				->select('id')
				->from('m_grids')
				->where('code', $grid_code)
				->get();
			if ($table->num_rows() > 0)
			{
				$table_record = $table->first_row();
				$data->m_grid_id = $table_record->id;
			}
		}
		$data->condition = $this->input->post('condition');
		$data->packed_group = $this->input->post('packed_group');
		$data->quantity_box = $this->input->post('quantity_box');
		
		parent::_execute('lib_inventory_out', 'pickingdetail_add_by_properties', 
			array($data, $user_id),
			array(
				array('field' => 'm_inventory_picking_id', 'label' => 'Inventory Picking', 'rules' => 'integer|required'),
				array('field' => 'm_inventory_picklist_id', 'label' => 'Inventory Pick List', 'rules' => 'integer|required')
			)
		);
	}
	
	function delete_detail_post()
	{
		if (!is_authorized('material/inventory_picking', 'delete')) 
			access_denied();
		
		$user_id = $this->session->userdata('user_id');
		
		$this->load->library('material/lib_inventory_out');
		
		$data = new stdClass();
		$data->m_inventory_picking_id = $this->input->post('m_inventory_picking_id');
		$data->m_inventory_picklist_id = $this->input->post('m_inventory_picklist_id');
		$m_product_id = $this->input->post('m_product_id');
		if ($m_product_id !== '')
			$data->m_product_id = $m_product_id;
		else
			$data->m_product_id = NULL;
		$m_grid_id = $this->input->post('m_grid_id');
		if ($m_grid_id !== '')
			$data->m_grid_id = $m_grid_id;
		else
			$data->m_grid_id = NULL;
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
		
		parent::_execute('lib_inventory_out', 'pickingdetail_remove_by_properties', 
			array($data, $user_id),
			array(
				array('field' => 'm_inventory_picking_id', 'label' => 'Inventory Picking', 'rules' => 'integer|required'),
				array('field' => 'm_inventory_picklist_id', 'label' => 'Inventory Pick List', 'rules' => 'integer|required')
			)
		);
	}
}