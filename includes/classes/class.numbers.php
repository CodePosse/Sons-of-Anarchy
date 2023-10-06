<?php

class numbers {

	public static $kilobyte_base2 = 1024;			// pow(2,10);
	public static $megabyte_base2 = 1048576;		// pow(2,20);
	public static $gigabyte_base2 = 1073741824;		// pow(2,30);
	public static $terabyte_base2 = 1099511627776;	// pow(2,40);
	
	public static function clean
	(	$args	=	array()
	)
	{	extract
		(	debug::function_argument_verify
			(	array
				(	'function'			=>	__FUNCTION__
				,	'arguments_input'		=>	$args
				,	'arguments_descriptions'	=>	array
					(	'number'				=>	array
						(	'blurb'				=>	'Number to strip non-numeric characters from.'
						)
					,	'keep_decimal_point'	=>	array
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
		
		if	(	is_array
				(	$number
				)
			):
			return false;
		else:
			$pattern = 
			(	$keep_decimal_point
			)
			?	'/[^0-9\.]/'
			:	'/[^0-9]/'
			;
			return preg_replace
			(	$pattern
			,	''
			,	$number
			);
		endif;
	}

	public static function truncate_decimal
	(	$args	=	array()
	)
	{	extract
		(	debug::function_argument_verify
			(	array
				(	'function'					=>	__FUNCTION__
				,	'arguments_input'			=>	$args
				,	'arguments_descriptions'	=>	array
					(	'decimal'					=>	array
						(	'blurb'						=>	'Decimal to strip trailing zeros from.'
						)
					)
				)
			)
		);
		
		/*
		if (strstr($decimal,'.') != false && substr($decimal,-1) == '0') {
			$truncat = 1;
			while ($truncat) {
				$test_dec = substr($decimal,-$truncat,1);
				if ($test_dec == '0' || $test_dec == '.') $truncat++;
				else {
					$decimal = substr($decimal,0,-$truncat);
					$truncat = 0;
				}
			}
		}
		*/
		return $decimal + 0;
	}
	
}

