<?php

class encryption {

	public static function my_crypt
	(	$args	=	array()
	)
	{	extract
		(	debug::function_argument_verify
			(	array
				(	'function'			=>	__FUNCTION__
				,	'arguments_input'		=>	$args
				,	'arguments_descriptions'	=>	array
					(	'data'				=>	array
						(	'blurb'				=>	'String to process.'
						)
					,	'key'				=>	array
						(	'blurb'				=>	'String encryption key.'
						)
					,	'encrypt'				=>	array
						(	'blurb'				=>	'Whether to encrypt or decrypt.'
						,	'possible_values'		=>	array
							(	array
								(	'possible_value'	=>	0
								,	'blurb'				=>	'Decrypt'
								)
							,	array
								(	'possible_value'	=>	1
								,	'blurb'				=>	'Encrypt'
								)
							)
						,	'default_value'		=>	1
						)
					,	'enctype'			=>	array
						(	'blurb'				=>	'Encryption method'
						,	'possible_values'		=>	array
							(	array
								(	'possible_value'	=>	'M'
								,	'blurb'				=>	'PHP mcrypt extension'
								)
							,	array
								(	'possible_value'	=>	'Z'
								,	'blurb'				=>	'Base64 encode'
								)
							)
						,	'default_value'		=>	'M'
						)
					,	'algorithm'			=>	array
						(	'blurb'				=>	'Initialization vector'
						,	'default_value'		=>	$GLOBALS['cfg']['encryption']['algorithm']
						)
					,	'mode'				=>	array
						(	'blurb'				=>	'Initialization vector'
						,	'default_value'		=>	$GLOBALS['cfg']['encryption']['mode']
						)
					,	'iniv'				=>	array
						(	'blurb'				=>	'Initialization vector'
						,	'default_value'		=>	$GLOBALS['cfg']['encryption']['iniv']
						)
					,	'test'				=>	array
						(	'blurb'				=>	'Display debugging information about the encryption process.'
						,	'possible_values'		=>	array
							(	0
							,	1
							)
						,	'default_value'		=>	0
						)
					)
				)
			)
		);
		
		if	(	empty
				(	$data
				)
			):
			return '';
		else:
			$original_data = $data;
			$ext_req = 'mcrypt';
			$ext_ext = 
			(	1
			)
			?	'dll'
			:	PHP_SHLIP_SUFFIX
			;	// WHAT ABOUT THIS FOR LINUX AND .so ?????
			if	(	$encrypt
				):
				$de_cryp_type = $enctype;
			else:
				$de_cryp_type = substr
				(	$data
				,	-1
				);
				$data = substr
				(	$data
				,	0
				,	-1
				);
			endif;
			if	(	$de_cryp_type == 'M'
				):
				if	(	!debug::verify_extension
						(	$ext_req
						)
					):
					if	(	$encrypt
						):
						$de_cryp_type = 'Z';
					else:
						die
						(	'Cannot perform decryption: PHP mcrypt extension not loaded.'
						);
					endif;
				endif;
			endif;
			switch
				(	$de_cryp_type
				):
				case 'M':
					$good_string = 0;
					$bad_chars = array
					(	'\\'
					);
					while
						(	!$good_string
						):
				//		set_time_limit(30);
						
						$algos = mcrypt_list_algorithms();
						if	(	!in_array
								(	$algorithm
								,	$algos
								)
							):
							echo '<hr />Mcrypt algorithm "'
							.	$algorithm
							.	'" not available.<hr /><h2>PERFORMING ERROR BACKTRACE...</h2><hr /><br />';
							$error_backtrace = debug_backtrace();
//							unset($error_backtrace[0]);
							debug::expose
							(	$error_backtrace
							);
							echo '<hr />';
							exit;
						endif;
						$modes = mcrypt_list_modes();
						if	(	!in_array
								(	$mode
								,	$modes
								)
							):
							echo '<hr />Mcrypt mode "'
							.	$mode
							.	'" not available.<hr /><h2>PERFORMING ERROR BACKTRACE...</h2><hr /><br />';
							$error_backtrace = debug_backtrace();
//							unset($error_backtrace[0]);
							debug::expose
							(	$error_backtrace
							);
							echo '<hr />';
							exit;
						endif;
						
						// Open the cipher
						$td = mcrypt_module_open
						(	$algorithm
						,	''
						,	$mode
						,	''
						);
						
						// Determine the IV size
						$vs = mcrypt_enc_get_iv_size
						(	$td
						);
						if	(	$test
							):
							echo 'IV Size: $vs = '
							.	$vs
							.	'<br />'
							.	"\n\n"
							;
						endif;
						
						if	(	$encrypt
							):
							// Create the IV
							$iv = mcrypt_create_iv
							(	$vs
							,	$iniv
							);
						else:
							if	(	$test
								):
								echo '$data [base64_encoded($iv.mcrypted($data))] = '
								.	$data
								.	'<br />'
								.	"\n\n"
								;
							endif;
							$data = base64_decode
							(	$data
							);
							if	(	$test
								):
								echo '$data [$iv.mcrypted($data))] = '
								.	$data
								.	'<br />'
								.	"\n\n"
								;
							endif;
							// Extract the IV from $data
							$iv = substr
							(	$data
							,	0
							,	$vs
							);
							$data = substr
							(	$data
							,	$vs
							);
							if	(	$test
								):
								echo 'mcrypted $data = '
								.	$data
								.	'<br />'
								.	"\n\n"
								;
							endif;
						endif;
						if	(	$test
							):
							echo '$iv = '
							.	$iv
							.	' | '
							.	strlen
								(	$iv
								)
							.	'<br />'
							.	"\n\n"
							;
						endif;
						
						// Determine the keysize length
						$ks = mcrypt_enc_get_key_size
						(	$td
						);
						if	(	$test
							):
							echo 'key_size = '
							.	$ks
							.	'<br />'
							.	"\n\n"
							;
						endif;
						
						// Create key
						$ke = substr
						(	md5
							(	$key
							)
						,	0
						,	$ks
						);
						if	(	$test
							):
							echo '$ke = '
							.	$ke
							.	'<br />'
							.	"\n\n"
							;
						endif;
						
						// Initialize encryption module
						mcrypt_generic_init
						(	$td
						,	$ke
						,	$iv
						);
						if	(	strlen
								(	$iv
								)	!=	$vs
							):
							echo '<hr />Mcrypt module failed to initialize.<hr /><h2>PERFORMING ERROR BACKTRACE...</h2><hr /><br />';
							$error_backtrace = debug_backtrace();
//							unset($error_backtrace[0]);
							debug::expose
							(	$error_backtrace
							);
							echo '<hr />';
							exit;
						endif;
						
						$contains_bad_chars = 0;
						if	(	$encrypt
							):
							// Encrypt data
							$data = $iv
							.	mcrypt_generic
								(	$td
								,	$data
								)
							;
							if	(	$test
								):
								echo '$data [$iv.mcrypted($data))] = '
								.	$data
								.	'<br />'
								.	"\n\n"
								;
							endif;
							$crypt_data = base64_encode
							(	$data
							);
							if	(	$test
								):
								echo '$crypt_data [base64_encoded($iv.mcrypted($data))] = '
								.	$crypt_data
								.	'<br />'
								.	"\n\n"
								;
							endif;
							$chars = preg_split
							(	'//'
							,	$crypt_data
							,	-1
							,	PREG_SPLIT_NO_EMPTY
							);
							
							// Make sure there are no bad characters
							foreach
								(	$bad_chars	as	$key	=>	$bad_char
								):
								if	(	in_array
										(	$bad_char
										,	$chars
										)
									):
									$contains_bad_chars = 1;
									break;
								endif;
							endforeach;
							$crypt_data .= 'M';
						else:
							// Decrypt encrypted string
							$crypt_data = mdecrypt_generic
							(	$td
							,	$data
							);
							if	(	$test
								):
								echo 'decrypted $crypt_data = '
								.	$crypt_data
								.	'<br />'
								.	"\n\n"
								;
							endif;
						endif;
						$good_string = 
						(	$contains_bad_chars
						)
						?	0
						:	1
						;
						
						// Terminate encryption handler
						mcrypt_generic_deinit
						(	$td
						);
						
						// Close module
						mcrypt_module_close
						(	$td
						);
						
						if	(	$test
							&&	!$good_string
							):
							echo 'bad characters detected - try again...<br />'
							.	"\n\n"
							;
						endif;
					endwhile;
					return trim
					(	$crypt_data
					);
					break;
				case 'Z':
					return self::z_crypt
					(	array
						(	'data'		=>	$data
						,	'key'		=>	$key
						,	'encrypt'	=>	$encrypt
						)
					);
					break;
				default:
					return $original_data;
/*
					trigger_error('unknown $de_cryp_type of "'.$de_cryp_type.'" in function my_crypt()');
					echo '<h2>PERFORMING ERROR BACKTRACE...</h2><hr /><br />';
					$error_backtrace = debug_backtrace();
//					unset($error_backtrace[0]);
					debug::expose
					(	$error_backtrace
					);
					echo '<hr />';
*/
			endswitch;
		endif;
	}
	
	private static function z_crypt
	(	$args	=	array()
	)
	{	extract
		(	debug::function_argument_verify
			(	array
				(	'function'			=>	__FUNCTION__
				,	'arguments_input'		=>	$args
				,	'arguments_descriptions'	=>	array
					(	'data'				=>	array
						(	'blurb'				=>	'String to process.'
						)
					,	'encrypt'				=>	array
						(	'blurb'				=>	'Whether to encrypt or decrypt.'
						,	'possible_values'		=>	array
							(	array
								(	'possible_value'	=>	0
								,	'blurb'			=>	'Decrypt'
								)
							,	array
								(	'possible_value'	=>	1
								,	'blurb'			=>	'Encrypt'
								)
							)
						,	'default_value'		=>	1
						)
					)
				)
			)
		);
		
		if	(	empty
				(	$data
				)
			):
			return '';
		else:
			if	(	$encrypt
				):
				/* Encrypt data */
				$crypt_data = base64_encode
				(	$data
				)
				.	'Z'
				;
			else:
				/* Decrypt encrypted string */
				$crypt_data = base64_decode
				(	substr
					(	$data
					,	0
					,	-1
					)
				);
			endif;
			return trim
			(	$crypt_data
			);
		endif;
	}

}
