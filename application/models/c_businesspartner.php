<?php
class C_businesspartner extends DataMapper {
	
	var $has_one = array(
		'c_department'	=> array('cascade_delete' => FALSE),
		'c_region'		=> array('cascade_delete' => FALSE)
	);
	
	var $has_many = array(
		'c_orderin'						=> array('cascade_delete' => FALSE),
		'c_orderout'					=> array('cascade_delete' => FALSE),
		'c_project'						=> array('cascade_delete' => FALSE),
		'a_asset_supplier'				=> array(
			'class'				=> 'a_asset',
			'other_field'		=> 'c_businesspartner_supplier',
			'cascade_delete'	=> FALSE
		),
		'a_asset_user'					=> array(
			'class'				=> 'a_asset',
			'other_field'		=> 'c_businesspartner_user',
			'cascade_delete'	=> FALSE
		),
		'a_asset_transferdetail_from'	=> array(
			'class'				=> 'a_asset_transferdetail',
			'other_field'		=> 'c_businesspartner_userfrom',
			'cascade_delete'	=> FALSE
		),
		'a_asset_transferdetail_to'		=> array(
			'class'				=> 'a_asset_transferdetail',
			'other_field'		=> 'c_businesspartner_userto',
			'cascade_delete'	=> FALSE
		)
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