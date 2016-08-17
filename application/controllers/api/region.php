<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'/libraries/REST_Controller.php';

class Region extends REST_Controller
{
	function __construct()
    {
        parent::__construct();
		
		$user_id = $this->session->userdata('user_id');
    }
    
    function list_get()
    {
		if (!is_authorized('core/region', 'index')) 
			access_denied();
		
		$this->db
			->select("rgn.id, rgn.code, rgn.name")
			->from('c_regions rgn');
		
		parent::_get_list_json();
    }
}