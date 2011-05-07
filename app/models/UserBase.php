<?php

# UserBase abstract model
abstract class UserBase extends Model
{	
	// Stores hash of roles
	protected static $hash_roles = array();
	
	// Stores hash of groups
	protected static $hash_groups = array();
	
	// Stores user role
	private $role;
	
	// Stores user groups
	private $groups = array();
	
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
			$this->role = static::$hash_roles[$this->role_id];
		
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
			foreach(static::$hash_groups as $group => $roles)
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
			return (is_array(static::$hash_groups[$roles_group]) and in_array($this->get_role(), static::$hash_groups[$roles_group]));
		
		// If $roles_group is an array of roles
		else
			// Return if the user role is in $roles_group
			return in_array($this->get_role(), $roles_group);
	}
}

?>
