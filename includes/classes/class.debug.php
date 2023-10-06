<?php

class debug {

	public static $from_ips = array
	(	array
		(	'ip'		=>	'127.0.0.1'
		,	'title'		=>	'Localhost'
		)
	);
	private static $mode = 1;
	
	public static function add_ip
	(	$args	=	array()
	)
	{	extract
		(	self::function_argument_verify
			(	array
				(	'function'			=>	__CLASS__.'->'.__FUNCTION__
				,	'arguments_input'		=>	$args
				,	'arguments_descriptions'	=>	array
					(	'ip'
					,	'title'				=>	array
						(	'default_value'		=>	''
						)
					)
				)
			)
		);

		if	(	self::$mode
			):
			force_load
			(	'validator'
			);			
			if	(	validator::is_ip
					(	$ip	
					)
				):
				self::$from_ips[] = array
				(	'ip'		=>	$ip
				,	'title'		=>	$title
				);
				return 1;
			else:
				self::expose
				(	validator::$error
				);
				return 0;
			endif;
		else:
			return false;
		endif;
	}
	
	public static function expose
	(	$args	=	array()
	)
	{	extract
		(	self::function_argument_verify
			(	array
				(	'function'			=>	__FUNCTION__
				,	'arguments_input'		=>	$args
				,	'arguments_descriptions'	=>	array
					(	'data'				=>	array
						(	'blurb'				=>	'Variable or data to output to browser for debugging.'
//						,	'default_value'		=>	''
						)
					,	'value'				=>	array
						(	'blurb'				=>	'//	FINALLY
	// A WAY TO OUTPUT THE NAME OF THE VARIABLE BEING EXPOSED
		//	THIS REQUIRES THAT THE $data ARGUMENT VALUE BE A STRING
		//	EQUAL TO THE VARIABLE NAME SANS DOLLAR SIGN // DIDN\'T IT START OUT THIS WAY?
	//	IF A GLOBAL VARIABLE OF THAT NAME DOES NOT EXIST
	//	OR THE ARGUMENT VALUE IS A PASSED ARRAY OR OBJECT
	//	THE ARGUMENT VALUE IS EXPOSED WITHOUT A VARIABLE NAME, LIKE IT USED TO BE
		//	THIS IS TO ACCOMMODATE LEGACY CALLS TO THIS FUNCTION
		//	WHICH PASS ONLY THE VARIABLE VALUE INTO THE $data ARGUMENT
	
	//	BUT THERE ARE STILL TWO CIRCUMSTANCES WHICH WILL FUCK THINGS UP:
		//	1)	IF THE $data STRING VALUE EQUALS THE NAME OF ANOTHER EXISTING GLOBAL VARIABLE
			//	THE VALUE OF THE SECOND VARIABLE WILL BE EXPOSED, IN A MANNER SIMILAR TO $$var
		//	2)	WHEN ATTEMPTING TO EXPOSE A VARIABLE THAT EXISTS ONLY WITHIN THE SCOPE OF ANOTHER FUNCTION
			//	debug::expose() WILL STILL SEEK OUT THE NAMED VARIABLE IN THE $GLOBALS ARRAY
	//	SO, WHEN CALLING debug::expose() WITH A FUNCTION SCOPE VARIABLE
	//	OR WITH A VARIABLE WHOSE VALUES ARE LIKELY TO BE THE NAMES OF OTHER GLOBAL VARIABLES
	//	SET $value = 1 TO BYPASS THE FOLLOWING ATTEMPT TO DISPLAY THE VARIABLE NAME'
						,	'possible_values'		=>	array
							(	0
							,	1
							)
						,	'default_value'		=>	0
						)
					,	'return'				=>	array
						(	'blurb'				=>	'Return the exposed values or echo to browser.'
						,	'possible_values'		=>	array
							(	0
							,	1
							)
						,	'default_value'		=>	0
						)
					,	'sort_by_key'			=>	array
						(	'blurb'				=>	'Sort arrays by key when exposing.'
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
		
		if	(	self::$mode
			):
			$opener = "\n\n"
			.	'<pre><div style="border:dashed 1px red;background:white;color:black;font-weight:bold">'
			;
			$quoter = '|<blockquote>';
			$closer = '</blockquote>|</div></pre>'
			.	"\n\n"
			;
			if	(	$return
				):
				$exposed = $opener;
			else:
				print $opener;
			endif;
			
			if	(	!$value
				&&	!is_array
					(	$data
					)
				&&	!is_object
					(	$data
					)
				&&	isset
					(	$GLOBALS[$data]
					)
				):
				$ex_splay = '<h1>$'
				.	$data
				.	' = </h1>'
				;
				$data = $GLOBALS[$data];
				if	(	$return	
					):
					$exposed .= $ex_splay;
				else:
					print $ex_splay;
				endif;
			endif;
			if	(	$return	
				):
				$exposed .= $quoter;
			else:
				print $quoter;
			endif;
			if	(	is_object
					(	$data
					)
				):
				$class_name = get_class
				(	$data
				);
				$exposable = self::expose_class
				(	array
					(	'class_name'	=>	$class_name
					,	'return'		=>	1
					)
				)
				.	'<h2>'
				.	$class_name
				.	' object vars:</h2>'
				;
				if	(	$return	
					):
					$exposed .= $exposable
					.	print_r
						(	get_object_vars
							(	$data
							)
						,	1
						)
					;
				else:
					print $exposable;
					var_dump
					(	$data
					);
				endif;
			else:
				if	(	is_array
						(	$data
						)
					):
					if	(	$sort_by_key	
						):
						ksort
						(	$data
						);
					endif;
					if	(	$return	
						):
						$exposed .= print_r
						(	$data
						,	1
						);
					else:
						print_r
						(	$data
						);
					endif;
				else:
					if	(	$return	
						):
						$exposed .= $data;
					else:
						print $data;
					endif;
				endif;
			endif;
			if	(	$return	
				):
				return $exposed
				.	$closer
				;
			else:
				print $closer;
			endif;
		else:
			return false;
		endif;
	}

	public static function expose_class
	(	$args	=	array()
	)
	{	extract
		(	self::function_argument_verify
			(	array
				(	'function'			=>	__CLASS__.'->'.__FUNCTION__
				,	'arguments_input'		=>	$args
				,	'arguments_descriptions'	=>	array
					(	'class_name'
					,	'return'				=>	array
						(	'blurb'				=>	'Return the exposed values or echo to browser.'
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
		
		$exposed_class = '<h1>Class: '
		.	$class_name
		.	'</h1><h2>'
		.	$class_name
		.	' class methods:</h2>'
		.	print_r
			(	get_class_methods
				(	$class_name
				)
			,	1
			)
		.	'<h2>'
		.	$class_name
		.	' class vars:</h2>'
		.	print_r
			(	get_class_vars
				(	$class_name
				)
			,	1
			)
		;
		
		if	(	$return
			):
			return $exposed_class;
		else:
			self::expose
			(	$exposed_class
			);
		endif;
	}

	public static function expose_classes
	(	$args	=	array()
	)
	{	extract
		(	debug::function_argument_verify
			(	array
				(	'function'			=>	__FUNCTION__
				,	'arguments_input'		=>	$args
				,	'arguments_descriptions'	=>	array
					(	'class_path'				=>	array
						(	'blurb'						=>	'Path to search for class files.'
						,	'default_value'				=>	$GLOBALS['src_path']
						)
					,	'class_file_prefix'			=>	array
						(	'default_value'				=>	$GLOBALS['class_file_prefix']
						)
					,	'list_only'					=>	array
						(	'possible_values'		=>	array
							(	0
							,	1
							)
						,	'default_value'		=>	0
						)
					)
				)
			)
		);
			
		$class_files = files::in_dir
		(	$class_path
		);
		
		$class_names = array();
		foreach
			(	$class_files	as	$class_file
			):
			if	(	strstr
					(	$class_file
					,	$class_file_prefix
					)	==	$class_file
				):
				$class_file_prefix_length = strlen
				(	$class_file_prefix
				);
				$class_name = substr
				(	$class_file
				,	$class_file_prefix_length
				,	strrpos
					(	$class_name
					,	'.'
					)
				-	$class_file_prefix_length
				+	2
				);
				$class_names[] = $class_name;
			endif;
		endforeach;
		
		if	(	$list_only
			):
			self::expose
			(	$class_names
			);
		else:
			foreach
				(	$class_names	as	$class_name
				):
				self::expose_class
				(	$class_name
				);
			endforeach;
		endif;
	}

	public static function function_argument_verify
	(	$args	=	array()
	)
	{	$args_descs = array
		(	'function'			=>	array
			(	'blurb'				=>	'Name of the function whose argument input is being verified.'
			,	'possible_values'		=>	array
				(	array
					(	'possible_value'	=>	'__FUNCTION__'
					,	'blurb'			=>	'http://us.php.net/manual/en/language.constants.predefined.php
	Use this value when function to verify exists in the global scope.'
					)
				,	array
					(	'possible_value'	=>	"__CLASS__.'->'.__FUNCTION__"
					,	'blurb'			=>	'http://us.php.net/manual/en/language.constants.predefined.php
	Use this value when function to verify is a class method.'
					)
				)
			,	'relax_possible_values'	=>	1
			)
		,	'function_description'	=>	array
			(	'blurb'				=>	'A short summary of what purpose the function is meant to accomplish.  Leave undefined only if the purpose is self-evident, or made sufficiently clear by the argument descriptions.'
			,	'default_value'		=>	''
			)
		,	'arguments_input'		=>	array
			(	'blurb'				=>	'An associative array of argument names and values, passed through from the function to be verified.  If a non-array value is entered, an associate array will be created, containing a single element, keyed with the first key from $arguments_descriptions and equal to the non-array value.'
			)
		,	'arguments_descriptions'	=>	array
			(	'blurb'				=>	'An associative nested array keyed by the names of the arguments expected by the function to be verified.  Can contain details for each argument, such as a descriptive blurb, possible values, and default value if undefined.'
			)
		,	'return_description'	=>	array
			(	'blurb'				=>	'Description of value returned by function.'
			,	'default_value'		=>	''
			)
		,	'help'				=>	array
			(	'blurb'				=>	'Defining this argument to any non-empty value forces the verification process to output the function documentation.'
			,	'default_value'		=>	''
			)
		);
		$default_arg = key
		(	$args['arguments_descriptions']
		);
		$default_arg_name = 
		(	is_numeric
			(	$default_arg
			)
		)
		?	current
			(	$args['arguments_descriptions']
			)
		:	$default_arg
		;
		if	(	!is_array
				(	$args['arguments_input']
				)
			||	(	!isset
					(	$args['arguments_input'][$default_arg_name]
					)
				&&	!isset
					(	$args['arguments_descriptions'][$default_arg_name]['default_value']
					)
				)
			):
			$args['arguments_input'] = array
			(	$default_arg_name	=>	$args['arguments_input']
			);
		endif;
		if	(	!self::$mode
			||	$args['function'] == __FUNCTION__
			):
			extract
			(	$args
			);
		else:
			extract
			(	self::function_argument_verify
				(	array
					(	'function'			=>	__FUNCTION__
					,	'function_description'	=>	'This function is meant to serve as self-generating documentation for the code base during ongoing development.  Other functions that use this verification method will produce explicit, detailed instructions for their use when called with improper input arguments.
	
	/*
	//	HOW TO USE FUNCTION ARGUMENT VERIFICATION
	
	//	1: TO VERIFY ANOTHER FUNCTION, THAT FUNCTION MUST NOW ACCEPT ONLY A SINGLE ARGUMENT: AN ARRAY CALLED $args
		
		// FUNCTION DEFINITION
		function function_name($args = array()) {
			{function_code}
		}
		
		// FUNCTION CALL
		function_name
		(	array
			(	\'{name_of_argument_1}\'	=>	{value_of_argument_1}
			,	\'{name_of_argument_2}\'	=>	{value_of_argument_2}
			...
			)
		);
		
	//	THE ARRAY KEYS ARE ALWAYS THE ARGUMENT NAMES, AND...
	//	ARGUMENTS CAN BE SUBMITTED IN ANY ORDER, REGARDLESS OF WHETHER OR NOT THEY HAVE DEFAULT VALUES DEFINED
	
	//
	//	HOWEVER, IF THE FIRST ARGUMENT EXPECTS AN ARRAY, ITS DEFAULT VALUE MUST BE DEFINED, OR ELSE THE VERIFICATION WILL FAIL
	//
	
	//	2: THEN CALL function_argument_verify() FROM THE BEGINNING OF THE OTHER FUNCTION USING THE FOLLOWING SYNTAX
		
		function function_name($args = array()) {
			extract
			(	debug::function_argument_verify
				(	array
					(	\'function\'				=>	{function_name}
					,	\'function_description\'		=>	{function_description}
					,	\'arguments_input\'			=>	$args
					,	\'arguments_descriptions\'	=>	array
						(	\'{name_of_argument_1}\'			=>	array
							(	\'blurb\'						=>	{description_of_argument_purpose}
								// LEAVE BLURB UNDEFINED IF NAME OF ARGUMENT IS SELF-EXPLANATORY
							
							,	\'possible_values\'			=>	array
								(	array
									(	\'possible_value\'		=> {possible_value_1}
									,	\'blurb\'				=> {description of possible value purpose / effect}
									)
								,	{possible_value_2}
								...
								) // LEAVE UNDEFINED IF NO RESTRICTIONS EXIST AS TO WHICH VALUES ARE PERMITTED
								// POSSIBLE VALUES WITHOUT BLURBS MAY BE SET AS STRING VALUES, RATHER THAN AS KEYS FOR SUB-ARRAYS
							
							,	\'relax_possible_values\'	=>	1
								// SETTING relax_possible_values TO TRUE DEACTIVATES VERIFICATION OF ARGUMENT VALUE INPUT AGAINST POSSIBLE VALUES LIST
								// LEAVE UNDEFINED IF FALSE
								// ALLOWS FOR SHORTHAND RANGES ("FROM X", "TO Y", ETC.), DYNAMIC VALUES, AND OTHER SPECIAL CASES
							
							,	\'default_value\'			=>	{default_value_of_argument}
								// LEAVE UNDEFINED IF NO DEFAULT VALUE EXISTS
							)
						,	\'{name_of_argument_2}\'			=>	array
							(
							)
							...
						)
						// IF ALL ARGUMENT DESCRIPTION VALUES ARE UNDEFINED, THE ARGUMENT NAMES MAY BE SET AS A SIMPLE STRINGS, RATHER THAN AS KEYS FOR SUB-ARRAYS
						// THE VERIFICATION PROCESS NEEDS TO KNOW, AT THE VERY LEAST, WHICH VARIABLE NAMES TO EXPECT IN THE $arguments_input ARRAY
					
					,	\'return_description\'		=>	{description_of_value_returned_by_function} (IF ANY)
					,	\'help\'					=>	1
					//	ARGUMENT VERIFICATION WILL ONLY BE PERFORMED IN APP DEBUG MODE
					//	UNLESS ARGUMENT $help = 1 IS INCLUDED
					)
				)
			);
			
			{function_code}
			
		}
	*/
	
	It does take a little longer initially to program in this way, but in the end, you have an integrated function reference that should save more hours that might have been spent hunting down bugs in your own or someone else\'s code.
	
	Verification is performed automatically in debug mode, but the "docs" for any function can also be forced visible by including an argument named "help", as you\'ll see below.'
					,	'arguments_input'		=>	$args
					,	'arguments_descriptions'	=>	$args_descs
					,	'return_description'	=>	'Upon successful verification, returns the same $arguments_input array that was passed in.  If verification fails, nothing is returned, page processing is killed, and the error text / function documentation is output to the browser.'
					)
				)
			);
		endif;
		
		if	(	self::$mode
			&&	empty
				(	$arguments_input['force']
				)
			):
			// CHECK FOR MYSTERY VARIABLES
			$mystery_args = array();
			foreach
				(	$arguments_input as $arg_name => $arg_value
				):
				if	(	!isset
						(	$arguments_descriptions[$arg_name]
						)						
					&&	array_search
						(	$arg_name
						,	$arguments_descriptions
						)	===	false
					):
					$mystery_args[$arg_name] = $arg_value;
				endif;
			endforeach;
			reset
			(	$arguments_input
			);
			
			// CHECK FOR REQUIRED VARIABLES AND DEFAULT VALUES
			foreach
				(	$arguments_descriptions as $arg_name => $arg_description
				):
				if	(	is_numeric
						(	$arg_name
						)
					):
					$arg_name = $arg_description;
					$arg_description = array();
				endif;
				if	(	!isset
						(	$arguments_input[$arg_name]
						)
					):
					if	(	!isset
							(	$arg_description['default_value']
							)
	//					&&	!is_null($arg_description['default_value'])
						):
						$arguments_input = array();
						break;
					else:
						$arguments_input[$arg_name] = $arg_description['default_value'];
					endif;
				else:
					if	(	!empty
							(	$arg_description['possible_values']
							)
						&&	empty
							(	$arg_description['relax_possible_values']
							)
						):
						$test_possible_values = array();
						foreach
							(	$arg_description['possible_values'] as $arg_possible_value
							):
							$test_possible_value =
							(	is_array
								(	$arg_possible_value
								)
							)
							?	$arg_possible_value['possible_value']
							:	$arg_possible_value
							;
							if	(	defined
									(	$test_possible_value
									)
								):
								$test_possible_value = constant
								(	$test_possible_value
								);
							endif;
							$test_possible_values[] = $test_possible_value;
						endforeach;
						if	(	!in_array
								(	$arguments_input[$arg_name]
								,	$test_possible_values
								)
							):
							$arguments_input = array();
							break;
						endif;
					endif;
				endif;
			endforeach;
		else:
			foreach
				(	$arguments_descriptions as $arg_name => $arg_description
				):
				if	(	is_numeric
						(	$arg_name
						)
					):
					$arg_name = $arg_description;
					$arg_description = array();
				endif;
				if	(	!isset
						(	$arguments_input[$arg_name]
						)
					):
					if	(	!isset
							(	$arg_description['default_value']
							)
	//					&&	!is_null($arg_description['default_value'])
						):
						$arguments_input[$arg_name] = NULL;
						break;
					else:
						$arguments_input[$arg_name] = $arg_description['default_value'];
					endif;
				endif;
			endforeach;
		endif;
		reset
		(	$arguments_descriptions
		);
		if	(	(	!count
					(	$arguments_input
					)
				&&	self::$mode
				)
			||	!empty
				(	$arguments_input['help']
				)
			):
			$func_help_html = '<h1>'
			.	$function
			.	'()</h1>'
			;
			if	(	!empty
					(	$function_description
					)
				):
				$func_help_html .= '<p><pre>'
				.	$function_description
				.	'</pre></p>'
				;
			endif;
			if	(	!empty
					(	$mystery_args
					)
				):
				$func_help_html .= '<h2>UNKNOWN ARGUMENTS PASSED:</h2>'
				.	self::expose
					(	array
						(	'data'		=>	$mystery_args
						,	'return'		=>	1
						,	'sort_by_key'	=>	1
						)
					)
				;
			endif;
			$func_help_html .= '<em>This function requires a single argument, an</em> <strong>array()</strong><br /><em>which should contain the following values:</em>'
			.	"\n"
			.	'<dl>'
			;
			foreach
				(	$arguments_descriptions as $arg_name => $arg_description
				):
				if	(	!is_array
						(	$arg_description
						)
					):
					$arg_name = $arg_description;
				endif;
				$func_help_html .= '<dt><h3>'
				.	$arg_name
				.	' => </h3></dt>'
				.	"\n"
				;
				if	(	is_array
						(	$arg_description
						)
					):
					$func_help_html .= '<dd>';
					if	(	!empty
							(	$arg_description['blurb']
							)
						):
						$func_help_html .= '<span style="font-size:smaller;font-style:italic;">'
						.	nl2br
							(	$arg_description['blurb']
							)
						.	'<br /><br /></span>'
						.	"\n"
						;
					endif;
					if	(	!empty
							(	$arg_description['possible_values']
							)
						):
						$func_help_html .= '<em>Possible Values</em>:<br />';
						foreach
							(	$arg_description['possible_values'] as $arg_possible_value
							):
							$func_help_html .= '<li type="square">';
							if	(	is_array
									(	$arg_possible_value
									)
								):
								$func_help_html .= '<span style="color:red;font-weight:bold;">'
								.	$arg_possible_value['possible_value']
								.	'</span> : '
								.	nl2br
									(	$arg_possible_value['blurb']
									)
								;
							else:
								$func_help_html .= '<span style="color:red;font-weight:bold;">'
								.	$arg_possible_value
								.	'</span>'
								;
							endif;
							$func_help_html .= '</li>'
							.	"\n"
							;
						endforeach;
						$func_help_html .= '<br />';
						;
					endif;
					$func_help_html .= '<em>Default Value</em>: ';
					if	(	isset
							(	$arg_description['default_value']
							)
						):
						if	(	$arg_description['default_value'] === ''
							):
							$arg_description['default_value'] = "'' <em>(EMPTY STRING)</em>";
						endif;
						$func_help_html .= '<span style="color:red;">'
						.	$arg_description['default_value']
						.	'</span><br />'
						;
					else:
						$func_help_html .= '<span style="color:red;font-style:italic;">NO DEFAULT VALUE SUPPLIED BY FUNCTION. Valid value for key "'
						.	$arg_name
						.	'" must be included in function call.</span><br />'
						;
					endif;
					$func_help_html .= '<br />'
					.	"\n"
					.	'</dd>'
					.	"\n"
					;
				else:
					$func_help_html .= '<dd><span style="color:red;font-style:italic;">NO DEFAULT VALUE SUPPLIED BY FUNCTION. Valid value for key "'
					.	$arg_name
					.	'" must be included in function call.</span><br />'
					.	"\n"
					.	'</dd>'
					.	"\n"
					;
				endif;
			endforeach;
			if	(	!empty
					(	$return_description
					)
				):
				$func_help_html .= '<h2>returns</h2><p><pre class="pre">'
				.	$return_description
				.	'</pre></p>'
				;
			endif;
			$error_blurb = 
			(	!empty
				(	$arguments_input['help']
				)
			)
			?	'help triggered'
			:	'failed'
			;
			trigger_error
			(	'function argument verification '
			.	$error_blurb
			.	' for function '
			.	$function
			.	'()'
			);
			echo '<style>
	<!--
	/* Browser specific (not valid) styles to make preformatted text wrap */
	pre {
	 white-space: pre-wrap;       /* css-3 */
	 white-space: -moz-pre-wrap;  /* Mozilla, since 1999 */
	 white-space: -pre-wrap;      /* Opera 4-6 */
	 white-space: -o-pre-wrap;    /* Opera 7 */
	 word-wrap: break-word;       /* Internet Explorer 5.5+ */
	}
	-->
			</style><blockquote><table cellpadding="0" cellspacing="0" border="0" width="100%"><tr><td>'
			.	$func_help_html
			.	'</dl></td></tr></table></blockquote>'
			;
			echo '<pre><hr /><h2>PERFORMING ERROR BACKTRACE...</h2><hr /><br />';
			$error_backtrace = debug_backtrace();
			unset
			(	$error_backtrace[0]
			);
			self::expose
			(	$error_backtrace
			);
			echo '<hr /></pre>';
			exit;
		else:
//			echo '<li>'.date('Y-m-d H:i:s').' | FUNCTION: <b>'.$function.'</b></li>';
			return $arguments_input;
		endif;
	}
	
	public static function get_mode()
	{	return self::$mode;
	}
	
	public static function set_mode
	(	$args	=	array()
	)
	{	extract
		(	self::function_argument_verify
			(	array
				(	'function'			=>	__CLASS__.'->'.__FUNCTION__
				,	'arguments_input'		=>	$args
				,	'arguments_descriptions'	=>	array
					(	'mode'				=>	array
						(	'possible_values'		=>	array
							(	'on'
							,	'off'
							,	'auto'
							)
						,	'default_value'		=>	'auto'
						)
					)
				)
			)
		);
			
		switch
			(	$mode
			):
			case 'on':
				$mode = 1;
				break;
			case 'off':
				$mode = 0;
				break;
			default: // case 'auto':
				$mode = 0;
				foreach
					(	self::$from_ips	as	$from_ip
					):
					if	(	$from_ip['ip']	==	$_SERVER['REMOTE_ADDR']
						):
						$mode = 1;
						break;
					endif;
				endforeach;
				reset
				(	self::$from_ips
				);
		endswitch;

		self::$mode =
		(	empty
			(	$mode
			)
		)
		?	0
		:	1
		;
	
		@ini_set
		(	'display_errors'
		,	self::$mode
		);

	}
	
	public static function verify_extension
	(	$args	=	array()
	)
	{	extract
		(	debug::function_argument_verify
			(	array
				(	'function'			=>	__FUNCTION__
				,	'arguments_input'		=>	$args
				,	'arguments_descriptions'	=>	array
					(	'extension'		=>	array
						(	'blurb'			=>	'Name of the extension to verify.  Do not include a file extension.'
						)
					)
				)
			)
		);
		
		// UMM, WHAT ABOUT "php_extension.ext" ?????
		
		$loaded = 0;
		if	(	extension_loaded
				(	$extension
				)
			):
			$loaded = 1;
		else:
			if	(	@dl
					(	$extension
					.	'.so'
					)
				):
				$loaded = 1;
			else:
				if	(	@dl
						(	$extension
						.	'.dll'
						)
					):
					$loaded = 1;
				endif;
			endif;
		endif;
		return
		(	$loaded
		);
	}

	public static function vomit
	(	$args	=	array()
	)
	{	extract
		(	debug::function_argument_verify
			(	array
				(	'function'			=>	__CLASS__.'->'.__FUNCTION__
				,	'arguments_input'		=>	$args
				,	'arguments_descriptions'	=>	array
					(	'error_text'	=>	array
						(	'default_value'	=>	'Forced page death'
						)
					)
				)
			)
		);
		
		if	(	self::$mode
			):
			trigger_error
			(	$error_text
			);
			echo '<style>
	<!--
	/* Browser specific (not valid) styles to make preformatted text wrap */
	pre {
	 white-space: pre-wrap;       /* css-3 */
	 white-space: -moz-pre-wrap;  /* Mozilla, since 1999 */
	 white-space: -pre-wrap;      /* Opera 4-6 */
	 white-space: -o-pre-wrap;    /* Opera 7 */
	 word-wrap: break-word;       /* Internet Explorer 5.5+ */
	}
	-->
			</style>';
			echo '<pre><hr /><h2>PERFORMING ERROR BACKTRACE...</h2><hr /><br />';
			$error_backtrace = debug_backtrace();
			unset
			(	$error_backtrace[0]
			);
			self::expose
			(	$error_backtrace
			);
			echo '<hr /></pre>';
			exit;
		else:
			return false;
		endif;
	}
	
}

