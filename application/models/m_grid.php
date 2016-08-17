<?php
class M_grid extends DataMapper {
	
	var $has_one = array(
		'm_warehouse'		=> array('cascade_delete' => FALSE),
		'm_productgroup'	=> array('cascade_delete' => FALSE)
	);
	
	var $has_many = array(
		'm_inventory_inbounddetail'			=> array('cascade_delete' => FALSE),
		'cus_m_inventory_inbounddetail'		=> array('cascade_delete' => FALSE),
		'm_inventory'						=> array('cascade_delete' => FALSE),
		'm_inventory_putawaydetail_from'	=> array(
			'class'				=> 'm_inventory_putawaydetail',
			'other_field'		=> 'm_gridfrom',
			'cascade_delete'	=> FALSE
		),
		'm_inventory_putawaydetail_to'		=> array(
			'class'				=> 'm_inventory_putawaydetail',
			'other_field'		=> 'm_gridto',
			'cascade_delete'	=> FALSE
		),
		'm_inventory_movedetail_from'		=> array(
			'class'				=> 'm_inventory_movedetail',
			'other_field'		=> 'm_gridfrom',
			'cascade_delete'	=> FALSE
		),
		'm_inventory_movedetail_to'			=> array(
			'class'				=> 'm_inventory_movedetail',
			'other_field'		=> 'm_gridto',
			'cascade_delete'	=> FALSE
		),
		'm_inventory_adjustdetail'			=> array('cascade_delete' => FALSE),
		'm_inventory_holddetail'			=> array('cascade_delete' => FALSE),
		'm_inventory_picklistdetail'		=> array('cascade_delete' => FALSE),
		'cus_m_inventory_forecastdetail'	=> array('cascade_delete' => FALSE),
		'cus_m_grid_usage'					=> array('cascade_delete' => FALSE),
		'm_inventory_assemblysource'		=> array('cascade_delete' => FALSE),
		'm_inventory_assemblytarget'		=> array('cascade_delete' => FALSE)
	);
	
    var $validation = array(
		'm_warehouse' => array(
            'label' => 'Warehouse',
            'rules' => array('required')
        ),
        'code' => array(
            'label' => 'Code',
            'rules' => array('required', 'unique', 'trim', 'max_length' => 12)
        ),
        'row' => array(
            'label' => 'Row',
            'rules' => array('required')
        ),
        'col' => array(
            'label' => 'Column',
            'rules' => array('required')
        ),
        'level' => array(
            'label' => 'Level',
            'rules' => array('required')
        )
    );
}