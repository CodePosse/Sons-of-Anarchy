<?php

class variables {

	public static function english_to_boolean
	(	$args	=	array()
	)
	{	extract
		(	debug::function_argument_verify
			(	array
				(	'function'			=>	__FUNCTION__
				,	'arguments_input'		=>	$args
				,	'arguments_descriptions'	=>	array
					(	'variable'
					)
				)
			)
		);
		
		return
		(	!empty
			(	$variable
			)
		&&	strtolower
			(	$variable
			)
			==	'yes'
		)
		?	(bool) 1
		:	(bool) 0
		;
	}
	
	public static function re_scope_variables
	(	$args	=	array()
	)
	{	extract
		(	debug::function_argument_verify
			(	array
				(	'function'			=>	__FUNCTION__
				,	'arguments_input'		=>	$args
				,	'arguments_descriptions'	=>	array
					(	'scopes'				=>	array
						(	'blurb'				=>	'String names of scopes to use.'
						)
					,	'new_scope'			=>	array
						(	'default_value'		=>	'GLOBALS'
						)
					)
				)
			)
		);
		
		if	(	!is_array
				(	$scopes
				)
			):
			$scopes = array
			(	$scopes
			);
		endif;
		foreach
			(	$scopes	as	$scope
			):
			eval
			(	'$scope = $'
			.	$scope
			.	';'
			);
			foreach
				(	$scope	as	$var	=>	$val
				):
				if	(	!is_array
						(	$val
						)
					):
					$val = trim
					(	$val
					);
				endif;
				eval
				(	'$'
				.	$new_scope
				.	'[$var] = $val;'
				);
			endforeach;
		endforeach;
	}
	
	public static function unset_en_masse
	(	$args	=	array()
	)
	{	extract
		(	debug::function_argument_verify
			(	array
				(	'function'			=>	__FUNCTION__
				,	'arguments_input'		=>	$args
				,	'arguments_descriptions'	=>	array
					(	'unsettees'			=>	array
						(	'blurb'				=>	'An array containing string names of variables to be unset en masse.'
						)
					,	'pre_scoper'			=>	array
						(	'blurb'				=>	'String to immediately proceed each $unsettee in unset() statement.'
						,	'default_value'		=>	''
						)
					,	'post_scoper'			=>	array
						(	'blurb'				=>	'String to immediately follow each $unsettee in unset() statement.'
						,	'default_value'		=>	''
						)
					)
				,	'return_description'	=>	'A single, properly formatted unset() statement to be evaluated as PHP upon return.'
				)
			)
		);
		
		$unsetter = 'unset(';
		foreach
			(	$unsettees	as	$unsettee
			):
			$unsetter .= $pre_scoper
			.	$unsettee
			.	$post_scoper
			.	','
			;
		endforeach;
		return substr
		(	$unsetter
		,	0
		,	-1
		)
		.	');'
		;
	}
	
}
