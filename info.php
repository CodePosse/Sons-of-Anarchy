<?php

require_once
(	'includes/initialize_site.php'
);

debug::expose
(	debug::get_mode()
);
exit;


debug::set_mode('on');
/*
debug::expose
(	$GLOBALS['dbi']->get_charset()
);
*/
	
	print '<pre>';
	
	print_r
	(	ini_get
		(	'date.timezone'
		)
	);
	
	print '<hr/>';

	print_r
	(	date
		(	'Y-m-d H:i:s'
		)
	);

	print '<hr/>';

	print_r
	(	$_SERVER
	);
	
	print '<hr/>';	

	$extensions_loaded = get_loaded_extensions();
	natcasesort
	(	$extensions_loaded
	);
	print_r
	(	$extensions_loaded
	);

	print '</pre>';


	phpinfo();
/*	
else:

	header
	(	'Location: /'
	);
	exit;
	
endif;
*/