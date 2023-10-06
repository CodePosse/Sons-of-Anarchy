<?php

require_once
(	'includes/initialize_admin.php'
);

$GLOBALS['page']->open_html();

if	(	empty
		(	$GLOBALS['page']->request['z']
		)
	):
	require
	(	'admin/home.php'
	);
else:
	if	(	is_file
			(	$GLOBALS['page']->path['file_root']
				.	'admin/'
				.	$GLOBALS['page']->request['z']
				.	'.php'
			)
		&&	empty
			(	$GLOBALS['page']->request['zpass']
			)
		):
		include_once
		(	'admin/'
			.	$GLOBALS['page']->request['z']
			.	'.php'
		);
	else:
		include_once
		(	'admin/default.php'
		);
	endif;
endif;

$GLOBALS['page']->render_html();

$GLOBALS['dbi']->kill
(	$GLOBALS['dbi']->thread_id
);
$GLOBALS['dbi']->close();