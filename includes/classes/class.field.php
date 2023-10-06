<?php

class field {

	function __construct
	(	$args	=	array()
	)
	{	extract
		(	debug::function_argument_verify
			(	array
				(	'function'			=>	__CLASS__.'->'.__FUNCTION__
				,	'arguments_input'		=>	$args
				,	'arguments_descriptions'	=>	array
					(	'fetch_field_object'		=>	array
						(	'default_value'				=>	array()
						)
					)
				)
			)
		);
		
		foreach
			(	$fetch_field_object as $key => $val
			):
			$this->$key = $val;
		endforeach;
	}
	
	function get_validators()
	{	$this->validators = $GLOBALS['dbi']->get_owned_records
		(	array
			(	'owners'	=>	array
				(	$GLOBALS['dbi']->tables['validator']->owners['associated_field']	=>	array
					(	$this->id
					)
				)
			,	'full_records'	=>	1
			)
		);
	}
	
	function is_datetime
	(	$args	=	array()
	)
	{	extract
		(	debug::function_argument_verify
			(	array
				(	'function'			=>	__CLASS__.'->'.__FUNCTION__
				,	'arguments_input'		=>	$args
				,	'arguments_descriptions'	=>	array
					(	'test_for'			=>	array
						(	'possible_values'		=>	array
							(	'date'
							,	'time'
							,	'either'
							,	'both'
							)
						,	'default_value'		=>	'both'
						)
					)
				)
			)
		);
		
		$is_date = 
		(	stristr
			(	$this->type
			,	'date'
			)
		||	stristr
			(	$this->type
			,	'timestamp'
			)
		)
		?	1
		:	0
		;
		
		$is_time = 
		(	stristr
			(	$this->type
			,	'time'
			)
		)
		?	1
		:	0
		;
		
		switch
			(	$test_for	
			):
			case 'date':
				return $is_date;
				break;
			case 'time':
				return $is_time;
				break;
			case 'either':
				return
				(	$is_date
				||	$is_time
				)
				?	1
				:	0
				;
				break;
			default: // both
				return
				(	$is_date
				&&	$is_time
				)
				?	1
				:	0
				;
		endswitch;
	}
	
	function is_enum()
	{	return 
		(	$this->type == 'ENUM'
		||	strstr
			(	$this->type_string
			,	'enum'
			)	==	$this->type_string
		)
		?	1
		:	0
		;
	}
	
	function is_text()
	{	return
		(	stristr
			(	$this->type
			,	'blob'
			)
		||	strstr
			(	$this->type_string
			,	'text'
			)
		)
		?	1
		:	0
		;
	}
	
	function name_contains_word
	(	$args	=	array()
	)
	{	extract
		(	debug::function_argument_verify
			(	array
				(	'function'			=>	__CLASS__.'->'.__FUNCTION__
				,	'arguments_input'		=>	$args
				,	'arguments_descriptions'	=>	array
					(	'word'
					)
				)
			)
		);
		
		$word = strtolower
		(	$word
		);
		$contains_word = 0;
		
		if	(	strstr
				(	$this->name
				,	'_'
				)
			):
			if	(	empty
					(	$this->name_words
					)
				):
				$this->name_words = explode
				(	'_'
				,	$this->name
				);
			endif;
			foreach
				(	$this->name_words as $a_word	
				):
				if	(	$word == $a_word
					):
					$contains_word = 1;
					break;
				endif;
			endforeach;
			reset
			(	$this->name_words
			);
		else:
			if	(	$this->name == $word
				):
				$contains_word = 1;
			endif;
		endif;
		
		return $contains_word;
	}
	
	function template
	(	$args	=	array()
	)
	{	extract
		(	debug::function_argument_verify
			(	array
				(	'function'			=>	__CLASS__.'->'.__FUNCTION__
				,	'arguments_input'		=>	$args
				,	'arguments_descriptions'	=>	array
					(	'form'				=>	array
						(	'blurb'				=>	'Name of the form to which this field template belongs.'
						)
					,	'type'				=> array
						(	'possible_values'		=>	array
							(	'table'
							,	'record'
							)
						,	'default_value'		=>	'record'
						)
					)
				)
			)
		);
		
		return new field_template
		(	array
			(	'form'	=>	$form
			,	'type'	=>	$type
			,	'field'	=>	&$this
			)
		);
	}
	
}
