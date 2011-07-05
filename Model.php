<?php

namespace Core;

# Model class
class Model extends ActiveRecord\Model
{
	/**
	 * Check if an attribute is valid on the current model and return a message with status info.
	 *
	 * @param string $attribute Name of the attribute to check.
	 * @param array $attributes Hash of attributes allowed to be checked
	 */
	public function ajax_check($attribute, $attributes)
	{
		// If the request isn't AJAX request or $attribute isn't in hash of allowed attributes
		if(!Request::isAjax() or !in_array($attribute, $attributes))
			// Redirect to controller root page
			Request::redirect('/' . Router::getController());
		
		// If the model isn't valid
		if(!$this->is_valid())
		{
			// Retrieve errors in a hash
			$errors = $this->errors->to_array();
			
			// If has errors related with $attribute
			if(!empty($errors[$attribute]))
				// Die (AJAX) with error information
				die($errors[$attribute][0]);
		}
		
		// If model is valid or hasn't errors related with $attribute --> Die with a success message
		die(ActiveRecord\Utils::human_attribute($attribute) . ' is correct!');
	}
}

?>
