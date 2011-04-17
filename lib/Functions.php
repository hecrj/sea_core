<?php

# CSS link tag
function css_tag($path_file, $media = 'screen')
{
	return '<link href="/css/'.$path_file.'.css" rel="stylesheet" type="text/css" media="'.$media.'" />';
}

# Throws an Exception with $info message and error code 404 unless $boolean is TRUE
function To404Unless($boolean, $info = 'Unexpected error')
{
	if(!$boolean)
		throw new Exception($info, 404);
}

?>