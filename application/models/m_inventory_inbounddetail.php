<?php
class M_inventory_inbounddetail extends DataMapper {
	
	var $has_one = array(
		'm_inventory_inbound'		=> array('cascade_delete' => FALSE),
		'm_inventory_receivedetail'	=> array('cascade_delete' => FALSE),
		'm_inventory'				=> array('cascade_delete' => FALSE),
		'm_grid'					=> array('cascade_delete' => FALSE)
	);
	
    var $validation = array(
		'm_inventory_inbound' => array(
            'label' => 'Inbound',
            'rules' => array('required')
        ),
        'm_inventory_receivedetail' => array(
            'label' => 'Receive Item',
            'rules' => array('required')
        ),
		'm_inventory' => array(
            'label' => 'Inventory',
            'rules' => array('required')
        ),
        'quantity' => array(
            'label' => 'Quantity',
            'rules' => array('required')
        )
    );
}