<?php
class A_assetamount extends DataMapper {
	
	var $has_one = array(
		'a_asset'			=> array('cascade_delete' => FALSE)
	);
	
    var $validation = array(
		'a_asset' => array(
            'label' => 'Asset',
            'rules' => array('required')
        ),
        'depreciated_date' => array(
            'label' => 'Depreciated Date',
            'rules' => array('required', 'valid_date')
        ),
		'book_value' => array(
            'label' => 'Book Value',
            'rules' => array('required')
        ),
		'market_value' => array(
            'label' => 'Market Value',
            'rules' => array('required')
        ),
		'depreciation_accumulated' => array(
            'label' => 'Depreciation Accumulated',
            'rules' => array('required')
        )
    );
}