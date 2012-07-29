<?php

namespace Sea\Core\Helpers\Form;
use Sea\Core\Components\Security;
use Sea\Core\Components\Routing\Request;
use Sea\Core\Helpers\HTML\Tag;

class Form extends Tag {
	static $tagName = 'form';
	
	private $security;
	private $request;
	private $fieldsets;
	private $posted;
	private $buttons;
	private $editing;
	
	protected $attributes = array(
		'action' 			=> '',
		'method' 			=> 'post',
		'accept-charset'	=> 'utf-8'
	);
	
	public function __construct(Security $security, Request $request) {
		$this->security = $security;
		$this->request = $request;
		$this->fieldsets = array();
		$this->posted = ($request->getMethod() == 'POST');
		$this->buttons = array();
		$this->editing = false;
	}
	
	public function isPosted() {
		return $this->posted;
	}
	
	public function getToken() {
		return $this->security->getCSRFToken();
	}
	
	public function getFieldsets() {
		return $this->fieldsets;
	}
	
	public function fieldset($model) {
		if(!$this->editing and !$model->is_new_record())
			$this->editing = true;
		
		$fieldset = new Fieldset($model, $this->posted);
		$this->fieldsets[] = $fieldset;
		
		return $fieldset;
	}
	
	public function hasButtons() {
		return !empty($this->buttons);
	}
	
	public function getButtons() {
		return $this->buttons;
	}
	
	public function button($name, $class = 'Button') {
		$class = __namespace__ . '\\Buttons\\' . $class;

		$button = new $class($name);		
		$this->buttons[] = $button;
		
		return $button;
	}
	
	public function submit($name, $edit = null) {
		if($this->editing and $edit !== null)
			$name = $edit;
		
		$button = $this->button($name);
		$button->set('type', 'submit');
		
		return $button;
	}
	
	public function link($name) {
		return $this->button($name, 'Link');
	}
	
	public function isEditing() {
		return $this->editing;
	}
}
