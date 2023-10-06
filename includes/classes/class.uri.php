<?php

class uri {

	public static function explode_query_string
	(	$args	=	array()
	)
	{	extract
		(	debug::function_argument_verify
			(	array
				(	'function'			=>	__CLASS__.'->'.__FUNCTION__
				,	'arguments_input'		=>	$args
				,	'arguments_descriptions'	=>	array
					(	'query_string'		=>	array
						(	'blurb'			=>	'URI query string, assuming question mark is not included.'
						)
					)
				)
			)
		);
		
		$exploded = array();
		$keys_and_vals = explode
		(	'&'
		,	$query_string
		);
		foreach
			(	$keys_and_vals	as	$key_and_val
			):
			$key_val_ray = explode
			(	'='
			,	$key_and_val
			);
			$exploded[$key_val_ray[0]] = urldecode
			(	$key_val_ray[1]
			);
		endforeach;
		return $exploded;
	}

	public static function generate
	(	$args	=	array()
	)
	{	extract
		(	debug::function_argument_verify
			(	array
				(	'function'			=>	__CLASS__.'->'.__FUNCTION__
				,	'function_description'	=>	'URI stands for Universal Resource Identifier and URL stands for Universal Resource Locator. Often times people use the terms interchangably, which is not entirely correct. A URL is a subset of the URI popular protocols. These are protocols (http://, ftp://, mailto:). Therefore all URLs are URIs. The term URL is deprecated and the more correct term URI is used in technical documentation. All URIs are means to access a resource on the Internet and are a a technical short hand used to link to the resource. URIs always designate a method to access the resource and designate the specific resource to be accessed.

-- http://www.bernzilla.com/item.php?id=100'
				,	'arguments_input'		=>	$args
				,	'arguments_descriptions'	=>	array
					(	'file_path'			=>	array
						(	'blurb'				=>	'The complete file path and/or filename.  Do not include beginning or ending slashes.'
						,	'default_value'		=>	$_SERVER['SCRIPT_NAME']
						)
					,	'scheme'			=>	array
						(	'blurb'				=>	'The scheme (also often referred to as the protocol) to be used for absolute URIs.
http://en.wikipedia.org/wiki/URI_scheme'
// HOW TO HANDLE SECURE STUFF ?  HERE OR OUTSIDE THIS FUNCTION, PRIOR TO PASSING IN ?????
						,	'possible_values'		=>	array
							(	'http'
							,	'https'
							,	'ftp'
							,	'ftps'
							,	'mailto'
							,	''
							)
						,	'default_value'		=>	'http'
						)
					,	'user'				=>	array
						(	'blurb'				=>	'When connecting to certain sites or schemes through a browser, such as FTP, usernames and/or passwords are sometimes required.'
						,	'default_value'		=>	''
						)
					,	'password'			=>	array
						(	'default_value'		=>	''
						)
					,	'host'				=>	array
						(	'blurb'				=>	'Host/domain name or IP addresss.  If left empty, $scheme, $user, $password and $port will be ignored and URI will be relative, beginning with $path.'
						,	'default_value'		=>	''
						)
					,	'port'				=>	array
						(	'blurb'				=>	'The port used to connect.  The http default port is 80, but as this is assumed when no port is included, the default port value for the purposes of this function is empty.'
						,	'default_value'		=>	''
						)
					,	'query'				=>	array
						(	'blurb'				=>	"Array containing query string values: // array('key'=>'value',etc.), or entire string (without opening question mark)."
						,	'default_value'		=>	0
						)
					,	'query_crypt_key'	=>	array
						(	'blurb'				=>	'If an encryption key is supplied here, the entire query string will be encrypted.'
						,	'default_value'		=>	0
						)
					,	'fragment'			=>	array
						(	'blurb'				=>	'Relocates to a named anchor tag within a target HTML page.  Appended to the end of the URL following a pound sign: #'
						,	'default_value'		=>	''
						)
					)
				)
			)
		);
		
		$loc = '';
		if	(	!empty
				(	$host
				)
			):
			if	(	!empty
					(	$scheme
					)
				):
				$loc .= $scheme
				.	'://'
				;
			endif;
			if	(	!empty
					(	$user
					)
				):
				$loc .= $user;
				if	(	!empty
						(	$password
						)
					):
					$loc .= ':'
					.	$password
					;
				endif;
				$loc .= '@';
			endif;
			$loc .= $host;
			if	(	!empty
					(	$port
					)
				):
				$loc .= ':'
				.	$port
				;
			endif;
			$loc .= '/';
		endif;
		$loc .= 
		(	!empty
			(	$loc
			)
		&&	$file_path == '.'
		)
		?	''
		:	$file_path
		;
		if	(	!empty
				(	$query
				)
			):
			$loc .= '?';
			if	(	is_array
					(	$query
					)
				):
				$query_string = '';
				foreach
					(	$query	as	$key	=>	$value	
					):
					$query_string .= 
						$key
					.	'='
					.	urlencode
						(	$value
						)
					.	'&'
					;
	//				utf8_decode() ?????
				endforeach;
				$query_string = substr
				(	$query_string
				,	0
				,	-1
				);
			else:
				$query_string = $query;
			endif;
			$loc .= 
			(	$query_crypt_key	
			)
			?	urlencode
				(	encryption::my_crypt
					(	array
						(	'data'	=>	$query_string
						,	'key'	=>	$query_crypt_key
						)
					)
				)
			:	$query_string
			;
		endif;
		if	(	!empty
				(	$fragment
				)
			):
			$loc .= '#'.$fragment;
		endif;
		
		return $loc;
	}
	
}