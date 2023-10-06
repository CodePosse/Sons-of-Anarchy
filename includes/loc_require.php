<?php

if	(	empty
		(	$GLOBALS['loc']
		)
	):
	if	(	empty
			(	$co
			)
		):
		$co = $_COOKIE[$GLOBALS['cfg']['cookie']['co']['name']];
	endif;
	if	(	!empty
			(	$co
			)
		&&	file_exists
			(	$co
				.	'/localize_'
				.	$co
				.	'.php'
			)
		):
		require_once
		(	$co
			.	'/localize_'
			.	$co
			.	'.php'
		);
	else:
		setcookie
		(	$GLOBALS['cfg']['cookie']['co']['name']
		,	''
		,	time()
			-	(	60
				*	60
				*	24
				*	90
				)	
		,	'/'
		,	$GLOBALS['cfg']['cookie_domain']
		);
		header
		(	'Location:'
			.	$_SERVER['REQUEST_URI']
		);
		exit;
	endif;
endif;

