<?php
class M_inventory_waterdetail extends DataMapper {
	
	var $has_one = array(
		'm_inventory_water'	=> array('cascade_delete' => FALSE),
		'm_grid'				=> array('cascade_delete' => FALSE)
	);
	
	var $has_many = array(
		'm_inventory_waterinventory'	=> array('cascade_delete' => FALSE)
	);
	
    var $validation = array(
		'm_inventory_water' => array(
            'label' => 'Sample',
            'rules' => array('required')
        ),
        'm_grid' => array(
            'label' => 'Grid',
            'rules' => array('required')
        )
    );
}