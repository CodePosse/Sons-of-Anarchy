<?php

class validator {

	private static $error	=	array();
	private static $errors	=	array();
	
	public static function clear_errors()
	{
		self::$error	=	array();
		self::$errors	=	array();
	}
	
	private static function compile_errors
	(	$args	=	array()
	)
	{	extract
		(	debug::function_argument_verify
			(	array
				(	'function'			=>	__CLASS__.'->'.__FUNCTION__
				,	'arguments_input'		=>	$args
				,	'arguments_descriptions'	=>	array
					(	'function'
					,	'arguments'
					)
				)
			)
		);
		
		if	(	count
				(	self::$errors
				)
			):
			self::$error[$function]	=	array
			(	'arguments'		=>	$arguments
			,	'errors'		=>	array_unique
				(	self::$errors
				)
			);
			return 0;
		else:
			return 1;
		endif;
	}
	
	public static function contains_spaces
	(	$args	=	array()
	)
	{	extract
		(	debug::function_argument_verify
			(	array
				(	'function'			=>	__CLASS__.'->'.__FUNCTION__
				,	'arguments_input'		=>	$args
				,	'arguments_descriptions'	=>	array
					(	'text'
					)
				)
			)
		);
		
		return
		(	preg_match
			(	'/[ 	]/'
			,	$text
			)
		)
		?	true
		:	false
		;
	}
	
	public static function is_country_code
	(	$args	=	array()
	)
	{	extract
		(	debug::function_argument_verify
			(	array
				(	'function'			=>	__CLASS__.'->'.__FUNCTION__
				,	'function_description'	=>	'
Dotted quad IPAddress within valid range? true or false
Checks format, leading zeros, and values > 255
Does not check for reserved or unroutable IPs.'
				,	'arguments_input'		=>	$args
				,	'arguments_descriptions'	=>	array
					(	'country_code'
					)
				)
			)
		);
		
		$country_code = strtolower
		(	trim
			(	$country_code
			)
		);
		if	(	strlen
				(	$country_code
				)
				!=	2
			):
			self::$errors[] = 'Country code must be 2 characters long: "'
			.	$country_code
			.	'".'
			;
			return false;
		else:
			//	A (roughly) 3.1 Kbyte array
			$country_codes	=	array
			(	'ac'	=>	'Ascension Island'
			,	'ad'	=>	'Andorra'
			,	'ae'	=>	'United Arab Emirates'
			,	'af'	=>	'Afghanistan'
			,	'ag'	=>	'Antigua and Barbuda'
			,	'ai'	=>	'Anguilla'
			,	'al'	=>	'Albania'
			,	'am'	=>	'Armenia'
			,	'an'	=>	'Netherlands Antilles'
			,	'ao'	=>	'Angola'
			,	'aq'	=>	'Antarctica'
			,	'ar'	=>	'Argentina'
			,	'as'	=>	'American Samoa'
			,	'at'	=>	'Austria'
			,	'au'	=>	'Australia'
			,	'aw'	=>	'Aruba'
//			,	'ax'	=>	'Aland Islands'
			,	'az'	=>	'Azerbaijan'
			,	'ba'	=>	'Bosnia Hercegovina'
			,	'bb'	=>	'Barbados'
			,	'bd'	=>	'Bangladesh'
			,	'be'	=>	'Belgium'
			,	'bf'	=>	'Burkina Faso'
			,	'bg'	=>	'Bulgaria'
			,	'bh'	=>	'Bahrain'
			,	'bi'	=>	'Burundi'
			,	'bj'	=>	'Benin'
			,	'bm'	=>	'Bermuda'
			,	'bn'	=>	'Brunei Darussalam'
			,	'bo'	=>	'Bolivia'
			,	'br'	=>	'Brazil'
			,	'bs'	=>	'Bahamas'
			,	'bt'	=>	'Bhutan'
			,	'bv'	=>	'Bouvet Island'
			,	'bw'	=>	'Botswana'
			,	'by'	=>	'Belarus (Byelorussia)'
			,	'bz'	=>	'Belize'
			,	'ca'	=>	'Canada'
			,	'cc'	=>	'Cocos Islands'
			,	'cd'	=>	'Congo (The Democratic Republic of the)'
			,	'cf'	=>	'Central African Republic'
			,	'cg'	=>	'Congo'
			,	'ch'	=>	'Switzerland'
			,	'ci'	=>	'Ivory Coast'
			,	'ck'	=>	'Cook Islands'
			,	'cl'	=>	'Chile'
			,	'cm'	=>	'Cameroon'
			,	'cn'	=>	'China'
			,	'co'	=>	'Colombia'
			,	'cr'	=>	'Costa Rica'
//			,	'cs'	=>	'Serbia and Montenegro'
			,	'cu'	=>	'Cuba'
			,	'cv'	=>	'Cape Verde'
			,	'cx'	=>	'Christmas Island'
			,	'cy'	=>	'Cyprus'
			,	'cz'	=>	'Czech Republic'
			,	'de'	=>	'Germany'
			,	'dj'	=>	'Djibouti'
			,	'dk'	=>	'Denmark'
			,	'dm'	=>	'Dominica'
			,	'do'	=>	'Dominican Republic'
			,	'dz'	=>	'Algeria'
			,	'ec'	=>	'Ecuador'
			,	'ee'	=>	'Estonia'
			,	'eg'	=>	'Egypt'
//			,	'eh'	=>	'Western Sahara'
			,	'er'	=>	'Eritrea'
			,	'es'	=>	'Spain'
			,	'et'	=>	'Ethiopia'
			,	'eu'	=>	'European Union'
			,	'fi'	=>	'Finland'
			,	'fj'	=>	'Fiji'
			,	'fk'	=>	'Falkland Islands'
			,	'fm'	=>	'Micronesia'
			,	'fo'	=>	'Faroe Islands'
			,	'fr'	=>	'France'
//			,	'fx'	=>	'France (Metropolitan FX)'
			,	'ga'	=>	'Gabon'
			,	'gb'	=>	'United Kingdom (Great Britain)'
			,	'gd'	=>	'Grenada'
			,	'ge'	=>	'Georgia'
			,	'gf'	=>	'French Guiana'
			,	'gg'	=>	'Guernsey'
			,	'gh'	=>	'Ghana'
			,	'gi'	=>	'Gibraltar'
			,	'gl'	=>	'Greenland'
			,	'gm'	=>	'Gambia'
			,	'gn'	=>	'Guinea'
			,	'gp'	=>	'Guadeloupe'
			,	'gq'	=>	'Equatorial Guinea'
			,	'gr'	=>	'Greece'
			,	'gs'	=>	'South Georgia and the South Sandwich Islands'
			,	'gt'	=>	'Guatemala'
			,	'gu'	=>	'Guam'
			,	'gw'	=>	'Guinea-bissau'
			,	'gy'	=>	'Guyana'
			,	'hk'	=>	'Hong Kong'
			,	'hm'	=>	'Heard and McDonald Islands'
			,	'hn'	=>	'Honduras'
			,	'hr'	=>	'Croatia'
			,	'ht'	=>	'Haiti'
			,	'hu'	=>	'Hungary'
			,	'id'	=>	'Indonesia'
			,	'ie'	=>	'Ireland'
			,	'il'	=>	'Israel'
			,	'im'	=>	'Isle of Man'
			,	'in'	=>	'India'
			,	'io'	=>	'British Indian Ocean Territory'
			,	'iq'	=>	'Iraq'
			,	'ir'	=>	'Iran'
			,	'is'	=>	'Iceland'
			,	'it'	=>	'Italy'
			,	'je'	=>	'Jersey'
			,	'jm'	=>	'Jamaica'
			,	'jo'	=>	'Jordan'
			,	'jp'	=>	'Japan'
			,	'ke'	=>	'Kenya'
			,	'kg'	=>	'Kyrgyzstan'
			,	'kh'	=>	'Cambodia'
			,	'ki'	=>	'Kiribati'
			,	'km'	=>	'Comoros'
			,	'kn'	=>	'Saint Kitts and Nevis'
//			,	'kp'	=>	'North Korea'
			,	'kr'	=>	'South Korea'
			,	'kw'	=>	'Kuwait'
			,	'ky'	=>	'Cayman Islands'
			,	'kz'	=>	'Kazakhstan'
			,	'la'	=>	'Laos'
			,	'lb'	=>	'Lebanon'
			,	'lc'	=>	'Saint Lucia'
			,	'li'	=>	'Lichtenstein'
			,	'lk'	=>	'Sri Lanka'
			,	'lr'	=>	'Liberia'
			,	'ls'	=>	'Lesotho'
			,	'lt'	=>	'Lithuania'
			,	'lu'	=>	'Luxembourg'
			,	'lv'	=>	'Latvia'
			,	'ly'	=>	'Libya'
			,	'ma'	=>	'Morocco'
			,	'mc'	=>	'Monaco'
			,	'md'	=>	'Moldova Republic'
			,	'mg'	=>	'Madagascar'
			,	'mh'	=>	'Marshall Islands'
			,	'mk'	=>	'Macedonia (The Former Yugoslav Republic of)'
			,	'ml'	=>	'Mali'
			,	'mm'	=>	'Myanmar'
			,	'mn'	=>	'Mongolia'
			,	'mo'	=>	'Macau'
			,	'mp'	=>	'Northern Mariana Islands'
			,	'mq'	=>	'Martinique'
			,	'mr'	=>	'Mauritania'
			,	'ms'	=>	'Montserrat'
			,	'mt'	=>	'Malta'
			,	'mu'	=>	'Mauritius'
			,	'mv'	=>	'Maldives'
			,	'mw'	=>	'Malawi'
			,	'mx'	=>	'Mexico'
			,	'my'	=>	'Malaysia'
			,	'mz'	=>	'Mozambique'
			,	'na'	=>	'Namibia'
			,	'nc'	=>	'New Caledonia'
			,	'ne'	=>	'Niger'
			,	'nf'	=>	'Norfolk Island'
			,	'ng'	=>	'Nigeria'
			,	'ni'	=>	'Nicaragua'
			,	'nl'	=>	'Netherlands'
			,	'no'	=>	'Norway'
			,	'np'	=>	'Nepal'
			,	'nr'	=>	'Nauru'
//			,	'nt'	=>	'Neutral Zone'
			,	'nu'	=>	'Niue'
			,	'nz'	=>	'New Zealand'
			,	'om'	=>	'Oman'
			,	'pa'	=>	'Panama'
			,	'pe'	=>	'Peru'
			,	'pf'	=>	'French Polynesia'
			,	'pg'	=>	'Papua New Guinea'
			,	'ph'	=>	'Philippines'
			,	'pk'	=>	'Pakistan'
			,	'pl'	=>	'Poland'
			,	'pm'	=>	'St. Pierre and Miquelon'
			,	'pn'	=>	'Pitcairn'
			,	'pr'	=>	'Puerto Rico'
			,	'ps'	=>	'Palestinian Territories'
			,	'pt'	=>	'Portugal'
			,	'pw'	=>	'Palau'
			,	'py'	=>	'Paraguay'
			,	'qa'	=>	'Qatar'
			,	're'	=>	'Reunion'
			,	'ro'	=>	'Romania'
			,	'ru'	=>	'Russia'
			,	'rw'	=>	'Rwanda'
			,	'sa'	=>	'Saudi Arabia'
			,	'sb'	=>	'Solomon Islands'
			,	'sc'	=>	'Seychelles'
			,	'sd'	=>	'Sudan'
			,	'se'	=>	'Sweden'
			,	'sg'	=>	'Singapore'
			,	'sh'	=>	'St. Helena'
			,	'si'	=>	'Slovenia'
			,	'sj'	=>	'Svalbard and Jan Mayen Islands'
			,	'sk'	=>	'Slovakia (Slovak Republic)'
			,	'sl'	=>	'Sierra Leone'
			,	'sm'	=>	'San Marino'
			,	'sn'	=>	'Senegal'
			,	'so'	=>	'Somalia'
			,	'sr'	=>	'Suriname'
			,	'st'	=>	'Sao Tome and Principe'
			,	'su'	=>	'Soviet Union'
			,	'sv'	=>	'El Salvador'
			,	'sy'	=>	'Syria'
			,	'sz'	=>	'Swaziland'
			,	'tc'	=>	'Turks and Caicos Islands'
			,	'td'	=>	'Chad'
			,	'tf'	=>	'French Southern Territories'
			,	'tg'	=>	'Togo'
			,	'th'	=>	'Thailand'
			,	'tj'	=>	'Tajikistan'
			,	'tk'	=>	'Tokelau'
			,	'tl'	=>	'Timor-Leste'
			,	'tm'	=>	'Turkmenistan'
			,	'tn'	=>	'Tunisia'
			,	'to'	=>	'Tonga'
			,	'tp'	=>	'East Timor'
			,	'tr'	=>	'Turkey'
			,	'tt'	=>	'Trinidad and Tobago'
//			,	'tv'	=>	'Tuvalu'
			,	'tw'	=>	'Taiwan'
			,	'tz'	=>	'Tanzania'
			,	'ua'	=>	'Ukraine'
			,	'ug'	=>	'Uganda'
			,	'uk'	=>	'United Kingdom'
			,	'um'	=>	'United States Minor Islands'
			,	'us'	=>	'United States of America'
			,	'uy'	=>	'Uruguay'
			,	'uz'	=>	'Uzbekistan'
			,	'va'	=>	'Vatican City'
			,	'vc'	=>	'Saint Vincent (Grenadines)'
			,	've'	=>	'Venezuela'
			,	'vg'	=>	'Virgin Islands (British)'
			,	'vi'	=>	'Virgin Islands (USA)'
			,	'vn'	=>	'Viet Nam'
			,	'vu'	=>	'Vanuatu'
			,	'wf'	=>	'Wallis and Futuna Islands'
			,	'ws'	=>	'Samoa'
			,	'ye'	=>	'Yemen'
			,	'yt'	=>	'Mayotte'
			,	'yu'	=>	'Yugoslavia'
			,	'za'	=>	'South Africa'
			,	'zm'	=>	'Zambia'
//			,	'zr'	=>	'Zaire'
			,	'zw'	=>	'Zimbabwe'
			);
			
			if	(	isset
					(	$country_codes[$country_code]
					)
				):
				return $country_codes[$country_code];
			else:
				self::$errors[] = 'Invalid country code: "'
				.	$country_code
				.	'".'
				;
				return self::compile_errors
				(	array
					(	'function'	=>	__FUNCTION__
					,	'arguments'	=>	$args
					)
				);
			endif;
		endif;
	}
	
	public static function is_email_address
	(	$args	=	array()
	)
	{	extract
		(	debug::function_argument_verify
			(	array
				(	'function'			=>	__CLASS__.'->'.__FUNCTION__
				,	'function_description'	=>	'
Dotted quad IPAddress within valid range? true or false
Checks format, leading zeros, and values > 255
Does not check for reserved or unroutable IPs.'
				,	'arguments_input'		=>	$args
				,	'arguments_descriptions'	=>	array
					(	'email_address'
					)
				)
			)
		);
		
		/*
		if	(	!preg_match
				(	'([0-9a-zA-Z]([-.\w]*[0-9a-zA-Z])*@([0-9a-zA-Z][-\w]*[0-9a-zA-Z]\.)+[a-zA-Z]{2,9})'
				,	$email
				)
			):
		*/
		if	(	!is_string
				(	$email_address
				)
			):
			self::$errors[] = 'Non-string value submitted as $email_address.';
		else:
			if	(	!preg_match
					(	'/@/'
					,	$email_address
					)
				):
				self::$errors[] = 'No @ symbol in $email_address.';
			else:
				list
				(	$user
				,	$host
				)	=	explode
				(	'@'
				,	$email_address
				);
				if	(	empty
						(	$user
						)
					||	empty
						(	$host
						)
					):
					self::$errors[] = 'Missing data: ["'
					.	$user
					.	'"]@["'
					.	$host
					.	'"].'
					;
				else:
					if	(	self::contains_spaces
							(	$user
							)
						||	self::contains_spaces
							(	$host
							)
						):
						self::$errors[] = 'Whitespace in: ["'
						.	$user
						.	'"]@["'
						.	$host
						.	'"].'
						;
					else:
						if	(	!self::is_host
								(	$host
								)
							):
							self::$errors[] = 'Invalid host: "'
							.	$host
							.	'".'
							;
						endif;
					endif;
				endif;
			endif;
		endif;
		
		return self::compile_errors
		(	array
			(	'function'	=>	__FUNCTION__
			,	'arguments'	=>	$args
			)
		);
	}
	
	public static function is_host
	(	$args	=	array()
	)
	{	extract
		(	debug::function_argument_verify
			(	array
				(	'function'			=>	__CLASS__.'->'.__FUNCTION__
				,	'function_description'	=>	'
Dotted quad IPAddress within valid range? true or false
Checks format, leading zeros, and values > 255
Does not check for reserved or unroutable IPs.'
				,	'arguments_input'		=>	$args
				,	'arguments_descriptions'	=>	array
					(	'host_name'
					)
				)
			)
		);
		
		if	(	!self::is_host_name
				(	$host_name
				)
			):
			self::$errors[] = 'Invalid host name: "'
			.	$host_name
			.	'".'
			;
		else:
			if	(	!checkdnsrr
					(	$host_name
					,	'ANY'
					)
				):
				//	http://php.net/manual/en/function.checkdnsrr.php
				//	NOTE:	This function is not implemented on Windows platforms.
				//			Try the PEAR class Net_DNS.
				self::$errors[] = 'No DNS records for host name: "'
				.	$host_name
				.	'".'
				;
			endif;
		endif;
		
		return self::compile_errors
		(	array
			(	'function'	=>	__FUNCTION__
			,	'arguments'	=>	$args
			)
		);
	}
	
	private static function is_host_name
	(	$args	=	array()
	)
	{	extract
		(	debug::function_argument_verify
			(	array
				(	'function'			=>	__CLASS__.'->'.__FUNCTION__
				,	'function_description'	=>	'
Dotted quad IPAddress within valid range? true or false
Checks format, leading zeros, and values > 255
Does not check for reserved or unroutable IPs.'
				,	'arguments_input'		=>	$args
				,	'arguments_descriptions'	=>	array
					(	'host_name'
					)
				)
			)
		);
		
		// Only a-z, 0-9, and "-" or "." are permitted in a hostname
		// Patch for POSIX regex lib by Sascha Schumann sas@schell.de
		$bad_chars = preg_replace
		(	'/([-A-Za-z0-9\.]+)/'
		,	''
		,	$host_name
		);
		if	(	!empty
				(	$bad_chars
				)
			):
			self::$errors[] = 'Bad characters ('
			.	$bad_chars
			.	') in host name.'
			;
		else:
			if	(	preg_match
					(	'/\.\./'
					,	$host_name
					)
				||	preg_match
					(	'/^\./'
					,	$host_name
					)
				):
				self::$errors[] = 'Leading or double dot in host name: "'
				.	$host_name
				.	'".'
				;
			else:
				$chunks = explode
				(	'.'
				,	$host_name
				);
				$count = count
				(	$chunks
				)
				-	1
				;
				
				if	(	$count < 1
					||	gettype
						(	$chunks
						)
						!=	'array'
					):
					self::$errors[] = 'No dot separator in host name: "'
					.	$host_name
					.	'".'
					;
				else:
					
					// Bug that can't be killed without doing an is_host,
					// something.something will return TRUE, even if it's something
					// stupid like NS.SOMETHING (with no tld), because SOMETHING is
					// construed to BE the tld.  The is_bigfour and is_country
					// checks should help eliminate this inconsistancy. To really
					// be sure you've got a valid hostname, do an is_host() on it.
					
					// See if we're doing www.hostname.tld or hostname.tld
					$web = 
					(	preg_match
						(	'/^www\./i'
						,	$host_name
						)
					)
					?	1
					:	0
					;
					if	(	$web
						&&	$count < 2
						):
						self::$errors[] = 'Invalid web host name: "'
						.	$host_name
						.	'".'
						;
					else:
						$tld = $chunks[$count];
						if	(	empty
								(	$tld
								)
							):
							self::$errors[] = 'No top level domain found in host name: "'
							.	$host_name
							.	'".'
							;
						else:
							if	(	!self::is_top_level_domain
									(	$tld
									)
								):
								if	(	!self::is_country_code
										(	$tld
										)
									):
									self::$errors[] = 'Invalid top level domain in host name: "'
									.	$host_name
									.	'".'
									;
								endif;
							endif;
						endif;
					endif;
				endif;
			endif;
		endif;
		
		return self::compile_errors
		(	array
			(	'function'	=>	__FUNCTION__
			,	'arguments'	=>	$args
			)
		);
	}
	
	public static function is_ip
	(	$args	=	array()
	)
	{	extract
		(	debug::function_argument_verify
			(	array
				(	'function'			=>	__CLASS__.'->'.__FUNCTION__
				,	'function_description'	=>	'
Dotted quad IPAddress within valid range? true or false
Checks format, leading zeros, and values > 255
Does not check for reserved or unroutable IPs.'
				,	'arguments_input'		=>	$args
				,	'arguments_descriptions'	=>	array
					(	'ip'
					)
				)
			)
		);
		
		$ip_length = strlen($ip);
		if	(	$ip_length > 15
			):
			self::$errors[] = 'Too many ('.$ip_length.') characters in $ip.';
		endif;
		
		$bad_chars = preg_replace
		(	'/([0-9\.]+)/'
		,	''
		,	$ip
		);
		if	(	!empty($bad_chars)
			):
			self::$errors[] = 'Bad characters ('.$bad_chars.') in $ip.';
		endif;
		
		$chunks = explode('.',$ip);
		$count = count($chunks);
		
		if	(	$count != 4
			):
			self::$errors[] = '$ip not a dotted quad.';
		endif;
		
		foreach
			(	$chunks	as	$key	=>	$val
			):
/*
//	why is an ip invalid if segments are zero value?
			if	(	preg_match
					(	'/^0/'
					,	$val
					)
				):
				self::$errors[] = 'Invalid $ip segment: "'.$val.'".';
			endif;
*/
			settype
			(	$val
			,	'integer'
			);
			if	(	$val > 255
				):
				self::$errors[] = '$ip segment out of range: "'.$val.'".';
			endif;
		endforeach;
		
		return self::compile_errors
		(
			(	array
				(	'function'	=>	__FUNCTION__
				,	'arguments'	=>	$args
				)
			)
		);
	}
	
	public static function is_phone_number
	(	$args	=	array()
	)
	{	extract
		(	debug::function_argument_verify
			(	array
				(	'function'			=>	__CLASS__.'->'.__FUNCTION__
				,	'arguments_input'		=>	$args
				,	'arguments_descriptions'	=>	array
					(	'phone_number'
					)
				)
			)
		);
		
		// SHOULD NOT TEST THE STRING LENGTH OF THE SUBMITTED NUMBER HERE
		// LET THE VALIDATOR TABLE REGEX TESTS HANDLE THAT
		// THIS FUNCTION SHOULD TEST VALIDITY OF ACTUAL PHONE NUMBERS
		
			
		
		return self::compile_errors
		(
			(	array
				(	'function'	=>	__FUNCTION__
				,	'arguments'	=>	$args
				)
			)
		);
	}
	
	public static function is_top_level_domain
	(	$args	=	array()
	)
	{	extract
		(	debug::function_argument_verify
			(	array
				(	'function'			=>	__CLASS__.'->'.__FUNCTION__
				,	'function_description'	=>	'
Dotted quad IPAddress within valid range? true or false
Checks format, leading zeros, and values > 255
Does not check for reserved or unroutable IPs.'
				,	'arguments_input'		=>	$args
				,	'arguments_descriptions'	=>	array
					(	'top_level_domain'
					)
				)
			)
		);
		
		if	(	preg_match
				(	'/^\./'
				,	$top_level_domain
				)
			):
			$top_level_domain = preg_replace
			(	'/^\./'
			,	''
			,	$top_level_domain
			);
		endif;
		$top_level_domain = strtolower
		(	$top_level_domain
		);
		
		$non_country_code_tlds = array
		(	'aero'
		,	'arpa'
		,	'asia'
		,	'biz'
		,	'cat'
		,	'com'
		,	'coop'
		,	'edu'
		,	'gov'
		,	'info'
		,	'int'
		,	'jobs'
		,	'mil'
		,	'mobi'
		,	'museum'
		,	'name'
		,	'net'
		,	'org'
		,	'pro'
		,	'tel'
		,	'travel'
		,	'tv'
		);
		
		return
		(	in_array
			(	$top_level_domain
			,	$non_country_code_tlds
			)
		)
		?	true
		:	false
		;
	}

	public static function return_errors
	(	$args	=	array()
	)
	{	extract
		(	debug::function_argument_verify
			(	array
				(	'function'			=>	__CLASS__.'->'.__FUNCTION__
				,	'arguments_input'		=>	$args
				,	'arguments_descriptions'	=>	array
					(	'function_details'			=>	 array
						(	'possible_values'			=>	array
							(	0
							,	1
							)
						,	'default_value'				=>	0
						)
					,	'clear'						=>	 array
						(	'possible_values'			=>	array
							(	0
							,	1
							)
						,	'default_value'				=>	1
						)
					)
				)
			)
		);
		
		if	(	$function_details
			):
			$function_errors = self::$error;
		else:
			$function_errors = array();
			foreach
				(	self::$error	as	$function	=>	$function_detail
				):
				foreach
					(	$function_detail['errors']	as	$function_error
					):
					if	(	$function == 'validate'
						):
						debug::expose
						(	$function_error
						);
					else:
						$function_errors[] = $function_error;
					endif;
				endforeach;
			endforeach;
			reset
			(	self::$error
			);
		endif;
		if	(	$clear
			):
			self::clear_errors();
		endif;
		return $function_errors;
	}
	
	public static function validate
	(	$args	=	array()
	)
	{	extract
		(	debug::function_argument_verify
			(	array
				(	'function'			=>	__CLASS__.'->'.__FUNCTION__
				,	'arguments_input'		=>	$args
				,	'arguments_descriptions'	=>	array
					(	'function'
					,	'input'
					)
				)
			)
		);
		
		if	(	method_exists
				(	'validator'
				,	$function
				)
			):
			eval
			(	'$validated = self::'
			.	$function
			.	'($input);'
			);
			return $validated;
		else:
			self::$errors[] = 'validator method "'
			.	$function
			.	'()" does not exist.'
			;
			return self::compile_errors
			(	array
				(	'function'	=>	__FUNCTION__
				,	'arguments'	=>	$args
				)
			);
		endif;
	}
	
}
