<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class lib_inventory_out extends Lib_general
{
	protected $CI;
	
	function __construct()
    {
		$this->CI =& get_instance();
		
		$this->CI->load->library('material/lib_inventory');
		$this->CI->load->library('custom/lib_custom');
	}
	
	/* ---------------------- */
	/* -- PICK LIST REGION -- */
	/* ---------------------- */
	
	public function picklist_add($data, $created_by = NULL)
	{
		$data->created_by = $created_by;
		
		$m_inventory_picklist = new M_inventory_picklist();
		$this->set_model_fields_values($m_inventory_picklist, $data);
		$m_inventory_picklist_saved = $m_inventory_picklist->save();
		if (!$m_inventory_picklist_saved)
			throw new Exception($m_inventory_picklist->error->string);
		
		// -- Update Status --
		$this->picklist_generate_status_inv_picking($m_inventory_picklist->id, $created_by);
		
		return $m_inventory_picklist->id;
	}
	
	public function picklist_update($m_inventory_picklist_id, $data, $updated_by = NULL)
	{
		$data->updated_by = $updated_by;
		
		$m_inventory_picklist = new M_inventory_picklist($m_inventory_picklist_id);
		$this->set_model_fields_values($m_inventory_picklist, $data);
		$m_inventory_picklist_saved = $m_inventory_picklist->save();
		if (!$m_inventory_picklist_saved)
			throw new Exception($m_inventory_picklist->error->string);
		
		// -- Update Status --
		$this->picklist_generate_status_inv_picking($m_inventory_picklist->id, $updated_by);
		
		return $m_inventory_picklist_id;
	}
	
	public function picklist_remove($m_inventory_picklist_id, $removed_by = NULL)
	{
		$m_inventory_picklist = new M_inventory_picklist($m_inventory_picklist_id);
		
		// -- Remove Pick List Detail --
		foreach ($m_inventory_picklist->m_inventory_picklistdetail->get() as $m_inventory_picklistdetail)
		{
			$this->picklistdetail_remove($m_inventory_picklistdetail->id, $removed_by);
		}
		
		// -- Remove Pick List --
		if (!$m_inventory_picklist->delete())
			throw new Exception($m_inventory_picklist->error->string);
		
		return $m_inventory_picklist_id;
	}
	
	public function picklist_generate_status_inv_picking($m_inventory_picklist_id, $generate_by = NULL)
	{
		$status = 'NO PICKING';
		
		$m_inventory_picklist = new M_inventory_picklist($m_inventory_picklist_id);
		
		$table = $this->CI->db
			->select_if_null("SUM(ipld.quantity_box)", 0, 'quantity_box')
			->select_if_null("SUM(ipld.quantity)", 0, 'quantity')
			->select_if_null("SUM(ipgd.quantity_box)", 0, 'ipgd_quantity_box')
			->select_if_null("SUM(ipgd.quantity)", 0, 'ipgd_quantity')
			->from('m_inventory_picklistdetails ipld')
			->join("(SELECT   m_inventory_picklistdetail_id "
				  ."	    , ". $this->CI->db->if_null("SUM(quantity_box)", 0) ." quantity_box "
				  ."	    , ". $this->CI->db->if_null("SUM(quantity)", 0) ." quantity "
				  ."   FROM m_inventory_pickingdetails "
				  ."  GROUP BY m_inventory_picklistdetail_id"
				  .") ipgd"
				, "ipgd.m_inventory_picklistdetail_id = ipld.id"
				, 'left'
			)
			->where('ipld.m_inventory_picklist_id', $m_inventory_picklist_id)
			->get();
		if ($table->num_rows() > 0)
		{
			$record = $table->first_row();
			
			if (	$record->ipgd_quantity_box == 0
				&&	$record->ipgd_quantity == 0)
			{
				$status = 'NO PICKING';
			}
			elseif (	$record->quantity_box == $record->ipgd_quantity_box
					&&	$record->quantity == $record->ipgd_quantity)
			{
				$status = 'COMPLETE';
			}
			elseif (	$record->quantity_box != $record->ipgd_quantity_box
					&&	$record->quantity != $record->ipgd_quantity)
			{
				$status = 'INCOMPLETE';
			}
			elseif ($record->quantity_box != $record->ipgd_quantity_box)
			{
				$status = 'INCOMPLETE BOX';
			}
			elseif ($record->quantity != $record->ipgd_quantity)
			{
				$status = 'INCOMPLETE QUANTITY';
			}
		}
		
		$data = new stdClass();
		$data->status_inventory_picking = $status;
		$data->updated_by = $generate_by;
		
		$this->set_model_fields_values($m_inventory_picklist, $data);
		$m_inventory_picklist_saved = $m_inventory_picklist->save();
		if (!$m_inventory_picklist_saved)
			throw new Exception($m_inventory_picklist->error->string);
	}
	
	private $picklist_get_inventories_by_properties_records = NULL;
	
	public function picklist_get_inventories_by_properties($data, $quantity, $lost_tolerance = 10, $depth = 1)
	{
		if ($this->picklist_get_inventories_by_properties_records === NULL)
			$this->picklist_get_inventories_by_properties_records = array();
		
		if ($depth == 1)
		{
			$this->CI->db
				->select("inv.id, inv.quantity_box, inv.quantity, inv.quantity_per_box")
				->select("inv.pallet, inv.expired_date")
				->select("MIN(CASE WHEN ib.inbound_date IS NULL THEN inv.created ELSE ib.inbound_date END) entry_date", FALSE)
				->from('m_inventories inv')
				->join('m_inventory_inbounddetails ibd', "ibd.m_inventory_id = inv.id", 'left')
				->join('m_inventory_inbounds ib', "ib.id = ibd.m_inventory_inbound_id", 'left');
			$this->picklistdetail_criteria_query($data, 'inv', 'm_inventories');
			$table = $this->CI->db
				->where("inv.quantity_box >", 0)
				->where("inv.quantity >", 0)
				->group_by(
					array(
						  'inv.id', 'inv.quantity_box', 'inv.quantity', 'inv.quantity_per_box'
						, 'inv.pallet', 'inv.expired_date'
					)
				)
				->order_by('entry_date', 'asc')
				->order_by('inv.pallet', 'asc')
				->order_by('inv.expired_date', 'asc')
				->order_by('inv.quantity', 'desc')
				->get();
			$this->picklist_get_inventories_by_properties_records = $table->result();
		}
		
		$m_inventories_getted = array();
		$quantity_getted = 0;
		foreach ($this->picklist_get_inventories_by_properties_records as $m_inventory_record_idx=>$m_inventory_record)
		{
			$quantity_box_get = 0;
			$quantity_get = 0;
			
			if ($m_inventory_record->quantity_box > 1 && $m_inventory_record->quantity_per_box > 0 && $m_inventory_record->quantity_per_box == ($m_inventory_record->quantity / $m_inventory_record->quantity_box))
			{
				$quantity_per_box = $m_inventory_record->quantity_per_box;
				
				if ($quantity_per_box > ($quantity - $quantity_getted))
					continue;
				
				for ($quantity_box_counter = 1; $quantity_box_counter <= $m_inventory_record->quantity_box; $quantity_box_counter++)
				{
					if (($quantity_getted + $quantity_get + $quantity_per_box) > $quantity)
						break;
					
					$quantity_box_get++;
					$quantity_get += $quantity_per_box;
				}
			}
			else
			{
				if ($m_inventory_record->quantity > ($quantity - $quantity_getted))
					continue;
				
				$quantity_box_get = $m_inventory_record->quantity_box;
				$quantity_get = $m_inventory_record->quantity;
			}
			
			$m_inventory_getted = new stdClass();
			$m_inventory_getted->m_inventory_id = $m_inventory_record->id;
			$m_inventory_getted->quantity_box = $quantity_box_get;
			$m_inventory_getted->quantity = $quantity_get;
			$m_inventories_getted[] = $m_inventory_getted;
			
			$quantity_getted += $quantity_get;
			if ($quantity_getted >= $quantity)
				break;
			
			unset($this->picklist_get_inventories_by_properties_records[$m_inventory_record_idx]);
			
			$m_inventories_getted_childs = $this->picklist_get_inventories_by_properties($data, $quantity - $quantity_getted, $lost_tolerance, $depth + 1);
			foreach ($m_inventories_getted_childs as $m_inventories_getted_child)
			{
				$m_inventories_getted[] = $m_inventories_getted_child;
				$quantity_getted += $m_inventories_getted_child->quantity;
			}
			
			break;
		}
		
		if ($depth == 1)
		{
			// -- Lost Quantity --
			
			// -- Tolerance be here --
			foreach ($this->picklist_get_inventories_by_properties_records as $m_inventory_record)
			{
				if (($m_inventory_record->quantity - ($m_inventory_record->quantity * ($lost_tolerance / 100))) > ($quantity - $quantity_getted))
					continue;
					
				$m_inventory_getted = new stdClass();
				$m_inventory_getted->m_inventory_id = $m_inventory_record->id;
				$m_inventory_getted->quantity_box = $m_inventory_record->quantity_box;
				$m_inventory_getted->quantity = $m_inventory_record->quantity;
				$m_inventories_getted[] = $m_inventory_getted;
				
				$quantity_getted += $m_inventory_getted->quantity;
				if ($quantity_getted > $quantity)
					break;
			}
			
			if ($quantity > $quantity_getted)
			{
				$m_inventory_getted = new stdClass();
				$m_inventory_getted->m_inventory_id = NULL;
				$m_inventory_getted->quantity_box = -1;
				$m_inventory_getted->quantity = ($quantity - $quantity_getted) * -1;
				$m_inventories_getted[] = $m_inventory_getted;
			}
			
			// -- Remove inventory list --
			$this->picklist_get_inventories_by_properties_records = NULL;
		}
		
		return $m_inventories_getted;
	}
	
	public function picklist_get_inventories_by_properties_partial($data, $quantity)
	{
		$this->CI->db
			->select("inv.id, inv.quantity_box, inv.quantity, inv.quantity_per_box")
			->select("inv.pallet, inv.expired_date")
			->select("MIN(CASE WHEN ib.inbound_date IS NULL THEN inv.created ELSE ib.inbound_date END) entry_date", FALSE)
			->from('m_inventories inv')
			->join('m_inventory_inbounddetails ibd', "ibd.m_inventory_id = inv.id", 'left')
			->join('m_inventory_inbounds ib', "ib.id = ibd.m_inventory_inbound_id", 'left');
		$this->picklistdetail_criteria_query($data, 'inv', 'm_inventories');
		$table = $this->CI->db
			->where("inv.quantity_box >", 0)
			->where("inv.quantity >", 0)
			->group_by(
				array(
					  'inv.id', 'inv.quantity_box', 'inv.quantity', 'inv.quantity_per_box'
					, 'inv.pallet', 'inv.expired_date'
				)
			)
			->order_by('entry_date', 'asc')
			->order_by('inv.pallet', 'asc')
			->order_by('inv.expired_date', 'asc')
			->order_by('inv.quantity', 'asc')
			->get();
		$picklist_get_inventories_by_properties_records = $table->result();
		
		$m_inventories_getted = array();
		$quantity_getted = 0;
		foreach ($picklist_get_inventories_by_properties_records as $m_inventory_record_idx=>$m_inventory_record)
		{
			$quantity_box_get = 0;
			$quantity_get = $m_inventory_record->quantity - ($quantity - $quantity_getted);
			if ($quantity_get < 0)
				$quantity_get = $m_inventory_record->quantity;
			else
				$quantity_get = ($quantity - $quantity_getted);
			
			$m_inventory_getted = new stdClass();
			$m_inventory_getted->m_inventory_id = $m_inventory_record->id;
			$m_inventory_getted->quantity_box = $quantity_box_get;
			$m_inventory_getted->quantity = $quantity_get;
			$m_inventories_getted[] = $m_inventory_getted;
			
			$quantity_getted += $quantity_get;
			if ($quantity_getted >= $quantity)
				break;
		}
		
		// -- Lost Quantity --
		if ($quantity > $quantity_getted)
		{
			$m_inventory_getted = new stdClass();
			$m_inventory_getted->m_inventory_id = NULL;
			$m_inventory_getted->quantity_box = -1;
			$m_inventory_getted->quantity = ($quantity - $quantity_getted) * -1;
			$m_inventories_getted[] = $m_inventory_getted;
		}
		
		return $m_inventories_getted;
	}
	
	public function picklistdetail_add_by_properties($data, $created_by = NULL)
	{
		$c_project_ids = $this->CI->lib_custom->project_get_ids($created_by);
		
		$result = new stdClass();
		
		$m_inventory_picklist = new M_inventory_picklist();
		$c_orderoutdetail = new C_orderoutdetail();
		$m_product = new M_product();
		$picklist_quantity = 0;
		$picklist_quantity_box = 0;
		$lost_tolerance = 10;
		
		if (property_exists($data, 'c_orderoutdetail_id'))
		{
			$c_orderoutdetail = new C_orderoutdetail($data->c_orderoutdetail_id);
			$m_product = $c_orderoutdetail->m_product->get();
			$data->m_product_id = $m_product->id;
			$picklist_quantity = $c_orderoutdetail->quantity;
			
			$c_orderout = $c_orderoutdetail->c_orderout->get();
			$c_project = $c_orderout->c_project->get();
			$data->c_project_id = $c_project->id;
			
			$project_is_valid = $this->CI->lib_custom->project_is_valid($created_by, $c_project->id);
			if (!$project_is_valid)
				throw new Exception("Access denied for the project.");
		}
		if (property_exists($data, 'm_inventory_picklist_id'))
		{
			$m_inventory_picklist = new M_inventory_picklist($data->m_inventory_picklist_id);
		}
		if (!empty($data->quantity))
		{
			$picklist_quantity = $data->quantity;
			unset($data->quantity);
		}
		if (!empty($data->quantity_box))
		{
			$picklist_quantity_box = $data->quantity_box;
			unset($data->quantity_box);
		}
		if (!empty($data->tolerance))
		{
			$lost_tolerance = $data->tolerance;
			unset($data->tolerance);
		}
		
		$c_orderout = $c_orderoutdetail->c_orderout->get();
		
		// -- Get used quantity of product order out --
		$quantity_used = 0;
		$this->CI->db
			->select_if_null("SUM(ipld.quantity)", 0, 'quantity')
			->from('m_inventory_picklistdetails ipld')
			->join('c_orderoutdetails ood', "ood.id = ipld.c_orderoutdetail_id")
			->join('c_orderouts oo', "oo.id = ood.c_orderout_id")
			->where('ipld.c_orderoutdetail_id', $c_orderoutdetail->id);
		$this->CI->lib_custom->project_query_filter('oo.c_project_id', $c_project_ids);
		$table = $this->CI->db
			->get();
		if ($table->num_rows() > 0)
		{
			$table_record = $table->first_row();
			$quantity_used = $table_record->quantity;
		}
		
		$quantity_do = $c_orderoutdetail->quantity;
		
		// -- Validate Quantity --
		$quantity_free = $quantity_do - $quantity_used;
		
		if ($quantity_free <= 0)
			throw new Exception("No quantity existing order out ".$c_orderout->code.".");
		
		if ($picklist_quantity > $quantity_free)
			$picklist_quantity = $quantity_free; // throw new Exception("Quantity is too large. The maximum quantity existing on order out ".$c_orderout->code." is ".$quantity_free.".");
		
		// -- Get Order Out Detail to Adds --
		$quantity_picklist_total = 0;
		$quantity_picklist_added_total = 0;
		$quantity_box_picklist_added_total = 0;
		$this->CI->db
			->select("ood.id")
			->select_if_null("SUM(ood.quantity)", 0, 'quantity')
			->from('c_orderoutdetails ood')
			->join('c_orderouts oo', "oo.id = ood.c_orderout_id")
			->where('ood.id', $c_orderoutdetail->id)
			->group_by(
				array(
					'ood.id'
				)
			)
			->order_by('id', 'asc');
		$this->CI->lib_custom->project_query_filter('oo.c_project_id', $c_project_ids);
		$table = $this->CI->db
			->get();
		$c_orderoutdetails = $table->result();
		foreach ($c_orderoutdetails as $c_orderoutdetail)
		{
			$quantity_used = 0;
			$table = $this->CI->db
				->select_if_null("SUM(quantity)", 0, 'quantity')
				->from('m_inventory_picklistdetails')
				->where('c_orderoutdetail_id', $c_orderoutdetail->id)
				->get();
			if ($table->num_rows() > 0)
			{
				$m_inventory_picklistdetail = $table->first_row();
				$quantity_used = $m_inventory_picklistdetail->quantity;
			}
			
			$quantity_add = $c_orderoutdetail->quantity - $quantity_used;
			if ($quantity_add <= 0)
				continue;
			
			$quantity_picklist_total += $quantity_add;
			if ($quantity_picklist_total > $picklist_quantity)
			{
				$quantity_add -= $quantity_picklist_total - $picklist_quantity;
			}
			
			$inventory_decrease_quantity = 0;
			// -- Get inventory by box --
			//$m_inventories_allocated = $this->picklist_get_inventories_by_properties($data, $quantity_add, $lost_tolerance);
			// -- Get inventory by quantity --
			$m_inventories_allocated = $this->picklist_get_inventories_by_properties_partial($data, $quantity_add);
			foreach ($m_inventories_allocated as $m_inventory_allocated)
			{
				if ($m_inventory_allocated->m_inventory_id !== NULL)
				{
					$inventory_decrease_quantity += $m_inventory_allocated->quantity;
				}
			}
			
			//if ($quantity_add > $inventory_decrease_quantity)
			//	throw new Exception("Order out ".$c_orderout->code." is not enought. Inventory quantity is ".$inventory_decrease_quantity.".");
			
			foreach ($m_inventories_allocated as $m_inventory_allocated)
			{
				if ($m_inventory_allocated->m_inventory_id !== NULL)
				{
					$data->quantity_box = $m_inventory_allocated->quantity_box;
					$data->quantity = $m_inventory_allocated->quantity;
					
					// -- Inventory picklist detail add --
					$data->m_inventory_id = $m_inventory_allocated->m_inventory_id;
					$this->picklistdetail_add(clone $data, $created_by);
					
					$quantity_picklist_added_total += $m_inventory_allocated->quantity;
					$quantity_box_picklist_added_total += $m_inventory_allocated->quantity_box;
					if ($picklist_quantity_box > 0 && $quantity_box_picklist_added_total >= $picklist_quantity_box)
						break;
				}
				else
				{
					// -- Add Lost Here --
					$result->inventory_lost = $m_inventory_allocated;
				}
			}
			
			if ($picklist_quantity_box > 0 && $quantity_box_picklist_added_total >= $picklist_quantity_box)
				break;
			
			if ($quantity_picklist_added_total >= $picklist_quantity)
				break;
		}
		
		return $result;
	}
	
	public function picklistdetail_remove_by_properties($data, $removed_by = NULL)
	{
		$c_project_ids = $this->CI->lib_custom->project_get_ids($removed_by);
		
		// -- Get Pick List Detail Exists --
		$this->CI->db
			->select("ipld.id")
			->from('m_inventory_picklistdetails ipld')
			->join('c_orderoutdetails ood', "ood.id = ipld.c_orderoutdetail_id")
			->join('c_orderouts oo', "oo.id = ood.c_orderout_id");
		$this->picklistdetail_criteria_query($data, 'ipld', 'm_inventory_picklistdetails');
		$this->CI->lib_custom->project_query_filter('oo.c_project_id', $c_project_ids);
		$table = $this->CI->db
			->get();
		if ($table->num_rows() == 0)
		{
			throw new Exception("Critera not found in pick list existing.");
		}
		
		// -- Remove Pick List Detail Exists --
		$table_records = $table->result();
		foreach ($table_records as $table_record)
		{
			$this->picklistdetail_remove($table_record->id, $removed_by);
		}
	}
	
	public function picklistdetail_add($data, $created_by = NULL)
	{
		$m_inventory_picklist = new M_inventory_picklist();
		$c_orderoutdetail = new C_orderoutdetail();
		$m_inventory_picklist = new M_inventory_picklist();
		$m_inventory = new M_inventory();
		$quantity_box = 0;
		$quantity = 0;
		
		$m_inventory_picklistdetail_relations = array();
		if (property_exists($data, 'm_inventory_picklist_id'))
		{
			$m_inventory_picklist = new M_inventory_picklist($data->m_inventory_picklist_id);
			$m_inventory_picklistdetail_relations['m_inventory_picklist'] = $m_inventory_picklist;
			unset($data->m_inventory_picklist_id);
		}
		if (property_exists($data, 'c_orderoutdetail_id'))
		{
			$c_orderoutdetail = new C_orderoutdetail($data->c_orderoutdetail_id);
			$m_inventory_picklistdetail_relations['c_orderoutdetail'] = $c_orderoutdetail;
			unset($data->c_orderoutdetail_id);
			
			$c_orderout = $c_orderoutdetail->c_orderout->get();
			$c_project = $c_orderout->c_project->get();
			$project_is_valid = $this->CI->lib_custom->project_is_valid($created_by, $c_project->id);
			if (!$project_is_valid)
				throw new Exception("Access denied for the project.");
		}
		if (property_exists($data, 'm_inventory_id'))
		{
			$m_inventory = new M_inventory($data->m_inventory_id);
			$m_inventory_picklistdetail_relations['m_inventory'] = $m_inventory;
			$m_inventory_picklistdetail_relations['m_grid'] = $m_inventory->m_grid->get();
			$m_inventory_picklistdetail_relations['m_product'] = $m_inventory->m_product->get();
			$m_inventory_picklistdetail_relations['c_project'] = $m_inventory->c_project->get();
			$quantity_box = $m_inventory->quantity_box;
			$quantity = $m_inventory->quantity;
			unset($data->m_inventory_id);
		}
		if (property_exists($data, 'c_project_id'))
		{
			unset($data->c_project_id);
		}
		if (property_exists($data, 'quantity_box'))
		{
			// if (!empty($data->quantity_box))
				// $quantity_box = $data->quantity_box;
			$quantity_box = $data->quantity_box;
		}
		if (property_exists($data, 'quantity'))
		{
			// if (!empty($data->quantity))
				// $quantity = $data->quantity;
			$quantity = $data->quantity;
		}
		
		$c_orderout = $c_orderoutdetail->c_orderout->get();
		
		$data->quantity_box = $quantity_box;
		$quantity_box_inventory = $m_inventory->quantity_box - $data->quantity_box;
		if ($quantity_box_inventory < 0)
			throw new Exception("Quantity box of inventory is not enought to decrease inventory.");
		
		$data->quantity = $quantity;
		$quantity_inventory = $m_inventory->quantity - $data->quantity;
		if ($quantity_inventory < 0)
			throw new Exception("Quantity of inventory is not enought to decrease inventory.");
		
		$data->barcode = $m_inventory->barcode;
		$data->pallet = $m_inventory->pallet;
		$data->carton_no = $m_inventory->carton_no;
		$data->lot_no = $m_inventory->lot_no;
		$data->condition = $m_inventory->condition;
		$data->packed_date = $m_inventory->packed_date;
		$data->expired_date = $m_inventory->expired_date;
		$data->received_date = $m_inventory->received_date;
		$data->price_buy = $m_inventory->price_buy;
		$data->product_size = $m_inventory->product_size;
		$data->created_by = $created_by;
		
		$m_inventory_picklistdetail = new M_inventory_picklistdetail();
		$this->set_model_fields_values($m_inventory_picklistdetail, $data);
		$m_inventory_picklistdetail_saved = $m_inventory_picklistdetail->save($m_inventory_picklistdetail_relations);
		if (!$m_inventory_picklistdetail_saved)
			throw new Exception($m_inventory_picklistdetail->error->string);
		
		// -- Allocate adjust decrease inventory --
		$data_m_inventory_log = new stdClass();
		$data_m_inventory_log->log_type = "PICKLIST";
		$data_m_inventory_log->ref1_code = $m_inventory_picklist->code;
		$data_m_inventory_log->ref2_code = $c_orderout->code;
		$data_m_inventory_log->notes = 'Add Pick List';
		
		$allocated_quantity_box = $m_inventory_picklistdetail->quantity_box;
		if ($allocated_quantity_box == 0 && $m_inventory->quantity_box === 1 && $m_inventory->quantity - $m_inventory_picklistdetail->quantity == 0)
			$allocated_quantity_box = $m_inventory->quantity_box;
		
		$this->CI->lib_inventory->allocate($m_inventory->id, $allocated_quantity_box, $m_inventory_picklistdetail->quantity, $created_by, $data_m_inventory_log);
		
		// -- Update Status --
		$this->picklist_generate_status_inv_picking($m_inventory_picklist->id, $created_by);
		$this->picklistdetail_generate_status_inv_picking($m_inventory_picklistdetail->id, $created_by);
		$this->CI->load->library('core/lib_order');
		$this->CI->lib_order->orderoutdetail_generate_status_inv_picklist($c_orderoutdetail->id, $created_by);
		$this->CI->lib_order->orderout_generate_status_inv_picklist($c_orderout->id, $created_by);
		
		return $m_inventory_picklistdetail->id;
	}
	
	public function picklistdetail_remove($m_inventory_picklistdetail_id, $removed_by = NULL)
	{
		$m_inventory_picklistdetail = new M_inventory_picklistdetail($m_inventory_picklistdetail_id);
		$c_orderoutdetail = $m_inventory_picklistdetail->c_orderoutdetail->get();
		$c_orderout = $c_orderoutdetail->c_orderout->get();
		$m_inventory_picklist = $m_inventory_picklistdetail->m_inventory_picklist->get();
		$m_inventory = $m_inventory_picklistdetail->m_inventory->get();
		$m_grid = $m_inventory->m_grid->get();
		
		$c_project = $c_orderout->c_project->get();
		$project_is_valid = $this->CI->lib_custom->project_is_valid($removed_by, $c_project->id);
		if (!$project_is_valid)
			throw new Exception("Access denied for the project.");
			
		// -- Allocate adjust increase inventory quantity --
		$data_m_inventory_log = new stdClass();
		$data_m_inventory_log->log_type = "PICKLIST";
		$data_m_inventory_log->ref1_code = $m_inventory_picklist->code;
		$data_m_inventory_log->ref2_code = $c_orderout->code;
		$data_m_inventory_log->notes = 'Remove Pick List';
		
		$allocated_quantity_box = $m_inventory_picklistdetail->quantity_box * -1;
		if ($allocated_quantity_box == 0 && $m_inventory->quantity_box == 0 && $m_inventory->quantity == 0)
			$allocated_quantity_box = -1;
		$allocated_quantity = $m_inventory_picklistdetail->quantity * -1;
		
		$this->CI->lib_inventory->allocate($m_inventory->id, $allocated_quantity_box, $allocated_quantity, $removed_by, $data_m_inventory_log);
		
		// -- Remove Picking Details --
		foreach ($m_inventory_picklistdetail->m_inventory_pickingdetail->get() as $m_inventory_pickingdetail)
		{
			$this->pickingdetail_remove($m_inventory_pickingdetail->id, $removed_by);
		}
		
		// -- Delete Pick List Detail --
		if (!$m_inventory_picklistdetail->delete())
			throw new Exception($m_inventory_picklistdetail->error->string);
		
		// -- Update Status --
		$this->picklist_generate_status_inv_picking($m_inventory_picklist->id, $removed_by);
		$this->CI->load->library('core/lib_order');
		$this->CI->lib_order->orderoutdetail_generate_status_inv_picklist($c_orderoutdetail->id, $removed_by);
		$this->CI->lib_order->orderout_generate_status_inv_picklist($c_orderout->id, $removed_by);
		
		// -- Verify Pallet and Grid --
		$this->CI->lib_inventory->verify_pallet_grid($m_inventory->pallet, $m_grid->id);
		
		return $m_inventory_picklistdetail_id;
	}
	
	private function picklistdetail_criteria_query($data, $table_alias, $table_name = 'm_inventory_picklistdetails')
	{
		$fields = array(
			'm_grid_id', 'm_product_id', 'c_project_id', 'pallet', 'barcode', 'carton_no', 'lot_no', 'condition', 'packed_date', 'received_date', 'expired_date', 'price_buy', 'product_size'
		);
		if ($table_name == 'm_inventory_picklistdetails')
		{
			$fields[] = 'm_inventory_picklist_id';
		}
		
		foreach ($data as $field=>$value)
		{
			if (!in_array($field, $fields))
				continue;
			
			if ($value !== NULL)
			{
				if ($value !== '')
				{
					if ($field == 'c_project_id' && $table_name == 'm_inventories')
						$this->CI->db
							->where("(".$table_alias.'.'.$field.' = '. (int)$value ." OR ".$table_alias.'.'.$field." IS NULL)", NULL, FALSE);
					else
						$this->CI->db
							->where($table_alias.'.'.$field, $value);
				}
			}
			else
			{
				if (!($field == 'c_project_id' && $table_name == 'm_inventories'))
					$this->CI->db
						->where($table_alias.'.'.$field." IS NULL", NULL, FALSE);
			}
		}
	}
	
	public function picklistdetail_generate_status_inv_picking($m_inventory_picklistdetail_id, $generate_by = NULL)
	{
		$status = 'NO PICKING';
		
		$m_inventory_picklistdetail = new M_inventory_picklistdetail($m_inventory_picklistdetail_id);
		
		$table = $this->CI->db
			->select_if_null("SUM(ipgd.quantity_box)", 0, 'quantity_box')
			->select_if_null("SUM(ipgd.quantity)", 0, 'quantity')
			->from('m_inventory_pickingdetails ipgd')
			->where('ipgd.m_inventory_picklistdetail_id', $m_inventory_picklistdetail_id)
			->get();
		if ($table->num_rows() > 0)
		{
			$record = $table->first_row();
			if (	$record->quantity_box == 0
				&&	$record->quantity == 0)
			{
				$status = 'NO PICKING';
			}
			elseif (	$m_inventory_picklistdetail->quantity_box == $record->quantity_box
					&&	$m_inventory_picklistdetail->quantity == $record->quantity)
			{
				$status = 'COMPLETE';
			}
			elseif (	$m_inventory_picklistdetail->quantity_box != $record->quantity_box
					&&	$m_inventory_picklistdetail->quantity != $record->quantity)
			{
				$status = 'INCOMPLETE';
			}
			elseif ($m_inventory_picklistdetail->quantity_box != $record->quantity_box)
			{
				$status = 'INCOMPLETE BOX';
			}
			elseif ($m_inventory_picklistdetail->quantity != $record->quantity)
			{
				$status = 'INCOMPLETE QUANTITY';
			}
		}
		
		$data = new stdClass();
		$data->status_inventory_picking = $status;
		$data->updated_by = $generate_by;
		
		$this->set_model_fields_values($m_inventory_picklistdetail, $data);
		$m_inventory_picklistdetail_saved = $m_inventory_picklistdetail->save();
		if (!$m_inventory_picklistdetail_saved)
			throw new Exception($m_inventory_picklistdetail->error->string);
	}
	
	public function picklist_generate_picking_shipment($id, $generate_by = NULL)
	{
		$c_project_ids = $this->CI->lib_custom->project_get_ids($generate_by);
		
		$table = $this->CI->db
			->select("ipl.id, ipl.code, ipl.picklist_date, ipl.picklist_orderout_type")
			->select("ipl.supervisor, ipl.schedule_phase, ipl.schedule_time")
			->select("ipl.shipment_type, ipl.transport_mode, ipl.shipment_to, ipl.vehicle_no, ipl.vehicle_driver")
			->select("ipl.status_inventory_picking, ipl.notes")
			->from('m_inventory_picklists ipl')
			->where('ipl.id', $id)
			->get();
		if ($table->num_rows() == 0)
			throw new Exception("Pick list not found.");
		$m_inventory_picklist = $table->first_row();
		
		$this->CI->db
			->select("ipld.id")
			->select("ipld.quantity_box")
			->select("ipld.quantity")
			->select_if_null('ipgd.quantity_box', 0, 'quantity_box_used')
			->select_if_null('ipgd.quantity', 0, 'quantity_used')
			->from('m_inventory_picklistdetails ipld')
			->join(
				 "(SELECT m_inventory_picklistdetail_id, "
				."		  " . $this->CI->db->if_null("SUM(quantity_box)", 0) . " quantity_box, "
				."		  " . $this->CI->db->if_null("SUM(quantity)", 0) . " quantity "
				."	 FROM m_inventory_pickingdetails "
				."  GROUP BY m_inventory_picklistdetail_id "
				.") ipgd", 
				"ipgd.m_inventory_picklistdetail_id = ipld.id", 'left')
			->where("ipld.m_inventory_picklist_id", $m_inventory_picklist->id)
			->where("ipld.quantity - ". $this->CI->db->if_null("ipgd.quantity", 0) ." > 0", NULL, FALSE);
		$this->CI->lib_custom->project_query_filter('ipld.c_project_id', $c_project_ids);
		$table = $this->CI->db
			->get();
		if ($table->num_rows() == 0)
			throw new Exception("No quantity to create picking and shipment.");
		
		$m_inventory_picklistdetails = $table->result();
		
		$data_picking = new stdClass();
		if ($m_inventory_picklist->picklist_orderout_type === 0)
			$data_picking->code = generate_code_number("DOPG". date('ymd-'), NULL, 3);
		elseif ($m_inventory_picklist->picklist_orderout_type === 1)
			$data_picking->code = generate_code_number("SPKPG". date('ymd-'), NULL, 3);
		$data_picking->picking_date = $m_inventory_picklist->picklist_date;
		$data_picking->shipment_type = $m_inventory_picklist->shipment_type;
		$data_picking->transport_mode = $m_inventory_picklist->transport_mode;
		$data_picking->shipment_to = $m_inventory_picklist->shipment_to;
		$data_picking->vehicle_no = $m_inventory_picklist->vehicle_no;
		$data_picking->vehicle_driver = $m_inventory_picklist->vehicle_driver;
		$data_picking->picking_orderout_type = $m_inventory_picklist->picklist_orderout_type;
		$m_inventory_picking_id = $this->picking_add($data_picking, $generate_by);
		
		foreach ($m_inventory_picklistdetails as $m_inventory_picklistdetail_idx=>$m_inventory_picklistdetail)
		{
			$data_pickingdetail = new stdClass();
			$data_pickingdetail->m_inventory_picking_id = $m_inventory_picking_id;
			$data_pickingdetail->m_inventory_picklistdetail_id = $m_inventory_picklistdetail->id;
			$data_pickingdetail->quantity_box = $m_inventory_picklistdetail->quantity_box - $m_inventory_picklistdetail->quantity_box_used;
			$data_pickingdetail->quantity = $m_inventory_picklistdetail->quantity - $m_inventory_picklistdetail->quantity_used;
			$this->pickingdetail_add($data_pickingdetail, $generate_by);
		}
		
		$m_inventory_shipment_id = $this->picking_generate_shipment($m_inventory_picking_id, $generate_by);
		
		return $m_inventory_shipment_id;
	}
	
	/* -------------------- */
	/* -- PICKING REGION -- */
	/* -------------------- */
	
	public function picking_add($data, $created_by = NULL)
	{
		$data->created_by = $created_by;
		
		$m_inventory_picking = new M_inventory_picking();
		$this->set_model_fields_values($m_inventory_picking, $data);
		$m_inventory_picking_saved = $m_inventory_picking->save();
		if (!$m_inventory_picking_saved)
			throw new Exception($m_inventory_picking->error->string);
		
		// -- Update Status --
		$this->picking_generate_status_inv_shipment($m_inventory_picking->id, $created_by);
		
		return $m_inventory_picking->id;
	}
	
	public function picking_update($m_inventory_picking_id, $data, $updated_by = NULL)
	{
		$data->updated_by = $updated_by;
		
		$m_inventory_picking = new M_inventory_picking($m_inventory_picking_id);
		$this->set_model_fields_values($m_inventory_picking, $data);
		$m_inventory_picking_saved = $m_inventory_picking->save();
		if (!$m_inventory_picking_saved)
			throw new Exception($m_inventory_picking->error->string);
		
		// -- Update Status --
		$this->picking_generate_status_inv_shipment($m_inventory_picking->id, $updated_by);
		
		return $m_inventory_picking_id;
	}
	
	public function picking_remove($m_inventory_picking_id, $removed_by = NULL)
	{
		$m_inventory_picking = new M_inventory_picking($m_inventory_picking_id);
		
		// -- Remove Picking Detail --
		foreach ($m_inventory_picking->m_inventory_pickingdetail->get() as $m_inventory_pickingdetail)
		{
			$this->pickingdetail_remove($m_inventory_pickingdetail->id, $removed_by);
		}
		
		// -- Remove Picking --
		if (!$m_inventory_picking->delete())
			throw new Exception($m_inventory_picking->error->string);
		
		return $m_inventory_picking_id;
	}
	
	public function picking_generate_status_inv_shipment($m_inventory_picking_id, $generate_by = NULL)
	{
		$status = 'NO SHIPMENT';
		
		$m_inventory_picking = new M_inventory_picking($m_inventory_picking_id);
		
		$table = $this->CI->db
			->select_if_null("SUM(ipgd.quantity_box)", 0, 'quantity_box')
			->select_if_null("SUM(ipgd.quantity)", 0, 'quantity')
			->select_if_null("SUM(ispd.quantity_box)", 0, 'ispd_quantity_box')
			->select_if_null("SUM(ispd.quantity)", 0, 'ispd_quantity')
			->from('m_inventory_pickingdetails ipgd')
			->join("(SELECT   m_inventory_pickingdetail_id "
				  ."	    , ". $this->CI->db->if_null("SUM(quantity_box)", 0) ." quantity_box "
				  ."	    , ". $this->CI->db->if_null("SUM(quantity)", 0) ." quantity "
				  ."   FROM m_inventory_shipmentdetails "
				  ."  GROUP BY m_inventory_pickingdetail_id"
				  .") ispd"
				, "ispd.m_inventory_pickingdetail_id = ipgd.id"
				, 'left'
			)
			->where('ipgd.m_inventory_picking_id', $m_inventory_picking_id)
			->get();
		if ($table->num_rows() > 0)
		{
			$record = $table->first_row();
			
			if (	$record->ispd_quantity_box == 0
				&&	$record->ispd_quantity == 0)
			{
				$status = 'NO SHIPMENT';
			}
			elseif (	$record->quantity_box == $record->ispd_quantity_box
					&&	$record->quantity == $record->ispd_quantity)
			{
				$status = 'COMPLETE';
			}
			elseif (	$record->quantity_box != $record->ispd_quantity_box
					&&	$record->quantity != $record->ispd_quantity)
			{
				$status = 'INCOMPLETE';
			}
			elseif ($record->quantity_box != $record->ispd_quantity_box)
			{
				$status = 'INCOMPLETE BOX';
			}
			elseif ($record->quantity != $record->ispd_quantity)
			{
				$status = 'INCOMPLETE QUANTITY';
			}
		}
		
		$data = new stdClass();
		$data->status_inventory_shipment = $status;
		$data->updated_by = $generate_by;
		
		$this->set_model_fields_values($m_inventory_picking, $data);
		$m_inventory_picking_saved = $m_inventory_picking->save();
		if (!$m_inventory_picking_saved)
			throw new Exception($m_inventory_picking->error->string);
	}
	
	public function pickingdetail_add_by_properties($data, $created_by = NULL)
	{
		$c_project_ids = $this->CI->lib_custom->project_get_ids($created_by);
		
		$m_inventory_picking = new M_inventory_picking();
		$m_inventory_picklist = new M_inventory_picklist();
		$picking_quantity_box = 0;
		
		if (property_exists($data, 'm_inventory_picking_id'))
		{
			$m_inventory_picking = new M_inventory_picking($data->m_inventory_picking_id);
		}
		if (property_exists($data, 'm_inventory_picklist_id'))
		{
			$m_inventory_picklist = new M_inventory_picklist($data->m_inventory_picklist_id);
			unset($data->m_inventory_picklist_id);
		}
		if (property_exists($data, 'quantity_box'))
		{
			$picking_quantity_box = $data->quantity_box;
			unset($data->quantity_box);
		}
		
		// -- Get used quantity box of product pick list --
		$quantity_box_used = 0;
		$this->CI->db
			->select_if_null("SUM(ipgd.quantity_box)", 0, 'quantity_box')
			->from('m_inventory_pickingdetails ipgd')
			->join('m_inventory_picklistdetails ipld', "ipld.id = ipgd.m_inventory_picklistdetail_id")
			->join('c_orderoutdetails ood', "ood.id = ipld.c_orderoutdetail_id")
			->join('c_orderouts oo', "oo.id = ood.c_orderout_id")
			->where('ipld.m_inventory_picklist_id', $m_inventory_picklist->id);
		$this->pickingdetail_criteria_query($data, 'ipld', 'm_inventory_picklistdetails');
		$this->CI->lib_custom->project_query_filter('oo.c_project_id', $c_project_ids);
		$table = $this->CI->db
			->get();
		if ($table->num_rows() > 0)
		{
			$table_record = $table->first_row();
			$quantity_box_used = $table_record->quantity_box;
		}
		
		$quantity_box_picklist = 0;
		$this->CI->db
			->select_if_null("SUM(ipld.quantity_box)", 0, 'quantity_box')
			->from('m_inventory_picklistdetails ipld')
			->join('c_orderoutdetails ood', "ood.id = ipld.c_orderoutdetail_id")
			->join('c_orderouts oo', "oo.id = ood.c_orderout_id")
			->where('ipld.m_inventory_picklist_id', $m_inventory_picklist->id);
		$this->pickingdetail_criteria_query($data, 'ipld', 'm_inventory_picklistdetails');
		$this->CI->lib_custom->project_query_filter('oo.c_project_id', $c_project_ids);
		$table = $this->CI->db
			->get();
		if ($table->num_rows() > 0)
		{
			$table_record = $table->first_row();
			$quantity_box_picklist = $table_record->quantity_box;
		}
		
		// -- Validate Quantity --
		$quantity_box_free = $quantity_box_picklist - $quantity_box_used;
		
		if ($quantity_box_free <= 0)
			throw new Exception("No box quantity existing of pick list ".$m_inventory_picklist->code.".");
		
		if ($picking_quantity_box > $quantity_box_free)
			throw new Exception("Box quantity is too large. The maximum box quantity existing on pick list ".$m_inventory_picklist->code." is ".$quantity_box_free.".");
		
		if ($picking_quantity_box == 0)
			$picking_quantity_box = $quantity_box_free;
		
		// -- Get Pick List Detail to Adds --
		$quantity_box_picking_total = 0;
		$quantity_box_picking_added_total = 0;
		$this->CI->db
			->select("ipld.id, ipld.quantity_box, ipld.quantity, inv.quantity_per_box")
			->from('m_inventory_picklistdetails ipld')
			->join('m_inventories inv', "inv.id = ipld.m_inventory_id")
			->join('c_orderoutdetails ood', "ood.id = ipld.c_orderoutdetail_id")
			->join('c_orderouts oo', "oo.id = ood.c_orderout_id")
			->where('ipld.m_inventory_picklist_id', $m_inventory_picklist->id);
		$this->pickingdetail_criteria_query($data, 'ipld', 'm_inventory_picklistdetails');
		$this->CI->lib_custom->project_query_filter('oo.c_project_id', $c_project_ids);
		$table = $this->CI->db
			->order_by('ipld.id', 'asc')
			->get();
		$m_inventory_picklistdetails = $table->result();
		foreach ($m_inventory_picklistdetails as $m_inventory_picklistdetail)
		{
			$quantity_box_used = 0;
			$table = $this->CI->db
				->select("SUM(quantity_box) quantity_box", FALSE)
				->from('m_inventory_pickingdetails')
				->where('m_inventory_picklistdetail_id', $m_inventory_picklistdetail->id)
				->get();
			if ($table->num_rows() > 0)
			{
				$m_inventory_pickingdetail = $table->first_row();
				$quantity_box_used = $m_inventory_pickingdetail->quantity_box;
			}
			
			$quantity_box_add = $m_inventory_picklistdetail->quantity_box - $quantity_box_used;
			if ($quantity_box_add <= 0)
				continue;
			
			$quantity_box_picking_total += $quantity_box_add;
			if ($quantity_box_picking_total > $picking_quantity_box)
			{
				$quantity_box_add -= $quantity_box_picking_total - $picking_quantity_box;
			}
			
			$data->m_inventory_picklistdetail_id = $m_inventory_picklistdetail->id;
			$data->quantity_box = $quantity_box_add;
			
			if ($m_inventory_picklistdetail->quantity_box > 1)
			{
				$data->quantity = ($m_inventory_picklistdetail->quantity / $m_inventory_picklistdetail->quantity_box) * $quantity_box_add;
				if ($m_inventory_picklistdetail->quantity_per_box > 0)
				{
					if ($m_inventory_picklistdetail->quantity_per_box == ($m_inventory_picklistdetail->quantity / $m_inventory_picklistdetail->quantity_box))
						$data->quantity = $m_inventory_picklistdetail->quantity_per_box * $quantity_box_add;
				}
			}
			else
				$data->quantity = $m_inventory_picklistdetail->quantity;
			
			$this->pickingdetail_add(clone $data, $created_by);
			
			$quantity_box_picking_added_total += $quantity_box_add;
			if ($quantity_box_picking_added_total >= $picking_quantity_box)
				break;
		}
		
		if ($quantity_box_picking_added_total != $picking_quantity_box)
			throw new Exception("Picking ".$m_inventory_picking->code." with quantity ".$picking_quantity_box." only ".$quantity_box_picking_added_total." existed.");
	}
	
	public function pickingdetail_remove_by_properties($data, $removed_by = NULL)
	{
		$c_project_ids = $this->CI->lib_custom->project_get_ids($removed_by);
		
		$m_inventory_picking = new M_inventory_picking();
		if (property_exists($data, 'm_inventory_picking_id'))
		{
			$m_inventory_picking = new M_inventory_picking($data->m_inventory_picking_id);
		}
		
		// -- Get Picking Details --
		$this->CI->db
			->select("ipgd.id")
			->from('m_inventory_pickingdetails ipgd')
			->join('m_inventory_picklistdetails ipld', "ipld.id = ipgd.m_inventory_picklistdetail_id")
			->join('c_orderoutdetails ood', "ood.id = ipld.c_orderoutdetail_id")
			->join('c_orderouts oo', "oo.id = ood.c_orderout_id")
			->where('ipgd.m_inventory_picking_id', $m_inventory_picking->id);
		$this->pickingdetail_criteria_query($data, 'ipld', 'm_inventory_picklistdetails');
		$this->pickingdetail_criteria_query($data, 'ipgd', 'm_inventory_pickingdetails');
		$this->CI->lib_custom->project_query_filter('oo.c_project_id', $c_project_ids);
		$table = $this->CI->db
			->get();
		if ($table->num_rows() == 0)
		{
			throw new Exception("Criteria in picking ".$m_inventory_picking->code." not found.");
		}
		
		// -- Remove Picking Detail Exists --
		$table_records = $table->result();
		foreach ($table_records as $table_record)
		{
			$this->pickingdetail_remove($table_record->id, $removed_by);
		}
	}
	
	public function pickingdetail_add($data, $created_by = NULL)
	{
		$m_inventory_picking = new M_inventory_picking();
		$m_inventory_picklistdetail = new M_inventory_picklistdetail();
		$c_orderout = new C_orderout();
		$quantity_box = 0;
		$quantity = 0;
		
		$m_inventory_pickingdetail_relations = array();
		if (property_exists($data, 'm_inventory_picking_id'))
		{
			$m_inventory_picking = new M_inventory_picking($data->m_inventory_picking_id);
			$m_inventory_pickingdetail_relations['m_inventory_picking'] = $m_inventory_picking;
			unset($data->m_inventory_picking_id);
		}
		if (property_exists($data, 'm_inventory_picklistdetail_id'))
		{
			$m_inventory_picklistdetail = new M_inventory_picklistdetail($data->m_inventory_picklistdetail_id);
			$m_inventory_pickingdetail_relations['m_inventory_picklistdetail'] = $m_inventory_picklistdetail;
			unset($data->m_inventory_picklistdetail_id);
			
			$c_orderoutdetail = $m_inventory_picklistdetail->c_orderoutdetail->get();
			$c_orderout = $c_orderoutdetail->c_orderout->get();
			$c_project = $c_orderout->c_project->get();
			$project_is_valid = $this->CI->lib_custom->project_is_valid($created_by, $c_project->id);
			if (!$project_is_valid)
				throw new Exception("Access denied for the project.");
		}
		if (property_exists($data, 'quantity_box'))
		{
			$quantity_box = $data->quantity_box;
		}
		if (empty($quantity_box))
			$quantity_box = $m_inventory_picklistdetail->quantity_box;
		if (property_exists($data, 'quantity'))
		{
			$quantity = $data->quantity;
		}
		if (empty($quantity))
			$quantity = $m_inventory_picklistdetail->quantity;
		
		// -- Add Picking Detail --
		$data->quantity_box = $quantity_box;
		$data->quantity = $quantity;
		$data->created_by = $created_by;
		
		$m_inventory_pickingdetail = new M_inventory_pickingdetail();
		$this->set_model_fields_values($m_inventory_pickingdetail, $data);
		$m_inventory_pickingdetail_saved = $m_inventory_pickingdetail->save($m_inventory_pickingdetail_relations);
		if (!$m_inventory_pickingdetail_saved)
			throw new Exception($m_inventory_pickingdetail->error->string);
		
		// -- Inventory Adjust Allocated to Picked --
		if ($m_inventory_picklistdetail->exists())
		{
			$m_inventory = $m_inventory_picklistdetail->m_inventory->get();
			
			$data_m_inventory_log = new stdClass();
			$data_m_inventory_log->log_type = "PICKING";
			$data_m_inventory_log->ref1_code = $m_inventory_picking->code;
			$data_m_inventory_log->ref2_code = $c_orderout->code;
			$data_m_inventory_log->notes = 'Add Picking';
			$this->CI->lib_inventory->pick($m_inventory->id, $m_inventory_pickingdetail->quantity_box, $m_inventory_pickingdetail->quantity, $created_by, $data_m_inventory_log);
		}
		
		$m_inventory_picklist = $m_inventory_picklistdetail->m_inventory_picklist->get();
		
		// -- Update Status --
		$this->picking_generate_status_inv_shipment($m_inventory_picking->id, $created_by);
		$this->pickingdetail_generate_status_inv_shipment($m_inventory_pickingdetail->id, $created_by);
		$this->picklistdetail_generate_status_inv_picking($m_inventory_picklistdetail->id, $created_by);
		$this->picklist_generate_status_inv_picking($m_inventory_picklist->id, $created_by);
		
		return $m_inventory_pickingdetail->id;
	}
	
	public function pickingdetail_remove($m_inventory_pickingdetail_id, $removed_by = NULL)
	{
		$m_inventory_pickingdetail = new M_inventory_pickingdetail($m_inventory_pickingdetail_id);
		
		// -- Inventory Adjust Allocated to Picked --
		$m_inventory_picking = $m_inventory_pickingdetail->m_inventory_picking->get();
		$m_inventory_picklistdetail = $m_inventory_pickingdetail->m_inventory_picklistdetail->get();
		$m_inventory_picklist = $m_inventory_picklistdetail->m_inventory_picklist->get();
		$m_inventory = $m_inventory_picklistdetail->m_inventory->get();
		$c_orderoutdetail = $m_inventory_picklistdetail->c_orderoutdetail->get();
		$c_orderout = $c_orderoutdetail->c_orderout->get();
		$c_project = $c_orderout->c_project->get();
		$project_is_valid = $this->CI->lib_custom->project_is_valid($removed_by, $c_project->id);
		if (!$project_is_valid)
			throw new Exception("Access denied for the project.");
		
		$data_m_inventory_log = new stdClass();
		$data_m_inventory_log->log_type = "PICKING";
		$data_m_inventory_log->ref1_code = $m_inventory_picking->code;
		$data_m_inventory_log->ref2_code = $c_orderout->code;
		$data_m_inventory_log->notes = 'Remove Picking';
		$this->CI->lib_inventory->pick($m_inventory->id, $m_inventory_pickingdetail->quantity_box * -1, $m_inventory_pickingdetail->quantity * -1, $removed_by, $data_m_inventory_log);
		
		// -- Remove Shipment Details --
		foreach ($m_inventory_pickingdetail->m_inventory_shipmentdetail->get() as $m_inventory_shipmentdetail)
		{
			$this->shipmentdetail_remove($m_inventory_shipmentdetail->id, $removed_by);
		}
		
		// -- Remove Picking Detail --
		if (!$m_inventory_pickingdetail->delete())
			throw new Exception($m_inventory_pickingdetail->error->string);
		
		// -- Update Status --
		$this->picking_generate_status_inv_shipment($m_inventory_picking->id, $removed_by);
		$this->picklistdetail_generate_status_inv_picking($m_inventory_picklistdetail->id, $removed_by);
		$this->picklist_generate_status_inv_picking($m_inventory_picklist->id, $removed_by);
		
		return $m_inventory_pickingdetail_id;
	}
	
	private function pickingdetail_criteria_query($data, $table_alias, $table_name = 'm_inventory_picklistdetails')
	{
		$fields = array();
		if ($table_name == 'm_inventory_picklistdetails')
		{
			$fields[] = 'm_inventory_picklist_id';
			$fields[] = 'm_grid_id';
			$fields[] = 'm_product_id';
			$fields[] = 'pallet';
			$fields[] = 'barcode';
			$fields[] = 'carton_no';
			$fields[] = 'lot_no';
			$fields[] = 'condition';
		}
		if ($table_name == 'm_inventory_pickingdetails')
		{
			$fields[] = 'packed_group';
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
	
	public function pickingdetail_generate_status_inv_shipment($m_inventory_pickingdetail_id, $generate_by = NULL)
	{
		$status = 'NO SHIPMENT';
		
		$m_inventory_pickingdetail = new M_inventory_pickingdetail($m_inventory_pickingdetail_id);
		
		$table = $this->CI->db
			->select_if_null("SUM(ispd.quantity_box)", 0, 'quantity_box')
			->select_if_null("SUM(ispd.quantity)", 0, 'quantity')
			->from('m_inventory_shipmentdetails ispd')
			->where('ispd.m_inventory_pickingdetail_id', $m_inventory_pickingdetail_id)
			->get();
		if ($table->num_rows() > 0)
		{
			$record = $table->first_row();
			if (	$record->quantity_box == 0
				&&	$record->quantity == 0)
			{
				$status = 'NO SHIPMENT';
			}
			elseif (	$m_inventory_pickingdetail->quantity_box == $record->quantity_box
					&&	$m_inventory_pickingdetail->quantity == $record->quantity)
			{
				$status = 'COMPLETE';
			}
			elseif (	$m_inventory_pickingdetail->quantity_box != $record->quantity_box
					&&	$m_inventory_pickingdetail->quantity != $record->quantity)
			{
				$status = 'INCOMPLETE';
			}
			elseif ($m_inventory_pickingdetail->quantity_box != $record->quantity_box)
			{
				$status = 'INCOMPLETE BOX';
			}
			elseif ($m_inventory_pickingdetail->quantity != $record->quantity)
			{
				$status = 'INCOMPLETE QUANTITY';
			}
		}
		
		$data = new stdClass();
		$data->status_inventory_shipment = $status;
		$data->updated_by = $generate_by;
		
		$this->set_model_fields_values($m_inventory_pickingdetail, $data);
		$m_inventory_pickingdetail_saved = $m_inventory_pickingdetail->save();
		if (!$m_inventory_pickingdetail_saved)
			throw new Exception($m_inventory_pickingdetail->error->string);
	}
	
	public function picking_generate_shipment($id, $generate_by = NULL)
	{
		$c_project_ids = $this->CI->lib_custom->project_get_ids($generate_by);
		
		$table = $this->CI->db
			->select("ipg.id, ipg.code, ipg.picking_date, ipg.picking_orderout_type, ipg.status_inventory_shipment, ipg.notes")
			->select("ipg.shipment_type, ipg.transport_mode, ipg.shipment_to, ipg.vehicle_no, ipg.vehicle_driver")
			->from('m_inventory_pickings ipg')
			->where('ipg.id', $id)
			->get();
		if ($table->num_rows() == 0)
			throw new Exception("Picking not found.");
		$m_inventory_picking = $table->first_row();
		
		$this->CI->db
			->select("ipgd.id")
			->select("ipgd.quantity_box")
			->select("ipgd.quantity")
			->select_if_null('ishd.quantity_box', 0, 'quantity_box_used')
			->select_if_null('ishd.quantity', 0, 'quantity_used')
			->from('m_inventory_pickingdetails ipgd')
			->join('m_inventory_picklistdetails ipld', "ipld.id = ipgd.m_inventory_picklistdetail_id")
			->join(
				 "(SELECT m_inventory_pickingdetail_id, "
				."		  ". $this->CI->db->if_null("SUM(quantity_box)", 0) . " quantity_box, "
				."		  ". $this->CI->db->if_null("SUM(quantity)", 0) . " quantity "
				."	 FROM m_inventory_shipmentdetails "
				."  GROUP BY m_inventory_pickingdetail_id "
				.") ishd", 
				"ishd.m_inventory_pickingdetail_id = ipgd.id", 'left')
			->where("ipgd.m_inventory_picking_id", $id)
			->where("ipgd.quantity - ". $this->CI->db->if_null("ishd.quantity", 0) ." > 0", NULL, FALSE);
		$this->CI->lib_custom->project_query_filter('ipld.c_project_id', $c_project_ids);
		$table = $this->CI->db
			->get();
		if ($table->num_rows() == 0)
			throw new Exception("No quantity to create shipment.");
		
		$m_inventory_pickingdetails = $table->result();
		
		$data_shipment = new stdClass();
		if ($m_inventory_picking->picking_orderout_type === 0)
			$data_shipment->code = generate_code_number("DOSH". date('ymd-'), NULL, 3);
		elseif ($m_inventory_picking->picking_orderout_type === 1)
			$data_shipment->code = generate_code_number("SPKSH". date('ymd-'), NULL, 3);
		$data_shipment->shipment_date = $m_inventory_picking->picking_date;
		$data_shipment->shipment_type = $m_inventory_picking->shipment_type;
		$data_shipment->transport_mode = $m_inventory_picking->transport_mode;
		$data_shipment->shipment_to = $m_inventory_picking->shipment_to;
		$data_shipment->vehicle_no = $m_inventory_picking->vehicle_no;
		$data_shipment->vehicle_driver = $m_inventory_picking->vehicle_driver;
		$data_shipment->shipment_orderout_type = $m_inventory_picking->picking_orderout_type;
		$m_inventory_shipment_id = $this->shipment_add($data_shipment, $generate_by);
		
		foreach ($m_inventory_pickingdetails as $m_inventory_pickingdetail_idx=>$m_inventory_pickingdetail)
		{
			$data_shipmentdetail = new stdClass();
			$data_shipmentdetail->m_inventory_shipment_id = $m_inventory_shipment_id;
			$data_shipmentdetail->m_inventory_pickingdetail_id = $m_inventory_pickingdetail->id;
			$data_shipmentdetail->quantity_box = $m_inventory_pickingdetail->quantity_box - $m_inventory_pickingdetail->quantity_box_used;
			$data_shipmentdetail->quantity = $m_inventory_pickingdetail->quantity - $m_inventory_pickingdetail->quantity_used;
			$m_inventory_shipmentdetail_id = $this->shipmentdetail_add($data_shipmentdetail, $generate_by);
		}
		
		return $m_inventory_shipment_id;
	}
	
	/* --------------------- */
	/* -- SHIPMENT REGION -- */
	/* --------------------- */
	
	public function shipment_add($data, $created_by = NULL)
	{
		$data->created_by = $created_by;
		
		$m_inventory_shipment = new M_inventory_shipment();
		$this->set_model_fields_values($m_inventory_shipment, $data);
		$m_inventory_shipment_saved = $m_inventory_shipment->save();
		if (!$m_inventory_shipment_saved)
			throw new Exception($m_inventory_shipment->error->string);
		
		return $m_inventory_shipment->id;
	}
	
	public function shipment_update($m_inventory_shipment_id, $data, $updated_by = NULL)
	{
		$data->updated_by = $updated_by;
		
		$m_inventory_shipment = new M_inventory_shipment($m_inventory_shipment_id);
		$this->set_model_fields_values($m_inventory_shipment, $data);
		$m_inventory_shipment_saved = $m_inventory_shipment->save();
		if (!$m_inventory_shipment_saved)
			throw new Exception($m_inventory_shipment->error->string);
		
		return $m_inventory_shipment_id;
	}
	
	public function shipment_remove($m_inventory_shipment_id, $removed_by = NULL)
	{
		$m_inventory_shipment = new M_inventory_shipment($m_inventory_shipment_id);
		
		// -- Remove Shipment Detail --
		foreach ($m_inventory_shipment->m_inventory_shipmentdetail->get() as $m_inventory_shipmentdetail)
		{
			$this->shipmentdetail_remove($m_inventory_shipmentdetail->id, $removed_by);
		}
		
		// -- Remove Shipment --
		if (!$m_inventory_shipment->delete())
			throw new Exception($m_inventory_shipment->error->string);
		
		return $m_inventory_shipment_id;
	}
	
	public function shipmentdetail_add_by_properties($data, $created_by = NULL)
	{
		$c_project_ids = $this->CI->lib_custom->project_get_ids($created_by);
		
		$m_inventory_shipment = new M_inventory_shipment();
		$m_inventory_picking = new M_inventory_picking();
		$shipment_quantity_box = 0;
		
		if (property_exists($data, 'm_inventory_shipment_id'))
		{
			$m_inventory_shipment = new M_inventory_shipment($data->m_inventory_shipment_id);
		}
		if (property_exists($data, 'm_inventory_picking_id'))
		{
			$m_inventory_picking = new M_inventory_picking($data->m_inventory_picking_id);
			unset($data->m_inventory_picking_id);
		}
		if (property_exists($data, 'quantity_box'))
		{
			$shipment_quantity_box = $data->quantity_box;
			unset($data->quantity_box);
		}
		
		// -- Get used quantity box of product pick list --
		$quantity_box_used = 0;
		$this->CI->db
			->select_if_null("SUM(ispd.quantity_box)", 0, 'quantity_box')
			->from('m_inventory_shipmentdetails ispd')
			->join('m_inventory_pickingdetails ipgd', "ipgd.id = ispd.m_inventory_pickingdetail_id")
			->join('m_inventory_picklistdetails ipld', "ipld.id = ipgd.m_inventory_picklistdetail_id")
			->join('c_orderoutdetails ood', "ood.id = ipld.c_orderoutdetail_id")
			->join('c_orderouts oo', "oo.id = ood.c_orderout_id")
			->where('ipgd.m_inventory_picking_id', $m_inventory_picking->id);
		$this->shipmentdetail_criteria_query($data, 'ipld', 'm_inventory_picklistdetails');
		$this->shipmentdetail_criteria_query($data, 'ipgd', 'm_inventory_pickingdetails');
		$this->CI->lib_custom->project_query_filter('oo.c_project_id', $c_project_ids);
		$table = $this->CI->db
			->get();
		if ($table->num_rows() > 0)
		{
			$table_record = $table->first_row();
			$quantity_box_used = $table_record->quantity_box;
		}
		
		$quantity_box_picking = 0;
		$this->CI->db
			->select_if_null("SUM(ipgd.quantity_box)", 0, 'quantity_box')
			->from('m_inventory_pickingdetails ipgd')
			->join('m_inventory_picklistdetails ipld', "ipld.id = ipgd.m_inventory_picklistdetail_id")
			->join('c_orderoutdetails ood', "ood.id = ipld.c_orderoutdetail_id")
			->join('c_orderouts oo', "oo.id = ood.c_orderout_id")
			->where('ipgd.m_inventory_picking_id', $m_inventory_picking->id);
		$this->shipmentdetail_criteria_query($data, 'ipld', 'm_inventory_picklistdetails');
		$this->shipmentdetail_criteria_query($data, 'ipgd', 'm_inventory_pickingdetails');
		$this->CI->lib_custom->project_query_filter('oo.c_project_id', $c_project_ids);
		$table = $this->CI->db
			->get();
		if ($table->num_rows() > 0)
		{
			$table_record = $table->first_row();
			$quantity_box_picking = $table_record->quantity_box;
		}
		
		// -- Validate quantity box --
		$quantity_box_free = $quantity_box_picking - $quantity_box_used;
		
		if ($quantity_box_free <= 0)
			throw new Exception("No box quantity existing on picking ".$m_inventory_picking->code.".");
		
		if ($shipment_quantity_box > $quantity_box_free)
			throw new Exception("Box quantity is too large. The maximum box quantity existing on picking ".$m_inventory_picking->code." is ".$quantity_box_free.".");
		
		if ($shipment_quantity_box == 0)
			$shipment_quantity_box = $quantity_box_free;
		
		// -- Get Picking Detail to Adds --
		$quantity_box_shipment_total = 0;
		$quantity_box_shipment_added_total = 0;
		$this->CI->db
			->select("ipgd.id, ipgd.quantity_box, ipgd.quantity, inv.quantity_per_box")
			->from('m_inventory_pickingdetails ipgd')
			->join('m_inventory_picklistdetails ipld', "ipld.id = ipgd.m_inventory_picklistdetail_id")
			->join('m_inventories inv', "inv.id = ipld.m_inventory_id")
			->join('c_orderoutdetails ood', "ood.id = ipld.c_orderoutdetail_id")
			->join('c_orderouts oo', "oo.id = ood.c_orderout_id")
			->where('ipgd.m_inventory_picking_id', $m_inventory_picking->id);
		$this->shipmentdetail_criteria_query($data, 'ipld', 'm_inventory_picklistdetails');
		$this->shipmentdetail_criteria_query($data, 'ipgd', 'm_inventory_pickingdetails');
		$this->CI->lib_custom->project_query_filter('oo.c_project_id', $c_project_ids);
		$table = $this->CI->db
			->order_by('ipgd.id', 'asc')
			->get();
		$m_inventory_pickingdetails = $table->result();
		foreach ($m_inventory_pickingdetails as $m_inventory_pickingdetail)
		{
			$quantity_box_used = 0;
			$table = $this->CI->db
				->select_if_null("SUM(quantity_box)", 0, 'quantity_box')
				->from('m_inventory_shipmentdetails')
				->where('m_inventory_pickingdetail_id', $m_inventory_pickingdetail->id)
				->get();
			if ($table->num_rows() > 0)
			{
				$m_inventory_shipmentdetail = $table->first_row();
				$quantity_box_used = $m_inventory_shipmentdetail->quantity_box;
			}
			
			$quantity_box_add = $m_inventory_pickingdetail->quantity_box - $quantity_box_used;
			if ($quantity_box_add <= 0)
				continue;
			
			$quantity_box_shipment_total += $quantity_box_add;
			if ($quantity_box_shipment_total > $shipment_quantity_box)
			{
				$quantity_box_add -= $quantity_box_shipment_total - $shipment_quantity_box;
			}
			
			$data->m_inventory_pickingdetail_id = $m_inventory_pickingdetail->id;
			$data->quantity_box = $quantity_box_add;
			
			if ($m_inventory_pickingdetail->quantity_box > 1)
			{
				$data->quantity = ($m_inventory_pickingdetail->quantity / $m_inventory_pickingdetail->quantity_box) * $quantity_box_add;
				if ($m_inventory_pickingdetail->quantity_per_box > 0)
				{
					if ($m_inventory_pickingdetail->quantity_per_box == ($m_inventory_pickingdetail->quantity / $m_inventory_pickingdetail->quantity_box))
						$data->quantity = $m_inventory_pickingdetail->quantity_per_box * $quantity_box_add;
				}
			}
			else
				$data->quantity = $m_inventory_pickingdetail->quantity;
			
			$this->shipmentdetail_add(clone $data, $created_by);
			
			$quantity_box_shipment_added_total += $quantity_box_add;
			if ($quantity_box_shipment_added_total >= $shipment_quantity_box)
				break;
		}
		
		if ($quantity_box_shipment_added_total != $shipment_quantity_box)
			throw new Exception("Shipment ".$m_inventory_shipment->code." with box quantity ".$shipment_quantity_box." only ".$quantity_box_shipment_added_total." existed.");
	}
	
	public function shipmentdetail_remove_by_properties($data, $removed_by = NULL)
	{
		$c_project_ids = $this->CI->lib_custom->project_get_ids($removed_by);
		
		$m_inventory_shipment = new M_inventory_shipment();
		if (property_exists($data, 'm_inventory_shipment_id'))
		{
			$m_inventory_shipment = new M_inventory_shipment($data->m_inventory_shipment_id);
		}
		$m_inventory_picking = new M_inventory_picking();
		if (property_exists($data, 'm_inventory_picking_id'))
		{
			$m_inventory_picking = new M_inventory_picking($data->m_inventory_picking_id);
		}
		
		// -- Get Shipment Details --
		$this->CI->db
			->select("ispd.id")
			->from('m_inventory_shipmentdetails ispd')
			->join('m_inventory_pickingdetails ipgd', "ipgd.id = ispd.m_inventory_pickingdetail_id")
			->join('m_inventory_picklistdetails ipld', "ipld.id = ipgd.m_inventory_picklistdetail_id")
			->join('c_orderoutdetails ood', "ood.id = ipld.c_orderoutdetail_id")
			->join('c_orderouts oo', "oo.id = ood.c_orderout_id")
			->where('ispd.m_inventory_shipment_id', $m_inventory_shipment->id)
			->where('ipgd.m_inventory_picking_id', $m_inventory_picking->id);
		$this->shipmentdetail_criteria_query($data, 'ipld', 'm_inventory_picklistdetails');
		$this->shipmentdetail_criteria_query($data, 'ispd', 'm_inventory_shipmentdetails');
		$this->CI->lib_custom->project_query_filter('oo.c_project_id', $c_project_ids);
		$table = $this->CI->db
			->get();
		if ($table->num_rows() == 0)
		{
			throw new Exception("Criteria in shipment ".$m_inventory_shipment->code." not found.");
		}
		
		// -- Remove Shipment Detail Exists --
		$table_records = $table->result();
		foreach ($table_records as $table_record)
		{
			$this->shipmentdetail_remove($table_record->id, $removed_by);
		}
	}
	
	public function shipmentdetail_add($data, $created_by = NULL)
	{
		$m_inventory_shipment = new M_inventory_shipment();
		$m_inventory_pickingdetail = new M_inventory_pickingdetail();
		$m_inventory_picking = new M_inventory_picking();
		$m_inventory_picklistdetail = new M_inventory_picklistdetail();
		$c_orderout = new C_orderout();
		$quantity_box = 0;
		$quantity = 0;
		
		$m_inventory_shipmentdetail_relations = array();
		if (property_exists($data, 'm_inventory_shipment_id'))
		{
			$m_inventory_shipment = new M_inventory_shipment($data->m_inventory_shipment_id);
			$m_inventory_shipmentdetail_relations['m_inventory_shipment'] = $m_inventory_shipment;
			unset($data->m_inventory_shipment_id);
		}
		if (property_exists($data, 'm_inventory_pickingdetail_id'))
		{
			$m_inventory_pickingdetail = new M_inventory_pickingdetail($data->m_inventory_pickingdetail_id);
			$m_inventory_shipmentdetail_relations['m_inventory_pickingdetail'] = $m_inventory_pickingdetail;
			unset($data->m_inventory_pickingdetail_id);
			
			$m_inventory_picking = $m_inventory_pickingdetail->m_inventory_picking->get();
			$m_inventory_picklistdetail = $m_inventory_pickingdetail->m_inventory_picklistdetail->get();
			$c_orderoutdetail = $m_inventory_picklistdetail->c_orderoutdetail->get();
			$c_orderout = $c_orderoutdetail->c_orderout->get();
			$c_project = $c_orderout->c_project->get();
			$project_is_valid = $this->CI->lib_custom->project_is_valid($created_by, $c_project->id);
			if (!$project_is_valid)
				throw new Exception("Access denied for the project.");
		}
		if (property_exists($data, 'repacked_group'))
		{
			$data->packed_group = $data->repacked_group;
			unset($data->repacked_group);
		}
		if ($m_inventory_pickingdetail->exists())
		{
			if (empty($data->packed_group))
				$data->packed_group = $m_inventory_pickingdetail->packed_group;
		}
		if (property_exists($data, 'quantity_box'))
		{
			$quantity_box = $data->quantity_box;
		}
		if (empty($quantity_box))
			$quantity_box = $m_inventory_pickingdetail->quantity_box;
		if (property_exists($data, 'quantity'))
		{
			$quantity = $data->quantity;
		}
		if (empty($quantity))
			$quantity = $m_inventory_pickingdetail->quantity;
		
		// -- Add Shipment Detail --
		$data->quantity_box = $quantity_box;
		$data->quantity = $quantity;
		$data->created_by = $created_by;
		
		$m_inventory_shipmentdetail = new M_inventory_shipmentdetail();
		$this->set_model_fields_values($m_inventory_shipmentdetail, $data);
		$m_inventory_shipmentdetail_saved = $m_inventory_shipmentdetail->save($m_inventory_shipmentdetail_relations);
		if (!$m_inventory_shipmentdetail_saved)
			throw new Exception($m_inventory_shipmentdetail->error->string);
		
		// -- Inventory Adjust Picked to Shipment --
		if ($m_inventory_pickingdetail->exists())
		{
			$m_inventory = $m_inventory_picklistdetail->m_inventory->get();
			
			$data_m_inventory_log = new stdClass();
			$data_m_inventory_log->log_type = "SHIPMENT";
			$data_m_inventory_log->ref1_code = $m_inventory_shipment->code;
			$data_m_inventory_log->ref2_code = $c_orderout->code;
			$data_m_inventory_log->notes = 'Add Shipment';
			$this->CI->lib_inventory->ship($m_inventory->id, $m_inventory_shipmentdetail->quantity_box, $m_inventory_shipmentdetail->quantity, $created_by, $data_m_inventory_log);
		}
		
		$this->pickingdetail_generate_status_inv_shipment($m_inventory_pickingdetail->id, $created_by);
		$this->picking_generate_status_inv_shipment($m_inventory_picking->id, $created_by);
		
		return $m_inventory_shipmentdetail->id;
	}
	
	public function shipmentdetail_remove($m_inventory_shipmentdetail_id, $removed_by = NULL)
	{
		$m_inventory_shipmentdetail = new M_inventory_shipmentdetail($m_inventory_shipmentdetail_id);
		
		// -- Inventory Adjust Picked to Shipment --
		$m_inventory_pickingdetail = $m_inventory_shipmentdetail->m_inventory_pickingdetail->get();
		$m_inventory_shipment = $m_inventory_shipmentdetail->m_inventory_shipment->get();
		$m_inventory_picking = $m_inventory_pickingdetail->m_inventory_picking->get();
		$m_inventory_picklistdetail = $m_inventory_pickingdetail->m_inventory_picklistdetail->get();
		$m_inventory = $m_inventory_picklistdetail->m_inventory->get();
		$c_orderoutdetail = $m_inventory_picklistdetail->c_orderoutdetail->get();
		$c_orderout = $c_orderoutdetail->c_orderout->get();
		$c_project = $c_orderout->c_project->get();
		$project_is_valid = $this->CI->lib_custom->project_is_valid($removed_by, $c_project->id);
		if (!$project_is_valid)
			throw new Exception("Access denied for the project.");
		
		$data_m_inventory_log = new stdClass();
		$data_m_inventory_log->log_type = "SHIPMENT";
		$data_m_inventory_log->ref1_code = $m_inventory_shipment->code;
		$data_m_inventory_log->ref2_code = $c_orderout->code;
		$data_m_inventory_log->notes = 'Remove Shipment';
		$this->CI->lib_inventory->ship($m_inventory->id, $m_inventory_shipmentdetail->quantity_box * -1, $m_inventory_shipmentdetail->quantity * -1, $removed_by, $data_m_inventory_log);
		
		// -- Remove Shipment Detail --
		if (!$m_inventory_shipmentdetail->delete())
			throw new Exception($m_inventory_shipmentdetail->error->string);
		
		$this->pickingdetail_generate_status_inv_shipment($m_inventory_pickingdetail->id, $removed_by);
		$this->picking_generate_status_inv_shipment($m_inventory_picking->id, $removed_by);
		
		return $m_inventory_shipmentdetail_id;
	}
	
	private function shipmentdetail_criteria_query($data, $table_alias, $table_name = 'm_inventory_picklistdetails')
	{
		$fields = array();
		if ($table_name == 'm_inventory_picklistdetails')
		{
			$fields[] = 'm_inventory_picklist_id';
			$fields[] = 'm_grid_id';
			$fields[] = 'm_product_id';
			$fields[] = 'pallet';
			$fields[] = 'barcode';
			$fields[] = 'carton_no';
			$fields[] = 'lot_no';
			$fields[] = 'condition';
		}
		if ($table_name == 'm_inventory_pickingdetails' || $table_name == 'm_inventory_shipmentdetails')
		{
			$fields[] = 'packed_group';
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
}