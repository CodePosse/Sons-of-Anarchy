<?php

class arrays {
	
	public static function implode_safe
	(	$args	=	array()
	)
	{	extract
		(	debug::function_argument_verify
			(	array
				(	'function'			=>	__FUNCTION__
				,	'arguments_input'		=>	$args
				,	'arguments_descriptions'	=>	array
					(	'pieces'	=>	array
						(	'blurb'		=>	'The array whose elements are to be imploded.  Keys will be discarded / ignored, as with normal imploding.  If a string is submitted, the same string will be returned, avoiding the error you would normally get by trying to implode a string.'
						)
					,	'glue'			=>	array
						(	'blurb'				=>	'String to be placed between array elements in the returned result string.'
						,	'default_value'		=>	''
						)
					)
				)
			)
		);
		
		if	(	is_array
				(	$pieces	
				)
			):
			return implode
			(	$glue
			,	$pieces
			);
		else:
			return $pieces;
		endif;
	}
	
	public static function sort_by_strlen
	(	$args	=	array()
	)
	{	extract
		(	debug::function_argument_verify
			(	array
				(	'function'			=>	__FUNCTION__
				,	'arguments_input'		=>	$args
				,	'arguments_descriptions'	=>	array
					(	'array'				=>	array
						(	'blurb'				=>	'An array with string keys.'
						)
					,	'sort_by_key'		=>	array
						(	'possible_values'	=>	array
							(	0
							,	1
							)
						,	'default_value'		=>	0
						)
					,	'secondary_sort_functions'	=>	array
						(	'default_value'				=>	array
							(	
							)
						)
					,	'reverse'			=>	array
						(	'possible_values'	=>	array
							(	0
							,	1
							)
						,	'default_value'		=>	0
						)
					)
				)
			)
		);
		
		$sorted_array = 
		$sorted_by_length = 
		array()
		;
		
		if	(	count
				(	$array
				)
			):
			foreach
				(	$array	as	$key	=>	$val	
				):
				$item_length = 
				(	$sort_by_key
				)
				?	strlen
					(	$key
					)
				:	strlen
					(	$val
					)
				;
				if	(	empty
						(	$sorted_by_length[$item_length]
						)
					):
					$sorted_by_length[$item_length] = array();
				endif;
				$sorted_by_length[$item_length][$key] = $val;
			endforeach;
			reset
			(	$array
			);
		endif;
		
		if	(	$reverse
			):
			krsort
			(	$sorted_by_length
			);
		else:
			ksort
			(	$sorted_by_length
			);
		endif;
		
		if	(	empty
				(	$secondary_sort_functions
				)
			):
			$secondary_sort_functions = 
			(	$sort_by_key
			)
			?	array
				(	'ksort'
				)
			:	array
				(	'natcasesort'
				)
			;
		endif;
		
		foreach
			(	$sorted_by_length	as	$length	=>	&$item_ray
			):
			if	(	!empty
					(	$secondary_sort_functions
					)
				):
				foreach
					(	$secondary_sort_functions	as	$secondary_sort_function	):
					eval
					(	$secondary_sort_function
					.	'($item_ray);'
					);
				endforeach;
			endif;
			foreach
				(	$item_ray	as	$item_key	=>	$item_value	
				):
				$sorted_array[$item_key] = $item_value;
			endforeach;
		endforeach;	
		
		return $sorted_array;
	}
	
}
