<?php
class M_inventory_assemblysource extends DataMapper {
	
	var $has_one = array(
		'm_inventory_assembly'	=> array('cascade_delete' => FALSE),
		'm_inventory'			=> array('cascade_delete' => FALSE),
		'm_grid'				=> array('cascade_delete' => FALSE),
		'm_product'				=> array('cascade_delete' => FALSE),
		'c_project'				=> array('cascade_delete' => FALSE)
	);
	
    var $validation = array(
		'm_inventory_assembly' => array(
            'label' => 'Assembly',
            'rules' => array('required')
        ),
        'm_inventory' => array(
            'label' => 'Inventory',
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