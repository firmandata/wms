<?php
class C_orderindetail extends DataMapper {
	
	var $has_one = array(
		'c_orderin'	=> array('cascade_delete' => FALSE),
		'm_product'	=> array('cascade_delete' => FALSE)
	);
	
	var $has_many = array(
		'm_inventory_receivedetail'		=> array('cascade_delete' => FALSE)
	);
	
    var $validation = array(
		'c_orderin' => array(
            'label' => 'Order In',
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