<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
 
class MY_Loader extends CI_Loader {
	public function database($params = '', $return = FALSE, $query_builder = NULL)
	{
		// Grab the super object
		$CI =& get_instance();

		// Do we even need to load the database class?
		if ($return === FALSE && $query_builder === NULL && isset($CI->db) && is_object($CI->db) && ! empty($CI->db->conn_id))
		{
			return FALSE;
		}
		
		// Check if custom DB file exists, else include core one
		if (file_exists(APPPATH.'core/'.config_item('subclass_prefix').'DB.php'))
		{
			require_once(APPPATH.'core/'.config_item('subclass_prefix').'DB.php');
		}
		else
		{
			require_once(BASEPATH.'database/DB.php');
		}

		if ($return === TRUE)
		{
			return DB($params, $query_builder);
		}

		// Initialize the db variable. Needed to prevent
		// reference errors with some configurations
		$CI->db = '';

		// Load the DB class
		$CI->db =& DB($params, $query_builder);
		return $this;
	}
}
 
/* End of file MY_Loader.php */
/* Location: ./application/core/MY_Loader.php */