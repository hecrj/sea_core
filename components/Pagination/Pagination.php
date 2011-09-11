<?php

namespace Core\Components\Pagination;
use Core\Components\Router\Request;

# Pagination helper
class Pagination
{
	private $model;
	private $limit = 10;
	private $conditions = array();
	private $joins = array();
	private $include = array();
	private $order = 'id ASC';
	private $actual_page;
	private $total_pages;
	private $path;
	
	public function __construct(Request $request)
	{		
		$path = $request->getPath() .'/'. $request->get('pageFormat');
		
		if($path[0] != '/')
			$path = '/'. $path;
		
		$this->path = $path;
		$this->actual_page = $request->get('page');
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
	
	public function joins(Array $joins)
	{
		$this->joins = $joins;
		
		return $this;
	}
	
	public function through($item)
	{
		$model = $this->model;
		$modelTable = $model::table()->table;
		$modelColumn = strtolower($model);
		$itemTable = $item::table()->table;
		$itemColumn = strtolower(get_class($item));
		$throughTable = $modelColumn .'_'. $itemTable;
		
		
		$conditions = "{$throughTable}.{$itemColumn}_id = ? AND ".
				"{$modelTable}.id = {$throughTable}.{$modelColumn}_id";
				
		if(empty($this->conditions))
			$this->conditions = array($conditions, $item->id);
		
		else
		{
			$this->conditions[0] .= ' AND '. $conditions;
			$this->conditions[] = $item->id;
		}
		
		$this->joins[] = $modelColumn . $itemTable;
		
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
		// Set model var for parsing reasons
		$model = $this->model;
		
		// Set options to select models efficiently
		$count_options = array(
			'select'		=> $model::table()->table .'.id',
			'conditions' 	=> $this->conditions,
			'joins'			=> $this->joins
		);
		
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
			'joins'			=>	$this->joins,
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
		$options = array('pages' => array(5, 5), 'previous' => PAGINATION_PREV, 'next' => PAGINATION_NEXT);
		
		if($custom)
			$options = array_merge($options, $custom);
		
		echo '<div class="pagination">'."\n";
		echo '  <ul>'."\n";
		
		$this->renderPreviousPages($options);
		$this->renderPage($this->actual_page);
		$this->renderNextPages($options);
		
		echo '  </ul>'."\n";
		echo '</div>'."\n";
	}
	
	private function renderPage($number, $text = null)
	{
		echo '    <li';
		
		if($this->actual_page == $number)
			echo ' class="active"';
		
		echo '><a href="'. $this->path . $number. '">'. ($text ?: $number) .'</a></li>'."\n";
	}
	
	private function renderPreviousPages($options)
	{
		if($this->actual_page != 1)
			$this->renderPage($this->actual_page - 1, '&larr; '. $options['previous']);
		
		$pages_diff = $this->actual_page - $options['pages'][0];
		
		for($page = $this->actual_page - 1; $page > 0 and $page >= $pages_diff; $page --)
			$this->renderPage($page);
			
		echo $previous_pages;
	}
	
	private function renderNextPages($options)
	{	
		$pages_diff = $this->actual_page + $options['pages'][1];
		
		for($page = $this->actual_page + 1; $page <= $this->total_pages and $page <= $pages_diff; $page ++)
			$this->renderPage($page);
		
		if($this->actual_page != $this->total_pages)
			$this->renderPage($this->total_pages, $options['next'] .' &rarr;');
	}
	
}
