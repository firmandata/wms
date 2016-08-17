<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Inventorylog extends MY_Controller 
{
	public function __construct()
	{
		parent::__construct(TRUE, 'backend');
	}
	
	public function index()
	{
		if (!is_authorized('material/inventorylog', 'index')) 
			access_denied();
		
		$data = array(
		);
		
		$content = array(
			'title'		=> "Inventory Log",
			'content' 	=> $this->load->view('material/inventorylog/index', $data, TRUE)
		);
		$this->_load_layout($content);
	}
	
	public function get_list_json()
	{
		if (!is_authorized('material/inventorylog', 'index')) 
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
		
		$date_current_start = $date_from.' 00:00:00';
		$date_current_end = $date_to.' 23:59:59';
		
		$this->db
			->select("pro.id m_product_id, pro.code m_product_code, pro.name m_product_name, pro.uom m_product_uom, pro.pack m_product_pack")
			->select_if_null("SUM(CASE WHEN invl.created < '".$date_current_start."' THEN invl.quantity_box ELSE 0 END)", 0, 'quantity_box_start')
			->select_if_null("SUM(CASE WHEN invl.created >= '".$date_current_start."' THEN invl.quantity_box ELSE 0 END)", 0, 'quantity_box_change')
			->select_if_null("SUM(invl.quantity_box)", 0, 'quantity_box_end')
			->select_if_null("SUM(CASE WHEN invl.created < '".$date_current_start."' THEN invl.quantity ELSE 0 END)", 0, 'quantity_start')
			->select_if_null("SUM(CASE WHEN invl.created >= '".$date_current_start."' THEN invl.quantity ELSE 0 END)", 0, 'quantity_change')
			->select_if_null("SUM(invl.quantity)", 0, 'quantity_end')
			->from('m_inventorylogs invl')
			->join('m_products pro', "pro.id = invl.m_product_id")
			->where('invl.created <=', $date_current_end)
			->group_by(
				array(
					'pro.id', 'pro.code', 'pro.name', 'pro.uom', 'pro.pack'
				)
			);
		
		parent::_get_list_json();
	}
	
	public function detail()
	{
		if (!is_authorized('material/inventorylog', 'index')) 
			access_denied();
		
		$m_product_id = $this->input->get_post('m_product_id');
		$date_from = $this->input->get_post('date_from');
		$date_to = $this->input->get_post('date_to');
		$show_by = $this->input->get_post('show_by');
		
		$data = array(
			'm_product_id'	=> $m_product_id,
			'date_from'		=> $date_from,
			'date_to'		=> $date_to,
			'show_by'		=> $show_by
		);
		
		$this->load->view('material/inventorylog/detail', $data);
	}
	
	public function get_list_detail_json()
	{
		if (!is_authorized('material/inventorylog', 'index')) 
			access_denied();
		
		$m_product_id = $this->input->get_post('m_product_id');
		$show_by = $this->input->get_post('show_by');
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
		
		$date_current_start = $date_from.' 00:00:00';
		$date_current_end = $date_to.' 23:59:59';
		
		$this->db
			->select("pro.id m_product_id, pro.code m_product_code, pro.name m_product_name, pro.uom m_product_uom")
			->select("grd.id m_grid_id, grd.code m_grid_code")
			->select("wh.id m_warehouse_id, wh.code m_warehouse_code, wh.name m_warehouse_name")
			->select("prog.id m_productgroup_id, prog.code m_productgroup_code, prog.name m_productgroup_name")
			->select("invl.log_type, invl.ref1_code, invl.ref2_code")
			->select("invl.quantity, invl.quantity_box")
			->select("invl.barcode, invl.pallet, invl.condition")
			->select("invl.created, invl.notes")
			->from('m_inventorylogs invl')
			->join('m_products pro', "pro.id = invl.m_product_id", 'left')
			->join('m_grids grd', "grd.id = invl.m_grid_id", 'left')
			->join('m_warehouses wh', "wh.id = grd.m_warehouse_id", 'left')
			->join('m_productgroups prog', "prog.id = grd.m_productgroup_id", 'left')
			->where('invl.m_product_id', $m_product_id);
		if ($show_by == 'change')
		{
			$this->db
				->where('invl.created >=', $date_current_start)
				->where('invl.created <=', $date_current_end);
		}
		elseif ($show_by == 'start')
		{
			$this->db
				->where('invl.created <', $date_current_start);
		}
		
		parent::_get_list_json();
	}
}