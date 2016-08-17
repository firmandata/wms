<?php
class M_inventory_shipmentdetail extends DataMapper {
	
	var $has_one = array(
		'm_inventory_shipment'		=> array('cascade_delete' => FALSE),
		'm_inventory_pickingdetail'	=> array('cascade_delete' => FALSE)
	);
	
	var $validation = array(
		'm_inventory_shipment' => array(
            'label' => 'Shipment',
            'rules' => array('required')
        ),
        'm_inventory_pickingdetail' => array(
            'label' => 'Picking Item',
            'rules' => array('required')
        ),
		'quantity' => array(
            'label' => 'Quantity',
            'rules' => array('required')
        )
    );
}