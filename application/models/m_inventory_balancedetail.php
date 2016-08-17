<?php
class M_inventory_balancedetail extends DataMapper {
	
	var $has_one = array(
		'm_inventory_balance'	=> array('cascade_delete' => FALSE),
		'm_inventory'			=> array('cascade_delete' => FALSE),
		'm_grid'				=> array('cascade_delete' => FALSE)
	);
	
    var $validation = array(
		'm_inventory_balance' => array(
            'label' => 'Inventory Balance',
            'rules' => array('required')
        ),
		'm_inventory' => array(
            'label' => 'Inventory',
            'rules' => array('required')
        ),
        'packed_date' => array(
            'label' => 'Packed Date',
            'rules' => array('valid_date')
        ),
        'expired_date' => array(
            'label' => 'Expired Date',
            'rules' => array('valid_date')
        )
    );
}