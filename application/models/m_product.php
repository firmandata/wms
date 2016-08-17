<?php
class M_product extends DataMapper {
	
	var $has_one = array(
		'm_productgroup'	=> array('cascade_delete' => FALSE)
	);
	
	var $has_many = array(
		'c_orderindetail'				=> array('cascade_delete' => FALSE),
		'm_inventory'					=> array('cascade_delete' => FALSE),
		'm_inventory_putawaydetail'		=> array('cascade_delete' => FALSE),
		'm_inventory_movedetail'		=> array('cascade_delete' => FALSE),
		'm_inventory_adjustdetail'		=> array('cascade_delete' => FALSE),
		'm_inventory_holddetail'		=> array('cascade_delete' => FALSE),
		'c_orderoutdetail'				=> array('cascade_delete' => FALSE),
		'm_inventory_picklistdetail'	=> array('cascade_delete' => FALSE),
		'cus_m_product'					=> array('cascade_delete' => FALSE),
		'cus_m_inventory_inbounddetail'	=> array('cascade_delete' => FALSE),
		'm_inventory_assemblysource'	=> array('cascade_delete' => FALSE),
		'm_inventory_assemblytarget'	=> array('cascade_delete' => FALSE),
		'm_product_category'			=> array('cascade_delete' => FALSE),
		'a_asset'						=> array('cascade_delete' => FALSE),
		'm_inventory_inbounddetail'		=> array('cascade_delete' => FALSE)
	);
	
    var $validation = array(
		'code' => array(
            'label' => 'Code',
            'rules' => array('required', 'unique', 'trim', 'max_length' => 50)
        ),
        'name' => array(
            'label' => 'Name',
            'rules' => array('required', 'trim', 'max_length' => 255)
        )
    );
}