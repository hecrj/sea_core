<?php

namespace Core\Helpers;
use Core\Components\Security;
use Core\Components\Router\Request;

# Form helper
class Form
{
	private $security;
	private $posted;
	private $models = array();
	private $model_active;
	private $model;
	private $inputs = array();
	private $buttons = array();
	private $editing = false;
	
	public function __construct(Security $security, Request $request)
	{
		// Set dependencies
		$this->security = $security;
		$this->request = $request;
		
		// Set if the form has been posted
		$this->posted = ($request->getMethod() == 'POST');
	}
	
	private function options_string($options, $custom)
	{
		if($custom)
			$options = array_merge($options, $custom);
		
		foreach($options as $option => $value)
			$options_string .= ' ' . $option . '="' . $value . '"';
			
		return $options_string;
	}
	
	// Open form tag --> <form action="/posts/add" method="post" accept-charset="utf-8" ...
	public function open($action = null, Array $custom = null)
	{
		$options = array('method' => 'post', 'accept-charset' => 'utf-8');
		$options = $this->options_string($options, $custom);
		
		echo '<form action="' . ((empty($action)) ? '' : '/'. $action) . '"' . $options . '>'."\n";
        echo '  <input name="csrf_token" type="hidden" value="' . $this->security->getCSRFToken() . '" />'."\n";
		
		return $this;
	}
	
	public function to($reference, $model = false)
	{
		$this->models[$reference] = $model;
		$this->model = $model;
		$this->model_active = $reference;
		
		if(!$model)
			return $this;
		
		if(!$this->editing)
			$this->editing = !$model->is_new_record();
			
		if(! $this->posted)
			return $this;
			
		if(is_object($model->errors))
		{
			if($model->errors->is_empty())
				return $this;
		}
		elseif($model->is_valid())
			return $this;

		echo '  <div class="message error">'."\n";
		echo '    <h3>Some errors have ocurred:</h3>'."\n";
		echo '    <ul>'."\n";
			
		foreach($model->errors->full_messages() as $error_msg)
			echo '      <li>' . $error_msg . '</li>'."\n";

		echo '    </ul>'."\n";
		echo '  </div>'."\n";
		
		return $this;
	}
	
	public function label($label, $name, $content, $tip)
	{	
		echo '  <div id="field_'. $name .'"';
		
		if($this->posted and $this->model)
			echo ' class="' . (($this->model->errors->on($name)) ? 'error' : 'success') . '"';
		
		echo '>'."\n";
		echo '    <label for="' . $name . '">' . $label . '</label>'."\n";
		echo '    ' . $content . "\n";
		
		if($tip != null)
			echo '    <span id="tip_'. $name .'">'. $tip .'</span>'."\n";
		
		echo '  </div>'."\n";
	}
	
	public function input($label, $name, Array $custom = null, $tip = null)
	{
		$options = array('type' => 'text');
		
		$options = $this->options_string($options, $custom);
		
		$input = '<input name="' . $this->model_active . '[' . $name . ']" id="' . $name . '"' . $options . (($this->model) ? ' value="' . htmlspecialchars($this->model->$name) . '"' : '') .' />';
		
		if($label)
			$this->label($label, $name, $input, $tip);
		else
			echo '  '. $input ."\n";
			
		return $this;
	}
	
	public function textarea($label, $name, Array $custom = null, $tip = null)
	{
		$options = array('cols' => 60, 'rows' => 10);
		
		$options = $this->options_string($options, $custom);
		
		$textarea = '<textarea name="' . $this->model_active . '[' . $name . ']" id="' . $name . '"' . $options . '>'. (($this->model) ? htmlspecialchars($this->model->$name) : '') . '</textarea>';
		
		if($label)
			$this->label($label, $name, $textarea, $tip);
		else
			echo '  '. $textarea ."\n";
		
		return $this;
	}
	
	public function select($label, $name, Array $selects, $selected = null, Array $custom = null, $tip = null)
	{
		if(is_null($selected) and $this->model)
			$selected = $this->model->$name;
		
		$options = array();
		$options = $this->options_string($options, $custom);
		
		foreach($selects as $value => $option)
			$select_options .= '  <option value="' . $value . '"' . (($selected == $value) ? ' selected="selected"' : '') . '>' . $option . '</option>'."\n";
		
		$select = '<select name="' . $this->model_active . '[' . $name . ']" id="' . $name . '"'. $options . '>' . "\n" . $select_options . '                        </select>';
		
		if($label)
			$this->label($label, $name, $select, $tip);
		else
			echo $select;
		
		return $this;
	}
	
	public function close($default, $editing = null)
	{
		echo '  <div class="buttons">'."\n";
		echo '    <button type="submit" class="button">' . (($editing and $this->editing) ? $editing : $default) . '</button>'."\n";
		echo '  </div>'."\n";
		echo '</form>'."\n";
	}
	
	
}

?>
