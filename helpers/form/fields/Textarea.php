<?php

namespace Sea\Helpers\Form\Fields;

class Textarea extends Field {
	static $tagName = 'textarea';
	
	protected $attributes = array('rows' => 15);
	private $content;
	
	public function set($attribute, $value) {
		if($attribute != 'value')
			return parent::set($attribute, $value);
		
		$this->content = $value;
		
		return $this;
	}
	
	public function getContent() {
		return $this->content;
	}
}
