<?php
class Sys_user extends DataMapper {
	
    var $has_many = array(
		'sys_usergroup_user'	=> array('cascade_delete' => FALSE)
	);
	
    var $validation = array(
        'username' => array(
            'label' => 'Username',
            'rules' => array('required', 'trim', 'unique', 'alpha_dash', 'min_length' => 3, 'max_length' => 20)
        ),
        'password' => array(
            'label' => 'Password',
            'rules' => array('required', 'min_length' => 6, 'max_length' => 100)
        ),
        'password_confirm' => array(
            'label' => 'Confirm Password',
            'rules' => array('required', 'matches' => 'password')
        ),
        'name' => array(
            'label' => 'Name',
            'rules' => array('required', 'trim', 'max_length' => 255)
        ),
        'email' => array(
            'label' => 'Email Address',
            'rules' => array('required', 'unique', 'trim', 'valid_email', 'max_length' => 255)
        ),
        'is_active' => array(
            'label' => 'Active',
            'rules' => array('boolean')
        )
    );
}