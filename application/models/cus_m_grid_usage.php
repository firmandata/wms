<?php
class Cus_m_grid_usage extends DataMapper {
	
	var $has_one = array(
		'm_grid'	=> array('cascade_delete' => FALSE)
	);
	
    var $validation = array(
		'm_grid' => array(
            'label' => 'Grid',
            'rules' => array('required', 'unique')
        ),
        'is_forecast_request' => array(
            'label' => 'Forecast Request',
            'rules' => array('boolean')
        )
    );
}