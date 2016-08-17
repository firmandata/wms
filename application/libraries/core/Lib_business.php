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
		$data->created_by = $created_by;
		
		$c_businesspartner = new C_businesspartner();
		$this->set_model_fields_values($c_businesspartner, $data);
		if (!$c_businesspartner->save())
			throw new Exception($c_businesspartner->error->string);
		
		return $c_businesspartner->id;
	}
	
	public function businesspartner_update($c_businesspartner_id, $data, $updated_by = NULL)
	{
		$data->updated_by = $updated_by;
		
		$c_businesspartner = new C_businesspartner($c_businesspartner_id);
		$this->set_model_fields_values($c_businesspartner, $data);
		if (!$c_businesspartner->save())
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
		
		// -- Remove Business Partner --
		if (!$c_businesspartner->delete())
			throw new Exception($c_businesspartner->error->string);
		
		return $c_businesspartner_id;
	}
}