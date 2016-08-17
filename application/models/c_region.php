<?php
class C_region extends DataMapper {
	
	var $has_many = array(
		'c_businesspartner'	=> array('cascade_delete' => FALSE),
		'c_location'		=> array('cascade_delete' => FALSE),
		'a_asset'			=> array('cascade_delete' => FALSE)
	);
	
    var $validation = array(
		'code' => array(
            'label' => 'Code',
            'rules' => array('required', 'unique', 'trim', 'max_length' => 15)
        ),
        'name' => array(
            'label' => 'Name',
            'rules' => array('required', 'trim', 'max_length' => 255)
        )
    );
}