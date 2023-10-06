<?php

$cfg = array();

// DEFAULT TIMEZONE REQUIRED FOR PHP DATE FUNCTIONS
$cfg['default_timezone'] = 'America/Los_Angeles';
//$cfg['default_timezone'] = 'UTC';

ini_set
(	'date.timezone'
,	$cfg['default_timezone']
);

// APPLICATION CUSTOM VALUES

// GLOBAL COOKIE NAMES & DEFAULT VALUES
$cfg['cookie'] = array
(	'co'	=>	array	// COUNTRY CODE THAT DETERMINES WHICH LOCALIZED PATHS/FILES ARE LOADED/USED
	(	'name'		=>	'SOACO'
	,	'default'	=>	'ww'
	)
,	'ui'	=>	array	// END-USER ID (VALUE USUALLY MD5 ENCODED)
	(	'name'		=>	'SOAUI'
	,	'default'	=>	0
	)
,	'no'	=>	array	// IF TRUE, BLOCKS USERS FROM ACCESSING SITE (IF THEY ARE TOO YOUNG, ETC.)
	(	'name'		=>	'SOANO'
	,	'default'	=>	0
	)
);

// SESSION VALUES
// ANY NAME VALUES IN THE ABOVE cookie ARRAY SET HERE AS session KEYS WILL USE SESSIONS INSTEAD OF COOKIES TO MANAGE PERSISTENCE
$cfg['cookie_domain']	= '.'.$_SERVER['SERVER_NAME'];

// DEFAULT WAIT TIME FOR JS AJAX / PHP INTERFACE JSON REQUESTS
$cfg['wait_time'] = '900000';

// COUNTRY-SPECIFIC SETTINGS
$cfg['co'] = array
(	'nogo'	=>	array	// PROHIBIT VISITORS FROM A SPECIFIC COUNTRY OR COUNTRIES
	(	//	'us'
	)	// used to redirect US users
,	'nogo_gogo'	=>	'http://www.domain.com'	// URL REDIRECT DESTINATION FOR PROHIBITED USERS
);

$cfg['fb'] = 0; // SET TO TRUE TO INCLUDE FACEBOOK FUNCTIONALITY
$cfg['cf'] = 0; // SET TO TRUE TO INCLUDE GOOGLE CHROME FRAME FUNCTIONALITY FOR IE8
$cfg['ga'] = 1; // SET TO TRUE TO INCLUDE GOOGLE ANALYTICS FUNCTIONALITY

//$cfg['ga'] = array ();  // ARRAY OF GOOGLE ANALYTIC ACCOUNTS TO SEND DATA TO


// SHOW FUNCTION DEBUG INFO // INTERNAL DOCUMENTATION USEFUL WHEN CUSTOMIZING PHP CODE
// AUTOMATICALLY RUN IN DEV / DEBUG MODE WHEN SURFING FROM SPECIFIC IPs

$cfg['debug_from_ips']		=	array
(	array
	(	'ip'		=>	'216.14.14.194'
	,	'title'		=>	'Addison Interactive (External)'
	)
,	array
	(	'ip'		=>	'10.24.0.69'
	,	'title'		=>	'Addison Interactive (Internal)'
	)
);


// TO INSURE THAT ALL SITE FILES ARE LOADING PROPERLY, DEBUGGING IS TURNED ON BY DEFAULT
// THEN SWITCHED OFF IF $_SERVER['REMOTE_ADDR'] DOES NOT MATCH LOCALHOST OR ONE OF THE SUPPLIED/ADDED IPs
// USE THE FOLLOWING LINE TO FORCIBLY TURN OFF HIGHER LEVEL FUNCTION DEBUGGING FOR ALL IPs (ONCE YOUR SITE IS LIVE)
//debug::set_mode('off');
	
$cfg['site_title']			=	'Sons of Anarchy - Clubhouse Stories';
// site_title MAY BE ANY STRING // USED AS DEFAULT HTML PAGE TITLE
$cfg['company_title']		=	'Addison Interactive Inc.';
// COMPANY NAME THAT SHOWS ONLY IN FOOTER BAR

// SEED KEY FOR DATA ENCRYPTION
//
$cfg['crypt_key']			=	"Plant a good seed and you will joyfully gather fruit"; 
//
//	*	///////////////////		*
//	*	// W A R N I N G //		*
//	*	///////////////////		*
//
// DO NOT CHANGE THE ABOVE VALUE ONCE ENCRYPTED DATA HAS BEEN ENTERED INTO THE DB
// OR YOU  W I L L  N O T  BE ABLE TO DECRYPT IT.


// YOU MAY SPECIFY A DIFFERENT ENCRYPTION KEY FOR URL QUERY STRINGS
// IF SET TO EMPTY VALUE, URL QUERY STRINGS WILL NOT BE ENCRYPTED
//
//$cfg['url_crypt_key'] = '2UZVpKv280aF3nO233phr5NIohSM83wC';

// DATABASE CONNECTION INFO
switch
	(	$_SERVER['SERVER_NAME']
	):
	
	case 'bench.addisoninteractive.com':////////////////////////////////////////////////////////////////////////////////////////////////
	case 'soa.local.com':////////////////////////////////////////////////////////////////////////////////////////////////
	case 'localhost':////////////////////////////////////////////////////////////////////////////////////////////////
	
		error_reporting
		(	E_ALL
		);
		@ini_set
		(	'display_errors'
		,	'1'
		);
		
		
		if	(	strpos
				(	$_SERVER['SERVER_NAME']
				,	'local'
				)	!==	false
			):
			$cfg['db']			=	array
			(	'loc'				=>	'bench.addisoninteractive.com'
			,	'user'				=>	'mldbi'
			,	'pass'				=>	'RjrCwBRJmYaaHSJG'
			);
			$cfg['web_root']	=	
			(	strstr
				(	$_SERVER['SERVER_NAME']
				,	'local'
				)	==	$_SERVER['SERVER_NAME']
			)
			?	'soaclub/'
			:	''
			;
		else:
			$cfg['db']			=	array
			(	'loc'				=>	'localhost'
			,	'user'				=>	'bzdbi'
			,	'pass'				=>	'XtBNCmML6sJ5RNwS'
			);
			$cfg['web_root']	=	'soaclub/';
		endif;
		$cfg['db']['name']			=	'soa_club';
		$cfg['db']['default_sorts']	=	array
		(	'sort_order'			=>	''
		,	'updated'				=>	'DESC'
		);
				// WEB ROOT PATH FOR HTML & JAVASCRIPT USE
		$cfg['web_prot']			=	'http:';
		$cfg['admin_prot']			=	'http:';
//		$cfg['cdn_server']			=	$_SERVER['SERVER_NAME'];

		//facebook login
		if	(	$cfg['fb']
			):
			$cfg['fb'] = array
			(	'sdk'		=>	'includes/facebook/facebook.3.2.0.php'
			,	'appId'		=>	'459275177442252'
			,	'secret'	=>	'2ec18e0b65e13c959f4b66255c4dc0e7'
			,	'appUrl'	=>	''
			,	'pageUrl'	=>	''
			,	'testId'	=>	'67b428ed533a1acd6e6afea32f77d3c3'
			);
		endif;	
		
		//google analytics
		if	(	$cfg['ga']
			):
			$cfg['ga'] = array 
			(	'ai'	=>	'UA-17030402-01'
			);	//	UA-17030402-xx ai code
		endif;
		
		// REFRESH STAMPED ITEMS ONCE PER PAGE LOAD
		$cfg['stamp'] = rand()
		.	time()
		;

		break;

//
//	DEV / STAGING SERVER
//
	case 'dev.soaclubhousestories.com':////////////////////////////////////////////////////////////////////////////////////////////////
		
		error_reporting
		(	E_ALL
		);
		@ini_set
		(	'display_errors'
		,	'1'
		);
		
		$cfg['db']					=	array
		(	'loc'	=>	getenv('SOACLUBHOUSESTORIES_HOST')//'devdb.foxfilm.com'
		,	'name'	=>	getenv('SOACLUBHOUSESTORIES_DB')//'soastories'
		,	'user'	=>	getenv('SOACLUBHOUSESTORIES_USER')//'soastories'
		,	'pass'	=>	getenv('SOACLUBHOUSESTORIES_PASS')//'aQlH-h6Wa8'

		,	'default_sorts'	=>	array
			(	'sort_order'	=>	''
			,	'updated'		=>	'DESC'
			)
/*			
// UNCOMMMENT AND POPULATE THE FOLLOWING ONLY IF THE DATABASE CONNECTION REQUIRES SSL			
		,	'ssl'	=>	array
			(	'key'		=>	null // "/home/user/ssl/client-key.pem"
			,	'cert'		=>	null // "/home/user/ssl/client-cert.pem"
			,	'ca'		=>	"/etc/pki/tls/certs/mysql-ssl-ca-cert.pem"
			,	'capath'	=>	null // "/home/user/ssl"
			,	'cipher'	=>	null
			)
*/
		);	

		// WEB ROOT PATH FOR HTML & JAVASCRIPT USE
		$cfg['web_prot']			=	'http:';
		$cfg['web_root']			=	'';
		$cfg['admin_prot']			=	'http:';
//		$cfg['cdn_server']			=	'content.com';

		//facebook login
		if	(	$cfg['fb']
			&&	empty
				(	$cfg['fb']['appId']
				)
			):
			$cfg['fb'] = array
			(	'sdk'		=>	'includes/facebook/facebook.3.2.0.php'
			,	'appId'		=>	''
			,	'secret'	=>	''
			,	'appUrl'	=>	''
			,	'pageUrl'	=>	''
//			,	'testId'	=>	''
			);
		endif;
		
		//google analytics
		if	(	$cfg['ga']
			):
			$cfg['ga'] = array 
			(	'ai'	=>	'UA-17030402-01'
			);	//	client's code
		endif;

		// REFRESH STAMPED ITEMS ONCE PER PAGE LOAD
		$cfg['stamp'] = rand()
		.	time()
		;
	
		break;
//
//  LIVE SITE SERVERS/////////////////////////////////////////////////////////////////////////////////////////////////
//			
	default:	// case 'live':
	
		error_reporting
		(	E_ALL
		);
		@ini_set
		(	'display_errors'
		,	'1'
		);
		
		$cfg['db']					=	array
		(	'loc'	=>	getenv('SOACLUBHOUSESTORIES_HOST')//'db.main.foxfilm.com'
		,	'name'	=>	getenv('SOACLUBHOUSESTORIES_DB')//'soastories'
		,	'user'	=>	getenv('SOACLUBHOUSESTORIES_USER')//'soastories'
		,	'pass'	=>	getenv('SOACLUBHOUSESTORIES_PASS')//'beAdxj-9iV'

		,	'default_sorts'	=>	array
			(	'sort_order'	=>	''
			,	'updated'		=>	'DESC'
			)
/*			
// UNCOMMMENT AND POPULATE THE FOLLOWING ONLY IF THE DATABASE CONNECTION REQUIRES SSL			
		,	'ssl'	=>	array
			(	'key'		=>	null // "/home/user/ssl/client-key.pem"
			,	'cert'		=>	null // "/home/user/ssl/client-cert.pem"
			,	'ca'		=>	"/etc/pki/tls/certs/mysql-ssl-ca-cert.pem"
			,	'capath'	=>	null // "/home/user/ssl"
			,	'cipher'	=>	null
			)
*/
		);	
		// WEB ROOT PATH FOR HTML & JAVASCRIPT USE
		$cfg['web_prot']			=	'http:';
		$cfg['web_root']			=	'';
		$cfg['admin_prot']			=	'http:';
//		$cfg['cdn_server']			=	'content.com';

		//facebook login
		if	(	$cfg['fb']
			&&	empty
				(	$cfg['fb']['appId']
				)
			):
			$cfg['fb'] = array
			(	'sdk'		=>	'includes/facebook/facebook.3.2.0.php'
			,	'appId'		=>	''
			,	'secret'	=>	''
			,	'appUrl'	=>	''
			,	'pageUrl'	=>	''
//			,	'testId'	=>	''
			);
		endif;
		
		//google analytics
		if	(	$cfg['ga']
			&&	!is_array
				(	$cfg['ga']
				)
			):
			$cfg['ga'] = array 
			(	
			);	//	client's code
		endif;

		if	(	!empty
				(	$_SERVER['HTTP_X_FORWARDED_FOR']
				)
			&&	$_SERVER['HTTP_X_FORWARDED_FOR']	== '216.14.14.194'//<--worried about this
			):
			// REFRESH STAMPED ITEMS ONCE PER PAGE LOAD
			$cfg['stamp'] = rand()
			.	time()
			;
		else:
			// REFRESH STAMPED ITEMS ONCE PER HOUR
			$cfg['stamp'] = date
			(	'YmdH'
			);
		endif;

endswitch;

$cfg['site_server'] = '//'
.	str_replace
	(	'origin.'
	,	'www.'
	,	$_SERVER['SERVER_NAME']
	)
.	'/'
;
$cfg['home_aurl'] = $cfg['site_server']
.	$cfg['web_root']
;
$cfg['home_url'] = $cfg['web_prot']
.	$cfg['home_aurl']
;
if	(	empty
		(	$cfg['cdn_server']
		)
	):
	$cfg['cdn_path'] = $cfg['home_url'];
endif;
if	(	empty
		(	$cfg['interface']
		)
	):
	$cfg['interface'] = $cfg['home_url']
	.	'interface.php'
	;
endif;

$cfg['ie_min'] = 8;


// PATH VALUES

// SOMETIMES ENDS WITH SUBDIRS // SHOULD ALWAYS END WITH TRAILING SLASH

// FILE ROOT PATH FOR PHP USE
//$cfg['file_root']			=	'';
// IF LEFT EMPTY, AUTO-GENERATED FROM $_SERVER['DOCUMENT_ROOT'] . $cfg['web_root']

$cfg['login']				=	array
(	'method'	=>	'cookie'	//	'session'
,	'required'	=>	
	(	isset
		(	$require_login
		)
	)
	?	$require_login	//	can be adjusted on a page by page basis by setting $GLOBALS['require_login'] at top of page
	:	1	// 0/FALSE ALLOWS ACCESS TO ANY USER / 1/TRUE REQUIRES LOGIN ON ALL PAGES BY DEFAULT
,	'field'		=>	'username'	// users can alternately log in with 'email'
);

// THE FOLLOWING ADDITIONAL COOKIES BELONG TO THIS APP
// ONLY THESE AND THE LOGIN COOKIE WILL BE AFFECTED BY kill_cookie() FUNCTION
// LOGIN COOKIE IS NOT NEEDED IF NO SITE PAGES REQUIRE LOGIN
$cfg['cookies']				=	array
(	'login'		=>	array
	(	'expire_in_seconds'	=>	60*60*8	// HOW LONG LOGIN COOKIE REMAINS VALID
	,	'value'				=>	0	// LEAVE INITIAL VALUE AS ZERO, WILL BE "FILLED IN" WHEN USER SUCCESFULLY LOGS IN
	)
);

$cfg['language'] = 'english';

// ASSUMED DEFAULT STYLESHEET base.css ALREADY INCLUDED IN THE style_sheets ARRAY
$cfg['style_sheets']			=	array
(	'admin'
);
// LEAVE OFF .css EXTENSION

$cfg['input_sizes']	 		=	array
(	'default'			=>	45	// 
,	'filter'			=>	18	// FILTER INPUTS ARE USUALLY SMALLER THAN DEFAULT
,	'max_select_height'	=>	12	// LIMITS HEIGHT OF SELECT ELEMENTS // SCROLLBAR APPEARS IF NUMBER OF CHOICES EXCEEDS MAX
);

//$cfg['doc_type_definition']	=	'<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">';
//$cfg['content_type']			=	'text/html; charset=iso-8859-1';
$cfg['refresh']				=	
(	isset
	(	$refresh
	)
)
?	$refresh
:	array
	(	'seconds'		=>	0
	,	'url'			=>	''
	)
;
$cfg['robots']				=	'INDEX,NOFOLLOW,NOIMAGEINDEX,NOIMAGECLICK';
$cfg['shortcut_icon_url']	=	'';
$cfg['author']				=	'Billy Z Duke';
$cfg['description']			=	'';
$cfg['keywords']				=	'';
//$cfg['expires']			=	0;

//$cfg[n]['src'][]			=	''; // FILE BASENAME WITHOUT .js EXTENSION
//$cfg[n]['content'][]		=	''; // SCRIPT CONTENT TO BE DIRECTLY WRITTEN INTO PAGE

$cfg['mailer']				=	array
(	'host'			=>	'localhost'		// often 'localhost' or 'mail'
,	'mailer'		=>	'sendmail'		// only 'mail', 'sendmail' or 'smtp'
,	'html_format'	=>	1
,	'watchdogs'		=>	array
	(	array
		(	'address'		=>	'billy@addisoninteractive.com'
		,	'name'			=>	'Billy Z Duke'
		)
//	,	array
	)
);

$iv = ip2long
(	gethostbyname
	(	$_SERVER['SERVER_NAME']
	)
);
$iv .= $iv;
$cfg['encryption']			=	array
(	'algorithm'		=>	'rijndael-128'
,	'mode'			=>	'cbc'
,	'iniv'			=>	$iv	// MCRYPT_DEV_URANDOM
);


//THIS ARRAY LOADS JAVASCRIPT FILES AT THE BASE OF THE HTML CODE. ADD ADDIONAL SCRIPTS HERE
$cfg['scripts']				=	array
(	'src'		=>	array
	(	'vendor/jquery-1.8.3.min.js'	=>	'<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script><script>window.jQuery || document.write(\'<script src="scripts/vendor/jquery-1.8.3.min.js"><\\/script>\')</script>'
	,	'vendor/jquery.tools-1.2.6.min.js'
//	,	'vendor/md5.js'  ONLY NEEDED IF PERFORMING MD5 HASHING IN JAVASCRIPT
	,	'vendor/jquery.cookie.js'
	,	'vendor/jquery.masonry.min.js'
//	,	'vendor/PxLoader.js'
//	,	'vendor/PxLoaderImage.js'
//	,	$cfg['sandworm_script'] //configures sandworm
//	,	'vendor/jquery-ui-1.10.3.custom.min.js'
	,	'plugins.js' // ALL SCRIPTS AFTER THIS ONE WILL BE INCLUDED AFTER global.js AND INLINE JAVASCRIPT IN HTML FOOTER
	)
,	'functions'	=>	array
	(
	)
,	'ready'		=>	array
	(
	)	
);
