<?php

# Pagination helper
class Pagination
{
	static $default_limit = 10;
	private $actual_page;
	private $total_pages;
	
	// Make a pagination
	public static function make($model, $options = array())
	{
		// If limit isn't set
		if(!isset($options['limit']))
			// Set default limit
			$options['limit'] = static::$default_limit;
		
		// If limit is set but it's not an integer
		elseif(!is_int($options['limit']))
			// Get integer value
			$options['limit'] = intval($options['limit']);
		
		// Set options to select models efficiently
		$count_options = array(
			'select'		=> 'id',
			'conditions' 	=> ((isset($options['conditions'])) ? $options['conditions'] : array())
		);
		
		// Calculate total pages
		$total_pages = ceil( count( $model::all($count_options) ) / $options['limit'] );
		
		// Get actual page
		$actual_page = intval(Request::params('page'));
		
		// If actual_page is false (0) or it's bigger than total pages
		if(!$actual_page or $actual_page > $total_pages)
			// Set actual page to first page
			$actual_page = 1;
		
		// Calculate offset: [(actual_page - 1) * limit]
		$options['offset'] = ($actual_page - 1) * $options['limit'];
		
		// Get results
		$results = $model::all($options);
		
		$Pagination = new Pagination($actual_page, $total_pages);
		
		return array($Pagination, $results);
	}
	
	public function __construct($actual_page, $total_pages)
	{
		// Set actual page and total pages
		$this->actual_page = $actual_page;
		$this->total_pages = $total_pages;
	}
	
	public function render(Array $custom = null)
	{
		// Default options hash
		$options = array('ajax' => false);
		
		// If custom options are set
		if($custom)
			// Merge default with custom options hash
			$options = array_merge($options, $custom);
		
		// Print div for pagination
		echo '                <div class="pagination'. (($options['ajax']) ? ' ajax-links' : '') . '">'."\n";
		
		// If the actual page isn't 1
		if($this->actual_page != 1)
			// Print link to previous page
			echo '                    <a href="' . Router::$route . 'page-' . ($this->actual_page - 1) . '">&laquo; Previous</a>'."\n";
		
		// If no page limit is set
		if(!isset($options['pages']))
			// Show all the pages
			for($page = 1; $this->total_pages >= $page; $page ++)
				echo '                    <a href="' . Router::$route . 'page-'. $page . '"' . (($this->actual_page == $page) ? ' class="active"' : '') . '>' . $page . '</a>'."\n";
		
		// If page limit is set
		else
		{
			// If page limit is integer
			if(is_int($options['pages']))
				// Previous pages and next pages equals to integer
				$previous_pages = $next_pages = $options['pages'];
				
			// If page limit isn't an integer --> Array
			else
			{
				// Previous pages are first integer of the array
				$previous_pages = $options['pages'][0];
				
				// Next pages are second integer of the array
				$next_pages = $options['pages'][1];
			}
			
			$this->render_previous_pages($previous_pages);
			
			echo '                    <a href="' . Router::$route . 'page-'. $this->actual_page . '" class="active">' . $this->actual_page . '</a>'."\n";
			
			$this->render_next_pages($next_pages);
		}
			
		if($this->actual_page != $this->total_pages)
			echo '                    <a href="' . Router::$route . 'page-' . ($this->actual_page + 1) . '">Next &raquo;</a>'."\n";
		
		echo '                </div>'."\n";
	}
	
	private function render_previous_pages($pages)
	{
		$pages_diff = $this->actual_page - $pages;
		
		for($page = $this->actual_page - 1; $page > 0 and $page >= $pages_diff; $page --)
			$previous_pages = '                    <a href="' . Router::$route . 'page-'. $page . '">' . $page . '</a>'."\n" . $previous_pages;
			
		echo $previous_pages;
	}
	
	private function render_next_pages($pages)
	{
		$pages_diff = $this->actual_page + $pages;
		
		for($page = $this->actual_page + 1; $page <= $this->total_pages and $page <= $pages_diff; $page ++)
			echo '                    <a href="' . Router::$route . 'page-'. $page . '">' . $page . '</a>'."\n";
	}
	
}

?>