<?php
class M_inventory_invoicedetail extends DataMapper {
	
	var $has_one = array(
		'm_inventory_invoice'	=> array('cascade_delete' => FALSE)
	);
	
    var $validation = array(
		'm_inventory_invoice' => array(
            'label' => 'Invoice',
            'rules' => array('required')
        )
    );
}