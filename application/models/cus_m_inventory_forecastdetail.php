<?php
class Cus_m_inventory_forecastdetail extends DataMapper {
	
	var $has_one = array(
		'cus_m_inventory_forecast'	=> array('cascade_delete' => FALSE),
		'm_inventory_receivedetail'	=> array('cascade_delete' => FALSE),
		'm_grid'					=> array('cascade_delete' => FALSE)
	);
	
    var $validation = array(
		'cus_m_inventory_forecast' => array(
            'label' => 'Inventory Forecast',
            'rules' => array('required')
        ),
		'm_inventory_receivedetail' => array(
            'label' => 'Inventory Receive Detail',
            'rules' => array('required')
        )
    );
}