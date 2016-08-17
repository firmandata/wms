<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Get extention from file address
 */
if ( ! function_exists('get_extention'))
{
	function get_extention($file_name)
	{
		$maps_name = explode('.', $file_name);
		return end($maps_name);
	}
}

/**
 * Get file name without extention
 */
if ( ! function_exists('get_file_without_extention'))
{
	function get_file_without_extention($file_name)
	{
		$maps_name = explode('.', $file_name, -1);
		return implode('.', $maps_name);
	}
}

/**
 * Get formated file size
 */
if ( ! function_exists('formated_filesize'))
{
	function formated_filesize($file_name)
	{
		$file_size = filesize($file_name);
		
		if ($file_size > 1048576)
			return number_format($file_size / 1048576 , 2, ',', '.').' MB';
		elseif ($file_size > 1024)
			return number_format($file_size / 1024 , 2, ',', '.').' KB';
		else
			return number_format($file_size / 1024 , 2, ',', '.').' Byte';
	}
}

/**
 * Get file name from file address
 */
if ( ! function_exists('get_file_name'))
{
	function get_file_name($file_address, $separator = '/')
	{
		$file_parts = explode($separator, $file_address);
		return end($file_parts);
	}
}

/**
 * Get file path from file address
 */
if ( ! function_exists('get_file_path'))
{
	function get_file_path($file_address, $separator = '/')
	{
		$file_parts = explode($separator, $file_address, -1);
		$file_path = implode($separator, $file_parts);
		return $file_path;
	}
}

/**
 * Remove files in directory
 */
if ( ! function_exists('delete_files_in_dir'))
{
	function delete_files_in_dir($path)
	{
		// Trim the trailing slash
		$path = rtrim($path, DIRECTORY_SEPARATOR);

		if ( ! $current_dir = @opendir($path))
		{
			return FALSE;
		}

		while (FALSE !== ($filename = @readdir($current_dir)))
		{
			if ($filename != "." and $filename != "..")
			{
				if (!is_dir($path.DIRECTORY_SEPARATOR.$filename))
				{
					unlink($path.DIRECTORY_SEPARATOR.$filename);
				}
			}
		}
		@closedir($current_dir);

		return TRUE;
	}
}

/**
 * Get clear filename
 */
if ( ! function_exists('get_clear_filename'))
{
	function get_clear_filename($file_name)
	{
		return str_replace(array('\\','/',':','*','?','"','<','>','|'), '_', $file_name);
	}
}
/* End of file MY_file_helper.php */
/* Location: ./application/helpers/MY_file_helper.php */