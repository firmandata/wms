<?php
class A_asset_movedetail extends DataMapper {
	
	var $has_one = array(
		'a_asset_move'		=> array('cascade_delete' => FALSE),
		'a_asset'			=> array('cascade_delete' => FALSE),
		'c_locationfrom'	=> array(
			'class'				=> 'c_location',
			'other_field'		=> 'a_asset_movedetail_from',
			'cascade_delete' 	=> FALSE
		),
		'c_locationto'		=> array(
			'class'				=> 'c_location',
			'other_field'		=> 'a_asset_movedetail_to',
			'cascade_delete'	=> FALSE
		)
	);
	
    var $validation = array(
		'a_asset_move' => array(
            'label' => 'Move',
            'rules' => array('required')
        ),
        'a_asset' => array(
            'label' => 'Asset',
            'rules' => array('required')
        ),
		'c_locationfrom' => array(
            'label' => 'Location From',
            'rules' => array('required')
        ),
		'c_locationto' => array(
            'label' => 'Location To',
            'rules' => array('required')
        )
    );
}