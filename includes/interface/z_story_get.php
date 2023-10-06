<?php

$response = check_required_vars
(	array
	(	'expected_vars'	=>	array
		(	'id'
		,	'type'
		)
	)
); 
if	(	!$response
	):

	$response = 
	$errors = 
	array()
	;
	
	$table = 'story_'
	.	$GLOBALS['page']->request['type']
	;	
	if	(	empty
			(	$GLOBALS['dbi']->tables[$table]
			)
		):
		$errors[] = 'story type table "'
		.	$table
		.	'" does not exist'
		;
	else:
		$story = $GLOBALS['dbi']->get_result_array
		(	array
			(	'table'				=>	$table
			,	'equals'			=>	array
				(	'id'				=>	$GLOBALS['page']->request['id']
				)
			,	'limit_lo'			=>	1
			,	'pop_single_row'	=>	1
			)
		);
		if	(	empty
				(	$story
				)	
			):
			$errors[] = 'story type "'
			.	$table
			.	'" id #'
			.	$GLOBALS['page']->request['id']
			.	' not found'
			;
		else:
			$response = $story;
			
			//THIS IS A RIDICULOUS PIECE OF FUCKING DUCT TAPE. THERE HAS TO BE A BETER WAY OF GETTING USER NAME BUT I CANNOT CRACK IT./////////////
			
			$all_tellers = $GLOBALS['dbi']->get_result_array
			(	array
				(	'table'				=>	'story_teller'
				)
			);
			
			$GLOBALS['dbi']->tables['story_photo']->get_ownerships();
			$GLOBALS['dbi']->tables['story_video']->get_ownerships();
			
			$all_owners = $GLOBALS['dbi']->get_result_array
			(	array
				(	'table'				=>	'owned'
				,	'fields'			=>	array
					(	'owner_id'
					)
				,	'in'				=>	array
					(	'ownership_id'		=>	array
						(	$GLOBALS['dbi']->tables['story_photo']->owners['story_teller']
						,	$GLOBALS['dbi']->tables['story_video']->owners['story_teller']
						)
					)
				,	'key_by'			=>	'owned_id'
				,	'order_by'			=>	array
					(	'owned_id'			=>	''
					)
				)
			);

			$response['u'] = $all_tellers[$all_owners[$response['id']]]['user_name'];
			
			
			//END DUCT TAPE BULLSHIT//////////////////////////
			
			
		endif;
	endif;
endif;

