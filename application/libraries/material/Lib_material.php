<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Lib_material extends Lib_general
{
	protected $CI;
	
	function __construct()
    {
		$this->CI =& get_instance();
	}
	
	/* -------------------- */
	/* -- PRODUCT REGION -- */
	/* -------------------- */
	public function product_add($data, $m_productgroup_id = NULL, $created_by = NULL)
	{
		$m_product = new M_product();
		$m_productgroup = new M_productgroup($m_productgroup_id);
		
		$data->created_by = $created_by;
		
		$this->set_model_fields_values($m_product, $data);
		$m_product_saved = $m_product->save(
			array(
				'm_productgroup'	=> $m_productgroup
			)
		);
		if (!$m_product_saved)
			throw new Exception($m_product->error->string);
		
		// -- Add custom product --
		$this->CI->load->library('custom/lib_custom');
		$this->CI->lib_custom->product_add($m_product->id, $data, $created_by);
		
		return $m_product->id;
	}
	
	public function product_update($m_product_id, $data, $m_productgroup_id = NULL, $updated_by = NULL)
	{
		$data->updated_by = $updated_by;
		
		$m_product = new M_product($m_product_id);
		$m_productgroup = new M_productgroup($m_productgroup_id);
		
		$this->set_model_fields_values($m_product, $data);
		$m_product_saved = $m_product->save(
			array(
				'm_productgroup'	=> $m_productgroup
			)
		);
		if (!$m_product_saved)
			throw new Exception($m_product->error->string);
		
		// -- Modify/Add custom product --
		$this->CI->load->library('custom/lib_custom');
		$this->CI->db->where('m_product_id', $m_product_id);
		$table = $this->CI->db->get('cus_m_products');
		if ($table->num_rows() > 0)
		{
			$cus_m_product = $table->first_row();
			$this->CI->lib_custom->product_update($cus_m_product->id, $data, $updated_by);
		}
		else
		{
			$this->CI->lib_custom->product_add($m_product_id, $data, $updated_by);
		}
		
		return $m_product_id;
	}
	
	public function product_remove($m_product_id, $removed_by = NULL)
	{
		$m_product = new M_product($m_product_id);
		
		// -- Remove Order In Detail --
		if ($m_product->c_orderindetail->count())
		{
			throw new Exception("You can't remove product because it's already in used by order in.");
		}
		
		// -- Remove Inventory --
		if ($m_product->m_inventory->count())
		{
			throw new Exception("You can't remove product because it's already in used by inventory.");
		}
		
		// -- Remove Inventory Putaway Detail --
		if ($m_product->m_inventory_putawaydetail->count())
		{
			throw new Exception("You can't remove product because it's already in used by inventory putaway.");
		}
		
		// -- Remove Inventory Move Detail --
		if ($m_product->m_inventory_movedetail->count())
		{
			throw new Exception("You can't remove product because it's already in used by inventory move.");
		}
		
		// -- Remove Inventory Adjust Detail --
		if ($m_product->m_inventory_adjustdetail->count())
		{
			throw new Exception("You can't remove product because it's already in used by inventory adjust.");
		}
		
		// -- Remove Inventory Hold Detail --
		if ($m_product->m_inventory_holddetail->count())
		{
			throw new Exception("You can't remove product because it's already in used by inventory hold.");
		}
		
		// -- Remove Inventory Assembly Source --
		if ($m_product->m_inventory_assemblysource->count())
		{
			throw new Exception("You can't remove product because it's already in used by inventory assembly source.");
		}
		
		// -- Remove Inventory Assembly Target --
		if ($m_product->m_inventory_assemblytarget->count())
		{
			throw new Exception("You can't remove product because it's already in used by inventory assembly target.");
		}
		
		// -- Remove Inventory Picklist Detail --
		if ($m_product->m_inventory_picklistdetail->count())
		{
			throw new Exception("You can't remove product because it's already in used by inventory pick list.");
		}
		
		// -- Remove Order Out Detail --
		if ($m_product->c_orderoutdetail->count())
		{
			throw new Exception("You can't remove product because it's already in used by order out.");
		}
		
		// -- Remove Custom Inbound Detail --
		if ($m_product->cus_m_inventory_inbounddetail->count())
		{
			throw new Exception("You can't remove product because it's already in used by custom inbound detail.");
		}
		
		// -- Remove Custom Product --
		$this->CI->load->library('custom/lib_custom');
		foreach ($m_product->cus_m_product->get() as $cus_m_product)
		{
			$this->CI->lib_custom->product_remove($cus_m_product->id, $removed_by);
		}
		
		// -- Remove Product Category --
		foreach ($m_product->m_product_category->get() as $m_product_category)
		{
			$this->product_category_remove($m_product_category->id, $removed_by);
		}
		
		// -- Remove Asset --
		if ($m_product->a_asset->count())
		{
			throw new Exception("You can't remove product because it's already in used by asset.");
		}
		
		// -- Remove Product --
		if (!$m_product->delete())
			throw new Exception($m_product->error->string);
		
		return $m_product_id;
	}
	
	public function product_set_productgroup($m_product_id, $m_productgroup_id, $set_by = NULL)
	{
		$m_product = new M_product($m_product_id);
		$m_productgroup = new M_productgroup($m_productgroup_id);
		
		$m_product->updated_by = $set_by;
		$m_product_saved = $m_product->save(
			array(
				'm_productgroup'	=> $m_productgroup
			)
		);
		if (!$m_product_saved)
			throw new Exception($m_product->error->string);
		
		return $m_product_id;
	}
	
	/* -------------------------- */
	/* -- PRODUCT GROUP REGION -- */
	/* -------------------------- */
	public function productgroup_add($data, $created_by = NULL)
	{
		$data->created_by = $created_by;
		
		$m_productgroup = new M_productgroup();
		$this->set_model_fields_values($m_productgroup, $data);
		if (!$m_productgroup->save())
			throw new Exception($m_productgroup->error->string);
		
		return $m_productgroup->id;
	}
	
	public function productgroup_update($m_productgroup_id, $data, $updated_by = NULL)
	{
		$data->updated_by = $updated_by;
		
		$m_productgroup = new M_productgroup($m_productgroup_id);
		$this->set_model_fields_values($m_productgroup, $data);
		if (!$m_productgroup->save())
			throw new Exception($m_productgroup->error->string);
		
		return $m_productgroup_id;
	}
	
	public function productgroup_remove($m_productgroup_id, $removed_by = NULL)
	{
		$m_productgroup = new M_productgroup($m_productgroup_id);
		
		// -- Remove Grid --
		if ($m_productgroup->m_grid->count())
		{
			throw new Exception("You can't remove product group because it's already in used by grid.");
		}
		
		// -- Remove Product --
		if ($m_productgroup->m_product->count())
		{
			throw new Exception("You can't remove product group because it's already in used by product.");
		}
		
		// -- Remove Product Group --
		if (!$m_productgroup->delete())
			throw new Exception($m_productgroup->error->string);
		
		return $m_productgroup_id;
	}
	
	/* ---------------------- */
	/* -- WAREHOUSE REGION -- */
	/* ---------------------- */
	public function warehouse_add($data, $created_by = NULL)
	{
		$data->created_by = $created_by;
		
		$m_warehouse = new M_warehouse();
		$this->set_model_fields_values($m_warehouse, $data);
		$m_warehouse_saved = $m_warehouse->save();
		if (!$m_warehouse_saved)
			throw new Exception($m_warehouse->error->string);
		
		return $m_warehouse->id;
	}
	
	public function warehouse_update($m_warehouse_id, $data, $updated_by = NULL)
	{
		$data->updated_by = $updated_by;
		
		$m_warehouse = new M_warehouse($m_warehouse_id);
		
		if ($m_warehouse->code == $this->CI->config->item('inventory_default_warehouse'))
		{
			throw new Exception("You can't modify default location.");
		}
		
		$this->set_model_fields_values($m_warehouse, $data);
		if (!$m_warehouse->save())
			throw new Exception($m_warehouse->error->string);
		
		return $m_warehouse_id;
	}
	
	public function warehouse_remove($m_warehouse_id, $removed_by = NULL)
	{
		$m_warehouse = new M_warehouse($m_warehouse_id);
		
		if ($m_warehouse->code == $this->CI->config->item('inventory_default_warehouse'))
		{
			throw new Exception("You can't remove default location.");
		}
		
		// -- Remove Grid --
		foreach ($m_warehouse->m_grid->get() as $m_grid)
		{
			$this->grid_remove($m_grid->id, $removed_by);
		}
		
		// -- Remove Warehouse --
		if (!$m_warehouse->delete())
			throw new Exception($m_warehouse->error->string);
		
		return $m_warehouse_id;
	}
	
	public function warehouse_generate_grid(
		$m_warehouse_id, 
		$m_grid_rows = 0, $m_grid_cols = 0, $m_grid_levels = 0, 
		$grid_types = NULL, $grid_lengths = 0, $grid_widths = 0, $grid_heights = 0, $grid_statuses = NULL, 
		$generated_by = NULL)
	{
		$m_grid_scalar = $this->warehouse_get_grid_scalar($m_warehouse_id);
		
		$m_warehouse = new M_warehouse($m_warehouse_id);
		if ($m_warehouse->code == $this->CI->config->item('inventory_default_warehouse'))
		{
			throw new Exception("You can't generate grid of default location.");
		}
		
		// -- Delete Grids By Level --
		if ($m_grid_scalar->levels > $m_grid_levels)
		{
			$table = $this->CI->db
				->where('m_warehouse_id', $m_warehouse_id)
				->where('level >', $m_grid_levels)
				->get('m_grids');
			$m_grids = $table->result();
			foreach ($m_grids as $m_grid_idx=>$m_grid)
			{
				$this->grid_remove($m_grid->id, $generated_by);
			}
		}
		
		// -- Delete Grids By Col --
		if ($m_grid_scalar->cols > $m_grid_cols)
		{
			$table = $this->CI->db
				->where('m_warehouse_id', $m_warehouse_id)
				->where('col >', $m_grid_cols)
				->get('m_grids');
			$m_grids = $table->result();
			foreach ($m_grids as $m_grid_idx=>$m_grid)
			{
				$this->grid_remove($m_grid->id, $generated_by);
			}
		}
		
		// -- Delete Grids By Row --
		if ($m_grid_scalar->rows > $m_grid_rows)
		{
			$table = $this->CI->db
				->where('m_warehouse_id', $m_warehouse_id)
				->where('row >', $m_grid_rows)
				->get('m_grids');
			$m_grids = $table->result();
			foreach ($m_grids as $m_grid_idx=>$m_grid)
			{
				$this->grid_remove($m_grid->id, $generated_by);
			}
		}
		
		// -- Add/Update Grids --
		for ($m_grid_row = 1; $m_grid_row <= $m_grid_rows; $m_grid_row++)
		{
			for ($m_grid_col = 1; $m_grid_col <= $m_grid_cols; $m_grid_col++)
			{
				for ($m_grid_level = 1; $m_grid_level <= $m_grid_levels; $m_grid_level++)
				{
					$this->CI->db
						->where('m_warehouse_id', $m_warehouse_id)
						->where('row', $m_grid_row)
						->where('col', $m_grid_col)
						->where('level', $m_grid_level);
					$table = $this->CI->db->get('m_grids');
					if ($table->num_rows() == 0)
					{
						// -- Add Grid --
						$this->grid_add(
							$m_warehouse_id, 
							$m_grid_row, $m_grid_col, $m_grid_level, NULL, 
							$grid_types, $grid_lengths, $grid_widths, $grid_heights, $grid_statuses, 
							NULL, $generated_by
						);
					}
					else
					{
						$m_grid_record = $table->first_row();
						
						// -- Update Grid --
						$this->grid_update(
							$m_grid_record->id, 
							$m_grid_record->row, $m_grid_record->col, $m_grid_record->level, $m_grid_record->m_productgroup_id, 
							$grid_types, $grid_lengths, $grid_widths, $grid_heights, $grid_statuses, 
							$m_grid_record->notes, $generated_by
						);
					}
				}
			}
		}
	}
	
	public function warehouse_default_generate($generated_by)
	{
		$m_warehouse_id = NULL;
		
		// -- Create default warehouse --
		$table = $this->CI->db
			->select('id')
			->from('m_warehouses')
			->where('code', $this->CI->config->item('inventory_default_warehouse'))
			->get();
		if ($table->num_rows() > 0)
		{
			$table_record = $table->first_row();
			$m_warehouse_id = $table_record->id;
		}
		else
		{
			$m_warehouse_new = new stdClass();
			$m_warehouse_new->code = $this->CI->config->item('inventory_default_warehouse');
			$m_warehouse_new->name = 'Default Location';
			$m_warehouse_id = $this->warehouse_add($m_warehouse_new, $generated_by);
		}
		
		// -- Create default grid by product group default --
		$m_product_groups = array(
			$this->CI->config->item('inventory_default_grid')	=> $this->CI->config->item('product_group_default'),
			$this->CI->config->item('inventory_default_grid_1')	=> $this->CI->config->item('product_group_default_1')
		);
		foreach ($m_product_groups as $m_grid_code=>$m_product_group)
		{
			$table = $this->CI->db
				->select('id')
				->from('m_grids')
				->where('code', $m_grid_code)
				->get();
			if ($table->num_rows() > 0)
				continue;
			
			$m_productgroup_id = NULL;
			$table = $this->CI->db
				->select('id')
				->from('m_productgroups')
				->where('code', $m_product_group['code'])
				->get();
			if ($table->num_rows() > 0)
			{
				$table_record = $table->first_row();
				$m_productgroup_id = $table_record->id;
			}
			else
			{
				$m_product_group_new = new stdClass();
				foreach ($m_product_group as $field=>$value)
				{
					$m_product_group_new->$field = $value;
				}
				$m_productgroup_id = $this->productgroup_add($m_product_group_new, $generated_by);
			}
			
			// -- Create default grid --
			$this->grid_add(
				$m_warehouse_id, 
				0, 0, 0, $m_productgroup_id, 
				NULL, 0, 0, 0, NULL, 
				NULL, $generated_by,
				$m_grid_code
			);
		}
	}
	
	public function warehouse_get_grid_scalar($m_warehouse_id)
	{
		$result = new stdClass();
		$result->rows = 0;
		$result->cols = 0;
		$result->levels = 0;
		$result->types = '';
		$result->lengths = 0;
		$result->widths = 0;
		$result->heights = 0;
		$result->statuses = '';
		
		$this->CI->db
			->select_max('row')
			->select_max('col')
			->select_max('level')
			->select_max('type')
			->select_max('length')
			->select_max('width')
			->select_max('height')
			->select_max('status')
			->where('m_warehouse_id', $m_warehouse_id);
		$table_scalar = $this->CI->db->get('m_grids');
		if ($table_scalar->num_rows() > 0)
		{
			$record_scalar = $table_scalar->first_row();
			$result->rows = ($record_scalar->row !== NULL ? $record_scalar->row : 0);
			$result->cols = ($record_scalar->col !== NULL ? $record_scalar->col : 0);
			$result->levels = ($record_scalar->level !== NULL ? $record_scalar->level : 0);
			$result->types = ($record_scalar->type !== NULL ? $record_scalar->type : '');
			$result->lengths = ($record_scalar->length !== NULL ? $record_scalar->length : 0);
			$result->widths = ($record_scalar->width !== NULL ? $record_scalar->width : 0);
			$result->heights = ($record_scalar->height !== NULL ? $record_scalar->height : 0);
			$result->statuses = ($record_scalar->status !== NULL ? $record_scalar->status : '');
		}
		
		return $result;
	}
	
	/* ----------------- */
	/* -- GRID REGION -- */
	/* ----------------- */
	public function grid_add(
		$m_warehouse_id, 
		$row, $col, $level, $m_productgroup_id = NULL,
		$type = NULL, $length = 0, $width = 0, $height = 0, $status = NULL, 
		$notes = NULL, $created_by = NULL,
		$code_default = NULL
	)
	{
		$m_warehouse = new M_warehouse($m_warehouse_id);
		$m_productgroup = new M_productgroup($m_productgroup_id);
		
		$code_row = sprintf("%02s", $row);
		$code_col = sprintf("%02s", $col);
		$code_level = sprintf("%02s", $level);
		
		$code = $m_warehouse->code . $code_row.$code_col.$code_level;
		if ($code_default !== NULL)
		{
			$code = $code_default;
		}
		
		$m_grid = new M_grid();
		$m_grid->code = $code;
		$m_grid->row = $row;
		$m_grid->col = $col;
		$m_grid->level = $level;
		$m_grid->type = $type;
		$m_grid->length = $length;
		$m_grid->width = $width;
		$m_grid->height = $height;
		$m_grid->status = $status;
		$m_grid->notes = $notes;
		$m_grid->created_by = $created_by;
		$m_grid_saved = $m_grid->save(
			array(
				'm_warehouse'		=> $m_warehouse,
				'm_productgroup'	=> $m_productgroup
			)
		);
		if (!$m_grid_saved)
			throw new Exception($m_grid->error->string);
		
		// -- Insert to grid usage --
		$this->CI->load->library('custom/lib_custom_inventory');
		$data_m_grid_usage = new stdClass();
		$this->CI->lib_custom_inventory->grid_usage_add($m_grid->id, $data_m_grid_usage, $created_by);
		
		return $m_grid->id;
	}
	
	public function grid_update(
		$m_grid_id, 
		$row, $col, $level, $m_productgroup_id = NULL, 
		$type = NULL, $length = 0, $width = 0, $height = 0, $status = NULL, 
		$notes = NULL, $updated_by = NULL
	)
	{
		$m_grid = new M_grid($m_grid_id);
		if (	$m_grid->code == $this->CI->config->item('inventory_default_grid')
			||	$m_grid->code == $this->CI->config->item('inventory_default_grid_1'))
		{
			throw new Exception("You can't modify default location detail.");
		}
		
		$m_productgroup = new M_productgroup($m_productgroup_id);
		
		$code_row = sprintf("%02s", $row);
		$code_col = sprintf("%02s", $col);
		$code_level = sprintf("%02s", $level);
		
		$m_grid->code = $m_grid->m_warehouse->get()->code . $code_row.$code_col.$code_level;
		$m_grid->row = $row;
		$m_grid->col = $col;
		$m_grid->level = $level;
		$m_grid->type = $type;
		$m_grid->length = $length;
		$m_grid->width = $width;
		$m_grid->height = $height;
		$m_grid->status = $status;
		$m_grid->notes = $notes;
		$m_grid->updated_by = $updated_by;
		$m_grid_saved = $m_grid->save(
			array(
				'm_productgroup'	=> $m_productgroup
			)
		);
		if (!$m_grid_saved)
			throw new Exception($m_grid->error->string);
		
		// -- Update to grid usage --
		$this->CI->load->library('custom/lib_custom_inventory');
		$data_m_grid_usage = new stdClass();
		$this->CI->lib_custom_inventory->grid_usage_update_by_grid($m_grid->id, $data_m_grid_usage, $updated_by);
		
		return $m_grid_id;
	}
	
	public function grid_remove($m_grid_id, $removed_by = NULL)
	{
		$m_grid = new M_grid($m_grid_id);
		if (	$m_grid->code == $this->CI->config->item('inventory_default_grid')
			||	$m_grid->code == $this->CI->config->item('inventory_default_grid_1'))
		{
			throw new Exception("You can't remove default location detail.");
		}
		
		// -- Remove Inventory --
		if ($m_grid->m_inventory->count())
		{
			throw new Exception("You can't remove location detail because it's already in used by inventory.");
		}
		
		// -- Remove Putaway From --
		if ($m_grid->m_inventory_putawaydetail_from->count())
		{
			throw new Exception("You can't remove location detail because it's already in used by putaway from.");
		}
		
		// -- Remove Putaway To --
		if ($m_grid->m_inventory_putawaydetail_to->count())
		{
			throw new Exception("You can't remove location detail because it's already in used by putaway to.");
		}
		
		// -- Remove Move From --
		if ($m_grid->m_inventory_movedetail_from->count())
		{
			throw new Exception("You can't remove location detail because it's already in used by move from.");
		}
		
		// -- Remove Move To --
		if ($m_grid->m_inventory_movedetail_to->count())
		{
			throw new Exception("You can't remove location detail because it's already in used by move to.");
		}
		
		// -- Remove Pick List --
		if ($m_grid->m_inventory_picklistdetail->count())
		{
			throw new Exception("You can't remove location detail because it's already in used by pick list.");
		}
		
		// -- Remove Inbound Detail --
		if ($m_grid->m_inventory_inbounddetail->count())
		{
			throw new Exception("You can't remove location detail because it's already in used by inbound.");
		}
		
		// -- Remove Adjust Detail --
		if ($m_grid->m_inventory_adjustdetail->count())
		{
			throw new Exception("You can't remove location detail because it's already in used by adjust.");
		}
		
		// -- Remove Hold Detail --
		if ($m_grid->m_inventory_holddetail->count())
		{
			throw new Exception("You can't remove location detail because it's already in used by hold.");
		}
		
		// -- Remove Assembly Source --
		if ($m_grid->m_inventory_assemblysource->count())
		{
			throw new Exception("You can't remove location detail because it's already in used by assembly source.");
		}
		
		// -- Remove Assembly Target --
		if ($m_grid->m_inventory_assemblytarget->count())
		{
			throw new Exception("You can't remove location detail because it's already in used by assembly target.");
		}
		
		// -- Remove Custom Inventory Inbound Detail --
		if ($m_grid->cus_m_inventory_inbounddetail->count())
		{
			throw new Exception("You can't remove location detail because it's already in used by inbound live.");
		}
		
		$this->CI->load->library('custom/lib_custom_inventory');
		
		// -- Remove Custom Inventory Forecast Detail --
		foreach ($m_grid->cus_m_inventory_forecastdetail->get() as $cus_m_inventory_forecastdetail)
		{
			$this->CI->lib_custom_inventory->forecastdetail_remove($cus_m_inventory_forecastdetail->id, $removed_by);
		}
		
		// -- Remove Custom Grid Usage --
		$this->CI->lib_custom_inventory->grid_usage_remove_by_grid($m_grid->id, $removed_by);
		
		// -- Remove Grid --
		if (!$m_grid->delete())
			throw new Exception($m_grid->error->string);
		
		return $m_grid_id;
	}
	
	public function grid_set_productgroup($m_grid_id, $m_productgroup_id, $set_by = NULL)
	{
		$m_grid = new M_grid($m_grid_id);
		if (	$m_grid->code == $this->CI->config->item('inventory_default_grid')
			||	$m_grid->code == $this->CI->config->item('inventory_default_grid_1'))
		{
			throw new Exception("You can't modify default location detail.");
		}
		
		$m_productgroup = new M_productgroup($m_productgroup_id);
		
		$m_grid->updated_by = $set_by;
		$m_grid_saved = $m_grid->save(
			array(
				'm_productgroup'	=> $m_productgroup
			)
		);
		if (!$m_grid_saved)
			throw new Exception($m_grid->error->string);
		
		return $m_grid_id;
	}
	
	/* --------------------- */
	/* -- CATEGORY REGION -- */
	/* --------------------- */
	public function category_add($data, $created_by = NULL)
	{
		$data->created_by = $created_by;
		
		$m_category = new M_category();
		$this->set_model_fields_values($m_category, $data);
		if (!$m_category->save())
			throw new Exception($m_category->error->string);
		
		return $m_category->id;
	}
	
	public function category_update($m_category_id, $data, $updated_by = NULL)
	{
		$data->updated_by = $updated_by;
		
		$m_category = new M_category($m_category_id);
		$this->set_model_fields_values($m_category, $data);
		if (!$m_category->save())
			throw new Exception($m_category->error->string);
		
		return $m_category_id;
	}
	
	public function category_remove($m_category_id, $removed_by = NULL)
	{
		$m_category = new M_category($m_category_id);
		
		// -- Remove Product Category --
		foreach ($m_category->m_product_category->get() as $m_product_category)
		{
			$this->product_category_remove($m_product_category->id, $removed_by);
		}
		
		// -- Remove Category --
		if (!$m_category->delete())
			throw new Exception($m_category->error->string);
		
		return $m_category_id;
	}
	
	/* ----------------------------- */
	/* -- PRODUCT CATEGORY REGION -- */
	/* ----------------------------- */
	public function product_category_add($data, $created_by = NULL)
	{
		$product_category_relations = array();
		if (property_exists($data, 'm_product_id'))
		{
			$product_category_relations['m_product'] = new M_product($data->m_product_id);
			unset($data->m_product_id);
		}
		if (property_exists($data, 'm_category_id'))
		{
			$product_category_relations['m_category'] = new M_category($data->m_category_id);
			unset($data->m_category_id);
		}
		
		$data->created_by = $created_by;
		
		$m_product_category = new M_product_category();
		$this->set_model_fields_values($m_product_category, $data);
		$product_category_saved = $m_product_category->save($product_category_relations);
		if (!$product_category_saved)
			throw new Exception($m_product_category->error->string);
		
		return $m_product_category->id;
	}
	
	public function product_category_update($m_product_category_id, $data, $updated_by = NULL)
	{
		$product_category_relations = array();
		if (property_exists($data, 'm_product_id'))
		{
			$product_category_relations['m_product'] = new M_product($data->m_product_id);
			unset($data->m_product_id);
		}
		if (property_exists($data, 'm_category_id'))
		{
			$product_category_relations['m_category'] = new M_category($data->m_category_id);
			unset($data->m_category_id);
		}
		
		$data->updated_by = $updated_by;
		
		$m_product_category = new M_product_category($m_product_category_id);
		$this->set_model_fields_values($m_product_category, $data);
		$product_category_saved = $m_product_category->save($product_category_relations);
		if (!$product_category_saved)
			throw new Exception($m_product_category->error->string);
		
		return $m_product_category_id;
	}
	
	public function product_category_remove($m_product_category_id, $removed_by = NULL)
	{
		$m_product_category = new M_product_category($m_product_category_id);
		
		if (!$m_product_category->delete())
			throw new Exception($m_product_category->error->string);
		
		return $m_product_category_id;
	}
}