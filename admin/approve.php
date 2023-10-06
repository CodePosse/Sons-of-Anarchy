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

	$GLOBALS['dbi']->tables[$GLOBALS['page']->request['t']]->get_ownerships();
	
	$GLOBALS['dbi']->insert_row
	(	array
		(	'table'	=>	'owned'
		,	'row'	=>	array
			(	'ownership_id'	=>	$GLOBALS['dbi']->tables[$GLOBALS['page']->request['t']]->owners['approved_by']
			,	'owner_id'		=>	$GLOBALS['user']->id
			,	'owned_id'		=>	$GLOBALS['page']->request['id']
			)
		)
	);
	
	while
		(	!$GLOBALS['dbi']->affect_rows
			(	array
				(	'table'	=>	$GLOBALS['page']->request['t']
				,	'rows'	=>	array
					(	$GLOBALS['page']->request['id']	=>	array
						(	'updated'	=>	date
							(	'Y-m-d H:i:s'
							)
						)
					)
				)
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

