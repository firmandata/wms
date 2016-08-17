<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Lib_inventory_operation extends Lib_general
{
	protected $CI;
	
	function __construct()
    {
		$this->CI =& get_instance();
		
		$this->CI->load->library('material/lib_inventory');
		$this->CI->load->library('custom/lib_custom');
	}
	
	/* -------------------- */
	/* -- PUTAWAY REGION -- */
	/* -------------------- */
	
	public function putaway_add($data, $created_by = NULL)
	{
		$data->created_by = $created_by;
		
		$m_inventory_putaway = new M_inventory_putaway();
		$this->set_model_fields_values($m_inventory_putaway, $data);
		$m_inventory_putaway_saved = $m_inventory_putaway->save();
		if (!$m_inventory_putaway_saved)
			throw new Exception($m_inventory_putaway->error->string);
		
		return $m_inventory_putaway->id;
	}
	
	public function putaway_update($m_inventory_putaway_id, $data, $updated_by = NULL)
	{
		$data->updated_by = $updated_by;
		
		$m_inventory_putaway = new M_inventory_putaway($m_inventory_putaway_id);
		$this->set_model_fields_values($m_inventory_putaway, $data);
		$m_inventory_putaway_saved = $m_inventory_putaway->save();
		if (!$m_inventory_putaway_saved)
			throw new Exception($m_inventory_putaway->error->string);
		
		return $m_inventory_putaway_id;
	}
	
	public function putaway_remove($m_inventory_putaway_id, $removed_by = NULL)
	{
		$m_inventory_putaway = new M_inventory_putaway($m_inventory_putaway_id);
		
		// -- Remove Inventory Putaway Detail --
		foreach ($m_inventory_putaway->m_inventory_putawaydetail->get() as $m_inventory_putawaydetail)
		{
			$this->putawaydetail_remove($m_inventory_putawaydetail->id, $removed_by);
		}
		
		// -- Remove Inventory Putaway --
		if (!$m_inventory_putaway->delete())
			throw new Exception($m_inventory_putaway->error->string);
		
		return $m_inventory_putaway_id;
	}
	
	public function putawaydetail_add_by_properties($data, $created_by = NULL)
	{
		$c_project_ids = $this->CI->lib_custom->project_get_ids($created_by);
		
		$quantity_box_new = NULL;
		
		if (property_exists($data, 'quantity_box'))
		{
			$quantity_box_new = $data->quantity_box;
		}
		
		$m_grid_default = new M_grid();
		$m_grid_default
			->where('code', $this->CI->config->item('inventory_default_grid'))
			->get();
		
		if ($quantity_box_new !== NULL)
		{
			// -- Get exist quantity --
			$exist_quantity_box = 0;
			$this->CI->db
				->select_if_null("SUM(inv.quantity_box)", 0, 'quantity_box')
				->from('m_inventories inv')
				->where('inv.m_grid_id', $m_grid_default->id);
			$this->putawaydetail_criteria_query($data, 'inv', 'm_inventories');
			$this->CI->db
				->where("inv.quantity_box > 0", NULL, FALSE);
			$this->CI->lib_custom->project_query_filter('inv.c_project_id', $c_project_ids);
			$table = $this->CI->db->get();
			if ($table->num_rows() > 0)
			{
				$m_inventory_record = $table->first_row();
				$exist_quantity_box = $m_inventory_record->quantity_box;
			}
			
			if ($quantity_box_new !== NULL && $quantity_box_new > $exist_quantity_box)
			{
				throw new Exception("New quantity box ".$quantity_box_new." more than exist quantity box ".$exist_quantity_box);
			}
		}
		
		// -- Get exist inventory records --
		$this->CI->db
			->select("inv.id, inv.quantity_box")
			->from('m_inventories inv');
		$this->putawaydetail_criteria_query($data, 'inv', 'm_inventories');
		$this->CI->db
			->where('inv.m_grid_id', $m_grid_default->id)
			->where("inv.quantity_box > 0", NULL, FALSE)
			->order_by('inv.quantity_box', 'asc')
			->order_by('inv.updated', 'desc');
		$this->CI->lib_custom->project_query_filter('inv.c_project_id', $c_project_ids);
		$table = $this->CI->db->get();
		if ($table->num_rows() == 0)
			throw new Exception("Critera not found in inventory existing.");
		$m_inventory_records = $table->result();
		
		// -- Putaway all quantity --
		if ($quantity_box_new === NULL)
		{
			foreach ($m_inventory_records as $m_inventory_record)
			{
				// -- Inventory putaway detail add --
				$data->m_inventory_id = $m_inventory_record->id;
				$data->quantity_box_to = $quantity_box_new;
				$this->putawaydetail_add(clone $data, $created_by);
			}
		}
		// -- Putaway partial quantity --
		else
		{
			$quantity_box_putawayed = 0;
			foreach ($m_inventory_records as $m_inventory_record)
			{
				$quantity_box = $quantity_box_new - $quantity_box_putawayed;
				if ($quantity_box > $m_inventory_record->quantity_box)
					$quantity_box = $m_inventory_record->quantity_box;
				
				if ($quantity_box > 0)
				{
					// -- Inventory putaway detail add --
					$data->m_inventory_id = $m_inventory_record->id;
					$data->quantity_box_to = $quantity_box;
					$this->putawaydetail_add(clone $data, $created_by);
				}
				
				$quantity_box_putawayed += $quantity_box;
				if ($quantity_box_putawayed >= $quantity_box_new)
					break;
			}
		}
		
		// -- Verification Pallet on Grid --
		foreach ($m_inventory_records as $m_inventory_record)
		{
			$table = $this->CI->db
				->select("id, pallet, m_grid_id")
				->from('m_inventories inv')
				->where('inv.id', $m_inventory_record->id)
				->get();
			if ($table->num_rows() > 0)
			{
				$m_inventory_record = $table->first_row();
				$this->CI->lib_inventory->verify_pallet_grid($m_inventory_record->pallet, $m_inventory_record->m_grid_id);
			}
		}
	}
	
	public function putawaydetail_remove_by_properties($data, $removed_by = NULL)
	{
		$c_project_ids = $this->CI->lib_custom->project_get_ids($removed_by);
		
		// -- Get Putaway Detail Exists --
		$this->CI->db
			->select("ipd.id, ipd.pallet, ipd.m_gridfrom_id")
			->from('m_inventory_putawaydetails ipd')
			->join('m_inventories inv', "inv.id = ipd.m_inventory_id");
		$this->putawaydetail_criteria_query($data, 'ipd', 'm_inventory_putawaydetails');
		$this->CI->lib_custom->project_query_filter('inv.c_project_id', $c_project_ids);
		$table = $this->CI->db
			->order_by('ipd.id', 'desc')
			->get();
		if ($table->num_rows() == 0)
		{
			throw new Exception("Critera not found in putaway existing.");
		}
		
		// -- Remove Putaway Detail Exists --
		$table_records = $table->result();
		foreach ($table_records as $table_record)
		{
			$this->putawaydetail_remove($table_record->id, $removed_by);
		}
		
		// -- Verification Pallet on Grid --
		foreach ($table_records as $table_record)
		{
			$this->CI->lib_inventory->verify_pallet_grid($table_record->pallet, $table_record->m_gridfrom_id);
		}
	}
	
	protected function putawaydetail_add($data, $created_by = NULL)
	{
		$m_inventory_putaway_ids = array();
		
		$m_inventory_putaway = new M_inventory_putaway();
		$m_inventory = new M_inventory();
		$m_gridto = new M_grid();
		$quantity_box_to = NULL;
		
		$m_inventory_putawaydetail_relations = array();
		if (property_exists($data, 'm_inventory_putaway_id'))
		{
			$m_inventory_putaway = new M_inventory_putaway($data->m_inventory_putaway_id);
			$m_inventory_putawaydetail_relations['m_inventory_putaway'] = $m_inventory_putaway;
			unset($data->m_inventory_putaway_id);
		}
		if (property_exists($data, 'm_inventory_id'))
		{
			$m_inventory = new M_inventory($data->m_inventory_id);
			$m_inventory_putawaydetail_relations['m_inventory'] = $m_inventory;
			unset($data->m_inventory_id);
			
			$c_project = $m_inventory->c_project->get();
			$project_is_valid = $this->CI->lib_custom->project_is_valid($created_by, $c_project->id);
			if (!$project_is_valid)
				throw new Exception("Access denied for the project.");
		}
		if (property_exists($data, 'm_gridto_id'))
		{
			$m_gridto = new M_grid($data->m_gridto_id);
			$m_inventory_putawaydetail_relations['m_gridto'] = $m_gridto;
			unset($data->m_gridto_id);
		}
		if (property_exists($data, 'quantity_box_to'))
		{
			$quantity_box_to = $data->quantity_box_to;
		}
		
		// -- Putaway inventory with new grid and quantity --
		$data_m_inventory = new stdClass();
		
		$data_m_inventory_log = new stdClass();
		$data_m_inventory_log->log_type = "PUTAWAY";
		$data_m_inventory_log->ref1_code = $m_inventory_putaway->code;
		$data_m_inventory_log->notes = 'Add Putaway';
		$putaway_results = $this->CI->lib_inventory->move($m_inventory->id, $m_gridto->id, $data_m_inventory, $quantity_box_to, $created_by, $data_m_inventory_log);
		foreach ($putaway_results as $putaway_result)
		{
			if ($putaway_result['type'] != 'move')
				continue;
			
			// -- Inventory putaway detail add --
			$m_inventory = new M_inventory($putaway_result['m_inventory_id']);
			$m_gridfrom = new M_grid($putaway_result['m_gridfrom_id']);
			
			$m_inventory_putawaydetail_relations['m_inventory_putaway'] = $m_inventory_putaway;
			$m_inventory_putawaydetail_relations['m_inventory'] = $m_inventory;
			$m_inventory_putawaydetail_relations['m_gridfrom'] = $m_gridfrom;
			$m_inventory_putawaydetail_relations['m_gridto'] = $m_gridto;
			$m_inventory_putawaydetail_relations['m_product'] = $m_inventory->m_product->get();
			
			$data->barcode = $m_inventory->barcode;
			$data->pallet = $m_inventory->pallet;
			$data->carton_no = $m_inventory->carton_no;
			$data->lot_no = $m_inventory->lot_no;
			$data->quantity_from = $putaway_result['quantity_from'];
			$data->quantity_to = $putaway_result['quantity_to'];
			$data->quantity_box_from = $putaway_result['quantity_box_from'];
			$data->quantity_box_to = $putaway_result['quantity_box_to'];
			$data->created_by = $created_by;
			
			$m_inventory_putawaydetail = new M_inventory_putawaydetail();
			$this->set_model_fields_values($m_inventory_putawaydetail, $data);
			$m_inventory_putaway_saved = $m_inventory_putawaydetail->save($m_inventory_putawaydetail_relations);
			if (!$m_inventory_putaway_saved)
				throw new Exception($m_inventory_putawaydetail->error->string);
			
			$m_inventory_putaway_ids[] = $m_inventory_putawaydetail->id;
		}
		
		return $m_inventory_putaway_ids;
	}
	
	protected function putawaydetail_remove($m_inventory_putawaydetail_id, $removed_by = NULL)
	{
		$m_inventory_putawaydetail = new M_inventory_putawaydetail($m_inventory_putawaydetail_id);
		$m_inventory_putaway = $m_inventory_putawaydetail->m_inventory_putaway->get();
		
		// -- ReMove Inventory to old grid --
		$m_inventory = $m_inventory_putawaydetail->m_inventory->get();
		$m_gridfrom = $m_inventory_putawaydetail->m_gridfrom->get();
		
		$c_project = $m_inventory->c_project->get();
		$project_is_valid = $this->CI->lib_custom->project_is_valid($removed_by, $c_project->id);
		if (!$project_is_valid)
			throw new Exception("Access denied for the project.");
		
		// -- Must remove in last operation first validation --
		$this->verify_last_of_operation($m_inventory->id, 'm_inventory_putawaydetails', $m_inventory_putawaydetail_id);
		
		// -- Update inventory with old grid and quantity --
		$data_m_inventory = new stdClass();
		
		$data_m_inventory_log = new stdClass();
		$data_m_inventory_log->log_type = "PUTAWAY";
		$data_m_inventory_log->ref1_code = $m_inventory_putaway->code;
		$data_m_inventory_log->notes = 'Remove Putaway';
		$putaway_results = $this->CI->lib_inventory->move($m_inventory->id, $m_gridfrom->id, $data_m_inventory, $m_inventory_putawaydetail->quantity_box_to, $removed_by, $data_m_inventory_log);
		
		// -- Remove Putaway Detail --
		if (!$m_inventory_putawaydetail->delete())
			throw new Exception($m_inventory_putawaydetail->error->string);
		
		return $m_inventory_putawaydetail_id;
	}
	
	private function putawaydetail_criteria_query($data, $table_alias, $table_name = 'm_inventory_putawaydetails')
	{
		$fields = array(
			'm_product_id', 'pallet', 'barcode', 'carton_no', 'lot_no'
		);
		if ($table_name == 'm_inventory_putawaydetails')
		{
			$fields[] = 'm_inventory_putaway_id';
			$fields[] = 'm_gridto_id';
		}
		if ($table_name == 'm_inventories')
		{
			$fields[] = 'm_grid_id';
			$fields[] = 'c_project_id';
			$fields[] = 'packed_date';
			$fields[] = 'received_date';
			$fields[] = 'expired_date';
			$fields[] = 'condition';
		}
		
		foreach ($data as $field=>$value)
		{
			if (!in_array($field, $fields))
				continue;
			
			if ($value !== NULL)
			{
				if ($value !== '')
				{
					$this->CI->db
						->where($table_alias.'.'.$field, $value);
				}
			}
			else
				$this->CI->db
					->where($table_alias.'.'.$field." IS NULL", NULL, FALSE);
		}
	}
	
	public function putawaydetail_get_default_grid_by_product($m_product_id)
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
	
	/* ----------------------- */
	/* -- ADJUSTMENT REGION -- */
	/* ----------------------- */
	
	public function adjust_add($data, $created_by = NULL)
	{
		$data->created_by = $created_by;
		
		$m_inventory_adjust = new M_inventory_adjust();
		$this->set_model_fields_values($m_inventory_adjust, $data);
		$m_inventory_adjust_saved = $m_inventory_adjust->save();
		if (!$m_inventory_adjust_saved)
			throw new Exception($m_inventory_adjust->error->string);
		
		return $m_inventory_adjust->id;
	}
	
	public function adjust_update($m_inventory_adjust_id, $data, $updated_by = NULL)
	{
		$data->updated_by = $updated_by;
		
		$m_inventory_adjust = new M_inventory_adjust($m_inventory_adjust_id);
		$this->set_model_fields_values($m_inventory_adjust, $data);
		$m_inventory_adjust_saved = $m_inventory_adjust->save();
		if (!$m_inventory_adjust_saved)
			throw new Exception($m_inventory_adjust->error->string);
		
		return $m_inventory_adjust_id;
	}
	
	public function adjust_remove($m_inventory_adjust_id, $removed_by = NULL)
	{
		$m_inventory_adjust = new M_inventory_adjust($m_inventory_adjust_id);
		
		// -- Remove Inventory Adjust Detail --
		foreach ($m_inventory_adjust->m_inventory_adjustdetail->get() as $m_inventory_adjustdetail)
		{
			$this->adjustdetail_remove($m_inventory_adjustdetail->id, $removed_by);
		}
		
		// -- Remove Inventory Adjust --
		if (!$m_inventory_adjust->delete())
			throw new Exception($m_inventory_adjust->error->string);
		
		return $m_inventory_adjust_id;
	}
	
	public function adjustdetail_add_by_properties($data, $created_by = NULL)
	{
		$c_project_ids = $this->CI->lib_custom->project_get_ids($created_by);
		
		$quantity_new = NULL;
		if (property_exists($data, 'quantity'))
		{
			$quantity_new = $data->quantity;
		}
		
		// -- Get exist quantity --
		$exist_quantity = 0;
		$this->CI->db
			->select_if_null("SUM(inv.quantity)", 0, 'quantity')
			->from('m_inventories inv');
		$this->adjustdetail_criteria_query($data, 'inv', 'm_inventories');
		$this->CI->db
			->where("inv.quantity > 0", NULL, FALSE);
		$this->CI->lib_custom->project_query_filter('inv.c_project_id', $c_project_ids);
		$table = $this->CI->db->get();
		if ($table->num_rows() > 0)
		{
			$m_inventory_record = $table->first_row();
			$exist_quantity = $m_inventory_record->quantity;
		}
		
		if ($quantity_new === NULL)
			$quantity_new = $exist_quantity;
		
		// -- Increase or Decrease ? --
		$is_increase_quantity = TRUE;
		if ($exist_quantity > $quantity_new)
			$is_increase_quantity = FALSE;
		
		// -- Decrease enought quantity ? --
		if ($is_increase_quantity == FALSE)
		{
			if ($exist_quantity - $quantity_new < 0)
			{
				throw new Exception("Inventory quantity of criteria is ".$exist_quantity." so it's not enought for decrease.");
			}
		}
		
		// -- Get inventory records --
		$this->CI->db
			->select("inv.id, inv.quantity")
			->from('m_inventories inv');
		$this->adjustdetail_criteria_query($data, 'inv', 'm_inventories');
		if ($is_increase_quantity == TRUE)
		{
			$this->CI->db
				->order_by('inv.quantity', 'desc');
		}
		else
		{
			$this->CI->db
				->where("inv.quantity >", 0)
				->order_by('inv.quantity', 'asc');
		}
		$this->CI->db
			->order_by('inv.id', 'desc');
		$this->CI->lib_custom->project_query_filter('inv.c_project_id', $c_project_ids);
		$table = $this->CI->db->get();
		if ($table->num_rows() == 0)
			throw new Exception("Inventory criteria is not match.");
		
		// -- Increase inventory quantity (only one inventory record) --
		if ($is_increase_quantity == TRUE)
		{
			$m_inventory_record = $table->first_row();
			
			// -- Inventory adjust detail add --
			$data->m_inventory_id = $m_inventory_record->id;
			$data->quantity_add = $quantity_new - $exist_quantity;
			$this->adjustdetail_add(clone $data, $created_by);
		}
		
		// -- Decrease inventory quantity (more then one inventory's records) --
		if ($is_increase_quantity == FALSE)
		{
			$quantity_adjust = $exist_quantity - $quantity_new;
			$m_inventory_records = $table->result();
			foreach ($m_inventory_records as $m_inventory_record)
			{
				$quantity = $m_inventory_record->quantity - $quantity_adjust;
				if ($quantity < 0)
					$quantity = $m_inventory_record->quantity;
				else
					$quantity = $quantity_adjust;
				
				// -- Inventory adjust detail add --
				$data->m_inventory_id = $m_inventory_record->id;
				$data->quantity_add = $quantity * -1;
				$this->adjustdetail_add(clone $data, $created_by);
				
				$quantity_adjust -= $quantity;
				if ($quantity_adjust == 0)
					break;
				elseif ($quantity_adjust < 0)
					throw new Exception("Inventory quantity of ".$m_inventory_record->id." is ".$m_inventory_record->quantity." so it's not enought for decrease.");
			}
		}
	}
	
	public function adjustdetail_remove_by_properties($data, $removed_by = NULL)
	{
		$c_project_ids = $this->CI->lib_custom->project_get_ids($removed_by);
		
		// -- Get Adjust Detail Exists --
		$this->CI->db
			->select("iad.id")
			->from('m_inventory_adjustdetails iad')
			->join('m_inventories inv', "inv.id = iad.m_inventory_id");
		$this->adjustdetail_criteria_query($data, 'iad', 'm_inventory_adjustdetails');
		$this->CI->lib_custom->project_query_filter('inv.c_project_id', $c_project_ids);
		$table = $this->CI->db
			->order_by('iad.id', 'desc')
			->get();
		if ($table->num_rows() == 0)
		{
			throw new Exception("Critera not found in adjust existing.");
		}
		
		// -- Remove Adjust Detail Exists --
		$table_records = $table->result();
		foreach ($table_records as $table_record)
		{
			$this->adjustdetail_remove($table_record->id, $removed_by);
		}
	}
	
	protected function adjustdetail_add($data, $created_by = NULL)
	{
		$m_inventory_adjust = new M_inventory_adjust();
		$m_inventory = new M_inventory();
		$m_grid = new M_grid();
		$m_product = new M_product();
		$quantity_add = 0;
		
		$m_inventory_adjustdetail_relations = array();
		if (property_exists($data, 'm_inventory_adjust_id'))
		{
			$m_inventory_adjust = new M_inventory_adjust($data->m_inventory_adjust_id);
			$m_inventory_adjustdetail_relations['m_inventory_adjust'] = $m_inventory_adjust;
			unset($data->m_inventory_adjust_id);
		}
		if (property_exists($data, 'm_inventory_id'))
		{
			$m_inventory = new M_inventory($data->m_inventory_id);
			$m_inventory_adjustdetail_relations['m_inventory'] = $m_inventory;
			$m_inventory_adjustdetail_relations['m_grid'] = $m_inventory->m_grid->get();
			$m_inventory_adjustdetail_relations['m_product'] = $m_inventory->m_product->get();
			unset($data->m_inventory_id);
			
			$c_project = $m_inventory->c_project->get();
			$project_is_valid = $this->CI->lib_custom->project_is_valid($created_by, $c_project->id);
			if (!$project_is_valid)
				throw new Exception("Access denied for the project.");
		}
		if (property_exists($data, 'quantity_add'))
		{
			$quantity_add = $data->quantity_add;
			unset($data->quantity_add);
		}
		
		$data->quantity_box_from = $m_inventory->quantity_box;
		$data->quantity_from = $m_inventory->quantity;
		$quantity_to = $m_inventory->quantity + $quantity_add;
		
		// -- Update inventory with new quantity --
		$data_m_inventory_log = new stdClass();
		$data_m_inventory_log->log_type = "ADJUST";
		$data_m_inventory_log->ref1_code = $m_inventory_adjust->code;
		$data_m_inventory_log->notes = 'Add Adjust';
		$m_inventory_id = $this->CI->lib_inventory->adjust($m_inventory->id, $m_inventory->quantity_box, $quantity_to, $created_by, $data_m_inventory_log);
		
		$m_inventory = new M_inventory($m_inventory_id);
		
		// -- Inventory adjust detail add --
		$data->pallet = $m_inventory->pallet;
		$data->carton_no = $m_inventory->carton_no;
		$data->lot_no = $m_inventory->lot_no;
		$data->quantity_box_to = $m_inventory->quantity_box;
		$data->quantity_to = $m_inventory->quantity;
		$data->created_by = $created_by;
		
		$m_inventory_adjustdetail = new M_inventory_adjustdetail();
		$this->set_model_fields_values($m_inventory_adjustdetail, $data);
		$m_inventory_adjust_saved = $m_inventory_adjustdetail->save($m_inventory_adjustdetail_relations);
		if (!$m_inventory_adjust_saved)
			throw new Exception($m_inventory_adjustdetail->error->string);
		
		return $m_inventory_adjustdetail->id;
	}
	
	protected function adjustdetail_remove($m_inventory_adjustdetail_id, $removed_by = NULL)
	{
		$m_inventory_adjustdetail = new M_inventory_adjustdetail($m_inventory_adjustdetail_id);
		$m_inventory_adjust = $m_inventory_adjustdetail->m_inventory_adjust->get();
		
		// -- ReAdjust Inventory --
		$m_inventory = $m_inventory_adjustdetail->m_inventory->get();
		$inventory_quantity_box = $m_inventory->quantity_box - ($m_inventory_adjustdetail->quantity_box_to - $m_inventory_adjustdetail->quantity_box_from);
		$inventory_quantity = $m_inventory->quantity - ($m_inventory_adjustdetail->quantity_to - $m_inventory_adjustdetail->quantity_from);
		
		$c_project = $m_inventory->c_project->get();
		$project_is_valid = $this->CI->lib_custom->project_is_valid($removed_by, $c_project->id);
		if (!$project_is_valid)
			throw new Exception("Access denied for the project.");
		
		// -- Must remove in last operation first validation --
		$this->verify_last_of_operation($m_inventory->id, 'm_inventory_adjustdetails', $m_inventory_adjustdetail_id);
		
		// -- Update inventory with old quantity --
		$data_m_inventory_log = new stdClass();
		$data_m_inventory_log->log_type = "ADJUST";
		$data_m_inventory_log->ref1_code = $m_inventory_adjust->code;
		$data_m_inventory_log->notes = 'Remove Adjust';
		$this->CI->lib_inventory->adjust($m_inventory->id, $inventory_quantity_box, $inventory_quantity, $removed_by, $data_m_inventory_log);
		
		// -- Remove Adjust Detail --
		if (!$m_inventory_adjustdetail->delete())
			throw new Exception($m_inventory_adjustdetail->error->string);
		
		return $m_inventory_adjustdetail_id;
	}
	
	private function adjustdetail_criteria_query($data, $table_alias, $table_name = 'm_inventory_adjustdetails')
	{
		$fields = array(
			'm_grid_id', 'm_product_id', 'pallet', 'barcode', 'carton_no', 'lot_no'
		);
		if ($table_name == 'm_inventory_adjustdetails')
		{
			$fields[] = 'm_inventory_adjust_id';
		}
		if ($table_name == 'm_inventories')
		{
			$fields[] = 'c_project_id';
			$fields[] = 'packed_date';
			$fields[] = 'received_date';
			$fields[] = 'expired_date';
			$fields[] = 'condition';
		}
		
		foreach ($data as $field=>$value)
		{
			if (!in_array($field, $fields))
				continue;
			
			if ($value !== NULL)
			{
				if ($value !== '')
				{
					$this->CI->db
						->where($table_alias.'.'.$field, $value);
				}
			}
			else
				$this->CI->db
					->where($table_alias.'.'.$field." IS NULL", NULL, FALSE);
		}
	}
	
	/* ----------------- */
	/* -- MOVE REGION -- */
	/* ----------------- */
	
	public function move_add($data, $created_by = NULL)
	{
		$data->created_by = $created_by;
		
		$m_inventory_move = new M_inventory_move();
		$this->set_model_fields_values($m_inventory_move, $data);
		$m_inventory_move_saved = $m_inventory_move->save();
		if (!$m_inventory_move_saved)
			throw new Exception($m_inventory_move->error->string);
		
		return $m_inventory_move->id;
	}
	
	public function move_update($m_inventory_move_id, $data, $updated_by = NULL)
	{
		$data->updated_by = $updated_by;
		
		$m_inventory_move = new M_inventory_move($m_inventory_move_id);
		$this->set_model_fields_values($m_inventory_move, $data);
		$m_inventory_move_saved = $m_inventory_move->save();
		if (!$m_inventory_move_saved)
			throw new Exception($m_inventory_move->error->string);
		
		return $m_inventory_move_id;
	}
	
	public function move_remove($m_inventory_move_id, $removed_by = NULL)
	{
		$m_inventory_move = new M_inventory_move($m_inventory_move_id);
		
		// -- Remove Inventory Move Detail --
		$table = $this->CI->db
			->select('id')
			->from('m_inventory_movedetails')
			->where('m_inventory_move_id', $m_inventory_move->id)
			->order_by('id', 'desc')
			->get();
		$m_inventory_movedetails = $table->result();
		foreach ($m_inventory_movedetails as $m_inventory_movedetail)
		{
			$this->movedetail_remove($m_inventory_movedetail->id, $removed_by);
		}
		
		// -- Remove Inventory Move --
		if (!$m_inventory_move->delete())
			throw new Exception($m_inventory_move->error->string);
		
		return $m_inventory_move_id;
	}
	
	public function movedetail_add_by_properties($data, $created_by = NULL)
	{
		$c_project_ids = $this->CI->lib_custom->project_get_ids($created_by);
		
		$quantity_box_new = NULL;
		
		if (property_exists($data, 'quantity_box'))
		{
			$quantity_box_new = $data->quantity_box;
		}
		
		$m_inventory_criteria = clone $data;
		if (property_exists($data, 'pallet_from'))
		{
			$m_inventory_criteria->pallet = $data->pallet_from;
		}
		if (property_exists($data, 'm_gridfrom_id'))
		{
			$m_inventory_criteria->m_grid_id = $data->m_gridfrom_id;
		}
		
		if ($quantity_box_new !== NULL)
		{
			// -- Get exist quantity --
			$exist_quantity_box = 0;
			$this->CI->db
				->select_if_null("SUM(inv.quantity_box)", 0, 'quantity_box')
				->from('m_inventories inv');
			$this->movedetail_criteria_query($m_inventory_criteria, 'inv', 'm_inventories');
			$this->CI->db
				->where("inv.quantity_box > 0", NULL, FALSE);
			$this->CI->lib_custom->project_query_filter('inv.c_project_id', $c_project_ids);
			$table = $this->CI->db->get();
			if ($table->num_rows() > 0)
			{
				$m_inventory_record = $table->first_row();
				$exist_quantity_box = $m_inventory_record->quantity_box;
			}
			
			if ($quantity_box_new !== NULL && $quantity_box_new > $exist_quantity_box)
			{
				throw new Exception("New quantity box ".$quantity_box_new." more than exist quantity box ".$exist_quantity_box);
			}
		}
		
		// -- Get exist inventory records --
		$this->CI->db
			->select("inv.id, inv.quantity_box")
			->from('m_inventories inv');
		$this->movedetail_criteria_query($m_inventory_criteria, 'inv', 'm_inventories');
		$this->CI->db
			->where("inv.quantity_box > 0", NULL, FALSE)
			->order_by('inv.quantity_box', 'asc')
			->order_by('inv.updated', 'desc');
		$this->CI->lib_custom->project_query_filter('inv.c_project_id', $c_project_ids);
		$table = $this->CI->db->get();
		if ($table->num_rows() == 0)
			throw new Exception("Critera not found in inventory existing.");
		$m_inventory_records = $table->result();
		
		// -- Move all quantity --
		if ($quantity_box_new === NULL)
		{
			foreach ($m_inventory_records as $m_inventory_record)
			{
				// -- Inventory move detail add --
				$data->m_inventory_id = $m_inventory_record->id;
				$data->quantity_box_to = $quantity_box_new;
				$this->movedetail_add(clone $data, $created_by);
			}
		}
		// -- Move partial quantity --
		else
		{
			$quantity_box_moved = 0;
			foreach ($m_inventory_records as $m_inventory_record)
			{
				$quantity_box = $quantity_box_new - $quantity_box_moved;
				if ($quantity_box > $m_inventory_record->quantity_box)
					$quantity_box = $m_inventory_record->quantity_box;
				
				if ($quantity_box > 0)
				{
					// -- Inventory move detail add --
					$data->m_inventory_id = $m_inventory_record->id;
					$data->quantity_box_to = $quantity_box;
					$this->movedetail_add(clone $data, $created_by);
				}
				
				$quantity_box_moved += $quantity_box;
				if ($quantity_box_moved >= $quantity_box_new)
					break;
			}
		}
		
		// -- Verification Pallet on Grid --
		foreach ($m_inventory_records as $m_inventory_record)
		{
			$table = $this->CI->db
				->select("id, pallet, m_grid_id")
				->from('m_inventories inv')
				->where('inv.id', $m_inventory_record->id)
				->get();
			if ($table->num_rows() > 0)
			{
				$m_inventory_record = $table->first_row();
				$this->CI->lib_inventory->verify_pallet_grid($m_inventory_record->pallet, $m_inventory_record->m_grid_id);
			}
		}
	}
	
	public function movedetail_remove_by_properties($data, $removed_by = NULL)
	{
		$c_project_ids = $this->CI->lib_custom->project_get_ids($removed_by);
		
		// -- Get Move Detail Exists --
		$this->CI->db
			->select("imd.id")
			->select("imd.pallet_from, imd.m_gridfrom_id")
			->from('m_inventory_movedetails imd')
			->join('m_inventories inv', "inv.id = imd.m_inventory_id");
		$this->movedetail_criteria_query($data, 'imd', 'm_inventory_movedetails');
		$this->CI->lib_custom->project_query_filter('inv.c_project_id', $c_project_ids);
		$table = $this->CI->db
			->order_by('imd.id', 'desc')
			->get();
		if ($table->num_rows() == 0)
		{
			throw new Exception("Critera not found in move existing.");
		}
		
		// -- Remove Move Detail Exists --
		$table_records = $table->result();
		foreach ($table_records as $table_record)
		{
			$this->movedetail_remove($table_record->id, $removed_by);
		}
		
		// -- Verification Pallet on Grid --
		foreach ($table_records as $table_record)
		{
			$this->CI->lib_inventory->verify_pallet_grid($table_record->pallet_from, $table_record->m_gridfrom_id);
		}
	}
	
	protected function movedetail_add($data, $created_by = NULL)
	{
		$m_inventory_move_ids = array();
		
		$m_inventory_move = new M_inventory_move();
		$m_inventory = new M_inventory();
		$m_gridto = new M_grid();
		$quantity_box_to = NULL;
		
		$m_inventory_movedetail_relations = array();
		if (property_exists($data, 'm_inventory_move_id'))
		{
			$m_inventory_move = new M_inventory_move($data->m_inventory_move_id);
			$m_inventory_movedetail_relations['m_inventory_move'] = $m_inventory_move;
			unset($data->m_inventory_move_id);
		}
		if (property_exists($data, 'm_inventory_id'))
		{
			$m_inventory = new M_inventory($data->m_inventory_id);
			$m_inventory_movedetail_relations['m_inventory'] = $m_inventory;
			$data->pallet_from = $m_inventory->pallet;
			unset($data->m_inventory_id);
			
			$c_project = $m_inventory->c_project->get();
			$project_is_valid = $this->CI->lib_custom->project_is_valid($created_by, $c_project->id);
			if (!$project_is_valid)
				throw new Exception("Access denied for the project.");
		}
		if (property_exists($data, 'm_gridto_id'))
		{
			$m_gridto = new M_grid($data->m_gridto_id);
			$m_inventory_movedetail_relations['m_gridto'] = $m_gridto;
			unset($data->m_gridto_id);
		}
		else
		{
			if (property_exists($data, 'pallet_to'))
			{
				$c_project_ids = $this->CI->lib_custom->project_get_ids($created_by);
				
				$this->CI->db
					->select('m_grid_id')
					->from('m_inventories')
					->where('pallet', $data->pallet_to)
					->where("quantity_box > 0", NULL, FALSE);
				$this->CI->lib_custom->project_query_filter('c_project_id', $c_project_ids);
				$table = $this->CI->db
					->get();
				if ($table->num_rows() > 0)
				{
					$table_record = $table->first_row();
					$m_gridto = new M_grid($table_record->m_grid_id);
					$m_inventory_movedetail_relations['m_gridto'] = $m_gridto;
				}
				else
				{
					// -- If destination is not active or quantity is empty, use grid destination of source --
					$m_gridto = $m_inventory->m_grid->get();
					$m_inventory_movedetail_relations['m_gridto'] = $m_gridto;
				}
			}
			else
				throw new Exception("Unknown destination.");
		}
		if (property_exists($data, 'quantity_box_to'))
		{
			$quantity_box_to = $data->quantity_box_to;
		}
		
		// -- Move inventory with new grid and quantity --
		$data_m_inventory = new stdClass();
		if (property_exists($data, 'pallet_to'))
		{
			$data_m_inventory->pallet = $data->pallet_to;
		}
		
		$data_m_inventory_log = new stdClass();
		$data_m_inventory_log->log_type = "MOVE";
		$data_m_inventory_log->ref1_code = $m_inventory_move->code;
		$data_m_inventory_log->notes = 'Add Move';
		$move_results = $this->CI->lib_inventory->move($m_inventory->id, $m_gridto->id, $data_m_inventory, $quantity_box_to, $created_by, $data_m_inventory_log);
		foreach ($move_results as $move_result)
		{
			if ($move_result['type'] != 'move')
				continue;
			
			// -- Inventory move detail add --
			$m_inventory = new M_inventory($move_result['m_inventory_id']);
			$m_gridfrom = new M_grid($move_result['m_gridfrom_id']);
			
			$m_inventory_movedetail_relations['m_inventory_move'] = $m_inventory_move;
			$m_inventory_movedetail_relations['m_inventory'] = $m_inventory;
			$m_inventory_movedetail_relations['m_gridfrom'] = $m_gridfrom;
			$m_inventory_movedetail_relations['m_gridto'] = $m_gridto;
			$m_inventory_movedetail_relations['m_product'] = $m_inventory->m_product->get();
			
			$data->barcode = $m_inventory->barcode;
			$data->pallet_to = $m_inventory->pallet;
			$data->carton_no = $m_inventory->carton_no;
			$data->lot_no = $m_inventory->lot_no;
			$data->quantity_from = $move_result['quantity_from'];
			$data->quantity_to = $move_result['quantity_to'];
			$data->quantity_box_from = $move_result['quantity_box_from'];
			$data->quantity_box_to = $move_result['quantity_box_to'];
			$data->created_by = $created_by;
			
			$m_inventory_movedetail = new M_inventory_movedetail();
			$this->set_model_fields_values($m_inventory_movedetail, $data);
			$m_inventory_move_saved = $m_inventory_movedetail->save($m_inventory_movedetail_relations);
			if (!$m_inventory_move_saved)
				throw new Exception($m_inventory_movedetail->error->string);
			
			$m_inventory_move_ids[] = $m_inventory_movedetail->id;
		}
		
		return $m_inventory_move_ids;
	}
	
	protected function movedetail_remove($m_inventory_movedetail_id, $removed_by = NULL)
	{
		$m_inventory_movedetail = new M_inventory_movedetail($m_inventory_movedetail_id);
		$m_inventory_move = $m_inventory_movedetail->m_inventory_move->get();
		
		// -- ReMove Inventory to old grid --
		$m_inventory = $m_inventory_movedetail->m_inventory->get();
		$m_gridfrom = $m_inventory_movedetail->m_gridfrom->get();
		
		$c_project = $m_inventory->c_project->get();
		$project_is_valid = $this->CI->lib_custom->project_is_valid($removed_by, $c_project->id);
		if (!$project_is_valid)
			throw new Exception("Access denied for the project.");
		
		// -- Must remove in last operation first validation --
		$this->verify_last_of_operation($m_inventory->id, 'm_inventory_movedetails', $m_inventory_movedetail_id);
		
		// -- Update inventory with old grid and quantity --
		$data_m_inventory = new stdClass();
		$data_m_inventory->pallet = $m_inventory_movedetail->pallet_from;
		
		$data_m_inventory_log = new stdClass();
		$data_m_inventory_log->log_type = "MOVE";
		$data_m_inventory_log->ref1_code = $m_inventory_move->code;
		$data_m_inventory_log->notes = 'Remove Move';
		$move_results = $this->CI->lib_inventory->move($m_inventory->id, $m_gridfrom->id, $data_m_inventory, $m_inventory_movedetail->quantity_box_to, $removed_by, $data_m_inventory_log);
		
		// -- Remove Adjust Detail --
		if (!$m_inventory_movedetail->delete())
			throw new Exception($m_inventory_movedetail->error->string);
		
		return $m_inventory_movedetail_id;
	}
	
	private function movedetail_criteria_query($data, $table_alias, $table_name = 'm_inventory_movedetails')
	{
		$fields = array(
			'm_product_id', 'barcode', 'carton_no', 'lot_no'
		);
		if ($table_name == 'm_inventory_movedetails')
		{
			$fields[] = 'pallet_from';
			$fields[] = 'pallet_to';
			$fields[] = 'm_inventory_move_id';
			$fields[] = 'm_gridto_id';
		}
		if ($table_name == 'm_inventories')
		{
			$fields[] = 'pallet';
			$fields[] = 'm_grid_id';
			$fields[] = 'c_project_id';
			$fields[] = 'packed_date';
			$fields[] = 'received_date';
			$fields[] = 'expired_date';
			$fields[] = 'condition';
		}
		
		foreach ($data as $field=>$value)
		{
			if (!in_array($field, $fields))
				continue;
			
			if ($value !== NULL)
			{
				if ($value !== '')
				{
					$this->CI->db
						->where($table_alias.'.'.$field, $value);
				}
			}
			else
				$this->CI->db
					->where($table_alias.'.'.$field." IS NULL", NULL, FALSE);
		}
	}
	
	/* ----------------- */
	/* -- HOLD REGION -- */
	/* ----------------- */
	
	public function hold_add($data, $created_by = NULL)
	{
		$data->created_by = $created_by;
		
		$m_inventory_hold = new M_inventory_hold();
		$this->set_model_fields_values($m_inventory_hold, $data);
		$m_inventory_hold_saved = $m_inventory_hold->save();
		if (!$m_inventory_hold_saved)
			throw new Exception($m_inventory_hold->error->string);
		
		return $m_inventory_hold->id;
	}
	
	public function hold_update($m_inventory_hold_id, $data, $updated_by = NULL)
	{
		$data->updated_by = $updated_by;
		
		$m_inventory_hold = new M_inventory_hold($m_inventory_hold_id);
		$this->set_model_fields_values($m_inventory_hold, $data);
		$m_inventory_hold_saved = $m_inventory_hold->save();
		if (!$m_inventory_hold_saved)
			throw new Exception($m_inventory_hold->error->string);
		
		return $m_inventory_hold_id;
	}
	
	public function hold_remove($m_inventory_hold_id, $removed_by = NULL)
	{
		$m_inventory_hold = new M_inventory_hold($m_inventory_hold_id);
		
		// -- Remove Inventory Hold Detail --
		foreach ($m_inventory_hold->m_inventory_holddetail->get() as $m_inventory_holddetail)
		{
			$this->holddetail_remove($m_inventory_holddetail->id, $removed_by);
		}
		
		// -- Remove Inventory Hold --
		if (!$m_inventory_hold->delete())
			throw new Exception($m_inventory_hold->error->string);
		
		return $m_inventory_hold_id;
	}
	
	public function holddetail_add_by_properties($data, $created_by = NULL)
	{
		$c_project_ids = $this->CI->lib_custom->project_get_ids($created_by);
		
		// -- Get inventory records --
		$this->CI->db
			->select("inv.id, inv.quantity_box, inv.quantity")
			->from('m_inventories inv');
		$this->holddetail_criteria_query($data, 'inv', 'm_inventories');
		$this->CI->db
			->where("(inv.quantity_box > 0 OR inv.quantity > 0)", NULL, FALSE);
		$this->CI->lib_custom->project_query_filter('inv.c_project_id', $c_project_ids);
		$table = $this->CI->db->get();
		if ($table->num_rows() == 0)
			throw new Exception("Inventory criteria is not match.");
		
		$m_inventory_records = $table->result();
		foreach ($m_inventory_records as $m_inventory_record)
		{
			// -- Inventory hold detail add --
			$data->m_inventory_id = $m_inventory_record->id;
			$this->holddetail_add(clone $data, $created_by);
		}
	}
	
	public function holddetail_rehold_by_properties($data, $rehold_by = NULL)
	{
		$c_project_ids = $this->CI->lib_custom->project_get_ids($rehold_by);
		
		// -- Get Hold Detail Exists --
		$this->CI->db
			->select("ihd.id")
			->from('m_inventory_holddetails ihd')
			->join('m_inventories inv', "inv.id = ihd.m_inventory_id")
			->where('ihd.is_hold', 0);
		$this->holddetail_criteria_query($data, 'ihd', 'm_inventory_holddetails');
		$this->CI->lib_custom->project_query_filter('inv.c_project_id', $c_project_ids);
		$table = $this->CI->db
			->order_by('ihd.id', 'asc')
			->get();
		if ($table->num_rows() == 0)
		{
			throw new Exception("Critera not found in hold existing.");
		}
		
		// -- Rehold Hold Detail Exists --
		$table_records = $table->result();
		foreach ($table_records as $table_record)
		{
			$this->holddetail_rehold($table_record->id, $rehold_by);
		}
	}
	
	public function holddetail_unhold_by_properties($data, $unhold_by = NULL)
	{
		$c_project_ids = $this->CI->lib_custom->project_get_ids($unhold_by);
		
		// -- Get Hold Detail Exists --
		$this->CI->db
			->select("ihd.id")
			->from('m_inventory_holddetails ihd')
			->join('m_inventories inv', "inv.id = ihd.m_inventory_id")
			->where('ihd.is_hold', 1);
		$this->holddetail_criteria_query($data, 'ihd', 'm_inventory_holddetails');
		$this->CI->lib_custom->project_query_filter('inv.c_project_id', $c_project_ids);
		$table = $this->CI->db
			->order_by('ihd.id', 'desc')
			->get();
		if ($table->num_rows() == 0)
		{
			throw new Exception("Critera not found in hold existing.");
		}
		
		// -- Rehold Hold Detail Exists --
		$table_records = $table->result();
		foreach ($table_records as $table_record)
		{
			$this->holddetail_unhold($table_record->id, $unhold_by);
		}
	}
	
	public function holddetail_remove_by_properties($data, $removed_by = NULL)
	{
		$c_project_ids = $this->CI->lib_custom->project_get_ids($removed_by);
		
		// -- Get Hold Detail Exists --
		$this->CI->db
			->select("ihd.id")
			->from('m_inventory_holddetails ihd')
			->join('m_inventories inv', "inv.id = ihd.m_inventory_id");
		$this->holddetail_criteria_query($data, 'ihd', 'm_inventory_holddetails');
		$this->CI->lib_custom->project_query_filter('inv.c_project_id', $c_project_ids);
		$table = $this->CI->db
			->order_by('ihd.id', 'desc')
			->get();
		if ($table->num_rows() == 0)
		{
			throw new Exception("Critera not found in hold existing.");
		}
		
		// -- Remove Hold Detail Exists --
		$table_records = $table->result();
		foreach ($table_records as $table_record)
		{
			$this->holddetail_remove($table_record->id, $removed_by);
		}
	}
	
	protected function holddetail_add($data, $created_by = NULL)
	{
		$m_inventory_hold = new M_inventory_hold();
		$m_inventory = new M_inventory();
		$m_grid = new M_grid();
		$m_product = new M_product();
		
		$m_inventory_holddetail_relations = array();
		if (property_exists($data, 'm_inventory_hold_id'))
		{
			$m_inventory_hold = new M_inventory_hold($data->m_inventory_hold_id);
			$m_inventory_holddetail_relations['m_inventory_hold'] = $m_inventory_hold;
			unset($data->m_inventory_hold_id);
		}
		if (property_exists($data, 'm_inventory_id'))
		{
			$m_inventory = new M_inventory($data->m_inventory_id);
			$m_inventory_holddetail_relations['m_inventory'] = $m_inventory;
			$m_inventory_holddetail_relations['m_grid'] = $m_inventory->m_grid->get();
			$m_inventory_holddetail_relations['m_product'] = $m_inventory->m_product->get();
			unset($data->m_inventory_id);
			
			$c_project = $m_inventory->c_project->get();
			$project_is_valid = $this->CI->lib_custom->project_is_valid($created_by, $c_project->id);
			if (!$project_is_valid)
				throw new Exception("Access denied for the project.");
		}
		
		$data->quantity_box_from = $m_inventory->quantity_box;
		$data->quantity_from = $m_inventory->quantity;
		
		// -- Update inventory with 0 --
		$data_m_inventory_log = new stdClass();
		$data_m_inventory_log->log_type = "HOLD";
		$data_m_inventory_log->ref1_code = $m_inventory_hold->code;
		$data_m_inventory_log->notes = 'Add Hold';
		$m_inventory_id = $this->CI->lib_inventory->adjust($m_inventory->id, 0, 0, $created_by, $data_m_inventory_log);
		
		$m_inventory = new M_inventory($m_inventory_id);
		
		// -- Inventory hold detail add --
		$data->pallet = $m_inventory->pallet;
		$data->carton_no = $m_inventory->carton_no;
		$data->lot_no = $m_inventory->lot_no;
		$data->quantity_box_to = $m_inventory->quantity_box;
		$data->quantity_to = $m_inventory->quantity;
		$data->is_hold = 1;
		$data->created_by = $created_by;
		
		$m_inventory_holddetail = new M_inventory_holddetail();
		$this->set_model_fields_values($m_inventory_holddetail, $data);
		$m_inventory_hold_saved = $m_inventory_holddetail->save($m_inventory_holddetail_relations);
		if (!$m_inventory_hold_saved)
			throw new Exception($m_inventory_holddetail->error->string);
		
		return $m_inventory_holddetail->id;
	}
	
	protected function holddetail_rehold($m_inventory_holddetail_id, $rehold_by = NULL)
	{
		$m_inventory_holddetail = new M_inventory_holddetail($m_inventory_holddetail_id);
		
		if (!$m_inventory_holddetail->is_hold)
		{
			$m_inventory_hold = $m_inventory_holddetail->m_inventory_hold->get();
			
			// -- ReAdjust Inventory --
			$m_inventory = $m_inventory_holddetail->m_inventory->get();
			$inventory_quantity_box = $m_inventory->quantity_box - ($m_inventory_holddetail->quantity_box_from - $m_inventory_holddetail->quantity_box_to);
			$inventory_quantity = $m_inventory->quantity - ($m_inventory_holddetail->quantity_from - $m_inventory_holddetail->quantity_to);
			
			$c_project = $m_inventory->c_project->get();
			$project_is_valid = $this->CI->lib_custom->project_is_valid($rehold_by, $c_project->id);
			if (!$project_is_valid)
				throw new Exception("Access denied for the project.");
			
			// -- Must in last operation first validation --
			$this->verify_last_of_operation($m_inventory->id, 'm_inventory_holddetails', $m_inventory_holddetail_id);
			
			// -- Update inventory with old quantity --
			$data_m_inventory_log = new stdClass();
			$data_m_inventory_log->log_type = "HOLD";
			$data_m_inventory_log->ref1_code = $m_inventory_hold->code;
			$data_m_inventory_log->notes = 'Rehold';
			$this->CI->lib_inventory->adjust($m_inventory->id, $inventory_quantity_box, $inventory_quantity, $rehold_by, $data_m_inventory_log);
			
			// -- Inventory hold detail set hold --
			$data = new stdClass();
			$data->is_hold = 1;
			$data->updated_by = $rehold_by;
			
			$this->set_model_fields_values($m_inventory_holddetail, $data);
			$m_inventory_hold_saved = $m_inventory_holddetail->save();
			if (!$m_inventory_hold_saved)
				throw new Exception($m_inventory_holddetail->error->string);
		}
		
		return $m_inventory_holddetail_id;
	}
	
	protected function holddetail_unhold($m_inventory_holddetail_id, $unhold_by = NULL)
	{
		$m_inventory_holddetail = new M_inventory_holddetail($m_inventory_holddetail_id);
		
		if ($m_inventory_holddetail->is_hold)
		{
			$m_inventory_hold = $m_inventory_holddetail->m_inventory_hold->get();
			
			// -- ReAdjust Inventory --
			$m_inventory = $m_inventory_holddetail->m_inventory->get();
			$inventory_quantity_box = $m_inventory->quantity_box - ($m_inventory_holddetail->quantity_box_to - $m_inventory_holddetail->quantity_box_from);
			$inventory_quantity = $m_inventory->quantity - ($m_inventory_holddetail->quantity_to - $m_inventory_holddetail->quantity_from);
			
			$c_project = $m_inventory->c_project->get();
			$project_is_valid = $this->CI->lib_custom->project_is_valid($unhold_by, $c_project->id);
			if (!$project_is_valid)
				throw new Exception("Access denied for the project.");
			
			// -- Must in last operation first validation --
			$this->verify_last_of_operation($m_inventory->id, 'm_inventory_holddetails', $m_inventory_holddetail_id);
			
			// -- Update inventory with old quantity --
			$data_m_inventory_log = new stdClass();
			$data_m_inventory_log->log_type = "HOLD";
			$data_m_inventory_log->ref1_code = $m_inventory_hold->code;
			$data_m_inventory_log->notes = 'Unhold';
			$this->CI->lib_inventory->adjust($m_inventory->id, $inventory_quantity_box, $inventory_quantity, $unhold_by, $data_m_inventory_log);
			
			// -- Inventory hold detail set unhold --
			$data = new stdClass();
			$data->is_hold = 0;
			$data->updated_by = $unhold_by;
			
			$this->set_model_fields_values($m_inventory_holddetail, $data);
			$m_inventory_hold_saved = $m_inventory_holddetail->save();
			if (!$m_inventory_hold_saved)
				throw new Exception($m_inventory_holddetail->error->string);
		}
		
		return $m_inventory_holddetail_id;
	}
	
	protected function holddetail_remove($m_inventory_holddetail_id, $removed_by = NULL)
	{
		$m_inventory_holddetail = new M_inventory_holddetail($m_inventory_holddetail_id);
		
		if ($m_inventory_holddetail->is_hold)
		{
			$m_inventory_hold = $m_inventory_holddetail->m_inventory_hold->get();
			
			// -- ReAdjust Inventory --
			$m_inventory = $m_inventory_holddetail->m_inventory->get();
			$inventory_quantity_box = $m_inventory->quantity_box - ($m_inventory_holddetail->quantity_box_to - $m_inventory_holddetail->quantity_box_from);
			$inventory_quantity = $m_inventory->quantity - ($m_inventory_holddetail->quantity_to - $m_inventory_holddetail->quantity_from);
			
			$c_project = $m_inventory->c_project->get();
			$project_is_valid = $this->CI->lib_custom->project_is_valid($removed_by, $c_project->id);
			if (!$project_is_valid)
				throw new Exception("Access denied for the project.");
			
			// -- Must remove in last operation first validation --
			$this->verify_last_of_operation($m_inventory->id, 'm_inventory_holddetails', $m_inventory_holddetail_id);
			
			// -- Update inventory with old quantity --
			$data_m_inventory_log = new stdClass();
			$data_m_inventory_log->log_type = "HOLD";
			$data_m_inventory_log->ref1_code = $m_inventory_hold->code;
			$data_m_inventory_log->notes = 'Remove Hold';
			$this->CI->lib_inventory->adjust($m_inventory->id, $inventory_quantity_box, $inventory_quantity, $removed_by, $data_m_inventory_log);
		}
		
		// -- Remove Hold Detail --
		if (!$m_inventory_holddetail->delete())
			throw new Exception($m_inventory_holddetail->error->string);
		
		return $m_inventory_holddetail_id;
	}
	
	private function holddetail_criteria_query($data, $table_alias, $table_name = 'm_inventory_holddetails')
	{
		$fields = array(
			'm_grid_id', 'm_product_id', 'pallet', 'barcode', 'carton_no', 'lot_no'
		);
		if ($table_name == 'm_inventory_holddetails')
		{
			$fields[] = 'm_inventory_hold_id';
		}
		if ($table_name == 'm_inventories')
		{
			$fields[] = 'c_project_id';
			$fields[] = 'packed_date';
			$fields[] = 'received_date';
			$fields[] = 'expired_date';
			$fields[] = 'condition';
		}
		
		foreach ($data as $field=>$value)
		{
			if (!in_array($field, $fields))
				continue;
			
			if ($value !== NULL)
			{
				if ($value !== '')
				{
					$this->CI->db
						->where($table_alias.'.'.$field, $value);
				}
			}
			else
				$this->CI->db
					->where($table_alias.'.'.$field." IS NULL", NULL, FALSE);
		}
	}
	
	/* --------------------- */
	/* -- ASSEMBLY REGION -- */
	/* --------------------- */
	
	public function assembly_add($data, $created_by = NULL)
	{
		$data->created_by = $created_by;
		
		$m_inventory_assembly = new M_inventory_assembly();
		$this->set_model_fields_values($m_inventory_assembly, $data);
		$m_inventory_assembly_saved = $m_inventory_assembly->save();
		if (!$m_inventory_assembly_saved)
			throw new Exception($m_inventory_assembly->error->string);
		
		return $m_inventory_assembly->id;
	}
	
	public function assembly_update($m_inventory_assembly_id, $data, $updated_by = NULL)
	{
		$data->updated_by = $updated_by;
		
		$m_inventory_assembly = new M_inventory_assembly($m_inventory_assembly_id);
		$this->set_model_fields_values($m_inventory_assembly, $data);
		$m_inventory_assembly_saved = $m_inventory_assembly->save();
		if (!$m_inventory_assembly_saved)
			throw new Exception($m_inventory_assembly->error->string);
		
		return $m_inventory_assembly_id;
	}
	
	public function assembly_remove($m_inventory_assembly_id, $removed_by = NULL)
	{
		$m_inventory_assembly = new M_inventory_assembly($m_inventory_assembly_id);
		
		// -- Remove Inventory Assembly Source --
		$table = $this->CI->db
			->select('id')
			->from('m_inventory_assemblysources')
			->where('m_inventory_assembly_id', $m_inventory_assembly->id)
			->order_by('id', 'desc')
			->get();
		$m_inventory_assemblysources = $table->result();
		foreach ($m_inventory_assemblysources as $m_inventory_assemblysource)
		{
			$this->assemblysource_remove($m_inventory_assemblysource->id, $removed_by);
		}
		
		// -- Remove Inventory Assembly Target --
		$table = $this->CI->db
			->select('id')
			->from('m_inventory_assemblytargets')
			->where('m_inventory_assembly_id', $m_inventory_assembly->id)
			->order_by('id', 'desc')
			->get();
		$m_inventory_assemblytargets = $table->result();
		foreach ($m_inventory_assemblytargets as $m_inventory_assemblytarget)
		{
			$this->assemblytarget_remove($m_inventory_assemblytarget->id, $removed_by);
		}
		
		// -- Remove Inventory Assembly --
		if (!$m_inventory_assembly->delete())
			throw new Exception($m_inventory_assembly->error->string);
		
		return $m_inventory_assembly_id;
	}
	
	public function assemblysource_add_by_properties($data, $created_by = NULL)
	{
		$c_project_ids = $this->CI->lib_custom->project_get_ids($created_by);
		
		$quantity_decrease = NULL;
		if (property_exists($data, 'quantity'))
		{
			$quantity_decrease = $data->quantity;
			unset($data->quantity);
		}
		
		// -- Get exist quantity --
		$exist_quantity = 0;
		$this->CI->db
			->select_if_null("SUM(inv.quantity)", 0, 'quantity')
			->from('m_inventories inv');
		$this->assemblysource_criteria_query($data, 'inv', 'm_inventories');
		$this->CI->db
			->where("inv.quantity > 0", NULL, FALSE);
		$this->CI->lib_custom->project_query_filter('inv.c_project_id', $c_project_ids);
		$table = $this->CI->db->get();
		if ($table->num_rows() > 0)
		{
			$m_inventory_record = $table->first_row();
			$exist_quantity = $m_inventory_record->quantity;
		}
		
		if ($quantity_decrease === NULL)
			$quantity_decrease = $exist_quantity;
		
		// -- Must decrease quantity --
		if ($quantity_decrease > $exist_quantity)
			throw new Exception("Inventory quantity ".$quantity_decrease." is too large.");
		
		// -- Could not negative --
		if ($quantity_decrease < 0)
			throw new Exception("Inventory quantity could not negative.");
		
		// -- Get inventory records --
		$this->CI->db
			->select("inv.id, inv.quantity")
			->from('m_inventories inv');
		$this->assemblysource_criteria_query($data, 'inv', 'm_inventories');
		$this->CI->db
			->where("inv.quantity >", 0)
			->order_by('inv.quantity', 'asc')
			->order_by('inv.id', 'desc');
		$this->CI->lib_custom->project_query_filter('inv.c_project_id', $c_project_ids);
		$table = $this->CI->db->get();
		if ($table->num_rows() == 0)
			throw new Exception("Inventory criteria is not match.");
		
		// -- Decrease inventory quantity (more then one inventory's records) --
		$quantity_adjust = $quantity_decrease;
		$m_inventory_records = $table->result();
		foreach ($m_inventory_records as $m_inventory_record)
		{
			$quantity = $m_inventory_record->quantity - $quantity_adjust;
			if ($quantity < 0)
				$quantity = $m_inventory_record->quantity;
			else
				$quantity = $quantity_adjust;
			
			// -- Inventory adjust detail add --
			$data->m_inventory_id = $m_inventory_record->id;
			$data->quantity_add = $quantity * -1;
			$this->assemblysource_add(clone $data, $created_by);
			
			$quantity_adjust -= $quantity;
			if ($quantity_adjust == 0)
				break;
			elseif ($quantity_adjust < 0)
				throw new Exception("Inventory quantity of ".$m_inventory_record->id." is ".$m_inventory_record->quantity." so it's not enought for decrease.");
		}
	}
	
	public function assemblysource_remove_by_properties($data, $removed_by = NULL)
	{
		$c_project_ids = $this->CI->lib_custom->project_get_ids($removed_by);
		
		// -- Get Assembly Source Exists --
		$this->CI->db
			->select("ias.id")
			->from('m_inventory_assemblysources ias')
			->join('m_inventories inv', "inv.id = ias.m_inventory_id");
		$this->assemblysource_criteria_query($data, 'ias', 'm_inventory_assemblysources');
		$this->CI->lib_custom->project_query_filter('inv.c_project_id', $c_project_ids);
		$table = $this->CI->db
			->order_by('ias.id', 'desc')
			->get();
		if ($table->num_rows() == 0)
		{
			throw new Exception("Critera not found in assembly source existing.");
		}
		
		// -- Remove Assembly Source Exists --
		$table_records = $table->result();
		foreach ($table_records as $table_record)
		{
			$this->assemblysource_remove($table_record->id, $removed_by);
		}
	}
	
	public function assemblysource_add($data, $created_by = NULL)
	{
		$m_inventory_assembly = new M_inventory_assembly();
		$m_inventory = new M_inventory();
		$m_grid = new M_grid();
		$m_product = new M_product();
		$quantity_add = 0;
		
		$m_inventory_assemblysource_relations = array();
		if (property_exists($data, 'm_inventory_assembly_id'))
		{
			$m_inventory_assembly = new M_inventory_assembly($data->m_inventory_assembly_id);
			$m_inventory_assemblysource_relations['m_inventory_assembly'] = $m_inventory_assembly;
			unset($data->m_inventory_assembly_id);
		}
		if (property_exists($data, 'm_inventory_id'))
		{
			$m_inventory = new M_inventory($data->m_inventory_id);
			$m_inventory_assemblysource_relations['m_inventory'] = $m_inventory;
			$m_inventory_assemblysource_relations['m_grid'] = $m_inventory->m_grid->get();
			$m_inventory_assemblysource_relations['m_product'] = $m_inventory->m_product->get();
			$m_inventory_assemblysource_relations['c_project'] = $m_inventory->c_project->get();
			unset($data->m_inventory_id);
			
			$project_is_valid = $this->CI->lib_custom->project_is_valid($created_by, $m_inventory_assemblysource_relations['c_project']->id);
			if (!$project_is_valid)
				throw new Exception("Access denied for the project.");
		}
		if (property_exists($data, 'quantity_add'))
		{
			$quantity_add = $data->quantity_add;
			unset($data->quantity_add);
		}
		
		$data->quantity_box_from = $m_inventory->quantity_box;
		$data->quantity_from = $m_inventory->quantity;
		$quantity_to = $m_inventory->quantity + $quantity_add;
		
		// -- Update inventory with new quantity --
		$data_m_inventory_log = new stdClass();
		$data_m_inventory_log->log_type = "ASSEMBLY";
		$data_m_inventory_log->ref1_code = $m_inventory_assembly->code;
		$data_m_inventory_log->notes = 'Add Assembly Source';
		$m_inventory_id = $this->CI->lib_inventory->adjust($m_inventory->id, $m_inventory->quantity_box, $quantity_to, $created_by, $data_m_inventory_log);
		
		$m_inventory = new M_inventory($m_inventory_id);
		
		// -- Inventory assembly source add --
		$data->pallet = $m_inventory->pallet;
		$data->carton_no = $m_inventory->carton_no;
		$data->lot_no = $m_inventory->lot_no;
		$data->quantity_box_to = $m_inventory->quantity_box;
		$data->quantity_to = $m_inventory->quantity;
		$data->created_by = $created_by;
		
		$m_inventory_assemblysource = new M_inventory_assemblysource();
		$this->set_model_fields_values($m_inventory_assemblysource, $data);
		$m_inventory_assembly_saved = $m_inventory_assemblysource->save($m_inventory_assemblysource_relations);
		if (!$m_inventory_assembly_saved)
			throw new Exception($m_inventory_assemblysource->error->string);
		
		return $m_inventory_assemblysource->id;
	}
	
	public function assemblysource_remove($m_inventory_assemblysource_id, $removed_by = NULL)
	{
		$m_inventory_assemblysource = new M_inventory_assemblysource($m_inventory_assemblysource_id);
		$m_inventory_assembly = $m_inventory_assemblysource->m_inventory_assembly->get();
		
		// -- ReAssembly Inventory --
		$m_inventory = $m_inventory_assemblysource->m_inventory->get();
		$inventory_quantity_box = $m_inventory->quantity_box - ($m_inventory_assemblysource->quantity_box_to - $m_inventory_assemblysource->quantity_box_from);
		$inventory_quantity = $m_inventory->quantity - ($m_inventory_assemblysource->quantity_to - $m_inventory_assemblysource->quantity_from);
		
		$c_project = $m_inventory->c_project->get();
		$project_is_valid = $this->CI->lib_custom->project_is_valid($removed_by, $c_project->id);
		if (!$project_is_valid)
			throw new Exception("Access denied for the project.");
		
		// -- Must remove in last operation first validation --
		$this->verify_last_of_operation($m_inventory->id, 'm_inventory_assemblysources', $m_inventory_assemblysource_id);
		
		// -- Update inventory with old quantity --
		$data_m_inventory_log = new stdClass();
		$data_m_inventory_log->log_type = "ASSEMBLY";
		$data_m_inventory_log->ref1_code = $m_inventory_assembly->code;
		$data_m_inventory_log->notes = 'Remove Assembly Source';
		$this->CI->lib_inventory->adjust($m_inventory->id, $inventory_quantity_box, $inventory_quantity, $removed_by, $data_m_inventory_log);
		
		// -- Remove Assembly Source --
		if (!$m_inventory_assemblysource->delete())
			throw new Exception($m_inventory_assemblysource->error->string);
		
		return $m_inventory_assemblysource_id;
	}
	
	private function assemblysource_criteria_query($data, $table_alias, $table_name = 'm_inventory_assemblysources')
	{
		$fields = array(
			'm_grid_id', 'm_product_id', 'c_project_id', 'pallet', 'barcode', 'carton_no', 'lot_no'
		);
		if ($table_name == 'm_inventory_assemblysources')
		{
			$fields[] = 'm_inventory_assembly_id';
		}
		if ($table_name == 'm_inventories')
		{
			$fields[] = 'packed_date';
			$fields[] = 'received_date';
			$fields[] = 'expired_date';
			$fields[] = 'condition';
		}
		
		foreach ($data as $field=>$value)
		{
			if (!in_array($field, $fields))
				continue;
			
			if ($value !== NULL)
			{
				if ($value !== '')
				{
					$this->CI->db
						->where($table_alias.'.'.$field, $value);
				}
			}
			else
				$this->CI->db
					->where($table_alias.'.'.$field." IS NULL", NULL, FALSE);
		}
	}
	
	public function assemblytarget_add($data, $created_by = NULL)
	{
		$m_inventory_assembly = new M_inventory_assembly();
		$m_grid = new M_grid();
		$m_product = new M_product();
		$c_project = new C_project();
		$quantity_box = 0;
		$quantity = 0;
		
		$assemblytarget_relations = array();
		if (property_exists($data, 'm_inventory_assembly_id'))
		{
			$m_inventory_assembly = new M_inventory_assembly($data->m_inventory_assembly_id);
			$assemblytarget_relations['m_inventory_assembly'] = $m_inventory_assembly;
			unset($data->m_inventory_assembly_id);
		}
		if (property_exists($data, 'c_project_id'))
		{
			$c_project = new C_project($data->c_project_id);
			$assemblytarget_relations['c_project'] = $c_project;
			unset($data->c_project_id);
		}
		if (property_exists($data, 'm_product_id'))
		{
			$m_product = new M_product($data->m_product_id);
			$assemblytarget_relations['m_product'] = $m_product;
			unset($data->m_product_id);
		}
		if (property_exists($data, 'm_grid_id'))
		{
			$m_grid = new M_grid($data->m_grid_id);
			$assemblytarget_relations['m_grid'] = $m_grid;
			unset($data->m_grid_id);
		}
		if (!$m_grid->exists())
		{
			$m_grid
				->where('code', $this->CI->config->item('inventory_default_grid'))
				->get();
		}
		if (property_exists($data, 'quantity_box'))
		{
			$quantity_box = $data->quantity_box;
		}
		if (property_exists($data, 'quantity'))
		{
			$quantity = $data->quantity;
		}
		
		if ($quantity_box < 0)
			throw new Exception("Invalid quantity box.");
		if ($quantity < 0)
			throw new Exception("Invalid quantity.");
		
		// -- Validate the project --
		$project_is_valid = $this->CI->lib_custom->project_is_valid($created_by, $c_project->id);
		if (!$project_is_valid)
			throw new Exception("Access denied for the project.");
		
		// -- Inventory Add --
		$data_m_inventory = $data;
		$data_m_inventory->m_product_id = $m_product->id;
		$data_m_inventory->m_grid_id = $m_grid->id;
		$data_m_inventory->c_project_id = $c_project->id;
		$data_m_inventory->received_date = $m_inventory_assembly->assembly_date;
		
		$data_m_inventory_log = new stdClass();
		$data_m_inventory_log->log_type = "ASSEMBLY";
		$data_m_inventory_log->ref1_code = $m_inventory_assembly->code;
		$data_m_inventory_log->notes = 'Add Assembly Target';
		
		$m_inventory_id = $this->CI->lib_inventory->add($data_m_inventory, $created_by, $data_m_inventory_log);
		
		// -- Assembly Target Add --
		$m_inventory = new M_inventory($m_inventory_id);
		$assemblytarget_relations['m_inventory'] = $m_inventory;
		
		$data->created_by = $created_by;
		
		$m_inventory_assemblytarget = new M_inventory_assemblytarget();
		$this->set_model_fields_values($m_inventory_assemblytarget, $data);
		$m_inventory_assemblytarget_saved = $m_inventory_assemblytarget->save($assemblytarget_relations);
		if (!$m_inventory_assemblytarget_saved)
			throw new Exception($m_inventory_assemblytarget->error->string);
		
		$this->CI->lib_inventory->verify_pallet_grid($m_inventory->pallet, $m_grid->id);
		
		return $m_inventory_assemblytarget->id;
	}
	
	public function assemblytarget_remove($m_inventory_assemblytarget_id, $removed_by = NULL)
	{
		$m_inventory_assemblytarget = new M_inventory_assemblytarget($m_inventory_assemblytarget_id);
		$m_inventory_assembly = $m_inventory_assemblytarget->m_inventory_assembly->get();
		$m_inventory = $m_inventory_assemblytarget->m_inventory->get();
		
		// -- Validate the project --
		$c_project = $m_inventory->c_project->get();
		$project_is_valid = $this->CI->lib_custom->project_is_valid($removed_by, $c_project->id);
		if (!$project_is_valid)
			throw new Exception("Access denied for the project.");
		
		// -- Inbound Detail Delete --
		if (!$m_inventory_assemblytarget->delete())
			throw new Exception($m_inventory_assemblytarget->error->string);
		
		// -- Inventory Delete --
		$data_m_inventory_log = new stdClass();
		$data_m_inventory_log->log_type = "ASSEMBLY";
		$data_m_inventory_log->ref1_code = $m_inventory_assembly->code;
		$data_m_inventory_log->notes = 'Remove Assembly Target';
		
		$m_inventory_id = $this->CI->lib_inventory->remove($m_inventory->id, $removed_by, $data_m_inventory_log);
		
		return $m_inventory_assemblytarget_id;
	}
	
	/* -------------------- */
	/* -- UTILITY REGION -- */
	/* -------------------- */
	
	private function verify_last_of_operation($m_inventory_id, $table_name, $id)
	{
		$created = strtotime($this->get_created_of_table($table_name, $id));
		
		$putaway_last = $this->get_last_table_of_inventory('m_inventory_putawaydetails', $m_inventory_id);
		if (	($table_name == 'm_inventory_putawaydetails' && $putaway_last['id'] > $id)
			||	($putaway_last['created'] !== NULL && strtotime($putaway_last['created']) > $created))
		{
			$table = $this->CI->db
				->select("ipd.barcode m_inventory_putawaydetail_barcode")
				->select("ia.code m_inventory_putaway_code, ia.putaway_date m_inventory_putaway_date")
				->select("pro.code m_product_code, pro.name m_product_name")
				->from('m_inventory_putawaydetails ipd')
				->join('m_inventory_putaways ia', "ia.id = ipd.m_inventory_putaway_id")
				->join('m_products pro', "pro.id = ipd.m_product_id")
				->where('ipd.id', $putaway_last['id'])
				->get();
			if ($table->num_rows() > 0)
			{
				$record = $table->first_row();
				throw new Exception(
					 "Product #". $record->m_product_code ." '". $record->m_product_name ."'"
					." with barcode '". $record->m_inventory_putawaydetail_barcode ."'"
					." had already exist in newer putaway process"
					." #". $record->m_inventory_putaway_code.".");
			}
			else
			{
				throw new Exception("Already exist in newer putaway process.");
			}
		}
		
		$adjustment_last = $this->get_last_table_of_inventory('m_inventory_adjustdetails', $m_inventory_id);
		if (	($table_name == 'm_inventory_adjustdetails' && $adjustment_last['id'] > $id)
			||	($adjustment_last['created'] !== NULL && strtotime($adjustment_last['created']) > $created))
		{
			$table = $this->CI->db
				->select("iad.barcode m_inventory_adjustdetail_barcode")
				->select("ia.code m_inventory_adjust_code, ia.adjust_date m_inventory_adjust_date")
				->select("pro.code m_product_code, pro.name m_product_name")
				->from('m_inventory_adjustdetails iad')
				->join('m_inventory_adjusts ia', "ia.id = iad.m_inventory_adjust_id")
				->join('m_products pro', "pro.id = iad.m_product_id")
				->where('iad.id', $adjustment_last['id'])
				->get();
			if ($table->num_rows() > 0)
			{
				$record = $table->first_row();
				throw new Exception(
					 "Product #". $record->m_product_code ." '". $record->m_product_name ."'"
					." with barcode '". $record->m_inventory_adjustdetail_barcode ."'"
					." had already exist in newer adjust process"
					." #". $record->m_inventory_adjust_code.".");
			}
			else
			{
				throw new Exception("Already exist in newer adjust process.");
			}
		}
		
		$move_last = $this->get_last_table_of_inventory('m_inventory_movedetails', $m_inventory_id);
		if (	($table_name == 'm_inventory_movedetails' && $move_last['id'] > $id)
			||	($move_last['created'] !== NULL && strtotime($move_last['created']) > $created))
		{
			$table = $this->CI->db
				->select("imd.barcode m_inventory_movedetail_barcode")
				->select("im.code m_inventory_move_code, im.move_date m_inventory_move_date")
				->select("pro.code m_product_code, pro.name m_product_name")
				->from('m_inventory_movedetails imd')
				->join('m_inventory_moves im', "im.id = imd.m_inventory_move_id")
				->join('m_products pro', "pro.id = imd.m_product_id")
				->where('imd.id', $move_last['id'])
				->get();
			if ($table->num_rows() > 0)
			{
				$record = $table->first_row();
				throw new Exception(
					 "Product #" . $record->m_product_code . " '" . $record->m_product_name ."'"
					." with barcode '". $record->m_inventory_movedetail_barcode ."'"
					." had already exist in newer move process"
					." #".$record->m_inventory_move_code.".");
			}
			else
			{
				throw new Exception("Already exist in newer move process.");
			}
		}
		
		$hold_last = $this->get_last_table_of_inventory('m_inventory_holddetails', $m_inventory_id);
		if (	($table_name == 'm_inventory_holddetails' && $hold_last['id'] > $id)
			||	($hold_last['created'] !== NULL && strtotime($hold_last['created']) > $created))
		{
			$table = $this->CI->db
				->select("ihd.barcode m_inventory_holddetail_barcode")
				->select("ih.code m_inventory_hold_code, ih.hold_date m_inventory_hold_date")
				->select("pro.code m_product_code, pro.name m_product_name")
				->from('m_inventory_holddetails ihd')
				->join('m_inventory_holds ih', "ih.id = ihd.m_inventory_hold_id")
				->join('m_products pro', "pro.id = ihd.m_product_id")
				->where('ihd.id', $hold_last['id'])
				->get();
			if ($table->num_rows() > 0)
			{
				$record = $table->first_row();
				throw new Exception(
					 "Product #" . $record->m_product_code . " '" . $record->m_product_name ."'"
					." with barcode '". $record->m_inventory_holddetail_barcode ."'"
					." had already exist in newer hold process"
					." #".$record->m_inventory_hold_code.".");
			}
			else
			{
				throw new Exception("Already exist in newer hold process.");
			}
		}
		
		$assembly_source_last = $this->get_last_table_of_inventory('m_inventory_assemblysources', $m_inventory_id);
		if (	($table_name == 'm_inventory_assemblysources' && $assembly_source_last['id'] > $id)
			||	($assembly_source_last['created'] !== NULL && strtotime($assembly_source_last['created']) > $created))
		{
			$table = $this->CI->db
				->select("ias.barcode m_inventory_assembly_source_barcode")
				->select("ia.code m_inventory_assembly_code, ia.assembly_date m_inventory_assembly_date")
				->select("pro.code m_product_code, pro.name m_product_name")
				->from('m_inventory_assemblysources ias')
				->join('m_inventory_assemblies ia', "ia.id = ias.m_inventory_assembly_id")
				->join('m_products pro', "pro.id = ias.m_product_id")
				->where('ias.id', $assembly_source_last['id'])
				->get();
			if ($table->num_rows() > 0)
			{
				$record = $table->first_row();
				throw new Exception(
					 "Product #" . $record->m_product_code . " '" . $record->m_product_name ."'"
					." with barcode '". $record->m_inventory_assembly_source_barcode ."'"
					." had already exist in newer assembly source process"
					." #".$record->m_inventory_assembly_code.".");
			}
			else
			{
				throw new Exception("Already exist in newer assembly source process.");
			}
		}
		
		$assembly_target_last = $this->get_last_table_of_inventory('m_inventory_assemblytargets', $m_inventory_id);
		if (	($table_name == 'm_inventory_assemblytargets' && $assembly_target_last['id'] > $id)
			||	($assembly_target_last['created'] !== NULL && strtotime($assembly_target_last['created']) > $created))
		{
			$table = $this->CI->db
				->select("iat.barcode m_inventory_assembly_target_barcode")
				->select("ia.code m_inventory_assembly_code, ia.assembly_date m_inventory_assembly_date")
				->select("pro.code m_product_code, pro.name m_product_name")
				->from('m_inventory_assemblytargets iat')
				->join('m_inventory_assemblies ia', "ia.id = iat.m_inventory_assembly_id")
				->join('m_products pro', "pro.id = iat.m_product_id")
				->where('iat.id', $assembly_target_last['id'])
				->get();
			if ($table->num_rows() > 0)
			{
				$record = $table->first_row();
				throw new Exception(
					 "Product #" . $record->m_product_code . " '" . $record->m_product_name ."'"
					." with barcode '". $record->m_inventory_assembly_target_barcode ."'"
					." had already exist in newer assembly target process"
					." #".$record->m_inventory_assembly_code.".");
			}
			else
			{
				throw new Exception("Already exist in newer assembly target process.");
			}
		}
	}
	
	private function get_last_table_of_inventory($table_name, $m_inventory_id)
	{
		$data = array(
			'id'		=> NULL,
			'created'	=> NULL
		);
		
		$table = $this->CI->db
			->select_max('id')
			->from($table_name)
			->where('m_inventory_id', $m_inventory_id)
			->get();
		if ($table->num_rows() > 0)
		{
			$record = $table->first_row();
			$data['id'] = $record->id;
			
			$table = $this->CI->db
				->select('created')
				->from($table_name)
				->where('id', $data['id'])
				->get();
			if ($table->num_rows() > 0)
			{
				$record = $table->first_row();
				$data['created'] = $record->created;
			}
		}
		
		return $data;
	}
	
	private function get_created_of_table($table_name, $id)
	{
		$created = NULL;
		
		$table = $this->CI->db
			->select('created')
			->from($table_name)
			->where('id', $id)
			->get();
		if ($table->num_rows() > 0)
		{
			$record = $table->first_row();
			$created = $record->created;
		}
		
		return $created;
	}
}