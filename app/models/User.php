<?php

# User model
class User extends Model
{
	static $validates_presence_of = array(
		array('username'),
		array('password'),
		array('email')
	);
	
	static $validates_length_of = array(
		array('username', 'within' => array(4, 15)),
		array('password', 'within' => array(6, 15))
	);
	
	static $validates_uniqueness_of = array(
		array('username'),
		array('email')
	);
	
	static $validates_format_of = array(
		array('email', 'with' => '/^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i')
	);
	
	static $before_save = array('encrypt_password');
	
	public $confirm_password;
	
	public function validate()
	{
		// If password is dirty and passwords don't match
		if($this->attribute_is_dirty('password') && md5($this->password) != md5($this->confirm_password))
			// Add an error to confirm_password
			$this->errors->add('confirm_password', 'does not match!');
	}
	
	public function encrypt_password()
	{
		// If password is dirty
		if($this->attribute_is_dirty('password'))
			// Encrypt md5 password before save
			$this->password = md5($this->password);
	}
	
}

?>