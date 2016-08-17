<?php
class Cus_m_inventory_cyclecount extends DataMapper {
	
	var $has_one = array(
		'cus_m_inventory_product'	=> array('cascade_delete' => FALSE)
	);
	
    var $validation = array(
		'cus_m_inventory_product' => array(
            'label' => 'Inventory Product',
            'rules' => array('required')
        ),
        'barcode' => array(
            'label' => 'Barcode',
            'rules' => array('required', 'unique', 'trim', 'max_length' => 255)
        ),
        'quantity' => array(
            'label' => 'Quantity',
            'rules' => array('required')
        ),
        'status' => array(
            'label' => 'Status',
            'rules' => array('required')
        ),
		'date_packed' => array(
            'label' => 'Date Packed',
            'rules' => array('required', 'valid_date')
        )
    );
}