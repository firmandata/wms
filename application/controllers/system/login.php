<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Login extends CI_Controller
{
	protected $_login_page;
	protected $_forget_login_page;
	protected $_callback_login_page;
	
	public function __construct()
	{
		parent::__construct();
		
		$this->_login_page = 'welcome/index/login';
		$this->_forget_login_page = 'welcome/index/forget_password';
		
		$this->_callback_login_page = $this->input->get_post('return', TRUE);
		if ($this->_callback_login_page == FALSE)
			$this->_callback_login_page = site_url('system/home/index');
	}
	
	public function index()
	{
		$this->session->set_flashdata('message', "Session Expired");
		redirect($this->_login_page.'?return='.$this->_callback_login_page);
	}
	
	public function authentication()
	{
		$this->load->library('form_validation');
		
		$field_settings = array(
			array('field'=>'username', 'label'=>'Username', 'rules'=>'trim|required'),
			array('field'=>'password', 'label'=>'Password', 'rules'=>'trim|required')
		);
		$this->form_validation->set_rules($field_settings);
		
		if ($this->form_validation->run() == FALSE)
		{
			$this->session->set_flashdata('message', validation_errors());
			redirect($this->_login_page.'?return='.$this->_callback_login_page);
		}
		else
		{
			$is_success = FALSE;
			
			$username = $this->input->post('username', TRUE);
			$password = $this->input->post('password', TRUE);
			
			try
			{
				$this->load->library('system/lib_user');
				$user_profiles = $this->lib_user->user_authentication($username, $password);
				
				$this->session->set_userdata($user_profiles);
				
				redirect($this->_callback_login_page);
			}
			catch (Exception $e)
			{
				$this->session->set_flashdata('message', $e->getMessage());
				redirect($this->_login_page.'?return='.$this->_callback_login_page);
			}
		}
	}
	
	public function logout()
	{
		$this->session->sess_destroy();
		redirect($this->_login_page);
	}
	
	private function set_language($language)
	{
		$this->session->set_userdata('language', $language);
	}
	
	public function switch_language()
	{
		$language = $this->input->get_post('language', TRUE);
		$this->set_language($language);
		
		$return = $this->input->get_post('return', TRUE);
		redirect($return);
	}
	
	public function forget_password()
	{
		$this->load->library('form_validation');
		
		$field_settings = array(
			array('field'=>'email', 'label'=>'Email', 'rules'=>'trim|required|valid_email')
		);
		
		$this->form_validation->set_rules($field_settings);
		
		if ($this->form_validation->run() == FALSE)
		{
			$this->session->set_flashdata('message', validation_errors());
			redirect($this->_forget_login_page);
		}
		else
		{
			$email = $this->input->post('email', TRUE);
			
			try
			{
				$this->load->library('system/lib_user');
				$this->lib_user->user_request_reset_password($email);
				
				$this->session->set_flashdata('message', "Please check your mail, reset password request was sent via email.");
				redirect($this->_forget_login_page);
			}
			catch (Exception $e)
			{
				$this->session->set_flashdata('message', $e->getMessage());
				redirect($this->_forget_login_page);
			}
		}
	}
	
	public function reset_password($user_id, $old_password, $new_password)
	{
		try
		{
			$this->load->library('system/lib_user');
			$this->lib_user->user_reset_password($user_id, $old_password, $new_password);
			
			$this->session->set_flashdata('message', "Reset password successfully.");
			redirect($this->_login_page);
		}
		catch (Exception $e)
		{
			$this->session->set_flashdata('message', $e->getMessage());
			redirect($this->_forget_login_page);
		}
	}
	
	public function request_token()
	{
	}
	
	public function authorize()
	{
	}
	
	public function access_token()
	{
	}
}