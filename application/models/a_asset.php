<?php
class A_asset extends DataMapper {
	
	var $has_one = array(
		'm_product'					=> array('cascade_delete' => FALSE),
		'c_region'					=> array('cascade_delete' => FALSE),
		'c_department'				=> array('cascade_delete' => FALSE),
		'c_location'				=> array('cascade_delete' => FALSE),
		'c_businesspartner_supplier'=> array(
			'class'				=> 'c_businesspartner',
			'other_field'		=> 'a_asset_supplier',
			'cascade_delete' 	=> FALSE
		),
		'c_businesspartner_user'	=> array(
			'class'				=> 'c_businesspartner',
			'other_field'		=> 'a_asset_user',
			'cascade_delete' 	=> FALSE
		)
	);
	
	var $has_many = array(
		'a_assetamount'				=> array('cascade_delete' => FALSE),
		'a_assetfile'				=> array('cascade_delete' => FALSE),
		'a_asset_movedetail'		=> array('cascade_delete' => FALSE),
		'a_asset_transferdetail'	=> array('cascade_delete' => FALSE)
	);
	
    var $validation = array(
		'code' => array(
            'label' => 'Code',
            'rules' => array('required', 'unique', 'trim', 'max_length' => 100)
        ),
		'type' => array(
            'label' => 'Type',
            'rules' => array('required', 'trim', 'max_length' => 15)
        ),
		'c_region' => array(
            'label' => 'Region',
            'rules' => array('required')
        ),
		'c_department' => array(
            'label' => 'Department',
            'rules' => array('required')
        ),
        'm_product' => array(
            'label' => 'Product',
            'rules' => array('required')
        ),
		'c_location' => array(
            'label' => 'Location',
            'rules' => array('required')
        ),
		'quantity' => array(
            'label' => 'Quantity',
            'rules' => array('required')
        ),
        'purchase_date' => array(
            'label' => 'Purchase Date',
            'rules' => array('required', 'valid_date')
        ),
        'currency' => array(
            'label' => 'Currency',
            'rules' => array('required')
        ),
        'purchase_price' => array(
            'label' => 'Purchase Price',
            'rules' => array('required')
        ),
		'depreciation_period_type' => array(
            'label' => 'Period Type',
            'rules' => array('required')
        ),
		'depreciation_period_time' => array(
            'label' => 'Period Time',
            'rules' => array('required')
        )
    );
}