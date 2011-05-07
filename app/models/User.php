<?php

// User model
## Feel free to customize this class at your will ##
class User extends UserBase
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
	
	/**
	 * Hash of possible user roles.
	 * 
	 * User table must have a role_id column. The value of role_id has to be an integer to relate
	 * one of this roles with the user.
	 * 
	 * EXAMPLE:
	 * With the next hash of roles...
	 * private static $hash_roles = array('guest', 'inactive', 'registered', 'moderator', 'webmaster');
	 *
	 * ROLE_ID			ROLE
	 * 0				guest
	 * 1				inactive
	 * ...				...
	 * 4				webmaster
	 *
	 * @var array
	 */
	protected static $hash_roles = array('guest', 'inactive', 'registered', 'moderator', 'webmaster');
	
	/**
	 * Hash of possible user groups.
	 * 
	 * The keys in this hash are the names of the different groups you wish.
	 * The values in this hash are arrays with the roles included in the group.
	 *
	 * @var array
	 */
	protected static $hash_groups = array(
		'active' 	=>	array('registered', 'moderator', 'webmaster'),
		'staff'		=>	array('moderator', 'webmaster')
	);
	
	public $confirm_password;
	
	/**
	 * Function executed every time after native validations.
	 * 
	 * IN ORDER:
	 * 1. If the password has changed, then checks if the password has been confirmed correctly.
	 */
	public function validate()
	{
		// If password is dirty and passwords don't match
		if($this->attribute_is_dirty('password') && md5($this->password) != md5($this->confirm_password))
			// Add an error to confirm_password
			$this->errors->add('confirm_password', 'does not match!');
	}
	
	/**
	 * Encrypts the password if is dirty before saving the user.
	 */
	public function encrypt_password()
	{
		// If password is dirty
		if($this->attribute_is_dirty('password'))
			// Encrypt md5 password before save
			$this->password = md5($this->password);
	}
	
	/**
	 * Tries to find an user with the provided username and password.
	 *
	 * @param string $username Username to find
	 * @param string $password Password to match with username
	 * @return User If username and password match, returns logged user. If not, returns an empty User
	 */
	public static function login($username, $password)
	{
		// If exists an user with $username and $password
		if($user = self::find(array('username' => $username, 'password' => $password)))
			return $user; // Return found user

		// If user does not exist
		else
			return new User; // Return an empty new user
	}
	
}

?>
