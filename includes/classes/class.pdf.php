<?php

class pdf {

	public static function extract_text
	(	$args	=	array()
	)
	{	extract
		(	debug::function_argument_verify
			(	array
				(	'function'			=>	__FUNCTION__
				,	'arguments_input'		=>	$args
				,	'arguments_descriptions'	=>	array
					(	'postscript_data'
					)
				)
			)
		);
		
		$text_start = 0;
		$text_end = strpos
		(	$postscript_data
		,	')'
		,	$text_start
			+	1
		);
		$plain_text = '';
		while	
			(	$text_start = strpos
				(	$postscript_data
				,	'('
				,	$text_start
				)
			&&	$text_end = strpos
				(	$postscript_data
				,	')'
				,	$text_start
					+	1
				)
			&&	substr
				(	$postscript_data
				,	$text_end
					-	1
				)
				!=	'\\'
			):
			//	set_time_limit(30);
			$plain_text .= substr
			(	$postscript_data
			,	$text_start
				+	1
			,	$text_end
				-	$text_start
				-	1
			);
			if	(	substr
					(	$postscript_data
					,	$text_end
						+	1
					,	1
					)
					==	']'
				):
				// This adds quite some additional spaces between the words
				$plain_text .= ' ';
			endif;
			$text_start = 
			(	$text_start < $text_end
			)
			?	$text_end
			:	$text_start
				+	1
			;
		endwhile;
		return stripslashes
		(	$plain_text
		);
	}

	public static function to_text
	(	$args	=	array()
	)
	{	extract
		(	debug::function_argument_verify
			(	array
				(	'function'			=>	__FUNCTION__
				,	'arguments_input'		=>	$args
				,	'arguments_descriptions'	=>	array
					(	'content'
					,	'metadata'		=>	array
						(	'default_value'		=>	''
						)
					)
				)
			)
		);
		
		# Locate all text hidden within the stream and endstream tags
		$search_start = 'stream';
		$search_end = 'endstream';
		$extracted_text = '';
		
		$pos_1 = 0;
		$pos_2 = 0;
		$start_pos = 0;
		# Iterate through each stream block
		while 
			(	$pos_1 !== false
			&&	$pos_2 !== false
			):
			# Grab beginning and end tag locations if they have not yet been parsed
			$pos_1 = strpos
			(	$content
			,	$search_start
			,	$start_pos
			);
			$pos_2 = strpos
			(	$content
			,	$search_end
			,	$start_pos
				+	1
			);
			if	(	$pos_1 !== false
				&&	$pos_2 !== false
				):
				# Extract compressed text from between stream tags and uncompress
				$text_section = substr
				(	$content
				,	$pos_1
					+	strlen
						(	$search_start
						)
					+	2
				,	$pos_2
					-	$pos_1
					-	strlen
						(	$search_start
						)
					-	1
				);
				$text = @gzuncompress
				(	$text_section
				);
				if	(	!$text
					):
					//	debug::expose($metadata);
					//	debug::expose($textsection);
					return false;
				else:
					# Clean up text via a special function
					$text = self::extract_text
					(	$text
					);
					# Increase our PDF pointer past the section we just read
					$start_pos = $pos_2
					+	strlen
						(	$search_end
						)
					-	1
					;
					if	(	$text === false
						):
						return false;
					else:
						$extracted_text .= $text;
					endif;
				endif;
			endif;
		endwhile;
		return $extracted_text;
	}
	
}

