<?php

$response = check_required_vars
(	array
	(	'expected_vars'	=>	array
		(	'x'
		)
	)
); 
if	(	!$response
	):
	
	$response = array
	(	'success'	=>	1
	);

endif;
