<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Exceptions extends CI_Exceptions
{
	public function __construct()
	{
		parent::__construct();
	}

	public function show_error($heading, $message, $template = 'error_general', $status_code = 500)
	{
		$CI = &get_instance();
		
		$templates_path = config_item('error_views_path');
		if (empty($templates_path))
		{
			$templates_path = VIEWPATH.'errors'.DIRECTORY_SEPARATOR;
		}

		if ($CI->input->is_ajax_request())
		{
			set_status_header($status_code);
			$message = (is_array($message) ? implode(", ", $message) : $message);
			$template = 'ajax'.DIRECTORY_SEPARATOR.$template;
		}
		elseif (is_cli())
		{
			$message = "\t".(is_array($message) ? implode("\n\t", $message) : $message);
			$template = 'cli'.DIRECTORY_SEPARATOR.$template;
		}
		else
		{
			set_status_header($status_code);
			$message = '<p>'.(is_array($message) ? implode('</p><p>', $message) : $message).'</p>';
			$template = 'html'.DIRECTORY_SEPARATOR.$template;
		}

		if (ob_get_level() > $this->ob_level + 1)
		{
			ob_end_flush();
		}
		ob_start();
		include($templates_path.$template.'.php');
		$buffer = ob_get_contents();
		ob_end_clean();
		return $buffer;
	}
	
	public function show_exception(Exception $exception)
	{
		$CI = &get_instance();
		
		$templates_path = config_item('error_views_path');
		if (empty($templates_path))
		{
			$templates_path = VIEWPATH.'errors'.DIRECTORY_SEPARATOR;
		}

		$message = $exception->getMessage();
		if (empty($message))
		{
			$message = '(null)';
		}

		if (is_cli())
		{
			$templates_path .= 'cli'.DIRECTORY_SEPARATOR;
		}
		else
		{
			set_status_header(500);
			if ($CI->input->is_ajax_request())
				$templates_path .= 'ajax'.DIRECTORY_SEPARATOR;
			else
				$templates_path .= 'html'.DIRECTORY_SEPARATOR;
		}

		if (ob_get_level() > $this->ob_level + 1)
		{
			ob_end_flush();
		}

		ob_start();
		include($templates_path.'error_exception.php');
		$buffer = ob_get_contents();
		ob_end_clean();
		echo $buffer;
	}
	
	public function show_php_error_override($severity, $message, $filepath, $line)
	{
		$CI = &get_instance();
		
		$templates_path = config_item('error_views_path');
		if (empty($templates_path))
		{
			$templates_path = VIEWPATH.'errors'.DIRECTORY_SEPARATOR;
		}

		$severity = isset($this->levels[$severity]) ? $this->levels[$severity] : $severity;

		// For safety reasons we don't show the full file path in non-CLI requests
		if ( ! is_cli())
		{
			$filepath = str_replace('\\', '/', $filepath);
			if (FALSE !== strpos($filepath, '/'))
			{
				$x = explode('/', $filepath);
				$filepath = $x[count($x)-2].'/'.end($x);
			}

			if ($CI->input->is_ajax_request())
				$template = 'ajax'.DIRECTORY_SEPARATOR.'error_php';
			else
				$template = 'html'.DIRECTORY_SEPARATOR.'error_php';
		}
		else
		{
			$template = 'cli'.DIRECTORY_SEPARATOR.'error_php';
		}

		if (ob_get_level() > $this->ob_level + 1)
		{
			ob_end_flush();
		}
		ob_start();
		include($templates_path.$template.'.php');
		$buffer = ob_get_contents();
		ob_end_clean();
		echo $buffer;
	}
	
	public function show_php_error($severity, $message, $filepath, $line)
	{
		$CI = &get_instance();
		
		$is_trans_opened = FALSE;
		if (isset($CI->db) && method_exists($CI->db, 'is_trans_opened'))
		{
			$is_trans_opened = $CI->db->is_trans_opened();
		}
		
		set_status_header(500);
		
		if ($is_trans_opened == TRUE)
		{
			$error_message = $message.' in '.$filepath.' on line '.$line;
			throw new Exception($error_message);
		}
		else
		{
			$this->show_php_error_override($severity, $message, $filepath, $line);
			exit;
		}
	}
}