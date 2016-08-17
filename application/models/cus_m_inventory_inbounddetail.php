<?php
class Cus_m_inventory_inbounddetail extends DataMapper {
	
	var $has_one = array(
		'm_inventory'		=> array('cascade_delete' => FALSE),
		'm_product'			=> array('cascade_delete' => FALSE),
		'm_grid'			=> array('cascade_delete' => FALSE),
		'c_project'			=> array('cascade_delete' => FALSE),
		'c_businesspartner'	=> array('cascade_delete' => FALSE)
	);
	
    var $validation = array(
        'barcode' => array(
            'label' => 'Barcode',
            'rules' => array('required', 'trim', 'max_length' => 255)
        ),
		'm_inventory' => array(
            'label' => 'Inventory',
            'rules' => array('required')
        ),
		'm_product' => array(
            'label' => 'Product',
            'rules' => array('required')
        ),
        'quantity' => array(
            'label' => 'Quantity',
            'rules' => array('required')
        ),
		'packed_date' => array(
            'label' => 'Packed Date',
            'rules' => array('valid_date')
        ),
		'received_date' => array(
            'label' => 'Received Date',
            'rules' => array('valid_date')
        )
    );
}