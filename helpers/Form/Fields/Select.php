<?php

namespace Core\Helpers\Form\Fields;

class Select extends Field {
	static $tagName = 'select';
	
	private $options = array();
	private $selected;
	
	public function getOptions() {
		return $this->options;
	}
	
	public function option($text, $value = null) {
		$this->options[$text] = $value ?: count($this->options);
		
		return $this;
	}
	
	public function set($attribute, $value) {
		if($attribute != 'value')
			return parent::set($attribute, $value);
		
		$this->selected = $value;
		
		return $this;
	}
	
	public function isSelected($value) {
		return $this->selected == $value;
	}
}
