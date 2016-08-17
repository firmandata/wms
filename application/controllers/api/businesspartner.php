<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'/libraries/REST_Controller.php';

class Businesspartner extends REST_Controller
{
	function __construct()
    {
        parent::__construct();
		
		$user_id = $this->session->userdata('user_id');
    }
    
    function list_get()
    {
		if (!is_authorized('core/businesspartner', 'index')) 
			access_denied();
		
		$this->db
			->select("bp.id, bp.code, bp.name, bp.address")
			->select("bp.phone_no, bp.fax_no")
			->select("bp.type, bp.model, bp.credit_limit")
			->select("bp.pic, bp.notes")
			->select("bp.c_region_id, rgn.code c_region_code, rgn.name c_region_name")
			->select("bp.c_department_id, dep.code c_department_code, dep.name c_department_name")
			->from('c_businesspartners bp')
			->join('c_regions rgn', "rgn.id = bp.c_region_id", 'left')
			->join('c_departments dep', "dep.id = bp.c_department_id", 'left');
		
		parent::_get_list_json();
    }
}