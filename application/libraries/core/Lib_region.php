<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Lib_region extends Lib_general
{
	protected $CI;
	
	function __construct()
    {
		$this->CI =& get_instance();
	}
	
	/* ------------------- */
	/* -- REGION REGION -- */
	/* ------------------- */
	
	public function region_add($data, $created_by = NULL)
	{
		$data->created_by = $created_by;
		
		$c_region = new C_region();
		$this->set_model_fields_values($c_region, $data);
		$c_region_saved = $c_region->save();
		if (!$c_region_saved)
			throw new Exception($c_region->error->string);
		
		return $c_region->id;
	}
	
	public function region_update($c_region_id, $data, $updated_by = NULL)
	{
		$data->updated_by = $updated_by;
		
		$c_region = new C_region($c_region_id);
		$this->set_model_fields_values($c_region, $data);
		$c_region_saved = $c_region->save();
		if (!$c_region_saved)
			throw new Exception($c_region->error->string);
		
		return $c_region_id;
	}
	
	public function region_remove($c_region_id, $removed_by = NULL)
	{
		$c_region = new C_region($c_region_id);
		
		// -- Remove Region Location --
		foreach ($c_region->c_location->get() as $c_location)
		{
			$this->location_remove($c_location->id, $removed_by);
		}
		
		// -- Remove Region Asset --
		$this->CI->load->library('asset/lib_asset');
		foreach ($c_region->a_asset->get() as $a_asset)
		{
			$this->CI->lib_asset->asset_remove($a_asset->id, $removed_by);
		}
		
		// -- Set Null Business Partner --
		$this->CI->load->library('core/lib_business');
		foreach ($c_region->c_businesspartner->get() as $c_businesspartner)
		{
			$c_businesspartner_data = new stdClass();
			$c_businesspartner_data->c_region_id = NULL;
			$this->CI->lib_business->businesspartner_update($c_businesspartner->id, $c_businesspartner_data, $removed_by);
		}
		
		// -- Remove Region --
		if (!$c_region->delete())
			throw new Exception($c_region->error->string);
		
		return $c_region_id;
	}
	
	/* --------------------- */
	/* -- LOCATION REGION -- */
	/* --------------------- */
	
	public function location_add($data, $created_by = NULL)
	{
		$location_relations = array();
		if (property_exists($data, 'c_region_id'))
		{
			$location_relations['c_region'] = new C_region($data->c_region_id);
			unset($data->c_region_id);
		}
		if (property_exists($data, 'c_department_id'))
		{
			$location_relations['c_department'] = new C_department($data->c_department_id);
			unset($data->c_department_id);
		}
		
		$data->created_by = $created_by;
		
		$c_location = new C_location();
		$this->set_model_fields_values($c_location, $data);
		$c_location_saved = $c_location->save($location_relations);
		if (!$c_location_saved)
			throw new Exception($c_location->error->string);
		
		return $c_location->id;
	}
	
	public function location_update($c_location_id, $data, $updated_by = NULL)
	{
		$location_relations = array();
		if (property_exists($data, 'c_region_id'))
		{
			$location_relations['c_region'] = new C_region($data->c_region_id);
			unset($data->c_region_id);
		}
		if (property_exists($data, 'c_department_id'))
		{
			$location_relations['c_department'] = new C_department($data->c_department_id);
			unset($data->c_department_id);
		}
		
		$data->updated_by = $updated_by;
		
		$c_location = new C_location($c_location_id);
		$this->set_model_fields_values($c_location, $data);
		$c_location_saved = $c_location->save($location_relations);
		if (!$c_location_saved)
			throw new Exception($c_location->error->string);
		
		return $c_location_id;
	}
	
	public function location_remove($c_location_id, $removed_by = NULL)
	{
		$c_location = new C_location($c_location_id);
		
		// -- Remove Asset --
		if ($c_location->a_asset->count())
		{
			throw new Exception("You can't remove location because it's already in used by asset.");
		}
		
		// -- Remove Location --
		if (!$c_location->delete())
			throw new Exception($c_location->error->string);
		
		return $c_location_id;
	}
	
	/* ----------------------- */
	/* -- DEPARTMENT REGION -- */
	/* ----------------------- */
	
	public function department_add($data, $created_by = NULL)
	{
		$data->created_by = $created_by;
		
		$c_department = new C_department();
		$this->set_model_fields_values($c_department, $data);
		$c_department_saved = $c_department->save();
		if (!$c_department_saved)
			throw new Exception($c_department->error->string);
		
		return $c_department->id;
	}
	
	public function department_update($c_department_id, $data, $updated_by = NULL)
	{
		$data->updated_by = $updated_by;
		
		$c_department = new C_department($c_department_id);
		$this->set_model_fields_values($c_department, $data);
		$c_department_saved = $c_department->save();
		if (!$c_department_saved)
			throw new Exception($c_department->error->string);
		
		return $c_department_id;
	}
	
	public function department_remove($c_department_id, $removed_by = NULL)
	{
		$c_department = new C_department($c_department_id);
		
		// -- Remove Department Location --
		foreach ($c_department->c_location->get() as $c_location)
		{
			$this->location_remove($c_location->id, $removed_by);
		}
		
		// -- Remove Department Asset --
		$this->CI->load->library('asset/lib_asset');
		foreach ($c_department->a_asset->get() as $a_asset)
		{
			$this->CI->lib_asset->asset_remove($a_asset->id, $removed_by);
		}
		
		// -- Set Null Business Partner --
		$this->CI->load->library('core/lib_business');
		foreach ($c_department->c_businesspartner->get() as $c_businesspartner)
		{
			$c_businesspartner_data = new stdClass();
			$c_businesspartner_data->c_deparment_id = NULL;
			$this->CI->lib_business->businesspartner_update($c_businesspartner->id, $c_businesspartner_data, $removed_by);
		}
		
		// -- Remove Department --
		if (!$c_department->delete())
			throw new Exception($c_department->error->string);
		
		return $c_department_id;
	}
}