<?php
class A_asset_transfer extends DataMapper {
	
    var $has_many = array(
		'a_asset_transferdetail'	=> array('cascade_delete' => FALSE)
	);
	
    var $validation = array(
		'code' => array(
            'label' => 'Code',
            'rules' => array('required', 'unique', 'trim', 'max_length' => 30)
        ),
        'transfer_date' => array(
            'label' => 'Date',
            'rules' => array('required', 'valid_date')
        )
    );
}