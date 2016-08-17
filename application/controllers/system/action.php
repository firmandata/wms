<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Action extends MY_Controller 
{
	public function __construct()
	{
		parent::__construct(TRUE, 'backend');
	}
	
	public function insert()
	{
		if (!is_authorized('system/action', 'insert'))
			access_denied();
		
		$this->load->library('system/lib_menu');
		
		$user_id = $this->session->userdata('user_id');
		
		$data = new stdClass();
		$data->name = $this->input->post('name');
		
		parent::_execute('lib_menu', 'action_add', 
			array($data, $user_id), 
			array(
				array('field' => 'name', 'label' => 'Name', 'rules' => 'required')
			)
		);
	}
	
	public function update($id)
	{
		if (!is_authorized('system/action', 'update'))
			access_denied();
		
		$this->load->library('system/lib_menu');
		
		$user_id = $this->session->userdata('user_id');
		
		$data = new stdClass();
		$data->name = $this->input->post('name');
		
		parent::_execute('lib_menu', 'action_update', 
			array($id, $data, $user_id), 
			array(
				array('field' => 'name', 'label' => 'Name', 'rules' => 'required')
			)
		);
	}
	
	public function delete($id)
	{
		if (!is_authorized('system/action', 'delete'))
			access_denied();
		
		$this->load->library('system/lib_menu');
		
		$user_id = $this->session->userdata('user_id');
		
		parent::_execute('lib_menu', 'action_remove', array($id, $user_id));
	}
}