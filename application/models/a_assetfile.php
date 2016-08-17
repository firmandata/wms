<?php
class A_assetfile extends DataMapper {
	
	var $has_one = array(
		'a_asset'			=> array('cascade_delete' => FALSE)
	);
	
    var $validation = array(
		'a_asset' => array(
            'label' => 'Asset',
            'rules' => array('required')
        ),
		'file_name' => array(
            'label' => 'File Name',
            'rules' => array('required')
        ),
		'file_path' => array(
            'label' => 'File Path',
            'rules' => array('required')
        ),
		'file_size' => array(
            'label' => 'File Size',
            'rules' => array('required')
        ),
		'file_mime' => array(
            'label' => 'File Mime',
            'rules' => array('required')
        )
    );
}