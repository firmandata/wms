<?php
class M_inventory_assembly extends DataMapper {
	
    var $has_many = array(
		'm_inventory_assemblysource'	=> array('cascade_delete' => FALSE),
		'm_inventory_assemblytarget'	=> array('cascade_delete' => FALSE)
	);
	
    var $validation = array(
		'code' => array(
            'label' => 'Code',
            'rules' => array('required', 'unique', 'trim', 'max_length' => 30)
        ),
        'assembly_date' => array(
            'label' => 'Date',
            'rules' => array('required', 'valid_date')
        )
    );
}