<?php

$response = array
(	'stories'	=>	array()
);

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

if	(	!debug::get_mode()
	):
	$GLOBALS['dbi']->tables['user']->get_ownerships();
	
	$eligible_approvers = $GLOBALS['dbi']->get_result_array
	(	array
		(	'table'				=>	'user'
		,	'fields'			=>	array
			(	'id'
			)
		,	'equals'			=>	array
			(	'status'			=>	'Active'
			)
		,	'owners'			=>	array
			(	$GLOBALS['dbi']->tables['user']->owners['user_role']	=>	array
				(	1
				)
			)
		)
	);
	
	$video_approvers = array
	(	$GLOBALS['dbi']->tables['story_video']->owners['approved_by']	=>	$eligible_approvers
	);
else:
	$video_approvers = array();
endif;

$approved_videos = $GLOBALS['dbi']->get_result_array
(	array
	(	'table'				=>	'story_video'
	,	'equals'			=>	array
		(	'status'			=>	'Active'
		)
	,	'owners'			=>	$video_approvers
	,	'order_by'			=>	array
		(	'updated'			=>	'DESC'
		)
	)
);

foreach
	(	$approved_videos	as	$approved_video
	):
	$response['stories'][$approved_video['updated'].' '.$approved_video['video_title']] = array
	(	'i'		=>	$approved_video['id']
	,	't'		=>	$approved_video['video_title']
	,	'vc'	=>	$approved_video['video_code']
	,	'th'	=>	$approved_video['thumbnail_url']
	,	'u'		=>	$all_tellers[$all_owners[$approved_video['id']]]['user_name']
	,	'd'		=>	$approved_video['description']
	);
endforeach;


if	(	!debug::get_mode()
	):
	$photo_approvers = array
	(	$GLOBALS['dbi']->tables['story_photo']->owners['approved_by']	=>	$eligible_approvers
	);
else:
	$photo_approvers = array();
endif;

$approved_photos = $GLOBALS['dbi']->get_result_array
(	array
	(	'table'				=>	'story_photo'
	,	'equals'			=>	array
		(	'status'			=>	'Active'
		)
	,	'owners'			=>	$photo_approvers
	,	'order_by'			=>	array
		(	'updated'			=>	'DESC'
		)
	)
);

foreach
	(	$approved_photos	as	$approved_photo
	):
	$response['stories'][$approved_photo['updated'].' '.$approved_photo['photo_title']] = array
	(	'i'		=>	$approved_photo['id']
	,	't'		=>	$approved_photo['photo_title']
	,	'src'	=>	$approved_photo['photo_url']
	,	'th'	=>	$approved_photo['thumbnail_url']
	,	'u'		=>	$all_tellers[$all_owners[$approved_photo['id']]]['user_name']
	,	'd'		=>	$approved_photo['description']
	);
endforeach;

if	(	!empty
		(	$response['stories']
		)
	):
	krsort
	(	$response['stories']
	);
	$response['stories'] = array_values
	(	$response['stories']
	);
endif;


