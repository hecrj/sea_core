<?php

# Form helper
class Form
{
	private $posted;
	private $models = array();
	private $model_active;
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
		
		return '                <form action="/' . $action . '"' . $options . '>'."\n";
	}
	
	public function to($reference, $model)
	{
		$this->models[$reference] = $model;
		$this->model_active = $reference;
		
		if($this->posted)
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
	
	public function input($label, $name, $custom = null)
	{
		$options = array('type' => 'text');
		
		$options = $this->options_string($options, $custom);
		$model = $this->models[$this->model_active];
		
		return '                    <div id="field_' . $name .'"' . (($this->posted) ? ' class="' . (($model->errors->on($name)) ? 'error' : 'success') . '"' : '' ) . '>
                        <label for="' . $name . '">' . $label . '</label>
                        <input name="' . $this->model_active . '[' . $name . ']" id="' . $name . '"'. $options . ' value="' . $model->$name . '" />
                    </div>'."\n";
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