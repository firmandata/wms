<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Lib_project extends Lib_general
{
	protected $CI;
	
	function __construct()
    {
		$this->CI =& get_instance();
	}
	
	/* -------------------- */
	/* -- PROJECT REGION -- */
	/* -------------------- */
	
	public function project_add($data, $created_by = NULL)
	{
		$project_relations = array();
		if (property_exists($data, 'c_businesspartner_id'))
		{
			$project_relations['c_businesspartner'] = new C_businesspartner($data->c_businesspartner_id);
			unset($data->c_businesspartner_id);
		}
		
		$data->created_by = $created_by;
		
		$c_project = new C_project();
		$this->set_model_fields_values($c_project, $data);
		$c_project_saved = $c_project->save($project_relations);
		if (!$c_project_saved)
			throw new Exception($c_project->error->string);
		
		return $c_project->id;
	}
	
	public function project_update($c_project_id, $data, $updated_by = NULL)
	{
		$project_relations = array();
		if (property_exists($data, 'c_businesspartner_id'))
		{
			$project_relations['c_businesspartner'] = new C_businesspartner($data->c_businesspartner_id);
			unset($data->c_businesspartner_id);
		}
		
		$data->updated_by = $updated_by;
		
		$c_project = new C_project($c_project_id);
		$this->set_model_fields_values($c_project, $data);
		$c_project_saved = $c_project->save($project_relations);
		if (!$c_project_saved)
			throw new Exception($c_project->error->string);
		
		return $c_project_id;
	}
	
	public function project_remove($c_project_id, $removed_by = NULL)
	{
		$c_project = new C_project($c_project_id);
		
		// -- Remove Project UserGroup --
		$this->CI->load->library('custom/lib_custom');
		foreach ($c_project->cus_c_project_sys_usergroup->get() as $cus_c_project_sys_usergroup)
		{
			$this->CI->lib_custom->project_usergroup_remove($cus_c_project_sys_usergroup->id, $removed_by);
		}
		
		// -- Set Null Order In --
		$this->CI->load->library('core/lib_order');
		foreach ($c_project->c_orderin->get() as $c_orderin)
		{
			$c_orderin_data = new stdClass();
			$c_orderin_data->c_project_id = NULL;
			$this->CI->lib_order->orderin_update($c_orderin->id, $c_orderin_data, $removed_by);
		}
		
		// -- Remove Project --
		if (!$c_project->delete())
			throw new Exception($c_project->error->string);
		
		return $c_project_id;
	}
}