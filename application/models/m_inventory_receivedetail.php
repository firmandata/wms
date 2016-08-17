<?php
class M_inventory_receivedetail extends DataMapper {
	
	var $has_one = array(
		'm_inventory_receive'	=> array('cascade_delete' => FALSE),
		'c_orderindetail'		=> array('cascade_delete' => FALSE),
		'm_grid'				=> array('cascade_delete' => FALSE)
	);
	
	var $has_many = array(
		'm_inventory_inbounddetail'			=> array('cascade_delete' => FALSE),
		'cus_m_inventory_forecastdetail'	=> array('cascade_delete' => FALSE)
	);
	
    var $validation = array(
		'm_inventory_receive' => array(
            'label' => 'Receive',
            'rules' => array('required')
        ),
        'c_orderindetail' => array(
            'label' => 'Order In Detail',
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