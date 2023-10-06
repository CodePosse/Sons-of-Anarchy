<?php

class files {

	public static $max_file_size_method = '';
	public static $max_file_size_directives = array
	(	'UPLOAD_MAX_FILESIZE'	=>	0
	,	'POST_MAX_SIZE'			=>	0
	);
	
	public static function bytes_display
	(	$args	=	array()
	)
	{	extract
		(	debug::function_argument_verify
			(	array
				(	'function'			=>	__FUNCTION__
				,	'arguments_input'		=>	$args
				,	'arguments_descriptions'	=>	array
					(	'bytes'
					,	'decimal_places'	=>	array
						(	'default_value'		=>	2
						)
					,	'label_type'		=>	array
						(	'possible_values'	=>	array
							(	'full'
							,	'abbr'
							)
						,	'default_value'		=>	'abbr'
						)
					)
				)
			)
		);	
	
		force_load
		(	'numbers'
		);
		
		if	(	$bytes >= numbers::$terabyte_base2
			):
			$bytes_display = number_format
			(	$bytes
				/	numbers::$terabyte_base2
			,	$decimal_places
			);
			$label = 'TB';
		else:
			if	(	$bytes >= numbers::$gigabyte_base2
				):
				$bytes_display = number_format
				(	$bytes
					/	numbers::$gigabyte_base2
				,	$decimal_places
				);
				$label = 'GB';
			else:
				if	(	$bytes >= numbers::$megabyte_base2
					):
					$bytes_display = number_format
					(	$bytes
						/	numbers::$megabyte_base2
					,	$decimal_places
					);
					$label = 'MB';
				else:
					if	(	$bytes >= numbers::$kilobyte_base2
						):
						$bytes_display = number_format
						(	$bytes
							/	numbers::$kilobyte_base2
						,	$decimal_places
						);
						$label = 'KB';
					else:
						$bytes_display = number_format
						(	$bytes
						);
						$label = 'B';
					endif;
				endif;
			endif;
		endif;
		
		switch
			(	$label_type
			):
			case 'full':
				$labels_full = array
				(	'B'		=>	'Bytes'
				,	'KB'	=>	'Kilobytes'
				,	'MB'	=>	'Megabytes'
				,	'GB'	=>	'Gigabytes'
				,	'TB'	=>	'Terabytes'
				);
				$label = $labels_full[$label];
				break;
		endswitch;		
		
		$bytes_display .= '&nbsp;'
		.	$label
		;
		
		return $bytes_display;
	}	

	public static function delete_file
	(	$args	=	array()
	)
	{	extract
		(	debug::function_argument_verify
			(	array
				(	'function'			=>	__FUNCTION__
				,	'arguments_input'		=>	$args
				,	'arguments_descriptions'	=>	array
					(	'file'				=>	array
						(	'blurb'				=>	'File to delete.  Prepend with full path.'
						)
					)
				)
			)
		);
		
		$delete = @unlink
		(	$file
		);
		if	(	@file_exists
				(	$file
				)
			):
			$filesys = preg_replace
			(	'/\//'
			,	'\\'
			,	$file
			);
			$delete = @system
			(	"del $filesys"
			);
			if	(	@file_exists
					(	$file
					)
				):
				$delete = @chmod
				(	$file
				,	0775
				);
				$delete = @unlink
				(	$file
				);
				$delete = @system
				(	"del $filesys"
				);
			endif;
		endif;
	}
	
	public static function force_download
	(	$args	=	array()
	)
	{	extract
		(	debug::function_argument_verify
			(	array
				(	'function'			=>	__FUNCTION__
				,	'arguments_input'		=>	$args
				,	'arguments_descriptions'	=>	array
					(	'name'						=>	array
						(	'blurb'						=>	'Name of file to be downloaded, with extension.  If $content is left empty, this value should be the full path to a local file.'
						)
					,	'content'					=>	array
						(	'blurb'						=>	'Content to be downloaded as a file with filename $name.'
						,	'default_value'				=>	''
						)
					,	'restrict_to_referer'		=>	array
						(	'blurb'						=>	'If $_SERVER[\'HTTP_REFERRER\'] is present, it must begin with this string to allow download.'
						,	'default_value'				=>	$_SERVER['HTTP_HOST']
						)
					)
				)
			)
		);
		
		if	(	empty
				(	$restrict_to_referer
				)
			||	(	empty
					(	$_SERVER['HTTP_REFERER']
					)
				||	stripos
					(	$_SERVER['HTTP_REFERER']
					,	$restrict_to_referer
					)	!== false
				)
			):
			
//			THE FOLLOWING 6 HEADERS MAY OR MAY NOT BE NECESSARY
			header
			(	'Pragma: public'
			);
			header
			(	'Expires: 0'
			);
			header
			(	'Cache-Control: must-revalidate, post-check=0, pre-check=0'
			);
			header
			(	'Cache-Control: private'
			,	false	
			);	// required for certain browsers
			header
			(	'Content-Description: File Transfer'
			);
			header
			(	'Content-Transfer-Encoding: binary'
			);
			
//			BUT EVERYTHING FROM THIS POINT ON IS PRETTY NECESSARY
			switch
				(	strtolower
					(	substr
						(	strrchr
							(	$name
							,	'.'
							)
						,	1
						)
					)
				):
			    case 'pdf':
			    	$mime = 'application/pdf';
			    	break;
			    case 'zip':
			    	$mime = 'application/zip';
			    	break;
			    case 'jpeg':
			    case 'jpg':
			    	$mime = 'image/jpg';
			    	break;
			    case 'gif':
			    	$mime = 'image/gif';
			    	break;
			    default:
			    	$mime = 'application/force-download';
    		endswitch;
			header
			(	'Content-Type: '
				.	$mime
			);
			if	(	empty
					(	$content
					)
				):
				$base_name = basename
				(	$name
				);
	
				$file_mtime = 
				(	$mtime	=	filemtime
					(	$name
					)
				)
				?	$mtime
				:	gmtime()
				;
						
				$file_size = intval
				(	sprintf
					(	"%u"
					,	filesize
						(	$name
						)
					)
				);
			else:
				$base_name = $name;
				
				$file_mtime = gmtime();				
				$file_size = strlen
				(	$content
				);
			endif;
		
			// Maybe the problem is we are running into PHPs own memory limit, so:
			if	(	intval
					(	$file_size
					+	1
					)	>	return_bytes
					(	ini_get
						(	'memory_limit'
						)
					)
				&&	intval
					(	$file_size
					*	1.5
					)	<=	1073741824	 //Not higher than 1GB
				):
				ini_set
				(	'memory_limit'
				,	intval
					(	$file_size
					*	1.5
					)
				);
			endif;
			
			// Maybe the problem is Apache is trying to compress the output, so:
			/*@*/ apache_setenv
			(	'no-gzip'
			,	1
			);
			/*@*/ini_set
			(	'zlib.output_compression'
			,	0
			);
			
			$base_name = 
			(	$GLOBALS['user']->browser['msie']
			)
			?	urlencode
				(	$base_name
				)
			:	'"'
				.	$base_name
				.	'"'
			;
			header
			(	'Content-Disposition: attachment; filename='
				.	$base_name
				.	'; modification-date="'
				.	date
					(	'r'
					,	$file_mtime
					)
				.	'";'
			);
			// Set the length so the browser can set the download timers
			header
			(	'Content-Length: '
				.	$file_size
			);
			// If it's a large file we don't want the script to timeout, so:
			set_time_limit
			(	600
			);
			// If it's a large file, readfile might not be able to do it in one go, so:
			$chunksize = 1
			*	(	1024
				*	1024
				)
			; // how many bytes per chunk
			
			if	(	empty
					(	$content
					)
				):
				if	(	$file_size	>	$chunksize
					):
					$handle = fopen
					(	$name
					,	'rb'
					);
					$buffer = '';
					while
						(	!feof
							(	$handle
							)
						):
						$buffer = fread
						(	$handle
						,	$chunksize
						);
						echo $buffer;
						ob_flush();
						flush();
					endwhile;
					fclose
					(	$handle
					);
				else:
					@readfile
					(	$name
					);
				endif;
			else:
//				if	(	$file_size	>	$chunksize
//					):
//					
//				else:
					echo $content;
//				endif;
			endif;
			return true;
		else:		
			return false;
		endif;
	}	
	
	public static function get_extension
	(	$args	=	array()
	)
	{	extract
		(	debug::function_argument_verify
			(	array
				(	'function'			=>	__FUNCTION__
				,	'arguments_input'		=>	$args
				,	'arguments_descriptions'	=>	array
					(	'file_name'
					)
				)
			)
		);
		
		return strtolower
		(	substr
			(	$file_name
			,	strrpos
				(	$file_name
				,	'.'
				)
				+	1
			)
		);
	}
	
	public static function get_perms
	(	$args	=	array()
	)
	{	extract
		(	debug::function_argument_verify
			(	array
				(	'function'			=>	__FUNCTION__
				,	'arguments_input'		=>	$args
				,	'arguments_descriptions'	=>	array
					(	'file_name'
					,	'return_as'					=>	array
						(	'possible_values'			=>	array
							(	'symbolic'
							,	'octal'
							,	'both'
							)
						,	'default_value'				=>	'octal'
						)	
					)
				)
			)
		);
		
		$perms_to_return = array
		(	'symbolic'	=>	''
		,	'octal'		=>	''
		);
		
		$perms = 
		(	fileperms
			(	$file_name
			)
		);
		
		if	(	$return_as	==	'symbolic'
			||	$return_as	==	'both'
			):
	
			if (($perms & 0xC000) == 0xC000) {
				// Socket
				$info = 's';
			} elseif (($perms & 0xA000) == 0xA000) {
				// Symbolic Link
				$info = 'l';
			} elseif (($perms & 0x8000) == 0x8000) {
				// Regular
				$info = '-';
			} elseif (($perms & 0x6000) == 0x6000) {
				// Block special
				$info = 'b';
			} elseif (($perms & 0x4000) == 0x4000) {
				// Directory
				$info = 'd';
			} elseif (($perms & 0x2000) == 0x2000) {
				// Character special
				$info = 'c';
			} elseif (($perms & 0x1000) == 0x1000) {
				// FIFO pipe
				$info = 'p';
			} else {
				// Unknown
				$info = 'u';
			}
	
			// Owner
			$info .= (($perms & 0x0100) ? 'r' : '-');
			$info .= (($perms & 0x0080) ? 'w' : '-');
			$info .= (($perms & 0x0040) ?
						(($perms & 0x0800) ? 's' : 'x' ) :
						(($perms & 0x0800) ? 'S' : '-'));
			
			// Group
			$info .= (($perms & 0x0020) ? 'r' : '-');
			$info .= (($perms & 0x0010) ? 'w' : '-');
			$info .= (($perms & 0x0008) ?
						(($perms & 0x0400) ? 's' : 'x' ) :
						(($perms & 0x0400) ? 'S' : '-'));
			
			// World
			$info .= (($perms & 0x0004) ? 'r' : '-');
			$info .= (($perms & 0x0002) ? 'w' : '-');
			$info .= (($perms & 0x0001) ?
						(($perms & 0x0200) ? 't' : 'x' ) :
						(($perms & 0x0200) ? 'T' : '-'));
						
			$perms_to_return['symbolic'] = $info;
		endif;
		
		if	(	$return_as	==	'octal'
			||	$return_as	==	'both'
			):
			$perms_to_return['octal'] = substr
			(	decoct
				(	$perms
				)
			,	2
			);
		endif;
		
		switch
			(	$return_as
			):
			case 'symbolic':
			case 'octal':
				$perms_to_return = $perms_to_return[$return_as];
				break;
			default: // case 'both':
		endswitch;

		return $perms_to_return;

	}
	
	public static function get_size
	(	$args	=	array()
	)
	{	extract
		(	debug::function_argument_verify
			(	array
				(	'function'			=>	__FUNCTION__
				,	'arguments_input'		=>	$args
				,	'arguments_descriptions'	=>	array
					(	'file'
					)
				)
			)
		);
		
		return exec
		(	'stat -c %s '
		.	escapeshellarg
			(	$file
			)
		);

	}

	public static function get_upload_max_file_size
	(	$args	=	array()
	)
	{	extract
		(	debug::function_argument_verify
			(	array
				(	'function'			=>	__FUNCTION__
				,	'arguments_input'		=>	$args
				,	'arguments_descriptions'	=>	array
					(	'max_file_size'				=>	array
						(	'blurb'						=>	'User/function-submitted max_file_size. Usually: $GLOBALS[\'dbi\']->tables[$table_name]->file_fields[$field_name][\'bytes_max\']'
						,	'default'					=>	0
						)
					)
				)
			)
		);
		
		force_load
		(	'numbers'
		);
		
		foreach
			(	self::$max_file_size_directives	as	$directive_key	=>	$directive_val
			):
			$directive_val = ini_get
			(	strtolower
				(	$directive_key
				)
			);
			if	(	substr
					(	$directive_val
					,	-1
					)	==	'M'
				):
				$directive_val = 
				(	substr
					(	$directive_val
					,	0
					,	-1
					)
				)
				*	numbers::$megabyte_base2
				;
			endif;
			self::$max_file_size_directives[$directive_key] = $directive_val;
		endforeach;
	
		self::$max_file_size_method = 
		(	self::$max_file_size_directives['UPLOAD_MAX_FILESIZE'] < self::$max_file_size_directives['POST_MAX_SIZE']
		)
		?	'UPLOAD_MAX_FILESIZE'
		:	'POST_MAX_SIZE'
		;
			
		if	(	!empty
				(	$max_file_size
				)
			&&	$max_file_size < self::$max_file_size_directives[self::$max_file_size_method]
			):
			self::$max_file_size_method = 'MAX_FILE_SIZE';
		else:
			$max_file_size = self::$max_file_size_directives[self::$max_file_size_method];
		endif;
		
		return $max_file_size;
	}
	
	public static function in_dir
	(	$args	=	array()
	)
	{	extract
		(	debug::function_argument_verify
			(	array
				(	'function'			=>	__FUNCTION__
				,	'arguments_input'		=>	$args
				,	'arguments_descriptions'	=>	array
					(	'path'				=>	array
						(	'blurb'				=>	'Retrieve content list from this directory.  DO NOT use trailing slash to terminate path value.'
						)
					,	'include_extensions'		=>	array
						(	'default_value'		=>	array
							(
							)
						)
					,	'exclude_extensions'		=>	array
						(	'default_value'		=>	array
							(
							)
						)
					,	'return_with_extensions'	=>	array
						(	'default_value'		=>	true
						)
					,	'group_by_extension'	=>	array
						(	'possible_values'		=>	array
							(	0
							,	1
							)
						,	'default_value'			=>	0
//						,	'blurb'					=>	'When used in conjunction with recursion, returns all files in specified sub-directories grouped by extension.  In other words, this value must be 0 to maintain directory tree structure in returned result.'
						)
					,	'sort_functions'		=>	array
						//	SOME WAY OF SORTING BY FILE DATE/TIMES ????
						(	'default_value'			=>	array
							(	'natcasesort'
							)
						)
					,	'ignore_entries'		=>	array
						(	'default_value'			=>	array
							(	'^\..*'
							,	'^thumbs\.db'
							)
						,	'blurb'					=>	'For maximum flexibility, the values entered here should be regular expressions.'
						)
					,	'recursion'				=>	array
						(	'default_value'			=>	true
						,	'blurb'					=>	'To recurse a specific number of levels, use an integer.  To recurse as deeply as possible, use "true".  To disable recursive listing and show only the files in the specified directory, use "false".'
						)
					,	'tree_only'				=>	array
						(	'default_value'			=>	0
						,	'blurb'					=>	'If true, returns only directory structure with no files info.'
						)
					,	'return_full_paths'		=>	array
						(	'default_value'			=>	0
						,	'blurb'					=>	'If true, returns all items beginning with full paths in single array.'
						)
					)
				)
			)
		);
		
		$snagged = 
		$snugged = 
		array()
		;
		if	(	strpos
				(	$path
				,	"\\'"
				)
			):
			$path = str_replace
			(	"\\'"
			,	"'"
			,	$path
			);
		endif;
		// GET RID OF TRAILING SLASH IF PRESENT
		if	(	strrpos
				(	$path
				,	'/'
				)	==	
				strlen
				(	$path
				)
				-	1
			):
			$path = substr
			(	$path
			,	0
			,	-1
			);
		endif;	
		if	(	$handle = opendir
				(	$path
				)
			):
		    while
				(	(	$entry	=	readdir
						(	$handle
						)
					)
					!==	false
				):
				$ignore_this_entry = false;
				foreach
					(	$ignore_entries	as	$ignore_entry	
					):
					if	(	preg_match
							(	'/'
								.	$ignore_entry
								.	'/i'
							,	$entry
							)
						):
						$ignore_this_entry = true;
						break;
					endif;
				endforeach;
				reset
				(	$ignore_entries
				);
		        if	(	!$ignore_this_entry
					):
					$path_entry = $path
					.	'/'
					.	$entry
					;
					if	(	!empty
						 	(	$recursion
							)
						&&	is_dir
						 	(	$path_entry
							)
						):
						if	(	is_numeric
							 	(	$recursion
								)
							&&	!$tree_only
							):
							$recursion--;
						endif;
						$recursor = array
						(	'path'					=>	$path_entry
						,	'include_extensions'	=>	$include_extensions
						,	'exclude_extensions'	=>	$exclude_extensions
						,	'group_by_extension'	=>	$group_by_extension
						,	'sort_functions'		=>	$sort_functions
						,	'ignore_entries'		=>	$ignore_entries
						,	'recursion'				=>	$recursion
						,	'tree_only'				=>	$tree_only
						,	'return_full_paths'		=>	$return_full_paths
						);
						if	(	$return_full_paths
							):
							$snugged[] = $path_entry;
							array_splice
							(	$snugged
							,	count
								(	$snugged
								)
							,	0
							,	self::in_dir
								(	$recursor
								)
							);
						else:
//							if	(	$tree_only
//								):
//								$snugged[] = $entry;
//							else:
								$snugged[$entry] = self::in_dir
								(	$recursor
								); 
//							endif;
						endif;
					else:
						if	(	!$tree_only
							):
							$ext = files::get_extension
							(	$entry
							);
							if	(	(	empty
										(	$include_extensions
										)
									||	in_array
										(	$ext
										,	$include_extensions
										)
									)
								&&	(	empty
										(	$exclude_extensions
										)
									||	!in_array
										(	$ext
										,	$exclude_extensions
										)
									)
								):
								if	(	!$return_with_extensions
									):
									$entry = basename
									(	$entry
									,	'.'
										.	$ext
									);
								endif;
								if	(	$return_full_paths
									):
									$entry = $path
									.	'/'
									.	$entry
									;
								endif;
								if	(	$group_by_extension
									&&	!empty
										(	$ext
										)
									):
									if	(	!isset
											(	$snagged[$ext]
											)
										):
										$snagged[$ext] = array();
									endif;
									$snagged[$ext][] = $entry;
								else:
									$snagged[] = $entry;
								endif;
							endif;
						endif;
					endif;
		        endif;
		    endwhile;
		    closedir
			(	$handle
			);
		endif;
		if	(	!empty
				(	$snagged
				)
			):
			if	(	$group_by_extension
				):
				foreach
					(	$snagged	as	$ext	=>	$files
					):
					foreach
						(	$sort_functions	as	$sort_function
						):
						eval
						(	$sort_function
						.	'($snagged[$ext]);'
						);
					endforeach;
					reset
					(	$sort_functions
					);
				endforeach;
				reset
				(	$snagged
				);
			endif;
			foreach
				(	$sort_functions	as	$sort_function
				):
				eval
				(	$sort_function
				.	'($snagged);'
				);
			endforeach;
			reset
			(	$sort_functions
			);
		endif;
		if	(	!empty
			 	(	$snugged
				)
			):
			ksort
			(	$snugged
			);
			$snagged = $snugged + $snagged;
		endif;
		return $snagged;
	}
	
	public static function include_as_string
	(	$args	=	array()
	)
	{	extract
		(	debug::function_argument_verify
			(	array
				(	'function'			=>	__FUNCTION__
				,	'arguments_input'		=>	$args
				,	'arguments_descriptions'	=>	array
					(	'file'				=>	array
						(	'blurb'				=>	'Name of file to include.  Prepend with full path.'
						)
					,	'parse'				=>	array
						(	'blurb'				=>	'Parse portions of the file between PHP tags.'
						,	'possible_values'		=>	array
							(	0
							,	1
							)
						,	'default_value'		=>	1
						)
					,	'string'				=>	array
						(	'blurb'				=>	'The string to which the included file contents should be concatenated.  If not included, a string will be returned after included file is read or parsed.'
						,	'default_value'		=>	0
						)
					)
				)
			)
		);
		
		$str_meat = trim
		(	file_get_contents
			(	$file
			)
		);
		if	(	$parse
			):
			$parsing = 
			(	substr
				(	$str_meat
				,	0
				,	2
				)
				==	'<?'
			)
			?	1
			:	0
			;
			$str_ray = split
			(	'<\?|\?>'
			,	$str_meat
			);
			foreach (	$str_ray	as	$key	=>	$val	
				):
				if	(	strlen
						(	trim
							(	$val
							)
						)
						>	0
					):
					if	(	$parsing	
						):
						eval
						(	$val	
						);
						$parsing = 0;
					else:
						$GLOBALS[$string] .= $val;
						$parsing = 1;
					endif;
				endif;
			endforeach;
		else:
			$GLOBALS[$string] .= $str_meat;
		endif;
	}
	
	public static function include_safe
	(	$args	=	array()
	)
	{	extract
		(	debug::function_argument_verify
			(	array
				(	'function'			=>	__FUNCTION__
				,	'arguments_input'		=>	$args
				,	'arguments_descriptions'	=>	array
					(	'file'				=>	array
						(	'blurb'				=>	'Name of file to include.  Full path not necessary, PHP\'s include_path is used.'
						)
					,	'once'				=>	array
						(	'blurb'				=>	'Use _once() with include or require function.'
						,	'possible_values'		=>	array
							(	0
							,	1
							)
						,	'default_value'		=>	0
						)
					,	'require'				=>	array
						(	'blurb'				=>	'Use require function instead of include.'
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
		
		$paths = explode
		(	':'
		,	get_include_path()
		);
		$includer = '';

		foreach
			(	$paths	as	$path	
			):
			$file_path = $path
			.	'/'
			.	$file
			;
			if	(	is_file
					(	$file_path
					)
				):
				$includer = 
				(	$require	
				)
				?	'require'
				:	'include'
				;
				if	(	$once	
					):
					$includer .= '_once';
				endif;
				$includer .= "('$file_path');";
				break;
			endif;
		endforeach;
		return $includer;
	}
	
	public static function upload
	(	$args	=	array()
	)
	{	extract
		(	debug::function_argument_verify
			(	array
				(	'function'			=>	__FUNCTION__
				,	'arguments_input'		=>	$args
				,	'arguments_descriptions'	=>	array
					(	'file_request'
					,	's3_bucket'					=>	array
						(	'default_value'				=>	''
						)
					,	'destination_path'			=>	array
						(	'default_value'				=>	''
						)
					,	'max_file_size'				=>	array
						(	'default_value'				=>	0
						)
					,	'safest_name'				=>	array
						(	'possible_values'			=>	array
							(	0
							,	1
							)
						,	'default_value'				=>	1
						)
					,	'replace_spaces'			=>	array
						(	'possible_values'			=>	array
							(	0
							,	1
							)
						,	'default_value'				=>	1
						)
					,	'file_name_to_lowercase'	=>	array
						(	'possible_values'			=>	array
							(	0
							,	1
							)
						,	'default_value'				=>	0
						)
					,	'unique_time_stamp'			=>	array
						(	'default_value'				=>	0
						)
					,	'bytes_min'					=> 	array
						(	'default_value'				=>	0
						)
					,	'valid_extensions'			=> 	array
						(	'default_value'				=>	array()
						)
					,	'image_constraints'			=>	array
						(	'default_value'				=>	array
							(	'width_min'					=>	0
							,	'width_max'					=>	0
							,	'height_min'				=>	0
							,	'height_max'				=>	0
							)
						)
					)
				)
			)
		);
		
		$max_file_size = self::get_upload_max_file_size
		(	$max_file_size
		);	
	
		$erray = array();

		if	(	is_uploaded_file
				(	$file_request['tmp_name']
				)
			):
			if	(	$file_request['error'] > 0
				):
				// THERE HAS BEEN AN UPLOAD ERROR
				switch
					(	$file_request['error']
					):
					case 1:
					case 2:
						// FILE TOO BIG
						$erray[] = 'Chosen file was larger than '
						.	self::bytes_display
							(	array
								(	'bytes'			=>	$max_file_size
								,	'label_type'	=>	'full'
								)
							)
						.	'.'
						;
						break;
					case 3:
						// UPLOAD INCOMPLETE
						$erray[] = 'The file upload was interrupted before completion.';
						break;
					case 4:
						// NO UPLOAD
						$erray[] = 'The file failed to upload.';
						break;
					default:
						// UNSPECIFIED ERROR
						$erray[] = 'Upload failed.';
				endswitch;
			else:
				$uploaded_file_name = $file_request['name'];
				// EXTRACT FILE EXTENSION && FORCE TO LOWERCASE
				$ext = self::get_extension
				(	$uploaded_file_name
				);
				$file_request['name'] = substr
				(	$uploaded_file_name
				,	0
				,	-	strlen
						(	$ext
						)
					-	1
				);
				if	(	$safest_name
					):
					$file_request['name'] = preg_replace
					(	'/[^0-9A-Za-z_\-\.]/'
					,	''
					,	$file_request['name']
					);
				endif;
				// APPEND OPTIONAL TIMESTAMP TO FILE NAME
				if	(	$unique_time_stamp
					):
					$file_request['name'] .= '_'
					.	$unique_time_stamp
					;
				endif;
				$file_request['name'] .= '.'
				.	$ext
				;
				// GET RID OF THE FRIGGING APOSTROPHES
				if	(	strpos
						(	$file_request['name']
						,	"'"
						)
					):
					$file_request['name'] = str_replace
					(	"'"
					,	''
					,	$file_request['name']
					);
				endif;
				// SPACES IN FILE NAME
				if	(	strpos
						(	$file_request['name']
						,	' '
						)
					):
					if	(	$replace_spaces
						):
						$file_request['name'] = str_replace
						(	' '
						,	'_'
						,	$file_request['name']
						);
					else:
						$erray[] = 'Your file name contains SPACE characters.  Please remove all spaces from the file name before attempting your upload again.';
					endif;
				endif;
				// LOWERCASE FILE NAMES
				if	(	$file_name_to_lowercase
					):
					$file_request['name'] = strtolower
					(	$file_request['name']
					);
				endif;
				// FILE TOO SMALL
				if	(	$file_request['size'] < $bytes_min
					):
					$erray[] = 'Supplied file was smaller than the required minimum file size: '
					.	self::bytes_display
						(	array
							(	'bytes'			=>	$bytes_min
							,	'label_type'	=>	'full'
							)
						)
					.	'.'
					;
				endif;
				if	(	!empty
						(	$valid_extensions
						)
					):
					if	(	empty
							(	$valid_extensions[$ext]
							)
						):
						$invalid_extension_error = 'Supplied file must end with one of the following extensions:<ul>';
						foreach
							(	$valid_extensions	as	$valid_extension	=>	$file_type
							):
							$invalid_extension_error .= '<li>'
							.	$valid_extension
							.	'</li>'
							;
						endforeach;
						reset
						(	$valid_extensions
						);
						$erray[] = $invalid_extension_error
						.	'</ul>'
						;
						unset
						(	$file_type
						);
					else:
						$file_type = $valid_extensions[$ext];
					endif;
				endif;
				if	(	!empty
						(	$file_type
						)
					&&	$file_type == 'Image'
					):
					// TEST IMAGE DIMENSIONS AGAINST UPLOAD COLUMN CONSTRAINTS
					$img_info = GetImageSize
					(	$file_request['tmp_name']
					);
					// IMAGE DIMENSIONS TOO LARGE
					if	(	(	$image_constraints['width_max'] > 0
							&&	$img_info[0] > $image_constraints['width_max']
							)
						||	(	$image_constraints['height_max'] > 0
							&&	$img_info[1] > $image_constraints['height_max']
							)
						):
						$image_size_mismatch_error = 'Your image file must be no larger than ';
						if	(	$image_constraints['width_max'] > 0
							):
							$image_size_mismatch_error .= $image_constraints['width_max']
							.	' pixels in width'
							;
						endif;
						if	(	$image_constraints['width_max'] > 0
							&&	$image_constraints['height_max'] > 0
							):
							$image_size_mismatch_error .= ' and ';
						endif;
						if	(	$image_constraints['height_max'] > 0
							):
							$image_size_mismatch_error .= $image_constraints['height_max']
							.	' pixels in height'
							;
						endif;
						$erray[] = $image_size_mismatch_error
						.	'.'
						;
					endif;
					// IMAGE DIMENSIONS TOO SMALL
					if	(	(	$image_constraints['width_min'] > 0
							&&	$img_info[0] < $image_constraints['width_min']
							)
						||	(	$image_constraints['height_min'] > 0
							&&	$img_info[1] < $image_constraints['height_min']
							)
						):
						$image_size_mismatch_error = 'Your image file must be at least ';
						if	(	$image_constraints['width_min'] > 0
							):
							$image_size_mismatch_error .= $image_constraints['width_min']
							.	' pixels in width'
							;
						endif;
						if	(	$image_constraints['width_min'] > 0
							&&	$image_constraints['height_min'] > 0
							):
							$image_size_mismatch_error .= ' and ';
						endif;
						if	(	$image_constraints['height_min'] > 0
							):
							$image_size_mismatch_error .= $image_constraints['height_min']
							.	' pixels in height'
							;
						endif;
						$erray[] = $image_size_mismatch_error
						.	'.'
						;
					endif;
				endif;
				if	(	empty
						(	$erray
						)
					):
					if	(	empty
							(	$s3_bucket
							)
						||	empty
							(	$GLOBALS['s3']
							)
						):
						$new_file_loc = $destination_path
						.	$file_request['name']
						;
						move_uploaded_file
						(	$file_request['tmp_name']
						,	$new_file_loc
						);
						if	(	!is_file
								(	$new_file_loc
								)
							):
							// FOR SOME REASON, TEMP UPLOADED FILE DID NOT MOVE TO PERMANENT LOCATION.
							// MOST OFTEN, THIS ERROR IS TRIGGERED BY INCORRECT PERMISSIONS SET ON THE DESTINATION FOLDER.
							$erray[] = 'The file upload <!-- '
							.	$new_file_loc
							.	' --> did not succeed.  Please try again.'
							;
						endif;
					else:
						if	(	preg_match
								(	'/\/champion_([^_]+)_large\//'
								,	$destination_path
								,	$matches
								)
							):
							$copy_smaller = array
							(	'type'		=>	$matches[1]
							,	's3_bucket'	=>	'player_'
								.	strings::pluralize
									(	$matches[1]
									)
								.	'_small'
							,	'lg_name'	=>	$GLOBALS['page']->request['champion_1-name_safe']
								.	'_'
								.	$matches[1]
								.	'_large.png'
							,	'sm_name'	=>	$GLOBALS['page']->request['champion_1-name_safe']
								.	'_'
								.	$matches[1]
								.	'_small.png'
							);
							$file_request['name'] = $copy_smaller['lg_name'];
							switch
								(	$copy_smaller['type']
								):
								case 'splash':
									$copy_smaller['sm_width'] = 613;
									$copy_smaller['sm_height'] = 361;
									break;
								case 'icon':
									$copy_smaller['sm_width'] = 41;
									$copy_smaller['sm_height'] = 41;										
									break;
								default:
									unset
									(	$copy_smaller
									);
							endswitch;
						else:
							unset
							(	$copy_smaller
							);
						endif;						
						$img_as_str = file_get_contents
						(	$file_request['tmp_name']
						);
						if	(	!$GLOBALS['s3']->create_object
								(	$s3_bucket				// BUCKET
								,	$file_request['name']	// TARGET FILE NAME
								,	array
									(	'body'			=>	$img_as_str
								    ,	'acl'         	=>	AmazonS3::ACL_PUBLIC
								   	,	'contentType'	=>	'image/png'
								   	)
								)
							):
							$erray[] = 's3 upload failed';
						else:
							if	(	!empty
									(	$copy_smaller
									)
								):
								
								force_load
								(	'image_toolbox'
								);
								
								$png = new Image_Toolbox
								(	$img_as_str
								);
								$png->newOutputSize
								(	$copy_smaller['sm_width'] // width
								,	$copy_smaller['sm_height'] // height
								,	1 //  image will crop if necessary to preserve the aspectratio and avoid image distortions.
								);
								$copy_smaller['made'] = $png->save
								(	$copy_smaller['sm_name']
								,	false
								,	false
								,	false
								,	true
								);
							
								if	(	!$GLOBALS['s3']->create_object
										(	$copy_smaller['s3_bucket']			// BUCKET
										,	$copy_smaller['sm_name']	// TARGET FILE NAME
										,	array
											(	'body'			=>	$copy_smaller['made']
										    ,	'acl'         	=>	AmazonS3::ACL_PUBLIC
										   	,	'contentType'	=>	'image/png'
										   	)
										)
									):
									$erray[] = 's3 upload of smaller image version failed';
								else:
									$GLOBALS['dbi']->affect_rows
									(	array
										(	'table'	=>	$GLOBALS['page']->request['z']
										,	'rows'	=>	array
											(	$GLOBALS['page']->request['id']	=>	array
												(	$copy_smaller['type'].'_small'	=>	$copy_smaller['sm_name']
												)
											)
										)
									);
								endif;
							
							endif;
						endif;
					endif;
				endif;
			endif;
		else:
			// POSSIBLE FILE UPLOAD ATTACK
			// SEEMS TO OCCUR WHEN FILE EXCEEDS $MAX_FILE_SIZE SET IN FORM
			$pfup = 'The file "'
			.	$file_request['name']
			.	'" did not finish uploading.'
			.	"\n"
			;
			$pfup .= 
			(	$max_file_size
			)
			?	'<br />Please make sure your chosen file is less than '
				.	self::bytes_display
					(	array
						(	'bytes'			=>	$max_file_size
						,	'label_type'	=>	'full'
						)
					)
				.	'.'
			:	'<br />Please try again.'
			;
			$erray[] = $pfup;
		endif;
		
		return
		(	empty
			(	$erray
			)
		)
		?	$file_request['name']
		:	$erray
		;
			
	}
	
}
