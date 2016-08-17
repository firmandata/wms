<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'/libraries/REST_Controller.php';

class System_login extends REST_Controller
{
	function __construct()
    {
        parent::__construct();
    }
	
	public function is_login_get()
	{
		$result = new stdClass();
		$result->response = FALSE;
		$result->value = NULL;
		$result->data = array();
		
		$is_logged_in = $this->session->userdata('logged_in');
		
		if ($is_logged_in)
		{
			
			$this->load->library('system/lib_user');
			
			$user_id = $this->session->userdata('user_id');
			
			$sys_user_table = $this->db
				->where('id', $user_id)
				->get('sys_users');
			if ($sys_user_table->num_rows() > 0)
			{
				$sys_user = $sys_user_table->first_row();
				
				$sys_accesscontrols = $this->lib_user->user_get_accesscontrols($sys_user->id);
				
				$result->response = TRUE;
				$result->value = $this->session->session_id;
				$result->data = array(
					'user_id' 			=> $sys_user->id,
					'username' 			=> $sys_user->username,
					'name' 				=> $sys_user->name,
					'accesscontrols' 	=> $sys_accesscontrols
				);
			}
		}
		else
		{
			$result->value = "Session expired";
		}
		
		$this->result_json($result);
	}
	
	public function authentication_post()
	{
		$result = new stdClass();
		$result->response = FALSE;
		$result->value = NULL;
		$result->data = array();
		
		$username = $this->input->get_post('username', TRUE);
		$password = $this->input->get_post('password', TRUE);
		
		try
		{
			$this->load->library('system/lib_user');
			$user_profiles = $this->lib_user->user_authentication($username, $password);
			
			$this->session->set_userdata($user_profiles);
			
			$result->response = TRUE;
			$result->value = $this->session->session_id;
			$result->data = array(
				'user_id' 			=> $user_profiles['user_id'],
				'username' 			=> $user_profiles['username'],
				'name' 				=> $user_profiles['name'],
				'accesscontrols' 	=> $user_profiles['accesscontrols']
			);
		}
		catch (Exception $e)
		{
			$result->value = $e->getMessage();
		}
		
		$this->result_json($result);
	}
	
	public function logout_post()
	{
		$result = new stdClass();
		$result->response = FALSE;
		$result->value = NULL;
		$result->data = array();
		
		try
		{
			$this->session->sess_destroy();
			$result->response = TRUE;
		}
		catch (Exception $e)
		{
			$result->value = $e->getMessage();
		}
		
		$this->result_json($result);
	}
}