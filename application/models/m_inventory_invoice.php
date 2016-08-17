<?php
class M_inventory_invoice extends DataMapper {
	
	var $has_one = array(
		'c_businesspartner'	=> array('cascade_delete' => FALSE)
	);
	
    var $has_many = array(
		'm_inventory_invoicedetail'	=> array('cascade_delete' => FALSE)
	);
	
    var $validation = array(
		'code' => array(
            'label' => 'Code',
            'rules' => array('required', 'unique', 'trim', 'max_length' => 30)
        ),
        'invoice_date' => array(
            'label' => 'Date',
            'rules' => array('required', 'valid_date')
        ),
		'c_businesspartner' => array(
            'label' => 'Business Partner',
            'rules' => array('required')
        ),
        'period_from' => array(
            'label' => 'Period From',
            'rules' => array('required', 'valid_date')
        ),
        'period_to' => array(
            'label' => 'Period To',
            'rules' => array('required', 'valid_date')
        )
    );
}