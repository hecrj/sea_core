<?php

namespace Sea\Core\Components\Cache;
use Sea\Core\Model;

/**
 * Abstract Cache class.
 *
 * @author Héctor Ramón Jiménez
 */
abstract class CacheAbstract
{
	protected $dir;
	protected $fileExtension = '.cache';
	
	public function setDir($dir) {
		// @todo Think about final slash...
		//if(substr($dir, -1) == '/')
		//	$dir = substr($dir, 0, -1);
		
		$this->dir = DIR .'cache/'. $dir;
		
		return $this;
	}
	
	public function to(Model $model) {
		return $this->setDir($model::table()->table . '/' . $model->id);
	}
	
	public function getDir() {
		return $this->dir;
	}
	
	public function getPath($filename) {
		return $this->getDir() . '/' . $filename . $this->fileExtension;
	}
}
