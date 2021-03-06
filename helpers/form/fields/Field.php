<?php

namespace Sea\Helpers\Form\Fields;
use Sea\Helpers\HTML\Tag;

abstract class Field extends Tag {
	private $label;
	private $errors;
	private $status;
	private $helps = array();
	
	public function __construct($label) {
		$this->label = $label;
	}
	
	public function getLabel() {
		return $this->label;
	}
	
	public function setErrors($errors) {
		$this->errors = $errors;
		$this->status = $errors ? 'error' : 'success';
		
		return $this;
	}
	
	public function hasStatus() {
		return !empty($this->status);
	}
	
	public function getStatus() {
		return $this->status;
	}

	public function getErrors()
	{
		return $this->errors;
	}
	
	public function help($help, $type = 'help-block') {
		$this->helps[$type] = $help;
		
		return $this;
	}
	
	public function getHelps() {
		return $this->helps;
	}
}
