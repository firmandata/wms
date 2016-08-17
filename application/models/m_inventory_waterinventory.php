<?php
class M_inventory_waterinventory extends DataMapper {
	
	var $has_one = array(
		'm_inventory_waterdetail'	=> array('cascade_delete' => FALSE),
		'm_inventory'				=> array('cascade_delete' => FALSE),
		'm_grid'					=> array('cascade_delete' => FALSE),
		'm_product'					=> array('cascade_delete' => FALSE),
		'c_project'					=> array('cascade_delete' => FALSE)
	);
	
    var $validation = array(
		'm_inventory_waterdetail' => array(
            'label' => 'Water Detail',
            'rules' => array('required')
        ),
        'm_inventory' => array(
            'label' => 'Inventory',
            'rules' => array('required')
        )
    );
}