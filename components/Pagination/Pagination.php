<?php

namespace Core\Components\Pagination;
use Core\Components\Routing\ContextInterface;
use Core\Components\Routing\Generators\URLGeneratorInterface;

# Pagination helper
class Pagination
{
	private $model;
	private $limit = 10;
	private $conditions = array();
	private $joins = array();
	private $include = array();
	private $order = 'id DESC';
	private $results;
	private $active;
	private $total;
	private $start;
	private $end;
	private $path;
	
	public function __construct(ContextInterface $context, URLGeneratorInterface $generator) {
		$routeName = $context->getRouteName();

		if($routeName === null)
			throw new \RuntimeException('You need to declare the corresponding route before using Pagination.');

		$arguments = $context->getArguments(array('page' => '%d'));

		$this->path = $generator->generate($routeName, $arguments);
	}
	
	public function model($model) {
		if(!isset($this->model))
			$this->model = strval($model);
			
		return $this;
	}
	
	public function limit($limit) {
		$this->limit = (int)$limit;
		
		return $this;
	}
	
	public function conditions(Array $conditions) {
		$this->conditions = $conditions;
		
		return $this;
	}
	
	public function joins(Array $joins) {
		$this->joins = $joins;
		
		return $this;
	}
	
	public function through($item) {
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
	
	public function includes(Array $include) {
		$this->include = $include;
		
		return $this;
	}
	
	public function order($order) {
		$this->order = $order;
		
		return $this;
	}
	
	public function page($page) {
		$this->active = (int)$page;
		
		return $this;
	}
	
	public function init() {
		// Set model var for parsing reasons
		$model = $this->model;
		
		// Set options to select models efficiently
		$count_options = array(
			'select'		=> $model::table()->table .'.id',
			'conditions' 	=> $this->conditions,
			'joins'			=> $this->joins
		);
		
		// Calculate total pages
		$this->total = ceil( count( $model::all($count_options) ) / $this->limit );
		
		// If actual_page is false (0) or it's bigger than total pages
		if(!$this->active or $this->active > $this->total)
			// Set actual page to first page
			$this->active = 1;
		
		// Set default page range
		$this->range(3);
		
		// Calculate offset
		$offset = ($this->active - 1) * $this->limit;
		
		// Return results
		$this->results = $model::all(array(
			'conditions'	=>	$this->conditions,
			'joins'			=>	$this->joins,
			'include'		=>	$this->include,
			'limit'			=>	$this->limit,
			'order'			=>	$this->order,
			'offset'		=>	$offset
		));
		
		return $this;
	}
	
	public function getResults() {
		return $this->results;
	}
	
	public function range($start, $end = null) {
		$this->start = $this->active - $start;
		
		if($this->start <= 0)
			$this->start = 1;
		
		$this->end = $end === null ? $this->active + $start : $this->active + $end;
		
		if($this->end > $this->total)
			$this->end = $this->total;
		
		return $this;
	}
	
	public function getPagePath($page) {
		return sprintf($this->path, $page);
	}
	
	public function getPreviousPath() {
		return $this->getPagePath($this->active - 1);
	}
	
	public function getNextPath() {
		return $this->getPagePath($this->active + 1);
	}
	
	public function getPages() {
		return range($this->start, $this->end);
	}
	
	public function isActive($page) {
		return $this->active == $page;
	}
	
	public function hasPrevious() {
		return $this->active > 1;
	}
	
	public function hasNext() {
		return $this->active < $this->total;
	}
	
	public function hasPreviousHidden() {
		return $this->start > 1;
	}
	
	public function hasNextHidden() {
		return $this->end < $this->total;
	}
}
