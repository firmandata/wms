<?php
class M_inventory_putawaydetail extends DataMapper {
	
	var $has_one = array(
		'm_inventory_putaway'	=> array('cascade_delete' => FALSE),
		'm_inventory'			=> array('cascade_delete' => FALSE),
		'm_product'				=> array('cascade_delete' => FALSE),
		'm_gridfrom'			=> array(
			'class'				=> 'm_grid',
			'other_field'		=> 'm_inventory_putawaydetail_from',
			'cascade_delete' 	=> FALSE
		),
		'm_gridto'			=> array(
			'class'				=> 'm_grid',
			'other_field'		=> 'm_inventory_putawaydetail_to',
			'cascade_delete'	=> FALSE
		)
	);
	
    var $validation = array(
		'm_inventory_putaway' => array(
            'label' => 'Putaway',
            'rules' => array('required')
        ),
        'm_inventory' => array(
            'label' => 'Inventory',
            'rules' => array('required')
        ),
		'm_gridfrom' => array(
            'label' => 'Grid From',
            'rules' => array('required')
        ),
		'm_gridto' => array(
            'label' => 'Grid To',
            'rules' => array('required')
        ),
        'quantity_from' => array(
            'label' => 'Quantity From',
            'rules' => array('required')
        ),
        'quantity_to' => array(
            'label' => 'Quantity To',
            'rules' => array('required')
        )
    );
}