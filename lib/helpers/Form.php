<?php

# Form helper
class Form
{
	
	private $action;
	private $options = array('method' => 'post', 'accept-charset' => 'utf-8');
	private $models = array();
	private $model_active;
	private $inputs = array();
	private $buttons = array();
	
	public function __construct($action, Array $options = null)
	{
		$this->action = $action;
		
		if($options)
			$this->options = array_merge($this->options, $options);
	}
	
	public function to($reference, $model)
	{
		$this->models[$reference] = $model;
		$this->model_active = $reference;
	}
	
	public function input($name, $options = array())
	{
		$this->inputs[$this->model_active][$name] = $options;
	}
	
	public function button($text, $options = array())
	{
		$this->buttons[$text] = $options;
	}
	
	// This method is optimized to performance, that's why the presence of duplicated code
	// You can simplify this function easily, but you will loss some ms when rendering a form
	// The blank spaces are necessary to tab correctly and return tidy html code
	public function render()
	{
		$errors = '';
		$inputs = '';
		// Error handler
		if(Request::isPost())
		{
			foreach($this->models as $reference => $model)
			{
				$errors = '                <div class="errors">
                    <h3>Some errors have ocurred:</h3>
                    <ul>'."\n";
				foreach($model->errors->full_messages() as $error_msg)
				{
					$errors .= '                        <li>' . $error_msg . '</li>'."\n";
				}
				
				$errors .= '                    </ul>
                </div>'."\n";
				
				foreach($this->inputs[$reference] as $attribute => $options)
				{
					$inputs .= '                    <div class="' . (($model->errors->on($attribute)) ? 'error' : 'success') . '">
                        <label for="' . $attribute . '">' . (($options['label']) ? $options['label'] : ucwords($attribute)) . '</label>
                        <input name="' . $reference . '[' . $attribute . ']" id="' . $attribute . '" type="' . (($options['type']) ? $options['type'] : 'text') . '" value="' . $model->$attribute . '" />
                    </div>'."\n";
				}
			}
			
		}else {
			// Process inputs without errors
			foreach($this->models as $reference => $model)
			{
				foreach($this->inputs[$reference] as $attribute => $options)
				{
					$inputs .= '                    <div>
                        <label for="' . $attribute . '">' . (($options['label']) ? $options['label'] : ucwords($attribute)) . '</label>
                        <input name="' . $reference . '[' . $attribute . ']" id="' . $attribute . '" type="' . (($options['type']) ? $options['type'] : 'text') . '" value="' . $model->$attribute . '" />
                    </div>'."\n";
				}
			}
		}
		
		$buttons = '                    <div class="buttons">'."\n";
		
		foreach($this->buttons as $text => $options)
			$buttons .= '                        <button type="' . (($options['type']) ? $options['type'] : 'submit') . '" class="button">' . $text . '</button>'."\n";
		
		$buttons .= '                    </div>';
		
		return $errors . '                <form action="/' . $this->action . '" method="' . $this->options['method'] . '" accept-charset="' . $this->options['accept-charset'] . '">' . "\n" . $inputs . $buttons . "\n" 
				. '                </form>'."\n";
	}
}

?>