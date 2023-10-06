<?php

class date_time {

	public static function assemble
	(	$args	=	array()
	)
	{	extract
		(	debug::function_argument_verify
			(	array
				(	'function'			=>	__FUNCTION__
				,	'arguments_input'		=>	$args
				,	'arguments_descriptions'	=>	array
					(	'aggregate_value'
					)
				)
			)
		);
		
		$assembled_value = '';
		if	(	isset($aggregate_value['year'])
			&&	isset($aggregate_value['month'])
			&&	isset($aggregate_value['day'])
			):
			$assembled_value .= str_pad
			(	$aggregate_value['year']
			,	4
			,	'0'
			,	STR_PAD_LEFT
			)
			.	'-'
			.	str_pad
			(	$aggregate_value['month']
			,	2
			,	'0'
			,	STR_PAD_LEFT
			)
			.	'-'
			.	str_pad
			(	$aggregate_value['day']
			,	2
			,	'0'
			,	STR_PAD_LEFT
			)
			;
		endif;
		if	(	isset($aggregate_value['hour'])
			&&	isset($aggregate_value['minute'])
			&&	isset($aggregate_value['second'])
			):
			if	(	!empty($assembled_value)
				):
				$assembled_value .= ' ';
			endif;
			$assembled_value .= str_pad
			(	$aggregate_value['hour']
			,	2
			,	'0'
			,	STR_PAD_LEFT
			)
			.	':'
			.	str_pad
			(	$aggregate_value['minute']
			,	2
			,	'0'
			,	STR_PAD_LEFT
			)
			.	':'
			.	str_pad
			(	$aggregate_value['second']
			,	2
			,	'0'
			,	STR_PAD_LEFT
			)
			;
		endif;
		return $assembled_value;
	}
	
	public static function db_explode
	(	$args	=	array()
	)
	{	extract
		(	debug::function_argument_verify
			(	array
				(	'function'			=>	__FUNCTION__
				,	'arguments_input'		=>	$args
				,	'arguments_descriptions'	=>	array
					(	'field'
					,	'value'						=>	array
						(	'blurb'						=>	'Datetime in db native format.'
						)
					)
				)
			)
		);
		
		return array
		(	$field
		.	'_year'		=>	substr
			(	$value
			,	0
			,	4
			)
		,	$field
		.	'_month'	=>	substr
			(	$value
			,	5
			,	2
			)
		,	$field
		.	'_day'		=>	substr
			(	$value
			,	8
			,	2
			)
		,	$field
		.	'_hour'		=>	substr
			(	$value
			,	11
			,	2
			)
		,	$field
		.	'_minute'	=>	substr
			(	$value
			,	14
			,	2
			)
		,	$field
		.	'_second'	=>	substr
			(	$value
			,	17
			,	2
			)
		);
	}
	
	public static function get_micro_time
	(	$args	=	array()
	)
	{	extract
		(	debug::function_argument_verify
			(	array
				(	'function'			=>	__FUNCTION__
				,	'arguments_input'		=>	$args
				,	'arguments_descriptions'	=>	array
					(	'with_time'		=>	array
						(	'blurb'			=>	'True returns full Unix timestamp with microtime.'
						,	'default_value'	=>	0
						)
					)
				)
			)
		);
		
		if	(	$with_time
			):
			return
			(	microtime
				(	1
				)
			);
		else:
			list
			(	$usec
			,	$sec
			)
			=	explode
			(	' '
			,	microtime()
			); 
			return str_pad
			(	substr
				(	(float)$usec
				,	2
				)
			,	6
			,	'0'
			,	STR_PAD_RIGHT
			);
		endif;
	}
	
	public static function get_stamp()
	{	return self::get_micro_time()
		.	rand()
		;
	}
	
	public static function valid_date
	(	$args	=	array()
	)
	{	extract
		(	debug::function_argument_verify
			(	array
				(	'function'			=>	__FUNCTION__
				,	'arguments_input'		=>	$args
				,	'arguments_descriptions'	=>	array
					(	'year'		=>	array
						(	'default_value'	=>	0
						)
					,	'month'		=>	array
						(	'default_value'	=>	0
						)
					,	'day'		=>	array
						(	'default_value'	=>	0
						)
					,	'return_array'	=>	array
						(	'possible_values'	=>	array
							(	0
							,	1
							)
						,	'default_value'	=>	0
						)
					,	'return_corrected'	=>	array
						(	'possible_values'	=>	array
							(	0
							,	1
							)
						,	'default_value'	=>	1
						)
					)
				)
			)
		);
		
		if	(	!($year + 0
				)
			):
			$year = 
			(	$return_corrected
			)
			?	date
				(	'Y'
				)
			:	'0000'
			;
		endif;
		if	(	!($month + 0
				)
			):
			$month = 
			(	$return_corrected
			)
			?	1
			:	'00'
			;
		endif;
		if	(	!($day + 0
				)
			):
			$day = 
			(	$return_corrected
			)
			?	1
			:	'00'
			;
		endif;
		
		$valid_stamp = mktime
		(	0
		,	0
		,	0
		,	$month
		,	$day
		,	$year
		);
		
		if	(	date
				(	'Y-m-d'
				,	$valid_stamp
				)
				!=	self::assemble
				(	array
					(	'aggregate_value'	=>	array
						(	'year'	=>	$year
						,	'month'	=>	$month
						,	'day'	=>	$day
						)
					)
				)
			):
			$year = '0000';
			$month = '00';
			$day = '00';
		endif;
		
		if	(	$return_array
			):
			$vali_date = 
			(	$return_corrected
			)
			?	array
				(	'year'	=>	date
					(	'Y'
					,	$valid_stamp
					)
				,	'month'	=>	date
					(	'm'
					,	$valid_stamp
					)
				,	'day'	=>	date
					(	'd'
					,	$valid_stamp
					)
				)
			:	array
				(	'year'	=>	$year
				,	'month'	=>	$month
				,	'day'	=>	$day
				)
			;
		else:
			$vali_date = 
			(	$return_corrected
			)
			?	date
				(	'Y-m-d'
				,	$valid_stamp
				)
			:	self::assemble
				(	array
					(	'aggregate_value'	=>	array
						(	'year'	=>	$year
						,	'month'	=>	$month
						,	'day'	=>	$day
						)
					)
				)
			;
		endif;
		
		return $vali_date;
	}

}

