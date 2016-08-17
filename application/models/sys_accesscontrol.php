<?php
class Sys_accesscontrol extends DataMapper {
	
    var $has_one = array(
		'sys_usergroup'	=> array('cascade_delete' => FALSE),
		'sys_control'	=> array('cascade_delete' => FALSE),
		'sys_action'	=> array('cascade_delete' => FALSE)
	);
	
    var $validation = array(
		'sys_usergroup' => array(
			'label' => 'User Group',
			'rules' => array('required')
		),
		'sys_control' => array(
			'label' => 'Control',
			'rules' => array('required')
		),
		'sys_action' => array(
			'label' => 'Action',
			'rules' => array('required')
		),
        'is_denied' => array(
            'label' => 'Denied',
            'rules' => array('boolean')
        )
    );
}