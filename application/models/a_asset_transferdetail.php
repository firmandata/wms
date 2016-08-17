<?php
class A_asset_transferdetail extends DataMapper {
	
	var $has_one = array(
		'a_asset_transfer'				=> array('cascade_delete' => FALSE),
		'a_asset'						=> array('cascade_delete' => FALSE),
		'c_businesspartner_userfrom'	=> array(
			'class'				=> 'c_businesspartner',
			'other_field'		=> 'a_asset_transferdetail_from',
			'cascade_delete' 	=> FALSE
		),
		'c_businesspartner_userto'		=> array(
			'class'				=> 'c_businesspartner',
			'other_field'		=> 'a_asset_transferdetail_to',
			'cascade_delete'	=> FALSE
		),
		'c_departmentfrom'	=> array(
			'class'				=> 'c_department',
			'other_field'		=> 'a_asset_transferdetail_from',
			'cascade_delete' 	=> FALSE
		),
		'c_departmentto'		=> array(
			'class'				=> 'c_department',
			'other_field'		=> 'a_asset_transferdetail_to',
			'cascade_delete'	=> FALSE
		)
	);
	
    var $validation = array(
		'a_asset_transfer' => array(
            'label' => 'Transfer',
            'rules' => array('required')
        ),
        'a_asset' => array(
            'label' => 'Asset',
            'rules' => array('required')
        )
    );
}