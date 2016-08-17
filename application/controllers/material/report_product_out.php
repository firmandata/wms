<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Report_product_out extends MY_Controller 
{
	public function __construct()
	{
		parent::__construct(TRUE, 'backend');
	}
	
	public function index()
	{
		if (!is_authorized('material/report_product_out', 'index')) 
			access_denied();
		
		$data = array(
		);
		
		$content = array(
			'title'		=> "Report Product Out",
			'content' 	=> $this->load->view('material/report_product_out/index', $data, TRUE)
		);
		$this->_load_layout($content);
	}
	
	protected function get_list_records()
	{
		$months = $this->config->item('months');
		
		$year = $this->input->get_post('year');
		$date_from = date($year.'-01-01');
		$date_to = date($year.'-12-31');
		
		$table = $this->db
			->select("id m_product_id, code m_product_code, name m_product_name")
			->from('m_products')
			->get();
		$m_product_records = $table->result();
		
		$table = $this->db
			->select("MONTH(oo.orderout_date) orderout_date_month", FALSE)
			->select("ood.m_product_id")
			->select_if_null("COUNT(DISTINCT oo.id)", 0, 'count_oo')
			->select_if_null("SUM(t_out.quantity)", 0, 'quantity')
			->from('c_orderouts oo')
			->join('c_orderoutdetails ood', "ood.c_orderout_id = oo.id")
			->join(
				   "(SELECT	  c_orderoutdetail_id "
				  ."		, SUM(quantity) quantity "
				  ."   FROM	m_inventory_picklistdetails"
				  ."  GROUP	BY c_orderoutdetail_id"
				  .") t_out"
				, "t_out.c_orderoutdetail_id = ood.id"
				, 'left'
			)
			->where("oo.orderout_date >=", $date_from)
			->where("oo.orderout_date <=", $date_to)
			->group_by(
				array(
					  'MONTH(oo.orderout_date)'
					, 'ood.m_product_id'
				)
			)
			->get();
		$table_records = $table->result();
		
		$report_records = array();
		foreach ($table_records as $table_record)
		{
			$key = $table_record->m_product_id;
			
			if (!isset($report_records[$key]))
			{
				$record = new stdClass();
				$record->m_product_id = $table_record->m_product_id;
				foreach ($months as $month_key=>$month_name)
				{
					$field_quantity = 'quantity_'.$month_key;
					$record->$field_quantity = 0;
					$field_count_oo = 'count_'.$month_key.'_oo';
					$record->$field_count_oo = 0;
					$field_item_type = 'item_type_'.$month_key;
					$record->$field_item_type = 'NON MOVING';
				}
				$record->count_oo = 0;
				$report_records[$key] = $record;
			}
			
			$month = $table_record->orderout_date_month;
			$field_quantity = 'quantity_'.$month;
			$report_records[$key]->$field_quantity += $table_record->quantity;
			$field_count_oo = 'count_'.$month.'_oo';
			$report_records[$key]->$field_count_oo += $table_record->count_oo;
			$report_records[$key]->count_oo += $table_record->count_oo;
		}
		
		$records = array();
		foreach ($m_product_records as $m_product_record)
		{
			$record = new stdClass();
			$record->m_product_id = $m_product_record->m_product_id;
			$record->m_product_code = $m_product_record->m_product_code;
			$record->m_product_name = $m_product_record->m_product_name;
			$record->item_type = 'NON MOVING';
			$record->count_oo = 0;
			if (isset($report_records[$m_product_record->m_product_id]))
			{
				foreach ($months as $month_key=>$month_name)
				{
					$field_quantity = 'quantity_'.$month_key;
					$record->$field_quantity = $report_records[$m_product_record->m_product_id]->$field_quantity;
					$field_count_oo = 'count_'.$month_key.'_oo';
					$record->$field_count_oo = $report_records[$m_product_record->m_product_id]->$field_count_oo;
					$field_item_type = 'item_type_'.$month_key;
					$record->$field_item_type = 'NON MOVING';
					if ($record->$field_count_oo >= 8)
						$record->$field_item_type = 'FAST MOVING';
					elseif ($record->$field_count_oo >= 4)
						$record->$field_item_type = 'MOVING';
					elseif ($record->$field_count_oo >= 1)
						$record->$field_item_type = 'SLOW MOVING';
				}
				$record->count_oo = $report_records[$m_product_record->m_product_id]->count_oo;
				if ($record->count_oo / 12 >= 8)
					$record->item_type = 'FAST MOVING';
				elseif ($record->count_oo / 12 >= 4)
					$record->item_type = 'MOVING';
				else
					$record->item_type = 'SLOW MOVING';
			}
			else
			{
				foreach ($months as $month_key=>$month_name)
				{
					$field_quantity = 'quantity_'.$month_key;
					$record->$field_quantity = 0;
					$field_count_oo = 'count_'.$month_key.'_oo';
					$record->$field_count_oo = 0;
					$field_item_type = 'item_type_'.$month_key;
					$record->$field_item_type = 'NON MOVING';
				}
			}
			$records[] = $record;
		}
		
		return $records;
	}
	
	public function get_list_json()
	{
		if (!is_authorized('material/report_product_out', 'index')) 
			access_denied();
		
		$data = $this->get_list_records();
		
		$response = new stdClass();
		$response->data = $data;
		$response->userdata = array();
		$response->page = 1; 
        $response->total = 1; 
        $response->records = count($data);
		
		$this->result_json($response);
	}
	
	public function get_list_excel()
	{
		if (!is_authorized('material/report_product_out', 'index')) 
			access_denied();
		
		$header_captions = array(
			'm_product_code'	=> 'Item ID',
			'm_product_name'	=> 'Item Name'
		);
		$months = $this->config->item('months');
		foreach ($months as $month_key=>$month_name)
		{
			$header_captions['quantity_'.$month_key] = $month_name.' Out/Qty';
			$header_captions['count_'.$month_key.'_oo'] = $month_name.' Out/DO';
			$header_captions['item_type_'.$month_key] = $month_name.' Item Type';
		}
		$header_captions['count_oo'] = 'Total DO';
		$header_captions['item_type'] = 'Item Type';
		
		$result = $this->get_list_records();
		
		$this->load->library('excel');
		$this->excel->write_stream_acos(
			array(
				'ProductOut' => $result
			), 
			'ReportProductOut.xls',
			array(
				'ProductOut' => $header_captions
			)
		);
	}
}