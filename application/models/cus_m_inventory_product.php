<?php
class Cus_m_inventory_product extends DataMapper {
	
	var $has_many = array(
		'cus_m_inventory_cyclecount'	=> array('cascade_delete' => FALSE)
	);
	
    var $validation = array(
		'sku' => array(
            'label' => 'SKU',
            'rules' => array('required', 'unique', 'trim', 'max_length' => 150)
        ),
		'barcode_length' => array(
            'label' => 'Barcode Length',
            'rules' => array('required')
        ),
		'qty_start' => array(
            'label' => 'Qty Start',
            'rules' => array('required')
        ),
		'qty_end' => array(
            'label' => 'Qty End',
            'rules' => array('required')
        ),
		'sku_start' => array(
            'label' => 'SKU Start',
            'rules' => array('required')
        ),
		'sku_end' => array(
            'label' => 'SKU End',
            'rules' => array('required')
        ),
		'carton_start' => array(
            'label' => 'Carton Start',
            'rules' => array('required')
        ),
		'carton_end' => array(
            'label' => 'Carton End',
            'rules' => array('required')
        ),
		'date_packed_start' => array(
            'label' => 'Date Packed Start',
            'rules' => array('required')
        ),
		'date_packed_end' => array(
            'label' => 'Date Packed End',
            'rules' => array('required')
        )
    );
}