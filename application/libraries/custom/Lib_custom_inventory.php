<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
 
class Lib_custom_inventory extends Lib_general
{
	protected $CI;
	
	function __construct()
    {
		$this->CI =& get_instance();
	}
	
	/* -------------------- */
	/* -- PRODUCT REGION -- */
	/* -------------------- */
	public function product_add($data, $created_by = NULL)
	{
		$data->created_by = $created_by;
		
		$cus_m_inventory_product = new Cus_m_inventory_product();
		$this->set_model_fields_values($cus_m_inventory_product, $data);
		if (!$cus_m_inventory_product->save())
			throw new Exception($cus_m_inventory_product->error->string);
		
		return $cus_m_inventory_product->id;
	}
	
	public function product_update($cus_m_inventory_product_id, $data, $updated_by = NULL)
	{
		$data->updated_by = $updated_by;
		
		$cus_m_inventory_product = new Cus_m_inventory_product($cus_m_inventory_product_id);
		$this->set_model_fields_values($cus_m_inventory_product, $data);
		if (!$cus_m_inventory_product->save())
			throw new Exception($cus_m_inventory_product->error->string);
		
		return $cus_m_inventory_product_id;
	}
	
	public function product_remove($cus_m_inventory_product_id, $removed_by = NULL)
	{
		$cus_m_inventory_product = new Cus_m_inventory_product($cus_m_inventory_product_id);
		
		// -- Remove Cycle Count --
		foreach ($cus_m_inventory_product->cus_m_inventory_cyclecount->get() as $cus_m_inventory_cyclecount)
		{
			if (!$cus_m_inventory_cyclecount->delete())
				throw new Exception($cus_m_inventory_cyclecount->error->string);
		}
		
		// -- Remove Product --
		if (!$cus_m_inventory_product->delete())
			throw new Exception($cus_m_inventory_product->error->string);
		
		return $cus_m_inventory_product_id;
	}
	
	/* ----------------------- */
	/* -- CYCLECOUNT REGION -- */
	/* ----------------------- */
	public function cyclecount_add_by_sku($sku, $data, $created_by = NULL)
	{
		$this->CI->db
			->from('cus_m_inventory_products')
			->where('sku', $sku);
		$table = $this->CI->db->get();
		if ($table->num_rows() == 0)
			throw new Exception("SKU Not Registered !");
		$cus_m_inventory_product_record = $table->first_row();
		
		return $this->cyclecount_add($cus_m_inventory_product_record->id, $data, $created_by);
	}
	
	public function cyclecount_add($cus_m_inventory_product_id, $data, $created_by = NULL)
	{
		$cus_m_inventory_product = new Cus_m_inventory_product($cus_m_inventory_product_id);
		
		$data->created_by = $created_by;
		$data->status = 0;
		$cus_m_inventory_cyclecount = new Cus_m_inventory_cyclecount();
		$this->set_model_fields_values($cus_m_inventory_cyclecount, $data);
		$cus_m_inventory_cyclecount_saved = $cus_m_inventory_cyclecount->save(
			array(
				'cus_m_inventory_product'	=> $cus_m_inventory_product
			)
		);
		if (!$cus_m_inventory_cyclecount_saved)
			throw new Exception($cus_m_inventory_cyclecount->error->string);
		
		return $cus_m_inventory_cyclecount->id;
	}
	
	public function cyclecount_update_by_sku_barcode($sku, $barcode, $data, $updated_by = NULL)
	{
		$this->CI->db
			->from('cus_m_inventory_products')
			->where('sku', $sku);
		$table = $this->CI->db->get();
		if ($table->num_rows() == 0)
			throw new Exception("SKU Not Registered !");
		$cus_m_inventory_product_record = $table->first_row();
		
		$this->CI->db
			->from('cus_m_inventory_cyclecounts')
			->where('barcode', $barcode)
			->where('cus_m_inventory_product_id', $cus_m_inventory_product_record->id);
		$table = $this->CI->db->get();
		if ($table->num_rows() == 0)
			throw new Exception("SKU & Barcode Is Not Match !");
		$cus_m_inventory_cyclecount_record = $table->first_row();
		
		return $this->cyclecount_update($cus_m_inventory_cyclecount_record->id, $data, $updated_by);
	}
	
	public function cyclecount_update($cus_m_inventory_cyclecount_id, $data, $updated_by = NULL)
	{
		$data->updated_by = $updated_by;
		$data->status = 1;
		
		$cus_m_inventory_cyclecount = new Cus_m_inventory_cyclecount($cus_m_inventory_cyclecount_id);
		$this->set_model_fields_values($cus_m_inventory_cyclecount, $data);
		$cus_m_inventory_cyclecount_saved = $cus_m_inventory_cyclecount->save();
		if (!$cus_m_inventory_cyclecount_saved)
			throw new Exception($cus_m_inventory_cyclecount->error->string);
		
		return $cus_m_inventory_cyclecount_id;
	}
	
	public function cyclecount_remove($cus_m_inventory_cyclecount_id, $removed_by = NULL)
	{
		$cus_m_inventory_cyclecount = new Cus_m_inventory_cyclecount($cus_m_inventory_cyclecount_id);
		if ($cus_m_inventory_cyclecount->status == 1)
		{
			$cus_m_inventory_cyclecount->status = 0;
			$cus_m_inventory_cyclecount->updated_by = $removed_by;
			if (!$cus_m_inventory_cyclecount->save())
				throw new Exception($cus_m_inventory_cyclecount->error->string);
		}
		else
		{
			if (!$cus_m_inventory_cyclecount->delete())
				throw new Exception($cus_m_inventory_cyclecount->error->string);
		}
		
		return $cus_m_inventory_cyclecount_id;
	}
	
	/* ----------------------- */
	/* -- GRID USAGE REGION -- */
	/* ----------------------- */
	public function grid_usage_add($m_grid_id, $data, $created_by = NULL)
	{
		$m_grid = new M_grid($m_grid_id);
		
		$data->created_by = $created_by;
		$cus_m_grid_usage = new Cus_m_grid_usage();
		$this->set_model_fields_values($cus_m_grid_usage, $data);
		$cus_m_grid_usage_saved = $cus_m_grid_usage->save(
			array(
				'm_grid'	=> $m_grid
			)
		);
		if (!$cus_m_grid_usage_saved)
			throw new Exception($cus_m_grid_usage->error->string);
		
		return $cus_m_grid_usage->id;
	}
	
	public function grid_usage_update_by_grid($m_grid_id, $data, $updated_by = NULL)
	{
		$table = $this->CI->db
			->select('id')
			->from('cus_m_grid_usages')
			->where('m_grid_id', $m_grid_id)
			->get();
		if ($table->num_rows() > 0)
		{
			$cus_m_grid_usage_record = $table->first_row();
			return $this->grid_usage_update($cus_m_grid_usage_record->id, $data, $updated_by);
		}
		else
			return $this->grid_usage_add($m_grid_id, $data, $updated_by);
	}
	
	public function grid_usage_update($cus_m_grid_usage_id, $data, $updated_by = NULL)
	{
		$cus_m_grid_usage_relations = array();
		
		if (property_exists($data, 'm_grid_id'))
		{
			$m_grid = new M_grid($data->m_grid_id);
			$cus_m_grid_usage_relations['m_grid'] = $m_grid;
			unset($data->m_grid_id);
		}
		
		$data->updated_by = $updated_by;
		
		$cus_m_grid_usage = new Cus_m_grid_usage($cus_m_grid_usage_id);
		$this->set_model_fields_values($cus_m_grid_usage, $data);
		$cus_m_grid_usage_saved = $cus_m_grid_usage->save($cus_m_grid_usage_relations);
		if (!$cus_m_grid_usage_saved)
			throw new Exception($cus_m_grid_usage->error->string);
		
		return $cus_m_grid_usage_id;
	}
	
	public function grid_usage_remove_by_grid($m_grid_id, $removed_by = NULL)
	{
		$table = $this->CI->db
			->select('id')
			->from('cus_m_grid_usages')
			->where('m_grid_id', $m_grid_id)
			->get();
		if ($table->num_rows() > 0)
		{
			$cus_m_grid_usage_record = $table->first_row();
			return $this->grid_usage_remove($cus_m_grid_usage_record->id, $removed_by);
		}
		else
			return NULL;
	}
	
	public function grid_usage_remove($cus_m_grid_usage_id, $removed_by = NULL)
	{
		$cus_m_grid_usage = new Cus_m_grid_usage($cus_m_grid_usage_id);
		if (!$cus_m_grid_usage->delete())
			throw new Exception($cus_m_grid_usage->error->string);
		
		return $cus_m_grid_usage_id;
	}
	
	public function grid_usage_add_quantity_by_grid($m_grid_id, $data, $updated_by = NULL)
	{
		$cus_m_grid_usage_record = NULL;
		
		$quantity = 0;
		$quantity_box = 0;
		$quantity_allocated = 0;
		$quantity_box_allocated = 0;
		$quantity_picked = 0;
		$quantity_box_picked = 0;
		
		$table = $this->CI->db
			->select('id')
			->select('quantity, quantity_box')
			->select('quantity_allocated, quantity_box_allocated')
			->select('quantity_picked, quantity_box_picked')
			->from('cus_m_grid_usages')
			->where('m_grid_id', $m_grid_id)
			->get();
		if ($table->num_rows() > 0)
		{
			$cus_m_grid_usage_record = $table->first_row();
			
			$quantity = $cus_m_grid_usage_record->quantity;
			$quantity_box = $cus_m_grid_usage_record->quantity_box;
			$quantity_allocated = $cus_m_grid_usage_record->quantity_allocated;
			$quantity_box_allocated = $cus_m_grid_usage_record->quantity_box_allocated;
			$quantity_picked = $cus_m_grid_usage_record->quantity_picked;
			$quantity_box_picked = $cus_m_grid_usage_record->quantity_box_picked;
		}
			
		if (property_exists($data, 'quantity'))
			$quantity += $data->quantity;
		if (property_exists($data, 'quantity_box'))
			$quantity_box += $data->quantity_box;
		if (property_exists($data, 'quantity_allocated'))
			$quantity_allocated += $data->quantity_allocated;
		if (property_exists($data, 'quantity_box_allocated'))
			$quantity_box_allocated += $data->quantity_box_allocated;
		if (property_exists($data, 'quantity_picked'))
			$quantity_picked += $data->quantity_picked;
		if (property_exists($data, 'quantity_box_picked'))
			$quantity_box_picked += $data->quantity_box_picked;
		
		$data->quantity = $quantity;
		$data->quantity_box = $quantity_box;
		$data->quantity_allocated = $quantity_allocated;
		$data->quantity_box_allocated = $quantity_box_allocated;
		$data->quantity_picked = $quantity_picked;
		$data->quantity_box_picked = $quantity_box_picked;
		$data->quantity_onhand = $quantity + $quantity_allocated + $quantity_picked;
		$data->quantity_box_onhand = $quantity_box + $quantity_box_allocated + $quantity_box_picked;
		
		if ($cus_m_grid_usage_record !== NULL)
			return $this->grid_usage_update($cus_m_grid_usage_record->id, $data, $updated_by);
		else
			return $this->grid_usage_add($m_grid_id, $data, $updated_by);
	}
	
	public function grid_usage_set_request_forecast_by_grid($m_grid_id, $is_forecast_request = TRUE, $updated_by = NULL)
	{
		$m_grid = new M_grid($m_grid_id);
		
		if ($m_grid->code == $this->CI->config->item('inventory_default_grid'))
			$is_forecast_request = FALSE;
		
		$data = new stdClass();
		$data->is_forecast_request = $is_forecast_request;
		
		$this->grid_usage_update_by_grid($m_grid_id, $data, $updated_by);
	}
	
	public function grid_usage_verify_request_forecast($m_inventory_receivedetail_id, $updated_by = NULL)
	{
		$table = $this->CI->db
			->select("ifcd.m_grid_id")
			->from('cus_m_inventory_forecastdetails ifcd')
			->where('ifcd.m_inventory_receivedetail_id', $m_inventory_receivedetail_id)
			->get();
		if ($table->num_rows() == 0)
			return;
		
		$cus_m_inventory_forecastdetail_records = $table->result();
		
		$is_forecast_request = TRUE;
		
		$table = $this->CI->db
			->select('iid.id')
			->from('m_inventory_inbounddetails iid')
			->where('iid.m_inventory_receivedetail_id', $m_inventory_receivedetail_id)
			->get();
		if ($table->num_rows() > 0)
			$is_forecast_request = FALSE;
		
		foreach ($cus_m_inventory_forecastdetail_records as $cus_m_inventory_forecastdetail_record_idx=>$cus_m_inventory_forecastdetail_record)
		{
			if (empty($cus_m_inventory_forecastdetail_record->m_grid_id))
				continue;
			
			$this->grid_usage_set_request_forecast_by_grid($cus_m_inventory_forecastdetail_record->m_grid_id, $is_forecast_request, $updated_by);
		}
	}
	
	public function grid_usage_reload($reload_by = NULL)
	{
		// -- Reset all grids usage to zero --
		$this->CI->db
			->set('quantity', 0)
			->set('quantity_box', 0)
			->set('quantity_allocated', 0)
			->set('quantity_box_allocated', 0)
			->set('quantity_picked', 0)
			->set('quantity_box_picked', 0)
			->set('quantity_onhand', 0)
			->set('quantity_box_onhand', 0)
			->set('is_forecast_request', 0)
			->set('updated_by', $reload_by)
			->set('updated', date('Y-m-d H:i:s'))
			->update('cus_m_grid_usages');
		
		// -- Get latest grid request from forecast --
		$table = $this->CI->db
			->distinct()
			->select("ifcd.m_grid_id")
			->from('cus_m_inventory_forecastdetails ifcd')
			->join('m_inventory_receivedetails ird', "ird.id = ifcd.m_inventory_receivedetail_id")
			->join('m_inventory_inbounddetails iid', "iid.m_inventory_receivedetail_id = ird.id", 'left')
			->where("iid.id IS NULL", NULL, FALSE)
			->where("ifcd.m_grid_id IS NOT NULL", NULL, FALSE)
			->get();
		
		// -- Update latest grid request from forecast --
		$inventory_grid_requests = $table->result();
		foreach ($inventory_grid_requests as $inventory_grid_request_idx=>$inventory_grid_request)
		{
			$table = $this->CI->db
				->select('gu.id')
				->from('cus_m_grid_usages gu')
				->where('gu.m_grid_id', $inventory_grid_request->m_grid_id)
				->get();
			if ($table->num_rows() > 0)
			{
				$cus_m_grid_usage_record = $table->first_row();
				
				$this->CI->db
					->set('is_forecast_request', 1)
					->set('updated_by', $reload_by)
					->set('updated', date('Y-m-d H:i:s'))
					->where('id', $cus_m_grid_usage_record->id)
					->update('cus_m_grid_usages');
			}
			else
			{
				$this->CI->db
					->set('m_grid_id', $inventory_grid_request->m_grid_id)
					->set('is_forecast_request', 1)
					->set('created_by', $reload_by)
					->set('created', date('Y-m-d H:i:s'))
					->insert('cus_m_grid_usages');
			}
		}
		
		// -- Default grid must not as forecast request --
		$table = $this->CI->db
			->select('gri.id')
			->from('m_grids gri')
			->where('gri.code', $this->CI->config->item('inventory_default_grid'))
			->get();
		if ($table->num_rows() > 0)
		{
			$m_grid_default = $table->first_row();
			
			$this->CI->db
				->set('is_forecast_request', 0)
				->set('updated_by', $reload_by)
				->set('updated', date('Y-m-d H:i:s'))
				->where('m_grid_id', $m_grid_default->id)
				->update('cus_m_grid_usages');
		}
		
		// -- Get latest grid usage from inventory --
		$table = $this->CI->db
			->select('gri.id')
			->select_if_null("SUM(inv.quantity)", 0, 'quantity')
			->select_if_null("SUM(inv.quantity_allocated)", 0, 'quantity_allocated')
			->select_if_null("SUM(inv.quantity_picked)", 0, 'quantity_picked')
			->select_if_null("SUM(inv.quantity_onhand)", 0, 'quantity_onhand')
			->select_if_null("SUM(inv.quantity_box)", 0, 'quantity_box')
			->select_if_null("SUM(inv.quantity_box_allocated)", 0, 'quantity_box_allocated')
			->select_if_null("SUM(inv.quantity_box_picked)", 0, 'quantity_box_picked')
			->select_if_null("SUM(inv.quantity_box_onhand)", 0, 'quantity_box_onhand')
			->from('m_grids gri')
			->join('m_inventories inv', "inv.m_grid_id = gri.id AND inv.quantity_box_onhand > 0 AND inv.quantity_onhand > 0", 'left')
			->group_by(
				array(
					'gri.id'
				)
			)
			->get();
		
		// -- Update latest grid usage from inventory --
		$inventory_grid_usages = $table->result();
		foreach ($inventory_grid_usages as $inventory_grid_usage_idx=>$inventory_grid_usage)
		{
			$table = $this->CI->db
				->select('gu.id')
				->from('cus_m_grid_usages gu')
				->where('gu.m_grid_id', $inventory_grid_usage->id)
				->get();
			if ($table->num_rows() > 0)
			{
				$cus_m_grid_usage_record = $table->first_row();
				
				$this->CI->db
					->set('quantity', $inventory_grid_usage->quantity)
					->set('quantity_box', $inventory_grid_usage->quantity_box)
					->set('quantity_allocated', $inventory_grid_usage->quantity_allocated)
					->set('quantity_box_allocated', $inventory_grid_usage->quantity_box_allocated)
					->set('quantity_picked', $inventory_grid_usage->quantity_picked)
					->set('quantity_box_picked', $inventory_grid_usage->quantity_box_picked)
					->set('quantity_onhand', $inventory_grid_usage->quantity + $inventory_grid_usage->quantity_allocated + $inventory_grid_usage->quantity_picked)
					->set('quantity_box_onhand', $inventory_grid_usage->quantity_box + $inventory_grid_usage->quantity_box_allocated + $inventory_grid_usage->quantity_box_picked)
					->set('updated_by', $reload_by)
					->set('updated', date('Y-m-d H:i:s'))
					->where('id', $cus_m_grid_usage_record->id)
					->update('cus_m_grid_usages');
			}
			else
			{
				$this->CI->db
					->set('m_grid_id', $inventory_grid_usage->id)
					->set('quantity', $inventory_grid_usage->quantity)
					->set('quantity_box', $inventory_grid_usage->quantity_box)
					->set('quantity_allocated', $inventory_grid_usage->quantity_allocated)
					->set('quantity_box_allocated', $inventory_grid_usage->quantity_box_allocated)
					->set('quantity_picked', $inventory_grid_usage->quantity_picked)
					->set('quantity_box_picked', $inventory_grid_usage->quantity_box_picked)
					->set('quantity_onhand', $inventory_grid_usage->quantity + $inventory_grid_usage->quantity_allocated + $inventory_grid_usage->quantity_picked)
					->set('quantity_box_onhand', $inventory_grid_usage->quantity_box + $inventory_grid_usage->quantity_box_allocated + $inventory_grid_usage->quantity_box_picked)
					->set('created_by', $reload_by)
					->set('created', date('Y-m-d H:i:s'))
					->insert('cus_m_grid_usages');
			}
		}
	}
	
	/* ------------------------------------------ */
	/* -- INVENTORY RECEIVE DETAIL GRID REGION -- */
	/* ------------------------------------------ */
	
	public function forecast_create_by_inventory_receive($m_inventory_receive_id, $created_by = NULL)
	{
		// -- Get or create inventory forecast --
		$cus_m_inventory_forecast_id = 0;
		$table = $this->CI->db
			->select("ifcd.cus_m_inventory_forecast_id")
			->from('cus_m_inventory_forecastdetails ifcd')
			->join('m_inventory_receivedetails ird', "ird.id = ifcd.m_inventory_receivedetail_id")
			->where('ird.m_inventory_receive_id', $m_inventory_receive_id)
			->limit(1)
			->get();
		if ($table->num_rows() > 0)
		{
			$table_record = $table->first_row();
			$cus_m_inventory_forecast_id = $table_record->cus_m_inventory_forecast_id;
			
			$forecast_data = new stdClass();
			$this->forecast_update($cus_m_inventory_forecast_id, $forecast_data, $created_by);
		}
		else
		{
			$forecast_data = new stdClass();
			$cus_m_inventory_forecast_id = $this->forecast_add($forecast_data, $created_by);
		}
		
		// -- Generate inventory forecast detail --
		$table = $this->CI->db
			->select("ird.id")
			->from('m_inventory_receivedetails ird')
			->where('ird.m_inventory_receive_id', $m_inventory_receive_id)
			->get();
		$m_inventory_receivedetails = $table->result();
		foreach ($m_inventory_receivedetails as $m_inventory_receivedetail)
		{
			$this->forecastdetail_generate($cus_m_inventory_forecast_id, $m_inventory_receivedetail->id, $created_by);
		}
		
		return $cus_m_inventory_forecast_id;
	}
	
	public function forecast_add($data, $created_by = NULL)
	{
		if (empty($data->forecast_date))
		{
			$data->forecast_date = date('Y-m-d');
		}
		if (empty($data->code))
		{
			$year = date('Y', strtotime($data->forecast_date));
			$month = date('m', strtotime($data->forecast_date));
			
			$forecast_num = 0;
			$table = $this->CI->db
				->select_if_null("MAX(ifc.forecast_num)", 0, 'forecast_num')
				->from('cus_m_inventory_forecasts ifc')
				->where("YEAR(ifc.forecast_date) = ". (int)$year, NULL, FALSE)
				->where("MONTH(ifc.forecast_date) = ". (int)$month, NULL, FALSE)
				->get();
			if ($table->num_rows() > 0)
			{
				$table_record = $table->first_row();
				$forecast_num = $table_record->forecast_num;
			}
			$forecast_num += 1;
			
			$data->forecast_num = $forecast_num;
			$data->code = $data->forecast_num . '/RCV/' . $month . '/' .$year;
		}
		$data->created_by = $created_by;
		
		$cus_m_inventory_forecast = new Cus_m_inventory_forecast();
		$this->set_model_fields_values($cus_m_inventory_forecast, $data);
		$cus_m_inventory_forecast_saved = $cus_m_inventory_forecast->save();
		if (!$cus_m_inventory_forecast_saved)
			throw new Exception($cus_m_inventory_forecast->error->string);
		
		return $cus_m_inventory_forecast->id;
	}
	
	public function forecast_update($cus_m_inventory_forecast_id, $data, $updated_by = NULL)
	{
		$data->updated_by = $updated_by;
		
		$cus_m_inventory_forecast = new Cus_m_inventory_forecast($cus_m_inventory_forecast_id);
		$this->set_model_fields_values($cus_m_inventory_forecast, $data);
		$cus_m_inventory_forecast_saved = $cus_m_inventory_forecast->save();
		if (!$cus_m_inventory_forecast_saved)
			throw new Exception($cus_m_inventory_forecast->error->string);
		
		return $cus_m_inventory_forecast->id;
	}
	
	public function forecast_remove($cus_m_inventory_forecast_id, $removed_by = NULL)
	{
		$cus_m_inventory_forecast = new Cus_m_inventory_forecast($cus_m_inventory_forecast_id);
		
		// -- Remove Forecast Details --
		foreach ($cus_m_inventory_forecast->cus_m_inventory_forecastdetail->get() as $cus_m_inventory_forecastdetail)
		{
			$this->forecastdetail_remove($cus_m_inventory_forecastdetail->id, $removed_by);
		}
		
		// -- Remove Forecast --
		if (!$cus_m_inventory_forecast->delete())
			throw new Exception($cus_m_inventory_forecast->error->string);
		
		return $cus_m_inventory_forecast_id;
	}
	
	public function forecastdetail_generate($cus_m_inventory_forecast_id, $m_inventory_receivedetail_id, $generated_by = NULL)
	{
		$this->forecastdetail_ungenerate($m_inventory_receivedetail_id, $generated_by);
		
		$m_inventory_receivedetail = new M_inventory_receivedetail($m_inventory_receivedetail_id);
		$c_orderindetail = $m_inventory_receivedetail->c_orderindetail->get();
		$m_product = $c_orderindetail->m_product->get();
		
		$pack = $m_product->pack;
		$receive_quantity = $m_inventory_receivedetail->quantity_box;
		$pallet_count = ceil($receive_quantity / ($pack > 0 ? $pack : $receive_quantity));
		
		for ($pallet = 1; $pallet <= $pallet_count; $pallet++)
		{
			$quantity_pack = $pack;
			if ($pallet == $pallet_count)
			{
				$quantity_pack = $receive_quantity - ($pack * ($pallet_count - 1));
			}
			
			$forecastdetail_data = new stdClass();
			$forecastdetail_data->cus_m_inventory_forecast_id = $cus_m_inventory_forecast_id;
			$forecastdetail_data->m_inventory_receivedetail_id = $m_inventory_receivedetail->id;
			$forecastdetail_data->m_grid_id = $this->forecastdetail_get_default_grid($m_product->id);
			$forecastdetail_data->quantity = $quantity_pack;
			$this->forecastdetail_add($forecastdetail_data, $generated_by);
		}
	}
	
	protected function forecastdetail_get_default_grid($m_product_id)
	{
		$m_grid_id = NULL;
		
		$m_product = new M_product($m_product_id);
		if (!$m_product->exists())
			throw new Exception("Product with id ".$m_product_id." is not found.");
		$m_productgroup = $m_product->m_productgroup->get();
		
		$this->CI->db
			->select('grd.id, grd.col, grd.row, grd.level')
			->select('wh.id warehouse_id, wh.code warehouse_code')
			->select('grd_use.is_forecast_request')
			->select('grd_use.quantity, grd_use.quantity_onhand')
			->select('grd_use.quantity_box, grd_use.quantity_box_onhand')
			->from('m_grids grd')
			->join('m_warehouses wh', 'wh.id = grd.m_warehouse_id')
			->join('cus_m_grid_usages grd_use', 'grd_use.m_grid_id = grd.id', 'left');
		if ($m_productgroup->exists())
			$this->CI->db
				->where('grd.m_productgroup_id', $m_productgroup->id);
		else
			$this->CI->db
				->where("grd.m_productgroup_id IS NULL", NULL, FALSE);
		$table = $this->CI->db
			->where('grd.code <>', $this->CI->config->item('inventory_default_grid'))
			->order_by('is_forecast_request', 'asc')
			->order_by('quantity_box', 'asc')
			->order_by('quantity_box_onhand', 'asc')
			->order_by('quantity', 'asc')
			->order_by('quantity_onhand', 'asc')
			->order_by('warehouse_code', 'asc')
			->order_by('level', 'asc')
			->order_by('row', 'asc')
			->order_by('col', 'asc')
			->limit(1)
			->get();
		if ($table->num_rows() > 0)
		{
			$m_grid_record = $table->first_row();
			$m_grid_id = $m_grid_record->id;
		}
		
		return $m_grid_id;
	}
	
	public function forecastdetail_ungenerate($m_inventory_receivedetail_id, $ungenerated_by = NULL)
	{
		$m_inventory_receivedetail = new M_inventory_receivedetail($m_inventory_receivedetail_id);
		
		// -- Remove Inventory Receive Detail Grid --
		foreach ($m_inventory_receivedetail->cus_m_inventory_forecastdetail->get() as $cus_m_inventory_forecastdetail)
		{
			$this->forecastdetail_remove($cus_m_inventory_forecastdetail->id, $ungenerated_by);
		}
	}
	
	public function forecastdetail_add($data, $created_by = NULL)
	{
		$m_grid = new M_grid();
		
		$forecastdetail_relations = array();
		if (property_exists($data, 'cus_m_inventory_forecast_id'))
		{
			$cus_m_inventory_forecast = new Cus_m_inventory_forecast($data->cus_m_inventory_forecast_id);
			$forecastdetail_relations['cus_m_inventory_forecast'] = $cus_m_inventory_forecast;
			unset($data->cus_m_inventory_forecast_id);
		}
		if (property_exists($data, 'm_inventory_receivedetail_id'))
		{
			$m_inventory_receivedetail = new M_inventory_receivedetail($data->m_inventory_receivedetail_id);
			$forecastdetail_relations['m_inventory_receivedetail'] = $m_inventory_receivedetail;
			unset($data->m_inventory_receivedetail_id);
		}
		if (property_exists($data, 'm_grid_id'))
		{
			$m_grid = new M_grid($data->m_grid_id);
			$forecastdetail_relations['m_grid'] = $m_grid;
			unset($data->m_grid_id);
		}
		
		$data->created_by = $created_by;
		
		$cus_m_inventory_forecastdetail = new Cus_m_inventory_forecastdetail();
		$this->set_model_fields_values($cus_m_inventory_forecastdetail, $data);
		$cus_m_inventory_forecastdetail_saved = $cus_m_inventory_forecastdetail->save($forecastdetail_relations);
		if (!$cus_m_inventory_forecastdetail_saved)
			throw new Exception($cus_m_inventory_forecastdetail->error->string);
		
		// -- Verify Grid Usage Forecast Request --
		if ($m_grid->exists())
			$this->grid_usage_set_request_forecast_by_grid($m_grid->id, TRUE, $created_by);
		
		return $cus_m_inventory_forecastdetail->id;
	}
	
	public function forecastdetail_remove($cus_m_inventory_forecastdetail_id, $removed_by = NULL)
	{
		$cus_m_inventory_forecastdetail = new Cus_m_inventory_forecastdetail($cus_m_inventory_forecastdetail_id);
		
		// -- Verify Grid Usage Forecast Request --
		$m_grid = $cus_m_inventory_forecastdetail->m_grid->get();
		if ($m_grid->exists())
			$this->grid_usage_set_request_forecast_by_grid($m_grid->id, FALSE, $removed_by);
		
		// -- Remove Forecast Detail Grid --
		if (!$cus_m_inventory_forecastdetail->delete())
			throw new Exception($cus_m_inventory_forecastdetail->error->string);
		
		return $cus_m_inventory_forecastdetail_id;
	}
}