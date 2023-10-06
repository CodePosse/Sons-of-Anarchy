<?php

$GLOBALS['dbi']->tables['story_photo']->permissions['create_records_if'] = 0;

$table_template['render_if_no_records'] = 1;

$table_template['header']['sorting'] = 1;
$table_template['header']['filtering'] = 
0
;

$table_template['pagination']['items_per_page'] = 100;
$table_template['pagination']['links_per_page'] = 10;

$table_template['columns'] = array
(	array
	(	'field'	=>	'photo_title'
	)
,	array
	(	'field'	=>	'photo_url'
	)
,	array
	(	'field'	=>	'thumbnail_url'
	)
,	array
	(	'field'	=>	'description'
	)
,	array
	(	'field'	=>	'status'
	)
,	array
	(	'field'		=>	'approved_by'
/*
	,	'template'	=>	'$approved_by'
	,	'filter'	=>	0
	,	'sort'	=>	0
*/
	)
,	array
	(	'field'	=>	'updated'
	)
);

