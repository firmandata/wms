<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Home extends MY_Controller 
{
	public function __construct()
	{
		parent::__construct(TRUE, 'backend');
	}
	
	public function index()
	{
		if (!is_authorized('system/home', 'index')) 
			access_denied();
		
		$data = array(
			
		);
		
		$content = array(
			'title'		=> "Home",
			'content' 	=> $this->load->view('system/home/index', $data, TRUE)
		);
		$this->_load_layout($content);
	}
	
	public function inventory_warehouse()
	{
		if (!is_authorized('system/home', 'index')) 
			access_denied();
		
		$table = $this->db
			->select("gri.m_warehouse_id, wh.code m_warehouse_code, wh.name m_warehouse_name")
			->select_if_null("SUM(gri_use.quantity)", 0, 'quantity')
			->select_if_null("COUNT(DISTINCT gri.id)", 0, 'm_grid_count')
			->select_if_null("COUNT(DISTINCT gri_use.m_grid_id)", 0, 'm_grid_used')
			->from('m_warehouses wh')
			->join('m_grids gri', "gri.m_warehouse_id = wh.id")
			->join('cus_m_grid_usages gri_use', "gri_use.m_grid_id = gri.id AND gri_use.quantity_box > 0 AND gri_use.quantity > 0", 'left')
			->where('wh.code <>', $this->config->item('inventory_default_warehouse'))
			->group_by(
				array(
					'gri.m_warehouse_id', 'wh.code', 'wh.name'
				)
			)
			->get();
		$table_records = $table->result();
		$used_total = 0;
		$m_grid_total = 0;
		foreach ($table_records as $table_record)
		{
			$used_total += $table_record->m_grid_used;
			$m_grid_total += $table_record->m_grid_count;
		}
		$records = array();
		foreach ($table_records as $table_record)
		{
			$record = new stdClass();
			$record->m_warehouse_name = $table_record->m_warehouse_name;
			$record->used = $table_record->m_grid_used;
			$record->used_percent = $table_record->m_grid_used / $table_record->m_grid_count * 100;
			$record->free = $table_record->m_grid_count - $table_record->m_grid_used;
			$record->free_percent = ($table_record->m_grid_count - $table_record->m_grid_used) / $table_record->m_grid_count * 100;
			$record->quantity = $table_record->quantity;
			$record->all_used_percent = $table_record->m_grid_used / $m_grid_total * 100;
			$record->all_free_percent = ($table_record->m_grid_count - $table_record->m_grid_used) / $m_grid_total * 100;
			$records[] = $record;
		}
		
		$data = array(
			'records'		=> $records,
			'used_total'	=> $used_total,
			'm_grid_total'	=> $m_grid_total
		);
		$this->load->view('system/home/inventory_warehouse', $data);
	}
	
	public function inventory_stock_accuration()
	{
		if (!is_authorized('system/home', 'index')) 
			access_denied();
		
		$data = array();
		$this->load->view('system/home/inventory_stock_accuration', $data);
	}
	
	public function inventory_stock_accuration_data()
	{
		if (!is_authorized('system/home', 'index')) 
			access_denied();
		
		$year = date('Y');
		$year_input = $this->input->get_post('year');
		if ($year_input)
			$year = (int)$year_input;
		
		$month = date('n');
		$month_input = $this->input->get_post('month');
		if ($year_input)
			$month = (int)$month_input;
		
		$table = $this->db
			->select("ood.m_product_id, pro.code m_product_code, pro.name m_product_name")
			->select_if_null("SUM(CASE WHEN ood.quantity - ipld.quantity < 0 THEN ipld.quantity - ood.quantity ELSE ood.quantity - ipld.quantity END)", 0, 'count_do_miss')
			->select_if_null("SUM(ood.quantity)", 0, 'count_do')
			// ->select_if_null("COUNT(DISTINCT CASE WHEN ood.quantity <> ipld.quantity THEN ood.c_orderout_id ELSE NULL END)", 0, 'count_do_miss')
			// ->select_if_null("COUNT(DISTINCT ood.c_orderout_id)", 0, 'count_do')
			->from('c_orderoutdetails ood')
			->join('m_products pro', "pro.id = ood.m_product_id")
			->join('c_orderouts oo', "oo.id = ood.c_orderout_id")
			->join(
				   "(SELECT c_orderoutdetail_id, "
				  ."		". $this->db->if_null("SUM(quantity)", 0) ." quantity "
				  ."   FROM m_inventory_picklistdetails "
				  ."  GROUP BY c_orderoutdetail_id"
				  .") ipld"
				, "ipld.c_orderoutdetail_id = ood.id"
			)
			->where("YEAR(oo.orderout_date)", $year, FALSE)
			->where("MONTH(oo.orderout_date)", $month, FALSE)
			->group_by(
				array(
					'ood.m_product_id', 'pro.code', 'pro.name'
				)
			)
			->get();
		$table_records = $table->result();
		
		$records = array();
		foreach ($table_records as $table_record)
		{
			$table_record->accuration = number_format(($table_record->count_do - $table_record->count_do_miss) / ($table_record->count_do > 0 ? $table_record->count_do : 1) * 100, 2);
			$records[] = $table_record;
		}
		
		$response = new stdClass();
		$response->data = $records;
		$response->userdata = array();
		$response->page = 1; 
        $response->total = 1; 
        $response->records = count($records);
		
		$this->result_json($response);
	}
	
	public function inventory_ontime_delivery()
	{
		if (!is_authorized('system/home', 'index')) 
			access_denied();
		
		$data = array();
		$this->load->view('system/home/inventory_ontime_delivery', $data);
	}
	
	public function inventory_ontime_delivery_data()
	{
		if (!is_authorized('system/home', 'index')) 
			access_denied();
		
		$year = date('Y');
		$year_input = $this->input->get_post('year');
		if ($year_input)
			$year = (int)$year_input;
		
		$table = $this->db
			->select("MONTH(oo.orderout_date) orderout_date_month", FALSE)
			->select("COUNT(DISTINCT CASE WHEN oo.orderout_date < isp.shipment_date THEN oo.id ELSE NULL END) count_do_miss", FALSE)
			->select("COUNT(DISTINCT oo.id) count_do", FALSE)
			->from('c_orderouts oo')
			->join('c_orderoutdetails ood', "ood.c_orderout_id = oo.id")
			->join('m_inventory_picklistdetails ipld', "ipld.c_orderoutdetail_id = ood.id")
			->join('m_inventory_pickingdetails iplg', "iplg.m_inventory_picklistdetail_id = ipld.id")
			->join('m_inventory_shipmentdetails ispg', "ispg.m_inventory_pickingdetail_id = iplg.id")
			->join('m_inventory_shipments isp', "isp.id = ispg.m_inventory_shipment_id")
			->where("YEAR(oo.orderout_date)", $year, FALSE)
			->group_by(
				array(
					'MONTH(oo.orderout_date)'
				)
			)
			->get();
		$records = $table->result();
		
		$months = $this->config->item('months');
		$delivery_records = array();
		foreach ($records as $record_idx=>$record)
		{
			$delivery_record = new stdClass();
			$delivery_record->Row = $months[$record->orderout_date_month];
			$delivery_record->Column = 'Target';
			$delivery_record->Cell = $record->count_do;
			$delivery_records[] = $delivery_record;
			
			$delivery_record = new stdClass();
			$delivery_record->Row = $months[$record->orderout_date_month];
			$delivery_record->Column = 'Miss';
			$delivery_record->Cell = $record->count_do_miss;
			$delivery_records[] = $delivery_record;
		}
		
		$this->result_json($delivery_records);
	}
	
	public function inventory_top10_product()
	{
		if (!is_authorized('system/home', 'index')) 
			access_denied();
		
		$data = array();
		$this->load->view('system/home/inventory_top10_product', $data);
	}
	
	public function inventory_top10_product_data()
	{
		if (!is_authorized('system/home', 'index')) 
			access_denied();
		
		$year = date('Y');
		$year_input = $this->input->get_post('year');
		if ($year_input)
			$year = (int)$year_input;
		
		$month = date('n');
		$month_input = $this->input->get_post('month');
		if ($year_input)
			$month = (int)$month_input;
		
		$table = $this->db
			->select("ood.m_product_id, pro.code m_product_code, pro.name m_product_name")
			->select("COUNT(DISTINCT oo.id) count_do", FALSE)
			->from('c_orderouts oo')
			->join('c_orderoutdetails ood', "ood.c_orderout_id = oo.id")
			->join('m_products pro', "pro.id = ood.m_product_id")
			->where("YEAR(oo.orderout_date)", $year, FALSE)
			->where("MONTH(oo.orderout_date)", $month, FALSE)
			->group_by(
				array(
					'ood.m_product_id', 'pro.code', 'pro.name'
				)
			)
			->having("COUNT(DISTINCT oo.id) >=", 8)
			->order_by('count_do', 'desc')
			->limit(10)
			->get();
		$records = $table->result();
		
		$data = array(
			'records'	=> $records
		);
		$this->load->view('system/home/inventory_top10_product_data', $data);
	}
	
	public function inventory_product_position()
	{
		if (!is_authorized('system/home', 'index')) 
			access_denied();
		
		$data = array();
		$this->load->view('system/home/inventory_product_position', $data);
	}
	
	public function inventory_product_position_data()
	{
		if (!is_authorized('system/home', 'index')) 
			access_denied();
		
		$this->db
			->select("inv.m_product_id, pro.code m_product_code, pro.name m_product_name")
			->select_if_null("SUM(inv.quantity_onhand)", 0, 'quantity_onhand')
			->from('m_inventories inv')
			->join('m_products pro', "pro.id = inv.m_product_id")
			->group_by(
				array(
					'inv.m_product_id', 'pro.code', 'pro.name'
				)
			);
		
		parent::_get_list_json();
	}
	
	public function inventory_warehouse_throughput()
	{
		if (!is_authorized('system/home', 'index')) 
			access_denied();
		
		$this->load->helper('date');
		
		$days = array();
		$now = date('Y-m-d');
		for ($day = 9; $day >= 0; $day--)
		{
			$days[] = date('d', strtotime(add_date($now, $day * -1)));
		}
		
		$data = array(
			'days'	=> $days
		);
		$this->load->view('system/home/inventory_warehouse_throughput', $data);
	}
	
	public function inventory_warehouse_throughput_data()
	{
		if (!is_authorized('system/home', 'index')) 
			access_denied();
		
		$this->load->helper('date');
		
		$now = date('Y-m-d');
		$now_input = $this->input->get_post('now');
		if ($now_input)
			$now = $now_input;
		
		$dates = array();
		for ($day = 9; $day >= 0; $day--)
		{
			$dates[] = add_date($now, $day * -1);
		}
		
		// -- Get Products --
		$table = $this->db
			->select("pro.id m_product_id, pro.code m_product_code, pro.name m_product_name")
			->from('m_products pro')
			->get();
		$product_records = $table->result();
		
		// -- Get In --
		$table = $this->db
			->select('oid.m_product_id')
			->select('ir.receive_date')
			->select_if_null("SUM(ird.quantity)", 0, 'quantity')
			->from('m_inventory_receivedetails ird')
			->join('m_inventory_receives ir', "ir.id = ird.m_inventory_receive_id")
			->join('c_orderindetails oid', "oid.id = ird.c_orderindetail_id")
			->where('ir.receive_date >=', $dates[0])
			->where('ir.receive_date <=', end($dates))
			->group_by(
				array(
					'oid.m_product_id',
					'ir.receive_date'
				)
			)
			->get();
		$in_records = $table->result();
		
		// -- Get Out --
		$table = $this->db
			->select('ipld.m_product_id')
			->select('ish.shipment_date')
			->select_if_null("SUM(ishd.quantity)", 0, 'quantity')
			->from('m_inventory_shipmentdetails ishd')
			->join('m_inventory_shipments ish', "ish.id = ishd.m_inventory_shipment_id")
			->join('m_inventory_pickingdetails ipgd', "ipgd.id = ishd.m_inventory_pickingdetail_id")
			->join('m_inventory_picklistdetails ipld', "ipld.id = ipgd.m_inventory_picklistdetail_id")
			->where('ish.shipment_date >=', $dates[0])
			->where('ish.shipment_date <=', end($dates))
			->group_by(
				array(
					'ipld.m_product_id',
					'ish.shipment_date'
				)
			)
			->get();
		$out_records = $table->result();
		
		// -- Populate records --
		$records = array();
		foreach ($product_records as $product_record)
		{
			$record = new stdClass();
			$record->m_product_id = $product_record->m_product_id;
			$record->m_product_code = $product_record->m_product_code;
			$record->m_product_name = $product_record->m_product_name;
			foreach ($dates as $date)
			{
				$day = date('d', strtotime($date));
				$field_in = 'quantity_in_'.$day;
				$record->$field_in = 0;
				$field_out = 'quantity_out_'.$day;
				$record->$field_out = 0;
			}
			
			foreach ($in_records as $in_record)
			{
				if ($record->m_product_id == $in_record->m_product_id)
				{
					$day = date('d', strtotime($in_record->receive_date));
					$field_in = 'quantity_in_'.$day;
					$record->$field_in = $in_record->quantity;
				}
			}
			
			foreach ($out_records as $out_record)
			{
				if ($record->m_product_id == $out_record->m_product_id)
				{
					$day = date('d', strtotime($out_record->shipment_date));
					$field_out = 'quantity_out_'.$day;
					$record->$field_out = $out_record->quantity;
				}
			}
			
			$records[] = $record;
		}
		
		$response = new stdClass();
		$response->data = $records;
		$response->userdata = array();
		$response->page = 1; 
        $response->total = 1; 
        $response->records = count($records);
		
		$this->result_json($response);
	}
}