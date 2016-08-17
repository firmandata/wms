<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Get extention from file address
 */
if ( ! function_exists('url_host_change'))
{
	function url_host_change($url, $host_destination = '127.0.0.1')
	{
		$url_paths = parse_url($url);
		if (isset($url_paths['host']))
			$url = str_replace($url_paths['host'], $host_destination, $url);
		return $url;
	}
}

/* End of file MY_url_helper.php */
/* Location: ./application/helpers/MY_url_helper.php */