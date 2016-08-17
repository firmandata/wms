<?php
class Sys_usergroup extends DataMapper {
	
    var $has_many = array(
		'sys_usergroup_user'			=> array('cascade_delete' => FALSE),
		'sys_accesscontrol'				=> array('cascade_delete' => FALSE),
		'cus_c_project_sys_usergroup'	=> array('cascade_delete' => FALSE)
	);
	
    var $validation = array(
        'name' => array(
            'label' => 'Name',
            'rules' => array('required', 'trim', 'unique', 'max_length' => 255)
        )
    );
}