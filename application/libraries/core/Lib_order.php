<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Lib_order extends Lib_general
{
	protected $CI;
	
	function __construct()
    {
		$this->CI =& get_instance();
		
		$this->CI->load->library('custom/lib_custom');
		$this->CI->load->library('material/lib_inventory_in');
		$this->CI->load->library('material/lib_inventory_out');
	}
	
	/* --------------------- */
	/* -- ORDER IN REGION -- */
	/* --------------------- */
	
	public function orderin_add($data, $created_by = NULL)
	{
		$orderin_relations = array();
		if (property_exists($data, 'c_businesspartner_id'))
		{
			$orderin_relations['c_businesspartner'] = new C_businesspartner($data->c_businesspartner_id);
			unset($data->c_businesspartner_id);
		}
		if (property_exists($data, 'c_project_id'))
		{
			$orderin_relations['c_project'] = new C_project($data->c_project_id);
			
			// -- Validate the project --
			$project_is_valid = $this->CI->lib_custom->project_is_valid($created_by, $data->c_project_id);
			if (!$project_is_valid)
				throw new Exception("Access denied for the project.");
			
			unset($data->c_project_id);
		}
		
		$data->created_by = $created_by;
		
		$c_orderin = new C_orderin();
		$this->set_model_fields_values($c_orderin, $data);
		$c_orderin_saved = $c_orderin->save($orderin_relations);
		if (!$c_orderin_saved)
			throw new Exception($c_orderin->error->string);
		
		// -- Update Status --
		$this->orderin_generate_status_inv_receive($c_orderin->id, $created_by);
		
		return $c_orderin->id;
	}
	
	public function orderin_update($c_orderin_id, $data, $updated_by = NULL)
	{
		$orderin_relations = array();
		if (property_exists($data, 'c_businesspartner_id'))
		{
			$orderin_relations['c_businesspartner'] = new C_businesspartner($data->c_businesspartner_id);
			unset($data->c_businesspartner_id);
		}
		if (property_exists($data, 'c_project_id'))
		{
			$orderin_relations['c_project'] = new C_project($data->c_project_id);
			
			// -- Validate the project --
			$project_is_valid = $this->CI->lib_custom->project_is_valid($updated_by, $data->c_project_id);
			if (!$project_is_valid)
				throw new Exception("Access denied for the project.");
			
			unset($data->c_project_id);
		}
		
		$data->updated_by = $updated_by;
		
		$c_orderin = new C_orderin($c_orderin_id);
		
		// -- Validate the project --
		$c_project = $c_orderin->c_project->get();
		if ($c_project->id)
		{
			$project_is_valid = $this->CI->lib_custom->project_is_valid($updated_by, $c_project->id);
			if (!$project_is_valid)
				throw new Exception("Access denied for the project.");
		}
		
		$this->set_model_fields_values($c_orderin, $data);
		$c_orderin_saved = $c_orderin->save($orderin_relations);
		if (!$c_orderin_saved)
			throw new Exception($c_orderin->error->string);
		
		// -- Update Status --
		$this->orderin_generate_status_inv_receive($c_orderin_id, $updated_by);
		
		// -- Update Inventory's project --
		$c_project = $c_orderin->c_project->get();
		$table = $this->CI->db
			->select('iid.m_inventory_id')
			->from('c_orderindetails oid')
			->join('m_inventory_receivedetails ird', "ird.c_orderindetail_id = oid.id")
			->join('m_inventory_inbounddetails iid', "iid.m_inventory_receivedetail_id = ird.id")
			->where('oid.c_orderin_id', $c_orderin->id)
			->get();
		$records = $table->result();
		foreach ($records as $record)
		{
			$this->CI->db
				->set('c_project_id', $c_project->id)
				->set('updated_by', $updated_by)
				->set('updated', date('Y-m-d H:i:s'))
				->where('id', $record->m_inventory_id)
				->update('m_inventories');
		}
		
		return $c_orderin_id;
	}
	
	public function orderin_remove($c_orderin_id, $removed_by = NULL)
	{
		$c_orderin = new C_orderin($c_orderin_id);
		
		// -- Validate the project --
		$c_project = $c_orderin->c_project->get();
		if ($c_project->id)
		{
			$project_is_valid = $this->CI->lib_custom->project_is_valid($removed_by, $c_project->id);
			if (!$project_is_valid)
				throw new Exception("Access denied for the project.");
		}
		
		// -- Remove Order In Detail --
		foreach ($c_orderin->c_orderindetail->get() as $c_orderindetail)
		{
			$this->orderindetail_remove($c_orderindetail->id, $removed_by);
		}
		
		// -- Remove Order In --
		if (!$c_orderin->delete())
			throw new Exception($c_orderin->error->string);
		
		return $c_orderin_id;
	}
	
	public function orderin_generate_status_inv_receive($c_orderin_id, $generate_by = NULL)
	{
		$status = 'NO RECEIVE';
		
		$c_orderin = new C_orderin($c_orderin_id);
		
		$table = $this->CI->db
			->select_if_null("SUM(oid.quantity_box)", 0, 'quantity_box')
			->select_if_null("SUM(oid.quantity)", 0, 'quantity')
			->select_if_null("SUM(ird.quantity_box)", 0, 'ird_quantity_box')
			->select_if_null("SUM(ird.quantity)", 0, 'ird_quantity')
			->from('c_orderindetails oid')
			->join("(SELECT   c_orderindetail_id "
				  ."	    , ". $this->CI->db->if_null("SUM(quantity_box)", 0) ." quantity_box "
				  ."	    , ". $this->CI->db->if_null("SUM(quantity)", 0) ." quantity "
				  ."   FROM m_inventory_receivedetails "
				  ."  GROUP BY c_orderindetail_id"
				  .") ird"
				, "ird.c_orderindetail_id = oid.id"
				, 'left'
			)
			->where('oid.c_orderin_id', $c_orderin_id)
			->get();
		if ($table->num_rows() > 0)
		{
			$record = $table->first_row();
			
			if (	$record->ird_quantity_box == 0
				&&	$record->ird_quantity == 0)
			{
				$status = 'NO RECEIVE';
			}
			elseif (	$record->quantity_box == $record->ird_quantity_box
					&&	$record->quantity == $record->ird_quantity)
			{
				$status = 'COMPLETE';
			}
			elseif (	$record->quantity_box != $record->ird_quantity_box
					&&	$record->quantity != $record->ird_quantity)
			{
				$status = 'INCOMPLETE';
			}
			elseif ($record->quantity_box != $record->ird_quantity_box)
			{
				$status = 'INCOMPLETE BOX';
			}
			elseif ($record->quantity != $record->ird_quantity)
			{
				$status = 'INCOMPLETE QUANTITY';
			}
		}
		
		$data = new stdClass();
		$data->status_inventory_receive = $status;
		$data->updated_by = $generate_by;
		
		$this->set_model_fields_values($c_orderin, $data);
		$c_orderin_saved = $c_orderin->save();
		if (!$c_orderin_saved)
			throw new Exception($c_orderin->error->string);
	}
	
	public function orderindetail_add($data, $created_by = NULL)
	{
		$c_orderin = new C_orderin();
		
		$orderindetail_relations = array();
		if (property_exists($data, 'c_orderin_id'))
		{
			$c_orderin = new C_orderin($data->c_orderin_id);
			$orderindetail_relations['c_orderin'] = $c_orderin;
			unset($data->c_orderin_id);
		}
		if (property_exists($data, 'm_product_id'))
		{
			$orderindetail_relations['m_product'] = new M_product($data->m_product_id);
			unset($data->m_product_id);
		}
		
		$data->created_by = $created_by;
		
		$c_orderindetail = new C_orderindetail();
		$this->set_model_fields_values($c_orderindetail, $data);
		$c_orderindetail_saved = $c_orderindetail->save($orderindetail_relations);
		if (!$c_orderindetail_saved)
			throw new Exception($c_orderindetail->error->string);
		
		// -- Update Status --
		$this->orderin_generate_status_inv_receive($c_orderin->id, $created_by);
		$this->orderindetail_generate_status_inv_receive($c_orderindetail->id, $created_by);
		
		return $c_orderindetail->id;
	}
	
	public function orderindetail_update($c_orderindetail_id, $data, $updated_by = NULL)
	{
		$c_orderindetail = new C_orderindetail($c_orderindetail_id);
		$c_orderin = $c_orderindetail->c_orderin->get();
		$m_product = $c_orderindetail->m_product->get();
		$quantity_box = $c_orderindetail->quantity_box;
		
		$orderindetail_relations = array();
		if (property_exists($data, 'c_orderin_id'))
		{
			$c_orderin = new C_orderin($data->c_orderin_id);
			$orderindetail_relations['c_orderin'] = $c_orderin;
			unset($data->c_orderin_id);
		}
		if (property_exists($data, 'm_product_id'))
		{
			$orderindetail_relations['m_product'] = new M_product($data->m_product_id);
			unset($data->m_product_id);
		}
		if (property_exists($data, 'quantity_box'))
		{
			$quantity_box = $data->quantity_box;
		}
		
		// -- Get Used Quantity Box --
		$quantity_box_used = 0;
		$table = $this->CI->db
			->select_if_null("SUM(ird.quantity_box)", 0, 'quantity_box')
			->from('m_inventory_receivedetails ird')
			->where('ird.c_orderindetail_id', $c_orderindetail->id)
			->get();
		if ($table->num_rows() > 0)
		{
			$table_record = $table->first_row();
			$quantity_box_used = $table_record->quantity_box;
		}
		
		// -- Validate Quantity Box --
		if ($quantity_box < $quantity_box_used)
			throw new Exception("Product ".$m_product->code." with quantiy ".$quantity_box_used." has been used in inventory receive, you can't decreased it.");
		
		// -- Upadate Order In Detail --
		$data->updated_by = $updated_by;
		
		$this->set_model_fields_values($c_orderindetail, $data);
		$c_orderindetail_saved = $c_orderindetail->save($orderindetail_relations);
		if (!$c_orderindetail_saved)
			throw new Exception($c_orderindetail->error->string);
		
		// -- Update Status --
		$this->orderin_generate_status_inv_receive($c_orderin->id, $updated_by);
		$this->orderindetail_generate_status_inv_receive($c_orderindetail->id, $updated_by);
		
		return $c_orderindetail_id;
	}
	
	public function orderindetail_remove($c_orderindetail_id, $removed_by = NULL)
	{
		$c_orderindetail = new C_orderindetail($c_orderindetail_id);
		$c_orderin = $c_orderindetail->c_orderin->get();
		
		// -- Remove Inventory Receive Details --
		foreach ($c_orderindetail->m_inventory_receivedetail->get() as $m_inventory_receivedetail)
		{
			$this->CI->lib_inventory_in->receivedetail_remove($m_inventory_receivedetail->id, $removed_by);
		}
		
		// -- Remove Order In Detail --
		if (!$c_orderindetail->delete())
			throw new Exception($c_orderindetail->error->string);
		
		// -- Update Status --
		$this->orderin_generate_status_inv_receive($c_orderin->id, $removed_by);
		
		return $c_orderindetail_id;
	}
	
	public function orderindetail_generate_status_inv_receive($c_orderindetail_id, $generate_by = NULL)
	{
		$status = 'NO RECEIVE';
		
		$c_orderindetail = new C_orderindetail($c_orderindetail_id);
		
		$table = $this->CI->db
			->select_if_null("SUM(ird.quantity_box)", 0, 'quantity_box')
			->select_if_null("SUM(ird.quantity)", 0, 'quantity')
			->from('m_inventory_receivedetails ird')
			->where('ird.c_orderindetail_id', $c_orderindetail_id)
			->get();
		if ($table->num_rows() > 0)
		{
			$record = $table->first_row();
			if (	$record->quantity_box == 0
				&&	$record->quantity == 0)
			{
				$status = 'NO RECEIVE';
			}
			elseif (	$c_orderindetail->quantity_box == $record->quantity_box
					&&	$c_orderindetail->quantity == $record->quantity)
			{
				$status = 'COMPLETE';
			}
			elseif (	$c_orderindetail->quantity_box != $record->quantity_box
					&&	$c_orderindetail->quantity != $record->quantity)
			{
				$status = 'INCOMPLETE';
			}
			elseif ($c_orderindetail->quantity_box != $record->quantity_box)
			{
				$status = 'INCOMPLETE BOX';
			}
			elseif ($c_orderindetail->quantity != $record->quantity)
			{
				$status = 'INCOMPLETE QUANTITY';
			}
		}
		
		$data = new stdClass();
		$data->status_inventory_receive = $status;
		$data->updated_by = $generate_by;
		
		$this->set_model_fields_values($c_orderindetail, $data);
		$c_orderindetail_saved = $c_orderindetail->save();
		if (!$c_orderindetail_saved)
			throw new Exception($c_orderindetail->error->string);
	}
	
	/* ---------------------- */
	/* -- ORDER OUT REGION -- */
	/* ---------------------- */
	
	public function orderout_add($data, $created_by = NULL)
	{
		$orderout_relations = array();
		if (property_exists($data, 'c_businesspartner_id'))
		{
			$orderout_relations['c_businesspartner'] = new C_businesspartner($data->c_businesspartner_id);
			unset($data->c_businesspartner_id);
		}
		if (property_exists($data, 'c_project_id'))
		{
			$orderout_relations['c_project'] = new C_project($data->c_project_id);
			
			// -- Validate the project --
			$project_is_valid = $this->CI->lib_custom->project_is_valid($created_by, $data->c_project_id);
			if (!$project_is_valid)
				throw new Exception("Access denied for the project.");
			
			unset($data->c_project_id);
		}
		
		$data->created_by = $created_by;
		
		$c_orderout = new C_orderout();
		$this->set_model_fields_values($c_orderout, $data);
		$c_orderout_saved = $c_orderout->save($orderout_relations);
		if (!$c_orderout_saved)
			throw new Exception($c_orderout->error->string);
		
		// -- Update Status --
		$this->orderout_generate_status_inv_picklist($c_orderout->id, $created_by);
		
		return $c_orderout->id;
	}
	
	public function orderout_update($c_orderout_id, $data, $updated_by = NULL)
	{
		$orderout_relations = array();
		if (property_exists($data, 'c_businesspartner_id'))
		{
			$orderout_relations['c_businesspartner'] = new C_businesspartner($data->c_businesspartner_id);
			unset($data->c_businesspartner_id);
		}
		if (property_exists($data, 'c_project_id'))
		{
			$orderout_relations['c_project'] = new C_project($data->c_project_id);
			
			// -- Validate the project --
			$project_is_valid = $this->CI->lib_custom->project_is_valid($updated_by, $data->c_project_id);
			if (!$project_is_valid)
				throw new Exception("Access denied for the project.");
			
			unset($data->c_project_id);
		}
		
		$data->updated_by = $updated_by;
		
		$c_orderout = new C_orderout($c_orderout_id);
		
		// -- Validate the project --
		$c_project = $c_orderout->c_project->get();
		if ($c_project->id)
		{
			$project_is_valid = $this->CI->lib_custom->project_is_valid($updated_by, $c_project->id);
			if (!$project_is_valid)
				throw new Exception("Access denied for the project.");
		}
		
		$this->set_model_fields_values($c_orderout, $data);
		$c_orderout_saved = $c_orderout->save($orderout_relations);
		if (!$c_orderout_saved)
			throw new Exception($c_orderout->error->string);
		
		// -- Update Status --
		$this->orderout_generate_status_inv_picklist($c_orderout_id, $updated_by);
		
		return $c_orderout_id;
	}
	
	public function orderout_remove($c_orderout_id, $removed_by = NULL)
	{
		$c_orderout = new C_orderout($c_orderout_id);
		
		// -- Validate the project --
		$c_project = $c_orderout->c_project->get();
		if ($c_project->id)
		{
			$project_is_valid = $this->CI->lib_custom->project_is_valid($removed_by, $c_project->id);
			if (!$project_is_valid)
				throw new Exception("Access denied for the project.");
		}
		
		// -- Remove Order Out Detail --
		foreach ($c_orderout->c_orderoutdetail->get() as $c_orderoutdetail)
		{
			$this->orderoutdetail_remove($c_orderoutdetail->id, $removed_by);
		}
		
		// -- Remove Order Out --
		if (!$c_orderout->delete())
			throw new Exception($c_orderout->error->string);
		
		return $c_orderout_id;
	}
	
	public function orderout_generate_status_inv_picklist($c_orderout_id, $generate_by = NULL)
	{
		$status = 'NO PICK LIST';
		
		$c_orderout = new C_orderout($c_orderout_id);
		
		$table = $this->CI->db
			->select_if_null("SUM(ood.quantity_box)", 0, 'quantity_box')
			->select_if_null("SUM(ood.quantity)", 0, 'quantity')
			->select_if_null("SUM(ipld.quantity_box)", 0, 'ipld_quantity_box')
			->select_if_null("SUM(ipld.quantity)", 0, 'ipld_quantity')
			->from('c_orderoutdetails ood')
			->join("(SELECT   c_orderoutdetail_id "
				  ."	    , ". $this->CI->db->if_null("SUM(quantity_box)", 0) ." quantity_box "
				  ."	    , ". $this->CI->db->if_null("SUM(quantity)", 0) ." quantity "
				  ."   FROM m_inventory_picklistdetails "
				  ."  GROUP BY c_orderoutdetail_id"
				  .") ipld"
				, "ipld.c_orderoutdetail_id = ood.id"
				, 'left'
			)
			->where('ood.c_orderout_id', $c_orderout_id)
			->get();
		if ($table->num_rows() > 0)
		{
			$record = $table->first_row();
			
			if (	$record->ipld_quantity_box == 0
				&&	$record->ipld_quantity == 0)
			{
				$status = 'NO PICK LIST';
			}
			elseif (	$record->quantity_box == $record->ipld_quantity_box
					&&	$record->quantity == $record->ipld_quantity)
			{
				$status = 'COMPLETE';
			}
			elseif (	$record->quantity_box != $record->ipld_quantity_box
					&&	$record->quantity != $record->ipld_quantity)
			{
				$status = 'INCOMPLETE';
			}
			elseif ($record->quantity_box != $record->ipld_quantity_box)
			{
				$status = 'INCOMPLETE BOX';
			}
			elseif ($record->quantity != $record->ipld_quantity)
			{
				$status = 'INCOMPLETE QUANTITY';
			}
		}
		
		$data = new stdClass();
		$data->status_inventory_picklist = $status;
		$data->updated_by = $generate_by;
		
		$this->set_model_fields_values($c_orderout, $data);
		$c_orderout_saved = $c_orderout->save();
		if (!$c_orderout_saved)
			throw new Exception($c_orderout->error->string);
	}
	
	public function orderoutdetail_add($data, $created_by = NULL)
	{
		$c_orderout = new C_orderout();
		
		$orderoutdetail_relations = array();
		if (property_exists($data, 'c_orderout_id'))
		{
			$c_orderout = new C_orderout($data->c_orderout_id);
			$orderoutdetail_relations['c_orderout'] = $c_orderout;
			unset($data->c_orderout_id);
		}
		if (property_exists($data, 'm_product_id'))
		{
			$orderoutdetail_relations['m_product'] = new M_product($data->m_product_id);
			unset($data->m_product_id);
		}
		
		$data->created_by = $created_by;
		
		$c_orderoutdetail = new C_orderoutdetail();
		$this->set_model_fields_values($c_orderoutdetail, $data);
		$c_orderoutdetail_saved = $c_orderoutdetail->save($orderoutdetail_relations);
		if (!$c_orderoutdetail_saved)
			throw new Exception($c_orderoutdetail->error->string);
		
		// -- Update Status --
		$this->orderout_generate_status_inv_picklist($c_orderout->id, $created_by);
		$this->orderoutdetail_generate_status_inv_picklist($c_orderoutdetail->id, $created_by);
		
		return $c_orderoutdetail->id;
	}
	
	public function orderoutdetail_update($c_orderoutdetail_id, $data, $updated_by = NULL)
	{
		$c_orderoutdetail = new C_orderoutdetail($c_orderoutdetail_id);
		$c_orderout = $c_orderoutdetail->c_orderout->get();
		$m_product = new M_product();
		$quantity_box = $c_orderoutdetail->quantity_box;
		$quantity = $c_orderoutdetail->quantity;
		
		$orderoutdetail_relations = array();
		if (property_exists($data, 'c_orderout_id'))
		{
			$c_orderout = new C_orderout($data->c_orderout_id);
			$orderoutdetail_relations['c_orderout'] = $c_orderout;
			unset($data->c_orderout_id);
		}
		if (property_exists($data, 'm_product_id'))
		{
			$m_product = new M_product($data->m_product_id);
			$orderoutdetail_relations['m_product'] = $m_product;
			unset($data->m_product_id);
		}
		if (property_exists($data, 'quantity_box'))
		{
			$quantity_box = $data->quantity_box;
		}
		if (property_exists($data, 'quantity'))
		{
			$quantity = $data->quantity;
		}
		
		// -- Get Used Quantity --
		$quantity_box_used = 0;
		$quantity_used = 0;
		$table = $this->CI->db
			->select_if_null("SUM(ipld.quantity_box)", 0, 'quantity_box')
			->select_if_null("SUM(ipld.quantity)", 0, 'quantity')
			->from('m_inventory_picklistdetails ipld')
			->where('ipld.c_orderoutdetail_id', $c_orderoutdetail->id)
			->get();
		if ($table->num_rows() > 0)
		{
			$table_record = $table->first_row();
			$quantity_box_used = $table_record->quantity_box;
			$quantity_used = $table_record->quantity;
		}
		
		// -- Validate Quantity --
		if ($quantity_box > 0 && $quantity_box < $quantity_box_used)
			throw new Exception("Product ".$m_product->code." with box quantiy ".$quantity_box_used." has been used in inventory pick list, you can't decreased it.");
		if ($quantity > 0 && $quantity < $quantity_used)
			throw new Exception("Product ".$m_product->code." with quantiy ".$quantity_used." has been used in inventory pick list, you can't decreased it.");
		
		// -- Upadate Order Out Detail --
		$data->updated_by = $updated_by;
		
		$this->set_model_fields_values($c_orderoutdetail, $data);
		$c_orderoutdetail_saved = $c_orderoutdetail->save($orderoutdetail_relations);
		if (!$c_orderoutdetail_saved)
			throw new Exception($c_orderoutdetail->error->string);
		
		// -- Update Status --
		$this->orderout_generate_status_inv_picklist($c_orderout->id, $updated_by);
		$this->orderoutdetail_generate_status_inv_picklist($c_orderoutdetail->id, $updated_by);
		
		return $c_orderoutdetail_id;
	}
	
	public function orderoutdetail_remove($c_orderoutdetail_id, $removed_by = NULL)
	{
		$c_orderoutdetail = new C_orderoutdetail($c_orderoutdetail_id);
		$c_orderout = $c_orderoutdetail->c_orderout->get();
		
		// -- Remove Inventory Pick List Detail --
		foreach ($c_orderoutdetail->m_inventory_picklistdetail->get() as $m_inventory_picklistdetail)
		{
			$this->CI->lib_inventory_out->picklistdetail_remove($m_inventory_picklistdetail->id, $removed_by);
		}
		
		// -- Remove Order Out Detail --
		if (!$c_orderoutdetail->delete())
			throw new Exception($c_orderoutdetail->error->string);
		
		// -- Update Status --
		$this->orderout_generate_status_inv_picklist($c_orderout->id, $removed_by);
		
		return $c_orderoutdetail_id;
	}
	
	public function orderoutdetail_generate_status_inv_picklist($c_orderoutdetail_id, $generate_by = NULL)
	{
		$status = 'NO PICK LIST';
		
		$c_orderoutdetail = new C_orderoutdetail($c_orderoutdetail_id);
		
		$table = $this->CI->db
			->select_if_null("SUM(ipld.quantity_box)", 0, 'quantity_box')
			->select_if_null("SUM(ipld.quantity)", 0, 'quantity')
			->from('m_inventory_picklistdetails ipld')
			->where('ipld.c_orderoutdetail_id', $c_orderoutdetail_id)
			->get();
		if ($table->num_rows() > 0)
		{
			$record = $table->first_row();
			if (	$record->quantity_box == 0
				&&	$record->quantity == 0)
			{
				$status = 'NO PICK LIST';
			}
			elseif (	$c_orderoutdetail->quantity_box == $record->quantity_box
					&&	$c_orderoutdetail->quantity == $record->quantity)
			{
				$status = 'COMPLETE';
			}
			elseif (	$c_orderoutdetail->quantity_box != $record->quantity_box
					&&	$c_orderoutdetail->quantity != $record->quantity)
			{
				$status = 'INCOMPLETE';
			}
			elseif ($c_orderoutdetail->quantity_box != $record->quantity_box)
			{
				$status = 'INCOMPLETE BOX';
			}
			elseif ($c_orderoutdetail->quantity != $record->quantity)
			{
				$status = 'INCOMPLETE QUANTITY';
			}
		}
		
		$data = new stdClass();
		$data->status_inventory_picklist = $status;
		$data->updated_by = $generate_by;
		
		$this->set_model_fields_values($c_orderoutdetail, $data);
		$c_orderoutdetail_saved = $c_orderoutdetail->save();
		if (!$c_orderoutdetail_saved)
			throw new Exception($c_orderoutdetail->error->string);
	}
}