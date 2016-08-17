<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Convert object to array
 */
if ( ! function_exists('get_extention'))
{
	function object_to_array($object)
	{
		if (is_object($object))
		{
			$object = get_object_vars($object);
		}

		if (is_array($object))
		{
			return array_map(__FUNCTION__, $object);
		}
		else
		{
			return $object;
		}
	}
}
/* End of file MY_array_helper.php */
/* Location: ./application/helpers/MY_array_helper.php */