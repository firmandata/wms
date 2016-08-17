<?php
class M_inventory_assemblytarget extends DataMapper {
	
	var $has_one = array(
		'm_inventory_assembly'	=> array('cascade_delete' => FALSE),
		'm_inventory'			=> array('cascade_delete' => FALSE),
		'm_grid'				=> array('cascade_delete' => FALSE),
		'm_product'				=> array('cascade_delete' => FALSE),
		'c_project'				=> array('cascade_delete' => FALSE)
	);
	
    var $validation = array(
		'm_inventory_assembly' => array(
            'label' => 'Assembly',
            'rules' => array('required')
        ),
        'm_inventory' => array(
            'label' => 'Inventory',
            'rules' => array('required')
        ),
		'quantity' => array(
            'label' => 'Quantity',
            'rules' => array('required')
        ),
        'quantity_box' => array(
            'label' => 'Quantity Box',
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