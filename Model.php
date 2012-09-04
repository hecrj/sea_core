<?php

namespace Sea;
use Sea\Components\Routing\Request;

class Model extends \ActiveRecord\Model
{
	public static function find_by($attribute, $value) {
		$find = self::find(array($attribute => $value));
		
		if($find === null)
			throw new \RuntimeException(get_called_class() .' with '. $attribute .': '. $value .' not found.');
		
		return $find;
	}
	
	public function updateFrom(Request $request, $postKey = null, $method = 'POST') {
		if($request->getMethod() != $method)
			return false;
		
		if(null == $postKey)
			$postKey = strtolower(get_class($this));
			
		return $this->update_attributes($request->post[$postKey]);
	}
}

