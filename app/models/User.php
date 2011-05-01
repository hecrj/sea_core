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
	private static $hash_roles = array('guest', 'inactive', 'registered', 'moderator', 'webmaster');
	
	/**
	 * Hash of possible user groups.
	 * 
	 * The keys in this hash are the names of the different groups you wish.
	 * The values in this hash are arrays with the roles included in the group.
	 *
	 * @var array
	 */
	private static $hash_groups = array(
		'active' 	=>	array('registered', 'moderator', 'webmaster'),
		'staff'		=>	array('moderator', 'webmaster')
	);
	
	public $confirm_password;
	
	// Stores user role
	private $role;
	// Stores user groups
	private $groups = array();
	
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
		if($user = User::find(array('username' => $username, 'password' => $password)))
			return $user; // Return found user

		// If user does not exist
		else
			return new User; // Return an empty new user
	}
	
	/**
	 * Return TRUE if the user is logged and FALSE if not.
	 *
	 * @return boolean TRUE if is logged, FALSE if not
	 */
	public function isLogged()
	{
		return (bool)$this->id; // User isn't logged when id is null
	}
	
	/**
	 * Get the user role related with role_id value.
	 *
	 * @return string Name of the user role
	 */
	public function get_role()
	{
		// If role is undefined
		if(! $this->role)
			// Set role name using role_id value
			$this->role = self::$hash_roles[$this->role_id];
		
		return $this->role; // Return role name
	}
	
	/**
	 * Get the user groups related with user role.
	 *
	 * @return array Names of the user groups
	 */
	public function get_groups()
	{
		// If user groups are undefined
		if(! $this->groups)
			// For each group in $hash_groups
			foreach(self::$hash_groups as $group => $roles)
				// If user role is in the group roles
				if(in_array($this->get_role(), $roles))
					$this->groups[] = $group; // Add group to array of groups
		
		return $this->groups; // Return array with names of the user groups
	}
	
	/**
	 * Check if the user belongs to one group of roles.
	 *
	 * @param array/string Array of roles or name of one defined group
	 * @return boolean TRUE if user belongs to, FALSE if not
	 */
	public function is($roles_group)
	{
		// If $roles_group is undefined
		if(!$roles_group)
			return true;
		
		// If $roles_group is a group name
		if(! is_array($roles_group))
			// Return if the group is defined in $hash_groups and the user role is in that group
			return (is_array(self::$hash_groups[$roles_group]) and in_array($this->get_role(), self::$hash_groups[$roles_group]));
		
		// If $roles_group is an array of roles
		else
			// Return if the user role is in $roles_group
			return in_array($this->get_role(), $roles_group);
	}
}

?>
