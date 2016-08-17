<?php
class M_inventory_shipment extends DataMapper {
	
    var $has_many = array(
		'm_inventory_shipmentdetail'	=> array('cascade_delete' => FALSE)
	);
	
    var $validation = array(
		'code' => array(
            'label' => 'Code',
            'rules' => array('required', 'unique', 'trim', 'max_length' => 30)
        ),
        'shipment_date' => array(
            'label' => 'Date',
            'rules' => array('required', 'valid_date')
        )
    );
}