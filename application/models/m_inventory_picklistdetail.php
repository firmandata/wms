<?php
class M_inventory_picklistdetail extends DataMapper {
	
	var $has_one = array(
		'm_inventory_picklist'	=> array('cascade_delete' => FALSE),
		'c_orderoutdetail'		=> array('cascade_delete' => FALSE),
		'm_inventory'			=> array('cascade_delete' => FALSE),
		'm_grid'				=> array('cascade_delete' => FALSE),
		'm_product'				=> array('cascade_delete' => FALSE),
		'c_project'				=> array('cascade_delete' => FALSE),
		'c_businesspartner'		=> array('cascade_delete' => FALSE)
	);
	
	var $has_many = array(
		'm_inventory_pickingdetail'	=> array('cascade_delete' => FALSE)
	);
	
    var $validation = array(
		'm_inventory_picklist' => array(
            'label' => 'Pick List',
            'rules' => array('required')
        ),
        'c_orderoutdetail' => array(
            'label' => 'Order Out Item',
            'rules' => array('required')
        ),
		'm_inventory' => array(
            'label' => 'Inventory',
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