<?php
class M_inventorylog extends DataMapper {
	
	var $has_one = array(
		'm_inventory'	=> array('cascade_delete' => FALSE),
		'm_product'		=> array('cascade_delete' => FALSE),
		'm_grid'		=> array('cascade_delete' => FALSE),
		'c_project'		=> array('cascade_delete' => FALSE),
		'c_businesspartner'	=> array('cascade_delete' => FALSE)
	);
	
	var $validation = array(
		'm_inventory' => array(
            'label' => 'Inventory',
            'rules' => array('required')
        ),
		'm_product' => array(
            'label' => 'Product',
            'rules' => array('required')
        ),
        'm_grid' => array(
            'label' => 'Grid',
            'rules' => array('required')
        ),
        'quantity' => array(
            'label' => 'Quantity',
            'rules' => array('required')
        )
    );
}