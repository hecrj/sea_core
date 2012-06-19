<?php

namespace Core\Helpers\Form\Fields;
use Core\Helpers\HTML\Tag;

abstract class Field extends Tag {
	private $label;
	private $status;
	private $helps = array();
	
	public function __construct($label) {
		$this->label = $label;
	}
	
	public function getLabel() {
		return $this->label;
	}
	
	public function error($error) {
		$this->status = $error ? 'error' : 'success';
		
		return $this;
	}
	
	public function hasStatus() {
		return !empty($this->status);
	}
	
	public function getStatus() {
		return $this->status;
	}
	
	public function help($help, $type = 'help-block') {
		$this->helps[$type] = $help;
		
		return $this;
	}
	
	public function getHelps() {
		return $this->helps;
	}
}
