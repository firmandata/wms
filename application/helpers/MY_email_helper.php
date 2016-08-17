<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('send_email'))
{
	function send_email($recipient, $subject = 'Test email', $message = 'Hello World')
	{
		$CI =& get_instance();
		
		$CI->load->library('email');
		
		$mail_config = array();
		$mail_config['protocol'] = 'smtp';
		$mail_config['smtp_host'] = $CI->config->item('mail_smtp_host');
		$mail_config['smtp_port'] = $CI->config->item('mail_smtp_port');
		$mail_config['smtp_user'] = $CI->config->item('mail_smtp_user');
		$mail_config['smtp_pass'] = $CI->config->item('mail_smtp_pass');
		$mail_config['mailtype'] = 'html';
		$CI->email->initialize($mail_config);
		$CI->email->set_newline("\r\n");
		
		$CI->email->from($CI->config->item('mail_smtp_user'), $CI->config->item('mail_smtp_label_name'));
		$CI->email->to($recipient);
		
		$CI->email->subject($subject);
		
		$CI->email->message($message);

		return $CI->email->send();
	}
}