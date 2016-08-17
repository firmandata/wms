<?php
class C_orderoutdetail extends DataMapper {
	
	var $has_one = array(
		'c_orderout'	=> array('cascade_delete' => FALSE),
		'm_product'		=> array('cascade_delete' => FALSE)
	);
	
	var $has_many = array(
		'm_inventory_picklistdetail'	=> array('cascade_delete' => FALSE)
	);
	
    var $validation = array(
		'c_orderout' => array(
            'label' => 'Delivery Order',
            'rules' => array('required')
        ),
        'm_product' => array(
            'label' => 'Product',
            'rules' => array('required')
        ),
        'quantity' => array(
            'label' => 'Quantity',
            'rules' => array('required')
        )
    );
}