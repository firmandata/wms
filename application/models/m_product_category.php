<?php
class M_product_category extends DataMapper {
	
	var $has_one = array(
		'm_product'	=> array('cascade_delete' => FALSE),
		'm_category'=> array('cascade_delete' => FALSE)
	);
	
    var $validation = array(
		'm_product' => array(
            'label' => 'Product',
            'rules' => array('required')
        ),
		'm_category' => array(
            'label' => 'Category',
            'rules' => array('required')
        )
    );
}