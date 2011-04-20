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
		array('username', 'within' => array(4, 15))
	);
	
	static $validates_uniqueness_of = array(
		array('username')
	);
}

?>