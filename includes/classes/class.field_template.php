<?php

class field_template {

	function __construct
	(	$args	=	array()
	)
	{	extract
		(	debug::function_argument_verify
			(	array
				(	'function'			=>	__CLASS__.'->'.__FUNCTION__
				,	'arguments_input'		=>	$args
				,	'arguments_descriptions'	=>	array
					(	'form'	=>	array
						(	'blurb'	=>	'Name of the form to which this field template will belong.'
						)
					,	'type'				=> array
						(	'possible_values'		=>	array
							(	'table'
							,	'record'
							)
						,	'default_value'		=>	'record'
						)
					,	'field'	=>	array
						(	'blurb'	=>	'Field object whose value is to be rendered, if the display in question corresponds directly to a database field.'
						,	'default_value'	=>	0
						)
					,	'name'	=>	array
						(	'blurb'	=>	'Name of display value.'
						,	'default_value'	=>	''
						)
					,	'template'	=>	array
						(	'default_value'	=>	''
						)
					)
				)
			)
		);
		
		$this->name = $name;
		$this->type = $type;
		$this->form = $form;
		$this->template = $template;
		
		$this->sort = array
		(	'priority'	=>	0
		,	'direction'	=>	'ASC'
		);
		$this->value = array
		(	'raw'			=>	''
		,	'masks'			=>	array()
		,	'display'		=>	''
		);
		$this->attributes = array
		(	'value'			=>	''
		,	'name'			=>	$this->name
		,	'id'			=>	$this->name
		,	'class'			=>	'rec_fld_val'
		,	'size'			=>	0
		,	'width'			=>	0
		,	'height'		=>	0
		,	'maxlength'		=>	0
		,	'onchange'		=>	''
		,	'onmouseover'	=>	''
		,	'onmouseout'	=>	''
		,	'onfocus'		=>	''
		,	'onblur'		=>	''
		,	'disabled'		=>	''
		,	'readonly'		=>	''
		);
		$this->output = 'view';
		
		if	(	!empty
				(	$field
				)	
			&&	is_object
				(	$field
				)
			):
			$this->field = &$field;
			
			if	(	!isset
					(	$this->field->value
					)
				||	is_null
					(	$this->field->value
					)
				||	$this->field->value == 'NULL'
				):
				$this->field->value = '';
			endif;
			$this->value['raw'] = $this->field->value;
			
			if	(	empty
					(	$this->name
					)
				):
				$this->name = $this->field->name;
			endif;
			
			$this->title = 
			(	empty
				(	$this->field->title_plural
				)
			||	$GLOBALS['dbi']->ownerships[$GLOBALS['dbi']->tables[$this->field->table]->owners[$this->name]]['owners_allowed'] == 1
			)
			?	$this->field->title
			:	$this->field->title_plural
			;
			
			$this->title_lowercase = strtolower
			(	$this->field->title
			);
			$this->title_lowercase_plural = 
			(	empty
				(	$this->field->title_plural
				)
			)
			?	strings::pluralize
				(	$this->title_lowercase
				)
			:	strtolower
				(	$this->field->title_plural
				)
			;
			
			$this->attributes['name'] = 
			$this->attributes['id'] = 
			$this->form.'-'.$this->name
			;
			
			if	(	!empty
					(	$this->field->sort_priority
					)
				):
				$this->sort['priority'] = $this->field->sort_priority;
				$this->sort['direction'] = $this->field->sort_direction;
			endif;
			
			$this->attributes['maxlength'] = $this->field->max_length;
			
//			if ($this->name != 'id'):
				
				// UPLOADABLE FILE FIELD
				if	(	!empty
						(	$GLOBALS['dbi']->tables[$this->field->table]->file_fields[$this->field->name]
						)
					):
					$this->value['masks']['file']	=	$GLOBALS['dbi']->tables[$this->field->table]->file_fields[$this->field->name];
					if	(	!empty
							(	$this->field->masks['file']
							)
						):
						foreach	(	$this->value['masks']['file']	as	$key	=>	$val
							):
							if	(	isset
									(	$this->field->masks['file'][$key]
									)
								):
								$this->value['masks']['file'][$key] = $this->field->masks['file'][$key];
							endif;
						endforeach;
						reset
						(	$this->values['masks']['file']
						);
					endif;
				endif;
				
				// FOREIGN KEY
				if	(	empty
						(	$this->value['masks']['foreign_key']
						)
					):
					$f_key_col = '_id';
					$o_key_col = '_owner'
					.	$f_key_col
					;
			//		if	(	!empty($field->foreign_table)	):
					if	(	strstr
							(	$this->name
							,	$f_key_col
							)
							==	$f_key_col
						&&	!in_array
							(	$this->name
							,	array
								(	'facebook_id'
								,	'gas_id'
								,	'lol_id'
								)
							)
						):
						if	(	$this->name == 'captain_id'
							):
							$this->field->foreign_table = 'player';
						else:
							$re_mover = 
							(	strstr
								(	$this->name
								,	$o_key_col
								)
								==	$o_key_col
							)
							?	strlen
								(	$o_key_col
								)
							:	strlen
								(	$f_key_col
								)
							;
							
							$this->field->foreign_table = substr
							(	$this->name
							,	0
							,	-$re_mover
							);
						endif;
					endif;
				else:
					$this->field->foreign_table = $this->value['masks']['foreign_key']['foreign_table'];
				endif;
				if	(	!empty
						(	$this->field->foreign_table
						)
	//				&&	!empty	(	$GLOBALS['dbi']->tables[$field->foreign_table]	)
					):
					if	(	empty
							(	$this->value['masks']['foreign_key']
							)
						):
						$this->value['masks']['foreign_key'] = array();
					endif;
					if	(	!empty
							(	$this->value['masks']['foreign_key']['foreign_table']
							)
						):
						unset
						(	$this->value['masks']['foreign_key']['foreign_table']
						);
					endif;
					if	(	empty
							(	$this->field->foreign_key_field
							)
						):
						$this->field->foreign_key_field = 'id';
					endif;
					unset
					(	$this->value['masks']['foreign_key']['foreign_key_field']
					);
					if	(	empty
							(	$this->value['masks']['foreign_key']['fields_display_mask']
							)
						):
						$this->value['masks']['foreign_key']['fields_display_mask'] = 
						(	empty
							(	$this->field->foreign_display_fields
							)
						)
						?	'$first_text_field$'
						:	'$'
							.	$this->field->foreign_display_fields
							.	'$'
						;
					endif;
					if	(	empty
							(	$this->value['masks']['foreign_key']['order_by']
							)
						):
						$this->value['masks']['foreign_key']['order_by'] = array
						(	'title'			=>	''
						,	'name'			=>	''
						,	'subject'		=>	''
						,	'last_name'		=>	''
						,	'first_name'	=>	''
						);
					endif;
				endif;
				
				// ENUM
				if	(	$this->field->is_enum()
					):
					$this->value['masks']['enum']	= 
					(	empty
						(	$this->field->masks['enum']
						)
					)
					?	array()
					:	$this->field->masks['enum']
					;
					if	(	empty
							(	$this->value['masks']['input_type']
							)
						):
						$this->value['masks']['enum']['input_type'] = 
						(	empty
							(	$this->field->input_type
							)
						)
						?	'auto'
						:	strtolower
							(	$this->field->input_type
							)
						;
					endif;
				endif;
				
				// ENCRYPTED FIELD DATA
				if	(	$this->field->encrypt
					):
					if	(	isset
							(	$this->field->masks['decrypt']
							)
						):
						$this->value['masks']['decrypt'] = $this->field->masks['decrypt'];
					else:
						if	(	$this->field->table == 'credit_card'
							):
							switch
								(	$this->field->name	
								):
								case 'number':
									$this->value['masks']['decrypt'] = 4;
									break;
								case 'code':
									$this->value['masks']['decrypt'] = 0;
									break;
							endswitch;
						else:
							if	(	$this->field->name	==	'password'
								):
								$this->value['masks']['decrypt'] = 0;
							else:
								$this->value['masks']['decrypt'] = '';
							endif;
						endif;
					endif;
				endif;
				
				// DATE / TIME
				if	(	$this->field->is_datetime
						(	'either'
						)
					||	strtolower
						(	$this->field->type
						)	==	'year'
					):
					$this->value['masks']['datetime']	= 
					(	empty
						(	$this->field->masks['datetime']
						)
					)
					?	array()
					:	$this->field->masks['datetime']
					;
					if	(	strtolower
							(	$this->field->type
							)	!=	'year'
						):
						$test_value = preg_replace
						(	'/[-:0 ]/'
						,	''
						,	$this->value['raw']
						);
						if	(	empty
								(	$test_value
								)
							):
							$date = 
							$time = 
							''
							;
							if	(	$this->field->is_datetime
									(	'date'
									)
								):
								$year = 
								(	!empty
									(	$this->field->aggregate_value['year']
									)	
								)
								?	$this->field->aggregate_value['year']
								:	'0000'
								;
								$month = 
								(	!empty
									(	$this->field->aggregate_value['month']
									)	
								)
								?	$this->field->aggregate_value['month']
								:	'00'
								;
								$day = 
								(	!empty
									(	$this->field->aggregate_value['day']
									)	
								)
								?	$this->field->aggregate_value['day']
								:	'00'
								;
								$date .= date_time::valid_date
								(	array
									(	'year'				=>	$year
									,	'month'				=>	$month
									,	'day'				=>	$day
									,	'return_corrected'	=>	0
									)
								);
								if	(	$date	==	'0000-00-00'
									&&	$year + 0
									):
									if	(	!($month + 0)
										):
										$day = '00';
									endif;
									$date = date_time::assemble
									(	array
										(	'aggregate_value'	=>	array
											(	'year'				=>	$year
											,	'month'				=>	$month
											,	'day'				=>	$day
											)
										)
									);
								endif;
							endif;
							if	(	$this->field->is_datetime
									(	'time'
									)
								):
								$time .= 
								(	!empty
									(	$this->field->aggregate_value['hour']
									)	
								)
								?	$this->field->aggregate_value['hour']
								:	'0000'
								;
								$time .= ':';
								$time .= 
								(	!empty
									(	$this->field->aggregate_value['minute']
									)	
								)
								?	$this->field->aggregate_value['minute']
								:	'00'
								;
								$time .= ':';
								$time .= 
								(	!empty
									(	$this->field->aggregate_value['second']
									)	
								)
								?	$this->field->aggregate_value['second']
								:	'00'
								;
							endif;
							if	(	$this->field->is_datetime('both')	
								):
								if	(	$date != '0000-00-00'
									):
									$this->value['raw'] = $date.' '.$time;
								endif;
							else:
								if	(	$this->field->is_datetime('time')
									):
									$this->value['raw'] = $time;
								else:
									if	(	$this->field->is_datetime('date')
										&&	$date != '0000-00-00'
										):
										$this->value['raw'] = $date;
									endif;
								endif;
							endif;
						endif;
					endif;
				else:
					unset
					(	$this->field->masks['datetime']
					);
				endif;
				
				// EMAIL
				if	(	validator::is_email_address
						(	$this->value['raw']
						)
					):
					$this->value['masks']['link'] = 1;
				endif;
				
				// PHONE NUMBER
				if	(	$this->field->name_contains_word
						(	'phone'
						)
					):
					$this->value['masks']['phone_number'] = 
					(	empty
						(	$this->field->masks['phone_number']
						)
					)
					?	'($area_code) $exchange-$number'
					:	$this->field->masks['phone_number']
					;
				endif;
				
				//	CURRENCY
				if	(	strtolower
						(	$this->field->type
						)	==	'decimal'
					&&	(	$this->field->name_contains_word
							(	'cost'
							)
						||	$this->field->name_contains_word
							(	'price'
							)
						)
					):
					$this->value['masks']['currency'] = 
					(	empty
						(	$this->field->masks['currency']
						)
					)
					?	array
						(	'symbol'		=>	'$'
						,	'decimals'		=>	2
						,	'decimal_point'	=>	'.'
						,	'thousands'		=>	','
						)
					:	$this->field->masks['currency']
					;
				endif;
				
				//	TEXT
				if	(	$this->field->is_text()
					):
					$this->value['masks']['textarea'] = 
					(	empty
						(	$this->field->masks['textarea']
						)
					)
					?	array
						(	'rows'			=>	4
						,	'max_length'	=>	$this->field->max_length
						,	'wrap'			=>	'virtual'
						)
					:	$this->field->masks['textarea']
					;
				endif;
				
				// M2 SHOP CODE 
				if	(	$this->field->name_contains_word
						(	'shop'
						)
					&&	$this->field->name_contains_word
						(	'code'
						)
					):
					$this->value['masks']['shop_code'] = true;
				endif;
				
				// TRIM VALUE
				if	(	!empty
						(	$this->field->masks['trim']
						)
					):
					$this->value['masks']['trim'] = $this->field->masks['trim'];
				endif;
				
				// ALL OTHER MASKS
				if	(	!empty
					 	(	$this->field->masks
						)
					):
					foreach
						(	$this->field->masks	as	$mask_name	=>	$field_mask
						):
						if	(	!isset
							 	(	$this->value['masks'][$mask_name]
								)
							):
							$this->value['masks'][$mask_name] = $field_mask;
						endif;
					endforeach;
					reset
					(	$this->field->masks
					);
				endif;
				
				$this->value['masks'] = array_unique
				(	$this->value['masks']
				);
				
//			endif;
		endif;
		
		if	(	empty
				(	$this->title
				)
			):
			$this->title = strings::label
			(	$this->name
			);
		endif;
	}
	
	function explode_options_from_type_string
	(	$args	=	array()
	)
	{	extract
		(	debug::function_argument_verify
			(	array
				(	'function'			=>	__CLASS__.'->'.__FUNCTION__
				,	'arguments_input'		=>	$args
				,	'arguments_descriptions'	=>	array
					(	'type_string'
					)
				)
			)
		);
		
		return explode
		(	"','"
		,	substr
			(	$type_string
			,	(	strpos
					(	$type_string
					,	'('
					)
					+	2
				)
			,	-2
			)
		);
	}
	
	function mask_value
	(	$args	=	array()
	)
	{	extract
		(	debug::function_argument_verify
			(	array
				(	'function'			=>	__CLASS__.'->'.__FUNCTION__
				,	'arguments_input'		=>	$args
				,	'arguments_descriptions'	=>	array
					(	'mask_name'
					,	'mask'	=>	array
						(	'default_value'	=>	''
						)
					)
				)
			)
		);
		
		$display_value = '';
		
		if	(	!empty
				(	$this->field->dud
				)
			):
			$this->attributes['class'] .= ' highlight_dud';
		endif;
		
		switch
			(	$mask_name	
			):
			
			case 'file':
				if	(	$this->output != 'filter'
					):
					if	(	empty
							(	$GLOBALS['s3']
							)
						||	empty
							(	$mask['s3_bucket']
							)
						):
						$href = 
						(	stristr
							(	$mask['path']
							,	'http'
							)	==	$mask['path']
						||	stristr
							(	$mask['path']
							,	'/'
							)	==	$mask['path']
						||	stristr
							(	$mask['path']
							,	'../'
							)	==	$mask['path']
						)
						?	''
						:	'/'
							.	$GLOBALS['page']->path['web_root']
						;	
						if	(	empty
							 	(	$this->value['display']
								)
							):
							$href = '';
						else:
							$href .= $mask['path']
							.	$this->value['display']
							;
						endif;
					else:
						if	(	empty
							 	(	$this->value['display']
								)
							):
							$href = '';
						else:
							$href = 'https://s3.amazonaws.com/'
							.	$mask['s3_bucket']
							.	'/'
							.	$this->value['display']
							;
						endif;
					endif;
					
					switch
						(	$mask['display_method']
						):
						case 'Link to New Window':
							$display_value .= $this->render_link
							(	array
								(	'href'		=>	$href
								,	'target'	=>	'_blank'
								)
							);
							break;
						case 'Link to Same Window':
							$display_value .= $this->render_link
							(	array
								(	'href'	=>	$href
								)
							);
							break;
						case 'iFrame':
//							break;
						case 'Inline':
							$ext = files::get_extension
							(	$this->value['display']
							);
							foreach
								(	$mask['file_types']	as	$ftid	=>	$file_type
								):
								$exts = explode
								(	','
								,	$file_type['extensions']
								);
								if	(	in_array
									 	(	$ext
										,	$exts
										)
									):
									$file_type = $file_type['type'];
									break;
								endif;
							endforeach;
							switch
								(	$file_type
								):
								case 'Video':
									switch
										(	$ext
										):
										default: // case 'flv':
											$display_value .= '<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=10,0,0,0" width="330" height="350" id="player" align="middle"><param name="FlashVars" value="videofile=../'
											.	$href
											.	'" /><param name="allowScriptAccess" value="sameDomain" /><param name="allowFullScreen" value="false" /><param name="movie" value="addons/flashvid/player.swf" /><param name="quality" value="high" /><param name="scale" value="noborder" /><param name="wmode" value="transparent" /><embed src="addons/flashvid/player.swf" type="application/x-shockwave-flash" pluginspage="http://www.adobe.com/go/getflashplayer" FlashVars="videofile=../'
											.	$href
											.	'" width="330" height="350" wmode="transparent" /></object>'
											;	
											break;
									endswitch;
									break;
								case 'Image':
									$display_value .= '<div>'
									.	xhtml::element
										(	array
											(	'tag_name'		=>	'img'
											,	'attributes'	=>	array
												(	'border'		=>	0
												,	'id'			=>	$this->name
													.	'-img'
												,	'src'			=>	$href
												,	'style'			=>	'max-width:100%'
												)
											)
										)
									.	'</div>'
									;	
									break;
								default:
									$display_value .= $href;
							endswitch;
							// FOR NON-IMAGE RENDERABLE MIME TYPES, TREAT AS iFrame
							// FOR NON-RENDERABLE MIME TYPES, TREAT AS Link to New Window
							break;
						case 'Thumbnail':
							// AUTO-THUMBNAILER
							// FOR NON-IMAGE RENDERABLE MIME TYPES, TREAT AS iFrame
							// FOR NON-RENDERABLE MIME TYPES, TREAT AS Link to New Window
//							break;
						default: // case 'Forced Download Link':
							$display_value .= $this->render_link
							(	array
								(	'href'		=>	uri::generate
									(	array
										(	'query'		=>	array
											(	'z'			=>	'download'
											,	'file'		=>	$href
											
/*											
																uri::generate
												(	array
													(	'host'		=>	strtolower
														(	$_SERVER['HTTP_HOST']
														)
													,	'file_path'	=>	$mask['path']
														.	$this->value['display']
													)
												)
*/
											,	'return_to'	=>	$_SERVER['REQUEST_URI']
											)
										,	'query_crypt_key'	=>	$GLOBALS['page']->url_crypt_key
										)
									)
								,	'output'	=>	'view'
								)
							);
					endswitch;
				endif;

				switch
					(	$this->output
					):
					case 'filter':
						$display_value .= $this->render_input();
						break;
					case 'edit':
/*
						if	(	!empty
							 	(	$href
								)
							):
							$display_value .= '$upload_file';
							$file_field_display_style = 'none';
							$this->attributes['type'] = 'text';
						else:
*/
							$file_field_display_style = 'block';
							$this->attributes['type'] = 'file';
//						endif;
					
						// MAX_FILE_SIZE
						$directives = array
						(	'upload_max_filesize'	=>	0
						,	'post_max_size'			=>	0
						);
						foreach
							(	$directives as $directive	=>	$val
							):
							$directives[$directive] = ini_get
							(	$directive
							);
							if	(	substr
									(	$directives[$directive]
									,	-1
									)
									==	'M'
								):
								$directives[$directive] = 
								(	substr
									(	$directives[$directive]
									,	0
									,	-1
									)
								)
								*	numbers::$megabyte_base2
								;
							endif;
						endforeach;
						$directive_bytes_max = 
						(	$directives['upload_max_filesize'] <= $directives['post_max_size']
						)
						?	$directives['upload_max_filesize']
						:	$directives['post_max_size']
						;
						if	(	$mask['bytes_max'] == 0
							||	$directive_bytes_max < $mask['bytes_max']
							):
							$mask['bytes_max'] = $directive_bytes_max;
						endif;
						$this->attributes['onclick'] = "document.getElementById('"
						.	$this->form
						.	"').enctype='multipart/form-data';"
						;
						
						if	(	$mask['select_existing']
							):
							$display_value .= $this->select_files
							(	$mask['filter_by_owner']
							);
							$or = 'OR ';
						else:
							$or = '';
						endif;
						
//						$display_value .= '$upload_file';
						
						$upload_parameters = 
						$image_parameters = 
						$other_parameters = 
						''
						;
						// EXTENSIONS
						if	(	!empty
								(	$mask['file_types']
								)
							):
							$upload_parameters .= '<p><strong>Allowed file types:</strong><ul>';
							$allowed_file_types = array();
							foreach
								(	$mask['file_types']	as	$file_type
								):
								if	(	$file_type['type']	==	'Image'
									):
									$image_parameters = 1;
								else:
									$other_parameters = 1;
								endif;
								$allowed_file_types[$file_type['title']] = '<li>'
								.	strings::pluralize
									(	$file_type['title']
									)
								.	' (file name extensions: <b>.'
								.	str_replace
									(	','
									,	', .'
									,	$file_type['extensions']
									)
								.	'</b>)</li>'
								;
							endforeach;
							natcasesort
							(   $allowed_file_types
							);
							$upload_parameters .= implode
							(   "\n"
							,   $allowed_file_types
							)
							.   '</ul></p>'
							;
						endif;
						// FILE SIZE
						if	(	$mask['bytes_min'] > 0
							||	$mask['bytes_max'] > 0
							):
							$upload_parameters .= '<p><strong>Allowed file sizes:</strong><ul><li>Files must be ';
							$dimensions = 
							$limits = 
							array
							(	'min'
							,	'max'
							);
							foreach
								(	$dimensions	as	$dimension
								):
								if	(	$mask['bytes_'.$dimension] >= numbers::$megabyte_base2
									):
									// MEGABYTES
									$limits[$dimension] = number_format
									(	$mask['bytes_'.$dimension]
										/	numbers::$megabyte_base2
									,	2
									)
									.	' megabytes'
									;
								else:
									if	(	$mask['bytes_'.$dimension] >= numbers::$kilobyte_base2
										):
										// KILOBYTES
										$limits[$dimension] = number_format
										(	$mask['bytes_'.$dimension]
											/	numbers::$kilobyte_base2
										,	2
										)
										.	' kilobytes'
										;
									else:
										// BYTES
										$limits[$dimension] = number_format
										(	$mask['bytes_'.$dimension]
										)
										.	' bytes'
										;
									endif;
								endif;
							endforeach;
							if	(	$mask['bytes_min'] > 0
								):
								if	(	$mask['bytes_min'] == $mask['bytes_max']
									):
									$upload_parameters .= 'exactly <b>'
									.	$limits['min']
									;
								else:
									if	(	$mask['bytes_max'] > 0
										):
										$upload_parameters .= 'between <b>'
										.	$limits['min']
										.	' and '
										.	$limits['max']
										;
									else:
										$upload_parameters .= 'at least <b>'
										.	$limits['min']
										;
									endif;
								endif;
							else:
								$upload_parameters .= 'no larger than <b>'
								.	$limits['max']
								;
							endif;
							$upload_parameters .= '</b></li></ul></p>';
						endif;
						// IMAGE PARAMETERS
						if	(	!empty
								(	$image_parameters
								)
							):
							$image_parameters = '<p style="text-align:left"><strong>Allowed image sizes</strong>';
							if	(	!empty
									(	$other_parameters
									)
								):
								$image_parameters .= ' (applies to image files only)';
							endif;
							$image_parameters .= '<strong>:</strong><ul>';
							$dimensions = array
							(	'width'
							,	'height'
							);
							foreach
								(	$dimensions	as	$dimension
								):
								if	(	$mask[$dimension.'_min'] > 0
									):
									if	(	$mask[$dimension.'_min'] == $mask[$dimension.'_max']
										):
										$image_parameters .= '<li>'
										.	ucwords
											(	$dimension
											)
										.	' must be exactly <b>'
										.	number_format
											(	$mask[$dimension.'_min']
											)
										.	' pixels</b></li>'
										;
									else:
										if	(	$mask[$dimension.'_max'] > 0
											):
											$image_parameters .= '<li>'
											.	ucwords
												(	$dimension
												)
											.	' must be between <b>'
											.	number_format
												(	$mask[$dimension.'_min']
												)
											.	' and '
											.	number_format
												(	 $mask[$dimension.'_max']
												)
											.	' pixels</b></li>'
											;
										else:
											$image_parameters .= '<li>'
											.	ucwords
												(	$dimension
												)
											.	' must be at least <b>'
											.	number_format
												(	$mask[$dimension.'_min']
												)
											.	' pixels</b></li>'
											;
										endif;
									endif;
								else:
									if	(	$mask[$dimension.'_max'] > 0
										):
										$image_parameters .= '<li>'
										.	ucwords
											(	$dimension
											)
										.	' must be no more than <b>'
										.	number_format
											(	$mask[$dimension.'_max']
											)
										.	' pixels</b></li>'
										;
									endif;
								endif;
							endforeach;
							$image_parameters .= '</ul></p>';
						endif;
						if	(	!empty
								(	$upload_parameters
								)
							):
							$parameters_class = 
							(	empty
								(	$this->field->dud
								)
							)
							?	'rec_fld_blr'
							:	'rec_fld_blr highlight_dud'
							;
							$upload_parameters = ' <br /> <div class="'
							.	$parameters_class
							.	'">The uploaded file must meet the following criteria:'
							.	$upload_parameters
							.	$image_parameters
							.	'</div> <br /> '
							;
						endif;
						$this->attributes['name'] .= '[]';
						$display_value .= '<div id="'
						.	$this->form
						.	'-upload_'
						.	$this->name
						.	'" style="display:'
						.	$file_field_display_style
						.	';">'
						.	$or
						.	'Upload a <span style="font-weight:bold">New '
						.	$this->field->title
						.	'</span>: '
						.	$this->render_input()
						.	$upload_parameters
						.	'</div>'
						;
						break;
					case 'view':
						// DISPLAY VALUE HAS ALREADY BEEN SET ABOVE TO APPROPRIATE DISPLAY METHOD
						break;
				endswitch;
				break;
			
			case 'foreign_key':
				switch
					(	$this->output
					):
					case 'filter':
						if	(	empty
								(	$this->field->title_plural
								)
							):
							$this->field->title_plural = strings::pluralize
							(	$this->field->title
							);
						endif;
						if	(	strstr
								(	$this->field->title
								,	' By'
								)	==	' By'
							):
							$this->field->title_plural = $this->field->title;
						endif;
						$mask['empty_option_text'] = '[All '
						.	str_replace
							(	'<br />'
							,	' '
							,	$this->field->title_plural
							)
						.	']'
						;
						if	(	!empty
								(	$GLOBALS['dbi']->tables[$this->field->table]->request[$this->name]
								)
							):
							$this->attributes['value'] = $GLOBALS['dbi']->tables[$this->field->table]->request[$this->name];
						endif;
						
						break;
					case 'edit':
						$mask['empty_option_text'] = '[No '
						.	str_replace
							(	'<br />'
							,	' '
							,	$this->field->title
							)
						.	']'
						;
						break;
					case 'view':
						break;
				endswitch;
				$display_value .= $this->render_foreign_key
				(	$mask
				);
				break;
			
			case 'enum':
				if	(	empty
					 	(	$mask
						)
					):
					$mask = array();
				endif;
				$display_value .= $this->render_enum
				(	$mask
				);
				break;
			
			case 'datetime':
				$date_args = array
				(	'attributes'	=>	$this->attributes
				);
				if	(	!empty
						(	$mask
						)
					):
					if	(	!is_array
							(	$mask
							)
						):
						$mask = array
						(	'display_mask'	=>	$mask
						);
					endif;
					$date_args = array_merge
					(	$date_args
					,	$mask
					);
				endif;
				if	(	$this->field->is_datetime
					 	(	'date'
						)
					&&	$this->output == 'filter'
					):
					$date_args['display_mask'] = '$Y$ &gt; $F$ &gt; $j$';
				endif;			
				if	(	empty
					 	(	$date_args['display_mask']
						)
					):
					$date_args['display_mask'] = '';
					if	(	strtolower
						 	(	$this->field->type
							)	==	'year'
						):
						$date_args['display_mask'] = '$Y$';
					else:
						if	(	$this->field->name == 'active_until'
							||	$this->field->name == 'active_from'
							):
							$this_year = date
							(	'Y'
							);
							$date_args['earliest_year'] = $this_year - 1;
							$date_args['latest_year'] = $this_year + 1;
							$date_args['reverse_year_display'] = 0;
						endif;
						if	(	$this->field->table == 'credit_card'
							&&	$this->field->name == 'expiration_date'
							):
							$this_year = date
							(	'Y'
							);
							$date_args['earliest_year'] = $this_year;
							$date_args['latest_year'] = $this_year + 10;
							$date_args['reverse_year_display'] = 0;
							$date_args['display_mask'] .= 
							(	$this->output == 'view'
							)
							?	'$m$&nbsp;/&nbsp;$y$'
							:	'$m - F$&nbsp;/&nbsp;$Y$'
							;
						else:
							if	(	$this->field->is_datetime
									(	'date'
									)
								):
								$date_args['display_mask'] .= 
								(	$this->output == 'view'
								)
								?	'$Y$-$m$-$d$'
								:	'$Y$/$m - F$/$d$'
								;
							endif;
							if	(	!	(	$this->field->is_datetime
											(	'date'
											)
										&&	$this->output == 'filter'
										)
								):
								if	(	$this->field->is_datetime
										(	'both'
										)
									):
									$date_args['display_mask'] .= 
									(	$this->output == 'view'
									)
									?	' '
									:	xhtml::element
										(	'BR'
										)
									;
								endif;
								if	(	$this->field->is_datetime
										(	'time'
										)
									&&	$this->field->name != 'active_until'
									&&	$this->field->name != 'active_from'
									):
									$date_args['display_mask'] .= 
									(	$this->output == 'view'
									)
									?	'$H$:$i$:$s$'
									:	'$H$:$i$'
									;
								endif;
							endif;
						endif;
					endif;
				endif;
				$display_value .= $this->render_datetime
				(	$date_args
				);
				break;
			
			case 'decrypt':
				$decrypted_value = encryption::my_crypt
				(	array
					(	'data'		=>	$this->value['display']
					,	'key'		=>	$this->field->encrypt
					,	'encrypt'	=>	0
					)
				);		
				switch
					(	$this->output	
					):
					case 'filter':
						// YOU CAN'T FILTER ON ENCRYPTED FIELD DATA
						// WELL, YOU COULD, BUT IT'S A PAIN IN THE ASS TO BE DEALT WITH LATER
						break;
					case 'edit':
						$this->attributes['type'] = 'text';
//						$this->attributes['type'] = 'password';
						$this->attributes['value'] = $decrypted_value;
						$display_value .= $this->render_input();
						break;
					default: // case 'view':
						$display_value .= 
						(	is_numeric
							(	$mask
							)
						)
						?	strings::obscure
							(	array
								(	'string'			=>	$decrypted_value
								,	'reveal_characters'	=>	$mask
								)
							)
						:	$decrypted_value
						;
				endswitch;
				break;
			
			case 'obscure':
				$mask['string'] = 
				(	empty($display_value)
				)
				?	$this->value['display']
				:	$display_value
				;
				$display_value = strings::obscure
				(	$mask
				);
				break;
			
			case 'phone_number':
				switch (	$this->output	
					):
					case 'edit':
						// IDEALLY, THIS SHOULD BE THREE AGGREGATE INPUTS FOR NUMBER SECTIONS
						// FOR NOW, SINGLE INPUT
					case 'filter':
						$display_value .= $this->render_input();
						break;
					default: // case 'view':
						$display_value .= strings::phone_format
						(	array
							(	'phone_number'	=>	$this->value['display']
							,	'display_mask'	=>	$mask
							)
						);
				endswitch;
				break;
			
			case 'currency':
				$display_value .= $this->render_currency();
				break;
			
			case 'link':
				$display_value .= $this->render_link();
				break;
			
			case 'trim':
				switch
					(	$this->output	
					):
					case 'view':
						$display_value .= 
						(	strlen
						 	(	$this->value['display']
							)	>	$mask
						)
						?	substr
							(	$this->value['display']
							,	0
							,	$mask
							)
							.	'...'
						:	$this->value['display']
						;
						break;
					default:
						break;
				endswitch;
				break;
			
			case 'textarea':
				switch
					(	$this->output
					):
					case 'edit':					
						$display_value .= 
						(	$this->name	==	'folder'
						)
						?	$this->render_folder_selector
							(	//$this->value['display']
							)
						:	$this->render_textarea
							(	array
								(	'content'		=>	$this->value['display']
								,	'attributes'	=>	$mask
								)
							)
						;
/*
//	OLDER VERSION: NO EXCEPTION FOR FOLDER SELECTORS
						$display_value .= $this->render_textarea
						(	array
							(	'content'		=>	$this->value['display']
							,	'attributes'	=>	$mask
							)
						);
*/
						if	(	strstr
								(	$this->name
								,	'html'
								)	==	$this->name
							):
							$GLOBALS['page']->scripts['src'][] = './admin/ckeditor/ckeditor.js';
							$GLOBALS['page']->scripts['src'][] = './admin/ckeditor/adapters/jquery.js';
/*
							$GLOBALS['page']->scripts['ready'][] = '$(\'textarea[name$="'
							.	$this->name
							.	'"]\').ckeditor(
	{	autoUpdateElement:	 true
/*
	,	toolbar:
		[
			[	"Bold"
			,	"Italic"
			,	"-"
			,	"NumberedList"
			,	"BulletedList"
			,	"-"
			,	"Link"
			,	"Unlink"
			]
		,	[	"UIColor"
			]
		]
* /
	});
'
							;
*/
							$GLOBALS['page']->scripts['ready'][] = '$ckeditor_'
							.	$this->name
							.	' = CKEDITOR.replace($(\'textarea[name$="'
							.	$this->name
							.	'"]\').attr("id"),
    {
        on:
       {
           "instanceReady": function(evt) {
               evt.editor.document.on("keyup", function() {
               	   $(\'textarea[name$="'
							.	$this->name
							.	'"]\').val(evt.editor.getData());
               });

               evt.editor.document.on("paste", function() {
               	   $(\'textarea[name$="'
							.	$this->name
							.	'"]\').val(evt.editor.getData());
               });
           }
       }
    });
'
							;
						endif;
						break;
					case 'filter':
						$display_value .= $this->render_input();
						break;
					default: // case 'view'
						switch
							(	$this->name
							):
							case 'thumbnail_url':
								$thumbnail_url = $this->value['display'];
								$this->value['display'] = xhtml::element
								(	array
									(	'tag_name'	=>	'IMG'
									,	'attributes'	=>	array
										(	'SRC'			=>	$this->value['display']
										,	'WIDTH'			=>	120
										,	'HEIGHT'		=>	90
										)
									)
								);
							case 'video_code':
								$video_code = 
								(	strstr
									(	$this->value['display']
									,	'<'
									)	==	$this->value['display']
								)
								?	explode
									(	'/'
									,	$this->value['display']
									)
								:	array
									(	0
									,	0
									,	0
									,	0
									,	$this->value['display']
									)
								;
								$this->value['display'] = xhtml::element
								(	array
									(	'tag_name'	=>	'A'
									,	'attributes'	=>	array
										(	'HREF'			=>	'http://youtube.com/watch?v='
											.	$video_code[4]
										,	'TARGET'		=>	'_blank'
										)
									,	'content'		=>	$this->value['display']
									)
								);
							default:
								if	(	$this->name == 'thumbnail_url'
									):
									$this->value['display'] .= '<br/>'
									.	xhtml::element
										(	array
											(	'tag_name'	=>	'A'
											,	'attributes'	=>	array
												(	'HREF'			=>	$thumbnail_url
												,	'TARGET'		=>	'_blank'
												)
											,	'content'		=>	$thumbnail_url
											)
										)
									;
								endif;
								$display_value .= nl2br
								(	$this->value['display']
								);
						endswitch;
				endswitch;
				break;
				
			case 'shop_code':
				switch
					(	$this->output
					):
					case 'filter':
					case 'edit':
						$display_value .= $this->render_shop_code_selector
						(	// $this->value['display']
						);
						break;
					default: // case 'view':
						$display_value .= nl2br
						(	$this->value['display']
						);
				endswitch;
				break;
			
			case 'custom':
				break;
			
			default: // NO MASK SUPPLIED, RETURN RAW VALUE
				switch
					(	$this->output
					):
					case 'filter':
					case 'edit':
						$display_value .= $this->render_input();
						break;
					default: // case 'view':
						$display_value .= nl2br
						(	$this->value['display']
						);
				endswitch;
		endswitch;
		
		return $display_value;
	}

	function render
	(	$args	=	array()
	)
	{	extract
		(	debug::function_argument_verify
			(	array
				(	'function'			=>	__CLASS__.'->'.__FUNCTION__
				,	'arguments_input'		=>	$args
				,	'arguments_descriptions'	=>	array
					(	'output'				=>	array
						(	'blurb'				=>	'The form in which the field value will be rendered to the browser.'
						,	'possible_values'		=>	array
							(	'view'
							,	'edit'
							,	'filter'
							)
						,	'default_value'		=>	'view'
						)
					)
				)
			)
		);
		
		$this->output = 
		(	$output != 'filter'
		&&	(	(	!empty
					(	$this->field
					)
				&&	!$this->field->edit_allowed
				)
			||	empty
				(	$this->field
				)
			)
		)
		?	'view'
		:	$output
		;
/**/		
		if	(	empty
				(	$this->template
				)
			):
			$this->attributes['value'] = 
			$this->value['display'] = 
			(	empty
				(	$this->value['raw']
				)
			&&	empty
				(	$this->field->show_empty_value
				)
			)
			?	''
			:	htmlspecialchars
				(	$this->value['raw']
				)
			;
		
			switch
				(	$this->output
				):
				case 'filter':
					$this->attributes['class'] = 'filter_select';
					$this->attributes['onchange'] = 'document.'
					.	$this->form
					.	'.most_recent_filter.value=this.name;document.'
					.	$this->form
					.	'.submit();return true;'
					;
					$this->attributes['title'] = 'Filter Results by Matching Column Value';
					unset
					(	$this->value['masks']['trim']
					,	$this->field->masks['trim']
					,	$this->attributes['onclick']
					);
					break;
				case 'edit':
					if	(	!is_array
							(	$this->value['raw']
							)
						&&	strlen
							(	$this->value['raw']
							)
							==	0
						):
						if	(	is_null
								(	$this->field->default_value
								)
							||	$this->field->default_value == 'NULL'
							):
							$this->field->default_value = '';
						endif;
						$this->attributes['value'] = 
						$this->value['display'] = 
						$this->value['raw'] = 
						$this->field->default_value
						;
					endif;
				default: // case 'view':
					$this->attributes['class'] = 'rec_fld_val';
					$this->attributes['onchange'] = '';
					$this->attributes['onmouseover'] = '';
					$this->attributes['onmouseout'] = '';
					$this->attributes['onfocus'] = '';
					$this->attributes['onblur'] = '';
			endswitch;
			
			if	(	empty
					(	$this->value['masks']
					)
				):
				$this->value['masks']['string'] = 1;
			endif;
/*			
if	(	$this->field->name ==	'banner'	
	):
	debug::expose
	(	$this->value
	);	
endif;
*/
			foreach
				(	$this->value['masks'] as $mask_name => $mask
				):
				$this->value['display'] = $this->mask_value
				(	array
					(	'mask_name'	=>	$mask_name
					,	'mask'		=>	$mask
					)
				);
			endforeach;
/*
if	(	$this->field->name ==	'banner'	
	):
	debug::expose
	(	$this->value
	);	
endif;
*/
		else:
			$this->value['display'] = $this->template;
		endif;
/**/
		
		if	(	$this->type	==	'record'
			):
			eval
			(	'$blurb = $this->field->'.$GLOBALS['dbi']->tables[$this->field->table]->record->act.'_blurb;'
			);
			
			if	(	!empty
					(	$blurb
					)
				):
				$elemeat_class = 
				(	empty
					(	$this->field->dud
					)
				)
				?	'rec_fld_blr'
				:	'rec_fld_blr highlight_dud'
				;
				$this->value['display'] .= xhtml::element
				(	array
					(	'tag_name'		=>	'DIV'
					,	'attributes'	=>	array
						(	'CLASS'		=>	$elemeat_class
						)
					,	'content'		=>	$blurb
					)
				);
			endif;
		endif;
		
		return $this->value['display'];
	}
		
	function render_currency
	(	$args	=	array()
	)
	{	extract
		(	debug::function_argument_verify
			(	array
				(	'function'			=>	__CLASS__.'->'.__FUNCTION__
				,	'arguments_input'		=>	$args
				,	'arguments_descriptions'	=>	array
					(	'display_mask'			=>	array
						(	'default_value'				=>	$this->value['masks']['currency']
						)
					)
				)
			)
		);
		
		$this->attributes['value'] = number_format
		(	$this->value['display']
		,	$display_mask['decimals']
		,	$display_mask['decimal_point']
		,	$display_mask['thousands']
		);
		
		switch
			(	$this->output	
			):
			case 'filter':
			case 'edit':
				$display_value = $display_mask['symbol']
				.	'&nbsp;'
				.	$this->render_input();
				break;
			default: // case 'view':
				$display_value = $display_mask['symbol']
				.	$this->attributes['value'];
		endswitch;
		return $display_value;
	}
	
	function render_datetime
	(	$args	=	array()
	)
	{	extract
		(	debug::function_argument_verify
			(	array
				(	'function'			=>	__CLASS__.'->'.__FUNCTION__
				,	'arguments_input'		=>	$args
				,	'arguments_descriptions'	=>	array
					(	'attributes'			=>	array
						(	'default_value'		=>	$this->attributes
						)
					,	'default_value'		=>	array
						(	'default_value'		=>	$this->field->default_value
						)
					,	'null_allowed'			=> array
						(	'possible_values'		=>	array
							(	0
							,	1
							)
						,	'default_value'		=>	$this->field->null_allowed
						)
					,	'display_mask'			=>	array
						(	'blurb'				=>	'Use analog of PHP\'s date() function formatting patterns to determine the layout of the various date select boxes.'
						,	'default_value'		=>	$this->value['masks']['datetime']
						)
					,	'earliest_year'			=>	array
						(	'default_value'		=>	(date('Y') - 111)
						)
					,	'latest_year'			=>	array
						(	'default_value'		=>	(date('Y') + 50)
						)
					,	'reverse_year_display'		=> array
						(	'possible_values'			=>	array
							(	0
							,	1
							)
						,	'default_value'		=>	1
						)
					,	'force_end_of_month'	=>	array
						(	'blurb'				=>	'For use with credit card expiration dates, and other cases when the "day" selector is hidden.  When TRUE, this will implement a Javascript triggered by the month selector onchange, which will set the value of the hidden "day" INPUT equal to the last day of the selected month, considering leap years as follows:

The Gregorian calendar, the current standard calendar in most of the world, adds a 29th day to February in all years evenly divisible by 4, except for century years (those ending in -00), which receive the extra day only if they are evenly divisible by 400. Thus 1996 was a leap year whereas 1999 was not, and 1600, 2000 and 2400 are leap years but 1700, 1800, 1900 and 2100 are not.'
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
		if	(	!empty
			 	(	$attributes['value']
				)
			&&	preg_match
				(	'/[^-:0 ]/'
				,	$attributes['value']
				)
			):
			$value = $attributes['value'];
		else:
			$value = '';
		endif;
		
		unset
		(	$attributes['value']
		);
		if	(	$this->output == 'view'
			&&	!preg_match
				(	'/[1-9]/'
				,	$value
				)
			&&	(	$this->field->is_datetime
				 	(	'date'
					)
				||	strtolower
					(	$this->field->type
					)	==	'year'
				)
			):
			return '';
		else:
			$selector_template = $display_mask;
			$varsing = 0;
			$selectors = preg_split
			(	'/\$/'
			,	' '
				.	$selector_template
				.	' '
			);
			foreach
				(	$selectors as $key => $selector
				):
				if	(	$varsing
					):
					$varsing = 0;
				else:
					unset
					(	$selectors[$key]
					);
					$varsing = 1;
				endif;
			endforeach;
			reset
			(	$selectors
			);
			
			$selector_values = 
			$ignore_selectors = 
			array()
			;
			
			if	(	$this->output == 'filter'
				):
				$null_allowed = 1;
			endif;
			
			// CREATE DATE SELECTORS
			if	(	$this->field->is_datetime
					(	'date'
					)
				||	strtolower
					(	$this->field->type
					)	==	'year'
				):
				$selected_year = '0000';
				$selected_month = '00';
				$selected_day = '00';
				if	(	empty
						(	$value
						)
					):
					if	(	empty
							(	$default_value
							)
						):
						if	(	!$null_allowed
							):
							$selected_year = date
							(	'Y'
							);
							$selected_month = date
							(	'm'
							);
							$selected_day = date
							(	'd'
							);
						endif;
					else:
						if	(	preg_match
								(	'/[0-9]{4}-[0-9]{2}-[0-9]{2}/'
								,	$default_value
								)
							):
							$selected_year = substr
							(	$default_value
							,	0
							,	4
							);
							$selected_month = substr
							(	$default_value
							,	5
							,	2
							);
							$selected_day = substr
							(	$default_value
							,	8
							,	2
							);
						endif;				
					endif;
				else:
					if	(	preg_match
							(	'/[0-9]{4}-[0-9]{2}-[0-9]{2}/'
							,	$value
							)
						):
						$selected_year = substr
						(	$value
						,	0
						,	4
						);
						$selected_month = substr
						(	$value
						,	5
						,	2
						);
						$selected_day = substr
						(	$value
						,	8
						,	2
						);
					endif;
				endif;
				
				// YEAR SELECTOR
				$year_selector = 
				(	in_array
					(	'y'
					,	$selectors
					)
				)
				?	'y'
				:	'Y'
				;
				if	(	in_array
						(	$year_selector
						,	$selectors
						)
					):
					$year_select = 1;
				endif;
				
				if	(	empty
						(	$year_select
						)
					):
					if	(	$this->output != 'view'
						):
						// HIDDEN INPUT
						$selectors[] = $year_selector;
						$selector_template .= '$'
						.	$year_selector
						.	'$'
						;
						$hidden_name = 
						(	strtolower
							(	$this->field->type
							)	==	'year'
						)
						?	$attributes['name']
						:	$attributes['name']
							.	'-year'
						;
						$hidden_value = 
						(	$selected_year	> 0
						)
						?	$selected_year
						:	date
							(	'Y'
							)
						;
						$selector_values[$year_selector] = xhtml::hidden_input
						(	array
							(	'name'		=>	$hidden_name
							,	'value'		=>	$hidden_value
							)
						);
					endif;
				else:
					if	(	$this->output == 'view'
						):
						if	(	$selected_year > 0
							):
							$selector_values[$year_selector] = date
							(	$year_selector
							,	mktime
								(	0
								,	0
								,	0
								,	1
								,	1
								,	$selected_year
								)
							);
						endif;
					else:
						// DROPDOWN
						$select_options = '';
						if	(	$attributes['onchange']
							||	$null_allowed
							):
							$option_attributes = array
							(	'VALUE'		=>	'0000'
							);
							
							if	(	!($selected_year > 0
									)	
								):
								$option_attributes['SELECTED'] = '';
							endif;
							
							$select_options .= xhtml::element
							(	array
								(	'tag_name'		=>	'OPTION'
								,	'attributes'	=>	$option_attributes
								,	'content'		=>	'Year'
								)
							);
						endif;
						
						$year_options = array();
						for (	$o	=	$earliest_year
							;	$o	<=	$latest_year
							;	$o++
							):
							$year_options[] = str_pad
							(	$o
							,	4
							,	'0'
							,	STR_PAD_LEFT
							);
						endfor;
						if	(	$reverse_year_display
							):
							$year_options = array_reverse
							(	$year_options
							,	1
							);
						endif;
					
						foreach
							(	$year_options as $option	
							):
							$option_attributes = array
							(	'VALUE'		=>	$option
							);
							
							if	(	$selected_year > 0
								&&	$selected_year == $option
								):
								$option_attributes['SELECTED'] = '';
							endif;
							
							$option_text = 
							(	$year_selector == 'y'	
							)
							?	substr
								(	$option
								,	-2
								,	2
								)
							:	$option
							;
							
							$select_options .= xhtml::element
							(	array
								(	'tag_name'		=>	'OPTION'
								,	'attributes'	=>	$option_attributes
								,	'content'		=>	$option_text
								)
							);
						endforeach;
						
						$select_attributes = array();
						foreach
							(	$attributes as $attribute => $att_value	
							):
							switch
								(	$attribute	
								):
								case 'size':
								case 'maxlength':
									break;
								case 'name':
								case 'id':
									$select_attributes[$attribute] = 
									(	strtolower
										(	$this->field->type
										)	==	'year'
									)
									?	$att_value
									:	$att_value.'-year'
									;
									break;
								default:
									if	(	!empty
											(	$att_value
											)
										):
										$select_attributes[$attribute] = $att_value;
									endif;
							endswitch;
						endforeach;
						reset
						(	$attributes
						);
						
						if	(	$attributes['onchange']
							&&	!empty
								(	$value
								)
							&&	!empty
								(	$select_attributes['class']
								)
							):
							$select_attributes['class'] .= ' in_use';
						endif;
						
						$selector_values[$year_selector] = xhtml::element
						(	array
							(	'tag_name'		=>	'SELECT'
							,	'attributes'	=>	$select_attributes
							,	'content'		=>	$select_options
							)
						);
					endif;
				endif;
				
				if	(	strtolower($this->field->type) != 'year'
					):
					// MONTH SELECTOR
					$month_selectors = array
					(	'm - F'
					,	'n - F'
					,	'm - M'
					,	'n - M'
					,	'F'
					,	'M'
					,	'm'
					,	'n'
					);
					foreach
						(	$month_selectors	as	$month_selector	
						):
						if	(	in_array
								(	$month_selector
								,	$selectors
								)
							):
							$month_select = 1;
							break;
						endif;
					endforeach;
					
					if	(	empty
							(	$month_select
							)
						):
						if	(	$this->output != 'view'
							):
							// HIDDEN INPUT
							$selectors[] = $month_selector;
							$selector_template .= '$'.$month_selector.'$';
							$selector_values[$month_selector] = xhtml::hidden_input
							(	array
								(	'name'		=>	$attributes['name'].'-month'
								,	'value'		=>	'01'
								)
							);
						endif;
					else:
						if	(	$this->output == 'view'
							):
							if	(	$selected_month > 0
								):
								$selector_values[$month_selector] = date
								(	$month_selector
								,	mktime
									(	0
									,	0
									,	0
									,	$selected_month
									,	1
									,	date('Y')
									)
								);
							endif;
						else:
							// DROPDOWN
							$select_options = '';
							if	(	$attributes['onchange']
								||	$null_allowed
								):
								$option_attributes = array
								(	'VALUE'		=>	'00'
								);
								
								if	(	!($selected_month > 0
										)	
									):
									$option_attributes['SELECTED'] = '';
								endif;
								
								$select_options .= xhtml::element
								(	array
									(	'tag_name'	=>	'OPTION'
									,	'attributes'	=>	$option_attributes
									,	'content'		=>	'Month'
									)
								);
							endif;
							
							$month_options = array();
							for	(	$o = 1
								;	$o <= 12
								;	$o++
								):
								$month_options[] = str_pad
								(	$o
								,	2
								,	'0'
								,	STR_PAD_LEFT
								);
							endfor;
							
							foreach
								(	$month_options as $option
								):
								$option_attributes = array
								(	'VALUE'		=>	$option
								);
								
								if	(	$selected_month > 0
									&&	$selected_month == $option
									):
									$option_attributes['SELECTED'] = '';
								endif;
								
								switch
									(	$month_selector
									):
									case 'm':
										$option_text = $option;
										break;
									case 'n':
										$option_text = $option + 0;
										break;
									default:
										$option_text = date
										(	$month_selector
										,	mktime
											(	0
											,	0
											,	0
											,	$option
											,	1
											,	date
												(	'Y'
												)
											)
										);
								endswitch;
								
								$select_options .= xhtml::element
								(	array
									(	'tag_name'	=>	'OPTION'
									,	'attributes'	=>	$option_attributes
									,	'content'		=>	$option_text
									)
								);
							endforeach;
							
							$select_attributes = array();
							foreach
								(	$attributes as $attribute => $att_value
								):
								switch
									(	$attribute
									):
									case 'size':
									case 'maxlength':
										break;
									case 'name':
									case 'id':
										$select_attributes[$attribute] = $att_value.'-month';
										break;
									default:
										if	(	!empty
												(	$att_value
												)
											):
											$select_attributes[$attribute] = $att_value;
										endif;
								endswitch;
							endforeach;
							reset
							(	$attributes
							);
							
							if	(	$attributes['onchange']
								&&	!empty($value)
								&&	!empty($select_attributes['class'])
								):
								$select_attributes['class'] .= ' in_use';
							endif;
							
							$selector_values[$month_selector] = xhtml::element
							(	array
								(	'tag_name'	=>	'SELECT'
								,	'attributes'	=>	$select_attributes
								,	'content'		=>	$select_options
								)
							);
						endif;
					endif;
					
					
					// DAY SELECTOR
					$day_selector = 
					(	in_array
						(	'd'
						,	$selectors
						)
					)
					?	'd'
					:	'j'
					;
					$ordinal_selector = 'S';
					if	(	in_array
							(	$ordinal_selector
							,	$selectors
							)
						):
						$ordinal_select = 1;
					endif;
					
					if	(	!in_array
							(	$day_selector
							,	$selectors
							)
						):
						if	(	$this->output != 'view'
							):
							// HIDDEN INPUT
							$selectors[] = $day_selector;
							$selector_template .= '$'.$day_selector.'$';
							$selector_values[$day_selector] = xhtml::hidden_input
							(	array
								(	'name'		=>	$attributes['name'].'-day'
								,	'value'		=>	'01'
								)
							);
							
							if	(	!empty
									(	$month_select
									)
								&&	$force_end_of_month
								):
//								$GLOBALS['page']->scripts['src'][] = 'last_day_of_month';
								$GLOBALS['page']->scripts['ready'][] = 'last_day_of_month();';
							endif;
						endif;
					else:
						if	(	$this->output	==	'view'
							):
							if	(	$selected_day	>	0
								):
								$selector_values[$day_selector] = date
								(	$day_selector
								,	mktime
									(	0
									,	0
									,	0
									,	1
									,	$selected_day
									,	date('Y')
									)
								);
								if	(	!empty
										(	$ordinal_select
										)
									):
									$selector_values[$ordinal_selector] = date
									(	$ordinal_selector
									,	mktime
										(	0
										,	0
										,	0
										,	1
										,	$selected_day
										,	date('Y')
										)
									);
								endif;
							endif;
						else:
							$ignore_selectors[] = $ordinal_selector;
							
							// DROPDOWN
							$select_options = '';
							if	(	$attributes['onchange']
								||	$null_allowed
								):
								$option_attributes = array
								(	'VALUE'		=>	'00'
								);
								
								if	(	!($selected_day > 0)	
									):
									$option_attributes['SELECTED'] = '';
								endif;
								
								$select_options .= xhtml::element
								(	array
									(	'tag_name'	=>	'OPTION'
									,	'attributes'	=>	$option_attributes
									,	'content'		=>	'Day'
									)
								);
							endif;
							
							$day_options = array();
							for	(	$o	=	1
								;	$o	<=	31
								;	$o++
								):
								$day_options[] = str_pad
								(	$o
								,	2
								,	'0'
								,	STR_PAD_LEFT
								);
							endfor;
							
							foreach
								(	$day_options	as	$option
								):
								$option_attributes = array
								(	'VALUE'		=>	$option
								);
								
								if	(	$selected_day > 0
									&&	$selected_day == $option
									):
									$option_attributes['SELECTED'] = '';
								endif;
								
								$option_text = 
								(	$day_selector	==	'j'
								)
								?	$option + 0
								:	$option
								;
								
								if	(	!empty
										(	$ordinal_select
										)
									):
									$option_text .= date
									(	$ordinal_selector
									,	mktime
										(	0
										,	0
										,	0
										,	1
										,	$option
										,	date
											(	'Y'
											)
										)
									);
								endif;
								
								$select_options .= xhtml::element
								(	array
									(	'tag_name'	=>	'OPTION'
									,	'attributes'	=>	$option_attributes
									,	'content'		=>	$option_text
									)
								);
							endforeach;
							
							$select_attributes = array();
							foreach
								(	$attributes	as	$attribute	=>	$att_value
								):
								switch
									(	$attribute
									):
									case 'size':
									case 'maxlength':
										break;
									case 'name':
									case 'id':
										$select_attributes[$attribute] = $att_value.'-day';
										break;
									default:
										if	(	!empty
												(	$att_value
												)
											):
											$select_attributes[$attribute] = $att_value;
										endif;
								endswitch;
							endforeach;
							reset
							(	$attributes
							);
							
							if	(	$attributes['onchange']
								&&	!empty
									(	$value
									)
								&&	!empty
									(	$select_attributes['class']
									)
								):
								$select_attributes['class'] .= ' in_use';
							endif;
							
							$selector_values[$day_selector] = xhtml::element
							(	array
								(	'tag_name'	=>	'SELECT'
								,	'attributes'	=>	$select_attributes
								,	'content'		=>	$select_options
								)
							);
						endif;
					endif;
				endif;
			endif;
			
			
			// CREATE TIME SELECTORS
			if	(	$this->field->is_datetime
					(	'time'
					)
				&&	empty
					(	$attributes['onchange']
					)
				):
				$selected_hour = 
				$selected_minute = 
				$selected_seconds = 
				'00'
				;
				if	(	empty
						(	$value
						)
					):
					if	(	empty
							(	$default_value
							)
						):
						if	(	!$null_allowed
							):
							$selected_hour = date
							(	'H'
							);
							$selected_minute = date
							(	'i'
							);
							$selected_seconds = date
							(	's'
							);
						endif;
					else:
						if	(	strlen
								(	$default_value
								)	>	9
							):
							$default_value = substr
							(	$default_value
							,	11
							);
						endif;
						if	(	preg_match
								(	'/[0-9]{2}:[0-9]{2}:[0-9]{2}/'
								,	$default_value
								)
							):
							$selected_hour = substr
							(	$default_value
							,	0
							,	2
							);
							$selected_minute = substr
							(	$default_value
							,	3
							,	2
							);
							$selected_seconds = substr
							(	$default_value
							,	6
							,	2
							);
						endif;
					endif;
				else:
					if	(	strlen
							(	$value
							)	>	9
						):
						$value = substr
						(	$value
						,	11
						);
					endif;
					if	(	preg_match
							(	'/[0-9]{2}:[0-9]{2}:[0-9]{2}/'
							,	$value
							)
						):
						$selected_hour = substr
						(	$value
						,	0
						,	2
						);
						$selected_minute = substr
						(	$value
						,	3
						,	2
						);
						$selected_seconds = substr
						(	$value
						,	6
						,	2
						);
					endif;
				endif;
				
				// HOUR SELECTOR
				$hour_selectors = array
				(	'g'
				,	'h'
				,	'G'
				,	'H'
				);
				$meridian_selectors = array
				(	'A'
				,	'a'
				);
				foreach
					(	$hour_selectors as $hour_selector
					):
					if	(	in_array
							(	$hour_selector
							,	$selectors
							)
						):
						$hour_select = 1;
						break;
					endif;
				endforeach;
				foreach
					(	$meridian_selectors as $meridian_selector
					):
					if	(	in_array
							(	$meridian_selector
							,	$selectors
							)
						):
						$meridian_select = 1;
						break;
					endif;
				endforeach;
				
				if	(	empty
						(	$hour_select
						)
					):
					if	(	$this->output != 'view'
						):
						// HIDDEN INPUT
						$selectors[] = $hour_selector;
						$selector_template .= '$'.$hour_selector.'$';
						$selector_values[$hour_selector] = xhtml::hidden_input
						(	array
							(	'name'		=>	$attributes['name'].'-hour'
							,	'value'		=>	'00'
							)
						);
					endif;
				else:
					if	(	$this->output == 'view'
						):
						$selector_values[$hour_selector] = date
						(	$hour_selector
						,	mktime
							(	$selected_hour
							,	0
							,	0
							,	1
							,	1
							,	date
								(	'Y'
								)
							)
						);
						if	(	!empty
								(	$meridian_select
								)
							):
							$selector_values[$meridian_selector] = date
							(	$meridian_selector
							,	mktime
								(	$selected_hour
								,	0
								,	0
								,	1
								,	1
								,	date
									(	'Y'
									)
								)
							);
						endif;
					else:
						array_push
						(	$ignore_selectors
						,	'a'
						,	'A'
						);
						
						// DROPDOWN
						$select_options = '';
						$option_attributes = array
						(	'VALUE'		=>	'00'
						);
						
						if	(	!(	$selected_hour > 0
								)	
							):
							$option_attributes['SELECTED'] = '';
						endif;
						
						switch
							(	$hour_selector
							):
							case 'g':
							case 'h':
								$option_text = '12 ';
								$option_text .= 
								(	$meridian_selector == 'A'
								)
								?	'AM'
								:	'am'
								;
								break;
							case 'G':
								$option_text = 0;
								break;
							default: // case 'H':
								$option_text = '00';
						endswitch;
						
						$select_options .= xhtml::element
						(	array
							(	'tag_name'	=>	'OPTION'
							,	'attributes'	=>	$option_attributes
							,	'content'		=>	$option_text
							)
						);
						
						$hour_options = array();
						for (	$o = 1
							;	$o < 24
							;	$o++
							):
							$hour_options[] = str_pad
							(	$o
							,	2
							,	'0'
							,	STR_PAD_LEFT
							);
						endfor;
						
						foreach
							(	$hour_options as $option	
							):
							$option_attributes = array
							(	'VALUE'		=>	$option
							);
							
							if	(	$selected_hour > 0
								&&	$selected_hour == $option
								):
								$option_attributes['SELECTED'] = '';
							endif;
							
							$option_text = $option;
							switch
								(	$hour_selector
								):
								case 'g':
									$option_text = 
									(	$option
									+	0
									);
								case 'h':
									$option_text .= ' ';
									$meridian = 
									(	$option < 12
									)
									?	'am'
									:	'pm'
									;
									$option_text .= 
									(	$meridian_selector == 'A'
									)
									?	strtoupper
										(	$meridian
										)
									:	$meridian
									;
									break;
								case 'G':
									$option_text = $option + 0;
									break;
								default: // case 'H':
									$option_text = $option;
							endswitch;
							
							$select_options .= xhtml::element
							(	array
								(	'tag_name'	=>	'OPTION'
								,	'attributes'	=>	$option_attributes
								,	'content'		=>	$option_text
								)
							);
						endforeach;
						
						$select_attributes = array();
						foreach
							(	$attributes as $attribute => $att_value
							):
							switch
								(	$attribute	
								):
								case 'size':
								case 'maxlength':
									break;
								case 'name':
								case 'id':
									$select_attributes[$attribute] = $att_value.'-hour';
									break;
								default:
									if	(	!empty
											(	$att_value
											)
										):
										$select_attributes[$attribute] = $att_value;
									endif;
							endswitch;
						endforeach;
						reset
						(	$attributes
						);
						
						if	(	$attributes['onchange']
							&&	!empty
								(	$value
								)
							&&	!empty
								(	$select_attributes['class']
								)
							):
							$select_attributes['class'] .= ' in_use';
						endif;
						
						$selector_values[$hour_selector] = xhtml::element
						(	array
							(	'tag_name'	=>	'SELECT'
							,	'attributes'	=>	$select_attributes
							,	'content'		=>	$select_options
							)
						);
					endif;
				endif;
				
				
				// MINUTES SELECTOR
				$minute_selector = 'i';
				
				if	(	!in_array
						(	$minute_selector
						,	$selectors
						)
					):
					if	(	$this->output != 'view'
						):
						// HIDDEN INPUT
						$selectors[] = $minute_selector;
						$selector_template .= '$'.$minute_selector.'$';
						$selector_values[$minute_selector] = xhtml::hidden_input
						(	array
							(	'name'		=>	$attributes['name'].'-minute'
							,	'value'		=>	'00'
							)
						);
					endif;
				else:
					if	(	$this->output == 'view'
						):
						$selector_values[$minute_selector] = date
						(	$minute_selector
						,	mktime
							(	0
							,	$selected_minute
							,	0
							,	1
							,	1
							,	date
								(	'Y'
								)
							)
						);
					else:
						// DROPDOWN
						$select_options = '';
						$option_attributes = array
						(	'VALUE'		=>	'00'
						);
						
						if	(	!(	$selected_minute > 0
								)
							):
							$option_attributes['SELECTED'] = '';
						endif;
						
						$select_options .= xhtml::element
						(	array
							(	'tag_name'	=>	'OPTION'
							,	'attributes'	=>	$option_attributes
							,	'content'		=>	'00'
							)
						);
						
						$minute_options = array();
						for (	$o = 1
							;	$o < 60
							;	$o++
							):
							$minute_options[] = str_pad
							(	$o
							,	2
							,	'0'
							,	STR_PAD_LEFT
							);
						endfor;
						
						foreach
							(	$minute_options as $option
							):
							$option_attributes = array
							(	'VALUE'		=>	$option
							);
							
							if	(	$selected_minute > 0
								&&	$selected_minute == $option
								):
								$option_attributes['SELECTED'] = '';
							endif;
							
							$select_options .= xhtml::element
							(	array
								(	'tag_name'	=>	'OPTION'
								,	'attributes'	=>	$option_attributes
								,	'content'		=>	$option
								)
							);
						endforeach;
						
						$select_attributes = array();
						foreach
							(	$attributes as $attribute => $att_value	
							):
							switch
								(	$attribute	
								):
								case 'size':
								case 'maxlength':
									break;
								case 'name':
								case 'id':
									$select_attributes[$attribute] = $att_value.'-minute';
									break;
								default:
									if	(	!empty
											(	$att_value
											)
										):
										$select_attributes[$attribute] = $att_value;
									endif;
							endswitch;
						endforeach;
						reset
						(	$attributes
						);
						
						if	(	$attributes['onchange']
							&&	!empty
								(	$value
								)
							&&	!empty
								(	$select_attributes['class']
								)
							):
							$select_attributes['class'] .= ' in_use';
						endif;
						
						$selector_values[$minute_selector] = xhtml::element
						(	array
							(	'tag_name'	=>	'SELECT'
							,	'attributes'	=>	$select_attributes
							,	'content'		=>	$select_options
							)
						);
					endif;
				endif;
				
				
				// SECONDS SELECTOR
				$seconds_selector = 's';
				
				if	(	!in_array
						(	$seconds_selector
						,	$selectors
						)
					):
					if	(	$this->output != 'view'
						):
						// HIDDEN INPUT
						$selectors[] = $seconds_selector;
						$selector_template .= '$'.$seconds_selector.'$';
						$selector_values[$seconds_selector] = xhtml::hidden_input
						(	array
							(	'name'		=>	$attributes['name'].'-second'
							,	'value'		=>	'00'
							)
						);
					endif;
				else:
					if	(	$this->output == 'view'
						):
						$selector_values[$seconds_selector] = date
						(	$seconds_selector
						,	mktime
							(	0
							,	0
							,	$selected_seconds
							,	1
							,	1
							,	date
								(	'Y'
								)
							)
						);
					else:
						// DROPDOWN
						$select_options = '';
						$option_attributes = array
						(	'VALUE'		=>	'00'
						);
						
						if	(	!(	$selected_seconds > 0
								)	
							):
							$option_attributes['SELECTED'] = '';
						endif;
						
						$select_options .= xhtml::element
						(	array
							(	'tag_name'	=>	'OPTION'
							,	'attributes'	=>	$option_attributes
							,	'content'		=>	'00'
							)
						);
						
						$seconds_options = array();
						for	(	$o = 1
							;	$o < 60
							;	$o++
							):
							$seconds_options[] = str_pad
							(	$o
							,	2
							,	'0'
							,	STR_PAD_LEFT
							);
						endfor;
						
						foreach
							(	$seconds_options as $option	
							):
							$option_attributes = array
							(	'VALUE'		=>	$option
							);
							
							if	(	$selected_seconds > 0
								&&	$selected_seconds == $option
								):
								$option_attributes['SELECTED'] = '';
							endif;
							
							$select_options .= xhtml::element
							(	array
								(	'tag_name'	=>	'OPTION'
								,	'attributes'	=>	$option_attributes
								,	'content'		=>	$option
								)
							);
						endforeach;
						
						$select_attributes = array();
						foreach
							(	$attributes as $attribute => $att_value
							):
							switch
								(	$attribute
								):
								case 'size':
								case 'maxlength':
									break;
								case 'name':
								case 'id':
									$select_attributes[$attribute] = $att_value.'-second';
									break;
								default:
									if	(	!empty
											(	$att_value
											)
										):
										$select_attributes[$attribute] = $att_value;
									endif;
							endswitch;
						endforeach;
						reset
						(	$attributes
						);
						
						if	(	$attributes['onchange']
							&&	!empty
								(	$value
								)
							&&	!empty
								(	$select_attributes['class']
								)
							):
							$select_attributes['class'] .= ' in_use';
						endif;
						
						$selector_values[$seconds_selector] = xhtml::element
						(	array
							(	'tag_name'	=>	'SELECT'
							,	'attributes'	=>	$select_attributes
							,	'content'		=>	$select_options
							)
						);
					endif;
				endif;
			endif;
			
			foreach
				(	$selectors as $selector
				):
				$selector_template = 
				(	!isset
					(	$selector_values[$selector]
					)
				||	in_array
					(	$selector
					,	$ignore_selectors
					)
				)
				?	str_replace
					(	'$'
						.	$selector
						.	'$'
					,	''
					,	$selector_template
					)
				:	str_replace
					(	'$'
						.	$selector
						.	'$'
					,	$selector_values[$selector]
					,	$selector_template
					)
				;
			endforeach;
			
			return $selector_template;
		endif;
	}
	
	function render_enum
	(	$args	=	array()
	)
	{	extract
		(	debug::function_argument_verify
			(	array
				(	'function'			=>	__CLASS__.'->'.__FUNCTION__
				,	'arguments_input'		=>	$args
				,	'arguments_descriptions'	=>	array
					(	'options'				=> array
						(	'blurb'				=>	'Numerically keyed array containing list of available values from db field type string.'
						,	'default_value'		=>	array()
						)
					,	'input_type'			=> array
						(	'possible_values'		=>	array
							(	'auto'
							,	'radio'
							,	'select'
							)
						,	'default_value'		=>	'auto'
						)
					,	'default_option'		=> array
						(	'default_value'		=>	$this->field->default_value
						)
					,	'option_sort_functions'	=> array
						(	'blurb'				=>	'Numerically keyed array containing list of sort functions, in the order they are to be run on the submitted options array.'
						,	'default_value'	=>	array
							(	'natcasesort'
							)
						)
					,	'null_allowed'			=> array
						(	'possible_values'		=>	array
							(	0
							,	1
							)
						,	'default_value'		=>	$this->field->null_allowed
						)
					,	'empty_option_text'		=> array
						(	'blurb'				=>	'The text to display for an empty-value option, if such an option is present.'
						,	'default_value'		=>	''
						)
					,	'display_columns'		=>	array
						(	'default_value'			=>	1
						)
					)
				)
			)
		);

		if	(	empty
				(	$default_option
				)
			):
			$default_option = 0;
		endif;
		
		if	(	empty
				(	$empty_option_text
				)
			):
			$empty_option_text = $this->title;
			if	(	$this->output == 'filter'	
				):
				$how_many = 'All';
				$empty_option_text = strings::pluralize
				(	$empty_option_text
				);
			else:
				$how_many = 'No';
			endif;
			$empty_option_text = '['
			.	$how_many
			.	' '
			.	$empty_option_text
			.	']'
			;
		endif;
		
		if	(	!empty
				(	$this->attributes['value']
				)
			):
			$value = $this->attributes['value'];
			unset
			(	$this->attributes['value']
			);
		else:
			$value = '';
		endif;
		
		if	(	$this->output	==	'filter'
			):
			$null_allowed = 1;
			$default_option = '';
/*
			$this->attributes['onclick'] = 'document.'
			.	$this->form
			.	'.submit()'
			;
*/
		endif;
		
		if	(	empty
				(	$options
				)
			):
			$options = $this->explode_options_from_type_string
			(	$this->field->type_string
			);
		else:
			if	(	!is_array
					(	$options
					)
				):
				$options = explode
				(	','
				,	$options
				);
			endif;
		endif;

		// DISPLAY FULL STATE AND COUNTRY NAMES AS OPTIONS
		if	(	in_array
				(	$this->name
				,	array
					(	'state'
					,	'country'
					)
				)
			&&	!empty
				(	$GLOBALS['dbi']->tables[$this->name]
				)
			):
			if	(	empty
					(	$GLOBALS['page']->arrays[$this->name]
					)
				):
				$options = 
				$GLOBALS['page']->arrays[$this->name] = 
				$GLOBALS['dbi']->get_result_array
				(	array
					(	'table'		=>	$this->name
					,	'fields'	=>	array
						(	'title'
						)
					,	'equals'	=>	array
						(	'status'	=>	'Active'
						)
					,	'in'		=>	array
						(	'code'		=>	$options
						)
					,	'key_by'	=>	'code'
					)
				)
				;
			else:
				$options = $GLOBALS['page']->arrays[$this->name];
			endif;
		endif;

		if	(	$this->output == 'view'
			):
			return $value;
/*
			return
			(	isset
				(	$options[$value]
				)
			)
			?	$options[$value]
			:	$value
			;
*/
		else:
			$enumptions = array();
			foreach
				(	$options	as	$option_value	=>	$option_content	
				):
				if	(	is_numeric
						(	$option_value
						)
					):
					$enumptions[$option_content] = $option_content;
				else:
					$enumptions[$option_value] = $option_content;
				endif;
			endforeach;
			$options = $enumptions;
			$option_count = count
			(	$options
			);

// KLUDGERY TO INCLUDE JAVASCRIPT VISIBILITY TOGGLE FOR DIFFERENT OWNABLE RECORDS DEPENDENT ON SELECTED CONTENT TYPE
			if	(	$this->attributes['name']	==	'content_block_1-content_type'
				):
				$this->attributes['onclick'] = 'switch_content($(\'input[type=radio][name=content_block_1-content_type]:checked\').val());';
				
				$GLOBALS['page']->scripts['functions'][] = 'var $content_types_A = new Array();
$content_types_A[1] = "Twitter";
$content_types_A[2] = "Instagram";
$content_types_A[3] = "Tumblr";
$content_types_A[4] = "Pinterest";

var $content_types_B = new Array();
$content_types_B[1] = "tweet_selectors";
$content_types_B[2] = "instagram_selectors";
$content_types_B[3] = "tumblr_selectors";
$content_types_B[4] = "pinterest_selectors";

function switch_content
(	$show_only
)	
{	for	(	var ct in $content_types_A
		)
	{	if	(	$content_types_A[ct] == $show_only
			)
		{	$("#"+$content_types_B[ct]).show();
		}
		else
		{	$("#"+$content_types_B[ct]).hide();
		}
	}
}';

				$GLOBALS['page']->scripts['ready'][] = 'switch_content($("input[type=radio][name=content_block_1-content_type]:checked").val());';
				
			endif;
// END KLUDGERY				
			
			
			$element_attributes = array();
			foreach
				(	$this->attributes	as	$attribute	=>	$att_value	
				):
				switch
					(	$attribute	
					):
					case 'class':
						$element_attributes[$attribute] = 
						(	!empty
							(	$value
							)
						&&	!empty
							(	$this->attributes['onchange']
							)
						&&	strpos
							(	$this->attributes['onchange']
							,	'.submit()'
							)
						)
						?	$att_value
							.	' in_use'
						:	$att_value
						;
						break;
					case 'size':
					case 'maxlength':
						break;
					default:
						if	(	!empty
								(	$att_value
								)
							):
							$element_attributes[$attribute] = $att_value;
						endif;
				endswitch;
			endforeach;
			reset
			(	$this->attributes
			);
			
			switch
				(	$input_type	
				):
				case 'radio':
					if	(	$null_allowed
						):
						$input_type = 'select';
					endif;
					break;
				case 'select':
									
					break;
				default: // case 'auto':
					$input_type = 
					(	(	$this->output == 'filter'
						&&	$option_count > 2
						)
					||	(	$this->output == 'edit'
						&&	$option_count > 3
						
						&&	$this->attributes['name']	!=	'content_block_1-content_type'

						)
					||	$null_allowed
					)
					?	'select'
					:	'radio'
					;
/*
					if	(	$option_count	<=	3
						):
						$display_columns = $option_count;
					endif;
*/
			endswitch;
			
			if	(	$this->output == 'edit'
				&&	$input_type == 'radio'
				&&	$option_count == 1
				&&	$null_allowed
				):
				$input_type = 'checkbox';
			endif;
			
			if	(	$input_type != 'select'
				&&	$option_count <= 3
				):
				$display_columns = $option_count;
			endif;
			
			if	(	$input_type != 'filter'
				&&	!empty
					(	$this->field->force_input_type
					)
				):
				$force_input_type = strtolower
				(	$this->field->force_input_type
				);
				switch
					(	$force_input_type
					):
					case 'select':
					case 'radio':
					case 'checkbox':
						$input_type = $force_input_type;
						break;
				endswitch;
			endif;
			
			switch
				(	$input_type	
				):
				case 'checkbox':
					return xhtml::checkbox_group
					(	array
						(	'options'				=>	$options
						,	'checkbox_type'			=>	$input_type
						,	'selected_options'		=>	array
							(	$value
							)
						,	'option_sort_functions'	=>	$option_sort_functions
						,	'null_allowed'			=>	$null_allowed
						,	'attributes'			=>	$element_attributes
						,	'display_columns'		=>	$display_columns
						)
					);
					break;
				case 'radio':								
					return xhtml::checkbox_group
					(	array
						(	'options'				=>	$options
						,	'checkbox_type'			=>	$input_type
						,	'default_option'		=>	array
							(	$default_option			=>	$empty_option_text
							)
						,	'selected_options'		=>	array
							(	$value
							)
						,	'option_sort_functions'	=>	$option_sort_functions
						,	'null_allowed'			=>	$null_allowed
						,	'attributes'			=>	$element_attributes
						,	'display_columns'		=>	$display_columns
						)
					);
					break;
				case 'select':
					return xhtml::select_element
					(	array
						(	'options'				=>	$options
						,	'default_option'		=>	array
							(	$default_option			=>	$empty_option_text
							)
						,	'selected_option'		=>	$value
						,	'option_sort_functions'	=>	$option_sort_functions
						,	'null_allowed'			=>	$null_allowed
						,	'attributes'			=>	$element_attributes
						)
					);
					break;
			endswitch;
		endif;
	}
	
	function render_folder_selector
	(	$args	=	array()
	)
	{	extract
		(	debug::function_argument_verify
			(	array
				(	'function'			=>	__CLASS__.'->'.__FUNCTION__
				,	'arguments_input'		=>	$args
				,	'arguments_descriptions'	=>	array
					(	'default_option'		=> array
						(	'default_value'		=>	$this->field->default_value
						)
					,	'null_allowed'			=> array
						(	'possible_values'		=>	array
							(	0
							,	1
							)
						,	'default_value'		=>	$this->field->null_allowed
						)
					,	'empty_option_text'		=> array
						(	'blurb'				=>	'The text to display for an empty-value option, if such an option is present.'
						,	'default_value'		=>	'[No folder]'
						)
					)
				)
			)
		);
		
		if	(	empty
				(	$default_option
				)
			):
			$default_option = 0;
		endif;
		
		$project_path = $GLOBALS['page']->path['project_root'];
		if	(	strrpos
				(	$project_path
				,	'/'
				)
				==	strlen
				(	$project_path
				)
				-	1
			):
			$project_path = substr
			(	$project_path
			,	0
			,	-1
			);
		endif;
		
		$elemeat = '<p>'
		.	$project_path
		.	'/</p><input type="hidden" name="'
		.	$this->attributes['name']
		.	'" id="'
		.	$this->attributes['id']
		.	'" value="'
		.	$this->attributes['value']
		.	'" />'
		;

		$raw_options = files::in_dir
		(	array
			(	'path'				=>	$project_path
			,	'ignore_entries'	=>	array
				(	'^\..*'
				,	substr
					(	$GLOBALS['page']->path['web_root']
					,	0
					,	-1
					)
				)
			,	'tree_only'			=>	1
			,	'recursion'			=>	true
			)
		);
		
		$drill_down_value = 
		(	empty
			(	$this->attributes['value']
			)
		)
		?	''
		:	substr
			(	$this->attributes['value']
			,	strlen
				(	$project_path
				)
				+	1
			)
		;	
		
		$selectors = $this->render_folder_selectors
		(	array
			(	'folder_tree'		=>	$raw_options
			,	'selected_value'	=>	$drill_down_value
			)
		);
		foreach
			(	$selectors	as	$level	=>	$selector
			):
			$elemeat .= '<div class="folder_drilldown_level folder_drilldown_level_'
			.	$level
			.	'">'
			.	$selector
			.	'</div>'
			;
		endforeach;
	
		if	(	!empty
				(	$blurb
				)
			):
			$elemeat_class = 
			(	empty
				(	$this->field->dud
				)
			)
			?	'rec_fld_blr'
			:	'rec_fld_blr highlight_dud'
			;
			$elemeat .= xhtml::element
			(	array
				(	'tag_name'		=>	'SPAN'
				,	'attributes'	=>	array
					(	'CLASS'		=>	$elemeat_class
					,	'STYLE'		=>	'white-space:nowrap;clear:all;'
					)
				,	'content'		=>	$blurb
				)
			)
			.	'<br />'
			;			
		endif;
		
		$GLOBALS['page']->scripts['ready'][] = '
$project_root = "'.$project_path.'/";
$(".folder_drilldown").change
(	function()
	{	$level = $(this).children(":first").val() - 0;
		$level++;
		$next = $(this).val().substring(0,$(this).val().length-1);
		$next_selector = "#'.$this->attributes['name'].'_"+$level+"_"+$next.replace(/ /g,"_");
		if	(	$($next_selector).length
			)
		{	$(".folder_drilldown_level_"+$level+" select").hide();
			$(".folder_drilldown_level_"+$level+" select").val($level);
			if	(	$(this).val() != parseInt($(this).val())
				)
			{	$($next_selector).slideDown("slow");
			}
		}
		
		$("#'.$this->attributes['id'].'").val($project_root);
		$(".folder_drilldown:visible").each
		(	function(i)
			{	if	(	$(this).val() != parseInt($(this).val())
					)
				{	$("#'.$this->attributes['id'].'").val($("#'.$this->attributes['id'].'").val()+$(this).val());
				}
			}
		);
	}
);		
';
		
		return $elemeat;
		
	}
	
	function render_folder_selectors
	(	$args	=	array()
	)
	{	extract
		(	debug::function_argument_verify
			(	array
				(	'function'			=>	__CLASS__.'->'.__FUNCTION__
				,	'arguments_input'		=>	$args
				,	'arguments_descriptions'	=>	array
					(	'folder_tree'
					,	'level'					=>	array
						(	'default_value'			=>	0
						)
					,	'selected_value'		=>	array
						(	'default_value'			=>	0
						)
					,	'name'					=>	array
						(	'default_value'			=>	''
						)
					)
				)
			)
		);

		$options = 
		$sub_options = 
		$selected_options = 
		array()
		;
		
		foreach
			(	$folder_tree	as	$possible_path	=>	$subfolders
			):
			$safe_name = str_replace
			(	' '
			,	'_'
			,	$possible_path
			);
			$possible_path .= '/';
			$options[$possible_path] = $possible_path;
			if	(	!empty
					(	$selected_value
					)
				&&	strstr
					(	$selected_value
					,	$possible_path
					)	==	$selected_value
				):
				$selected_options	=	array
				(	$possible_path
				);
			endif;
			if	(	!empty
					(	$subfolders
					)
				):
				$subfolder_options = $this->render_folder_selectors
				(	array
					(	'folder_tree'		=>	$subfolders
					,	'level'				=>	$level
						+	1
					,	'selected_value'	=>	substr
						(	$selected_value
						,	strpos
							(	$selected_value
							,	'/'
							)
							+	1
						)
					,	'name'				=>	$safe_name
					)
				);
				foreach
					(	$subfolder_options	as	$sub_level	=>	$sub_option
					):
					$sub_options[$sub_level] .= $sub_option;
				endforeach;
			endif;
		endforeach;
		
		$this_name_id = $this->attributes['name']
		.	'_'
		.	$level
		.	'_'
		.	$name
		;
		$these_attributes = array
		(	'class'					=>	$this->attributes['class']
			.	' folder_drilldown'
		,	'name'					=>	$this_name_id
		,	'id'					=>	$this_name_id
		);
		if	(	$level
			&&	empty
				(	$selected_options
				)
			):
			$these_attributes['style'] = "display:none;";
		endif;

		$options = array
		(	$level	=>	xhtml::select_element
			(	array
				(	'options'				=>	$options
				,	'default_option'		=>	array
					(	$level					=>	'[&lt;&lt; USE PARENT FOLDER]'
					)
				,	'selected_options'		=>	$selected_options
				,	'option_sort_functions'	=>	array
					(
					)
				,	'null_allowed'			=>	1
				,	'attributes'			=>	$these_attributes
				)
			)
		);
		foreach
			(	$sub_options	as	$sub_level	=>	$sub_option
			):
			$options[$sub_level] .= $sub_option;
		endforeach;

		return $options;
	}

	function render_foreign_key
	(	$args	=	array()
	)
	{	extract
		(	debug::function_argument_verify
			(	array
				(	'function'			=>	__CLASS__.'->'.__FUNCTION__
				,	'arguments_input'		=>	$args
				,	'arguments_descriptions'	=>	array
					(	'fields_display_mask'	=>	array
						(	'default_value'			=>	'$first_text_field$'
						)
					,	'order_by'				=>	array
						(	'default_value'			=>	array
							(	'title'			=>	''
							,	'name'			=>	''
							,	'subject'		=>	''
							,	'last_name'		=>	''
							,	'first_name'	=>	''
							)
						)
					,	'input_type'			=> array
						(	'possible_values'		=>	array
							(	'checkbox'
							,	'select'
							)
						,	'default_value'		=>	'select'
						)
					,	'default_option'		=> array
						(	'default_value'		=>	$this->field->default_value
						)
					,	'null_allowed'			=> array
						(	'possible_values'		=>	array
							(	0
							,	1
							)
						,	'default_value'		=>	$this->field->null_allowed
						)
					,	'empty_option_text'		=> array
						(	'blurb'				=>	'The text to display for an empty-value option, if such an option is present.'
						,	'default_value'		=>	'[All]'
						)
					)
				)
			)
		);
		
		if	(	empty
				(	$default_option
				)
			):
			$default_option = 0;
		endif;
		
		if	(	$this->output == 'view'
			||	(	$this->output == 'edit'
				&&	!$this->field->edit_allowed
				)
			):
			$cell = '';
			if	(	!empty
					(	$this->attributes['value']
					)
				):

				if	(	$GLOBALS['dbi']->tables[$this->field->foreign_table]->is_node_table
					):
					$root_node_id = $GLOBALS['dbi']->get_result_array
					(	array
						(	'table'	=>	$this->field->foreign_table
						,	'fields'	=>	array
							(	'id'
							)
//						,	'equals'	=>	array
//							(	'status'	=>	'Active'
//							)
						,	'where'		=>	"	LOWER(title)	=	'".strings::pluralize($this->field->foreign_table)."'
							"
						,	'limit'		=>	1
						,	'pop_single_row'	=>	1
						)
					);
				endif;
				
				if	(	!empty
						(	$root_node_id
						)
					):
					$this->tree = new node_tree
					(	array
						(	'owner_node'	=>	$root_node_id
						,	'node_table'	=>	$this->field->foreign_table
						,	'depth'			=>	0
						)
					);
					
					$options = $GLOBALS['page']->render_menu
					(	array
						(	'menu_name'				=>	strings::pluralize
							(	$this->field->foreign_table
							)
						,	'node_table'			=>	$this->field->foreign_table
						,	'return_as'				=>	'options'
						)
					);
					
					$result = array();
					$id_values = explode
					(	','
					,	$this->attributes['value']
					);
					foreach
						(	$id_values	as	$option_id
						):
						$result[$option_id] = $options[$option_id];
					endforeach;
					unset
					(	$options
					);
				else:
					$result = $GLOBALS['dbi']->get_result_array
					(	array
						(	'table'		=>	$this->field->foreign_table
						,	'in'		=>	array
							(	$this->field->foreign_key_field		=>	explode
								(	','
								,	$this->attributes['value']
								)
							)
						)
					);
				endif;
				
				foreach
					(	$result	as	$owner_id	=>	$owner_info	
					):					
					$owner =
					(	is_array
					 	(	$owner_info
						)
					)
					?	$GLOBALS['dbi']->tables[$this->field->foreign_table]->render_record_title
						(	$owner_info
						)
					:	$owner_info	
					;
					permission::initialize
					(	$this->field->foreign_table
					);
					$cell .= 
					(	permission::evaluate
						(	$GLOBALS['dbi']->tables[$this->field->foreign_table]->permissions['view_records_if']
						)
					)
					?	xhtml::element
						(	array
							(	'tag_name'	=>	'A'
							,	'attributes'	=>	array
								(	'HREF'		=>	uri::generate
									(	array
										(	'query'	=>	array
											(	'z'		=>	$this->field->foreign_table
											,	'id'	=>	$owner_id
											)
											,	'query_crypt_key'	=>	$GLOBALS['page']->url_crypt_key
										)
									)
								)
							,	'content'		=>	$owner
							)
						)
					:	$owner
					;
					$cell .= '<br />';
				endforeach;
			endif;
			// APPROVAL BUTTON KLUDGE
			if	(	$this->field->name	==	'approved_by'
				&&	empty
					(	$cell
					)
				):
				$use_record = 
				(	empty
					(	$GLOBALS['dbi']->tables[$this->field->table]->record->values['id']
					)
				)
				?	$GLOBALS['dbi']->tables[$this->field->table]->template->record
				:	$GLOBALS['dbi']->tables[$this->field->table]->record->values
				;
				
				if	(	$use_record['status'] == 'Active'	
					):
				
					$approve_link = uri::generate
					(	array
						(	'query'	=>	array
							(	'z'		=>	'approve'
							,	't'		=>	$this->field->table
							,	'id'	=>	$use_record['id']
							)
						,	'query_crypt_key'	=>	$GLOBALS['page']->url_crypt_key
						)
					);
							
					$approve_anchor = xhtml::element
					(	array
						(	'tag_name'	=>	'A'
						,	'attributes'	=>	array
							(	'HREF'			=>	$approve_link
							,	'CLASS'			=>	'yes'
							)
						,	'content'		=>	'Approve'
						)
					);
	
					$reject_link = uri::generate
					(	array
						(	'query'	=>	array
							(	'z'		=>	'reject'
							,	't'		=>	$this->field->table
							,	'id'	=>	$use_record['id']
							)
						,	'query_crypt_key'	=>	$GLOBALS['page']->url_crypt_key
						)
					);
							
					$reject_anchor = xhtml::element
					(	array
						(	'tag_name'	=>	'A'
						,	'attributes'	=>	array
							(	'HREF'			=>	$reject_link
							,	'CLASS'			=>	'no'
							)
						,	'content'		=>	'Reject'
						)
					);
	
					$cell = xhtml::element
					(	array
						(	'tag_name'	=>	'DIV'
						,	'attributes'	=>	array
							(	'ID'			=>	$this->form
								.	'.approve_'
								.	$use_record['id']
							,	'CLASS'		=>	'record_button'
							)
						,	'content'		=>	$approve_anchor
							.	'&nbsp;'
							.	$reject_anchor
						)
					);
					
				endif;
			endif;
			return $cell;
		else:
			// FIND OUT WHICH TABLES CAN OWN RECORDS IN THE FOREIGN TABLE
			$GLOBALS['dbi']->tables[$this->field->foreign_table]->get_ownerships();
			$owned_selectables = array();
			if	(	empty
					(	$GLOBALS['dbi']->tables[$this->field->foreign_table]->owners
					)
				):
				foreach
					(	$GLOBALS['user']->owners	as	$ownership_id	=>	$user_owners
					):
					if	(	$GLOBALS['dbi']->ownerships[$ownership_id]['owner_table'] == $this->field->foreign_table
						):
						$owned_selectables = array_merge
						(	$owned_selectables
						,	array_keys
							(	$user_owners
							)
						);
					endif;
				endforeach;
				reset
				(	$GLOBALS['user']->owners
				);
			else:
				foreach
					(	$GLOBALS['dbi']->tables[$this->field->foreign_table]->owners	as	$owner_name	=>	$ownership_id
					):
					$owner_ids = array();
					if	(	!empty
							(	$GLOBALS['dbi']->tables['user']->owners[$owner_name]
							)
						&&	!empty
							(	$GLOBALS['user']->owners[$GLOBALS['dbi']->tables['user']->owners[$owner_name]]
							)
						):
						$owner_ids = array_keys
						(	$GLOBALS['user']->owners[$GLOBALS['dbi']->tables['user']->owners[$owner_name]]
						);
					else:
						if	(	$GLOBALS['dbi']->ownerships[$ownership_id]['owner_table'] == 'user'
							&&	!empty
								(	$GLOBALS['user']->id
								)
//// SHOULD THIS BE RELATED TO ADMIN PRIVILEGES INSTEAD OF TITLED RELATIONSHIPS?								
							&&	empty
								(	$GLOBALS['dbi']->ownerships[$ownership_id]['owner_title']
								)
							):
							$owner_ids[] = $GLOBALS['user']->id;
						endif;
					endif;
					if	(	!empty
							(	$owner_ids
							)
						):
						$result = $GLOBALS['dbi']->get_result
						(	"	SELECT	owned_id
								FROM	owned
								WHERE	ownership_id	=	$ownership_id
								AND		owner_id		IN	(".implode(',',$owner_ids).")
								ORDER					BY	owned_id
							"
						);
						while
							(	$row	=	$result->fetch_array
							 	(	MYSQLI_ASSOC
								)
							):
							$owned_selectables[] = $row['owned_id'];
						endwhile;
					endif;
				endforeach;
				reset
				(	$GLOBALS['dbi']->tables[$this->field->foreign_table]->owners
				);
			endif;
//			
			if	(	$GLOBALS['dbi']->tables[$this->field->foreign_table]->is_node_table
				):
				$root_node_id = $GLOBALS['dbi']->get_result_array
				(	array
				 	(	'table'	=>	$this->field->foreign_table
					,	'fields'	=>	array
						(	'id'
						)
//					,	'equals'	=>	array
//						(	'status'	=>	'Active'
//						)
					,	'where'		=>	"	LOWER(title)	=	'".strings::pluralize($this->field->foreign_table)."'
						"
					,	'limit'		=>	1
					,	'pop_single_row'	=>	1
					)
				);
			endif;
			
			if	(	!empty
				 	(	$root_node_id
					)
				):
				$this->tree = new node_tree
				(	array
					(	'owner_node'	=>	$root_node_id
					,	'node_table'	=>	$this->field->foreign_table
					,	'depth'			=>	0
					)
				);
				
				$options = $GLOBALS['page']->render_menu
				(	array
					(	'menu_name'				=>	strings::pluralize
						(	$this->field->foreign_table
						)
					,	'node_table'			=>	$this->field->foreign_table
					,	'return_as'				=>	'options'
					)
				);
				
				// PREVENT SELF-OWNERSHIP SELECTION
				if	(	$this->field->foreign_table == $this->field->table
					):
					$nuptions = array();
					foreach
						(	$options as	$ok	=>	$ov
						):
						if	(	$ok	==	$GLOBALS['dbi']->tables[$this->field->table]->fields['id']->value
							):
							$nuptions[0] = $ov;
						else:
							$nuptions[$ok] = $ov;
						endif;
					endforeach;
					$options = $nuptions;
					unset
					(	$nuptions
					);
				endif;
		
			else:
				$whered = 0;
				$sql	=	"	SELECT	*
								FROM	`".$this->field->foreign_table."`
							";
				if	(	$this->field->foreign_table == 'country'
					):
					$sql 	.=	"	WHERE	status = 'Active'	";
					$whered = 1;
				endif;

				if	(	!empty
						(	$owned_selectables
						)
					):
					$owned_selectables = array_unique
					(	$owned_selectables
					);
					sort
					(	$owned_selectables
					);
					$sql .= 
					(	$whered
					)
					?	" AND "	
					:	" WHERE "
					;
					$sql .= "	id	IN	("
					.	implode
						(	','
						,	$owned_selectables
						)
					.	")	"
					;
					$whered = 1;
				endif;
				if	(	!empty
						(	$GLOBALS['dbi']->tables[$this->field->table]->owners[$this->field->name]
						)
					&&	!empty
						(	$GLOBALS['dbi']->ownerships[$GLOBALS['dbi']->tables[$this->field->table]->owners[$this->field->name]]['owner_where']
						)
					):
					$sql .= 
					(	$whered
					)
					?	" AND "	
					:	" WHERE "
					;
					$sql .= $GLOBALS['dbi']->ownerships[$GLOBALS['dbi']->tables[$this->field->table]->owners[$this->field->name]]['owner_where']
					.	"	"
					;
				endif;
				$sql .= $GLOBALS['dbi']->order_by_real_fields
				(	array
					(	'table'		=>	$this->field->foreign_table
					,	'order_by'	=>	$order_by
					)
				);
				$result = $GLOBALS['dbi']->get_result
				(	array
					(	'sql'		=>	$sql
					,	'tables'	=>	array
						(	$this->field->foreign_table
						)
					)
				);
				$options = array();
/*				
// MOMENTARY KLUDGERY TO PREPEND OWNER OWNER TITLES TO OPTION TEXT FOR CERTAIN TABLES
// UNSURE AS OF NOW HOW TO GET THIS INFO INTO THIS FUNCTION IN A MORE MODULAR FASHION
				$owner_labels = array
				(	//'project'	=>	'campaign'
				);
				$owner_owners = array();
				if	(	!empty
					 	(	$owner_labels[$this->field->foreign_table]
						)
					):
					$owner_owners = $GLOBALS['dbi']->get_result_array
					(	array
						(	'table'				=>	$owner_labels[$this->field->foreign_table]
						,	'fields'			=>	array
							(	'title'
							)
						,	'order_by'			=>	array
							(	'title'				=>	''
							)
						)
					);
				endif;
*/
				while
					(	$row = $result->fetch_array
						(	MYSQLI_ASSOC
						)	
					):
					if	(	!
							(	$this->field->foreign_table == $this->field->table
							&&	$row[$this->field->foreign_key_field] == $GLOBALS['dbi']->tables[$this->field->table]->fields['id']->value
							)
						):
						if	(	empty
							 	(	$owner_owners
								)
							):
							$options[$row[$this->field->foreign_key_field]] = $GLOBALS['dbi']->tables[$this->field->foreign_table]->render_record_title
							(	$row
							);
						else:
							$options[$row[$this->field->foreign_key_field]] = $owner_owners[array_pop
							(	$GLOBALS['dbi']->get_owner_records
								(	array
									(	'owned'				=>	array
										(	$GLOBALS['dbi']->tables[$this->field->foreign_table]->owners[$owner_labels[$this->field->foreign_table]]	=>	array
											(	$row['id']
											)
										)
									)
								)
							)]
							.	' &gt; '
							.	$GLOBALS['dbi']->tables[$this->field->foreign_table]->render_record_title
								(	$row
								)
							;
						endif;
					endif;
				endwhile;
				
				if	(	!empty
					 	(	$owner_owners
						)
					):
					natcasesort
					(	$options
					);
				endif;
				
			endif;

			$element_attributes = array();
			
			foreach
				(	$this->attributes	as	$attribute	=>	$att_value	
				):
				switch
					(	$attribute	
					):
					case 'class':
						$element_attributes[$attribute] = 
						(	!empty
							(	$this->attributes['value']
							)
						&&	!empty
							(	$this->attributes['onchange']
							)
						&&	strpos
							(	$this->attributes['onchange']
							,	'.submit()'
							)
						)
						?	$att_value
							.	' in_use'
						:	$att_value
						;
						break;
					case 'size':
					case 'maxlength':
						break;
					default:
						if	(	!empty
								(	$att_value
								)
							):
							$element_attributes[$attribute] = $att_value;
						endif;
				endswitch;
			endforeach;
			reset
			(	$this->attributes
			);
			
			if	(	empty
					(	$GLOBALS['dbi']->tables[$this->field->table]->owners[$this->name]
					)
				):
				$owners_required = 
				$owners_allowed = 
				1
				;
			else:
				$owners_required = 
				(	$this->output == 'filter'
				)
				?	0
				:	
	$GLOBALS['dbi']->ownerships[$GLOBALS['dbi']->tables[$this->field->table]->owners[$this->name]]['owners_required']
				;
				$owners_allowed = 
				(	$this->output == 'filter'
				)
				?	1
				:	$GLOBALS['dbi']->ownerships[$GLOBALS['dbi']->tables[$this->field->table]->owners[$this->name]]['owners_allowed']
				;
			endif;
			
			if	(	$this->output == 'filter'
				):
				$input_type = 'select';
			endif;
			
			switch
				(	$owners_allowed	
				):
				case 1:
					// SINGLE OWNER ONLY
					switch
						(	$input_type	
						):
						case 'select':
							// SINGLE SELECT DROPDOWN
							
							break;
						case 'checkbox':
							// OR RADIO BUTTONS
							$input_type = 'radio';
//							$GLOBALS['page']->scripts['src'][] = 'radio_limit';
							$element_attributes['onclick'] = 'radio_limit(this.id,\''
							.	$this->title_lowercase
							.	'\')'
							;
							break;
					endswitch;
					break;
				default: // ZERO OR ANY NUMBER GREATER THAN 1
					// 0: INFINITE NUMBER OF OWNERS ALLOWED
						// SELECT DROPDOWN REQUIRED // SO THAT GLOBAL DESELECTION IS POSSIBLE
					// > 1: MAXIMUM NUMBER OF OWNERS greater than 1
					$input_type = 
					(	!$owners_allowed
					||	count
						(	$options
						)	>	10
					)
					?	'select'
					:	'checkbox'
					;
					switch
						(	$input_type	
						):
						case 'select':
							// MULTIPLE SELECT DROPDOWN
							if	(	count
								 	(	$options
									)	>	$owners_allowed
								):
//								$GLOBALS['page']->scripts['src'][] = 'dropdown_limit';
								$GLOBALS['page']->scripts['ready'][] = 'dropdown_limit_init(document.getElementById(\''
								.	$element_attributes['id']
								.	'\'),'
								.	$owners_required
								.	','
								.	$owners_allowed
								.	');'
								;
								$element_attributes['onchange'] = 'dropdown_limit(this,\''
								.	$this->title_lowercase
								.	'\')'
								;
							endif;
							$element_attributes['multiple'] = '';
							if	(	$this->field->null_allowed
								):
								$option_count = count
								(	$options
								)
								+	1
								;
//								$element_attributes['size'] = $owners_allowed + 1;
							else:
								$option_count = count
								(	$options
								);
//								$element_attributes['size'] = $owners_allowed;
							endif;
//							$max_select_height = 
							$element_attributes['size'] = 
							(	$option_count	<	$GLOBALS['page']->input_sizes['max_select_height']
							)
							?	$option_count
							:	$GLOBALS['page']->input_sizes['max_select_height']
							;
/*							
							if	(	$element_attributes['size']	>	$max_select_height
								):
								$element_attributes['size'] = $max_select_height;
							endif;
*/
							break;
						case 'checkbox':
							// OR CHECKBOXES
							if	(	count
								 	(	$options
									)	>	$owners_allowed
								):
//								$GLOBALS['page']->scripts['src'][] = 'checkbox';
								$GLOBALS['page']->scripts['ready'][] = 'checkbox_limit_init(\''
								.	$element_attributes['id']
								.	'-0\','
								.	$owners_required
								.	','
								.	$owners_allowed
								.	');'
								;
								$element_attributes['onclick'] = 'checkbox_limit(this,\''
								.	$this->title_lowercase
								.	'\')'
								;
							endif;
							break;
					endswitch;
			endswitch;
			
			if	(	is_array
					(	$this->attributes['value']
					)
				):
				$selected_options = $this->attributes['value'];
			else:
				$selected_options = 
				(	strstr
					(	$this->attributes['value']
					,	','
					)
				)
				?	explode
					(	','
					,	$this->attributes['value']
					)
				:	array
					(	$this->attributes['value']
					)
				;
			endif;
			
			switch
				(	$input_type	
				):
				case 'checkbox':
				case 'radio':
					$elemeat = xhtml::checkbox_group
					(	array
						(	'options'				=>	$options
						,	'checkbox_type'			=>	$input_type
						,	'default_option'		=>	array
							(	$default_option			=>	$empty_option_text
							)
						,	'selected_options'		=>	$selected_options
/*
						,	'option_sort_functions'	=>	array
							(
							)
*/
						,	'null_allowed'			=>	$null_allowed
						,	'attributes'			=>	$element_attributes
						)
					)
					;
					break;
				case 'select':
					unset
					(	$element_attributes['value']
					);
					$elemeat = xhtml::select_element
					(	array
						(	'options'				=>	$options
						,	'default_option'		=>	array
							(	$default_option			=>	$empty_option_text
							)
						,	'selected_options'		=>	$selected_options
/*
						,	'option_sort_functions'	=>	array
							(
							)
*/
						,	'null_allowed'			=>	
							(	$this->output == 'filter'
							)
							?	1
							:	$null_allowed
						,	'attributes'			=>	$element_attributes
						)
					);
/*					
					if	(	$this->output != 'filter'
						):
						permission::initialize
						(	$this->field->foreign_table
						);
						if	(	permission::evaluate
								(	$GLOBALS['dbi']->tables[$this->field->foreign_table]->permissions['create_records_if']
								)
							):
			
							$new_record_link = uri::generate
							(	array
								(	'query'	=>	array
									(	'z'		=>	$this->field->foreign_table
									,	'id'	=>	0
									)
								,	'query_crypt_key'	=>	$GLOBALS['page']->url_crypt_key
								)
							);
							
							$new_record_anchor = xhtml::element
							(	array
								(	'tag_name'	=>	'A'
								,	'attributes'	=>	array
									(	'HREF'		=>	$new_record_link
									)
								,	'content'		=>	'New '
									.	$GLOBALS['dbi']->tables[$this->field->foreign_table]->title
								)
							);
							
							$elemeat = xhtml::element
							(	array
								(	'tag_name'	=>	'DIV'
								,	'attributes'	=>	array
									(	'CLASS'			=>	'float_left'
									,	'STYLE'			=>	'margin-right:10px;'
									)
								,	'content'		=>	$elemeat
								)
							)
							.	xhtml::element
								(	array
									(	'tag_name'	=>	'DIV'
									,	'attributes'	=>	array
										(	'CLASS'		=>	'record_button float_left'
										)
									,	'content'		=>	$new_record_anchor
									)
								)
							;
				
						endif;
					endif;
*/					
					$elemeat .=	'<br style="clear:left;" />';
					break;
			endswitch;
			
			if	(	$this->output != 'filter'
				&&	(	!empty
						(	$owners_required
						)
					||	!empty
						(	$owners_allowed
						)
					)
				):
				$owner_table_title = $this->title_lowercase;
				if	(	$owners_required == $owners_allowed
					):
					if	(	$owners_required	>	1
						):
						$owner_table_title = $this->title_lowercase_plural;
					endif;
					$blurb = 'Please select '
					.	$owners_required
					.	' '
					.	$owner_table_title
					.	'.'
					;
				else:
					if	(	!empty
							(	$owners_required
							)
						&&	!empty
							(	$owners_allowed
							)
						):
						$blurb = 'Please select between '
						.	$owners_required
						.	' and '
						.	$owners_allowed
						.	' '
						.	$owner_table_title
						.	'.'
						;
					else:
						if	(	!empty
								(	$owners_required
								)
							):
							if	(	$owners_required	>	1
								):
								$owner_table_title = $this->title_lowercase_plural;
							endif;
							$blurb = 'Please select at least '
							.	$owners_required
							.	' '
							.	$owner_table_title
							.	'.'
							;
						endif;
						if	(	!empty
								(	$owners_allowed
								)
							):
							$only_up_to = 'only';
							if	(	$owners_allowed	>	1
								):
								$only_up_to = 'up to';
								$owner_table_title = $this->title_lowercase_plural;
							endif;
							$blurb = 'Please select '
							.	$only_up_to
							.	' '
							.	$owners_allowed
							.	' '
							.	$this->title_lowercase
							.	'.'
							;
						endif;
					endif;
				endif;
			endif;
			
			if	(	!empty
					(	$blurb
					)
				):
				$elemeat_class = 
				(	empty
					(	$this->field->dud
					)
				)
				?	'rec_fld_blr'
				:	'rec_fld_blr highlight_dud'
				;
				$elemeat .= xhtml::element
				(	array
					(	'tag_name'		=>	'SPAN'
					,	'attributes'	=>	array
						(	'CLASS'		=>	$elemeat_class
						,	'STYLE'		=>	'white-space:nowrap;clear:all;'
						)
					,	'content'		=>	$blurb
					)
				)
				.	'<br />'
				;			
			endif;
			
			return $elemeat;
			
		endif;
	}
	
	function render_input
	(	$args	=	array()
	)
	{	extract
		(	debug::function_argument_verify
			(	array
				(	'function'			=>	__CLASS__.'->'.__FUNCTION__
				,	'arguments_input'		=>	$args
				,	'arguments_descriptions'	=>	array
					(	'null_allowed'			=>	array
						(	'possible_values'		=>	array
							(	0
							,	1
							)
						,	'default_value'		=>	$this->field->null_allowed
						)
					)
				)
			)
		);
		
		$input = '';
		
		$input_attributes = array();
		foreach
			(	$this->attributes as $attribute => $att_value	
			):
			switch (	$attribute	
				):
				case 'width':
				case 'height':
					break;
				case 'value':
					$input_attributes[$attribute] = $att_value;
				default:
					if	(	!empty
						 	(	$att_value
							)
						):
						$input_attributes[$attribute] = $att_value;
					endif;
			endswitch;
		endforeach;
		reset
		(	$this->attributes
		);
		
		if	(	$this->output == 'filter'
			):
//			$GLOBALS['page']->scripts['src'][] = 'enter_submit';
			$enter_submit = 'return enter_submit(this,event)';
			$input_attributes['onkeypress']	=	
			(	empty
				(	$input_attributes['onkeypress']
				)
			)
			?	$enter_submit
			:	$input_attributes['onkeypress']
				.	';'
				.	$enter_submit
				;
			;
		endif;
		
		if	(	$this->attributes['onchange']
			&&	!empty
				(	$this->attributes['value']
				)
			&&	!empty
				(	$input_attributes['class']
				)
			):
			$input_attributes['class'] .= ' in_use';
		endif;
		
		$input .= xhtml::element
		(	array
			(	'tag_name'		=>	'INPUT'
			,	'attributes'	=>	$input_attributes
			)
		);
		
		return $input;
	}
	
	function render_link
	(	$args	=	array()
	)
	{	extract
		(	debug::function_argument_verify
			(	array
				(	'function'			=>	__FUNCTION__
				,	'arguments_input'		=>	$args
				,	'arguments_descriptions'	=>	array
					(	'content'				=>	array
						(	'blurb'				=>	'Link text.'
						,	'default_value'		=>	$this->value['display']
						)
					,	'href'				=>	array
						(	'default_value'		=>	''
						)
					,	'target'				=>	array
						(	'blurb'				=>	'Anchor TARGET attribute value.'
						,	'default_value'		=>	''
						)
					,	'output'				=>	array
						(	'default_value'			=>	$this->output
						)
					)
				)
			)
		);
		
		switch (	$output	
			):
			case 'filter':
			case 'edit':
				$display_value = $this->render_input();
				break;
			default: // case 'view':
				$display_value = strings::anchor_format
				(	array
					(	'content'	=>	$content
					,	'href'		=>	$href
					,	'target'	=>	$target
					)
				);
		endswitch;
		return $display_value;
	}
	
	function render_shop_code_selector
	(	$args	=	array()
	)
	{	extract
		(	debug::function_argument_verify
			(	array
				(	'function'			=>	__CLASS__.'->'.__FUNCTION__
				,	'arguments_input'		=>	$args
				,	'arguments_descriptions'	=>	array
					(	'default_option'		=> array
						(	'default_value'		=>	$this->field->default_value
						)
					,	'null_allowed'			=> array
						(	'possible_values'		=>	array
							(	0
							,	1
							)
						,	'default_value'		=>	$this->field->null_allowed
						)
					,	'empty_option_text'		=> array
						(	'blurb'				=>	'The text to display for an empty-value option, if such an option is present.'
						,	'default_value'		=>	'[No Shop Code]'
						)
					)
				)
			)
		);
		
		if	(	empty
				(	$default_option
				)
			):
			$default_option = 0;
		endif;

		$nonoptions = $GLOBALS['dbi']->get_result_array
		(	array
			(	'table'				=>	$this->field->table
			,	'fields'			=>	array
				(	$this->field->name
				)
			,	'order_by'			=>	array
				(	$this->field->name	=>	''
				)
			,	'where'				=>	"
					CHAR_LENGTH(".$this->field->name.") > 0
					AND	".$this->field->name." != '".$this->attributes['value']."'
				"
//			,	'key_by'			=>	$this->field->name
			)
		);
		
		switch
			(	$this->field->table
			):
			case 'album_track':
				$use_table = 'track';
				break;
			case 'album':
				$use_table = 
				(	$this->field->name_contains_word
					(	'digital'
					)
				)
				?	'digital_album'
				:	'compact_disc'
				;
				break;
			default:
				$use_table = $this->field->table;
		endswitch;
		
		$elemeat = '<ul id="m2_'
		.	$use_table
		.	'" class="m2_code_loader"><li>Retrieving M2 Product Manifest Data</li>'
		;
		foreach
			(	$nonoptions	as	$nonoption
			):
			$elemeat .= '<li id="m2_'
			.	$nonoption
			.	'" class="m2_nonoption"></li>'
			;
		endforeach;
		$elemeat .= '</ul>';

		$elemeat .= xhtml::select_element
		(	array
			(	'options'				=>	array()
			,	'default_option'		=>	array
				(	$default_option			=>	$empty_option_text
				)
//			,	'selected_options'		=>	$selected_options
/*			,	'option_sort_functions'	=>	array
				(
				)
*/
			,	'null_allowed'			=>	$null_allowed
			,	'attributes'			=>	array
				(	'name'					=>	$this->attributes['name']
				,	'id'					=>	$this->attributes['id']
				,	'class'					=>	$this->attributes['class']
					.	' m2_'
					.	$use_table
					.	' m2_code_loaded'
				,	'data-value'			=>	$this->attributes['value']
				)
			)
		);
	
		if	(	!empty
				(	$blurb
				)
			):
			$elemeat_class = 
			(	empty
				(	$this->field->dud
				)
			)
			?	'rec_fld_blr'
			:	'rec_fld_blr highlight_dud'
			;
			$elemeat .= xhtml::element
			(	array
				(	'tag_name'		=>	'SPAN'
				,	'attributes'	=>	array
					(	'CLASS'		=>	$elemeat_class
					,	'STYLE'		=>	'white-space:nowrap;clear:all;'
					)
				,	'content'		=>	$blurb
				)
			)
			.	'<br />'
			;			
		endif;
		
		$GLOBALS['page']->scripts['src'][] = 'jquery/jquery.cookies.js';
		$GLOBALS['page']->scripts['src'][] = 'jquery/jquery.jsonp.js';
		$GLOBALS['page']->scripts['src'][] = 'm2/api.js';
		$GLOBALS['page']->scripts['src'][] = 'm2/display.js';
		$GLOBALS['page']->scripts['src'][] = 'm2/cart.js';
		$GLOBALS['page']->scripts['src'][] = 'm2/store.js';
		$GLOBALS['page']->scripts['src'][] = 'm2/manifest.js';

		return $elemeat;

	}

	function render_textarea
	(	$args	=	array()
	)
	{	extract
		(	debug::function_argument_verify
			(	array
				(	'function'			=>	__CLASS__.'->'.__FUNCTION__
				,	'arguments_input'		=>	$args
				,	'arguments_descriptions'	=>	array
					(	'content'					=>	array
						(	'default_value'				=>	$this->value['raw']
						)
					,	'attributes'				=>	array
						(	'default_value'				=>	$this->attributes
						)
					)
				)
			)
		);
		
		$textarea = '';
		if	(	!empty
				(	$attributes['max_length']
				)
			&&	$attributes['max_length'] < 65535
			):
//			$GLOBALS['page']->scripts['src'][] = 'textarea_limit';
			$attributes['onkeydown'] = 'textarea_limit(this,'
			.	$attributes['max_length']
			.	')'
			;
			$attributes['onkeyup'] = 'textarea_limit(this,'
			.	$attributes['max_length']
			.	')'
			;
			unset($attributes['max_length']);
		endif;
		
		foreach
			(	$this->attributes as $attribute => $att_value	
			):
			switch
				(	$attribute	
				):
				case 'name':
				case 'id':
					$attributes[$attribute] = $att_value;
					break;
				case 'class':
					if	(	strstr
							(	$att_value
							,	'rec_fld_val'
							)
						):
						$attributes[$attribute] = str_replace
						(	'rec_fld_val'
						,	'rec_fld_content'
						,	$att_value
						)
						;
					endif;
					break;
			endswitch;
		endforeach;
		reset
		(	$this->attributes
		);
		
		$textarea .= xhtml::element
		(	array
			(	'tag_name'		=>	'TEXTAREA'
			,	'attributes'	=>	$attributes
			,	'content'		=>	$content
			)
		);
		
		return $textarea;
	}
	
	function select_files
	(	$args	=	array()
	)
	{	extract
		(	debug::function_argument_verify
			(	array
				(	'function'			=>	__CLASS__.'->'.__FUNCTION__
				,	'arguments_input'		=>	$args
				,	'arguments_descriptions'	=>	array
					(	'filter_by_owner'			=>	array
						(	'default_value'				=>	0
						)
					)
				)
			)
		);
		
		if	(	$filter_by_owner
			):
			// if filter_by_owner is true, an owned record is inserted, so that this record owns all files uploaded to it, and thus, only these files will appear for future selection when this record is edited.
		else:
		
			$files = 
			(	empty
				(	$GLOBALS['s3']
				)
			||	empty
				(	$this->value['masks']['file']['s3_bucket']
				)
			)
			?	files::in_dir
				(	$this->value['masks']['file']['path']
				)
			:	$GLOBALS['s3']->get_object_list
				(	$this->value['masks']['file']['s3_bucket']
				)
			;
/*			
debug::expose
(	$files
);			
*/			
			$files = array_flip
			(	$files
			);
			foreach
				(	$files	as	$fk	=>	$fv
				):
				$files[$fk] = $fk;
			endforeach;
			reset
			(	$files
			);
/*			
debug::expose
(	$this->value['masks']['file']['s3_bucket']	
);
debug::expose
(	$files
);
*/			
			return '<div>&nbsp;<br/>Select a <span style="font-weight:bold">Different Existing '
			.	$this->field->title
			.	'</span>: '
			.	xhtml::select_element
				(	array
					(	'options'				=>	$files
					,	'selected_options'		=>	array
						(	$this->value['display']
						)
					,	'null_allowed'			=>	0
					,	'attributes'			=>	array
						(	'name'		=>	$this->attributes['name']
							.	'[]'
						,	'id'		=>	$this->attributes['id']
						,	'class'		=>	'rec_fld_val'
						,	'onchange'	=>	'if(this.selectedIndex){$(\'#'
							.	$this->name
							.	'-img\').attr(\'src\',\'https://s3.amazonaws.com/'
							.	$this->value['masks']['file']['s3_bucket']
							.	'/\'+this.options[this.selectedIndex].value)}'
						)
					)
				)
			.	'<br/>&nbsp;</div>'
			;

		endif;
			/*
	$existing_owners = array();
		$inner_owners = $this->owners;
		foreach (	$this->owners	as	$ownership_id	=>	$owners	
			):
			$ownership_info = $GLOBALS['dbi']->ownerships[$ownership_id];
			if	(	empty($GLOBALS['dbi']->tables[$ownership_info['owner_table']]->owners)
				&&	!empty($GLOBALS['user']->owners[$ownership_id])
				):
				
				foreach (	$inner_owners	as	$inner_ownership_id	=>	$inner_owner
					):
					foreach	(	$inner_owner	as	$inner_owner_id	=>	$inner_owner_info
						):
						$existing_owners[$inner_ownership_id][$inner_owner_id] = $inner_owner_info;
					endforeach;
				endforeach;
				reset($inner_owners);
				
// CAN'T DO THE FOLLOWING WITH NUMERIC ARRAY KEYS // FUCK
//				$existing_owners = array_merge_recursive
//				(	$existing_owners
//				,	$this->owners
//				);
// NO WAIT, YOU FIGURED OUT A WAY, WITH THE CAPITAL 'O' KEY
			else:
				foreach (	$GLOBALS['dbi']->tables[$ownership_info['owner_table']]->owners	as	$owner_ownership_name	=>	$owner_ownership_id	
					):
//					$owner_ownership_info = $GLOBALS['dbi']->ownerships[$owner_ownership_id];
					if	(	!empty($this->table->owners[$owner_ownership_id])
						&&	!empty($GLOBALS['dbi']->tables[$ownership_info['owner_table']]->record->owners[$owner_ownership_id])
						):
						$existing_owners[$owner_ownership_id] = 
						(	!empty($existing_owners[$owner_ownership_id])
						)
						?	array_merge_recursive
							(	$existing_owners[$owner_ownership_id]
							,	array_keys
								(	$GLOBALS['dbi']->tables[$ownership_info['owner_table']]->record->owners[$owner_ownership_id]
								)
							)
						:	array_keys
							(	$GLOBALS['dbi']->tables[$ownership_info['owner_table']]->record->owners[$owner_ownership_id]
							)
						;
					endif;
				endforeach;
			endif;
			reset($GLOBALS['dbi']->tables[$ownership_info['owner_table']]->owners);
		endforeach;
		reset($this->owners);
		
		$ownable_records = $this->table->select_records
		(	array
			(	'owners'	=>	$existing_owners
			,	'fields'	=>	0
			)
		);
		
		if	(	!empty
				(	$ownable_records
				)
			):
			$options = array
			(	
			);
			foreach	(	$ownable_records	as	$ownable_id	=>	$ownable_info	
				):
				if	(	empty
						(	$this->records[$ownable_id]
						)
					):
					$options[$ownable_id] = $this->table->render_record_title($ownable_info);
				endif;
			endforeach;
		endif;
		if	(	!empty
				(	$options
				)
			):
			$an_other = 
			(	$this->records
			)
			?	'another'
			:	'an'
			;
			
			natcasesort($options);
			
			$ownable_ids = xhtml::hidden_input
			(	array
				(	'name'		=>	$this->form
					.	'-ownable_ids'
				,	'value'		=>	implode
					(	','
					,	array_keys
						(	$options
						)
					)
				)
			);
			
			$options = array_reverse
			(	$options
			,	1
			);
			// THE OPTION TO ASSOCIATE ALL EXISTING RECORDS IS KEYED BY THE CAPITAL LETTER 'O'
			// THE DEFAULT OPTION, WHICH JUST DISPLAYS EXPLANATORY TEXT ABOUT ASSOCIATING RECORDS
				// IS KEYED BY THE NUMBER 0
			$options['O'] = 'Associate ALL existing '
			.	$this->table->title_plural
			;
			$options = array_reverse
			(	$options
			,	1
			);
			
			$this->template['aft_tbl']['own_record'] = $ownable_ids
			.	xhtml::select_element
				(	array
					(	'options'				=>	$options
					,	'default_option'		=>	array
						(	0						=>	'Associate '
							.	$an_other
							.	' existing '
							.	$this->table->title
						)
					,	'option_sort_functions'	=>	array()
					,	'null_allowed'			=>	1
					,	'attributes'			=>	array
						(	'name'		=>	$this->form
							.	'-own_id'
						,	'id'		=>	$this->form
							.	'-own_id'
						,	'class'		=>	'rec_fld_val'
						,	'onchange'	=>	'if(this.selectedIndex){document.'
							.	$this->form
							.	'.act.value=\'own\';document.'
							.	$this->form
							.	'.submit()}'
						)
					)
				)
			;
		endif;
		$this->template['own_record'] = $this->template['aft_tbl']['own_record'];
		*/
	}
}
