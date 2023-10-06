<?php

function expose_script
(	$file
,	$line
,	$modt	=	true
,	$kill	=	false
)
{	expose
	(	'<h1>FILE: '
		.	$file
		.	'<br/>LINE: '
		.	$line
		.	(	(	$modt	
				)
				?	'<br/>MODIFIED: '
					.	date
						(	'F d, Y H:i:s'
						,	filemtime
							(	$file
							)
						)
				:	''
			)
		.	'</h1>'
	);
	if	(	$kill
		):
		exit;
	endif;
}
/*
expose_script
(	__FILE__
,	__LINE__
);
*/

function expose
(	$var
)
{	print "\n\n"
	.	'<pre><div><blockquote>'
	;
	print_r
	(	$var
	);
	print '</blockquote></div></pre>'
	.	"\n\n"
	;
}

function __autoload
(	$class_name
)
{	return load
	(	$class_name
	);
}
spl_autoload_register
(	'__autoload'
);

function force_load
(	$class_name
)
{	return require_once
	(	$GLOBALS['class_path']
		.	$GLOBALS['class_file_prefix']
		.	$class_name
		.	'.php'
	);
	
	//	PHP BUG #31562: __autoload() problem with static variables
	//	
	//	Autoload is not invoked for missing class when using static variables
	//	(no other reference to the class in same file).
	//	
	//	http://bugs.php.net/bug.php?id=31562
	
}

function load
(	$class_name
)
{	$look_for_class = array
	(	$GLOBALS['class_path']
		.	$GLOBALS['class_file_prefix']
		.	$class_name
		.	'.php'
	);
	$looked_in = '';
	foreach
		(	$look_for_class as $lookie
		):
		$looked_in .= '<li>'
		.	$lookie
		.	'</li>'
		;
		$class_found = @include_once
		(	$lookie
		);
		if	(	$class_found
			):
			break;
		endif;
	endforeach;
	if	(	!$class_found
		):
/*
		echo '<pre><div>Class '
		.	$class_name
		.	' could not be found in the following locations:'
		.	$looked_in
		.	'</div><hr /><h2>PERFORMING ERROR BACKTRACE...</h2><hr /><br />'
		;
		$error_backtrace = debug_backtrace();
//		unset($error_backtrace[0]);
		debug::expose
		(	$error_backtrace
		);
		echo '<hr /></pre>';
		exit;
*/
		return false;
	else:
		return true;
	endif;
}

function return_bytes
(	$val
)
{	$val = trim
	(	$val
	);
    $last = strtolower
	(	$val[strlen($val)-1]
	);
    switch
		(	$last
		):
        // The 'G' modifier is available since PHP 5.1.0
        case 'g':
            $val *= 1024;
        case 'm':
            $val *= 1024;
        case 'k':
            $val *= 1024;
    endswitch;

    return $val;
}

function analyze_client()
{	$client_details = array
	(	'string'	=>	$_SERVER['HTTP_USER_AGENT']
	);
	
	$check_fors = array
	(	'android'
	,	'firefox'
	,	'ipad'
	,	'iphone'
	,	'ipod'
	,	'kindle'
	,	'macintosh'
	,	'msie'
	,	'chrome'
	,	'safari'
	,	'windows'
	,	'webkit'
	,	'chromeframe'
	);
	
	foreach
		(	$check_fors	as	$check_for
		):
		$client_details[$check_for] = preg_match
		(	'/'
			.	$check_for
			.	'/i'
		,	$client_details['string']
		);
	endforeach;

	$client_details['ios'] = 
	(	$client_details['iphone']
	||	$client_details['ipod']
	||	$client_details['ipad']
	)
	?	1
	:	0
	;

	$client_details['mobile'] = 
	(	preg_match
		(	'/mobile/i'
		,	$client_details['string']
		)
	||	$client_details['ios']
	||	$client_details['android']
	)
	?	1
	:	0
	;
	
	if	(	$client_details['msie']
		):
		$client_details['msie'] = substr
		(	stristr
			(	$client_details['string']
			,	'msie'
			)
		,	5
		);
		$client_details['msie'] = substr
		(	$client_details['msie']
		,	0
		,	strpos
			(	$client_details['msie']
			,	'.'
			)
		);
	endif;

	return $client_details;
	
}


// THESE NEED TO LIVE ELSEWHERE....

function scriptSrc
(	$src
)
{	if	(	stristr
			(	$src
			,	'http'
			)	==	$src
		||	strstr
			(	$src
			,	'//'
			)	==	$src
		):
		$script = $src;
		$use_stamp = false;
	else:
		$script = $GLOBALS['cfg']['home_url']
		.	'scripts/'
		.	$src
		;
		$use_stamp = true;
	endif;
	$script = '<script src="'
	.	$script
	;
	if	(	$use_stamp
		):
		$script .=
		(	strpos
			(	$src
			,	'?'
			)	===	false
		)
		?	'?'
		:	'&'
		;
		$script .= 'v='
		.	$GLOBALS['cfg']['stamp']
		;
	endif;
	$script .= '"></script>';
	return $script;
}

function ga_verify
(	$ga_id
)
{	return
	(	!empty
		(	$ga_id
		)
	&&	preg_match
		(	'/UA\-[\d]{5,8}\-[\d]{1,2}/'
		,	$ga_id
		)
	);
}

function co_verify
(	$co
)
{	return
	(	!empty
		(	$co
		)
	&&	preg_match
		(	'/^[a-z]{2}$/'
		,	$co
		)
	&&	is_dir
		(	$GLOBALS['page']->path['file_root']
			.	$co
		)
	);
}
