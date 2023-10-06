<?php

// $response_format = 'jsonp'; // DEFAULT VALUE IN interface_header.php, NO NEED TO SET EXPLICITLY UNLESS DESIRED FORMAT IS NOT jsonp

$response = check_required_vars
(	array
	(	'expected_vars'	=>	array
		(	'var1'
		,	'var2'
		,	'var3'
		,	'callback'
		)
	)
); 
if	(	!$response
	):

	// do yr thing here

	

endif;
