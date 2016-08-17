<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Report_stock extends MY_Controller 
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
		if (!is_authorized('material/report_stock', 'index')) 
			access_denied();
		
		$data = array(
		);
		
		$content = array(
			'title'		=> "Report Stock",
			'content' 	=> $this->load->view('material/report_stock/index', $data, TRUE)
		);
		$this->_load_layout($content);
	}
	
	protected function get_list_query($date_current_start, $date_current_end)
	{
		$this->db
			->select("invl.m_product_id, pro.code m_product_code, pro.name m_product_name, pro.uom m_product_uom, pro.pack m_product_pack")
			->select("invl.c_project_id, prj.name c_project_name")
			->select("pro.m_productgroup_id, prog.name m_productgroup_name")
			->select_if_null("SUM(CASE WHEN invl.created < '".$date_current_start."' THEN invl.quantity ELSE 0 END)", 0, 'quantity_start')
			->select_if_null("SUM(CASE WHEN invl.created >= '".$date_current_start."' AND invl.quantity > 0 THEN invl.quantity ELSE 0 END)", 0, 'quantity_in')
			->select_if_null("SUM(CASE WHEN invl.created >= '".$date_current_start."' AND invl.quantity < 0 THEN invl.quantity ELSE 0 END)", 0, 'quantity_out')
			->select_if_null("SUM(invl.quantity)", 0, 'quantity_end')
			->from('m_inventorylogs invl')
			->join('m_products pro', "pro.id = invl.m_product_id")
			->join('c_projects prj', "prj.id = invl.c_project_id", 'left')
			->join('m_productgroups prog', "prog.id = pro.m_productgroup_id", 'left')
			->where('invl.created <=', $date_current_end)
			->group_by(
				array(
					'invl.m_product_id', 'pro.code', 'pro.name', 'pro.uom', 'pro.pack',
					'invl.c_project_id', 'prj.name',
					'pro.m_productgroup_id', 'prog.name'
				)
			);
	}
	
	public function get_list_json()
	{
		if (!is_authorized('material/report_stock', 'index')) 
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
		
		$this->get_list_query($date_current_start, $date_current_end);
		$this->lib_custom->project_query_filter('invl.c_project_id', $this->c_project_ids);
		
		parent::_get_list_json();
	}
	
	public function get_list_excel()
	{
		if (!is_authorized('material/report_stock', 'index')) 
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
		
		$this->get_list_query($date_current_start, $date_current_end);
		$this->lib_custom->project_query_filter('invl.c_project_id', $this->c_project_ids);
		
		$this->load->library('jqgrid');
		
		$header_captions = array(
			'm_product_code'		=> 'Product Code',
			'm_product_name'		=> 'Product Name',
			'm_product_uom'			=> 'UOM',
			'm_productgroup_name'	=> 'Group',
			'c_project_name'		=> 'Project',
			'quantity_start'		=> 'Start',
			'quantity_in'			=> 'In',
			'quantity_out'			=> 'Out',
			'quantity_end'			=> 'End'
		);
		
		$data = $this->jqgrid->result();
		
		$result = $data->data;
		
		$this->load->library('excel');
		$this->excel->write_stream_acos(
			array(
				'StockSummary' => $result
			), 
			'ReportStockSummary.xls',
			array(
				'StockSummary' => $header_captions
			)
		);
	}
	
	public function detail()
	{
		if (!is_authorized('material/report_stock', 'index')) 
			access_denied();
		
		$m_product_id = $this->input->get_post('m_product_id');
		$c_project_id = $this->input->get_post('c_project_id');
		$date_from = $this->input->get_post('date_from');
		$date_to = $this->input->get_post('date_to');
		$show_by = $this->input->get_post('show_by');
		
		$m_product = NULL;
		$this->db->where('invl.m_product_id', $m_product_id);
		if (empty($c_project_id))
			$this->db
				->where("invl.c_project_id IS NULL", NULL, FALSE);
		else
		{
			if (in_array($c_project_id, $this->c_project_ids))
			{
				$this->db
					->where("invl.c_project_id", $c_project_id);
			}
			else
			{
				$this->db
					->where("invl.c_project_id", 0);
			}
		}
		$this->get_list_query($date_from.' 00:00:00', $date_to.' 23:59:59');
		$table = $this->db->get();
		if ($table->num_rows() > 0)
		{
			$m_product = $table->first_row();
		}
		
		$data = array(
			'm_product_id'	=> $m_product_id,
			'm_product'		=> $m_product,
			'c_project_id'	=> $c_project_id,
			'date_from'		=> $date_from,
			'date_to'		=> $date_to,
			'show_by'		=> $show_by
		);
		
		$this->load->view('material/report_stock/detail', $data);
	}
	
	protected function get_list_detail_query($m_product_id, $c_project_id, $date_current_start, $date_current_end, $show_by)
	{
		$this->db
			->select("invl.created, invl.log_type")
			->select("invl.pallet, invl.barcode")
			->select("grd.code m_grid_code")
			->select("invl.ref1_code, invl.ref2_code")
			->select("CASE WHEN invl.quantity > 0 THEN invl.quantity ELSE 0 END quantity_in", FALSE)
			->select("CASE WHEN invl.quantity < 0 THEN invl.quantity ELSE 0 END quantity_out", FALSE)
			->select("invl.quantity_allocated")
			->select("invl.quantity_picked")
			->select("invl.notes")
			->from('m_inventorylogs invl')
			->join('m_grids grd', "grd.id = invl.m_grid_id", 'left')
			->join('c_projects prj', "prj.id = invl.c_project_id", 'left')
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
		
		if (empty($c_project_id))
			$this->db
				->where("invl.c_project_id IS NULL", NULL, FALSE);
		else
		{
			if (in_array($c_project_id, $this->c_project_ids))
			{
				$this->db
					->where("invl.c_project_id", $c_project_id);
			}
			else
			{
				$this->db
					->where("invl.c_project_id", 0);
			}
		}
	}
	
	public function get_list_detail_json()
	{
		if (!is_authorized('material/report_stock', 'index')) 
			access_denied();
		
		$m_product_id = $this->input->get_post('m_product_id');
		$c_project_id = $this->input->get_post('c_project_id');
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
		
		$this->get_list_detail_query($m_product_id, $c_project_id, $date_current_start, $date_current_end, $show_by);
		
		parent::_get_list_json();
	}
	
	public function get_list_detail_excel()
	{
		if (!is_authorized('material/report_stock', 'index')) 
			access_denied();
		
		$m_product_id = $this->input->get_post('m_product_id');
		$c_project_id = $this->input->get_post('c_project_id');
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
		
		$this->get_list_detail_query($m_product_id, $c_project_id, $date_current_start, $date_current_end, $show_by);
		
		$this->load->library('jqgrid');
		
		$header_captions = array(
			'created'				=> 'Date Time', 
			'log_type'				=> 'Log Type',
			'pallet'				=> 'Pallet',
			'barcode'				=> 'Barcode',
			'm_grid_code'			=> 'Grid',
			'ref1_code'				=> 'Ref 1',
			'ref2_code'				=> 'Ref 2',
			'quantity_in'			=> 'In',
			'quantity_out'			=> 'Out',
			'notes'					=> 'Notes',
			'quantity_allocated'	=> 'Allocated',
			'quantity_picked'		=> 'Picked'
		);
		
		$data = $this->jqgrid->result();
		
		$result = $data->data;
		
		$this->load->library('excel');
		$this->excel->write_stream_acos(
			array(
				'StockDetail' => $result
			), 
			'ReportStockDetail.xls',
			array(
				'StockDetail' => $header_captions
			)
		);
	}
}