<?php
class M_inventory_balance extends DataMapper {
	
	var $has_one = array(
		'm_inventory'	=> array('cascade_delete' => FALSE)
	);
	
    var $has_many = array(
		'm_inventory_balancedetail'	=> array('cascade_delete' => FALSE)
	);
	
    var $validation = array(
		'code' => array(
            'label' => 'Code',
            'rules' => array('required', 'unique', 'trim', 'max_length' => 30)
        ),
		'm_inventory' => array(
            'label' => 'Inventory',
            'rules' => array('required')
        ),
        'balance_date' => array(
            'label' => 'Date',
            'rules' => array('required', 'valid_date')
        )
    );
}