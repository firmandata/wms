<?php
class C_businesspartner extends DataMapper {
	
	var $has_many = array(
		'c_orderin'		=> array('cascade_delete' => FALSE),
		'c_orderout'	=> array('cascade_delete' => FALSE),
		'c_project'		=> array('cascade_delete' => FALSE)
	);
	
    var $validation = array(
		'code' => array(
            'label' => 'Code',
            'rules' => array('required', 'unique', 'trim', 'max_length' => 10)
        ),
        'name' => array(
            'label' => 'Name',
            'rules' => array('required', 'trim', 'max_length' => 255)
        )
    );
}