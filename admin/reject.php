<?php

if	(	!empty
		(	$GLOBALS['page']->request['t']
		)
	&&	!empty
		(	$GLOBALS['dbi']->tables[$GLOBALS['page']->request['t']]
		)
	&&	!empty
		(	$GLOBALS['page']->request['id']
		)
	):
	
	while
		(	!$GLOBALS['dbi']->get_result
			(	"	UPDATE	".$GLOBALS['page']->request['t']."
					SET		status			=	'Hidden'
					WHERE	id				=	".$GLOBALS['page']->request['id']."
				"	
			)
		):
		continue;	
	endwhile;
	
	$GLOBALS['dbi']->tables[$GLOBALS['page']->request['t']]->get_ownerships();
	
	while
		(	!$GLOBALS['dbi']->get_result
			(	"	DELETE	
					FROM	owned
					WHERE	ownership_id	=	".$GLOBALS['dbi']->tables[$GLOBALS['page']->request['t']]->owners['approved_by']."
					AND		owned_id		=	".$GLOBALS['page']->request['id']."
				"	
			)
		):
		continue;	
	endwhile;
		
	header
	(	'Location:admin.php?z='
	.	$GLOBALS['page']->request['t']
	);
else:
	header
	(	'Location:admin.php'
	);
endif;
exit;

