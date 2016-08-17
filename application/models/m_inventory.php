<?php
class M_inventory extends DataMapper {
	
	var $has_one = array(
		'm_product'			=> array('cascade_delete' => FALSE),
		'm_grid'			=> array('cascade_delete' => FALSE),
		'c_project'			=> array('cascade_delete' => FALSE),
		'c_businesspartner'	=> array('cascade_delete' => FALSE)
	);
	
	var $has_many = array(
		'm_inventorylog'				=> array('cascade_delete' => FALSE),
		'm_inventory_inbounddetail'		=> array('cascade_delete' => FALSE),
		'm_inventory_putawaydetail'		=> array('cascade_delete' => FALSE),
		'm_inventory_movedetail'		=> array('cascade_delete' => FALSE),
		'm_inventory_adjustdetail'		=> array('cascade_delete' => FALSE),
		'm_inventory_holddetail'		=> array('cascade_delete' => FALSE),
		'm_inventory_picklistdetail'	=> array('cascade_delete' => FALSE),
		'cus_m_inventory_inbounddetail'	=> array('cascade_delete' => FALSE),
		'm_inventory_assemblysource'	=> array('cascade_delete' => FALSE),
		'm_inventory_assemblytarget'	=> array('cascade_delete' => FALSE)
	);
	
    var $validation = array(
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
        ),
		'packed_date' => array(
            'label' => 'Packed Date',
            'rules' => array('valid_date')
        ),
		'received_date' => array(
            'label' => 'Received Date',
            'rules' => array('valid_date')
        )
    );
}