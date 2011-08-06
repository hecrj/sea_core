<?php

namespace Core\Components;
use Core\Components\Request;

# Pagination helper
class Pagination
{
	private $request;
	private $model;
	private $limit = 10;
	private $conditions = array();
	private $order = 'id DESC';
	private $format = 'page-';
	private $actual_page;
	private $total_pages;
	private $path;
	
	public function __construct(Route $route)
	{
		$this->path = '/'. $route->getPath() ?: 'index/';
		
		if(substr($this->path, 0, -1) != '/')
			$this->path = $this->path . '/';
		
		preg_match('/'.$this->format.'([0-9]+)\/$/', $this->path, $matches);
		
		$this->actual_page = (int)$matches[1];
	}
	
	public function model($model)
	{
		if(!isset($this->model))
			$this->model = strval($model);
			
		return $this;
	}
	
	public function limit($limit)
	{
		$this->limit = (int)$limit;
		
		return $this;
	}
	
	public function conditions(Array $conditions)
	{
		$this->conditions = $conditions;
		
		return $this;
	}
	
	public function order($order)
	{
		$this->order = $order;
		
		return $this;
	}
	
	public function page($page)
	{
		$this->actual_page = (int)$page;
	}
	
	public function getResults()
	{
		// Set options to select models efficiently
		$count_options = array(
			'select'		=> 'id',
			'conditions' 	=> $this->conditions
		);
		
		// Calculate total pages
		$this->total_pages = ceil( count( $model::all($count_options) ) / $this->limit );
		
		// If actual_page is false (0) or it's bigger than total pages
		if(!$this->actual_page or $this->actual_page > $this->total_pages)
			// Set actual page to first page
			$this->actual_page = 1;
		
		// Calculate offset
		$offset = ($this->actual_page - 1) * $this->limit;
		
		// Set model var for parsing reasons
		$model = $this->model;
		
		// Return results
		return $model::all(array(
			'conditions'	=>	$this->conditions,
			'limit'			=>	$this->limit,
			'order'			=>	$this->order,
			'offset'		=>	$offset
		));
	}
	
	public function path($path)
	{
		if(substr($path, 0, -1) != '/')
			$path = $path . '/';
		
		$this->path = $path;
		
		return $this;
	}
	
	public function render(Array $custom = null)
	{
		// Delete format from route
		$this->path = str_replace($this->format . $this->actual_page .'/', '', $this->path);
		
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
			echo '                    <a href="' . $this->path . $this->format . ($this->actual_page - 1) . '">&laquo; Previous</a>'."\n";
		
		// If no page limit is set
		if(!isset($options['pages']))
			// Show all the pages
			for($page = 1; $this->total_pages >= $page; $page ++)
				echo '                    <a href="' . $this->path . $this->format . $page . '"' . (($this->actual_page == $page) ? ' class="active"' : '') . '>' . $page . '</a>'."\n";
		
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
			
			echo '                    <a href="' . $this->path . $this->format . $this->actual_page . '" class="active">' . $this->actual_page . '</a>'."\n";
			
			$this->render_next_pages($next_pages);
		}
			
		if($this->actual_page != $this->total_pages)
			echo '                    <a href="' . $this->path . $this->format . ($this->actual_page + 1) . '">Next &raquo;</a>'."\n";
		
		echo '                </div>'."\n";
	}
	
	private function render_previous_pages($pages)
	{
		$pages_diff = $this->actual_page - $pages;
		
		for($page = $this->actual_page - 1; $page > 0 and $page >= $pages_diff; $page --)
			$previous_pages = '                    <a href="' . $this->path . $this->format . $page . '">' . $page . '</a>'."\n" . $previous_pages;
			
		echo $previous_pages;
	}
	
	private function render_next_pages($pages)
	{
		$pages_diff = $this->actual_page + $pages;
		
		for($page = $this->actual_page + 1; $page <= $this->total_pages and $page <= $pages_diff; $page ++)
			echo '                    <a href="' . $this->path . $this->format . $page . '">' . $page . '</a>'."\n";
	}
	
}

?>
