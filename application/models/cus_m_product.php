<?php
class Cus_m_product extends DataMapper {
	
	var $has_one = array(
		'm_product'	=> array('cascade_delete' => FALSE)
	);
	
    var $validation = array(
		'm_product' => array(
            'label' => 'Product',
            'rules' => array('required', 'unique')
        ),
		'barcode_length' => array(
            'label' => 'Barcode Length',
            'rules' => array('required')
        ),
		'quantity_start' => array(
            'label' => 'Quantity Start',
            'rules' => array('required')
        ),
		'quantity_end' => array(
            'label' => 'Quantity End',
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
		'packed_date_start' => array(
            'label' => 'Packed Date Start',
            'rules' => array('required')
        ),
		'packed_date_end' => array(
            'label' => 'Packed Date End',
            'rules' => array('required')
        ),
		'quantity_divider' => array(
            'label' => 'Quantity Divider',
            'rules' => array('required')
        )
    );
}