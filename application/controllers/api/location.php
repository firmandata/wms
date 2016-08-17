<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'/libraries/REST_Controller.php';

class Location extends REST_Controller
{
	function __construct()
    {
        parent::__construct();
		
		$user_id = $this->session->userdata('user_id');
    }
    
    function list_get()
    {
		if (!is_authorized('core/location', 'index')) 
			access_denied();
		
		$this->db
			->select("loc.id, loc.code, loc.name")
			->select("loc.c_region_id, rgn.code c_region_code, rgn.name c_region_name")
			->select("loc.c_department_id, dep.code c_department_code, dep.name c_department_name")
			->from('c_locations loc')
			->join('c_regions rgn', "rgn.id = loc.c_region_id")
			->join('c_departments dep', "dep.id = loc.c_department_id", 'left');
		
		parent::_get_list_json();
    }
}