<?php

class xhtml {

	private static $empty_elements = array
	// HEADER ELEMENTS
	(	'base'
	,	'isindex'
	,	'link'
	,	'meta'
	,	'nextid'
	,	'range'
	// BODY ELEMENTS
	,	'area'
	,	'atop'
	,	'audioscope'
	,	'basefont'
	,	'br'
	,	'choose'
	,	'col'
	,	'frame'
	,	'hr'
	,	'img'
	,	'input'
	,	'keygen'
	,	'left'
	,	'limittext'
	,	'of'
	,	'over'
	,	'param'
	,	'right'
	,	'spacer'
	,	'spot'
	,	'tab'
	,	'wbr'
	);
		
	private static $minimized_attributes = array
	(	'checked'
	,	'selected'
	,	'compact'
	,	'nowrap'
	,	'multiple'
	,	'noshade'
	);
		
	public static function checkbox_element
	(	$args	=	array()
	)
	{	extract
		(	debug::function_argument_verify
			(	array
				(	'function'			=>	__CLASS__.'->'.__FUNCTION__
				,	'arguments_input'		=>	$args
				,	'arguments_descriptions'	=>	array
					(	'checkbox_type'			=>	array
						(	'possible_values'		=>	array
							(	'checkbox'
							,	'radio'
							)
						,	'default_value'			=>	'checkbox'
						)
					,	'option_number'				=>	array
						(	'default_value'				=>	0
						)
					,	'option_value'				=> array
						(	'default_value'				=>	''
						)
					,	'option_content'		=> array
						(	'default_value'			=>	''
						)
					,	'checked'			=> array
						(	'possible_values'		=>	array
							(	0
							,	1
							)
						,	'default_value'		=>	0
						)
					,	'attributes'			=> array
						(	'blurb'					=>	"Associate array: 'attribute_name' => 'attribute_value'."
						,	'default_value'		=>	array()
						)
					,	'single_box'			=>	array
						(	'possible_values'		=>	array
							(	0
							,	1
							)
						,	'default_value'			=>	0
						)
					)
				)
			)
		);
		
		$attributes['type'] = $checkbox_type;
		$attributes['id'] = $attributes['name'].'-'.$option_number;
		$label_attributes = array
		(	'id'	=>	$attributes['id'].'-label'
		);
		if	(	$checkbox_type == 'checkbox'
			):
			if	(	!$single_box
				):
				$attributes['name'] .= '[]';
			endif;
			if	(	!empty
					(	$option_content
					)
				):
				$attributes['onchange'] = 'if(this.checked){document.getElementById(\''
				.	$label_attributes['id']
				.	'\').className=\'rec_fld_tbl_cell_selected\'}else{document.getElementById(\''
				.	$label_attributes['id']
				.	'\').className=\'rec_fld_tbl_cell\'}';
				$label_attributes['onclick'] = 'if(document.getElementById(\''
				.	$attributes['id']
				.	'\').checked){document.getElementById(\''
				.	$attributes['id']
				.	'\').checked=0;this.className=\'rec_fld_tbl_cell\'}else{document.getElementById(\''
				.	$attributes['id']
				.	'\').checked=1;this.className=\'rec_fld_tbl_cell_selected\'}'
				;
			endif;
		else:
			$attributes['onchange'] = 
			$label_attributes['onclick'] = 
			'radio_limit(\''
			.	$attributes['id']
			.	'\',1)'
			;
		endif;
		
		if	(	$checked
			):
			$attributes['checked'] = '';
			$label_attributes['class'] = 'rec_fld_tbl_cell_selected';
		else:
			$label_attributes['class'] = 'rec_fld_tbl_cell';
		endif;
		
		$transfer_attributes = array
		(	'class'			=>	' '
		,	'onclick'		=>	';'
		,	'onmouseover'	=>	';'
		,	'onmouseout'	=>	';'
		,	'onfocus'		=>	';'
		,	'onblur'		=>	';'
		);
		foreach
			(	$transfer_attributes	as	$transfer_attribute	=>	$concat
			):
			if	(	!empty
					(	$attributes[$transfer_attribute]
					)
				):
				$label_attributes[$transfer_attribute] = 
				(	empty
					(	$label_attributes[$transfer_attribute]
					)
				)
				?	$attributes[$transfer_attribute]
				:	$label_attributes[$transfer_attribute]
					.	$concat
					.	$attributes[$transfer_attribute]
				;
			endif;
		endforeach;
		reset
		(	$transfer_attributes
		);
		
		$attributes['value'] = $option_value;
		
		// IE CSS KLUDGE
		$attributes['class'] = 
		(	empty
			(	$attributes['class']
			)
		)
		?	'checkbox'
		:	'checkbox '
			.	$attributes['class']
		;
		
		if	(	strpos
				(	$label_attributes['class']
				,	'rec_fld_tbl_cell_selected'
				)	=== false
			&&	strpos
				(	$label_attributes['class']
				,	' in_use'
				)	!== false
			):
			$label_attributes['class'] = str_replace
			(	' in_use'
			,	''
			,	$label_attributes['class']
			);
		endif;
		
		$checkbox = self::element
		(	array
			(	'tag_name'		=>	'INPUT'
			,	'attributes'	=>	$attributes
			)
		);
		
		if	(	!empty
				(	$option_content
				)
			):
			$checkbox = self::element
			(	array
				(	'tag_name'		=>	'TD'
				,	'attributes'	=>	array
					(	'style'			=>	'white-space:nowrap'
					)
				,	'content'		=>	$checkbox
				)
			);
			
			$checkbox_label = self::element
			(	array
				(	'tag_name'		=>	'TD'
				,	'attributes'	=>	$label_attributes
				,	'content'		=>	'&nbsp;'
					.	$option_content
					.	'&nbsp;&nbsp;'
				)
			);
			
			$checkbox = self::element
			(	array
				(	'tag_name'		=>	'TABLE'
				,	'content'		=>	self::element
					(	array
						(	'tag_name'		=>	'TR'
						,	'content'		=>	$checkbox
							.	$checkbox_label
						)
					)
				)
			);
		endif;
		
		return $checkbox;
	}
	
	public static function checkbox_group
	(	$args	=	array()
	)
	{	extract
		(	debug::function_argument_verify
			(	array
				(	'function'			=>	__CLASS__.'->'.__FUNCTION__
				,	'arguments_input'		=>	$args
				,	'arguments_descriptions'	=>	array
					(	'checkbox_type'			=>	array
						(	'possible_values'		=>	array
							(	'checkbox'
							,	'radio'
							)
						,	'default_value'			=>	'checkbox'
						)
					,	'options'				=> array
						(	'blurb'				=>	'Numerically keyed array containing list of available values from db field type string.'
						,	'default_value'		=>	array()
						)
					,	'default_option'		=> array
						(	'default_value'			=>	array
							(	0	=>	''
							)
						)
					,	'selected_options'		=>	array
						(	'default_value'		=>	array()
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
						,	'default_value'		=>	0
						)
					,	'attributes'			=> array
						(	'blurb'					=>	"Associate array: 'attribute_name' => 'attribute_value'."
						,	'default_value'		=>	array()
						)
					,	'display_columns'		=>	array
						(	'default_value'			=>	1
						)
					)
				)
			)
		);
		
//		unset($attributes['class']);
		$checkboxes = array();
		$box_count = 0;
		if	(	$checkbox_type	==	'radio'
			):
//			$GLOBALS['page']->scripts['src'][] = 'radio_limit';
			$GLOBALS['page']->scripts['ready'][] = 'radio_limit(\''
			.	$attributes['id']
			.	'-'
			.	(	(	$null_allowed
					)
					?	0
					:	1
				)
			.	'\',0);'
			;
			if	(	$null_allowed
				):
				$checked = 
				(	empty
					(	$selected_options
					)
				)
				?	1
				:	0
				;
				$checkboxes[key($default_option)] = self::checkbox_element
				(	array
					(	'checkbox_type'		=>	$checkbox_type
					,	'option_value'		=>	key
						(	$default_option
						)
					,	'option_content'	=>	current
						(	$default_option
						)
					,	'checked'			=>	$checked
					,	'attributes'		=>	$attributes
					)
				);
				$box_count++;
			endif;
		endif;
		
		$single_box = 
		(	count
			(	$options
			)	>	1
		)
		?	0
		:	1
		;
		foreach
			(	$options	as	$option_value	=>	$option_content
			):
			$checked = 
			(	in_array
				(	$option_value
				,	$selected_options
				)
			)
			?	1
			:	0
			;
			$checkboxes[$option_value] = self::checkbox_element
			(	array
				(	'checkbox_type'		=>	$checkbox_type
				,	'option_number'		=>	$box_count
				,	'option_value'		=>	$option_value
				,	'option_content'	=>	$option_content
				,	'checked'			=>	$checked
				,	'attributes'		=>	$attributes
				,	'single_box'		=>	$single_box
				)
			);
			$box_count++;
		endforeach;
		
		$checkbox_row = 
		$checkbox_rows = 
		''
		;
		$columns = 0;
		
		foreach
			(	$checkboxes	as	$labeled_checkbox
			):
			$columns++;
			$checkbox_row .= self::element
			(	array
				(	'tag_name'		=>	'TD'
				,	'content'		=>	$labeled_checkbox
				)
			);
			if	(	$columns % $display_columns == 0
				):
				$checkbox_rows .= self::element
				(	array
					(	'tag_name'		=>	'TR'
					,	'content'		=>	$checkbox_row
					)
				);
				$checkbox_row = '';
			endif;
		endforeach;
		if	(	!empty
				(	$checkbox_row
				)
			):
			$checkbox_rows .= self::element
			(	array
				(	'tag_name'		=>	'TR'
				,	'content'		=>	$checkbox_row
				)
			);
			$checkbox_row = '';
		endif;
		
		return self::element
		(	array
			(	'tag_name'		=>	'TABLE'
			,	'attributes'	=>	array
				(	'class'			=>	'rec_fld_tbl'
				,	'cellspacing'	=>	0
				)
			,	'content'		=>	$checkbox_rows
			)
		);
	}
	
	public static function element
	(	$args	=	array()
	)
	{	extract
		(	debug::function_argument_verify
			(	array
				(	'function'			=>	__CLASS__.'->'.__FUNCTION__
				,	'arguments_input'		=>	$args
				,	'arguments_descriptions'	=>	array
					(	'tag_name'
					,	'attributes'			=>	array
						(	'blurb'					=>	'array("{attribute_name}" => "{attribute_value}", etc.)'
						,	'default_value'			=>	0
						)
					,	'content'				=>	array
						(	'blurb'					=>	'Content of non-empty elements, may include other elements.'
						,	'default_value'			=>	''
						)
					,	'parse_content'			=>	array
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
		
		$empty_element =
		(	in_array
			(	strtolower
				(	$tag_name
				)
			,	self::$empty_elements
			)
		)
		?	1
		:	0
		;
		
		return xml::element
		(	array
			(	'tag_name'				=>	$tag_name
			,	'attributes'			=>	$attributes
			,	'content'				=>	$content
			,	'minimized_attributes'	=>	self::$minimized_attributes
            ,	'empty_element'			=>	$empty_element
			,	'parse_content'			=>	$parse_content
			)
		);
	}
	
	public static function hidden_input
	(	$args	=	array()
	)
	{	extract
		(	debug::function_argument_verify
			(	array
				(	'function'			=>	__CLASS__.'->'.__FUNCTION__
				,	'arguments_input'		=>	$args
				,	'arguments_descriptions'	=>	array
					(	'name'
					,	'value'				=>	array
						(	'default_value'		=>	''
						)
					)
				)
			)
		);
		
		return self::element
		(	array
			(	'tag_name'	=>	'INPUT'
			,	'attributes'	=>	array
				(	'TYPE'		=>	'hidden'
				,	'ID'		=>	$name
				,	'NAME'		=>	$name
				,	'VALUE'		=>	$value
				)
			)
		);
	}
	
	public static function hidden_inputs
	(	$args	=	array()
	)
	{	extract
		(	debug::function_argument_verify
			(	array
				(	'function'			=>	__CLASS__.'->'.__FUNCTION__
				,	'arguments_input'		=>	$args
				,	'arguments_descriptions'	=>	array
					(	'inputs'
					)
				)
			)
		);
		
		$tags = '';
		foreach
			(	$inputs as $name => $value	
			):
			$tags .= self::hidden_input
			(	array
				(	'name'	=>	$name
				,	'value'	=>	$value
				)
			);
		endforeach;
		return $tags;
	}
	
	public static function script_element
	(	$args	=	array()
	)
	{	extract
		(	debug::function_argument_verify
			(	array
				(	'function'			=>	__CLASS__.'->'.__FUNCTION__
				,	'arguments_input'		=>	$args
				,	'arguments_descriptions'	=>	array
					(	'content'				=>	array
						(	'default_value'		=>	''
						)
					,	'src'				=>	array
						(	'default_value'		=>	''
						)
					,	'type'	=>	array
						(	'blurb'			=>	'The mime type of the script or file.'
						,	'default_value'	=>	'text/JavaScript'
						)
					,	'stamp'				=>	array
						(	'default_value'		=>	$GLOBALS['page']->time['stamp']
						)
					)
				)
			)
		);
		
		$element_args = array
		(	'tag_name'	=>	'SCRIPT'
/*		
		,	'attributes'	=>	array
			(	'TYPE'	=>	$type
			)
*/
		);
		if	(	!empty
				(	$src
				)
			):
			// TEST FOR EXISTENCE OF SCRIPT FILE BEFORE INCLUDING SRC !!!
//			$GLOBALS['page']->path['file_root']
			if	(	debug::get_mode()
				&&	stristr
					(	$src
					,	'http'
					)	===	false
				):
				$src .= '?force_refresh='.$stamp;
			endif;
			$element_args['attributes']['SRC'] = $src;
		else:
			if	(	!empty
					(	$content
					)
				):
				$element_args['content'] = '
/* <![CDATA[ */
'
				.	"\n"
				.	$content
				.	"\n"
				.	'
/* ]]> */
'
				;
			else:
				trigger_error
				(	__CLASS__.'->'.__FUNCTION__.'(): EITHER $src OR $content ARGUMENT NEEDS TO BE SET'
				);
				exit;
			endif;
		endif;
		
		return self::element
		(	$element_args
		);
	}
	
	public static function select_element
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
					,	'default_option'		=> array
						(	'default_value'			=>	array
							(	0	=>	''
							)
						)
					,	'selected_options'		=>	array
						(	'default_value'		=>	array()
						)
					,	'selected_option'		=>	array
						(	'default_value'			=>	''
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
						,	'default_value'		=>	0
						)
					,	'attributes'			=> array
						(	'blurb'					=>	"Associate array: 'attribute_name' => 'attribute_value'."
						,	'default_value'		=>	array()
						)
					)
				)
			)
		);

		$select_options = '';
		if	(	$null_allowed
			):
			
			$option_attributes = array
			(	'VALUE'		=>	key
				(	$default_option
				)
			,	'CLASS'		=>	'null_option'
			);
			
			if	(	empty
					(	$selected_option
					)
				&&	empty
					(	$selected_options
					)
				):
				$option_attributes['SELECTED'] = '';
			endif;
			
			$select_options .= self::element
			(	array
				(	'tag_name'		=>	'OPTION'
				,	'attributes'	=>	$option_attributes
				,	'content'		=>	current
					(	$default_option
					)
				)
			);
		endif;
		if	(	!empty
				(	$option_sort_functions
				)
			):
			foreach
				(	$option_sort_functions	as	$option_sort_function
				):
				eval
				(	$option_sort_function
				.	'($options);'
				);
			endforeach;
		endif;
		foreach
			(	$options as $option_value => $option_content
			):
			$option_attributes = array
			(	'VALUE'		=>	$option_value
			);
			if	(	$option_value == 'O'
				):
				$option_attributes['CLASS'] = 'all_option';
			endif;
			
			if	(	(	!empty
						(	$selected_option
						)
					&&	$selected_option == $option_value
					)
				||	(	!empty
						(	$selected_options
						)
					&&	in_array
						(	$option_value
						,	$selected_options
						)
					)
				):
				$option_attributes['SELECTED'] = '';
			endif;
			
			$select_options .= self::element
			(	array
				(	'tag_name'		=>	'OPTION'
				,	'attributes'	=>	$option_attributes
				,	'content'		=>	$option_content
				)
			);
		endforeach;
		
		if	(	!empty
				(	$attributes['onchange']
				)
			&&	!empty
				(	$selected_option
				)
			&&	!empty
				(	$attributes['class']
				)
			):
			$attributes['class'] .= ' in_use';
		endif;
		
		if	(	isset
				(	$attributes['multiple']
				)
			):
			$attributes['name'] .= '[]';
		endif;

		return self::element
		(	array
			(	'tag_name'		=>	'SELECT'
			,	'attributes'	=>	$attributes
			,	'content'		=>	$select_options
			)
		);
	}
	
	public static function valid_icon
	(	$args	=	array()
	)
	{	extract
		(	debug::function_argument_verify
			(	array
				(	'function'			=>	__CLASS__.'->'.__FUNCTION__
				,	'arguments_input'		=>	$args
				,	'arguments_descriptions'	=>	array
					(	'markup'				=>	array
						(	'default_value'		=>	'XHTML 1.1'
						)
					,	'color'				=>	array
						(	'blurb'				=>	'Which icon image to display.'
						,	'possible_values'		=>	array
							(	'blue'
							,	'gold'
							)
						,	'default_value'		=>	'blue'
						)
					)
				)
			)
		);
		
		return '<p><a href="http://validator.w3.org/check?uri=referer"><img border="0" src="img/valid-'
		.	strtolower
			(	preg_replace
				(	'/[ \.]/'
				,	''
				,	$markup
				)
			)
		.	'-'
		.	$color
		.	'.gif" alt="Valid '
		.	$markup
		.	'" TITLE="Valid '
		.	$markup
		.	'" height="31" width="88" /></a></p>'
		;
	}
	
}