<?php
class M_inventory_sample extends DataMapper {
	
	var $has_many = array(
		'm_inventory_sampledetail'	=> array('cascade_delete' => FALSE)
	);
	
    var $validation = array(
		'code' => array(
            'label' => 'Code',
            'rules' => array('required', 'unique', 'trim', 'max_length' => 30)
        ),
        'sampling_date' => array(
            'label' => 'Date',
            'rules' => array('required', 'valid_date')
        )
    );
}