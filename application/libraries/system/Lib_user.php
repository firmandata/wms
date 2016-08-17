<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Lib_user extends Lib_general
{
	protected $CI;
	
	function __construct()
    {
		$this->CI =& get_instance();
	}
	
	/* ----------------- */
	/* -- USER REGION -- */
	/* ----------------- */
	
	public function user_add($data, $created_by = NULL)
	{
		$data->password = md5($data->password);
		$data->password_confirm = md5($data->password_confirm);
		$data->created_by = $created_by;
		
		$sys_user = new Sys_user();
		$this->set_model_fields_values($sys_user, $data);
		if (!$sys_user->save())
			throw new Exception($sys_user->error->string);
		
		return $sys_user->id;
	}
	
	public function user_update($sys_user_id, $data, $updated_by = NULL)
	{
		if (!empty($data->password))
		{
			$data->password = md5($data->password);
			$data->password_confirm = md5($data->password_confirm);
		}
		else
		{
			unset($data->password);
			unset($data->password_confirm);
		}
		$data->updated_by = $updated_by;
		
		$sys_user = new Sys_user($sys_user_id);
		$this->set_model_fields_values($sys_user, $data);
		if (!$sys_user->save())
			throw new Exception($sys_user->error->string);
		
		return $sys_user_id;
	}
	
	public function user_remove($sys_user_id, $removed_by = NULL)
	{
		$sys_user = new Sys_user($sys_user_id);
		
		// -- Remove UserGroup User --
		foreach ($sys_user->sys_usergroup_user->get() as $sys_usergroup_user)
		{
			$this->usergroup_user_remove($sys_usergroup_user->id, $removed_by);
		}
		
		// -- Remove User --
		if (!$sys_user->delete())
			throw new Exception($sys_user->error->string);
		
		return $sys_user_id;
	}
	
	public function user_request_reset_password($email)
	{
		$sys_user_table = $this->CI->db
			->where('email', $email)
			->get('sys_users');
		if ($sys_user_table->num_rows() > 0)
		{
			$sys_user = $sys_user_table->first_row();
			if (!$sys_user->is_active)
			{
				throw new Exception("User is inactive !");
			}
			
			$this->CI->load->helper('email');
			
			$new_password = substr(md5(date('Y-m-d H:i:s')), -7);
			$message = "Name : ".$sys_user->name."<br/>";
			$message .= "Username : ".$sys_user->username."<br/>";
			$message .= "New Password :".$new_password."<br/>";
			$message .= "<a href='".site_url('login/reset_password/'.$sys_user->id.'/'.$sys_user->password.'/'.$new_password)."'>Click here</a> to reset the password.";
			
			$email_sent = send_email($email, "Request Password", $message);
			if (!$email_sent)
				throw new Exception("Failed to send the email, please contact your admnistrator.");
			else
				return TRUE;
		}
		else
		{
			throw new Exception("Email not found.");
		}
	}
	
	public function user_reset_password($sys_user_id, $old_password, $new_password)
	{
		$sys_user_table = $this->CI->db
			->where('id', $sys_user_id)
			->where('password', $old_password)
			->get('sys_users');
		if ($sys_user_table->num_rows() > 0)
		{
			$sys_user = new Sys_user($sys_user_id);
			$sys_user->password = md5($new_password);
			$sys_user->updated_by = $sys_user_id;
			if (!$sys_user->save())
				throw new Exception($sys_user->error->string);
			else
				return TRUE;
		}
		else
			throw new Exception("Reset password failed, token not match.");
	}
	
	public function user_authentication($username, $password)
	{
		$user_profiles = array();
		
		$password = md5($password);
		
		$sys_user_table = $this->CI->db
			->where('username', $username)
			->where('password', $password)
			->get('sys_users');
		if ($sys_user_table->num_rows() > 0)
		{
			$sys_user = $sys_user_table->first_row();
			
			if (!$sys_user->is_active)
			{
				throw new Exception("User is inactive.");
			}
			
			if ($sys_user->username == $username && $sys_user->password == $password)
			{
				$sys_accesscontrols = $this->user_get_accesscontrols($sys_user->id);
				
				$this->CI->load->library('system/lib_menu');
				
				// -- User Profile --
				$user_profiles = array(
					'user_id' 			=> $sys_user->id,
					'username' 			=> $sys_user->username,
					'name' 				=> $sys_user->name,
					'logged_in' 		=> TRUE,
					'menus' 			=> $this->CI->lib_menu->menu_get_structure($sys_accesscontrols),
					'accesscontrols' 	=> $sys_accesscontrols
				);
			}
			else
			{
				throw new Exception("Login not match.");
			}
		}
		else
		{
			throw new Exception("Login not match.");
		}
		
		return $user_profiles;
	}
	
	public function user_get_accesscontrols($sys_user_id)
	{
		$results = array();
		
		// -- Get authorization of control and action --
		$this->CI->db
			->select("ctrl.name AS control, act.name AS action")
			->select("MAX(CASE WHEN acl.is_denied = 1 THEN 1 ELSE 0 END) is_denied", FALSE)
			->from('sys_accesscontrols acl')
			->join('sys_usergroups ug', "ug.id = acl.sys_usergroup_id")
			->join('sys_usergroup_users ugu', "ugu.sys_usergroup_id = ug.id")
			->join('sys_controls ctrl', "ctrl.id = acl.sys_control_id")
			->join('sys_actions act', "act.id = acl.sys_action_id")
			->where('ugu.sys_user_id', $sys_user_id)
			->group_by(
				array(
					'ctrl.name', 'act.name'
				)
			);
		$control_action_src = $this->CI->db->get();
		$control_actions = $control_action_src->result();
		foreach ($control_actions as $control_action)
		{
			$is_accepted = TRUE;
			if ($control_action->is_denied)
				$is_accepted = FALSE;
			$results[$control_action->control][$control_action->action] = $is_accepted;
		}
		
		return $results;
	}
	
	public function user_is_authorized($sys_user_id, $control, $action)
	{
		$result = FALSE;
		
		// -- Get is authorization of control and action --
		$this->CI->db
			->select("ugu.sys_user_id")
			->select("MAX(CASE WHEN acl.is_denied = 1 THEN 1 ELSE 0 END) is_denied", FALSE)
			->from('sys_accesscontrols acl')
			->join('sys_usergroups ug', "ug.id = acl.sys_usergroup_id")
			->join('sys_usergroup_users ugu', "ugu.sys_usergroup_id = ug.id")
			->join('sys_controls ctrl', "ctrl.id = acl.sys_control_id")
			->join('sys_actions act', "act.id = acl.sys_action_id")
			->where('ugu.sys_user_id', $sys_user_id)
			->where('ctrl.name', $control)
			->where('act.name', $action)
			->group_by(
				array(
					'ugu.sys_user_id'
				)
			);
		$control_action_src = $this->CI->db->get();
		if ($control_action_src->num_rows() > 0)
		{
			$control_action = $control_action_src->first_row();
			if (!$control_action->is_denied)
				$result = TRUE;
		}
		
		return $result;
	}
	
	/* ---------------------- */
	/* -- USERGROUP REGION -- */
	/* ---------------------- */
	
	public function usergroup_add($data, $created_by = NULL)
	{
		$data->created_by = $created_by;
		
		$sys_usergroup = new Sys_usergroup();
		$this->set_model_fields_values($sys_usergroup, $data);
		if (!$sys_usergroup->save())
			throw new Exception($sys_usergroup->error->string);
		
		return $sys_usergroup->id;
	}
	
	public function usergroup_update($sys_usergroup_id, $data, $updated_by = NULL)
	{
		$data->updated_by = $updated_by;
		
		$sys_usergroup = new Sys_usergroup($sys_usergroup_id);
		$this->set_model_fields_values($sys_usergroup, $data);
		if (!$sys_usergroup->save())
			throw new Exception($sys_usergroup->error->string);
		
		return $sys_usergroup_id;
	}
	
	public function usergroup_remove($sys_usergroup_id, $removed_by = NULL)
	{
		$sys_usergroup = new Sys_usergroup($sys_usergroup_id);
		
		// -- Remove UserGroup User --
		foreach ($sys_usergroup->sys_usergroup_user->get() as $sys_usergroup_user)
		{
			$this->usergroup_user_remove($sys_usergroup_user->id, $removed_by);
		}
		
		// -- Remove AccessControl --
		foreach ($sys_usergroup->sys_accesscontrol->get() as $sys_accesscontrol)
		{
			$this->accesscontrol_remove($sys_accesscontrol->id, $removed_by);
		}
		
		// -- Remove UserGroup --
		if (!$sys_usergroup->delete())
			throw new Exception($sys_usergroup->error->string);
		
		return $sys_usergroup_id;
	}
	
	/* --------------------------- */
	/* -- USERGROUP USER REGION -- */
	/* --------------------------- */
	
	public function usergroup_user_add($sys_usergroup_id, $sys_user_id, $created_by = NULL)
	{
		$sys_user = new Sys_user($sys_user_id);
		$sys_usergroup = new Sys_usergroup($sys_usergroup_id);
		
		$sys_usergroup_user = new Sys_usergroup_user();
		$sys_usergroup_user->created_by = $created_by;
		$sys_usergroup_user_saved = $sys_usergroup_user->save(
			array(
				'sys_user'		=> $sys_user, 
				'sys_usergroup'	=> $sys_usergroup
			)
		);
		if (!$sys_usergroup_user_saved)
			throw new Exception($sys_usergroup_user->error->string);
		
		return $sys_usergroup_user->id;
	}
	
	public function usergroup_user_remove($sys_usergroup_user_id, $removed_by = NULL)
	{
		$sys_usergroup_user = new Sys_usergroup_user($sys_usergroup_user_id);
		
		// -- Remove UserGroup User --
		if (!$sys_usergroup_user->delete())
			throw new Exception($sys_usergroup_user->error->string);
		
		return $sys_usergroup_user_id;
	}
	
	/* -------------------------- */
	/* -- ACCESSCONTROL REGION -- */
	/* -------------------------- */
	
	public function accesscontrol_add($sys_usergroup_id, $sys_control_id, $sys_action_id, $is_denied = FALSE, $created_by = NULL)
	{
		$sys_usergroup = new Sys_usergroup($sys_usergroup_id);
		$sys_control = new Sys_control($sys_control_id);
		$sys_action = new Sys_action($sys_action_id);
		
		$data = new stdClass();
		$data->created_by = $created_by;
		$data->is_denied = $is_denied;
		
		$sys_accesscontrol = new Sys_accesscontrol();
		$this->set_model_fields_values($sys_accesscontrol, $data);
		$sys_accesscontrol_saved = $sys_accesscontrol->save(
			array(
				'sys_usergroup'	=> $sys_usergroup,
				'sys_control'	=> $sys_control,
				'sys_action'	=> $sys_action
			)
		);
		if (!$sys_accesscontrol_saved)
			throw new Exception($sys_accesscontrol->error->string);
		
		return $sys_accesscontrol->id;
	}
	
	public function accesscontrol_update($sys_accesscontrol_id, $sys_usergroup_id, $sys_control_id, $sys_action_id, $is_denied = FALSE, $updated_by = NULL)
	{
		$sys_usergroup = new Sys_usergroup($sys_usergroup_id);
		$sys_control = new Sys_control($sys_control_id);
		$sys_action = new Sys_action($sys_action_id);
		
		$data = new stdClass();
		$data->updated_by = $updated_by;
		$data->is_denied = $is_denied;
		
		$sys_accesscontrol = new Sys_accesscontrol($sys_accesscontrol_id);
		$this->set_model_fields_values($sys_accesscontrol, $data);
		$sys_accesscontrol_saved = $sys_accesscontrol->save(
			array(
				'sys_usergroup'	=> $sys_usergroup,
				'sys_control'	=> $sys_control,
				'sys_action'	=> $sys_action
			)
		);
		if (!$sys_accesscontrol_saved)
			throw new Exception($sys_accesscontrol->error->string);
		
		return $sys_accesscontrol_id;
	}
	
	public function accesscontrol_remove($sys_accesscontrol_id, $removed_by = NULL)
	{
		$sys_accesscontrol = new Sys_accesscontrol($sys_accesscontrol_id);
		
		// -- Remove AccessControl --
		if (!$sys_accesscontrol->delete())
			throw new Exception($sys_accesscontrol->error->string);
		
		return $sys_accesscontrol_id;
	}
	
	public function accesscontrol_assign($sys_usergroup_id, $sys_control_id, $sys_action_id, $is_denied = FALSE, $assigned_by = NULL)
	{
		$sys_accesscontrol_id = NULL;
		
		$this->CI->db
			->where('sys_usergroup_id', $sys_usergroup_id)
			->where('sys_control_id', $sys_control_id)
			->where('sys_action_id', $sys_action_id);
		$table = $this->CI->db->get('sys_accesscontrols');
		if ($table->num_rows() > 0)
		{
			$accesscontrol_record = $table->first_row();
			$sys_accesscontrol_id = $this->accesscontrol_update($accesscontrol_record->id, $sys_usergroup_id, $sys_control_id, $sys_action_id, $is_denied, $assigned_by);
		}
		else
		{
			$sys_accesscontrol_id = $this->accesscontrol_add($sys_usergroup_id, $sys_control_id, $sys_action_id, $is_denied, $assigned_by);
		}
		
		return $sys_accesscontrol_id;
	}
}