<?php

namespace Core\Helpers\Form\Fields;

class Input extends Field {
	static $tagName = 'input';
	
	protected $attributes = array('type' => 'text');
}
