<?php
class Sys_menu extends DataMapper {
	
    var $has_one = array(
		'parent'		=> array(
			'class'				=> 'sys_menu',
			'other_field' 		=> 'sys_menu',
			'cascade_delete' 	=> FALSE
		),
		'sys_control'	=> array('cascade_delete' => FALSE),
		'sys_action'	=> array('cascade_delete' => FALSE)
	);
	var $has_many = array(
		'sys_menu'		=> array(
			'class'				=> 'sys_menu',
			'other_field' 		=> 'parent',
			'cascade_delete' 	=> FALSE
		)
	);
	
    var $validation = array(
        'name' => array(
            'label' => 'Name',
            'rules' => array('required', 'trim', 'max_length' => 255)
        ),
		'sequence' => array(
            'label' => 'Sequence',
            'rules' => array('required', 'min_size' => 0)
        ),
		'suffix_url' => array(
            'label' => 'Suffix URL',
            'rules' => array('trim', 'max_length' => 255)
        ),
		'image_url' => array(
            'label' => 'Image URL',
            'rules' => array('trim', 'max_length' => 255)
        )
    );
}