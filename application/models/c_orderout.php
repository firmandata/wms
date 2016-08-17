<?php
class C_orderout extends DataMapper {
	
	var $has_one = array(
		'c_businesspartner'	=> array('cascade_delete' => FALSE),
		'c_project'			=> array('cascade_delete' => FALSE)
	);
	
    var $has_many = array(
		'c_orderoutdetail'	=> array('cascade_delete' => FALSE)
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
        'orderout_date' => array(
            'label' => 'Date',
            'rules' => array('required', 'valid_date')
        ),
        'request_arrive_date' => array(
            'label' => 'Request Arrive Date',
            'rules' => array('valid_date')
        ),
		'c_project' => array(
            'label' => 'Project',
            'rules' => array('required')
        )
    );
}