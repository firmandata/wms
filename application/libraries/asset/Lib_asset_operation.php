<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Lib_asset_operation extends Lib_general
{
	protected $CI;
	
	function __construct()
    {
		$this->CI =& get_instance();
		
		$this->CI->load->library('asset/lib_asset');
	}
	
	/* ----------------- */
	/* -- MOVE REGION -- */
	/* ----------------- */
	
	public function move_add($data, $created_by = NULL)
	{
		$data->created_by = $created_by;
		
		$a_asset_move = new A_asset_move();
		$this->set_model_fields_values($a_asset_move, $data);
		$a_asset_move_saved = $a_asset_move->save();
		if (!$a_asset_move_saved)
			throw new Exception($a_asset_move->error->string);
		
		return $a_asset_move->id;
	}
	
	public function move_update($a_asset_move_id, $data, $updated_by = NULL)
	{
		$data->updated_by = $updated_by;
		
		$a_asset_move = new A_asset_move($a_asset_move_id);
		$this->set_model_fields_values($a_asset_move, $data);
		$a_asset_move_saved = $a_asset_move->save();
		if (!$a_asset_move_saved)
			throw new Exception($a_asset_move->error->string);
		
		return $a_asset_move_id;
	}
	
	public function move_remove($a_asset_move_id, $removed_by = NULL)
	{
		$a_asset_move = new A_asset_move($a_asset_move_id);
		
		// -- Remove Asset Move Detail --
		$table = $this->CI->db
			->select('id')
			->from('a_asset_movedetails')
			->where('a_asset_move_id', $a_asset_move->id)
			->order_by('id', 'desc')
			->get();
		$a_asset_movedetails = $table->result();
		foreach ($a_asset_movedetails as $a_asset_movedetail)
		{
			$this->movedetail_remove($a_asset_movedetail->id, $removed_by);
		}
		
		// -- Remove Asset Move --
		if (!$a_asset_move->delete())
			throw new Exception($a_asset_move->error->string);
		
		return $a_asset_move_id;
	}
	
	public function movedetail_add_by_properties($data, $created_by = NULL)
	{
		$a_asset_criteria = clone $data;
		if (property_exists($data, 'c_locationfrom_id'))
		{
			$a_asset_criteria->c_location_id = $data->c_locationfrom_id;
		}
		
		// -- Get exist asset records --
		$this->CI->db
			->select("ast.id")
			->from('a_assets ast');
		$this->movedetail_criteria_query($a_asset_criteria, 'ast', 'a_assets');
		$table = $this->CI->db->get();
		if ($table->num_rows() == 0)
			throw new Exception("Critera not found in asset existing.");
		$a_asset_records = $table->result();
		
		// -- Move all --
		foreach ($a_asset_records as $a_asset_record)
		{
			// -- Asset move detail add --
			$data->a_asset_id = $a_asset_record->id;
			$this->movedetail_add(clone $data, $created_by);
		}
	}
	
	public function movedetail_remove_by_properties($data, $removed_by = NULL)
	{
		// -- Get Move Detail Exists --
		$this->CI->db
			->select("amd.id")
			->from('a_asset_movedetails amd')
			->join('a_assets ast', "ast.id = amd.a_asset_id");
		$this->movedetail_criteria_query($data, 'amd', 'a_asset_movedetails');
		$table = $this->CI->db
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
	}
	
	protected function movedetail_add($data, $created_by = NULL)
	{
		$a_asset_move_ids = array();
		
		$a_asset_move = new A_asset_move();
		$a_asset = new A_asset();
		$c_locationto = new C_location();
		
		$a_asset_movedetail_relations = array();
		if (property_exists($data, 'a_asset_move_id'))
		{
			$a_asset_move = new A_asset_move($data->a_asset_move_id);
			$a_asset_movedetail_relations['a_asset_move'] = $a_asset_move;
			unset($data->a_asset_move_id);
		}
		if (property_exists($data, 'a_asset_id'))
		{
			$a_asset = new A_asset($data->a_asset_id);
			$a_asset_movedetail_relations['a_asset'] = $a_asset;
			unset($data->a_asset_id);
		}
		if (property_exists($data, 'c_locationto_id'))
		{
			$c_locationto = new C_location($data->c_locationto_id);
			$a_asset_movedetail_relations['c_locationto'] = $c_locationto;
			unset($data->c_locationto_id);
		}
		
		$data_a_asset_log = new stdClass();
		$data_a_asset_log->log_type = "MOVE";
		$data_a_asset_log->ref1_code = $a_asset_move->code;
		$data_a_asset_log->notes = 'Add Move';
		$move_results = $this->CI->lib_asset->asset_move_location($a_asset->id, $c_locationto->id, $created_by, $data_a_asset_log);
		
		foreach ($move_results as $move_result)
		{
			if ($move_result['type'] != 'move')
				continue;
			
			// -- Asset move detail add --
			$a_asset = new A_asset($move_result['a_asset_id']);
			$c_locationfrom = new C_location($move_result['c_locationfrom_id']);
			
			$a_asset_movedetail_relations['a_asset_move'] = $a_asset_move;
			$a_asset_movedetail_relations['a_asset'] = $a_asset;
			$a_asset_movedetail_relations['c_locationfrom'] = $c_locationfrom;
			$a_asset_movedetail_relations['c_locationto'] = $c_locationto;
			
			$data->created_by = $created_by;
			
			$a_asset_movedetail = new A_asset_movedetail();
			$this->set_model_fields_values($a_asset_movedetail, $data);
			$a_asset_move_saved = $a_asset_movedetail->save($a_asset_movedetail_relations);
			if (!$a_asset_move_saved)
				throw new Exception($a_asset_movedetail->error->string);
			
			$a_asset_move_ids[] = $a_asset_movedetail->id;
		}
		
		return $a_asset_move_ids;
	}
	
	protected function movedetail_remove($a_asset_movedetail_id, $removed_by = NULL)
	{
		$a_asset_movedetail = new A_asset_movedetail($a_asset_movedetail_id);
		$a_asset_move = $a_asset_movedetail->a_asset_move->get();
		
		// -- Remove Asset to old location --
		$a_asset = $a_asset_movedetail->a_asset->get();
		$c_locationfrom = $a_asset_movedetail->c_locationfrom->get();
		
		$data_a_asset_log = new stdClass();
		$data_a_asset_log->log_type = "MOVE";
		$data_a_asset_log->ref1_code = $a_asset_move->code;
		$data_a_asset_log->notes = 'Remove Move';
		$move_results = $this->CI->lib_asset->asset_move_location($a_asset->id, $c_locationfrom->id, $removed_by, $data_a_asset_log);
		
		// -- Remove Move Detail --
		if (!$a_asset_movedetail->delete())
			throw new Exception($a_asset_movedetail->error->string);
		
		return $a_asset_movedetail_id;
	}
	
	private function movedetail_criteria_query($data, $table_alias, $table_name = 'a_asset_movedetails')
	{
		$fields = array();
		if ($table_name == 'a_asset_movedetails')
		{
			$fields[] = 'id';
			$fields[] = 'a_asset_move_id';
			$fields[] = 'a_asset_id';
			$fields[] = 'c_locationfrom_id';
			$fields[] = 'c_locationto_id';
		}
		if ($table_name == 'a_assets')
		{
			$fields[] = 'code';
			$fields[] = 'c_location_id';
			$fields[] = 'm_product_id';
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
	/* -- TRANSFER REGION -- */
	/* --------------------- */
	
	public function transfer_add($data, $created_by = NULL)
	{
		$data->created_by = $created_by;
		
		$a_asset_transfer = new A_asset_transfer();
		$this->set_model_fields_values($a_asset_transfer, $data);
		$a_asset_transfer_saved = $a_asset_transfer->save();
		if (!$a_asset_transfer_saved)
			throw new Exception($a_asset_transfer->error->string);
		
		return $a_asset_transfer->id;
	}
	
	public function transfer_update($a_asset_transfer_id, $data, $updated_by = NULL)
	{
		$data->updated_by = $updated_by;
		
		$a_asset_transfer = new A_asset_transfer($a_asset_transfer_id);
		$this->set_model_fields_values($a_asset_transfer, $data);
		$a_asset_transfer_saved = $a_asset_transfer->save();
		if (!$a_asset_transfer_saved)
			throw new Exception($a_asset_transfer->error->string);
		
		return $a_asset_transfer_id;
	}
	
	public function transfer_remove($a_asset_transfer_id, $removed_by = NULL)
	{
		$a_asset_transfer = new A_asset_transfer($a_asset_transfer_id);
		
		// -- Remove Asset Transfer Detail --
		$table = $this->CI->db
			->select('id')
			->from('a_asset_transferdetails')
			->where('a_asset_transfer_id', $a_asset_transfer->id)
			->order_by('id', 'desc')
			->get();
		$a_asset_transferdetails = $table->result();
		foreach ($a_asset_transferdetails as $a_asset_transferdetail)
		{
			$this->transferdetail_remove($a_asset_transferdetail->id, $removed_by);
		}
		
		// -- Remove Asset Transfer --
		if (!$a_asset_transfer->delete())
			throw new Exception($a_asset_transfer->error->string);
		
		return $a_asset_transfer_id;
	}
	
	public function transferdetail_add_by_properties($data, $created_by = NULL)
	{
		$a_asset_criteria = clone $data;
		if (property_exists($data, 'c_businesspartner_userfrom_id'))
		{
			$a_asset_criteria->c_businesspartner_user_id = $data->c_businesspartner_userfrom_id;
		}
		
		// -- Get exist asset records --
		$this->CI->db
			->select("ast.id")
			->from('a_assets ast');
		$this->transferdetail_criteria_query($a_asset_criteria, 'ast', 'a_assets');
		$table = $this->CI->db->get();
		if ($table->num_rows() == 0)
			throw new Exception("Critera not found in asset existing.");
		$a_asset_records = $table->result();
		
		// -- Transfer all --
		foreach ($a_asset_records as $a_asset_record)
		{
			// -- Asset transfer detail add --
			$data->a_asset_id = $a_asset_record->id;
			$this->transferdetail_add(clone $data, $created_by);
		}
	}
	
	public function transferdetail_remove_by_properties($data, $removed_by = NULL)
	{
		// -- Get Transfer Detail Exists --
		$this->CI->db
			->select("atd.id")
			->from('a_asset_transferdetails atd')
			->join('a_assets ast', "ast.id = atd.a_asset_id");
		$this->transferdetail_criteria_query($data, 'atd', 'a_asset_transferdetails');
		$table = $this->CI->db
			->get();
		if ($table->num_rows() == 0)
		{
			throw new Exception("Critera not found in transfer existing.");
		}
		
		// -- Remove Transfer Detail Exists --
		$table_records = $table->result();
		foreach ($table_records as $table_record)
		{
			$this->transferdetail_remove($table_record->id, $removed_by);
		}
	}
	
	protected function transferdetail_add($data, $created_by = NULL)
	{
		$a_asset_transfer_ids = array();
		
		$a_asset_transfer = new A_asset_transfer();
		$a_asset = new A_asset();
		$c_businesspartner_userto = new C_businesspartner();
		$c_departmentto = new C_department();
		
		$a_asset_transferdetail_relations = array();
		if (property_exists($data, 'a_asset_transfer_id'))
		{
			$a_asset_transfer = new A_asset_transfer($data->a_asset_transfer_id);
			$a_asset_transferdetail_relations['a_asset_transfer'] = $a_asset_transfer;
			unset($data->a_asset_transfer_id);
		}
		if (property_exists($data, 'a_asset_id'))
		{
			$a_asset = new A_asset($data->a_asset_id);
			$a_asset_transferdetail_relations['a_asset'] = $a_asset;
			unset($data->a_asset_id);
		}
		if (property_exists($data, 'c_businesspartner_userto_id'))
		{
			$c_businesspartner_userto = new C_businesspartner($data->c_businesspartner_userto_id);
			$a_asset_transferdetail_relations['c_businesspartner_userto'] = $c_businesspartner_userto;
			unset($data->c_businesspartner_userto_id);
		}
		if (property_exists($data, 'c_departmentto_id'))
		{
			$c_departmentto = new C_department($data->c_departmentto_id);
			$a_asset_transferdetail_relations['c_departmentto'] = $c_departmentto;
			unset($data->c_departmentto_id);
		}
		
		$data_a_asset_log = new stdClass();
		$data_a_asset_log->log_type = "TRANSFER";
		$data_a_asset_log->ref1_code = $a_asset_transfer->code;
		$data_a_asset_log->notes = 'Add Transfer';
		$transfer_results = $this->CI->lib_asset->asset_change_user($a_asset->id, $c_businesspartner_userto->id, $c_departmentto->id, $created_by, $data_a_asset_log);
		
		foreach ($transfer_results as $transfer_result)
		{
			if ($transfer_result['type'] != 'change')
				continue;
			
			// -- Asset transfer detail add --
			$a_asset = new A_asset($transfer_result['a_asset_id']);
			$c_businesspartner_userfrom = new C_businesspartner($transfer_result['c_businesspartner_userfrom_id']);
			$c_departmentfrom = new C_department($transfer_result['c_departmentfrom_id']);
			
			$a_asset_transferdetail_relations['a_asset_transfer'] = $a_asset_transfer;
			$a_asset_transferdetail_relations['a_asset'] = $a_asset;
			$a_asset_transferdetail_relations['c_businesspartner_userfrom'] = $c_businesspartner_userfrom;
			$a_asset_transferdetail_relations['c_businesspartner_userto'] = $c_businesspartner_userto;
			$a_asset_transferdetail_relations['c_departmentfrom'] = $c_departmentfrom;
			$a_asset_transferdetail_relations['c_departmentto'] = $c_departmentto;
			
			$data->created_by = $created_by;
			
			$a_asset_transferdetail = new A_asset_transferdetail();
			$this->set_model_fields_values($a_asset_transferdetail, $data);
			$a_asset_transfer_saved = $a_asset_transferdetail->save($a_asset_transferdetail_relations);
			if (!$a_asset_transfer_saved)
				throw new Exception($a_asset_transferdetail->error->string);
			
			$a_asset_transfer_ids[] = $a_asset_transferdetail->id;
		}
		
		return $a_asset_transfer_ids;
	}
	
	protected function transferdetail_remove($a_asset_transferdetail_id, $removed_by = NULL)
	{
		$a_asset_transferdetail = new A_asset_transferdetail($a_asset_transferdetail_id);
		$a_asset_transfer = $a_asset_transferdetail->a_asset_transfer->get();
		
		// -- Remove Asset to old user --
		$a_asset = $a_asset_transferdetail->a_asset->get();
		$c_businesspartner_userfrom = $a_asset_transferdetail->c_businesspartner_userfrom->get();
		$c_departmentfrom = $a_asset_transferdetail->c_departmentfrom->get();
		
		$data_a_asset_log = new stdClass();
		$data_a_asset_log->log_type = "TRANSFER";
		$data_a_asset_log->ref1_code = $a_asset_transfer->code;
		$data_a_asset_log->notes = 'Remove Transfer';
		$transfer_results = $this->CI->lib_asset->asset_change_user($a_asset->id, $c_businesspartner_userfrom->id, $c_departmentfrom->id, $removed_by, $data_a_asset_log);
		
		// -- Remove Transfer Detail --
		if (!$a_asset_transferdetail->delete())
			throw new Exception($a_asset_transferdetail->error->string);
		
		return $a_asset_transferdetail_id;
	}
	
	private function transferdetail_criteria_query($data, $table_alias, $table_name = 'a_asset_transferdetails')
	{
		$fields = array();
		if ($table_name == 'a_asset_transferdetails')
		{
			$fields[] = 'id';
			$fields[] = 'a_asset_transfer_id';
			$fields[] = 'a_asset_id';
			$fields[] = 'c_businesspartner_userfrom_id';
			$fields[] = 'c_businesspartner_userto_id';
			$fields[] = 'c_departmentfrom_id';
			$fields[] = 'c_departmentto_id';
		}
		if ($table_name == 'a_assets')
		{
			$fields[] = 'code';
			$fields[] = 'c_businesspartner_user_id';
			$fields[] = 'm_product_id';
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