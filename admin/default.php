<?php

if	(	is_file
		(	$GLOBALS['page']->path['file_root']
			.	$GLOBALS['page']->request['z']
			.	'.'
			.	$GLOBALS['page']->request['act']
			.	'.php'
		)
	):
	include_once
	(	$GLOBALS['page']->request['z']
		.	'.'
		.	$GLOBALS['page']->request['act']
		.	'.php'
	);
else:
	if	(	isset
			(	$GLOBALS['page']->request['id']
			)
		):
		if	(	!$GLOBALS['user']->is_admin()
			&&	$GLOBALS['page']->request['z'] == 'project'
			&&	$GLOBALS['page']->request['id']
			):
			include_once
			(	'comp_viewer.php'
			);
		else:			
			$GLOBALS['page']->render_records
			(	array
				(	'table'		=>	$GLOBALS['page']->request['z']
				,	'id'		=>	$GLOBALS['page']->request['id']
				,	'act'		=>	$GLOBALS['page']->request['act']
				)
			);
		endif;
	else:
		switch
			(	$GLOBALS['page']->request['act']	
			):
			case 'own':
				$GLOBALS['page']->own_records();
				break;
			case 'disown':
				$GLOBALS['page']->disown_records();
				break;
			case 'reorder':
				$re_ray = array
				(	'table'		=>	$GLOBALS['page']->request['z']
				);
				$re_rayers = array
				(	'in'
				,	'equals'
				,	'ownership_id'
				,	'owner_id'
				);
				foreach
					(	$re_rayers	as	$re_rayee	
					):
					if	(	!empty
							(	$GLOBALS['page']->request[$re_rayee]
							)
						):
						$re_ray[$re_rayee] = $GLOBALS['page']->request[$re_rayee];
					endif;
				endforeach;

				$GLOBALS['page']->reorder_records
				(	$re_ray
				);
				break;
			case 'view':
			default:
				// DISPLAY ALL ROWS IN TABLE
				$GLOBALS['page']->render_records
				(	array
					(	'table'		=>	$GLOBALS['page']->request['z']
//					,	'template'	=>	'compact'
					)
				);
		endswitch;
	endif;
endif;
