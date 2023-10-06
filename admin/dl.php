<?php

if	(	files::force_download
		(	$GLOBALS['page']->request['f']
		)	!==	false
	):
	$GLOBALS['user']->log_activity
	(	array
		(	'action'	=>	'DOWNLOADED "'
			.	$GLOBALS['page']->request['f']
			.	'" from '
			.	$GLOBALS['user']->ip
		)
	);
endif;
exit;