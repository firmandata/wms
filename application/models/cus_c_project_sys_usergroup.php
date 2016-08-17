<?php
class Cus_c_project_sys_usergroup extends DataMapper {
	
	var $has_one = array(
		'c_project'		=> array('cascade_delete' => FALSE),
		'sys_usergroup'	=> array('cascade_delete' => FALSE)
	);
	
    var $validation = array(
       'c_project' => array(
            'label' => 'Project',
            'rules' => array('required')
        ),
		'sys_usergroup' => array(
            'label' => 'User Group',
            'rules' => array('required')
        )
    );
}