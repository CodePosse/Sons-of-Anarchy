<?php

class strings {

	public static function anchor_format
	(	$args	=	array()
	)
	{	extract
		(	debug::function_argument_verify
			(	array
				(	'function'			=>	__FUNCTION__
				,	'arguments_input'		=>	$args
				,	'arguments_descriptions'	=>	array
					(	'content'				=>	array
						(	'blurb'				=>	'Link text.'
						)
					,	'href'				=>	array
						(	'default_value'		=>	''
						)
					,	'target'				=>	array
						(	'blurb'				=>	'Anchor TARGET attribute value.'
						,	'default_value'		=>	''
						)
					)
				)
			)
		);
		
		if	(	empty
				(	$href
				)
			):
			$href = $content;
		endif;
		if	(	validator::is_email_address
				(	$href
				)
			):
			$href = 'mailto:'
			.	$href
			;
		endif;
		$attributes = array
		(	'HREF'	=>	$href
		);
		if	(	!empty
				(	$target
				)
			):
			$attributes['TARGET'] = $target;
		endif;
		return xhtml::element
		(	array
			(	'tag_name'	=>	'A'
			,	'attributes'	=>	$attributes
			,	'content'		=>	$content
			)
		);
	}
	
	public static function domain_name_by_levels
	(	$args	=	array()
	)
	{	extract
		(	debug::function_argument_verify
			(	array
				(	'function'			=>	__FUNCTION__
				,	'arguments_input'		=>	$args
				,	'arguments_descriptions'	=>	array
					(	'return_levels'	=>	array
						(	'blurb'				=>	'How many levels of domain name to return.  A value of 1 will return the top level domain only.  2 will return the second-level domain followed by the top level, etc...'
						,	'default_value'		=>	0
						)
					,	'domain_name'		=>	array
						(	'blurb'				=>	'String to process.'
						,	'default_value'		=>	strtolower
							(	$_SERVER['HTTP_HOST']
							)
						)
					)
				)
			)
		);
		
		if	(	$return_levels	
			):
			$leveled_domain_name = '';
			
			$levels = array_reverse
			(	explode
				(	'.'
				,	$domain_name
				)
			);
			
			for (	$l	=	0
				;	$l	<	$return_levels
				;	$l	++
				):
				if	(	empty
						(	$levels[$l]
						)
					):
					break;
				else:
					$leveled_domain_name = 
					(	empty
						(	$leveled_domain_name
						)
					)
					?	$levels[$l]
					:	$levels[$l]
						.	'.'
						.	$leveled_domain_name
					;
				endif;
			endfor;
			return $leveled_domain_name;
		else:
			return $domain_name;
		endif;
	}

	public static function label
	(	$args	=	array()
	)
	{	extract
		(	debug::function_argument_verify
			(	array
				(	'function'			=>	__FUNCTION__
				,	'arguments_input'		=>	$args
				,	'arguments_descriptions'	=>	array
					(	'label'			=>	array
						(	'blurb'			=>	'Text to transform / format as label.'
						)
					,	'lowercase_first'	=>	array
						(	'blurb'			=>	'If true, force input string to lowercase before processing label.'
						,	'possible_values'	=>	array
							(	0
							,	1
							)
						,	'default_value'	=>	0
						)
					)
				)
			)
		);
		
		if	(	strstr
				(	$label
				,	'_'
				)	!=	false
			):
			$label = str_replace
			(	'_'
			,	' '
			,	$label
			);
		endif;
		if	(	strstr
				(	$label
				,	'^'
				)	!=	false
			):
			$label = str_replace
			(	'^'
			,	'&#148;'
			,	$label
			);
		endif;
		if	(	$lowercase_first
			):
			$label = strtolower
			(	$label
			);
		endif;
		return trim
		(	UCwords
			(	$label
			)
		);
	}
	
	public static function midgify
	(	$args	=	array()
	)
	{	extract
		(	debug::function_argument_verify
			(	array
				(	'function'			=>	__FUNCTION__
				,	'arguments_input'		=>	$args
				,	'arguments_descriptions'	=>	array
					(	'string'		=>	array
						(	'blurb'			=>	'Text to midgify.'
						)
					)
				)
			)
		);
		
		return preg_replace
		(	'/[\n\t\r ]+/'
		,	' '
		,	$string
		);
	}

	public static function name_safe
	(	$args	=	array()
	)
	{	extract
		(	debug::function_argument_verify
			(	array
				(	'function'			=>	__FUNCTION__
				,	'arguments_input'		=>	$args
				,	'arguments_descriptions'	=>	array
					(	'string'		=>	array
						(	'blurb'			=>	'Text to transform / format as label.'
						)
					)
				)
			)
		);
		
		return preg_replace
		(	'/[- 	]/'
		,	'_'
		,	strtolower
			(	$string
			)
		);
	}
	
	public static function obscure
	(	$args	=	array()
	)
	{	extract
		(	debug::function_argument_verify
			(	array
				(	'function'			=>	__FUNCTION__
				,	'arguments_input'		=>	$args
				,	'arguments_descriptions'	=>	array
					(	'string'			=>	array
						(	'blurb'				=>	'String to process.'
						)
					,	'reveal_characters'	=>	array
						(	'blurb'				=>	'How much of the right end of the string will remain visible.'
						,	'default_value'		=>	4
						)
					,	'mask_character'	=>	array
						(	'blurb'				=>	'Character to mask with.'
						,	'default_value'		=>	'*'
						)
					)
				)
			)
		);
		
		// ADD OPTION TO MASK BEGINNING OR ENDING CHARACTERS ???
		
		$obscured = '';
		switch
			(	$reveal_characters	
			):
			case 0: // MASK ALL CHARACTERS
				$obscured .= preg_replace
				(	'/[^'
					.	$mask_character
					.	']/'
				,	$mask_character
				,	$string
				);
				break;
			default: // MASK ALL CHARACTERS EXCEPT LAST $reveal_characters CHARACTERS
				$obscure = strlen
				(	$string
				)
				-	$reveal_characters
				;
				for (	$x = 0
					;	$x < $obscure
					;	$x++
					):
					$obscured .= $mask_character;
				endfor;
				$obscured .= substr
				(	$string
				,	$obscure
				);
		endswitch;
		return $obscured;
	}
	
	public static function phone_format
	(	$args	=	array()
	)
	{	extract
		(	debug::function_argument_verify
			(	array
				(	'function'			=>	__FUNCTION__
				,	'arguments_input'		=>	$args
				,	'arguments_descriptions'	=>	array
					(	'phone_number'			=>	array
						(	'blurb'			=>	'Text to transform / format as phone number.'
						)
					,	'display_mask'			=>	array
						(	'default_value'			=>	'($area_code) $exchange-$number'
						)
					)
				)
			)
		);
		
		$number = numbers::clean
		(	array
			(	'number'				=>	$phone_number
			,	'keep_decimal_point'	=>	0
			)
		);
		
		if	(	empty
				(	$number
				)
			):
			return '';
		else:
			if	(	strstr
					(	$number
					,	'1'
					)	==	$number
				):
				$number = substr
				(	$number
				,	1
				);
			endif;
			
			if	(	empty
					(	$display_mask
					)
				||	!strstr
					(	$display_mask
					,	'$'
					)
				):
				$formatted = $number;
			else:
				if	(	strlen
						(	$number
						)	==	7
					):
					// PHONE NUMBER SANS AREA CODE
					$segments = array
					(	'exchange'	=>	substr
						(	$number
						,	0
						,	3
						)
					,	'number'	=>	substr
						(	$number
						,	3
						,	4
						)
					);
					$formatted = substr
					(	$display_mask
					,	strpos
						(	$display_mask
						,	'$exchange'
						)
					);
				else:
					// FULL PHONE NUMBER WITH AREA CODE
					$segments = array
					(	'area_code'	=>	substr
						(	$number
						,	0
						,	3
						)
					,	'exchange'	=>	substr
						(	$number
						,	3
						,	3
						)
					,	'number'	=>	substr
						(	$number
						,	6
						,	4
						)
					,	'extension'	=>	substr
						(	$number
						,	10
						)
					);
					if	(	!empty
							(	$segments['extension']
							)
						):
						$display_mask .= ' x$extension';
					endif;
					$formatted = $display_mask;
				endif;
				
				foreach
					(	$segments	as	$segment_name	=>	$segment_number
					):
					$formatted = str_replace
					(	'$'
						.	$segment_name
					,	$segment_number
					,	$formatted
					);
				endforeach;
			endif;
			return xhtml::element
			(	array
				(	'tag_name'		=>	'NOBR'
				,	'content'		=>	$formatted
				)
			);
		endif;
	}
	
	public static function pluralize
	(	$args	=	array()
	)
	{	extract
		(	debug::function_argument_verify
			(	array
				(	'function'			=>	__FUNCTION__
				,	'arguments_input'		=>	$args
				,	'arguments_descriptions'	=>	array
					(	'label'			=>	array
						(	'blurb'			=>	'Text to transform / format as plural.'
						)
					)
				)
			)
		);
		
		$last_chr = substr
		(	$label
		,	-1
		);
		$next2last_chr = substr
		(	$label
		,	-2
		,	1
		);
		$plural = $label;
		switch
			(	$last_chr
			):
			case 'y':
				// ENDING IN PRONOUNCED "Y"
				if	(	preg_match
						(	'/[^aeiou]/i'
						,	$next2last_chr
						)
					):
					$plural = substr
					(	$label
					,	0
					,	-1
					)
					.	'ie'
					;
				endif;
				break;
			case 'h':
				// ENDING IN PRONOUNCED "H"
				if	(	preg_match
						(	'/[^aeioudgkprt]/i'
						,	$next2last_chr
						)
					):
					$plural .= 'e';
				endif;
				break;
			case 's':
			case 'x':
			case 'z':
				// ENDING IN "S", "X" OR "Z"
				$plural .= 'e';
				break;
		endswitch;
		$plural .= 's';
		return $plural;
	}
	
	public static function replace_keys_with_values
	(	$args	=	array()
	)
	{	extract
		(	debug::function_argument_verify
			(	array
				(	'function'			=>	__FUNCTION__
				,	'arguments_input'		=>	$args
				,	'arguments_descriptions'	=>	array
					(	'template_string'			=>	array
						(	'blurb'						=>	'String containing dollar sign variable references.'
						)
					,	'values'					=>	array
						(	'blurb'						=>	'Associative array of values to be inserted into the template string.'
						)
					,	'key_delimiters'			=>	array
						(	'blurb'						=>	'A two item array.  The first item must not be empty, set to a string to look for prior to the key name in the template string.  The second delimiter can be left empty, but if supplied, will be considered the end of the key string.'
						,	'default_value'				=>	array
							(	'$'
							,	''
							)
						)
					)
				)
			)
		);
		
		$values = arrays::sort_by_strlen
		(	array
			(	'array'			=>	$values
			,	'sort_by_key'	=>	1
			,	'reverse'		=>	1
			)
		);
		
		$replaced_string = $template_string;
		
		foreach
			(	$values	as	$key	=>	$value
			):
			$delimited_key = $key_delimiters[0]
			.	$key
			.	$key_delimiters[1]
			;
			if	(	strstr
					(	$replaced_string
					,	$delimited_key
					)
				):
				$replaced_string = str_replace
				(	$delimited_key
				,	$value
				,	$replaced_string
				);
			endif;
		endforeach;
		reset
		(	$values
		);
		return $replaced_string;
	}
	
	public static function scour
	(	$args	=	array()
	)
	{	extract
		(	debug::function_argument_verify
			(	array
				(	'function'			=>	__FUNCTION__
				,	'arguments_input'		=>	$args
				,	'arguments_descriptions'	=>	array
					(	'input_string'				=>	array
						(	'blurb'						=>	'The string from which to strip certain characters.'
						)
					)
				)
			)
		);

		return 
		(	is_string
			(	$input_string
			)
		)
		?	preg_replace
			(	'/[<>\(\)=%\|&;\*\$\^\[\]\+\\\\\?\{\}]/'
	//			'/[<>\'"\(\)=%\|&;\*\$\^\[\]\+,\\\\\/\n\r\t\?\{\}]/'		
			,	''
			,	strip_tags
				(	$input_string
				)
			)
		:	$input_string
		;
	}
	
	public static function strip_chrs
	(	$args	=	array()
	)
	{	extract
		(	debug::function_argument_verify
			(	array
				(	'function'			=>	__FUNCTION__
				,	'arguments_input'		=>	$args
				,	'arguments_descriptions'	=>	array
					(	'from_string'			=>	array
						(	'blurb'				=>	'The string from which to strip certain characters.'
						)
					,	'line_feeds'		=>	array
						(	'possible_values'	=>	array
							(	0
							,	1
							)
						,	'default_value'	=>	1
						)
					,	'carriage_returns'	=>	array
						(	'possible_values'	=>	array
							(	0
							,	1
							)
						,	'default_value'	=>	1
						)
					,	'tabs'	=>	array
						(	'possible_values'	=>	array
							(	0
							,	1
							)
						,	'default_value'	=>	1
						)
					,	'spaces'	=>	array
						(	'possible_values'	=>	array
							(	0
							,	1
							)
						,	'default_value'	=>	1
						)
					,	'is_html'	=>	array
						(	'possible_values'	=>	array
							(	0
							,	1
							)
						,	'default_value'	=>	0
						)
					)
				)
			)
		);
		
		// preg_replace('/[ \t\r\n]+/',' ',$from_string);
		
		// LINE FEEDS
		if	(	$line_feeds
			):
			$from_string = str_replace
			(	chr
				(	10
				)
			,	' '
			,	$from_string
			);
		endif;
		// CARRIAGE RETURNS
		if	(	$carriage_returns
			):
			$from_string = str_replace
			(	chr
				(	13
				)
			,	' '
			,	$from_string
			);
		endif;
		// TABS
		if	(	$tabs
			):
			$from_string = preg_replace
			(	'/	+/'
			,	' '
			,	$from_string
			);
		endif;
		// SPACES
		if	(	$spaces
			):
			$from_string = preg_replace
			(	'/ +/'
			,	' '
			,	$from_string
			);
		endif;
		// JAVASCRIPT EXCEPTION
		if	(	$carriage_returns
			&&	$is_html
			):
			$from_string = str_replace
			(	' /*'
			,	chr
				(	13
				)
				.	'/*'
			,	$from_string
			);
			$from_string = str_replace
			(	'*/ '
			,	'*/'
				.	chr
					(	13
					)
			,	$from_string
			);
		endif;
		
		return $from_string;
	}
	
}
