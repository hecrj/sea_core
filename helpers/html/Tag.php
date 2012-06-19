<?php

namespace Core\Helpers\HTML;

abstract class Tag {
	static $tagName;
	protected $attributes = array();
	
	public function _construct() {
	
	}
	
	public function getTagName() {
		return $this::$tagName;
	}
	
	public function set($attribute, $value) {
		$this->attributes[$attribute] = $value;
		
		return $this;
	}
	
	public function get($attribute) {
		return $this->attributes[$attribute];
	}
	
	public function shift($attribute) {
		if(!isset($this->attributes[$attribute]))
			return null;
		
		$shift = $this->attributes[$attribute];
		unset($this->attributes[$attribute]);
		
		return ' '. $shift;
	}
	
	public function getAttributes() {
		$attributes = '';
		
		foreach($this->attributes as $attribute => $value)
			$attributes .= ' ' . $attribute . '="' . $value . '"';
			
		return $attributes;
	}
}
