<?php
class Sys_control extends DataMapper {
	
    var $has_many = array(
		'sys_menu'			=> array('cascade_delete' => FALSE), 
		'sys_accesscontrol'	=> array('cascade_delete' => FALSE)
	);
	
    var $validation = array(
        'name' => array(
            'label' => 'Name',
            'rules' => array('required', 'trim', 'unique', 'max_length' => 255)
        )
    );
}