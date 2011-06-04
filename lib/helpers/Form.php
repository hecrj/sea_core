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
	private $editing = false;
	
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
	public function open($action = null, Array $custom = null)
	{
		$options = array('method' => 'post', 'accept-charset' => 'utf-8');
		$options = $this->options_string($options, $custom);
		
		return '                <form action="' . ((empty($action)) ? '' : '/'. $action) . '"' . $options . '>
                    <input name="csrf_token" type="hidden" value="' . Session::read('csrf_token') . '" />'."\n";
	}
	
	public function to($reference, $model = false)
	{
		$this->models[$reference] = $model;
		$this->model = $model;
		$this->model_active = $reference;
		
		if($model)
		{
			if(!$this->editing)
				$this->editing = !$model->is_new_record();
			
			if($this->posted)
			{
				if(is_object($model->errors))
				{
					if($model->errors->is_empty())
						return null;
				}
				elseif($model->is_valid())
					return null;

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
		
		$input = '<input name="' . $this->model_active . '[' . $name . ']" id="' . $name . '"' . $options . (($this->model) ? ' value="' . $this->model->$name . '"' : '') .' />';
		
		if($label)
			return $this->label($label, $name, $input);
		else
			return $input;
	}
	
	public function textarea($label, $name, Array $custom = null)
	{
		$options = array('cols' => 60, 'rows' => 10);
		
		$options = $this->options_string($options, $custom);
		
		$textarea = '<textarea name="' . $this->model_active . '[' . $name . ']" id="' . $name . '"' . $options . '>'. $this->model->$name . '</textarea>';
		
		if($label)
			return $this->label($label, $name, $textarea);
		else
			return $textarea;
	}
	
	public function select($label, $name, Array $selects, Array $custom = null, $selected = null)
	{
		if(is_null($selected) and $this->model)
			$selected = $this->model->$name;
		
		$options = array();
		$options = $this->options_string($options, $custom);
		
		foreach($selects as $value => $option)
			$select_options .= '                            <option value="' . $value . '"' . (($selected == $value) ? ' selected="selected"' : '') . '>' . $option . '</option>'."\n";
		
		$select = '<select name="' . $this->model_active . '[' . $name . ']" id="' . $name . '"'. $options . '>' . "\n" . $select_options . '                        </select>';
		
		if($label)
			return $this->label($label, $name, $select);
		else
			return $select;		
	}
	
	public function close($default, $editing = null)
	{
		return '                    <div class="buttons">
                        <button type="submit" class="button">' . (($editing and $this->editing) ? $editing : $default) . '</button>
                    </div>
                </form>'."\n";
	}
	
	
}

?>
