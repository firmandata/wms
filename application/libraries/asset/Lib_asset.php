<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Lib_asset extends Lib_general
{
	protected $CI;
	
	function __construct()
    {
		$this->CI =& get_instance();
	}
	
	/* ------------------ */
	/* -- ASSET REGION -- */
	/* ------------------ */
	
	public function asset_add($data, $created_by = NULL, $log = NULL)
	{
		$m_product = new M_product();
		$c_region = new C_region();
		
		$asset_relations = array();
		if (property_exists($data, 'm_product_id'))
		{
			$m_product = new M_product($data->m_product_id);
			$asset_relations['m_product'] = $m_product;
			unset($data->m_product_id);
		}
		if (property_exists($data, 'c_region_id'))
		{
			$c_region = new C_region($data->c_region_id);
			$asset_relations['c_region'] = $c_region;
			unset($data->c_region_id);
		}
		if (property_exists($data, 'c_department_id'))
		{
			$asset_relations['c_department'] = new C_department($data->c_department_id);
			unset($data->c_department_id);
		}
		if (property_exists($data, 'c_location_id'))
		{
			$asset_relations['c_location'] = new C_location($data->c_location_id);
			unset($data->c_location_id);
		}
		if (property_exists($data, 'c_businesspartner_supplier_id'))
		{
			$asset_relations['c_businesspartner_supplier'] = new C_businesspartner($data->c_businesspartner_supplier_id);
			unset($data->c_businesspartner_supplier_id);
		}
		if (property_exists($data, 'c_businesspartner_user_id'))
		{
			$asset_relations['c_businesspartner_user'] = new C_businesspartner($data->c_businesspartner_user_id);
			unset($data->c_businesspartner_user_id);
		}
		if (	!property_exists($data, 'code')
			||	(property_exists($data, 'code') && empty($data->code)))
		{
			$asset_code = $this->asset_get_code($c_region->id, $m_product->id, $data->type, (property_exists($data, 'purchase_date') ? $data->purchase_date : NULL));
			$data->code_no = $asset_code->code_no;
			$data->code = $asset_code->code;
		}
		
		$is_a_assetamounts_manual_update = FALSE;
		$a_assetamounts = array();
		
		// -- Depreciation by Manual --
		if (property_exists($data, 'a_assetamounts'))
		{
			if (is_array($data->a_assetamounts))
			{
				$a_assetamounts = $data->a_assetamounts;
				$is_a_assetamounts_manual_update = TRUE;
			}
			unset($data->a_assetamounts);
		}
		
		$data->created_by = $created_by;
		
		$a_asset = new A_asset();
		$this->set_model_fields_values($a_asset, $data);
		$asset_saved = $a_asset->save($asset_relations);
		if (!$asset_saved)
			throw new Exception($a_asset->error->string);
		
		// -- Depreciation by Auto --
		if (count($a_assetamounts) == 0 || $is_a_assetamounts_manual_update === FALSE)
		{
			$a_assetamounts = $this->asset_get_depreciation_list($a_asset->purchase_date, $a_asset->purchase_price, $a_asset->depreciation_period_type, $a_asset->depreciation_period_time);
		}
		
		// -- Add Depreciations --
		foreach ($a_assetamounts as $a_assetamount_idx=>$a_assetamount)
		{
			$a_assetamount_data = new stdClass();
			$a_assetamount_data->a_asset_id = $a_asset->id;
			$a_assetamount_data->depreciated_date = $a_assetamount->depreciated_date;
			$a_assetamount_data->book_value = $a_assetamount->book_value;
			$a_assetamount_data->market_value = $a_assetamount->market_value;
			$a_assetamount_data->depreciation_accumulated = $a_assetamount->depreciation_accumulated;
			$a_assetamount_data->depreciated_value = $a_assetamount->depreciated_value;
			$this->assetamount_add($a_assetamount_data, $created_by);
		}
		
		return $a_asset->id;
	}
	
	public function asset_update($a_asset_id, $data, $updated_by = NULL, $log = NULL)
	{
		$a_asset = new A_asset($a_asset_id);
		
		$asset_relations = array();
		if (property_exists($data, 'm_product_id'))
		{
			$asset_relations['m_product'] = new M_product($data->m_product_id);
			unset($data->m_product_id);
		}
		if (property_exists($data, 'c_region_id'))
		{
			$asset_relations['c_region'] = new C_region($data->c_region_id);
			unset($data->c_region_id);
		}
		if (property_exists($data, 'c_department_id'))
		{
			$asset_relations['c_department'] = new C_department($data->c_department_id);
			unset($data->c_department_id);
		}
		if (property_exists($data, 'c_location_id'))
		{
			$asset_relations['c_location'] = new C_location($data->c_location_id);
			unset($data->c_location_id);
		}
		if (property_exists($data, 'c_businesspartner_supplier_id'))
		{
			$asset_relations['c_businesspartner_supplier'] = new C_businesspartner($data->c_businesspartner_supplier_id);
			unset($data->c_businesspartner_supplier_id);
		}
		if (property_exists($data, 'c_businesspartner_user_id'))
		{
			$asset_relations['c_businesspartner_user'] = new C_businesspartner($data->c_businesspartner_user_id);
			unset($data->c_businesspartner_user_id);
		}
		
		$is_a_assetamounts_manual_update = FALSE;
		$a_assetamounts = array();
		
		if (property_exists($data, 'a_assetamounts'))
		{
			if (is_array($data->a_assetamounts))
			{
				$a_assetamounts = $data->a_assetamounts;
				$is_a_assetamounts_manual_update = TRUE;
			}
			unset($data->a_assetamounts);
		}
		
		$is_a_assetamounts_params_change = FALSE;
		if (	(property_exists($data, 'purchase_date') && $a_asset->purchase_date != $data->purchase_date)
			||	(property_exists($data, 'purchase_price') && $a_asset->purchase_price != $data->purchase_price)
			||	(property_exists($data, 'depreciation_period_type') && $a_asset->depreciation_period_type != $data->depreciation_period_type)
			||	(property_exists($data, 'depreciation_period_time') && $a_asset->depreciation_period_time != $data->depreciation_period_time))
		{
			$is_a_assetamounts_params_change = TRUE;
		}
		
		$data->updated_by = $updated_by;
		
		$this->set_model_fields_values($a_asset, $data);
		$asset_saved = $a_asset->save($asset_relations);
		if (!$asset_saved)
			throw new Exception($a_asset->error->string);
		
		// -- Depreciation by Auto --
		if ($is_a_assetamounts_manual_update === FALSE && $is_a_assetamounts_params_change === TRUE)
		{
			$table = $this->CI->db
				->select("asta.depreciated_date, asta.id")
				->from('a_assetamounts asta')
				->where('asta.a_asset_id', $a_asset->id)
				->order_by('asta.id', 'asc');
			$a_assetamount = $table->get();
			$a_assetamount_records = $a_assetamount->result();
			
			$a_assetamounts = $this->asset_get_depreciation_list($a_asset->purchase_date, $a_asset->purchase_price, $a_asset->depreciation_period_type, $a_asset->depreciation_period_time);
			foreach ($a_assetamounts as $a_assetamount_idx=>$a_assetamount)
			{
				$a_assetamounts[$a_assetamount_idx]->id = NULL;
				if (isset($a_assetamount_records[$a_assetamount_idx]))
					$a_assetamounts[$a_assetamount_idx]->id = $a_assetamount_records[$a_assetamount_idx]->id;
			}
			$is_a_assetamounts_manual_update = TRUE;
		}
		
		// -- Depreciation Handler --
		if ($is_a_assetamounts_manual_update === TRUE)
		{
			$table = $this->CI->db
				->where('a_asset_id', $a_asset_id)
				->get('a_assetamounts');
			$a_assetamounts_existing = $table->result();
			
			// -- Add/Modify Asset Amount --
			foreach ($a_assetamounts as $a_assetamount)
			{
				$is_found_new = TRUE;
				foreach ($a_assetamounts_existing as $a_assetamount_existing)
				{
					if ($a_assetamount_existing->id == $a_assetamount->id)
					{
						$is_found_new = FALSE;
						break;
					}
				}
				$a_assetamount_data = new stdClass();
				$a_assetamount_data->depreciated_date = $a_assetamount->depreciated_date;
				$a_assetamount_data->book_value = $a_assetamount->book_value;
				$a_assetamount_data->market_value = $a_assetamount->market_value;
				$a_assetamount_data->depreciation_accumulated = $a_assetamount->depreciation_accumulated;
				$a_assetamount_data->depreciated_value = $a_assetamount->depreciated_value;
				if ($is_found_new == TRUE)
				{
					$a_assetamount_data->a_asset_id = $a_asset_id;
					$this->assetamount_add($a_assetamount_data, $updated_by);
				}
				else
				{
					$this->assetamount_update($a_assetamount->id, $a_assetamount_data, $updated_by);
				}
			}
			
			// -- Remove Asset Amount --
			foreach ($a_assetamounts_existing as $a_assetamount_existing)
			{
				$is_found_delete = TRUE;
				foreach ($a_assetamounts as $a_assetamount)
				{
					if ($a_assetamount->id == $a_assetamount_existing->id)
					{
						$is_found_delete = FALSE;
						break;
					}
				}
				if ($is_found_delete == TRUE)
				{
					$this->assetamount_remove($a_assetamount_existing->id, $updated_by);
				}
			}
		}
		
		return $a_asset_id;
	}
	
	public function asset_remove($a_asset_id, $removed_by = NULL, $log = NULL)
	{
		$a_asset = new A_asset($a_asset_id);
		
		// -- Remove Move Detail --
		if ($a_asset->a_asset_movedetail->count())
		{
			throw new Exception("You can't remove asset because it's already in used by move.");
		}
		
		// -- Remove Transfer Detail --
		if ($a_asset->a_asset_transferdetail->count())
		{
			throw new Exception("You can't remove asset because it's already in used by transfer.");
		}
		
		// -- Remove Asset Amount --
		foreach ($a_asset->a_assetamount->get() as $a_assetamount)
		{
			$this->assetamount_remove($a_assetamount->id, $removed_by);
		}
		
		if (!$a_asset->delete())
			throw new Exception($a_asset->error->string);
		
		return $a_asset_id;
	}
	
	public function asset_move_location($a_asset_id, $c_location_id, $updated_by = NULL, $log = NULL)
	{
		$move_results = array();
		
		$a_asset = new A_asset($a_asset_id);
		$c_locationfrom = $a_asset->c_location->get();
		
		$data = new stdClass();
		$data->c_location_id = $c_location_id;
		
		$a_asset_id_new = $this->asset_update($a_asset_id, $data, $updated_by, $log);
		$a_asset_new = new A_asset($a_asset_id_new);
		
		$move_results[] = array(
			'a_asset_id'		=> $a_asset_id_new,
			'c_locationfrom_id'	=> $c_locationfrom->id,
			'c_locationto_id'	=> $c_location_id,
			'type'				=> 'move'
		);
		
		return $move_results;
	}
	
	public function asset_change_user($a_asset_id, $c_businesspartner_user_id, $c_department_id, $updated_by = NULL, $log = NULL)
	{
		$change_results = array();
		
		$a_asset = new A_asset($a_asset_id);
		$c_businesspartner_userfrom = $a_asset->c_businesspartner_user->get();
		$c_departmentfrom = $a_asset->c_department->get();
		
		$data = new stdClass();
		$data->c_businesspartner_user_id = $c_businesspartner_user_id;
		$data->c_department_id = $c_department_id;
		
		$a_asset_id_new = $this->asset_update($a_asset_id, $data, $updated_by, $log);
		$a_asset_new = new A_asset($a_asset_id_new);
		
		$change_results[] = array(
			'a_asset_id'						=> $a_asset_id_new,
			'c_businesspartner_userfrom_id'		=> $c_businesspartner_userfrom->id,
			'c_businesspartner_userto_id'		=> $c_businesspartner_user_id,
			'c_departmentfrom_id'				=> $c_departmentfrom->id,
			'c_departmentto_id'					=> $c_department_id,
			'type'								=> 'change'
		);
		
		return $change_results;
	}
	
	public function asset_get_code($c_region_id, $m_product_id, $type, $purchase_date = NULL)
	{
		$result = new stdClass();
		$result->code_no = 0;
		$result->code = NULL;
		
		if (empty($purchase_date))
			$purchase_date = date('Y-m-d');
		
		$purchase_date_time = strtotime($purchase_date);
		$purchase_date_year_nc = date('y', $purchase_date_time);
		$purchase_date_year = date('Y', $purchase_date_time);
		
		$table = $this->CI->db
			->select('prog.id, prog.code')
			->from('m_products pro')
			->join('m_productgroups prog', "prog.id = pro.m_productgroup_id", 'left')
			->where('pro.id', $m_product_id)
			->get();
		if ($table->num_rows() == 0)
			return $result;
		$m_productgroup_record = $table->first_row();
		
		$c_region_code = '';
		$table = $this->CI->db
			->select('rgn.id, rgn.code')
			->from('c_regions rgn')
			->where('rgn.id', $c_region_id)
			->get();
		if ($table->num_rows() == 0)
			return $result;
		$c_region_record = $table->first_row();
		$c_region_code = $c_region_record->code;
		
		$type_code = '';
		if ($type == 'PROJECT')
			$type_code = 'P';
		
		$code_no = 0;
		
		$this->CI->db
			->select_max('ast.code_no')
			->from('a_assets ast')
			->join('m_products pro', "pro.id = ast.m_product_id");
		if ($m_productgroup_record->id !== NULL)
			$this->CI->db
				->where('pro.m_productgroup_id', $m_productgroup_record->id);
		else
			$this->CI->db
				->where("pro.m_productgroup_id IS NULL", NULL, FALSE);
		$table = $this->CI->db
			->where('ast.c_region_id', $c_region_id)
			->where($this->CI->db->getyear('ast.purchase_date') .' = '.$purchase_date_year, NULL, FALSE)
			->get();
		if ($table->num_rows() > 0)
		{
			$scalar_record = $table->first_row();
			$code_no = $scalar_record->code_no;
		}
		$code_no++;
		
		$result = new stdClass();
		$result->code_no = $code_no;
		$result->code = 
			  $type_code
			. $c_region_code
			. $purchase_date_year_nc
			. $m_productgroup_record->code
			. sprintf('%04s', $code_no);
		
		return $result;
	}
	
	public function asset_get_depreciation_list($begin_date, $price, $type, $time, $a_asset_id = NULL)
	{
		$a_assetamount_records = array();
		$a_asset = new A_asset($a_asset_id);
		if ($a_asset->exists())
		{
			$table = $this->CI->db
				->select("asta.id, asta.a_asset_id")
				->select("asta.depreciated_date")
				->select("asta.book_value, asta.market_value, asta.depreciated_value, asta.depreciation_accumulated")
				->from('a_assetamounts asta')
				->where('asta.a_asset_id', $a_asset->id)
				->order_by('asta.id', 'asc');
			$a_assetamount = $table->get();
			$a_assetamount_records = $a_assetamount->result();
			
			if (	$a_asset->purchase_date == $begin_date
				&&	$a_asset->purchase_price == $price
				&&	$a_asset->depreciation_period_type == $type
				&&	$a_asset->depreciation_period_time == $time)
			{
				return $a_assetamount_records;
			}
		}
		
		$results = array();
		
		$this->CI->load->helper('date');
		$date_format = $this->CI->config->item('server_date_format');
		
		$type = trim(strtoupper($type));
		if (!in_array($type, array('MONTHLY', 'ANNUAL', 'DAILY')))
			return $results;
		if (empty($begin_date))
			return $results;
		
		$time = (int)$time;
		$depreciated_value = $price;
		if ($time > 0)
			$depreciated_value = round($price / $time, 2);
		$depreciate_date = $begin_date;
		$book_value = $price;
		$depreciation_accumulated = 0;
		
		for ($time_count = 0; $time_count < $time + 1; $time_count++)
		{
			if ($time > 0 && $time_count >= $time)
			{
				$book_value = 0;
				$depreciation_accumulated = $price;
				$depreciated_value = $price - ($depreciated_value * ($time - 1));
			}
			
			$result = new stdClass();
			
			$result->id = NULL;
			if (isset($a_assetamount_records[$time_count]))
				$result->id = $a_assetamount_records[$time_count]->id;
			
			$result->depreciated_value = 0;
			if ($time_count > 0)
				$result->depreciated_value = $depreciated_value;
			
			$result->depreciated_date = $depreciate_date;
			if ($type == 'MONTHLY')
				$depreciate_date = date($date_format, strtotime(add_date($depreciate_date, 0, 1, 0)));
			elseif ($type == 'ANNUAL')
				$depreciate_date = date($date_format, strtotime(add_date($depreciate_date, 0, 0, 1)));
			elseif ($type == 'DAILY')
				$depreciate_date = date($date_format, strtotime(add_date($depreciate_date, 1, 0, 0)));
			
			$result->book_value = $book_value;
			$book_value -= $depreciated_value;
			
			$result->market_value = $result->book_value;
			
			$result->depreciation_accumulated = $depreciation_accumulated;
			$depreciation_accumulated += $depreciated_value;
			
			$results[] = $result;
		}
		
		return $results;
	}
	
	/* ------------------------- */
	/* -- ASSET AMOUNT REGION -- */
	/* ------------------------- */
	
	public function assetamount_add($data, $created_by = NULL)
	{
		$assetamount_relations = array();
		if (property_exists($data, 'a_asset_id'))
		{
			$assetamount_relations['a_asset'] = new A_asset($data->a_asset_id);
			unset($data->a_asset_id);
		}
		
		$data->created_by = $created_by;
		
		$a_assetamount = new A_assetamount();
		$this->set_model_fields_values($a_assetamount, $data);
		$assetamount_saved = $a_assetamount->save($assetamount_relations);
		if (!$assetamount_saved)
			throw new Exception($a_assetamount->error->string);
		
		return $a_assetamount->id;
	}
	
	public function assetamount_update($a_assetamount_id, $data, $updated_by = NULL)
	{
		$assetamount_relations = array();
		if (property_exists($data, 'a_asset_id'))
		{
			$assetamount_relations['a_asset'] = new A_asset($data->a_asset_id);
			unset($data->a_asset_id);
		}
		
		$data->updated_by = $updated_by;
		
		$a_assetamount = new A_assetamount($a_assetamount_id);
		$this->set_model_fields_values($a_assetamount, $data);
		$assetamount_saved = $a_assetamount->save($assetamount_relations);
		if (!$assetamount_saved)
			throw new Exception($a_assetamount->error->string);
		
		return $a_assetamount_id;
	}
	
	public function assetamount_remove($a_assetamount_id, $removed_by = NULL)
	{
		$a_assetamount = new A_assetamount($a_assetamount_id);
		
		if (!$a_assetamount->delete())
			throw new Exception($a_assetamount->error->string);
		
		return $a_assetamount_id;
	}
}