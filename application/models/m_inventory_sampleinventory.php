<?php
class M_inventory_sampleinventory extends DataMapper {
	
	var $has_one = array(
		'm_inventory_sampledetail'	=> array('cascade_delete' => FALSE),
		'm_inventory'				=> array('cascade_delete' => FALSE),
		'm_grid'					=> array('cascade_delete' => FALSE),
		'm_product'					=> array('cascade_delete' => FALSE),
		'c_project'					=> array('cascade_delete' => FALSE)
	);
	
    var $validation = array(
		'm_inventory_sampledetail' => array(
            'label' => 'Sample Detail',
            'rules' => array('required')
        ),
        'm_inventory' => array(
            'label' => 'Inventory',
            'rules' => array('required')
        )
    );
}