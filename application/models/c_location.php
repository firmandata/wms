<?php
class C_location extends DataMapper {
	
	var $has_one = array(
		'c_region'		=> array('cascade_delete' => FALSE),
		'c_department'	=> array('cascade_delete' => FALSE)
	);
	
	var $has_many = array(
		'a_asset'					=> array('cascade_delete' => FALSE),
		'a_asset_movedetail_from'	=> array(
			'class'				=> 'a_asset_movedetail',
			'other_field'		=> 'c_locationfrom',
			'cascade_delete'	=> FALSE
		),
		'a_asset_movedetail_to'		=> array(
			'class'				=> 'a_asset_movedetail',
			'other_field'		=> 'c_locationto',
			'cascade_delete'	=> FALSE
		)
	);
	
    var $validation = array(
		'code' => array(
            'label' => 'Code',
            'rules' => array('required', 'unique', 'trim', 'max_length' => 15)
        ),
        'name' => array(
            'label' => 'Name',
            'rules' => array('required', 'trim', 'max_length' => 255)
        ),
		'c_region' => array(
            'label' => 'Region',
            'rules' => array('required')
        )
    );
}