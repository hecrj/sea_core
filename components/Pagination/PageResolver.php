<?php

namespace Core\Components\Pagination;
use Core\Components\Router\ResolverAbstract;
use Core\Components\Router\Request;

/**
 * Cleans the page from the route.
 *
 * @author Héctor Ramón Jiménez
 */
class PageResolver extends ResolverAbstract
{
	protected function resolve(Request $request, Array $rules)
	{
		$path = '/'. $request->getPath();
		$page = 1;
		$format = $rules['page_format'] ?: 'page-';
		$pattern = '/\/'. $format .'([0-9]+)\/?/';
		
		if(preg_match($pattern, $path, $matches))
		{
			$page = $matches[1];
			
			$path = preg_replace($pattern, '', $path);
		}
		
		$request->setPath($path);
		$request->set('page', $page);
		$request->set('pageFormat', $format);
		
		// Controller data is not set yet
		return false;
	}
}
