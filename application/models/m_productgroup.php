<?php
class M_productgroup extends DataMapper {
	
	var $has_many = array(
		'm_grid'	=> array('cascade_delete' => FALSE),
		'm_product'	=> array('cascade_delete' => FALSE)
	);
	
    var $validation = array(
		'code' => array(
            'label' => 'Code',
            'rules' => array('required', 'unique', 'trim', 'max_length' => 15)
        ),
        'name' => array(
            'label' => 'Name',
            'rules' => array('required', 'trim', 'max_length' => 150)
        )
    );
}