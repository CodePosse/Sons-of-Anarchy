<?php

require_once
(	'includes/initialize_admin.php'
);

$bodyClass = basename
(	$_SERVER['PHP_SELF']
,	'.php'
);

$this_page_js = $bodyClass
.	'.js'
;
if	(	file_exists
		(	'scripts/'
			.	$this_page_js
		)
	&&	filesize
		(	'scripts/'
			.	$this_page_js
		)
	):
	$cfg['scripts']['src'][] = $this_page_js;
endif;

$ht = // ALL THE HTML CREATED DURING PHP PROCESSING
$ol = // PAGE-SPECIFIC OVERLAYS TO BE APPENDED TO THE HTML FOOTER
$js = // PAGE-SPECIFIC INLINE SCRIPTS TO BE APPENDED TO THE HTML FOOTER
''
;

$client = analyze_client();

// ACCURSED IE BROWSER DETECTION
if	(	stripos
		(	$_SERVER['HTTP_USER_AGENT']
		,	'msie'
		)	!==	false
	):
	$ie = substr
	(	stristr
		(	$_SERVER['HTTP_USER_AGENT']
		,	'msie'
		)
	,	5
	);
	$ie = substr
	(	$ie
	,	0
	,	strpos
		(	$ie
		,	'.'
		)
	);
else:
	$ie = false;
endif;
/*
// THIS SECTION OF CODE IS MEANT TO SPOOF FACEBOOK'S DEBUG PAGE WHEN COOKIE-BASED LOGIN IS REQUIRED FOR THIS SITE
$hedwig = getallheaders();
if	(	strstr
		(	$hedwig['User-Agent']
		,	'facebookexternalhit'
		)
	):
	$_COOKIE[$cfg['cookie']['co']['name']] = $cfg['cookie']['co']['default'];
	setcookie
	(	$cfg['cookie']['co']['name']
	,	$_COOKIE[$cfg['cookie']['co']['name']]
	,	(	time()
		+	(	60
			*	60
			*	24
			*	365
			)
		)
	,	'/'
	,	$GLOBALS['cfg']['cookie_domain']
	);

	$_COOKIE[$cfg['cookie']['ui']['name']] = $cfg['fb']['testId'];
	setcookie
	(	$cfg['cookie']['ui']['name']
	,	$_COOKIE[$cfg['cookie']['ui']['name']]
	,	(	time()
		+	(	60
			*	60
			*	24
			*	365
			)
		)
	,	'/'
	,	$GLOBALS['cfg']['cookie_domain']
	);
endif;
*/

if	(	empty
		(	$_COOKIE[$GLOBALS['cfg']['cookie']['co']['name']]
		)
	||	!empty
		(	$_GET[$GLOBALS['cfg']['cookie']['co']['name']]
		)
	):
	// IF NO COUNTRY CODE COOKIE VALUE PRESENT, CHECK $_GET AND $_POST FOR COUNTRY CODE
	if	(	empty
			(	$_GET[$GLOBALS['cfg']['cookie']['co']['name']]
			)
		):
		// IF NO COUNTRY CODE SUBMITTED, FOR NOW DEFAULT TO us
		// SHOULD/MIGHT EVENTUALLY REDIRECT TO COUNTRY SELECTOR
		$_GET[$GLOBALS['cfg']['cookie']['co']['name']] = 
		(	empty
			(	$_GET['co']
			)	
		)
		?	$GLOBALS['cfg']['cookie']['co']['default']
		:	$_GET['co']
		;
	endif;
	$_COOKIE[$GLOBALS['cfg']['cookie']['co']['name']] = strtolower
	(	$_GET[$GLOBALS['cfg']['cookie']['co']['name']]
	);
	if	(	co_verify
			(	$_COOKIE[$GLOBALS['cfg']['cookie']['co']['name']]
			)
		):
		if	(	empty
				(	$_GET['co']
				)
			):
			setcookie
			(	$GLOBALS['cfg']['cookie']['co']['name']
			,	$_COOKIE[$cfg['cookie']['co']['name']]
			,	(	time()
				+	(	60
					*	60
					*	24
					*	365
					)
				)
			,	'/'
			,	$GLOBALS['cfg']['cookie_domain']
			);
		endif;
	else:
		header
		(	'Location:'
			.	$_SERVER['SCRIPT_NAME']
		);
		exit;
	endif;
endif;

$GLOBALS['cfg']['loc_url'] = $GLOBALS['cfg']['home_url']
.	$_COOKIE[$GLOBALS['cfg']['cookie']['co']['name']]
.	'/'
;

if	(	(	(	$ie
			&&	$ie	<	$GLOBALS['cfg']['ie_min']
			)
		||	(	!co_verify
				(	$_COOKIE[$GLOBALS['cfg']['cookie']['co']['name']]
				)
			)
		)
	&&	strstr
		(	$_SERVER['SCRIPT_NAME']
		,	'nogo.php'
		)	!=	'nogo.php'
	):	
	// FOR IE VERSIONS $ie_min AND OLDER, REDIRECT TO FUCK YOU PAGE
	setcookie
	(	$GLOBALS['cfg']['cookie']['co']['name']
	,	''
	,	time()
		-	(	60
			*	60
			*	24
			*	90
			)
	,	'/'
	,	$GLOBALS['cfg']['cookie_domain']
	);
	header
	(	'Location: nogo.php'
	);
	exit;
else:
	foreach
		(	$GLOBALS['cfg']['co']['nogo']	as	$co_nogo	
		):
		if	(	$_COOKIE[$cfg['cookie']['co']['name']] == $co_nogo
			&&	strpos
				(	$_SERVER['SERVER_NAME']
				,	'bench.'
				)	===	false
			):
			header
			(	'Location:'
				.	$GLOBALS['cfg']['co']['nogo_gogo']
			);
			exit;
		endif;
	endforeach;
	require
	(	'includes/loc_require.php'
	);
		
endif;

if	(	$cfg['fb']
	):
	require_once
	(	$cfg['fb']['sdk']
	);
	$facebook = new Facebook
	(	array
		(	'appId'		=>	$cfg['fb']['appId'] 
		,	'secret'	=>	$cfg['fb']['secret']
		,	'cookie'	=>	true
		,
		)
	);
	$fb = array();
endif;

if	(	!empty
		(	$_REQUEST['no']
		)
	):
	$_COOKIE[$cfg['cookie']['no']['name']] = 1;
	setcookie
	(	$cfg['cookie']['no']['name']
	,	$_COOKIE[$cfg['cookie']['no']['name']]
	,	time()
		+	(	60
			*	60
			*	24
			)	
	,	'/'
	,	$GLOBALS['cfg']['cookie_domain']
	);
endif;
