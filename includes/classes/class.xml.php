<?php

class xml {

	public static function element
	(	$args	=	array()
	)
	{	extract
		(	debug::function_argument_verify
			(	array
				(	'function'			=>	__CLASS__.'->'.__FUNCTION__
				,	'arguments_input'		=>	$args
				,	'arguments_descriptions'	=>	array
					(	'tag_name'
					,	'attributes'			=>	array
						(	'blurb'					=>	'array("{attribute_name}" => "{attribute_value}", etc.)'
						,	'default_value'			=>	0
						)
					,	'content'				=>	array
						(	'blurb'					=>	'Content of non-empty elements, may include other elements.'
						,	'default_value'			=>	''
						)
					,	'minimized_attributes'	=>	array
                    	(	'default_value'			=>	array()
                        )
                    ,	'empty_element'			=>	array
                    	(	'possible_values'		=>	array
							(	0
							,	1
							)
						,	'default_value'		=>	0
                        )
					,	'parse_content'			=>	array
                    	(	'possible_values'		=>	array
							(	0
							,	1
							)
						,	'default_value'		=>	1
                        )
					)
				)
			)
		);
		
		$tag_name = strtolower
		(	$tag_name
		);
		$element_code = '<'
		.	$tag_name
		;
		
		if	(	!empty
				(	$attributes
				)
			&&	is_array
				(	$attributes
				)
			):
			foreach (	$attributes	as	$attribute_name	=>	$attribute_value	
				):
				$attribute_name = strtolower
				(	$attribute_name
				);
				if	(	in_array
						(	$attribute_name
						,	$minimized_attributes
						)
					):
					$attribute_value = $attribute_name;
				endif;
				$element_code .= ' '
				.	strtolower
					(	$attribute_name
					)
				.	'="'
				.	$attribute_value
				.	'"'
				;
			endforeach;
		endif;
		
		if	(	!empty
				(	$content
				)
			&&	!$parse_content
			):
			$content = '<![CDATA['
			.	$content
			.	']]>'
			;
		endif;
		
		$element_code .=
		(	$empty_element
		)
		?	' />'
		:	'>'
			.	$content
			.	'</'
			.	$tag_name
			.	'>'
		;
		
		return $element_code;
	}
	
}