<?php

# CSS link tag
function css_tag($path_file, $media = 'screen')
{
	return '<link href="/css/' . $path_file . '.css" rel="stylesheet" type="text/css" media="'.$media.'" />
';
}

# JavaScript link tag
function js_tag($path_file)
{
	return '<script src="/js/' . $path_file . '.js" type="text/javascript"></script>
';
}

# Throws an Exception with $info message and error code $code unless $boolean is TRUE
function ExceptionUnless($boolean, $info = 'Unexpected error', $code = 404)
{
	if(!$boolean)
		throw new Exception($info, $code);
}

# Throws an Exception with $info message and error code $code if $boolean is TRUE
function ExceptionIf($boolean, $info = 'Unexpected error', $code = 404)
{
	if($boolean)
		throw new Exception($info, $code);
}

?>
