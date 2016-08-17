<?php
class M_inventory_putaway extends DataMapper {
	
    var $has_many = array(
		'm_inventory_putawaydetail'	=> array('cascade_delete' => FALSE)
	);
	
    var $validation = array(
		'code' => array(
            'label' => 'Code',
            'rules' => array('required', 'unique', 'trim', 'max_length' => 30)
        ),
        'putaway_date' => array(
            'label' => 'Date',
            'rules' => array('required', 'valid_date')
        )
    );
}