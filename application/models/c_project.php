<?php
class C_project extends DataMapper {
	
	var $has_one = array(
		'c_businesspartner'	=> array('cascade_delete' => FALSE)
	);
	
	var $has_many = array(
		'c_orderin'						=> array('cascade_delete' => FALSE),
		'c_orderout'					=> array('cascade_delete' => FALSE),
		'm_inventory'					=> array('cascade_delete' => FALSE),
		'm_inventorylog'				=> array('cascade_delete' => FALSE),
		'cus_m_inventory_inbounddetail'	=> array('cascade_delete' => FALSE),
		'cus_c_project_sys_usergroup'	=> array('cascade_delete' => FALSE),
		'm_inventory_assemblysource'	=> array('cascade_delete' => FALSE),
		'm_inventory_assemblytarget'	=> array('cascade_delete' => FALSE),
		'm_inventory_picklistdetail'	=> array('cascade_delete' => FALSE),
		'm_inventory_inbounddetail'		=> array('cascade_delete' => FALSE)
	);
	
    var $validation = array(
		'code' => array(
            'label' => 'Code',
            'rules' => array('required', 'unique', 'trim', 'max_length' => 30)
        ),
        'name' => array(
            'label' => 'Name',
            'rules' => array('required', 'trim', 'max_length' => 255)
        )
    );
}