<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
exist (inventory quantity)
allocated (picklistdetails quantity)
picked (pickingdetails quantity)
onhand (exist + allocated + picked)
*/
class Lib_inventory extends Lib_general
{
	protected $CI;
	
	function __construct()
    {
		$this->CI =& get_instance();
		
		$this->CI->load->library('custom/lib_custom_inventory');
	}
	
	/* ---------------------- */
	/* -- INVENTORY REGION -- */
	/* ---------------------- */
	
	public function add($data, $created_by = NULL, $log = NULL)
	{
		$m_product = new M_product();
		$m_grid = new M_grid();
		$c_project = new C_project();
		$quantity_per_box = 0;
		$quantity_box = 0;
		$quantity_box_allocated = 0;
		$quantity_box_picked = 0;
		$quantity = 0;
		$quantity_allocated = 0;
		$quantity_picked = 0;
		$pallet = NULL;
		
		$inventory_relations = array();
		if (property_exists($data, 'm_product_id'))
		{
			$m_product = new M_product($data->m_product_id);
			$inventory_relations['m_product'] = $m_product;
			unset($data->m_product_id);
		}
		if (property_exists($data, 'm_grid_id'))
		{
			$m_grid = new M_grid($data->m_grid_id);
			$inventory_relations['m_grid'] = $m_grid;
			unset($data->m_grid_id);
		}
		if (property_exists($data, 'c_project_id'))
		{
			$c_project = new C_project($data->c_project_id);
			$inventory_relations['c_project'] = $c_project;
			unset($data->c_project_id);
		}
		if (property_exists($data, 'quantity_per_box'))
		{
			$quantity_per_box = $data->quantity_per_box;
		}
		else
		{
			$quantity_per_box = $m_product->netto;
		}
		if (property_exists($data, 'quantity_box'))
		{
			$quantity_box = $data->quantity_box;
		}
		if (property_exists($data, 'quantity_box_allocated'))
		{
			$quantity_box_allocated = $data->quantity_box_allocated;
		}
		if (property_exists($data, 'quantity_box_picked'))
		{
			$quantity_box_picked = $data->quantity_box_picked;
		}
		if (property_exists($data, 'quantity'))
		{
			$quantity = $data->quantity;
		}
		if (property_exists($data, 'quantity_allocated'))
		{
			$quantity_allocated = $data->quantity_allocated;
		}
		if (property_exists($data, 'quantity_picked'))
		{
			$quantity_picked = $data->quantity_picked;
		}
		if (property_exists($data, 'pallet'))
		{
			$pallet = $data->pallet;
		}
		
		$data->quantity_per_box = $quantity_per_box;
		$data->quantity_box_onhand = $quantity_box + $quantity_box_allocated + $quantity_box_picked;
		$data->quantity_onhand = $quantity + $quantity_allocated + $quantity_picked;
		$data->created_by = $created_by;
		
		// -- Validate standard product --
		if ($data->quantity_per_box > 0 && $data->quantity_box_onhand > 0 && $data->quantity_per_box != ($data->quantity_onhand / $data->quantity_box_onhand))
			throw new Exception("Failed onhand quantity for standard product. Set the onhand quantity to '". ($data->quantity_per_box * $data->quantity_box_onhand) ."' for matching with netto of product.");
		
		$m_inventory = new M_inventory();
		$this->set_model_fields_values($m_inventory, $data);
		$m_inventory_saved = $m_inventory->save($inventory_relations);
		if (!$m_inventory_saved)
			throw new Exception($m_inventory->error->string);
		
		// -- Add quantity to inventory grid usage --
		$data_m_grid_usage = new stdClass();
		$data_m_grid_usage->quantity = $quantity;
		$data_m_grid_usage->quantity_box = $quantity_box;
		$data_m_grid_usage->quantity_allocated = $quantity_allocated;
		$data_m_grid_usage->quantity_box_allocated = $quantity_box_allocated;
		$data_m_grid_usage->quantity_picked = $quantity_picked;
		$data_m_grid_usage->quantity_box_picked = $quantity_box_picked;
		$this->CI->lib_custom_inventory->grid_usage_add_quantity_by_grid($m_grid->id, $data_m_grid_usage, $created_by);
		
		// -- Create inventory log --
		$log_data = new stdClass();
		if (!empty($log))
		{
			if (is_object($log))
			{
				$log_data = $log;
				if (!empty($log->class_name) && !empty($log->id_value))
					$log_data->notes = $this->_log_notes($log->class_name, $log->id_value, (!empty($log->unique_data_field) ? $log->unique_data_field : 'code'), (!empty($log->note) ? $log->note : NULL) );
			}
			else
				$log_data->notes = $log;
		}
		$this->add_log(
			$m_inventory->id, 
			$quantity_box, $quantity, 
			$quantity_box_allocated, $quantity_allocated, 
			$quantity_box_picked, $quantity_picked, 
			$log_data, $created_by
		);
		
		return $m_inventory->id;
	}
	
	public function update($m_inventory_id, $data, $updated_by = NULL, $log = NULL)
	{
		$m_inventory = new M_inventory($m_inventory_id);
		$m_product = $m_inventory->m_product->get();
		
		$m_grid_old = $m_inventory->m_grid->get();
		$m_grid = $m_inventory->m_grid->get();
		
		$c_project = $m_inventory->c_project->get();
		
		$quantity_per_box_old = $m_inventory->quantity_per_box;
		
		$quantity_box_old = $m_inventory->quantity_box;
		$quantity_box_allocated_old = $m_inventory->quantity_box_allocated;
		$quantity_box_picked_old = $m_inventory->quantity_box_picked;
		
		$quantity_old = $m_inventory->quantity;
		$quantity_allocated_old = $m_inventory->quantity_allocated;
		$quantity_picked_old = $m_inventory->quantity_picked;
		
		$quantity_per_box = $m_inventory->quantity_per_box;
		
		$quantity_box = $m_inventory->quantity_box;
		$quantity_box_allocated = $m_inventory->quantity_box_allocated;
		$quantity_box_picked = $m_inventory->quantity_box_picked;
		
		$quantity = $m_inventory->quantity;
		$quantity_allocated = $m_inventory->quantity_allocated;
		$quantity_picked = $m_inventory->quantity_picked;
		
		$pallet = $m_inventory->pallet;
		
		$inventory_relations = array();
		if (property_exists($data, 'm_product_id'))
		{
			$m_product = new M_product($data->m_product_id);
			$inventory_relations['m_product'] = $m_product;
			unset($data->m_product_id);
		}
		if (property_exists($data, 'm_grid_id'))
		{
			$m_grid = new M_grid($data->m_grid_id);
			$inventory_relations['m_grid'] = $m_grid;
			unset($data->m_grid_id);
		}
		if (property_exists($data, 'c_project_id'))
		{
			$c_project = new C_project($data->c_project_id);
			$inventory_relations['c_project'] = $c_project;
			unset($data->c_project_id);
		}
		if (property_exists($data, 'pallet'))
		{
			$pallet = $data->pallet;
		}
		if (property_exists($data, 'quantity_per_box'))
		{
			$quantity_per_box = $data->quantity_per_box;
		}
		if (property_exists($data, 'quantity_box'))
		{
			$quantity_box = $data->quantity_box;
		}
		if (property_exists($data, 'quantity_box_allocated'))
		{
			$quantity_box_allocated = $data->quantity_box_allocated;
		}
		if (property_exists($data, 'quantity_box_picked'))
		{
			$quantity_box_picked = $data->quantity_box_picked;
		}
		if (property_exists($data, 'quantity'))
		{
			$quantity = $data->quantity;
		}
		if (property_exists($data, 'quantity_allocated'))
		{
			$quantity_allocated = $data->quantity_allocated;
		}
		if (property_exists($data, 'quantity_picked'))
		{
			$quantity_picked = $data->quantity_picked;
		}
		
		// Quantity box change
		$quantity_box_changed = 0;
		if ($m_inventory->quantity_box > $quantity_box)
			$quantity_box_changed = ($m_inventory->quantity_box - $quantity_box) * -1;
		elseif ($quantity_box > $m_inventory->quantity_box)
			$quantity_box_changed = $quantity_box - $m_inventory->quantity_box;
		
		// Quantity box allocated change
		$quantity_box_allocated_changed = 0;
		if ($m_inventory->quantity_box_allocated > $quantity_box_allocated)
			$quantity_box_allocated_changed = ($m_inventory->quantity_box_allocated - $quantity_box_allocated) * -1;
		elseif ($quantity_box_allocated > $m_inventory->quantity_box_allocated)
			$quantity_box_allocated_changed = $quantity_box_allocated - $m_inventory->quantity_box_allocated;
		
		// Quantity box picked change
		$quantity_box_picked_changed = 0;
		if ($m_inventory->quantity_box_picked > $quantity_box_picked)
			$quantity_box_picked_changed = ($m_inventory->quantity_box_picked - $quantity_box_picked) * -1;
		elseif ($quantity_box_picked > $m_inventory->quantity_box_picked)
			$quantity_box_picked_changed = $quantity_box_picked - $m_inventory->quantity_box_picked;
		
		// Quantity change
		$quantity_changed = 0;
		if ($m_inventory->quantity > $quantity)
			$quantity_changed = ($m_inventory->quantity - $quantity) * -1;
		elseif ($quantity > $m_inventory->quantity)
			$quantity_changed = $quantity - $m_inventory->quantity;
		
		// Quantity allocated change
		$quantity_allocated_changed = 0;
		if ($m_inventory->quantity_allocated > $quantity_allocated)
			$quantity_allocated_changed = ($m_inventory->quantity_allocated - $quantity_allocated) * -1;
		elseif ($quantity_allocated > $m_inventory->quantity_allocated)
			$quantity_allocated_changed = $quantity_allocated - $m_inventory->quantity_allocated;
		
		// Quantity picked change
		$quantity_picked_changed = 0;
		if ($m_inventory->quantity_picked > $quantity_picked)
			$quantity_picked_changed = ($m_inventory->quantity_picked - $quantity_picked) * -1;
		elseif ($quantity_picked > $m_inventory->quantity_picked)
			$quantity_picked_changed = $quantity_picked - $m_inventory->quantity_picked;
		
		$data->quantity_per_box = $quantity_per_box;
		$data->quantity_box_onhand = $quantity_box + $quantity_box_allocated + $quantity_box_picked;
		$data->quantity_onhand = $quantity + $quantity_allocated + $quantity_picked;
		$data->updated_by = $updated_by;
		
		// -- Validate standard product --
		if ($data->quantity_per_box > 0 && $data->quantity_box_onhand > 0 && $data->quantity_per_box != ($data->quantity_onhand / $data->quantity_box_onhand))
			throw new Exception("Failed onhand quantity for standard product. Set the onhand quantity to '". ($data->quantity_per_box * $data->quantity_box_onhand) ."' for matching with netto of product.");
		
		$this->set_model_fields_values($m_inventory, $data);
		$m_inventory_saved = $m_inventory->save($inventory_relations);
		if (!$m_inventory_saved)
			throw new Exception($m_inventory->error->string);
		
		// -- Change inventory grid usage --
		if ($m_grid_old->id != $m_grid->id)
		{
			$m_inventory_new = new M_inventory($m_inventory_id);
			
			// -- Decrease on old grid --
			$data_m_grid_usage = new stdClass();
			$data_m_grid_usage->quantity_box = $quantity_box_old * -1;
			$data_m_grid_usage->quantity_box_allocated = $quantity_box_allocated_old * -1;
			$data_m_grid_usage->quantity_box_picked = $quantity_box_picked_old * -1;
			$data_m_grid_usage->quantity = $quantity_old * -1;
			$data_m_grid_usage->quantity_allocated = $quantity_allocated_old * -1;
			$data_m_grid_usage->quantity_picked = $quantity_picked_old * -1;
			$this->CI->lib_custom_inventory->grid_usage_add_quantity_by_grid($m_grid_old->id, $data_m_grid_usage, $updated_by);
			
			// -- Increase on new grid --
			$data_m_grid_usage = new stdClass();
			$data_m_grid_usage->quantity_box = $quantity_box;
			$data_m_grid_usage->quantity_box_allocated = $quantity_box_allocated;
			$data_m_grid_usage->quantity_box_picked = $quantity_box_picked;
			$data_m_grid_usage->quantity = $quantity;
			$data_m_grid_usage->quantity_allocated = $quantity_allocated;
			$data_m_grid_usage->quantity_picked = $quantity_picked;
			$this->CI->lib_custom_inventory->grid_usage_add_quantity_by_grid($m_grid->id, $data_m_grid_usage, $updated_by);
		}
		else
		{
			$data_m_grid_usage = new stdClass();
			$data_m_grid_usage->quantity_box = $quantity_box_changed;
			$data_m_grid_usage->quantity_box_allocated = $quantity_box_allocated_changed;
			$data_m_grid_usage->quantity_box_picked = $quantity_box_picked_changed;
			$data_m_grid_usage->quantity = $quantity_changed;
			$data_m_grid_usage->quantity_allocated = $quantity_allocated_changed;
			$data_m_grid_usage->quantity_picked = $quantity_picked_changed;
			$this->CI->lib_custom_inventory->grid_usage_add_quantity_by_grid($m_grid_old->id, $data_m_grid_usage, $updated_by);
		}
		
		// -- Create inventory log --
		$log_data = new stdClass();
		if (!empty($log))
		{
			if (is_object($log))
			{
				$log_data = $log;
				if (!empty($log->class_name) && !empty($log->id_value))
					$log_data->notes = $this->_log_notes($log->class_name, $log->id_value, (!empty($log->unique_data_field) ? $log->unique_data_field : 'code'), (!empty($log->note) ? $log->note : NULL) );
			}
			else
				$log_data->notes = $log;
		}
		$this->add_log(
			$m_inventory->id, 
			$quantity_box_changed, $quantity_changed, 
			$quantity_box_allocated_changed, $quantity_allocated_changed, 
			$quantity_box_picked_changed, $quantity_picked_changed, 
			$log_data, $updated_by
		);
		
		return $m_inventory->id;
	}
	
	public function remove($m_inventory_id, $removed_by = NULL, $log = NULL)
	{
		$m_inventory = new M_inventory($m_inventory_id);
		$m_grid = $m_inventory->m_grid->get();
		
		// -- Add quantity to inventory grid usage --
		$data_m_grid_usage = new stdClass();
		$data_m_grid_usage->quantity = $m_inventory->quantity * -1;
		$data_m_grid_usage->quantity_box = $m_inventory->quantity_box * -1;
		$data_m_grid_usage->quantity_allocated = $m_inventory->quantity_allocated * -1;
		$data_m_grid_usage->quantity_box_allocated = $m_inventory->quantity_box_allocated * -1;
		$data_m_grid_usage->quantity_picked = $m_inventory->quantity_picked * -1;
		$data_m_grid_usage->quantity_box_picked = $m_inventory->quantity_box_picked * -1;
		$this->CI->lib_custom_inventory->grid_usage_add_quantity_by_grid($m_grid->id, $data_m_grid_usage, $removed_by);
		
		// -- Create inventory log --
		$log_data = new stdClass();
		if (!empty($log))
		{
			if (is_object($log))
			{
				$log_data = $log;
				if (!empty($log->class_name) && !empty($log->id_value))
					$log_data->notes = $this->_log_notes($log->class_name, $log->id_value, (!empty($log->unique_data_field) ? $log->unique_data_field : 'code'), (!empty($log->note) ? $log->note : NULL) );
			}
			else
				$log_data->notes = $log;
		}
		$this->add_log(
			$m_inventory->id, 
			$m_inventory->quantity_box * -1, $m_inventory->quantity * -1, 
			$m_inventory->quantity_box_allocated * -1, $m_inventory->quantity_allocated * -1, 
			$m_inventory->quantity_box_picked * -1, $m_inventory->quantity_picked * -1, 
			$log_data, $removed_by
		);
		
		// -- Remove inventory --
		if (!$m_inventory->delete())
			throw new Exception($m_inventory->error->string);
		
		return $m_inventory_id;
	}
	
	public function move($m_inventory_id, $m_grid_id, $data, $quantity_box = NULL, $updated_by = NULL, $log = NULL)
	{
		$move_results = array();
		
		$m_inventory = new M_inventory($m_inventory_id);
		$m_gridfrom = $m_inventory->m_grid->get();
		
		// -- Move All Quantity Box --
		if ($quantity_box == NULL || $m_inventory->quantity_box == $quantity_box)
		{
			if ($quantity_box == NULL)
				$quantity_box = $m_inventory->quantity_box;
			
			$data->m_grid_id = $m_grid_id;
			$data->quantity_box = $quantity_box;
			$m_inventory_id_new = $this->update($m_inventory_id, $data, $updated_by, $log);
			
			$m_inventory_new = new M_inventory($m_inventory_id_new);
			$move_results[] = array(
				'm_inventory_id'	=> $m_inventory_id_new,
				'm_gridfrom_id'		=> $m_gridfrom->id,
				'm_gridto_id'		=> $m_grid_id,
				'quantity_from'		=> $m_inventory_new->quantity,
				'quantity_to'		=> $m_inventory_new->quantity,
				'quantity_box_from'	=> $quantity_box,
				'quantity_box_to'	=> $quantity_box,
				'type'				=> 'move'
			);
		}
		// -- Move Partial Quantity Box --
		elseif ($m_inventory->quantity_box > $quantity_box)
		{
			$m_product = $m_inventory->m_product->get();
			$c_project = $m_inventory->c_project->get();
			
			if ($m_inventory->quantity_per_box > 0 && $m_inventory->quantity_box > 0 && $m_inventory->quantity_per_box == ($m_inventory->quantity / $m_inventory->quantity_box))
				$quantity = $m_inventory->quantity_per_box * $quantity_box;
			else
			{
				if ($m_inventory->quantity_box > 1 && $m_inventory->quantity_box > 0)
					$quantity = ($m_inventory->quantity / $m_inventory->quantity_box) * $quantity_box;
				else
					$quantity = $m_inventory->quantity;
			}
			
			$data->m_product_id = $m_product->id;
			$data->c_project_id = $c_project->id;
			$data->received_date = $m_inventory->received_date;
			$data->m_grid_id = $m_grid_id;
			$data->quantity_per_box = $m_inventory->quantity_box;
			$data->quantity = $quantity;
			$data->quantity_box = $quantity_box;
			$m_inventory_id_new = $this->add($data, $updated_by, $log);
			
			$m_inventory_new = new M_inventory($m_inventory_id_new);
			$move_results[] = array(
				'm_inventory_id'	=> $m_inventory_id_new,
				'm_gridfrom_id'		=> $m_gridfrom->id,
				'm_gridto_id'		=> $m_grid_id,
				'quantity_from'		=> 0,
				'quantity_to'		=> $quantity,
				'quantity_box_from'	=> 0,
				'quantity_box_to'	=> $quantity_box,
				'type'				=> 'move'
			);
			
			$quantity_box_new = $m_inventory_new->quantity_box - $quantity_box;
			$quantity_new = $m_inventory_new->quantity - $quantity;
			$m_inventory_id_new = $this->adjust($m_inventory_id, $quantity_box_new, $quantity_new, $updated_by, $log);
			
			$move_results[] = array(
				'm_inventory_id'	=> $m_inventory_id_new,
				'm_gridfrom_id'		=> $m_gridfrom->id,
				'm_gridto_id'		=> $m_gridfrom->id,
				'quantity_from'		=> $m_inventory->quantity,
				'quantity_to'		=> $quantity_new,
				'quantity_box_from'	=> $m_inventory->quantity_box,
				'quantity_box_to'	=> $quantity_box_new,
				'type'				=> 'adjust'
			);
		}
		// -- Failed Quantity --
		else
		{
			throw new Exception("Failed move quantity box. New quantity box can't more than old quantity box.");
		}
		
		return $move_results;
	}
	
	public function adjust($m_inventory_id, $quantity_box_new, $quantity_new, $updated_by = NULL, $log = NULL)
	{
		// -- Override and calculate the new quantity_box, if standard product in inventory --
		$m_inventory = new M_inventory($m_inventory_id);
		if (	$quantity_new > 0
			&&	$quantity_box_new == $m_inventory->quantity_box && $quantity_new != $m_inventory->quantity
			&&	$m_inventory->quantity_per_box > 0)
		{
			$quantity_box_new = ceil($quantity_new / $m_inventory->quantity_per_box);
		}
		
		$data = new stdClass();
		$data->quantity_box = $quantity_box_new;
		if ($data->quantity_box == 0)
			$data->quantity = 0;
		else
			$data->quantity = $quantity_new;
		if ($data->quantity == 0)
			$data->quantity_box = 0;
		return $this->update($m_inventory_id, $data, $updated_by, $log);
	}
	
	public function allocate($m_inventory_id, $quantity_box, $quantity, $updated_by = NULL, $log = NULL)
	{
		$m_inventory = new M_inventory($m_inventory_id);
		
		$data = new stdClass();
		$data->quantity_box = $m_inventory->quantity_box - $quantity_box;
		$data->quantity = $m_inventory->quantity - $quantity;
		$data->quantity_box_allocated = $m_inventory->quantity_box_allocated + $quantity_box;
		$data->quantity_allocated = $m_inventory->quantity_allocated + $quantity;
		return $this->update($m_inventory_id, $data, $updated_by, $log);
	}
	
	public function pick($m_inventory_id, $quantity_box, $quantity, $updated_by = NULL, $log = NULL)
	{
		$m_inventory = new M_inventory($m_inventory_id);
		
		$data = new stdClass();
		$data->quantity_box_allocated = $m_inventory->quantity_box_allocated - $quantity_box;
		$data->quantity_allocated = $m_inventory->quantity_allocated - $quantity;
		$data->quantity_box_picked = $m_inventory->quantity_box_picked + $quantity_box;
		$data->quantity_picked = $m_inventory->quantity_picked + $quantity;
		return $this->update($m_inventory_id, $data, $updated_by, $log);
	}
	
	public function ship($m_inventory_id, $quantity_box, $quantity, $updated_by = NULL, $log = NULL)
	{
		$m_inventory = new M_inventory($m_inventory_id);
		
		$data = new stdClass();
		$data->quantity_box_picked = $m_inventory->quantity_box_picked - $quantity_box;
		$data->quantity_picked = $m_inventory->quantity_picked - $quantity;
		return $this->update($m_inventory_id, $data, $updated_by, $log);
	}
	
	public function verify_pallet_grid($pallet, $m_grid_id)
	{
		// -- Pallet can't placed in multiple grid --
		$this->CI->db
			->select('inv.m_grid_id, gri.code m_grid_code')
			->from('m_inventories inv')
			->join('m_grids gri', "gri.id = inv.m_grid_id");
		if ($pallet === NULL)
			$this->CI->db
				->where('inv.pallet IS NULL', NULL, FALSE);
		else
			$this->CI->db
				->where('inv.pallet', $pallet);
		$table = $this->CI->db
			->where('inv.quantity_box >', 0)
			->where('inv.m_grid_id <>', $m_grid_id)
			->group_by(
				array(
					'inv.m_grid_id', 'gri.code'
				)
			)
			->limit(1)
			->get();
		if ($table->num_rows() > 0)
		{
			$table_record = $table->first_row();
			throw new Exception("Pallet ".$pallet." already placed in grid ". $table_record->m_grid_code .".");
		}
	}
	
	/* -------------------------- */
	/* -- INVENTORY LOG REGION -- */
	/* -------------------------- */
	protected function add_log(
		$m_inventory_id, 
		$quantity_box_changed, $quantity_changed, 
		$quantity_box_allocated_changed, $quantity_allocated_changed, 
		$quantity_box_picked_changed, $quantity_picked_changed, 
		$data = NULL, $created_by = NULL
	)
	{
		if (   $quantity_box_changed == 0 && $quantity_changed == 0
			&& $quantity_box_allocated_changed == 0 && $quantity_allocated_changed == 0
			&& $quantity_box_picked_changed == 0 && $quantity_picked_changed == 0)
			return FALSE;
		
		$m_inventory = new M_inventory($m_inventory_id);
		$m_product = $m_inventory->m_product->get();
		$m_grid = $m_inventory->m_grid->get();
		$c_project = $m_inventory->c_project->get();
		
		if ($data == NULL)
			$data = new stdClass();
		$data->quantity_box = $quantity_box_changed;
		$data->quantity_box_allocated = $quantity_box_allocated_changed;
		$data->quantity_box_picked = $quantity_box_picked_changed;
		$data->quantity_box_onhand = $data->quantity_box + $data->quantity_box_allocated + $data->quantity_box_picked;
		$data->quantity = $quantity_changed;
		$data->quantity_allocated = $quantity_allocated_changed;
		$data->quantity_picked = $quantity_picked_changed;
		$data->quantity_onhand = $data->quantity + $data->quantity_allocated + $data->quantity_picked;
		$data->barcode = $m_inventory->barcode;
		$data->pallet = $m_inventory->pallet;
		$data->condition = $m_inventory->condition;
		$data->created_by = $created_by;
		
		$m_inventorylog = new M_inventorylog();
		$this->set_model_fields_values($m_inventorylog, $data);
		$m_inventorylog_saved = $m_inventorylog->save(
			array(
				'm_inventory'	=> $m_inventory,
				'm_product'		=> $m_product,
				'm_grid'		=> $m_grid,
				'c_project'		=> $c_project
			)
		);
		if (!$m_inventorylog_saved)
			throw new Exception($m_inventorylog->error->string);
		
		return $m_inventorylog->id;
	}
	
	protected function _log_notes($class_name, $id_value, $unique_data_field = 'code', $note = '')
	{
		$object = new $class_name($id_value);
		
		$notes = $class_name." ".$object->$unique_data_field." ".$note;
		return $notes;
	}
}