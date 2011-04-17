<?php

# Throws an Exception with $info message and error code 404 unless $boolean is TRUE
function To404Unless($boolean, $info = 'Unexpected error')
{
	if(!$boolean)
		throw new Exception($info, 404);
}

?>