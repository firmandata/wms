<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Lib_inventory_activity extends Lib_general
{
	protected $CI;
	
	function __construct()
    {
		$this->CI =& get_instance();
		
		$this->CI->load->library('custom/lib_custom');
		$this->CI->load->library('material/lib_inventory_in');
		$this->CI->load->library('material/lib_inventory_out');
	}
	
	/* ------------------- */
	/* -- SAMPLE REGION -- */
	/* ------------------- */
	
	public function sample_add($data, $created_by = NULL)
	{
		$sample_relations = array();
		
		$data->created_by = $created_by;
		
		$m_inventory_sample = new M_inventory_sample();
		$this->set_model_fields_values($m_inventory_sample, $data);
		$m_inventory_sample_saved = $m_inventory_sample->save($sample_relations);
		if (!$m_inventory_sample_saved)
			throw new Exception($m_inventory_sample->error->string);
		
		return $m_inventory_sample->id;
	}
	
	public function sample_update($m_inventory_sample_id, $data, $updated_by = NULL)
	{
		$sample_relations = array();
		
		$data->updated_by = $updated_by;
		
		$m_inventory_sample = new M_inventory_sample($m_inventory_sample_id);
		$this->set_model_fields_values($m_inventory_sample, $data);
		$m_inventory_sample_saved = $m_inventory_sample->save($sample_relations);
		if (!$m_inventory_sample_saved)
			throw new Exception($m_inventory_sample->error->string);
		
		return $m_inventory_sample_id;
	}
	
	public function sample_remove($m_inventory_sample_id, $removed_by = NULL)
	{
		$m_inventory_sample = new M_inventory_sample($m_inventory_sample_id);
		
		// -- Remove Sample Detail --
		foreach ($m_inventory_sample->m_inventory_sampledetail->get() as $m_inventory_sampledetail)
		{
			$this->sampledetail_remove($m_inventory_sampledetail->id, $removed_by);
		}
		
		// -- Remove Sample --
		if (!$m_inventory_sample->delete())
			throw new Exception($m_inventory_sample->error->string);
		
		return $m_inventory_sample_id;
	}
	
	public function sampledetail_add($data, $created_by = NULL)
	{
		$m_inventory_sample = new M_inventory_sample();
		$m_grid = new M_grid();
		
		$sampledetail_relations = array();
		if (property_exists($data, 'm_inventory_sample_id'))
		{
			$m_inventory_sample = new M_inventory_sample($data->m_inventory_sample_id);
			$sampledetail_relations['m_inventory_sample'] = $m_inventory_sample;
			unset($data->m_inventory_sample_id);
		}
		if (property_exists($data, 'm_grid_id'))
		{
			$m_grid = new M_grid($data->m_grid_id);
			$sampledetail_relations['m_grid'] = $m_grid;
			unset($data->m_grid_id);
		}
		
		$data->created_by = $created_by;
		
		$m_inventory_sampledetail = new M_inventory_sampledetail();
		$this->set_model_fields_values($m_inventory_sampledetail, $data);
		$m_inventory_sampledetail_saved = $m_inventory_sampledetail->save($sampledetail_relations);
		if (!$m_inventory_sampledetail_saved)
			throw new Exception($m_inventory_sampledetail->error->string);
		
		// -- Add sample inventory --
		$c_project_ids = $this->CI->lib_custom->project_get_ids($created_by);
		$this->CI->db
			->select("inv.id")
			->from('m_inventories inv')
			->where('inv.m_grid_id', $m_grid->id)
			->where('inv.quantity_box >', 0)
			->where('inv.quantity >', 0);
		$this->CI->lib_custom->project_query_filter('inv.c_project_id', $c_project_ids);
		$table = $this->CI->db
			->get();
		$m_inventories = $table->result();
		foreach ($m_inventories as $m_inventory_idx=>$m_inventory)
		{
			$data_sampleinventory = new stdClass();
			$data_sampleinventory->m_inventory_sampledetail_id = $m_inventory_sampledetail->id;
			$data_sampleinventory->m_inventory_id = $m_inventory->id;
			$this->sampleinventory_add($data_sampleinventory, $created_by);
		}
		
		return $m_inventory_sampledetail->id;
	}
	
	public function sampledetail_update($m_inventory_sampledetail_id, $data, $updated_by = NULL)
	{
		$m_inventory_sampledetail = new M_inventory_sampledetail($m_inventory_sampledetail_id);
		$m_grid_new = new M_grid();
		$m_grid = $m_inventory_sampledetail->m_grid->get();
		$m_inventory_sample = $m_inventory_sampledetail->m_inventory_sample->get();
		
		$sampledetail_relations = array();
		if (property_exists($data, 'm_inventory_sample_id'))
		{
			$m_inventory_sample = new M_inventory_sample($data->m_inventory_sample_id);
			$sampledetail_relations['m_inventory_sample'] = $m_inventory_sample;
			unset($data->m_inventory_sample_id);
		}
		if (property_exists($data, 'm_grid_id'))
		{
			$m_grid_new = new M_grid($data->m_grid_id);
			$sampledetail_relations['m_grid'] = $m_grid_new;
			unset($data->m_grid_id);
		}
		
		// -- Update Sample Detail --
		$data->updated_by = $updated_by;
		
		$this->set_model_fields_values($m_inventory_sampledetail, $data);
		$m_inventory_sampledetail_saved = $m_inventory_sampledetail->save($sampledetail_relations);
		if (!$m_inventory_sampledetail_saved)
			throw new Exception($m_inventory_sampledetail->error->string);
		
		if ($m_grid->id != $m_grid_new->id)
		{
			// -- Remove Sample Inventory --
			foreach ($m_inventory_sampledetail->m_inventory_sampleinventory->get() as $m_inventory_sampleinventory)
			{
				$this->sampleinventory_remove($m_inventory_sampleinventory->id, $updated_by);
			}
			
			// -- Add new Sample Inventory --
			$c_project_ids = $this->CI->lib_custom->project_get_ids($updated_by);
			$this->CI->db
				->select("inv.id")
				->from('m_inventories inv')
				->where('inv.m_grid_id', $m_grid_new->id)
				->where('inv.quantity_box >', 0)
				->where('inv.quantity >', 0);
			$this->CI->lib_custom->project_query_filter('inv.c_project_id', $c_project_ids);
			$table = $this->CI->db
				->get();
			$m_inventories = $table->result();
			foreach ($m_inventories as $m_inventory_idx=>$m_inventory)
			{
				$data_sampleinventory = new stdClass();
				$data_sampleinventory->m_inventory_sampledetail_id = $m_inventory_sampledetail->id;
				$data_sampleinventory->m_inventory_id = $m_inventory->id;
				$this->sampleinventory_add($data_sampleinventory, $updated_by);
			}
		}
		
		return $m_inventory_sampledetail_id;
	}
	
	public function sampledetail_remove($m_inventory_sampledetail_id, $removed_by = NULL)
	{
		$m_inventory_sampledetail = new M_inventory_sampledetail($m_inventory_sampledetail_id);
		
		// -- Remove Sample Inventory --
		foreach ($m_inventory_sampledetail->m_inventory_sampleinventory->get() as $m_inventory_sampleinventory)
		{
			$this->sampleinventory_remove($m_inventory_sampleinventory->id, $removed_by);
		}
		
		// -- Remove Sample Detail --
		if (!$m_inventory_sampledetail->delete())
			throw new Exception($m_inventory_sampledetail->error->string);
		
		return $m_inventory_sampledetail_id;
	}
	
	protected function sampleinventory_add($data, $created_by = NULL)
	{
		$sampleinventory_relations = array();
		if (property_exists($data, 'm_inventory_sampledetail_id'))
		{
			$m_inventory_sampledetail = new M_inventory_sampledetail($data->m_inventory_sampledetail_id);
			$sampleinventory_relations['m_inventory_sampledetail'] = $m_inventory_sampledetail;
			unset($data->m_inventory_sampledetail_id);
		}
		if (property_exists($data, 'm_inventory_id'))
		{
			$m_inventory = new M_inventory($data->m_inventory_id);
			$sampleinventory_relations['m_inventory'] = $m_inventory;
			unset($data->m_inventory_id);
			
			$sampleinventory_relations['m_product'] = $m_inventory->m_product->get();
			$sampleinventory_relations['m_grid'] = $m_inventory->m_grid->get();
			$sampleinventory_relations['c_project'] = $m_inventory->c_project->get();
			
			$data->quantity_box = $m_inventory->quantity_box;
			$data->quantity = $m_inventory->quantity;
			$data->pallet = $m_inventory->pallet;
			$data->barcode = $m_inventory->barcode;
			$data->carton_no = $m_inventory->carton_no;
			$data->lot_no = $m_inventory->lot_no;
		}
		
		$data->created_by = $created_by;
		
		$m_inventory_sampleinventory = new M_inventory_sampleinventory();
		$this->set_model_fields_values($m_inventory_sampleinventory, $data);
		$m_inventory_sampleinventory_saved = $m_inventory_sampleinventory->save($sampleinventory_relations);
		if (!$m_inventory_sampleinventory_saved)
			throw new Exception($m_inventory_sampleinventory->error->string);
		
		return $m_inventory_sampleinventory->id;
	}
	
	protected function sampleinventory_remove($m_inventory_sampleinventory_id, $removed_by = NULL)
	{
		$m_inventory_sampleinventory = new M_inventory_sampleinventory($m_inventory_sampleinventory_id);
		
		// -- Remove Sample Inventory --
		if (!$m_inventory_sampleinventory->delete())
			throw new Exception($m_inventory_sampleinventory->error->string);
		
		return $m_inventory_sampleinventory_id;
	}
	
	/* ------------------- */
	/* -- WATER REGION -- */
	/* ------------------- */
	
	public function water_add($data, $created_by = NULL)
	{
		$water_relations = array();
		
		$data->created_by = $created_by;
		
		$m_inventory_water = new M_inventory_water();
		$this->set_model_fields_values($m_inventory_water, $data);
		$m_inventory_water_saved = $m_inventory_water->save($water_relations);
		if (!$m_inventory_water_saved)
			throw new Exception($m_inventory_water->error->string);
		
		return $m_inventory_water->id;
	}
	
	public function water_update($m_inventory_water_id, $data, $updated_by = NULL)
	{
		$water_relations = array();
		
		$data->updated_by = $updated_by;
		
		$m_inventory_water = new M_inventory_water($m_inventory_water_id);
		$this->set_model_fields_values($m_inventory_water, $data);
		$m_inventory_water_saved = $m_inventory_water->save($water_relations);
		if (!$m_inventory_water_saved)
			throw new Exception($m_inventory_water->error->string);
		
		return $m_inventory_water_id;
	}
	
	public function water_remove($m_inventory_water_id, $removed_by = NULL)
	{
		$m_inventory_water = new M_inventory_water($m_inventory_water_id);
		
		// -- Remove Water Detail --
		foreach ($m_inventory_water->m_inventory_waterdetail->get() as $m_inventory_waterdetail)
		{
			$this->waterdetail_remove($m_inventory_waterdetail->id, $removed_by);
		}
		
		// -- Remove Water --
		if (!$m_inventory_water->delete())
			throw new Exception($m_inventory_water->error->string);
		
		return $m_inventory_water_id;
	}
	
	public function waterdetail_add($data, $created_by = NULL)
	{
		$m_inventory_water = new M_inventory_water();
		$m_grid = new M_grid();
		
		$waterdetail_relations = array();
		if (property_exists($data, 'm_inventory_water_id'))
		{
			$m_inventory_water = new M_inventory_water($data->m_inventory_water_id);
			$waterdetail_relations['m_inventory_water'] = $m_inventory_water;
			unset($data->m_inventory_water_id);
		}
		if (property_exists($data, 'm_grid_id'))
		{
			$m_grid = new M_grid($data->m_grid_id);
			$waterdetail_relations['m_grid'] = $m_grid;
			unset($data->m_grid_id);
		}
		
		$data->created_by = $created_by;
		
		$m_inventory_waterdetail = new M_inventory_waterdetail();
		$this->set_model_fields_values($m_inventory_waterdetail, $data);
		$m_inventory_waterdetail_saved = $m_inventory_waterdetail->save($waterdetail_relations);
		if (!$m_inventory_waterdetail_saved)
			throw new Exception($m_inventory_waterdetail->error->string);
		
		// -- Add water inventory --
		$c_project_ids = $this->CI->lib_custom->project_get_ids($created_by);
		$this->CI->db
			->select("inv.id")
			->from('m_inventories inv')
			->where('inv.m_grid_id', $m_grid->id)
			->where('inv.quantity_box >', 0)
			->where('inv.quantity >', 0);
		$this->CI->lib_custom->project_query_filter('inv.c_project_id', $c_project_ids);
		$table = $this->CI->db
			->get();
		$m_inventories = $table->result();
		foreach ($m_inventories as $m_inventory_idx=>$m_inventory)
		{
			$data_waterinventory = new stdClass();
			$data_waterinventory->m_inventory_waterdetail_id = $m_inventory_waterdetail->id;
			$data_waterinventory->m_inventory_id = $m_inventory->id;
			$this->waterinventory_add($data_waterinventory, $created_by);
		}
		
		return $m_inventory_waterdetail->id;
	}
	
	public function waterdetail_update($m_inventory_waterdetail_id, $data, $updated_by = NULL)
	{
		$m_inventory_waterdetail = new M_inventory_waterdetail($m_inventory_waterdetail_id);
		$m_grid_new = new M_grid();
		$m_grid = $m_inventory_waterdetail->m_grid->get();
		$m_inventory_water = $m_inventory_waterdetail->m_inventory_water->get();
		
		$waterdetail_relations = array();
		if (property_exists($data, 'm_inventory_water_id'))
		{
			$m_inventory_water = new M_inventory_water($data->m_inventory_water_id);
			$waterdetail_relations['m_inventory_water'] = $m_inventory_water;
			unset($data->m_inventory_water_id);
		}
		if (property_exists($data, 'm_grid_id'))
		{
			$m_grid_new = new M_grid($data->m_grid_id);
			$waterdetail_relations['m_grid'] = $m_grid_new;
			unset($data->m_grid_id);
		}
		
		// -- Update Water Detail --
		$data->updated_by = $updated_by;
		
		$this->set_model_fields_values($m_inventory_waterdetail, $data);
		$m_inventory_waterdetail_saved = $m_inventory_waterdetail->save($waterdetail_relations);
		if (!$m_inventory_waterdetail_saved)
			throw new Exception($m_inventory_waterdetail->error->string);
		
		if ($m_grid->id != $m_grid_new->id)
		{
			// -- Remove Water Inventory --
			foreach ($m_inventory_waterdetail->m_inventory_waterinventory->get() as $m_inventory_waterinventory)
			{
				$this->waterinventory_remove($m_inventory_waterinventory->id, $updated_by);
			}
			
			// -- Add new Water Inventory --
			$c_project_ids = $this->CI->lib_custom->project_get_ids($updated_by);
			$this->CI->db
				->select("inv.id")
				->from('m_inventories inv')
				->where('inv.m_grid_id', $m_grid_new->id)
				->where('inv.quantity_box >', 0)
				->where('inv.quantity >', 0);
			$this->CI->lib_custom->project_query_filter('inv.c_project_id', $c_project_ids);
			$table = $this->CI->db
				->get();
			$m_inventories = $table->result();
			foreach ($m_inventories as $m_inventory_idx=>$m_inventory)
			{
				$data_waterinventory = new stdClass();
				$data_waterinventory->m_inventory_waterdetail_id = $m_inventory_waterdetail->id;
				$data_waterinventory->m_inventory_id = $m_inventory->id;
				$this->waterinventory_add($data_waterinventory, $updated_by);
			}
		}
		
		return $m_inventory_waterdetail_id;
	}
	
	public function waterdetail_remove($m_inventory_waterdetail_id, $removed_by = NULL)
	{
		$m_inventory_waterdetail = new M_inventory_waterdetail($m_inventory_waterdetail_id);
		
		// -- Remove Water Inventory --
		foreach ($m_inventory_waterdetail->m_inventory_waterinventory->get() as $m_inventory_waterinventory)
		{
			$this->waterinventory_remove($m_inventory_waterinventory->id, $removed_by);
		}
		
		// -- Remove Water Detail --
		if (!$m_inventory_waterdetail->delete())
			throw new Exception($m_inventory_waterdetail->error->string);
		
		return $m_inventory_waterdetail_id;
	}
	
	protected function waterinventory_add($data, $created_by = NULL)
	{
		$waterinventory_relations = array();
		if (property_exists($data, 'm_inventory_waterdetail_id'))
		{
			$m_inventory_waterdetail = new M_inventory_waterdetail($data->m_inventory_waterdetail_id);
			$waterinventory_relations['m_inventory_waterdetail'] = $m_inventory_waterdetail;
			unset($data->m_inventory_waterdetail_id);
		}
		if (property_exists($data, 'm_inventory_id'))
		{
			$m_inventory = new M_inventory($data->m_inventory_id);
			$waterinventory_relations['m_inventory'] = $m_inventory;
			unset($data->m_inventory_id);
			
			$waterinventory_relations['m_product'] = $m_inventory->m_product->get();
			$waterinventory_relations['m_grid'] = $m_inventory->m_grid->get();
			$waterinventory_relations['c_project'] = $m_inventory->c_project->get();
			
			$data->quantity_box = $m_inventory->quantity_box;
			$data->quantity = $m_inventory->quantity;
			$data->pallet = $m_inventory->pallet;
			$data->barcode = $m_inventory->barcode;
			$data->carton_no = $m_inventory->carton_no;
			$data->lot_no = $m_inventory->lot_no;
		}
		
		$data->created_by = $created_by;
		
		$m_inventory_waterinventory = new M_inventory_waterinventory();
		$this->set_model_fields_values($m_inventory_waterinventory, $data);
		$m_inventory_waterinventory_saved = $m_inventory_waterinventory->save($waterinventory_relations);
		if (!$m_inventory_waterinventory_saved)
			throw new Exception($m_inventory_waterinventory->error->string);
		
		return $m_inventory_waterinventory->id;
	}
	
	protected function waterinventory_remove($m_inventory_waterinventory_id, $removed_by = NULL)
	{
		$m_inventory_waterinventory = new M_inventory_waterinventory($m_inventory_waterinventory_id);
		
		// -- Remove Water Inventory --
		if (!$m_inventory_waterinventory->delete())
			throw new Exception($m_inventory_waterinventory->error->string);
		
		return $m_inventory_waterinventory_id;
	}
}