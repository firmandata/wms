<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Lib_business extends Lib_general
{
	protected $CI;
	
	function __construct()
    {
		$this->CI =& get_instance();
	}
	
	/* ----------------------------- */
	/* -- BUSINESS PARTNER REGION -- */
	/* ----------------------------- */
	public function businesspartner_add($data, $created_by = NULL)
	{
		$businesspartner_relations = array();
		if (property_exists($data, 'c_region_id'))
		{
			$businesspartner_relations['c_region'] = new C_region($data->c_region_id);
			unset($data->c_region_id);
		}
		if (property_exists($data, 'c_department_id'))
		{
			$businesspartner_relations['c_department'] = new C_department($data->c_department_id);
			unset($data->c_department_id);
		}
		if (property_exists($data, 'type'))
		{
			if (in_array($data->type, array('SUPPLIER', 'CUSTOMER')))
			{
				$businesspartner_relations['c_region'] = new C_region();
				$businesspartner_relations['c_department'] = new C_department();
				$data->personal_position = NULL;
			}
			if (in_array($data->type, array('EMPLOYEE')))
			{
				$data->model = 'PERSONAL';
			}
		}
		
		$data->created_by = $created_by;
		
		$c_businesspartner = new C_businesspartner();
		$this->set_model_fields_values($c_businesspartner, $data);
		$c_businesspartner_saved = $c_businesspartner->save($businesspartner_relations);
		if (!$c_businesspartner_saved)
			throw new Exception($c_businesspartner->error->string);
		
		return $c_businesspartner->id;
	}
	
	public function businesspartner_update($c_businesspartner_id, $data, $updated_by = NULL)
	{
		$businesspartner_relations = array();
		if (property_exists($data, 'c_region_id'))
		{
			$businesspartner_relations['c_region'] = new C_region($data->c_region_id);
			unset($data->c_region_id);
		}
		if (property_exists($data, 'c_department_id'))
		{
			$businesspartner_relations['c_department'] = new C_department($data->c_department_id);
			unset($data->c_department_id);
		}
		if (property_exists($data, 'type'))
		{
			if (in_array($data->type, array('SUPPLIER', 'CUSTOMER')))
			{
				$businesspartner_relations['c_region'] = new C_region();
				$businesspartner_relations['c_department'] = new C_department();
				$data->personal_position = NULL;
			}
			if (in_array($data->type, array('EMPLOYEE')))
			{
				$data->model = 'PERSONAL';
			}
		}
		
		$data->updated_by = $updated_by;
		
		$c_businesspartner = new C_businesspartner($c_businesspartner_id);
		$this->set_model_fields_values($c_businesspartner, $data);
		$c_businesspartner_saved = $c_businesspartner->save($businesspartner_relations);
		if (!$c_businesspartner_saved)
			throw new Exception($c_businesspartner->error->string);
		
		return $c_businesspartner_id;
	}
	
	public function businesspartner_remove($c_businesspartner_id, $removed_by = NULL)
	{
		$c_businesspartner = new C_businesspartner($c_businesspartner_id);
		
		// -- Remove Order In --
		if ($c_businesspartner->c_orderin->count())
		{
			throw new Exception("You can't remove business partner because it's already in used by order in.");
		}
		
		// -- Remove Order Out --
		if ($c_businesspartner->c_orderout->count())
		{
			throw new Exception("You can't remove business partner because it's already in used by order out.");
		}
		
		// -- Set NULL the Project --
		$this->CI->load->library('core/lib_project');
		foreach ($c_businesspartner->c_project->get() as $c_project)
		{
			$c_project_data = new stdClass();
			$c_project_data->c_businesspartner_id = NULL;
			$this->CI->lib_project->project_update($c_project->id, $c_project_data, $removed_by);
		}
		
		$this->CI->load->library('asset/lib_asset');
		// -- Set NULL the Asset Supplier --
		foreach ($c_businesspartner->a_asset_supplier->get() as $a_asset)
		{
			$a_asset_data = new stdClass();
			$a_asset_data->c_businesspartner_supplier_id = NULL;
			$this->CI->lib_asset->asset_update($a_asset->id, $a_asset_data, $removed_by);
		}
		// -- Set NULL the Asset User --
		foreach ($c_businesspartner->a_asset_user->get() as $a_asset)
		{
			$a_asset_data = new stdClass();
			$a_asset_data->c_businesspartner_user_id = NULL;
			$this->CI->lib_asset->asset_update($a_asset->id, $a_asset_data, $removed_by);
		}
		
		// -- Remove Business Partner --
		if (!$c_businesspartner->delete())
			throw new Exception($c_businesspartner->error->string);
		
		return $c_businesspartner_id;
	}
}