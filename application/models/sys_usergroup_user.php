<?php
class Sys_usergroup_user extends DataMapper {
	
    var $has_one = array(
		'sys_usergroup'	=> array('cascade_delete' => FALSE), 
		'sys_user'		=> array('cascade_delete' => FALSE)
	);
	
    var $validation = array(
        'sys_usergroup' => array(
            'label' => 'User Group',
            'rules' => array('required')
        ),
		'sys_user' => array(
            'label' => 'User',
            'rules' => array('required')
        )
    );
}