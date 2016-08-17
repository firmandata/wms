<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
 
class Lib_menu extends Lib_general
{
	protected $CI;
	
	function __construct()
    {
		$this->CI =& get_instance();
	}
	
	/* -------------------- */
	/* -- CONTROL REGION -- */
	/* -------------------- */
	
	public function control_add($data, $created_by = NULL)
	{
		$data->created_by = $created_by;
		
		$sys_control = new Sys_control();
		$this->set_model_fields_values($sys_control, $data);
		if (!$sys_control->save())
			throw new Exception($sys_control->error->string);
		
		return $sys_control->id;
	}
	
	public function control_update($sys_control_id, $data, $updated_by = NULL)
	{
		$data->updated_by = $updated_by;
		
		$sys_control = new Sys_control($sys_control_id);
		$this->set_model_fields_values($sys_control, $data);
		if (!$sys_control->save())
			throw new Exception($sys_control->error->string);
		
		return $sys_control_id;
	}
	
	public function control_remove($sys_control_id, $removed_by = NULL)
	{
		$sys_control = new Sys_control($sys_control_id);
		
		// -- Remove AccessControl --
		$this->CI->load->library('system/lib_user');
		foreach ($sys_control->sys_accesscontrol->get() as $sys_accesscontrol)
		{
			$this->CI->lib_user->accesscontrol_remove($sys_accesscontrol->id, $removed_by);
		}
		
		// -- Remove Menu --
		foreach ($sys_control->sys_menu->get() as $sys_menu)
		{
			$sys_menu_data = new stdClass();
			$this->menu_update(
				  $sys_menu->id
				, $sys_menu_data
				, $sys_menu->parent->get()->id
				, NULL
				, $sys_menu->sys_action->get()->id
				, $removed_by
			);
		}
		
		// -- Remove Control --
		if (!$sys_control->delete())
			throw new Exception($sys_control->error->string);
		
		return $sys_control_id;
	}
	
	/* ------------------- */
	/* -- ACTION REGION -- */
	/* ------------------- */
	
	public function action_add($data, $created_by = NULL)
	{
		$data->created_by = $created_by;
		
		$sys_action = new Sys_action();
		$this->set_model_fields_values($sys_action, $data);
		if (!$sys_action->save())
			throw new Exception($sys_action->error->string);
		
		return $sys_action->id;
	}
	
	public function action_update($sys_action_id, $data, $updated_by = NULL)
	{
		$data->updated_by = $updated_by;
		
		$sys_action = new Sys_action($sys_action_id);
		$this->set_model_fields_values($sys_action, $data);
		if (!$sys_action->save())
			throw new Exception($sys_action->error->string);
		
		return $sys_action_id;
	}
	
	public function action_remove($sys_action_id, $removed_by = NULL)
	{
		$sys_action = new Sys_action($sys_action_id);
		
		// -- Remove AccessControl --
		$this->CI->load->library('system/lib_user');
		foreach ($sys_action->sys_accesscontrol->get() as $sys_accesscontrol)
		{
			$this->CI->lib_user->accesscontrol_remove($sys_accesscontrol->id, $removed_by);
		}
		
		// -- Remove Menu --
		foreach ($sys_action->sys_menu->get() as $sys_menu)
		{
			$sys_menu_data = new stdClass();
			$this->menu_update(
				  $sys_menu->id
				, $sys_menu_data
				, $sys_menu->parent->get()->id
				, $sys_menu->sys_control->get()->id
				, NULL
				, $removed_by
			);
		}
		
		// -- Remove Action --
		if (!$sys_action->delete())
			throw new Exception($sys_action->error->string);
		
		return $sys_action_id;
	}
	
	/* ------------------- */
	/* -- MENU REGION -- */
	/* ------------------- */
	
	public function menu_add($data, $parent_id = NULL, $sys_control_id = NULL, $sys_action_id = NULL, $created_by = NULL)
	{
		$data->created_by = $created_by;
		
		$parent = new Sys_menu($parent_id);
		$sys_control = new Sys_control($sys_control_id);
		$sys_action = new Sys_action($sys_action_id);
		
		$sys_menu = new Sys_menu();
		$this->set_model_fields_values($sys_menu, $data);
		$sys_menu_saved = $sys_menu->save(
			array(
				'parent'		=> $parent,
				'sys_control'	=> $sys_control,
				'sys_action'	=> $sys_action,
			)
		);
		if (!$sys_menu_saved)
			throw new Exception($sys_menu->error->string);
		
		return $sys_menu->id;
	}
	
	public function menu_update($sys_menu_id, $data, $parent_id = NULL, $sys_control_id = NULL, $sys_action_id = NULL, $updated_by = NULL)
	{
		$data->updated_by = $updated_by;
		
		if ($sys_menu_id == $parent_id)
			throw new Exception("Parent must be different with it self.");
		
		$parent = new Sys_menu($parent_id);
		$sys_control = new Sys_control($sys_control_id);
		$sys_action = new Sys_action($sys_action_id);
		
		$sys_menu = new Sys_menu($sys_menu_id);
		$this->set_model_fields_values($sys_menu, $data);
		$sys_menu_saved = $sys_menu->save(
			array(
				'parent'		=> $parent,
				'sys_control'	=> $sys_control,
				'sys_action'	=> $sys_action,
			)
		);
		if (!$sys_menu_saved)
			throw new Exception($sys_menu->error->string);
		
		return $sys_menu_id;
	}
	
	public function menu_remove($sys_menu_id, $removed_by = NULL)
	{
		$sys_menu = new Sys_menu($sys_menu_id);
		
		// -- Remove MenuChilds --
		foreach ($sys_menu->sys_menu->get() as $sys_menu_child)
		{
			$this->menu_remove($sys_menu_child->id, $removed_by);
		}
		
		// -- Remove Menu --
		if (!$sys_menu->delete())
			throw new Exception($sys_menu->error->string);
		
		return $sys_menu_id;
	}
	
	public function menu_get_structure($accesscontrols = NULL, $parent_id = NULL)
	{
		$structures = array();
		
		$this->CI->db
			->select("mnu.id, mnu.name AS name, ctrl.name AS control, act.name AS action, mnu.url, mnu.css")
			->from('sys_menus mnu')
			->join('sys_controls ctrl', "ctrl.id = mnu.sys_control_id", 'left')
			->join('sys_actions act', "act.id = mnu.sys_action_id", 'left')
			->order_by('mnu.sequence', 'asc');
		if ($parent_id != NULL)
			$this->CI->db->where('mnu.parent_id', $parent_id);
		else
			$this->CI->db->where('mnu.parent_id IS NULL', NULL, FALSE);
		$sys_menu_table = $this->CI->db->get();
		if ($sys_menu_table->num_rows() > 0)
		{
			$sys_menus = $sys_menu_table->result();
			foreach($sys_menus as $sys_menu)
			{
				$sys_menu->childs = $this->menu_get_structure($accesscontrols, $sys_menu->id);
				
				$is_authorized = FALSE;
				if ($sys_menu->control != NULL && $sys_menu->action != NULL)
				{
					if ($accesscontrols != NULL)
					{
						if (isset($accesscontrols[$sys_menu->control][$sys_menu->action]) && $accesscontrols[$sys_menu->control][$sys_menu->action] == TRUE)
							$is_authorized = TRUE;
					}
					else
						$is_authorized = TRUE;
				}
				elseif ($sys_menu->control == NULL && $sys_menu->action == NULL)
				{
					if (count($sys_menu->childs) > 0)
						$is_authorized = TRUE;
				}
				
				if ($is_authorized == TRUE)
					$structures[] = $sys_menu;
			}
		}
			
		return $structures;
	}
	
	public function menu_get_adjacency()
	{
		$leafs = array();
		$this->CI->db
			->select('mnu.id, mnu.sequence')
			->from('sys_menus mnu')
			->join('sys_menus mnu_chi', "mnu_chi.parent_id = mnu.id", 'left')
			->where("mnu_chi.id IS NULL", NULL, FALSE)
			->order_by('mnu.sequence', 'asc');
		$leaf_table = $this->CI->db->get();
		$leaf_table_result = $leaf_table->result();
		foreach ($leaf_table_result as $_leaf_table_result)
		{
			$leafs[$_leaf_table_result->id] = $_leaf_table_result->id;
		}
		
		return $this->_menu_get_adjacency($leafs, NULL, 0);
	}
	
	protected function _menu_get_adjacency($leafs, $parent_id = NULL, $level = 0)
	{
		$adjacencies = array();
		
		$this->CI->db
			->select("mnu.id, mnu.parent_id, mnu.name, mnu.sequence, mnu.url, mnu.css")
			->select("mnu.sys_control_id, ctrl.name control_name")
			->select("mnu.sys_action_id, act.name action_name")
			->from('sys_menus mnu')
			->join('sys_controls ctrl', "ctrl.id = mnu.sys_control_id", 'left')
			->join('sys_actions act', "act.id = mnu.sys_action_id", 'left')
			->order_by('sequence', 'asc');
		if (!empty($parent_id))
			$this->CI->db->where('mnu.parent_id', $parent_id);
		else
			$this->CI->db->where('mnu.parent_id IS NULL', NULL, FALSE);
		$sys_menu_table = $this->CI->db->get();
		$sys_menus = $sys_menu_table->result();
		foreach ($sys_menus as $menu)
		{
			$adjacencies[] = $menu;
			
			$menu->is_leaf = FALSE;
			if (isset($leafs[$menu->id]))
				$menu->is_leaf = TRUE;
			$menu->is_expanded = TRUE;
			$menu->level = $level;
			
			$chids = $this->_menu_get_adjacency($leafs, $menu->id, $level + 1);
			foreach ($chids as $_chid)
			{
				$adjacencies[] = $_chid;
			}
		}
		
		return $adjacencies;
	}
}