<?php

namespace Sea\Core\Helpers\Form\Buttons;
use Sea\Core\Helpers\HTML\Tag;

class Button extends Tag {
	static $tagName = 'button';
	protected $attributes = array('type' => 'button');
	
	private $text;
	
	public function __construct($text) {
		$this->text = $text;
	}
	
	public function getText() {
		return $this->text;
	}
}
