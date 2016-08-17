<?php
class Cus_m_inventory_forecast extends DataMapper {
	
    var $has_many = array(
		'cus_m_inventory_forecastdetail'	=> array('cascade_delete' => FALSE)
	);
	
    var $validation = array(
		'code' => array(
            'label' => 'Code',
            'rules' => array('required', 'unique', 'trim', 'max_length' => 50)
        )
    );
}