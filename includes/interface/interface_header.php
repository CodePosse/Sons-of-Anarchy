<?php

// FOR DEBUGGING ONLY
error_reporting
(	E_ALL
);
@ini_set
(	'display_errors'
,	'1'
);
//////////////////////

$require_login = 0;

require_once
(	'includes/initialize_admin.php'
);

require_once
(	'includes/interface/interface_functions.php'
);

if	(	empty
		(	$response_format
		)
	):
	$response_format = 'jsonp';
endif;

$request_attributes = 
$errors = 
array()
;

foreach
	(	$GLOBALS['page']->request	as	$gprk	=>	$gprv
	):
	if	(	$gprk == 'fields'
		&&	is_array
			(	$gprv
			)
		):
		$GLOBALS['page']->request['fields'] = $gprv;
		foreach
			(	$GLOBALS['page']->request['fields']	as	$gprfk	=>	$gprfv
			):
			$GLOBALS['page']->request['fields'][$gprfk] = str_replace
			(	'\\'
			,	''
			,	$GLOBALS['dbi']->real_escape_string
				(	$gprfv
				)
			);
		endforeach;
		reset
		(	$GLOBALS['page']->request['fields'] 
		);
	else:
		if	(	strstr
				(	$gprk
				,	'fields%5B'
				)	==	$gprk
			):
			if	(	empty
					(	$GLOBALS['page']->request['fields']
					)
				):
				$GLOBALS['page']->request['fields'] = array();
			endif;
			$subkey = substr
			(	$gprk
			,	9
			,	-3
			);
			$GLOBALS['page']->request['fields'][$subkey] = str_replace
			(	'\\'
			,	''
			,	$GLOBALS['dbi']->real_escape_string
				(	$gprv
				)
			);
			unset
			(	$GLOBALS['page']->request[$gprk]
			);
		else:
			$GLOBALS['page']->request[$gprk] = str_replace
			(	'\\'
			,	''
			,	$GLOBALS['dbi']->real_escape_string
				(	$gprv
				)
			);
		endif;
	endif;
endforeach;
reset
(	$GLOBALS['page']->request
);	

// ALL email ENTRIES ALWAYS FORCED TO LOWERCASE
$force_lcase = array
(	'email'
//,	'user_name'
);
foreach
	(	$force_lcase	as	$flc
	):
	if	(	!empty
			(	$GLOBALS['page']->request[$flc]
			)
		):
		$GLOBALS['page']->request[$flc] = strtolower
		(	$GLOBALS['page']->request[$flc]
		);
		if	(	$flc	==	'email'
			):
			$encrypted[$flc] = encryption::my_crypt
			(	array
				(	'data'		=>	$GLOBALS['page']->request[$flc]
				,	'key'		=>	$GLOBALS['page']->crypt_key
				,	'enctype'	=>	'Z'
				)
			);
		endif;
	endif;
endforeach;

