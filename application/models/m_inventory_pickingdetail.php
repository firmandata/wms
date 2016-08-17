<?php
class M_inventory_pickingdetail extends DataMapper {
	
	var $has_one = array(
		'm_inventory_picking'			=> array('cascade_delete' => FALSE),
		'm_inventory_picklistdetail'	=> array('cascade_delete' => FALSE)
	);
	
	var $has_many = array(
		'm_inventory_shipmentdetail'	=> array('cascade_delete' => FALSE)
	);
	
	var $validation = array(
		'm_inventory_picking' => array(
            'label' => 'Picking',
            'rules' => array('required')
        ),
        'm_inventory_picklistdetail' => array(
            'label' => 'Picklist Item',
            'rules' => array('required')
        ),
        'quantity' => array(
            'label' => 'Quantity',
            'rules' => array('required')
        )
    );
}