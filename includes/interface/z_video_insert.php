<?php

$teller_fields = array
(	'user_name'
//	,	'date_of_birth'	
);
$story_fields = array
(	'video_title'
,	'video_code'
,	'thumbnail_url'
,	'description'
);

$response = check_required_vars
(	array
	(	'expected_vars'	=>	array_merge
		(	$teller_fields
		,	$story_fields
		)
	)
);
if	(	!$response
	):
				
	$teller_row = array
	(	'id'	=>	$GLOBALS['dbi']->get_result_array
		(	array
			(	'table'				=>	'story_teller'
			,	'fields'			=>	array
				(	'id'
				)
			,	'equals'			=>	array
				(	'user_name'			=>	$GLOBALS['page']->request['user_name']
				,	'status'			=>	'Active'
				)
			,	'limit_lo'			=>	1
			,	'pop_single_row'	=>	1
			)
		)
	);
	if	(	empty
			(	$teller_row['id']
			)
		):
		$teller_row['id'] = $GLOBALS['dbi']->random_id
		(	array
			(	'table'		=>	'story_teller'
//				,	'digits'	=>	12
			)
		);
		$teller_row['signup_ip'] = $_SERVER['REMOTE_ADDR'];
		$teller_row['signup_browser'] = $_SERVER['HTTP_USER_AGENT'];
	endif;
	foreach
		(	$teller_fields	as	$teller_field
		):
		$teller_row[$teller_field]	=	$GLOBALS['page']->request[$teller_field]; // addslashes() ???
	endforeach;
	while
		(	empty
			(	$db_did
			)
		):
		$db_did = 
		(	!empty
			(	$teller_row['signup_ip']
			)
		&&	!empty
			(	$teller_row['signup_browser']
			)
		)
		?	$GLOBALS['dbi']->insert_row
			(	array
				(	'table'	=>	'story_teller'
				,	'row'	=>	$teller_row
				)
			)
		:	$GLOBALS['dbi']->affect_rows
			(	array
				(	'table'	=>	'story_teller'
				,	'rows'	=>	array
					(	$teller_row['id']	=>	$teller_row
					)
				)
			)
		;
	endwhile;
	
	$story_row = array
	(	'id'	=>	$GLOBALS['dbi']->random_id
		(	array
			(	'table'		=>	'story_video'
//				,	'digits'	=>	12
			)
		)
	);
	foreach
		(	$story_fields	as	$story_field
		):
		$story_row[$story_field]	=	$GLOBALS['page']->request[$story_field]; // addslashes() ???
	endforeach;
	// REPLACE ALL NON-ASCII CHARACTERS
	// REPLACE ALL TABS, LINE BREAKS AND MULTIPLE CONSECUTIVE SPACES WITH A SINGLE SPACE
	// ENCODE ALL REMAINING HTML ENTITIES
	// YEAH, BITCH!
	$story_row['video_title'] = htmlentities
	(	preg_replace
		(	'/[ \t\n\r]+/'
		,	' '
		,	preg_replace
			(	'/[^(\x20-\x7F)]*/'
			,	''
			,	$story_row['video_title']
			)
		)
	);
	
	while
		(	empty
			(	$new_story
			)
		):
		$new_story = $GLOBALS['dbi']->insert_row
		(	array
			(	'table'	=>	'story_video'
			,	'row'	=>	$story_row
			)
		);
	endwhile;

	$GLOBALS['dbi']->tables['story_video']->get_ownerships();
					
	while
		(	!$GLOBALS['dbi']->insert_row
			(	array
				(	'table'	=>	'owned'
				,	'row'	=>	array
					(	'ownership_id'	=>	$GLOBALS['dbi']->tables['story_video']->owners['story_teller']
					,	'owner_id'		=>	$teller_row['id']
					,	'owned_id'		=>	$new_story
					)
				)
			)
		):
		continue;
	endwhile;
	
	$response = array
	(	'story'	=>	$new_story
	);
/*	
	if	(	!empty
			(	$response['story']
			)
		):							
		$responsys = curlit
		(	array
			(	'CURLOPT_URL'			=>	'https://foxus.rsys1.net/servlet/campaignrespondent'		
			,	'CURLOPT_POST'			=>	1
			,	'CURLOPT_POSTFIELDS'	=>	array
				(	'referringlink'			=>	'20121112_WrongTurn5'
				,	'REG_SOURCE'			=>	'20121112_WrongTurn5'
				,	'_ID_'					=>	'fxus.9114'
				,	'fox_all_newsletter'	=>	'no' // or 'yes'
				,	'email'					=>	substr
					(	$GLOBALS['page']->request['email']
					,	0
					,	50
					)
				,	'first_name'			=>	$user_row['first_name']
				,	'last_name'				=>	$user_row['last_name']
//						,	'display_name'			=>	$user_row['display_name']
//						,	'video_link'			=>	'//youtube.com/watch?v='
//							.	$submission_row['video_code']
				)
			)
		);
	endif;
*/
endif;


