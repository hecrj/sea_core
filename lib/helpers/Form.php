<?php

# Form helper
class Form
{
	private $posted;
	private $models = array();
	private $model_active;
	private $model;
	private $inputs = array();
	private $buttons = array();
	
	public function __construct()
	{
		// Set if the form has been posted
		$this->posted = Request::isPost();
		
		// Set models defined as arguments of the constructor
		if($models = func_get_args())
			foreach($models as $model)
				$this->models[] = $model;
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
	public function open($action, Array $custom = null)
	{
		$options = array('method' => 'post', 'accept-charset' => 'utf-8');
		$options = $this->options_string($options, $custom);
		
		return '                <form action="/' . $action . '"' . $options . '>
                    <input name="csrf_token" type="hidden" value="' . Session::read('csrf_token') . '" />'."\n";
	}
	
	public function to($reference, $model = false)
	{
		$this->models[$reference] = $model;
		$this->model = $model;
		$this->model_active = $reference;
		
		if($this->posted and $model)
		{
			$errors = '                    <div class="message error">
	            <h3>Some errors have ocurred:</h3>
	            <ul>'."\n";
			foreach($model->errors->full_messages() as $error_msg)
			{
				$errors .= '                        <li>' . $error_msg . '</li>'."\n";
			}
		
			$errors .= '                    </ul>
	            </div>'."\n";
			
			return $errors;
		}
	}
	
	public function label($label, $name, $content)
	{	
		return '                    <div id="field_' . $name .'"' . (($this->posted and $this->model) ? ' class="' . (($this->model->errors->on($name)) ? 'error' : 'success') . '"' : '' ) . '>
                        <label for="' . $name . '">' . $label . '</label>
                        ' . $content . '
                    </div>'."\n";
	}
	
	public function input($label, $name, Array $custom = null)
	{
		$options = array('type' => 'text');
		
		$options = $this->options_string($options, $custom);
		
		return $this->label($label, $name, '<input name="' . $this->model_active . '[' . $name . ']" id="' . $name . '"' . $options . ' value="' . $this->model->$name . '" />');
	}
	
	public function textarea($label, $name, Array $custom = null)
	{
		$options = array('cols' => 60, 'rows' => 10);
		
		$options = $this->options_string($options, $custom);
		
		return $this->label($label, $name, '<textarea name="' . $this->model_active . '[' . $name . ']" id="' . $name . '"' . $options . '>'. $this->model->$name . '</textarea>');
	}
	
	public function select($label, $name, Array $selects, Array $custom = null)
	{
		$options = array();
		$options = $this->options_string($options, $custom);
		
		foreach($selects as $value => $option)
			$select_options .= '                            <option value="' . $value . '"' . (($this->model->$name == $value) ? ' selected="selected"' : '') . '>' . $option . '</option>'."\n";
			
		return $this->label($label, $name, '<select name="' . $this->model_active . '[' . $name . ']" id="' . $name . '"'. $options . '>' . "\n" . $select_options . '                        </select>');
	}
	
	public function close($submit_text, $options = array())
	{
		return '                    <div class="buttons">
                        <button type="' . (($options['type']) ? $options['type'] : 'submit') . '" class="button">' . $submit_text . '</button>
                    </div>
                </form>'."\n";
	}
	
	
}

?>
