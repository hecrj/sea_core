<?php

# Throws an Exception with $info message and error code $code unless $boolean is TRUE
function ExceptionUnless($boolean, $info = 'Unexpected error', $code = 404)
{
	if(!$boolean)
		throw new \Exception($info, $code);
}

# Throws an Exception with $info message and error code $code if $boolean is TRUE
function ExceptionIf($boolean, $info = 'Unexpected error', $code = 404)
{
	if($boolean)
		throw new \Exception($info, $code);
}

?>
