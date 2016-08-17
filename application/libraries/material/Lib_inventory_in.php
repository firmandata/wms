<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class lib_inventory_in extends Lib_general
{
	protected $CI;
	
	function __construct()
    {
		$this->CI =& get_instance();
		
		$this->CI->load->library('material/lib_inventory');
		$this->CI->load->library('custom/lib_custom');
		$this->CI->load->library('custom/lib_custom_inventory');
	}
	
	/* -------------------- */
	/* -- RECEIVE REGION -- */
	/* -------------------- */
	
	public function receive_add($data, $created_by = NULL)
	{
		$data->created_by = $created_by;
		
		$m_inventory_receive = new M_inventory_receive();
		$this->set_model_fields_values($m_inventory_receive, $data);
		$m_inventory_receive_saved = $m_inventory_receive->save();
		if (!$m_inventory_receive_saved)
			throw new Exception($m_inventory_receive->error->string);
		
		// -- Update Status --
		$this->receive_generate_status_inv_inbound($m_inventory_receive->id, $created_by);
		
		return $m_inventory_receive->id;
	}
	
	public function receive_update($m_inventory_receive_id, $data, $updated_by = NULL)
	{
		$data->updated_by = $updated_by;
		
		$m_inventory_receive = new M_inventory_receive($m_inventory_receive_id);
		$this->set_model_fields_values($m_inventory_receive, $data);
		$m_inventory_receive_saved = $m_inventory_receive->save();
		if (!$m_inventory_receive_saved)
			throw new Exception($m_inventory_receive->error->string);
		
		// -- Update Status --
		$this->receive_generate_status_inv_inbound($m_inventory_receive->id, $updated_by);
		
		// -- Update Inventory's received date --
		$table = $this->CI->db
			->select('iid.m_inventory_id')
			->from('m_inventory_receivedetails ird')
			->join('m_inventory_inbounddetails iid', "iid.m_inventory_receivedetail_id = ird.id")
			->where('ird.m_inventory_receive_id', $m_inventory_receive->id)
			->get();
		$records = $table->result();
		foreach ($records as $record)
		{
			$this->CI->db
				->set('received_date', $m_inventory_receive->receive_date)
				->set('updated_by', $updated_by)
				->set('updated', date('Y-m-d H:i:s'))
				->where('id', $record->m_inventory_id)
				->update('m_inventories');
		}
		
		return $m_inventory_receive_id;
	}
	
	public function receive_remove($m_inventory_receive_id, $removed_by = NULL)
	{
		$m_inventory_receive = new M_inventory_receive($m_inventory_receive_id);
		
		// -- Remove Receive Details --
		foreach ($m_inventory_receive->m_inventory_receivedetail->get() as $m_inventory_receivedetail)
		{
			$this->receivedetail_remove($m_inventory_receivedetail->id, $removed_by);
		}
		
		// -- Remove Receive --
		if (!$m_inventory_receive->delete())
			throw new Exception($m_inventory_receive->error->string);
		
		return $m_inventory_receive_id;
	}
	
	public function receive_generate_status_inv_inbound($m_inventory_receive_id, $generate_by = NULL)
	{
		$status = 'NO INBOUND';
		
		$m_inventory_receive = new M_inventory_receive($m_inventory_receive_id);
		
		$table = $this->CI->db
			->select_if_null("SUM(ird.quantity_box)", 0, 'quantity_box')
			->select_if_null("SUM(ird.quantity)", 0, 'quantity')
			->select_if_null("SUM(iid.quantity_box)", 0, 'iid_quantity_box')
			->select_if_null("SUM(iid.quantity)", 0, 'iid_quantity')
			->from('m_inventory_receivedetails ird')
			->join("(SELECT   m_inventory_receivedetail_id "
				  ."	    , ". $this->CI->db->if_null("SUM(quantity_box)", 0) ." quantity_box "
				  ."	    , ". $this->CI->db->if_null("SUM(quantity)", 0) ." quantity "
				  ."   FROM m_inventory_inbounddetails "
				  ."  GROUP BY m_inventory_receivedetail_id"
				  .") iid"
				, "iid.m_inventory_receivedetail_id = ird.id"
				, 'left'
			)
			->where('ird.m_inventory_receive_id', $m_inventory_receive_id)
			->get();
		if ($table->num_rows() > 0)
		{
			$record = $table->first_row();
			
			if (	$record->iid_quantity_box == 0
				&&	$record->iid_quantity == 0)
			{
				$status = 'NO INBOUND';
			}
			elseif (	$record->quantity_box == $record->iid_quantity_box
					&&	$record->quantity == $record->iid_quantity)
			{
				$status = 'COMPLETE';
			}
			elseif (	$record->quantity_box != $record->iid_quantity_box
					&&	$record->quantity != $record->iid_quantity)
			{
				$status = 'INCOMPLETE';
			}
			elseif ($record->quantity_box != $record->iid_quantity_box)
			{
				$status = 'INCOMPLETE BOX';
			}
			elseif ($record->quantity != $record->iid_quantity)
			{
				$status = 'INCOMPLETE QUANTITY';
			}
		}
		
		$data = new stdClass();
		$data->status_inventory_inbound = $status;
		$data->updated_by = $generate_by;
		
		$this->set_model_fields_values($m_inventory_receive, $data);
		$m_inventory_receive_saved = $m_inventory_receive->save();
		if (!$m_inventory_receive_saved)
			throw new Exception($m_inventory_receive->error->string);
	}
	
	public function receivedetail_add($data, $created_by = NULL)
	{
		$m_inventory_receive = new M_inventory_receive();
		$c_orderindetail = new C_orderindetail();
		$m_grid = new M_grid();
		$c_orderin = new C_orderin();
		$quantity_box = 0;
		
		$receivedetail_relations = array();
		if (property_exists($data, 'm_inventory_receive_id'))
		{
			$m_inventory_receive = new M_inventory_receive($data->m_inventory_receive_id);
			$receivedetail_relations['m_inventory_receive'] = $m_inventory_receive;
			unset($data->m_inventory_receive_id);
		}
		if (property_exists($data, 'c_orderindetail_id'))
		{
			$c_orderindetail = new C_orderindetail($data->c_orderindetail_id);
			$receivedetail_relations['c_orderindetail'] = $c_orderindetail;
			unset($data->c_orderindetail_id);
			
			// -- Validate the project --
			$c_orderin = $c_orderindetail->c_orderin->get();
			$c_project = $c_orderin->c_project->get();
			$project_is_valid = $this->CI->lib_custom->project_is_valid($created_by, $c_project->id);
			if (!$project_is_valid)
				throw new Exception("Access denied for the project.");
		}
		if (property_exists($data, 'm_grid_id'))
		{
			$m_grid = new M_grid($data->m_grid_id);
			$receivedetail_relations['m_grid'] = $m_grid;
			unset($data->m_grid_id);
		}
		if (property_exists($data, 'quantity_box'))
		{
			$quantity_box = $data->quantity_box;
		}
		
		// -- Validate location --
		$m_product = $c_orderindetail->m_product->get();
		$m_productgroup_product = $m_product->m_productgroup->get();
		if ($m_productgroup_product->exists())
		{
			$m_productgroup_grid = $m_grid->m_productgroup->get();
			if ($m_productgroup_grid->exists())
			{
				if ($m_productgroup_product->id != $m_productgroup_grid->id)
					throw new Exception("Invalid location placed.");
			}
		}
		
		// -- Get used quantity box in other receive detail --
		$quantity_box_other_used = 0;
		$table = $this->CI->db
			->select_if_null("SUM(ird.quantity_box)", 0, 'quantity_box')
			->from('m_inventory_receivedetails ird')
			->where('ird.c_orderindetail_id', $c_orderindetail->id)
			->get();
		if ($table->num_rows() > 0)
		{
			$table_record = $table->first_row();
			$quantity_box_other_used = $table_record->quantity_box;
		}
		$quantity_box_existing = $c_orderindetail->quantity_box - $quantity_box_other_used;
		if ($quantity_box > $quantity_box_existing)
		{
			throw new Exception("Receive box quantity ".$quantity_box." is not enough for order in detail box quantity ".$quantity_box_existing.".");
		}
		
		$data->created_by = $created_by;
		
		$m_inventory_receivedetail = new M_inventory_receivedetail();
		$this->set_model_fields_values($m_inventory_receivedetail, $data);
		$m_inventory_receivedetail_saved = $m_inventory_receivedetail->save($receivedetail_relations);
		if (!$m_inventory_receivedetail_saved)
			throw new Exception($m_inventory_receivedetail->error->string);
		
		// -- Update Status --
		$this->receive_generate_status_inv_inbound($m_inventory_receive->id, $created_by);
		$this->receivedetail_generate_status_inv_inbound($m_inventory_receivedetail->id, $created_by);
		$this->CI->load->library('core/lib_order');
		$this->CI->lib_order->orderindetail_generate_status_inv_receive($c_orderindetail->id, $created_by);
		$this->CI->lib_order->orderin_generate_status_inv_receive($c_orderin->id, $created_by);
		
		return $m_inventory_receivedetail->id;
	}
	
	public function receivedetail_update($m_inventory_receivedetail_id, $data, $updated_by = NULL)
	{
		$m_inventory_receivedetail = new M_inventory_receivedetail($m_inventory_receivedetail_id);
		$m_inventory_receive = $m_inventory_receivedetail->m_inventory_receive->get();
		$c_orderindetail = $m_inventory_receivedetail->c_orderindetail->get();
		$c_orderindetail_id = $c_orderindetail->id;
		$m_grid = $m_inventory_receivedetail->m_grid->get();
		$quantity_box = $m_inventory_receivedetail->quantity_box;
		
		$receivedetail_relations = array();
		if (property_exists($data, 'm_inventory_receive_id'))
		{
			$m_inventory_receive = new M_inventory_receive($data->m_inventory_receive_id);
			$receivedetail_relations['m_inventory_receive'] = $m_inventory_receive;
			unset($data->m_inventory_receive_id);
		}
		if (property_exists($data, 'c_orderindetail_id'))
		{
			$receivedetail_relations['c_orderindetail'] = $c_orderindetail;
			unset($data->c_orderindetail_id);
		}
		if (property_exists($data, 'm_grid_id'))
		{
			$receivedetail_relations['m_grid'] = $m_grid;
			unset($data->m_grid_id);
		}
		if (property_exists($data, 'quantity_box'))
		{
			$quantity_box = $data->quantity_box;
		}
		
		// -- Validate the project --
		$c_orderin = $c_orderindetail->c_orderin->get();
		$c_project = $c_orderin->c_project->get();
		$project_is_valid = $this->CI->lib_custom->project_is_valid($updated_by, $c_project->id);
		if (!$project_is_valid)
			throw new Exception("Access denied for the project.");
		
		// -- Get used quantity box in other receive detail --
		$quantity_box_other_used = 0;
		$table = $this->CI->db
			->select_if_null("SUM(ird.quantity_box)", 0, 'quantity_box')
			->from('m_inventory_receivedetails ird')
			->where('ird.c_orderindetail_id', $c_orderindetail->id)
			->where('ird.id <>', $m_inventory_receivedetail_id)
			->get();
		if ($table->num_rows() > 0)
		{
			$table_record = $table->first_row();
			$quantity_box_other_used = $table_record->quantity_box;
		}
		$quantity_box_existing = $c_orderindetail->quantity_box - $quantity_box_other_used;
		if ($quantity_box > $quantity_box_existing)
		{
			throw new Exception("Receive quantity box ".$quantity_box." is not enough for order in quantity box ".$quantity_box_existing.".");
		}
		
		// -- Get used quantity box in inbound details --
		$quantity_box_used = 0;
		$table = $this->CI->db
			->select_if_null("SUM(iid.quantity_box)", 0, 'quantity_box')
			->from('m_inventory_inbounddetails iid')
			->where('iid.m_inventory_receivedetail_id', $m_inventory_receivedetail->id)
			->get();
		if ($table->num_rows() > 0)
		{
			$table_record = $table->first_row();
			$quantity_box_used = $table_record->quantity_box;
		}
		
		// -- Validate Quantity Box --
		if ($quantity_box < $quantity_box_used)
			throw new Exception("Product ".$m_product->code." can't be decreased because it has been used ".$quantity_box_used." in inbound.");
		
		$data->updated_by = $updated_by;
		
		$this->set_model_fields_values($m_inventory_receivedetail, $data);
		$m_inventory_receivedetail_saved = $m_inventory_receivedetail->save($receivedetail_relations);
		if (!$m_inventory_receivedetail_saved)
			throw new Exception($m_inventory_receivedetail->error->string);
		
		// -- Update Status --
		$this->receive_generate_status_inv_inbound($m_inventory_receive->id, $updated_by);
		$this->receivedetail_generate_status_inv_inbound($m_inventory_receivedetail->id, $updated_by);
		$this->CI->load->library('core/lib_order');
		$this->CI->lib_order->orderindetail_generate_status_inv_receive($c_orderindetail->id, $updated_by);
		$this->CI->lib_order->orderin_generate_status_inv_receive($c_orderin->id, $updated_by);
		
		return $m_inventory_receivedetail_id;
	}
	
	public function receivedetail_remove($m_inventory_receivedetail_id, $removed_by = NULL)
	{
		$m_inventory_receivedetail = new M_inventory_receivedetail($m_inventory_receivedetail_id);
		$m_inventory_receive = $m_inventory_receivedetail->m_inventory_receive->get();
		
		// -- Validate the project --
		$c_orderindetail = $m_inventory_receivedetail->c_orderindetail->get();
		$c_orderin = $c_orderindetail->c_orderin->get();
		$c_project = $c_orderin->c_project->get();
		$project_is_valid = $this->CI->lib_custom->project_is_valid($removed_by, $c_project->id);
		if (!$project_is_valid)
			throw new Exception("Access denied for the project.");
			
		// -- Remove Inbound Details --
		foreach ($m_inventory_receivedetail->m_inventory_inbounddetail->get() as $m_inventory_inbounddetail)
		{
			$this->inbounddetail_remove($m_inventory_inbounddetail->id, $removed_by);
		}
		
		// -- Remove Forecast Details --
		$cus_m_inventory_forecast_id = 0;
		foreach ($m_inventory_receivedetail->cus_m_inventory_forecastdetail->get() as $cus_m_inventory_forecastdetail)
		{
			$cus_m_inventory_forecast = $cus_m_inventory_forecastdetail->cus_m_inventory_forecast->get();
			$cus_m_inventory_forecast_id = $cus_m_inventory_forecast->id;
			$this->CI->lib_custom_inventory->forecastdetail_remove($cus_m_inventory_forecastdetail->id, $removed_by);
		}
		
		// -- Remove Forecast --
		$cus_m_inventory_forecastdetails_num = 0;
		$table = $this->CI->db
			->select_if_null("COUNT(ifcd.id)", 0, 'cus_m_inventory_forecastdetails_num')
			->from('cus_m_inventory_forecastdetails ifcd')
			->join('m_inventory_receivedetails ird', "ird.id = ifcd.m_inventory_receivedetail_id")
			->where('ird.m_inventory_receive_id', $m_inventory_receive->id)
			->get();
		if ($table->num_rows() > 0)
		{
			$table_record = $table->first_row();
			$cus_m_inventory_forecastdetails_num = $table_record->cus_m_inventory_forecastdetails_num;
		}
		if ($cus_m_inventory_forecastdetails_num == 0 && $cus_m_inventory_forecast_id > 0)
		{
			$this->CI->lib_custom_inventory->forecast_remove($cus_m_inventory_forecast_id, $removed_by);
		}
		
		// -- Remove Receive Detail --
		if (!$m_inventory_receivedetail->delete())
			throw new Exception($m_inventory_receivedetail->error->string);
		
		// -- Update Status --
		$this->receive_generate_status_inv_inbound($m_inventory_receive->id, $removed_by);
		$this->CI->load->library('core/lib_order');
		$this->CI->lib_order->orderindetail_generate_status_inv_receive($c_orderindetail->id, $removed_by);
		$this->CI->lib_order->orderin_generate_status_inv_receive($c_orderin->id, $removed_by);
		
		return $m_inventory_receivedetail_id;
	}
	
	public function receivedetail_generate_status_inv_inbound($m_inventory_receivedetail_id, $generate_by = NULL)
	{
		$status = 'NO INBOUND';
		
		$m_inventory_receivedetail = new M_inventory_receivedetail($m_inventory_receivedetail_id);
		
		$table = $this->CI->db
			->select_if_null("SUM(iid.quantity_box)", 0, 'quantity_box')
			->select_if_null("SUM(iid.quantity)", 0, 'quantity')
			->from('m_inventory_inbounddetails iid')
			->where('iid.m_inventory_receivedetail_id', $m_inventory_receivedetail_id)
			->get();
		if ($table->num_rows() > 0)
		{
			$record = $table->first_row();
			if (	$record->quantity_box == 0
				&&	$record->quantity == 0)
			{
				$status = 'NO INBOUND';
			}
			elseif (	$m_inventory_receivedetail->quantity_box == $record->quantity_box
					&&	$m_inventory_receivedetail->quantity == $record->quantity)
			{
				$status = 'COMPLETE';
			}
			elseif (	$m_inventory_receivedetail->quantity_box != $record->quantity_box
					&&	$m_inventory_receivedetail->quantity != $record->quantity)
			{
				$status = 'INCOMPLETE';
			}
			elseif ($m_inventory_receivedetail->quantity_box != $record->quantity_box)
			{
				$status = 'INCOMPLETE BOX';
			}
			elseif ($m_inventory_receivedetail->quantity != $record->quantity)
			{
				$status = 'INCOMPLETE QUANTITY';
			}
		}
		
		$data = new stdClass();
		$data->status_inventory_inbound = $status;
		$data->updated_by = $generate_by;
		
		$this->set_model_fields_values($m_inventory_receivedetail, $data);
		$m_inventory_receivedetail_saved = $m_inventory_receivedetail->save();
		if (!$m_inventory_receivedetail_saved)
			throw new Exception($m_inventory_receivedetail->error->string);
	}
	
	public function receive_generate_inbound($data, $m_inventory_receive_id, $generate_by = NULL)
	{
		$c_project_ids = $this->CI->lib_custom->project_get_ids($generate_by);
		
		$this->CI->db
			->select("ird.id, ird.condition, ird.m_grid_id")
			->select("ird.quantity_box - ". $this->CI->db->if_null("inbd.quantity_box", 0) ." quantity_box_free", FALSE)
			->select("ird.quantity - ". $this->CI->db->if_null("inbd.quantity", 0) ." quantity_free", FALSE)
			->from('m_inventory_receivedetails ird')
			->join('c_orderindetails oid', "oid.id = ird.c_orderindetail_id")
			->join('c_orderins oi', "oi.id = oid.c_orderin_id")
			->join(
				 "(SELECT m_inventory_receivedetail_id, "
				."		  " . $this->CI->db->if_null("SUM(quantity_box)", 0) . " quantity_box, "
				."		  " . $this->CI->db->if_null("SUM(quantity)", 0) . " quantity "
				."	 FROM m_inventory_inbounddetails "
				."  GROUP BY m_inventory_receivedetail_id "
				.") inbd", 
				"inbd.m_inventory_receivedetail_id = ird.id", 'left')
			->where('ird.m_inventory_receive_id', $m_inventory_receive_id)
			->where("ird.quantity_box - ". $this->CI->db->if_null("inbd.quantity_box", 0) ." > 0", NULL, FALSE);
		$this->CI->lib_custom->project_query_filter('oi.c_project_id', $c_project_ids);
		$query = $this->CI->db->get();
		if ($query->num_rows() == 0)
			throw new Exception("No quantity to inbound.");
		
		$m_inventory_receivedetails = $query->result();
		
		$m_inventory_inbound_id = $this->inbound_add($data, $generate_by);
		
		foreach ($m_inventory_receivedetails as $m_inventory_receivedetail_idx=>$m_inventory_receivedetail)
		{
			$m_inventory_inbound_data = new stdClass();
			$m_inventory_inbound_data->m_inventory_inbound_id = $m_inventory_inbound_id;
			$m_inventory_inbound_data->m_inventory_receivedetail_id = $m_inventory_receivedetail->id;
			$m_inventory_inbound_data->quantity_box = $m_inventory_receivedetail->quantity_box_free;
			$m_inventory_inbound_data->quantity = $m_inventory_receivedetail->quantity_free;
			$m_inventory_inbound_data->condition = $m_inventory_receivedetail->condition;
			$m_inventory_inbound_data->pallet = generate_code_number("PLT". date('ymd-'), NULL, 5);
			$m_inventory_inbound_data->m_grid_id = $m_inventory_receivedetail->m_grid_id;
			
			$this->inbounddetail_add($m_inventory_inbound_data, $generate_by);
		}
		
		return $m_inventory_inbound_id;
	}
	
	/* -------------------- */
	/* -- INBOUND REGION -- */
	/* -------------------- */
	
	public function inbound_add($data, $created_by = NULL)
	{
		$data->created_by = $created_by;
		
		$m_inventory_inbound = new M_inventory_inbound();
		$this->set_model_fields_values($m_inventory_inbound, $data);
		$m_inventory_inbound_saved = $m_inventory_inbound->save();
		if (!$m_inventory_inbound_saved)
			throw new Exception($m_inventory_inbound->error->string);
		
		return $m_inventory_inbound->id;
	}
	
	public function inbound_update($m_inventory_inbound_id, $data, $updated_by = NULL)
	{
		$data->updated_by = $updated_by;
		
		$m_inventory_inbound = new M_inventory_inbound($m_inventory_inbound_id);
		$this->set_model_fields_values($m_inventory_inbound, $data);
		$m_inventory_inbound_saved = $m_inventory_inbound->save();
		if (!$m_inventory_inbound_saved)
			throw new Exception($m_inventory_inbound->error->string);
		
		return $m_inventory_inbound_id;
	}
	
	public function inbound_remove($m_inventory_inbound_id, $removed_by = NULL)
	{
		$m_inventory_inbound = new M_inventory_inbound($m_inventory_inbound_id);
		
		// -- Remove Inbound Details--
		foreach ($m_inventory_inbound->m_inventory_inbounddetail->get() as $m_inventory_inbounddetail)
		{
			$this->inbounddetail_remove($m_inventory_inbounddetail->id, $removed_by);
		}
		
		// -- Remove Inbound --
		if (!$m_inventory_inbound->delete())
			throw new Exception($m_inventory_inbound->error->string);
		
		return $m_inventory_inbound_id;
	}
	
	public function inbounddetail_add($data, $created_by = NULL)
	{
		$m_inventory_inbound = new M_inventory_inbound();
		$m_inventory_receivedetail = new M_inventory_receivedetail();
		$m_inventory_receive = new M_inventory_receive();
		$c_orderin = new C_orderin();
		$m_grid = new M_grid();
		$quantity_box = 0;
		$quantity = 0;
		
		$inventory_quantity_box_count = 1;
		
		$inbounddetail_relations = array();
		if (property_exists($data, 'm_inventory_inbound_id'))
		{
			$m_inventory_inbound = new M_inventory_inbound($data->m_inventory_inbound_id);
			$inbounddetail_relations['m_inventory_inbound'] = $m_inventory_inbound;
			unset($data->m_inventory_inbound_id);
		}
		if (property_exists($data, 'm_inventory_receivedetail_id'))
		{
			$m_inventory_receivedetail = new M_inventory_receivedetail($data->m_inventory_receivedetail_id);
			$inbounddetail_relations['m_inventory_receivedetail'] = $m_inventory_receivedetail;
			unset($data->m_inventory_receivedetail_id);
		}
		if (property_exists($data, 'm_grid_id'))
		{
			$m_grid = new M_grid($data->m_grid_id);
			$inbounddetail_relations['m_grid'] = $m_grid;
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
		
		// -- For inventory add --
		$c_orderindetail = $m_inventory_receivedetail->c_orderindetail->get();
		$m_inventory_receive = $m_inventory_receivedetail->m_inventory_receive->get();
		$m_product = $c_orderindetail->m_product->get();
		$c_orderin = $c_orderindetail->c_orderin->get();
		$c_project = $c_orderin->c_project->get();
		
		// -- Validate location --
		$m_productgroup_product = $m_product->m_productgroup->get();
		if ($m_productgroup_product->exists())
		{
			$m_productgroup_grid = $m_grid->m_productgroup->get();
			if ($m_productgroup_grid->exists())
			{
				if ($m_productgroup_product->id != $m_productgroup_grid->id)
					throw new Exception("Invalid location placed.");
			}
		}
		
		// -- Validate the project --
		$project_is_valid = $this->CI->lib_custom->project_is_valid($created_by, $c_project->id);
		if (!$project_is_valid)
			throw new Exception("Access denied for the project.");
		
		$data_m_inventory = $data;
		$data_m_inventory->m_product_id = $m_product->id;
		$data_m_inventory->m_grid_id = $m_grid->id;
		$data_m_inventory->c_project_id = $c_project->id;
		$data_m_inventory->received_date = $m_inventory_receive->receive_date;
		$data_m_inventory->price_buy = $c_orderindetail->price;
		
		if ($m_product->netto == 0 && $quantity_box > 0)
		{
			$inventory_quantity_box_count = $quantity_box;
			$data_m_inventory->quantity_box = 1;
			$data_m_inventory->quantity = $data_m_inventory->quantity / $quantity_box;
		}
		
		$data_m_inventory_log = new stdClass();
		$data_m_inventory_log->log_type = "INBOUND";
		$data_m_inventory_log->ref1_code = $m_inventory_inbound->code;
		$data_m_inventory_log->ref2_code = $c_orderin->code;
		$data_m_inventory_log->notes = 'Add Inbound';
		
		$m_inventory_inbounddetail_ids = array();
		for ($inventory_quantity_box_counter = 0; $inventory_quantity_box_counter < $inventory_quantity_box_count; $inventory_quantity_box_counter++)
		{
			$m_inventory_id = $this->CI->lib_inventory->add(clone $data_m_inventory, $created_by, $data_m_inventory_log);
			
			// -- Inbound Detail Add --
			$m_inventory = new M_inventory($m_inventory_id);
			$inbounddetail_relations['m_inventory'] = $m_inventory;
			
			$data->created_by = $created_by;
			
			$m_inventory_inbounddetail = new M_inventory_inbounddetail();
			$this->set_model_fields_values($m_inventory_inbounddetail, clone $data);
			$m_inventory_inbounddetail_saved = $m_inventory_inbounddetail->save($inbounddetail_relations);
			if (!$m_inventory_inbounddetail_saved)
				throw new Exception($m_inventory_inbounddetail->error->string);
			
			// -- Update Status --
			$this->receive_generate_status_inv_inbound($m_inventory_receive->id, $created_by);
			$this->receivedetail_generate_status_inv_inbound($m_inventory_receivedetail->id, $created_by);
			
			$this->CI->lib_inventory->verify_pallet_grid($m_inventory->pallet, $m_grid->id);
			
			// -- Verify Grid Usage Forecast Request --
			if ($m_inventory_receivedetail->exists())
				$this->CI->lib_custom_inventory->grid_usage_verify_request_forecast($m_inventory_receivedetail->id, $created_by);
			
			$m_inventory_inbounddetail_ids[] = $m_inventory_inbounddetail->id;
		}
		
		return $m_inventory_inbounddetail_ids;
	}
	
	public function inbounddetail_remove($m_inventory_inbounddetail_id, $removed_by = NULL)
	{
		$m_inventory_inbounddetail = new M_inventory_inbounddetail($m_inventory_inbounddetail_id);
		$m_inventory_inbound = $m_inventory_inbounddetail->m_inventory_inbound->get();
		$m_inventory = $m_inventory_inbounddetail->m_inventory->get();
		$m_inventory_receivedetail = $m_inventory_inbounddetail->m_inventory_receivedetail->get();
		$m_inventory_receive = $m_inventory_receivedetail->m_inventory_receive->get();
		$c_orderindetail = $m_inventory_receivedetail->c_orderindetail->get();
		
		// -- Validate the project --
		$c_orderin = $c_orderindetail->c_orderin->get();
		$c_project = $c_orderin->c_project->get();
		$project_is_valid = $this->CI->lib_custom->project_is_valid($removed_by, $c_project->id);
		if (!$project_is_valid)
			throw new Exception("Access denied for the project.");
		
		// -- Inbound Detail Delete --
		if (!$m_inventory_inbounddetail->delete())
			throw new Exception($m_inventory_inbounddetail->error->string);
		
		// -- Update Status --
		$this->receive_generate_status_inv_inbound($m_inventory_receive->id, $removed_by);
		$this->receivedetail_generate_status_inv_inbound($m_inventory_receivedetail->id, $removed_by);
		
		// -- Inventory Delete --
		$data_m_inventory_log = new stdClass();
		$data_m_inventory_log->log_type = "INBOUND";
		$data_m_inventory_log->ref1_code = $m_inventory_inbound->code;
		$data_m_inventory_log->ref2_code = $c_orderindetail->c_orderin->get()->code;
		$data_m_inventory_log->notes = 'Remove Inbound';
		
		$m_inventory_id = $this->CI->lib_inventory->remove($m_inventory->id, $removed_by, $data_m_inventory_log);
		
		// -- Verify Grid Usage Forecast Request --
		if ($m_inventory_receivedetail->exists())
			$this->CI->lib_custom_inventory->grid_usage_verify_request_forecast($m_inventory_receivedetail->id, $removed_by);
		
		return $m_inventory_inbounddetail_id;
	}

	/* -------------------- */
	/* -- BALANCE REGION -- */
	/* -------------------- */
	
	public function balance_add($data, $created_by = NULL)
	{
		$m_inventory = new M_inventory();
		$harvest_sequence = 0;
		$m_inventory_balance_relations = array();
		if (property_exists($data, 'm_inventory_id'))
		{
			$m_inventory = new M_inventory($data->m_inventory_id);
			$m_inventory_balance_relations['m_inventory'] = $m_inventory;
			unset($data->m_inventory_id);
		}
		if (property_exists($data, 'harvest_sequence'))
		{
			$harvest_sequence = $data->harvest_sequence;
		}
		if (!property_exists($data, 'code'))
		{
			$data->code = NULL;
		}
		
		// -- Set inventory data --
		$data->m_inventory_quantity_from = $m_inventory->quantity;
		$data->m_inventory_quantity_to = $m_inventory->quantity;
		$data->m_inventory_quantity_box_from = $m_inventory->quantity_box;
		$data->m_inventory_quantity_box_to = $m_inventory->quantity_box;
		
		// -- Empty inventory quantity --
		if ($harvest_sequence == 9)
		{
			// -- Update inventory with new quantity --
			$data_m_inventory_log = new stdClass();
			$data_m_inventory_log->log_type = "BALANCE";
			$data_m_inventory_log->ref1_code = $data->code;
			$data_m_inventory_log->notes = 'Add Balance';
			$m_inventory_id = $this->CI->lib_inventory->adjust($m_inventory->id, $m_inventory->quantity_box, 0, $created_by, $data_m_inventory_log);
			
			$m_inventory = new M_inventory($m_inventory_id);
			$data->m_inventory_quantity_to = $m_inventory->quantity;
			$data->m_inventory_quantity_box_to = $m_inventory->quantity_box;
		}
		
		$data->created_by = $created_by;
		
		$m_inventory_balance = new M_inventory_balance();
		$this->set_model_fields_values($m_inventory_balance, $data);
		$m_inventory_balance_saved = $m_inventory_balance->save($m_inventory_balance_relations);
		if (!$m_inventory_balance_saved)
			throw new Exception($m_inventory_balance->error->string);
		
		return $m_inventory_balance->id;
	}
	
	public function balance_update($m_inventory_balance_id, $data, $updated_by = NULL)
	{
		$m_inventory_balance_relations = array();
		if (property_exists($data, 'm_inventory_id'))
		{
			unset($data->m_inventory_id);
		}
		if (property_exists($data, 'harvest_sequence'))
		{
			unset($data->harvest_sequence);
		}
		
		$data->updated_by = $updated_by;
		
		$m_inventory_balance = new M_inventory_balance($m_inventory_balance_id);		
		$this->set_model_fields_values($m_inventory_balance, $data);
		$m_inventory_balance_saved = $m_inventory_balance->save($m_inventory_balance_relations);
		if (!$m_inventory_balance_saved)
			throw new Exception($m_inventory_balance->error->string);
		
		return $m_inventory_balance_id;
	}
	
	public function balance_remove($m_inventory_balance_id, $removed_by = NULL)
	{
		$m_inventory_balance = new M_inventory_balance($m_inventory_balance_id);
		
		// -- Rollback inventory quantity --
		if ($m_inventory_balance->harvest_sequence == 9)
		{
			$m_inventory = $m_inventory_balance->m_inventory->get();
			
			// -- Update inventory with new quantity --
			$data_m_inventory_log = new stdClass();
			$data_m_inventory_log->log_type = "BALANCE";
			$data_m_inventory_log->ref1_code = $m_inventory_balance->code;
			$data_m_inventory_log->notes = 'Remove Balance';
			$m_inventory_id = $this->CI->lib_inventory->adjust($m_inventory->id, $m_inventory_balance->m_inventory_quantity_box_from, $m_inventory_balance->m_inventory_quantity_from, $removed_by, $data_m_inventory_log);
		}
		
		// -- Remove Balance Details --
		foreach ($m_inventory_balance->m_inventory_balancedetail->get() as $m_inventory_balancedetail)
		{
			$this->balancedetail_remove($m_inventory_balancedetail->id, $removed_by);
		}
		
		// -- Remove Balance --
		if (!$m_inventory_balance->delete())
			throw new Exception($m_inventory_balance->error->string);
		
		return $m_inventory_balance_id;
	}
	
	public function balancedetail_add($data, $created_by = NULL)
	{
		$m_inventory_balance = new M_inventory_balance();
		$m_grid = new M_grid();
		$m_product = new M_product();
		$quantity = 0;
		$pallet = NULL;
		$condition = NULL;
		$product_conditions = array_keys($this->CI->config->item('product_conditions'));
		if (!empty($product_conditions))
			$condition = $product_conditions[0];
		
		$balancedetail_relations = array();
		if (property_exists($data, 'm_inventory_balance_id'))
		{
			$m_inventory_balance = new M_inventory_balance($data->m_inventory_balance_id);
			$balancedetail_relations['m_inventory_balance'] = $m_inventory_balance;
			unset($data->m_inventory_balance_id);
		}
		if (property_exists($data, 'm_product_id'))
		{
			$m_product = new M_product($data->m_product_id);
			unset($data->m_product_id);
		}
		if (property_exists($data, 'quantity'))
		{
			$quantity = $data->quantity;
		}
		if (property_exists($data, 'pallet'))
		{
			$pallet = $data->pallet;
		}
		if (empty($pallet))
			$pallet = generate_code_number("PLT". date('ymd-'), NULL, 5);
		if (property_exists($data, 'condition'))
		{
			$condition = $data->condition;
		}
		if (!$m_inventory_balance->exists())
		{
			throw new Exception("Require inventory balance.");
		}
		$m_inventory_balance_inventory = $m_inventory_balance->m_inventory->get();
		$m_grid = $m_inventory_balance_inventory->m_grid->get();
		
		// -- For inventory add --
		$c_project = $m_inventory_balance_inventory->c_project->get();
		
		// -- Validate location --
		$m_productgroup_product = $m_product->m_productgroup->get();
		if ($m_productgroup_product->exists())
		{
			$m_productgroup_grid = $m_grid->m_productgroup->get();
			if ($m_productgroup_grid->exists())
			{
				if ($m_productgroup_product->id != $m_productgroup_grid->id)
					throw new Exception("Invalid location placed.");
			}
		}
		
		// -- Validate the project --
		$project_is_valid = $this->CI->lib_custom->project_is_valid($created_by, $c_project->id);
		if (!$project_is_valid)
			throw new Exception("Access denied for the project.");
		
		$data_m_inventory = $data;
		$data_m_inventory->m_product_id = $m_product->id;
		$data_m_inventory->m_grid_id = $m_grid->id;
		$data_m_inventory->c_project_id = $c_project->id;
		$data_m_inventory->received_date = $m_inventory_balance->balance_date;
		$data_m_inventory->pallet = $pallet;
		$data_m_inventory->condition = $condition;
		$data_m_inventory->quantity_box = 1;
		$data_m_inventory->quantity = $quantity;
		$data_m_inventory->product_size = $m_inventory_balance->product_size;
		
		$data_m_inventory_log = new stdClass();
		$data_m_inventory_log->log_type = "BALANCE";
		$data_m_inventory_log->ref1_code = $m_inventory_balance->code;
		$data_m_inventory_log->notes = 'Add Balance';
		
		$m_inventory_id = $this->CI->lib_inventory->add($data_m_inventory, $created_by, $data_m_inventory_log);
		
		// -- Balance Detail Add --
		$m_inventory = new M_inventory($m_inventory_id);
		$balancedetail_relations['m_inventory'] = $m_inventory;
		
		$data->created_by = $created_by;
		
		$m_inventory_balancedetail = new M_inventory_balancedetail();
		$this->set_model_fields_values($m_inventory_balancedetail, clone $data);
		$m_inventory_balancedetail_saved = $m_inventory_balancedetail->save($balancedetail_relations);
		if (!$m_inventory_balancedetail_saved)
			throw new Exception($m_inventory_balancedetail->error->string);
		
		$this->CI->lib_inventory->verify_pallet_grid($m_inventory->pallet, $m_grid->id);
		
		return $m_inventory_balancedetail->id;
	}
	
	public function balancedetail_remove($m_inventory_balancedetail_id, $removed_by = NULL)
	{
		$m_inventory_balancedetail = new M_inventory_balancedetail($m_inventory_balancedetail_id);
		$m_inventory_balance = $m_inventory_balancedetail->m_inventory_balance->get();
		$m_inventory = $m_inventory_balancedetail->m_inventory->get();
		
		// -- Validate the project --
		$c_project = $m_inventory->c_project->get();
		$project_is_valid = $this->CI->lib_custom->project_is_valid($removed_by, $c_project->id);
		if (!$project_is_valid)
			throw new Exception("Access denied for the project.");
		
		// -- Balance Detail Delete --
		if (!$m_inventory_balancedetail->delete())
			throw new Exception($m_inventory_balancedetail->error->string);
		
		// -- Inventory Delete --
		$data_m_inventory_log = new stdClass();
		$data_m_inventory_log->log_type = "BALANCE";
		$data_m_inventory_log->ref1_code = $m_inventory_balance->code;
		$data_m_inventory_log->notes = 'Remove Balance';
		
		$m_inventory_id = $this->CI->lib_inventory->remove($m_inventory->id, $removed_by, $data_m_inventory_log);
		
		return $m_inventory_balancedetail_id;
	}
}