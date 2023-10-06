<?php

error_reporting
(	E_ALL
);
@ini_set
(	'display_errors'
,	'1'
);

if	(	!isset
		(	$_REQUEST
		)
	):
	$_REQUEST = array_merge
	(	$_GET
	,	$_POST
	,	$_COOKIE
	);
endif;
if	(	!empty
		(	$_FILES
		)
	):
	$_REQUEST = array_merge
	(	$_REQUEST
	,	$_FILES
	);
endif;

/*
if	(	$_SERVER['REMOTE_ADDR']	!=	'66.92.217.130'
	):
	$_REQUEST['z'] = 'offline';
endif;
*/

if	(	!isset
		(	$_SERVER['SERVER_NAME']
		)
	):
	$_SERVER['SERVER_NAME'] = $_SERVER['HTTP_HOST'];
endif;

if	(	!isset
		(	$_SERVER['SCRIPT_URI']
		)
	):
	$_SERVER['SCRIPT_URI'] = 'http';
	if	(	!empty
			(	$_SERVER['HTTPS']
			)
		):
		$_SERVER['SCRIPT_URI'] .= 's';
	endif;
	$_SERVER['SCRIPT_URI'] .= '://'
	.	$_SERVER['SERVER_NAME']
	.	$_SERVER["SCRIPT_NAME"]
	;
endif;

/*
foreach
	(	$_REQUEST as $key => $val
	):
	$key_low = strtolower
	(	$key
	);
	if	(	$key != $key_low
		):
		$_REQUEST[$key_low] == $_REQUEST[$key];
		unset
		(	$_REQUEST[$key]
		);
	endif;
endforeach;
reset
(	$_REQUEST
);
ksort
(	$_REQUEST
);
*/

/*
if	(	substr
		(	$_SERVER['DOCUMENT_ROOT']
		,	-1
		)
		!=	'/'
	):
	$_SERVER['DOCUMENT_ROOT'] .= '/';
endif;
*/

$class_path = 'includes/classes/';
$class_file_prefix = 'class.';
$home_page_content = 'home.php';

require_once
(	'includes/functions.php'
);

if	(	ini_get
		(	'display_errors'
		)
	||	strstr
		(	$_SERVER['SERVER_NAME']
		,	'bench.'
		)	==	$_SERVER['SERVER_NAME']
	||	strstr
		(	$_SERVER['SERVER_NAME']
		,	'linux.'
		)	==	$_SERVER['SERVER_NAME']
	||	(	!empty
			(	$_ENV['DEBUG_MODE']
			)
		&&	getenv
			(	'DEBUG_MODE'
			)	==	'on'
		)
		
	||	1	
		
	):
	debug::set_mode('on');
else:
	debug::set_mode('off');
endif;

require_once
(	'includes/config.php'
);

/*
foreach
	(	$cfg['debug_from_ips']	as	$debug_from_ip
	):
	debug::add_ip
	(	$debug_from_ip
	);
endforeach;
reset
(	$cfg['debug_from_ips']
);
if	(	in_array
		(	$_SERVER['SERVER_NAME']
		,	array
			(	'bench.addisoninteractive.com'
			)
		)
	||	$_SERVER['HTTP_X_FORWARDED_FOR']	==	'216.14.14.194'
	):
	debug::set_mode('on');	
else:
	debug::set_mode();
endif;
*/

if	(	!empty
		(	$cfg['db']
		)
	):
	// ESTABLISH DATABASE CONNECTION
	if	(	empty
			(	$cfg['db']['ssl']
			)
		):
		$GLOBALS['dbi'] = new dbi
		(	$cfg['db']['loc']
		,	$cfg['db']['user']
		,	$cfg['db']['pass']
		,	$cfg['db']['name']
		);
	else:
		// create a connection object which is not connected
		$GLOBALS['dbi'] = new dbi();
		$GLOBALS['dbi']->init();
		$GLOBALS['dbi']->ssl_set
		(	$cfg['db']['ssl']['key']		
		,	$cfg['db']['ssl']['cert']	
		,	$cfg['db']['ssl']['ca']		
		,	$cfg['db']['ssl']['capath']	
		,	$cfg['db']['ssl']['cipher']	
		);		
		// set connection options
		$GLOBALS['dbi']->options
		(	MYSQLI_INIT_COMMAND
		,	'SET AUTOCOMMIT=0'
		);
		$GLOBALS['dbi']->options
		(	MYSQLI_OPT_CONNECT_TIMEOUT
		,	5
		);
		//	connect to server
		$GLOBALS['dbi']->real_connect
		(	$cfg['db']['loc']
		,	$cfg['db']['user']
		,	$cfg['db']['pass']
		,	$cfg['db']['name']
		,	3306
		,	null
		,	MYSQLI_CLIENT_SSL
		); 
	endif;
	$GLOBALS['dbi']->set_charset('utf8');
	
	$GLOBALS['dbi']->get_tables();

	$user	=	new user();

else:

	$page	=	new page();

endif;


