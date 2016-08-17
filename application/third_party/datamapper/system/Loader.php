<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Data Mapper ORM bootstrap
 *
 * Dynamic CI Loader class extension
 *
 * @license 	MIT License
 * @package		DataMapper ORM
 * @category	DataMapper ORM
 * @author  	Harro "WanWizard" Verton
 * @link		http://datamapper.wanwizard.eu/
 * @version 	2.0.0
 */

$dmclass = <<<CODE
class DM_Loader extends $name
{
	// --------------------------------------------------------------------

	/**
	 * Database Loader
	 *
	 * @param	string	the DB credentials
	 * @param	bool	whether to return the DB object
	 * @param	bool	whether to enable active record (this allows us to override the config setting)
	 * @return	object
	 */
	public function database(\$params = '', \$return = FALSE, \$query_builder = NULL)
	{
		// Grab the super object
		\$CI =& get_instance();
		
		// Do we even need to load the database class?
		if (\$return === FALSE && \$query_builder === NULL && isset(\$CI->db) && is_object(\$CI->db) && ! empty(\$CI->db->conn_id))
		{
			return FALSE;
		}
		
		require_once(DATAMAPPERPATH.'third_party/datamapper/system/DB.php');

		if (\$return === TRUE)
		{
			return DB(\$params, \$query_builder);
		}

		// Initialize the db variable.  Needed to prevent
		// reference errors with some configurations
		\$CI->db = '';

		// Load the DB class
		\$CI->db =& DB(\$params, \$query_builder);
		return \$this;
	}
}
CODE;

// dynamically add our class extension
eval($dmclass);
unset($dmclass);

// and update the name of the class to instantiate
$name = 'DM_Loader';

/* End of file Loader.php */
/* Location: ./application/third_party/datamapper/system/Loader.php */
