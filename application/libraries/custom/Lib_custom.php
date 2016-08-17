<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
 
class Lib_custom extends Lib_general
{
	protected $CI;
	
	function __construct()
    {
		$this->CI =& get_instance();
	}
	
	/* -------------------- */
	/* -- PRODUCT REGION -- */
	/* -------------------- */
	public function product_add($m_product_id, $data, $created_by = NULL)
	{
		$data->created_by = $created_by;
		
		$m_product = new M_product($m_product_id);
		
		$cus_m_product = new Cus_m_product();
		$this->set_model_fields_values($cus_m_product, $data);
		$cus_m_product_saved = $cus_m_product->save(
			array(
				'm_product'	=> $m_product
			)
		);
		
		if (!$cus_m_product_saved)
			throw new Exception($cus_m_product->error->string);
		
		return $cus_m_product->id;
	}
	
	public function product_update($cus_m_product_id, $data, $updated_by = NULL)
	{
		$data->updated_by = $updated_by;
		
		$cus_m_product = new Cus_m_product($cus_m_product_id);
		$this->set_model_fields_values($cus_m_product, $data);
		if (!$cus_m_product->save())
			throw new Exception($cus_m_product->error->string);
		
		return $cus_m_product_id;
	}
	
	public function product_remove($cus_m_product_id, $removed_by = NULL)
	{
		$cus_m_product = new Cus_m_product($cus_m_product_id);
		
		// -- Remove Product --
		if (!$cus_m_product->delete())
			throw new Exception($cus_m_product->error->string);
		
		return $cus_m_product_id;
	}
	
	/* -------------------- */
	/* -- INBOUND REGION -- */
	/* -------------------- */
	public function inbounddetail_add_by_value($product_code, $grid_code = NULL, $data, $created_by = NULL)
	{
		$this->CI->db
			->from('m_products')
			->where('code', $product_code);
		$table = $this->CI->db->get();
		if ($table->num_rows() == 0)
			throw new Exception("Product code not registered.");
		$m_product_record = $table->first_row();
		
		$m_grid_id = NULL;
		if ($grid_code !== NULL)
		{
			$this->CI->db
				->from('m_grids')
				->where('code', $grid_code);
			$table = $this->CI->db->get();
			if ($table->num_rows() == 0)
				throw new Exception("Grid code not registered.");
			$m_grid_record = $table->first_row();
			$m_grid_id = $m_grid_record->id;
		}
		
		$data->m_product_id = $m_product_record->id;
		$data->m_grid_id = $m_grid_id;
		
		return $this->inbounddetail_add($data, $created_by);
	}
	
	public function inbounddetail_add($data, $created_by = NULL)
	{
		$m_product = new M_product();
		$m_grid = new M_grid();
		$c_project = new C_project();
		$received_date = date('Y-m-d');
		$quantity_box = 1;
		
		$inbounddetail_relations = array();
		if (property_exists($data, 'm_product_id'))
		{
			$m_product = new M_product($data->m_product_id);
			$inbounddetail_relations['m_product'] = $m_product;
			unset($data->m_product_id);
		}
		if (property_exists($data, 'm_grid_id'))
		{
			$m_grid = new M_grid($data->m_grid_id);
			$inbounddetail_relations['m_grid'] = $m_grid;
			unset($data->m_grid_id);
		}
		if (property_exists($data, 'c_project_id'))
		{
			$c_project = new C_project($data->c_project_id);
			$inbounddetail_relations['c_project'] = $c_project;
			unset($data->c_project_id);
		}
		if (property_exists($data, 'received_date'))
		{
			if (!empty($data->received_date))
				$received_date = $data->received_date;
		}
		$data->received_date = $received_date;
		if (!$m_grid->exists())
		{
			$m_grid
				->where('code', $this->CI->config->item('inventory_default_grid'))
				->get();
		}
		if (!property_exists($data, 'quantity_box'))
		{
			$data->quantity_box = $quantity_box;
		}
		
		$this->CI->load->library('material/lib_inventory');
		
		// -- Get Grid --
		if ($m_grid == NULL)
		{
			$m_grid
				->where('code', $this->CI->config->item('inventory_default_grid'))
				->get();
		}
		
		// -- Inventory Add --
		$data_m_inventory = $data;
		$data_m_inventory->m_product_id = $m_product->id;
		$data_m_inventory->m_grid_id = $m_grid->id;
		$data_m_inventory->c_project = $c_project->id;
		
		$data_m_inventory_log = new stdClass();
		$data_m_inventory_log->log_type = "INBOUND";
		$data_m_inventory_log->notes = "Add Custom Inbound";
		
		$m_inventory_id = $this->CI->lib_inventory->add($data_m_inventory, $created_by, $data_m_inventory_log);
		
		// -- Custom Inbound Detail Add --
		$m_inventory = new M_inventory($m_inventory_id);
		$inbounddetail_relations['m_inventory'] = $m_inventory;
		
		$data->created_by = $created_by;
		
		$cus_m_inventory_inbounddetail = new Cus_m_inventory_inbounddetail();
		$this->set_model_fields_values($cus_m_inventory_inbounddetail, $data);
		$cus_m_inventory_inbounddetail_saved = $cus_m_inventory_inbounddetail->save($inbounddetail_relations);
		if (!$cus_m_inventory_inbounddetail_saved)
			throw new Exception($cus_m_inventory_inbounddetail->error->string);
		
		$this->CI->lib_inventory->verify_pallet_grid($m_inventory->pallet, $m_grid->id);
		
		return $cus_m_inventory_inbounddetail->id;
	}
	
	public function inbounddetail_update($cus_m_inventory_inbounddetail_id, $data, $updated_by = NULL)
	{
		$this->CI->load->library('material/lib_inventory');
		
		$cus_m_inventory_inbounddetail = new Cus_m_inventory_inbounddetail($cus_m_inventory_inbounddetail_id);
		$m_inventory = $cus_m_inventory_inbounddetail->m_inventory->get();
		$m_product = $cus_m_inventory_inbounddetail->m_product->get();
		$m_grid = $cus_m_inventory_inbounddetail->m_grid->get();
		$c_project = $cus_m_inventory_inbounddetail->c_project->get();
		$received_date = $cus_m_inventory_inbounddetail->received_date;
		$quantity = $cus_m_inventory_inbounddetail->quantity;
		$quantity_box = $cus_m_inventory_inbounddetail->quantity_box;
		
		$inbounddetail_relations = array();
		if (property_exists($data, 'm_inventory_id'))
		{
			$m_inventory = new M_inventory($data->m_inventory_id);
			$inbounddetail_relations['m_inventory'] = $m_inventory;
			unset($data->m_inventory_id);
		}
		if (property_exists($data, 'm_product_id'))
		{
			$m_product = new M_product($data->m_product_id);
			$inbounddetail_relations['m_product'] = $m_product;
			unset($data->m_product_id);
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
		if (property_exists($data, 'c_project_id'))
		{
			$c_project = new C_project($data->c_project_id);
			$inbounddetail_relations['c_project'] = $c_project;
			unset($data->c_project_id);
		}
		if (property_exists($data, 'received_date'))
		{
			$received_date = $data->received_date;
		}
		$data->received_date = $received_date;
		if (property_exists($data, 'quantity'))
		{
			$quantity = $data->quantity;
		}
		if (property_exists($data, 'quantity_box'))
		{
			$quantity_box = $data->quantity_box;
		}
		
		// -- Get Inventory Quantity --
		$quantity_box_inventory = $m_inventory->quantity_box;
		if ($quantity_box > $m_inventory->quantity_box)
			$quantity_box_inventory += $quantity_box - $m_inventory->quantity_box;
		elseif ($m_inventory->quantity_box > $quantity_box)
			$quantity_box_inventory -= $m_inventory->quantity_box - $quantity_box;
		
		$quantity_inventory = $m_inventory->quantity;
		if ($quantity > $m_inventory->quantity)
			$quantity_inventory += $quantity - $m_inventory->quantity;
		elseif ($m_inventory->quantity > $quantity)
			$quantity_inventory -= $m_inventory->quantity - $quantity;
		
		// -- Inventory Update --
		$data_m_inventory = $data;
		$data_m_inventory->m_product_id = $m_product->id;
		$data_m_inventory->m_grid_id = $m_grid->id;
		$data_m_inventory->c_project_id = $c_project->id;
		$data_m_inventory->quantity_box = $quantity_box_inventory;
		$data_m_inventory->quantity = $quantity_inventory;
		
		$data_m_inventory_log = new stdClass();
		$data_m_inventory_log->log_type = "INBOUND";
		$data_m_inventory_log->notes = "Modify Custom Inbound";
		
		$m_inventory_id = $this->CI->lib_inventory->update($m_inventory->id, $data_m_inventory, $updated_by, $data_m_inventory_log);
		
		// -- Custom Inbound Detail Update --
		$data->updated_by = $updated_by;
		
		$this->set_model_fields_values($cus_m_inventory_inbounddetail, $data);
		$m_inventory_inbounddetail_saved = $cus_m_inventory_inbounddetail->save($inbounddetail_relations);
		if (!$m_inventory_inbounddetail_saved)
			throw new Exception($cus_m_inventory_inbounddetail->error->string);
		
		$m_inventory = new M_inventory($m_inventory_id);
		$this->CI->lib_inventory->verify_pallet_grid($m_inventory->pallet, $m_grid->id);
		
		return $cus_m_inventory_inbounddetail_id;
	}
	
	public function inbounddetail_remove($cus_m_inventory_inbounddetail_id, $removed_by = NULL)
	{
		$this->CI->load->library('material/lib_inventory');
		
		$cus_m_inventory_inbounddetail = new Cus_m_inventory_inbounddetail($cus_m_inventory_inbounddetail_id);
		$m_inventory = $cus_m_inventory_inbounddetail->m_inventory->get();
		
		// -- Custom Inbound Detail Delete --
		if (!$cus_m_inventory_inbounddetail->delete())
			throw new Exception($cus_m_inventory_inbounddetail->error->string);
		
		// -- Inventory Delete --
		$data_m_inventory_log = new stdClass();
		$data_m_inventory_log->log_type = "INBOUND";
		$data_m_inventory_log->notes = "Remove Custom Inbound";
		
		$m_inventory_id = $this->CI->lib_inventory->remove($m_inventory->id, $removed_by, $data_m_inventory_log);
		
		return $cus_m_inventory_inbounddetail_id;
	}
	
	public function inbounddetail_parse_barcode($product_code, $barcode)
	{
		$m_product_id = '';
		if ($product_code !== '')
		{
			$table = $this->CI->db
				->select('id')
				->from('m_products')
				->where('code', $product_code)
				->get();
			if ($table->num_rows() > 0)
			{
				$table_record = $table->first_row();
				$m_product_id = $table_record->id;
			}
		}
		return $this->inbounddetail_parse_barcode_by_id($m_product_id, $barcode);
	}
	
	public function inbounddetail_parse_barcode_by_id($m_product_id, $barcode)
	{
		$result = new stdClass();
		$result->quantity = NULL;
		$result->carton_no = NULL;
		$result->packed_date = NULL;
		
		if ($m_product_id !== '' && $barcode !== '')
		{
			$this->CI->db
				->select("cpro.quantity_start, cpro.quantity_end")
				->select("cpro.quantity_point_start, cpro.quantity_point_end")
				->select("cpro.quantity_divider")
				->select("cpro.carton_start, cpro.carton_end")
				->select("cpro.packed_date_start, cpro.packed_date_end")
				->select("pro.netto")
				->from('m_products pro')
				->join('cus_m_products cpro', "cpro.m_product_id = pro.id")
				->where('pro.id', $m_product_id);
			$table = $this->CI->db->get();
			if ($table->num_rows() > 0)
			{
				$m_product_record = $table->first_row();
				
				if ($m_product_record->quantity_start > 0 && $m_product_record->quantity_end > 0)
				{
					$result->quantity = substr($barcode, $m_product_record->quantity_start - 1, $m_product_record->quantity_end - $m_product_record->quantity_start + 1);
					if ($result->quantity === FALSE)
						$result->quantity = 0;
				}
				if ($m_product_record->quantity_point_start > 0 && $m_product_record->quantity_point_end > 0)
				{
					$quantity_point = substr($barcode, $m_product_record->quantity_point_start - 1, $m_product_record->quantity_point_end - $m_product_record->quantity_point_start + 1);
					if ($quantity_point !== FALSE && $quantity_point > 0 && !empty($result->quantity))
					{
						$start = substr($result->quantity, 0, -1 * (int)$quantity_point);
						$end = substr($result->quantity, -1 * (int)$quantity_point);
						if ($start !== FALSE && $end !== FALSE)
							$result->quantity = floatval($start.'.'.$end);
					}
				}
				if ($result->quantity && $m_product_record->quantity_divider > 0)
				{
					$result->quantity = round($result->quantity / $m_product_record->quantity_divider, 4);
				}
				if (!$result->quantity && $m_product_record->netto)
				{
					$result->quantity = $m_product_record->netto;
				}
				if ($m_product_record->carton_start > 0 && $m_product_record->carton_end > 0)
				{
					$result->carton_no = substr($barcode, $m_product_record->carton_start - 1, $m_product_record->carton_end - $m_product_record->carton_start + 1);
					if ($result->carton_no === FALSE)
						$result->carton_no = NULL;
				}
				if ($m_product_record->packed_date_start > 0 && $m_product_record->packed_date_end > 0)
				{
					$result->packed_date = substr($barcode, $m_product_record->packed_date_start - 1, $m_product_record->packed_date_end - $m_product_record->packed_date_start + 1);
					if ($result->packed_date === FALSE)
						$result->packed_date = NULL;
					else
					{
						$date_value = date_create_from_format('ymd', $result->packed_date);
						if ($date_value !== FALSE)
							$result->packed_date = date_format($date_value, 'Y-m-d');
						else
							$result->packed_date = NULL;
					}
				}
			}
		}
		
		return $result;
	}
	
	/* ------------------------------ */
	/* -- USERGROUP PROJECT REGION -- */
	/* ------------------------------ */
	
	public function project_usergroup_add($sys_usergroup_id, $c_project_id, $created_by = NULL)
	{
		$c_project = new C_project($c_project_id);
		$sys_usergroup = new Sys_usergroup($sys_usergroup_id);
		
		$cus_c_project_sys_usergroup = new Cus_c_project_sys_usergroup();
		$cus_c_project_sys_usergroup->created_by = $created_by;
		$cus_c_project_sys_usergroup_saved = $cus_c_project_sys_usergroup->save(
			array(
				'c_project'		=> $c_project, 
				'sys_usergroup'	=> $sys_usergroup
			)
		);
		if (!$cus_c_project_sys_usergroup_saved)
			throw new Exception($cus_c_project_sys_usergroup->error->string);
		
		return $cus_c_project_sys_usergroup->id;
	}
	
	public function project_usergroup_remove($cus_c_project_sys_usergroup_id, $removed_by = NULL)
	{
		$cus_c_project_sys_usergroup = new Cus_c_project_sys_usergroup($cus_c_project_sys_usergroup_id);
		
		// -- Remove UserGroup User --
		if (!$cus_c_project_sys_usergroup->delete())
			throw new Exception($cus_c_project_sys_usergroup->error->string);
		
		return $cus_c_project_sys_usergroup_id;
	}
	
	public function project_get_ids($sys_user_id)
	{
		$c_project_ids = array();
		
		$table = $this->CI->db
			->distinct()
			->select('prjug.c_project_id')
			->from('cus_c_project_sys_usergroups prjug')
			->join('sys_usergroup_users ugu', "ugu.sys_usergroup_id = prjug.sys_usergroup_id")
			->where('ugu.sys_user_id', $sys_user_id)
			->get();
		$records = $table->result();
		foreach ($records as $record)
		{
			$c_project_ids[] = $record->c_project_id;
		}
		return $c_project_ids;
	}
	
	public function project_sql_filter($field, $c_project_ids)
	{
		$wheres = array();
		if (count($c_project_ids) > 0)
		{
			$wheres[] = " ". $field . " IN (" . implode(',', $c_project_ids) . ") ";
		}
		$wheres[] = " ". $field ." IS NULL ";
		
		return "(" . implode(' OR ', $wheres) . ")";
	}
	
	public function project_query_filter($field, $c_project_ids)
	{
		$this->CI->db->where($this->project_sql_filter($field, $c_project_ids), NULL, FALSE);
	}
	
	public function project_is_valid($sys_user_id, $c_project_id)
	{
		if (!$c_project_id)
			return TRUE;
		
		$is_valid = FALSE;
		
		$table = $this->CI->db
			->select('prjug.id')
			->from('cus_c_project_sys_usergroups prjug')
			->join('sys_usergroup_users ugu', "ugu.sys_usergroup_id = prjug.sys_usergroup_id")
			->where('ugu.sys_user_id', $sys_user_id)
			->where('prjug.c_project_id', $c_project_id)
			->get();
		if ($table->num_rows() > 0)
			$is_valid = TRUE;
		
		return $is_valid;
	}
}