<?php

$response = check_required_vars
(	array
	(	'expected_vars'	=>	array
		(	'user_name'
		,	'video_link'
		)
	)
); 
if	(	!$response
	):

	$response = 
	$video = 
	$errors = 
	array()
	;

	// GET THE YOUTUBE VIDEO ID FROM THE URL THEY PASTED IN
		// TO REFORM SIMPLEST POSSIBLE URL FROM VIDEO CODE
	if	(	preg_match
			(	'/(?:(?<=[\?&]v=)|(?<=\/v\/))[\w_-]+/'
			,	$GLOBALS['page']->request['video_link']
			,	$video_code_match
			)
		):
		$video['video_code']	= $video_code_match[0];
		
		// CHECK DATABASE FOR DUPLICATE VIDEO
		$video_blow = $GLOBALS['dbi']->get_result_array
		(	array
			(	'table'				=>	'story_video'
			,	'fields'			=>	array
				(	'id'
				)
			,	'equals'			=>	array
				(	'video_code'		=>	$video['video_code']
				,	'status'			=>	'Active'
				)
			,	'count_only'		=>	1
			)
		);
		if	(	!empty
				(	$video_blow
				)
			&&	!in_array
				(	$_SERVER['SERVER_NAME']
				,	array
					(	'bench.addisoninteractive.com'
					,	'localhost'
					)
				)
			):
			$errors['video_link'] = 'video_already_submitted';
		else:
		
			// VERIFY THAT VIDEO EXISTS BY RETRIEVING XML AND THUMBNAILS
			$xml = curlit
			(	'https://gdata.youtube.com/feeds/api/videos/'
				.	$video['video_code']
				.	'?v=2'
			);
			if	(	empty
					(	$xml	
					)
				):
				$errors['video_link'] = 'video_cannot_be_verified';
			else:
				$feed = new sxe
				(	$xml
				);

				// VERIFY THAT VIDEO EMBED INTO SITE IS ALLOWED
				$yt = $feed->data->children($feed->namespaces['yt']);
				$video['embed_allowed'] = 0;
				foreach
					(	$yt->accessControl as $ac
					):
					$attr = $ac->attributes();
					if	(	$attr->action		==	'embed'
						&&	$attr->permission	==	'allowed'
						):
						$video['embed_allowed'] = 1;
						break;
					endif;
				endforeach;
				if	(	empty
						(	$video['embed_allowed']
						)	
					):
					$errors['video_link'] = 'video_cannot_be_embedded';
				else:
				
					// GET VIDEO TITLE, DEFAULT THUMBNAIL IMAGE, AND MEDIA DESCRIPTION
					$media = $feed->data->children($feed->namespaces['media']);  
					$video['description'] = (string)$media->group->description;
					
					$attr = $media->group->thumbnail[0]->attributes();
					if	(	strpos
							(	$attr->url
							,	'0'
							)	!==	false
						):
						$video['thumbnail_url'] = (string)$attr->url;
					endif;
					if	(	empty
							(	$video['thumbnail_url']
							)
						):
						$video['thumbnail_url'] = '//i.ytimg.com/vi/'
						.	$video['video_code']
						.	'/0.jpg'
						;
					endif;

					$video['video_title'] = 
					(	empty
						(	$feed->data->title
						)
					)
					?	(string)$media->group->title
					:	(string)$feed->data->title
					;
					if	(	empty
							(	$video['video_title']
							)
						):
						$video['video_title'] = '[Untitled]';
					endif;
				endif;
			endif;
		endif;
	else:
		$errors['video_link'] = 'invalid_youtube_url';
	endif;
	if	(	!empty
			(	$video
			)
		):
		$response['video'] = $video;
	endif;

endif;
