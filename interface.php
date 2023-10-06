<?php
require_once
(	'includes/interface/interface_header.php'
);

$z_action_file = 'includes/interface/z_'
.	$GLOBALS['page']->request['z']
.	'.php'
;

if	(	file_exists
		(	$GLOBALS['page']->path['file_root']
			.	$z_action_file
		)	
	):
	
	require_once
	(	$z_action_file
	);

else:

	header
	(	'Location: index.php'
	);
	exit;

endif;

require_once
(	'includes/interface/interface_footer.php'
);

$GLOBALS['dbi']->kill
(	$GLOBALS['dbi']->thread_id
);
@$GLOBALS['dbi']->close();
