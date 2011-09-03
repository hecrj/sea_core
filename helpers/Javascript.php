<?php

namespace Core\Helpers;

class Javascript
{
	private $js;
	
	public function __construct()
	{
		$this->js = array();
	}
	
	public function add()
	{
		foreach(func_get_args() as $jsPath)
			$this->js[] = $jsPath;
	}
	
	public function render()
	{
		foreach($this->js as $jsPath)
			$tags .= '<script type="text/javascript" src="/js/'. $jsPath .'.js"></script>'."\n";
		
		return $tags;
	}
}

?>
