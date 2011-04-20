<?php

# Model class
class Model extends ActiveRecord\Model
{
	public function ajax_check($attribute, $attributes)
	{
		if(!Request::isAjax() or !in_array($attribute, $attributes))
			Request::redirect('/' . Router::$controller);
		
		if(!$this->is_valid())
		{
			$errors = $this->errors->to_array();
			
			if(!empty($errors[$attribute]))
				die($errors[$attribute][0]);
		}

		die(ucwords($attribute) . ' is correct!');
	}
}

?>