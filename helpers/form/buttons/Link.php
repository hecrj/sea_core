<?php

namespace Sea\Helpers\Form\Buttons;
use Sea\Helpers\HTML\Tag;

class Link extends Tag {
	static $tagName = 'anchor';
	protected $attributes = array('href' => '/');
	
	private $text;
	
	public function __construct($text) {
		$this->text = $text;
	}
	
	public function getText() {
		return $this->text;
	}
}
