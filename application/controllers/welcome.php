<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Welcome extends MY_Controller 
{
	public function __construct()
	{
		parent::__construct(FALSE, 'frontend');
	}
	
	public function index()
	{
		$login_data = array(
			'message'		=> $this->session->flashdata('message')
		);
		
		$contents = array(
			'login' 	=> $this->load->view('system/login/login_full', $login_data, TRUE),
			'content'	=> ''
		);
		$this->_load_layout($contents);
	}
	
	public function forget_password()
	{
		$forget_password_data = array(
			'message'	=> $this->session->flashdata('message')
		);
		
		$contents = array(
			'login' 	=> $this->load->view('system/login/forget_password', $forget_password_data, TRUE),
			'content'	=> ''
		);
		$this->_load_layout($contents);
	}
}