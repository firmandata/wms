<?php
class C_orderin extends DataMapper {
	
	var $has_one = array(
		'c_businesspartner'	=> array('cascade_delete' => FALSE),
		'c_project'			=> array('cascade_delete' => FALSE)
	);
	
    var $has_many = array(
		'c_orderindetail'	=> array('cascade_delete' => FALSE)
	);
	
    var $validation = array(
		'code' => array(
            'label' => 'Code',
            'rules' => array('required', 'unique', 'trim', 'max_length' => 30)
        ),
		'c_businesspartner' => array(
            'label' => 'Business Partner',
            'rules' => array('required')
        ),
        'orderin_date' => array(
            'label' => 'Date',
            'rules' => array('required', 'valid_date')
        ),
		'c_project' => array(
            'label' => 'Project',
            'rules' => array('required')
        )
    );
}