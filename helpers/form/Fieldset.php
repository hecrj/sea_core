<?php

namespace Sea\Core\Helpers\Form;
use Sea\Core\Helpers\HTML\Tag;
use Sea\Core\Model;

class Fieldset extends Tag {
	static $tagName = 'fieldset';
	
	private $model;
	private $name;
	private $legend;
	private $fields;
	private $posted;
	private $success;
	private $successMsg;
	
	public function __construct(Model $model, $posted = false) {
		$this->model = $model;
		$this->name = lcfirst(get_class($model));
		$this->fields = array();
		$this->posted = $posted;
		$this->success = $this->check_success();
	}
	
	private function check_success() {
		if(! $this->posted)
			return false;
		
		if(is_object($this->model->errors))
			return $this->model->errors->is_empty();
		else
			return $this->model->is_valid();
	}
	
	public function getFields() {
		return $this->fields;
	}
	
	public function legend($legend) {
		$this->legend = $legend;
		
		return $this;
	}
	
	public function hasLegend() {
		return !empty($this->legend);
	}
	
	public function hasStatus() {
		return $this->posted;
	}
	
	public function isSuccess() {
		return $this->success;
	}
	
	public function getErrors() {
		return (array) $this->model->errors->full_messages();
	}
	
	public function success($message) {
		$this->successMsg = $message;
		
		return $this;
	}
	
	public function getSuccessMessage() {
		return $this->successMsg;
	}
	
	public function field($label, $id, $class = 'Input') {
		$class = __namespace__ . '\\Fields\\' . $class;
		
		$field = new $class($label);
		$field->set('id', $id);
		$field->set('name', $this->name . '['. $id .']');
		$field->set('value', htmlspecialchars($this->model->$id));
		
		if($this->posted)
			$field->error($this->model->errors->on($id));
		
		$this->fields[] = $field;
		
		return $field;
	}
	
	public function input($label, $id) {
		return $this->field($label, $id, 'Input');
	}
	
	public function textarea($label, $id) {
		return $this->field($label, $id, 'Textarea');
	}
	
	public function select($label, $id) {
		return $this->field($label, $id, 'Select');
	}
	
}
