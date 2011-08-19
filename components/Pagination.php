<?php

namespace Core\Components;
use Core\Components\Router\Route;

# Pagination helper
class Pagination
{
	private $model;
	private $limit = 10;
	private $conditions = array();
	private $include = array();
	private $order = 'id ASC';
	private $actual_page;
	private $total_pages;
	private $path;
	
	public function __construct(Route $route)
	{
		$path = '/'. $route->getPageFormat();
		$routePath = $route->getPath();
		
		if(! empty($routePath))
			$path = '/'. $routePath . $path;
		
		$this->path = $path;
		$this->actual_page = $route->getPage();
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
	
	public function includes(Array $include)
	{
		$this->include = $include;
		
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
		
		return $this;
	}
	
	public function getResults()
	{
		// Set options to select models efficiently
		$count_options = array(
			'select'		=> 'id',
			'conditions' 	=> $this->conditions
		);
		
		// Set model var for parsing reasons
		$model = $this->model;
		
		// Calculate total pages
		$this->total_pages = ceil( count( $model::all($count_options) ) / $this->limit );
		
		// If actual_page is false (0) or it's bigger than total pages
		if(!$this->actual_page or $this->actual_page > $this->total_pages)
			// Set actual page to first page
			$this->actual_page = 1;
		
		// Calculate offset
		$offset = ($this->actual_page - 1) * $this->limit;
		
		// Return results
		return $model::all(array(
			'conditions'	=>	$this->conditions,
			'include'		=>	$this->include,
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
		// Default options hash
		$options = array('ajax' => false, 'previous' => '&laquo; Previous', 'next' => 'Next &raquo');
		
		// If custom options are set
		if($custom)
			// Merge default with custom options hash
			$options = array_merge($options, $custom);
		
		// Print div for pagination
		echo '                <div class="pagination'. (($options['ajax']) ? ' ajax-links' : '') . '">'."\n";
		
		// If the actual page isn't 1
		if($this->actual_page != 1)
			// Print link to previous page
			echo '                    <a href="' . $this->path . ($this->actual_page - 1) . '">'. $options['previous'] .'</a>'."\n";
		
		// If no page limit is set
		if(!isset($options['pages']))
			// Show all the pages
			for($page = 1; $this->total_pages >= $page; $page ++)
				echo '                    <a href="' . $this->path . $page . '"' . (($this->actual_page == $page) ? ' class="active"' : '') . '>' . $page . '</a>'."\n";
		
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
			
			echo '                    <a href="' . $this->path . $this->actual_page . '" class="active">' . $this->actual_page . '</a>'."\n";
			
			$this->render_next_pages($next_pages);
		}
			
		if($this->actual_page != $this->total_pages)
			echo '                    <a href="' . $this->path . ($this->actual_page + 1) . '">'. $options['next'] .'</a>'."\n";
		
		echo '                </div>'."\n";
	}
	
	private function render_previous_pages($pages)
	{
		$pages_diff = $this->actual_page - $pages;
		
		for($page = $this->actual_page - 1; $page > 0 and $page >= $pages_diff; $page --)
			$previous_pages = '                    <a href="' . $this->path . $page . '">' . $page . '</a>'."\n" . $previous_pages;
			
		echo $previous_pages;
	}
	
	private function render_next_pages($pages)
	{
		$pages_diff = $this->actual_page + $pages;
		
		for($page = $this->actual_page + 1; $page <= $this->total_pages and $page <= $pages_diff; $page ++)
			echo '                    <a href="' . $this->path . $page . '">' . $page . '</a>'."\n";
	}
	
}

?>
