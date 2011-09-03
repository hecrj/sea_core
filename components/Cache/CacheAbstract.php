<?php

namespace Core\Components\Cache;
use Core\Model;

/**
 * Abstract Cache class.
 *
 * @author Héctor Ramón Jiménez
 */
abstract class CacheAbstract
{
	protected $dir;
	protected $fileExtension = '.cache';
	
	public function setDir($dir)
	{
		$this->dir = $dir;
		
		return $this;
	}
	
	public function setDirForModel(Model $model)
	{
		$this->dir = $model::table()->table .'/'. $model->id;
		
		return $this;
	}
	
	public function getDir()
	{
		return $this->dir;
	}
	
	public function getCacheDir()
	{
		return DIR .'cache/'. $this->dir .'/';
	}
	
	public function getCachePath($filename)
	{
		return $this->getCacheDir() . $filename . $this->fileExtension;
	}
	
}
