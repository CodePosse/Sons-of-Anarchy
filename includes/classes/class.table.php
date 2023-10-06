<?php

class table {

	function __construct
	(	$args	=	array()
	)
	{	extract
		(	debug::function_argument_verify
			(	array
				(	'function'			=>	__CLASS__.'->'.__FUNCTION__
				,	'arguments_input'		=>	$args
				,	'arguments_descriptions'	=>	array
					(	'name'
/*
					,	'owner_id'				=>	array
						(	'default_value'				=>	0
						)
*/
					,	'hidden'					=>	array
						(	'possible_values'			=>	array
							(	0
							,	1
							)
						,	'default_value'				=>	0
						)
					)
				)
			)
		);
		
		$this->initialized	=	0;
		$this->name 		=	$name;
		$this->hidden		=	$hidden;
		
		$this->info_table		=	'field';
		$this->validator_table	=	'validator';
//		$this->owner_id		=	$owner_id;
		
//		$table_name_d = (strstr($this->name,'faq')) ? str_replace('faq','FAQ',$this->name) : $this->name;
//		$this->name_hyphenated = str_replace('_','-',$this->name);
		$this->name_plural = strings::pluralize
		(	$this->name
		);
		
		$this->title = '';
//		$this->title = strings::label($table_name_d);
//		$this->title_uppercase = strtoupper($this->title);
		
		$this->fields = 
		array();
		
		$this->record_title_template = 
		$this->blurb = 
		$this->is_node_table = 
		''
		;
		
		$this->request_form = $this->name
		.	'_1'
		;	
		
		$this->renderings = 0;
		
//		$this->label_fields = 1;
	}
	
	function get_field_prefs
	(	$args	=	array()
	)
	{	extract
		(	debug::function_argument_verify
			(	array
				(	'function'			=>	__CLASS__.'->'.__FUNCTION__
				,	'arguments_input'		=>	$args
				,	'arguments_descriptions'	=>	array
					(	'columns_info_ray'			=>	array
						(	'default_value'	=>	array()
						)
					)
				)
			)
		);
		
		$prefs = $GLOBALS['dbi']->get_result_array
		(	array
			(	'table'	=>	$this->info_table
			,	'key_by'	=>	'name'
			,	'equals'	=>	array
				(	'table_name'	=>	$this->name
				)
			)
		);
		
		if	(	count
				(	$columns_info_ray
				)
			):
			$columns_ray = array();
			foreach
				(	$columns_info_ray	as	$column	=>	$column_info_obj	
				):
				$columns_ray[$column_info_obj->name] = $column_info_obj;
			endforeach;
			foreach
				(	$prefs		as	$column_name	=>	$pref	
				):
				if	(	empty
						(	$columns_ray[$column_name]
						)
					):
					$deleted_pref = $GLOBALS['dbi']->affect_rows
					(	array
						(	'table'	=>	$this->info_table
						,	'act'	=>	'delete'
						,	'rows'	=>	array
							(	$pref['id']
							)
						)
					);
					unset
					(	$prefs[$column]
					);
				endif;
			endforeach;
			reset
			(	$prefs
			);
		endif;
		
		return $prefs;
	}
	
	function get_ownerships()
	// FOR TABLE OBJECTS owners OR owned PROPERTIES, THE FIELD NAMES ARE THE KEYS & THE VALUE IS THE OWNERSHIP ID
	// THIS IS TO ALLOW FUNCTION ARGUMENTS TO REFERENCE THE NAMES, SO NO ONE HAS TO MEMORIZE DB ROW IDs
	// NOTE THAT THIS IS THE ONLY SUCH CASE WHERE AN OWNER-RELATED ARRAY IS KEYED BY NAME RATHER THAN BY ID
	// $GLOBALS['dbi']->ownerships ARRAY & THE RECORD OBJECT owners AND owned PROPERTIES ARE KEYED BY ID
	//
	// $this->owners_or_owns[$owner_or_owned_name] = $ownership_id
	{	if	(	!isset
				(	$this->owners
				)
			||	$this->request_form	!=	$this->name
			):
			$this->owners = array();
			$owners = $GLOBALS['dbi']->get_result_array
			(	array
				(	'fields'	=>	array
					(	'id'
					)
				,	'table'		=>	'owner'
				,	'equals'	=>	array
					(	'owned_table'	=>	$this->name
					,	'status'		=>	'Active'
					)
				,	'order_by'	=>	array
					(	'owners_order'	=>	''
					)
				)
			);
			foreach
				(	$owners	as	$ownership_id	
				):
				$owner_name = $GLOBALS['dbi']->ownerships[$ownership_id]['owner_name'];
				$this->owners[$owner_name] = $ownership_id;
				if	(	isset
						(	$GLOBALS['page']->request[$this->request_form.'-'.$owner_name]
						)
					):
					$this->request[$owner_name] = $GLOBALS['page']->request[$this->request_form.'-'.$owner_name];
				endif;
			endforeach;
		endif;
		if	(	!isset
				(	$this->owns
				)
			||	$this->request_form	!=	$this->name
			):
			$this->owns = array();
			$owns = $GLOBALS['dbi']->get_result_array
			(	array
				(	'fields'	=>	array
					(	'id'
					)
				,	'table'		=>	'owner'
				,	'equals'	=>	array
					(	'owner_table'	=>	$this->name
					,	'status'		=>	'Active'
					)
				)
			);
			foreach
				(	$owns	as	$ownership_id	
				):
				$owned_name = $GLOBALS['dbi']->ownerships[$ownership_id]['owned_name'];
				$this->owns[$owned_name] = $ownership_id;
				if	(	isset
						(	$GLOBALS['page']->request[$this->request_form.'-'.$owned_name]
						)
					):
					$this->request[$owned_name] = $GLOBALS['page']->request[$this->request_form.'-'.$owned_name];
				endif;
			endforeach;
		endif;
	}
	
	function initialize()
	{	if	(	!$this->initialized
			):
			
			if	(	empty
					(	$GLOBALS['page']
					)
				):
				$GLOBALS['page'] = new page();
			endif;
			
			$this->request['act']			=	
			(	!empty
				(	$GLOBALS['page']->request['act']
				)
			&&	!empty
				(	$GLOBALS['page']->request['z']
				)
			&&	$this->name	==	$GLOBALS['page']->request['z']
			)
			?	$GLOBALS['page']->request['act']
			:	'view'
			;

			if	(	!empty
					(	$GLOBALS['page']->request
					)
				):
				foreach
					(	$GLOBALS['page']->request	as	$request_key	=>	$request_val
					):
					if	(	strstr
							(	$request_key
							,	$this->name
								.	'_'
							)
							==	$request_key
						):
						$this->request_form = substr
						(	$request_key
						,	0
						,	strpos
							(	$request_key
							,	'-'
							)
						);
						break;
					endif;
				endforeach;
				reset
				(	$GLOBALS['page']->request
				);
			endif;
			
			$table_related_inputs = array
			(	'first_item'		=>	0
			);
			foreach
				(	$table_related_inputs	as	$related_input	=>	$default_value
				):
				$this->request[$related_input] = 
				(	empty
					(	$GLOBALS['page']->request[$this->request_form.'-'.$related_input]
					)
				)
				?	$default_value
				:	$GLOBALS['page']->request[$this->request_form.'-'.$related_input]
				;
			endforeach;
			reset
			(	$table_related_inputs
			);
			
			$this->record = new record
			(	$this->name
			);
			
			// CREATE OBJECT BASED ON TABLE SCHEMA / PREFERENCES
			if	(	empty
					(	$this->title
					)
				):
				$this->title = strings::label
				(	$this->name
				);
			endif;
			$this->title_plural = strings::pluralize
			(	$this->title
			);
			
			$this->full_state_names = 1;
			
			$this->utility_row = 0;
			
			$this->max_rows_per_page = 100; // IF NOT ZERO: MAXIMUM TABLED RESULT ROWS TO DISPLAY PER PAGINATED PAGE
			$this->max_field_display_length = 0; // IF NOT ZERO: MAXIMUM CHARS TO DISPLAY PER FIELD VALUE IN TABLED RESULTS CELLS
			
			$field_related_inputs = array
			(	''			=>	0
			,	'sort'		=>	0
			,	'dir'		=>	0
			,	'year'		=>	1
			,	'month'		=>	1
			,	'day'		=>	1
			,	'hour'		=>	1
			,	'minute'	=>	1
			,	'second'	=>	1
			);
			
			// COLUMN INFO from SELECT single row query
			$columns = $GLOBALS['dbi']->get_result
			(	array
				(	'sql'		=>	"
						SELECT	*
						FROM	`".$this->name."`
						LIMIT	1
					"
				,	'tables'	=>	array
					(	$this->name
					)
				)
			);
			$columns_info_ray = $columns->fetch_fields();
			$unsettees = array
			(	'def'
			,	'flags'
			,	'length'
			);
			$columns->close();
			
			// CUSTOM FIELD DISPLAY / SORT PREFERENCES FROM __field TABLE
			$prefs = $this->get_field_prefs
			(	array
				(	'columns_info_ray'	=>	$columns_info_ray
				)
			);
			
			// FIELD INFO from SHOW COLUMNS query // SAME AS "DESC $table"
			$show_columns_sql = "	SHOW	COLUMNS
									FROM	`".$this->name."`
									";
			$this->fields = $GLOBALS['dbi']->result_to_array
			(	array
				(	'result'	=>	$GLOBALS['dbi']->get_result
					(	array
						(	'sql'		=>	$show_columns_sql
						,	'tables'	=>	array
							(	$this->name
							)
						)
					)
				,	'key_by'	=>	'Field'
				)
			);
			
			$default_sort = array();
			$sort_count = 1;
			foreach
				(	$GLOBALS['dbi']->default_sorts	as	$column_name	=>	$column_dir
				):
				if	(	!empty
						(	$this->fields[$column_name]
						)
					):
					$default_sort[$column_name] = array
					(	'sort_priority'		=>	$sort_count
					,	'sort_direction'	=>	$column_dir
					);
					$sort_count++;
				endif;
			endforeach;
			reset
			(	$GLOBALS['dbi']->default_sorts
			);
			
			foreach
				(	$columns_info_ray	as	$column	=>	$column_info_obj	
				):
				foreach
					(	$field_related_inputs	as	$related_input	=>	$aggregate_value	
					):
					if	(	empty
							(	$related_input
							)
						):
						// POSSIBLE FILE UPLOADS
						if	(	!empty
								(	$_FILES
								)
							&&	!empty
								(	$_FILES[$this->request_form.'-'.$column_info_obj->name]
								)
							):
							$this->request[$column_info_obj->name] = &$_FILES[$this->request_form.'-'.$column_info_obj->name];
						else:
							if	(	isset
									(	$GLOBALS['page']->request[$this->request_form.'-'.$column_info_obj->name]
									)
								):
								$column_info_obj->value = 
								$this->request[$column_info_obj->name] = 
								(	is_array
									(	$GLOBALS['page']->request[$this->request_form.'-'.$column_info_obj->name]
									)
								)
								?	$GLOBALS['page']->request[$this->request_form.'-'.$column_info_obj->name]
								:	trim
									(	$GLOBALS['page']->request[$this->request_form.'-'.$column_info_obj->name]
									)
								;
							endif;
						endif;
					else:
						if	(	empty
								(	$aggregate_value
								)
							):
							if	(	isset
									(	$GLOBALS['page']->request[$this->request_form.'-'.$column_info_obj->name.'-'.$related_input]
									)
								):
								$this->request[$column_info_obj->name.'-'.$related_input] = $GLOBALS['page']->request[$this->request_form.'-'.$column_info_obj->name.'-'.$related_input];
							endif;
						else:
							if	(	isset
									(	$GLOBALS['page']->request[$this->request_form.'-'.$column_info_obj->name.'-'.$related_input]
									)
								):
								if	(	empty
										(	$column_info_obj->aggregate_value
										)
									):
									$column_info_obj->aggregate_value = array();
								endif;
//								$this->request[$column_info_obj->name.'-'.$related_input] = 
								$column_info_obj->aggregate_value[$related_input] = 
								$GLOBALS['page']->request[$this->request_form.'-'.$column_info_obj->name.'-'.$related_input]
								;
							endif;
						endif;
					endif;
				endforeach;
				reset
				(	$field_related_inputs
				);
				$column_info_obj->column = $column;
				$column_info_obj->type = $GLOBALS['dbi']->field_types[$column_info_obj->type];
	//			$column_info_obj->max_value_length = $column_info_obj->max_length;
				$column_info_obj->max_length = $column_info_obj->length;
				eval
				(	variables::unset_en_masse
					(	array
						(	'unsettees'	=>	$unsettees
						,	'pre_scoper'	=>	'$column_info_obj->'
						)
					)
				);
	//			$column_info_obj->flags = $db->field_flags[$column_info_obj->flags];
	
	/*
	Flag Value 		Flag Description						Bit Flag
	
	NOT_NULL_FLAG		Field can't be NULL						  1
	PRI_KEY_FLAG		Field is part of a primary key			  2
	UNIQUE_KEY_FLAG	Field is part of a unique key				  4
	MULTIPLE_KEY_FLAG	Field is part of a non-unique key			  8
	UNSIGNED_FLAG		Field has the UNSIGNED attribute			 32
	ZEROFILL_FLAG		Field has the ZEROFILL attribute			 64
	BINARY_FLAG		Field has the BINARY attribute			128
	AUTO_INCREMENT_FLAG	Field has the AUTO_INCREMENT attribute		512
	
	ENUM_FLAG			Field is an ENUM (deprecated)
	SET_FLAG			Field is a SET (deprecated)
	BLOB_FLAG			Field is a BLOB or TEXT (deprecated)
	TIMESTAMP_FLAG		Field is a TIMESTAMP (deprecated)
	*/
	
				$this->fields[$column_info_obj->name] = new field
				(	array
					(	'fetch_field_object'	=>	$column_info_obj
					)
				);
				
				if	(	!count
						(	$prefs
						)
					||	empty
						(	$prefs[$column_info_obj->name]
						)
					):
					// ADD DEFAULT PREFS TO __field TABLE
					$values =	array
					(	'table_name'	=>	$this->name
					,	'name'			=>	$column_info_obj->name
					);
					if	(	!empty
							(	$default_sort[$column_info_obj->name]
							)
						):
						$values['sort_priority'] = $default_sort[$column_info_obj->name]['sort_priority'];
						$values['sort_direction'] = $default_sort[$column_info_obj->name]['sort_direction'];
					endif;
					$inserted_pref = $GLOBALS['dbi']->insert_row
					(	array
						(	'table'	=>	$this->info_table
						,	'row'	=>	$values
						)
					);
					$prefs_updated = 1;
				endif;
			endforeach;
			
			if	(	!count
					(	$prefs
					)
				||	!empty
					(	$prefs_updated
					)
				):
				$prefs = $this->get_field_prefs();
			endif;
			$unsettees = array
			(	'table_name'
			,	'name'
			,	'inserted'
			,	'updated'
	//		,	'id'
			);
			
			$fields = $GLOBALS['dbi']->get_result
			(	array
				(	'sql'		=>	$show_columns_sql
				,	'tables'	=>	array
					(	$this->name
					)
				)
			);
		
			while
				(	$row	=	$fields->fetch_array
					(	MYSQLI_ASSOC
					)
				):
				$this->fields[$row['Field']]->type_string = $row['Type'];
				$this->fields[$row['Field']]->null_allowed = 
				(	$row['Null']
				&&	$row['Null']	!=	'NO'
				)
				?	(bool) 1
				:	(bool) 0
				;
				$this->fields[$row['Field']]->keys = $row['Key'];
				$this->fields[$row['Field']]->default_value = 
				(	is_null
					(	$row['Default']
					)
				)
				?	''
				:	$row['Default']
				;
				$this->fields[$row['Field']]->flags_string = $row['Extra'];
				
				eval
				(	variables::unset_en_masse
					(	array
						(	'unsettees'	=>	$unsettees
						,	'pre_scoper'	=>	'$prefs["'
							.	$row['Field']
							.	'"]["'
						,	'post_scoper'	=>	'"]'
						)
					)
				);
				
				foreach
					(	$prefs[$row['Field']]	as	$pref	=>	$pref_value	
					):
					switch 
						(	$pref_value	
						):
						case NULL:
							$this->fields[$row['Field']]->$pref = '';
							break;
						case 'Yes':
							$this->fields[$row['Field']]->$pref = (bool) 1;
							break;
						case 'No':
							$this->fields[$row['Field']]->$pref = (bool) 0;
							break;
						default:
							// WHAT ABOUT DECIMAL VALUES ?????
							$this->fields[$row['Field']]->$pref = 
							(	is_numeric
								(	$pref_value
								)
							)
							?	(int) $pref_value
							:	$pref_value
							;
					endswitch;				
				endforeach;
				
				if	(	isset
						(	$this->request[$row['Field'].'-sort']
						)
					&&	(	$this->request[$row['Field'].'-sort'] != $this->fields[$row['Field']]->sort_priority
						||	$this->request[$row['Field'].'-dir'] != $this->fields[$row['Field']]->sort_direction
						)
					):
					$this->fields[$row['Field']]->sort_priority = $this->request[$row['Field'].'-sort'];
					$this->fields[$row['Field']]->sort_direction = 
					(	empty
						(	$this->fields[$row['Field']]->sort_priority
						)	
					)
					?	'ASC'
					:	$this->request[$row['Field'].'-dir']
					;
/*					
					// AUTO-UPDATE FIELD SORTING ACCORDING TO USER SELECTION 
					$GLOBALS['dbi']->affect_rows
					(	array
						(	'table'	=>	$this->info_table
						,	'rows'	=>	array
							(	$this->fields[$row['Field']]->id	=>	array
								(	'sort_priority'			=>	$this->fields[$row['Field']]->sort_priority
								,	'sort_direction'		=>	$this->fields[$row['Field']]->sort_direction
								)
							)
						)
					);
*/
				endif;
				
				// THIS SETS THE FIELD'S encrypt PROPERTY FLAG TO THE PAGE'S crypt_key
				if	(	$this->fields[$row['Field']]->encrypt
					):
					$this->fields[$row['Field']]->encrypt = $GLOBALS['cfg']['crypt_key'];
				endif;
				
				if	(	empty
						(	$this->fields[$row['Field']]->title
						)
					):
					$this->fields[$row['Field']]->title = strings::label
					(	$row['Field']
					);
				endif;
				
				
				// GET VALIDATION SCHEMES FOR THIS FIELD, IF ANY
				if	(	!$this->hidden
					):
					$this->fields[$row['Field']]->get_validators();
				endif;
				
				// INSERT DEFAULT VALIDATION SCHEMES FOR EACH FIELD, DEPENDENT ON PREFS
				$new_validators = array();
				
				// UNIQUE
				if	(	!empty
						(	$GLOBALS['dbi']->tables[$this->validator_table]->records['unique_index']
						)
					&&	is_array
						(	$GLOBALS['dbi']->tables[$this->validator_table]->records['unique_index']
						)
					):
					$unique_index = $GLOBALS['dbi']->tables[$this->validator_table]->records['unique_index'];
					
					if	(	$this->fields[$row['Field']]->keys == 'UNI'
						&&	empty
							(	$this->fields[$row['Field']]->validators[$unique_index['id']]
							)
						):
						$new_validators[$unique_index['id']] = $unique_index; 
					endif;
				endif;
				
				// EMAIL
				if	(	$this->fields[$row['Field']]->name_contains_word
						(	'email'
						)
					&&	empty
						(	$GLOBALS['dbi']->tables[$this->validator_table]->records['email']
						)
					):
					$email = $GLOBALS['dbi']->field_validators['email'];
					$new_validators[$email['id']] = $email;
				endif;

				// PHONE NUMBER
				if	(	$this->fields[$row['Field']]->name_contains_word
						(	'phone'
						)
					&&	empty
						(	$GLOBALS['dbi']->tables[$this->validator_table]->records['phone_number']
						)
					):
					$phone_number = $GLOBALS['dbi']->field_validators['phone_number'];
					$new_validators[$phone_number['id']] = $phone_number;
				endif;
/*				
				// DATE // NOT HERE - GOTTA USE FUNCTION TO VALIDATE, NOT REGEX
				if	(	$this->fields[$row['Field']]->is_datetime('date')
					):
					$new_validators = 
				endif;
*/				
				if	(	count
						(	$new_validators
						)
					):
					
					// insert multiple validators at once, baby!s
					
					foreach
						(	$new_validators	as	$validator_id	=>	$validator	
						):
						$this->fields[$row['Field']]->validators[$validator_id] = $validator;
					endforeach;
					reset
					(	$new_validators
					);
				endif;
			endwhile;
			$fields->close();
			
			$this->get_ownerships();
			
			if	(	$this->is_node_table
				):
				$GLOBALS['page']->node_trees[$this->name] = array();
			endif;
			
			$this->initialized = 1;
/*			
			if	(	isset($GLOBALS['page']->request['id'])
				):
				$this->record->initialize($GLOBALS['page']->request['id']);
			endif;
*/			

			if	(	!empty
					(	$this->fields['status']
					)
				&&	!empty
					(	$this->fields['active_from']
					)
				&&	!empty
					(	$this->fields['active_until']
					)
				):
				$now = date
				(	'Y-m-d H:i:s'
				);
				
				//	WHEN SET, active_from AND/OR active_until DATE VALUES TAKE PRECEDENCE OVER Active/Hidden SETTING IN ALL CASES
				$activate_by_date = $GLOBALS['dbi']->get_result
				(	"	UPDATE	`".$this->name."`
						SET		status	=	'Active'
						WHERE	(	(	active_from		IS	NOT	NULL
									AND	active_from		!=	''
									AND	active_from		!=	'0000-00-00 00:00:00'
									)
								OR	(	active_until	IS	NOT NULL
									AND	active_until	!=	''
									AND	active_until	!=	'0000-00-00 00:00:00'
									)
								)
						AND		(	active_from		IS	NULL
								OR	active_from		=	''
								OR	active_from		=	'0000-00-00 00:00:00'
								OR	active_from		<=	'$now'
								)
						AND		(	active_until	IS	NULL
								OR	active_until	=	''
								OR	active_until	=	'0000-00-00 00:00:00'
								OR	active_until	>	'$now'	
								)
					"
				);
/*
				$deactivate_by_date = $GLOBALS['dbi']->get_result
				(	"	UPDATE	`".$this->name."`
						SET		status	=	'Hidden'
						WHERE	(	active_from		IS	NOT	NULL
								AND	active_from		!=	''
								AND	active_from		!=	'0000-00-00 00:00:00'
								AND	active_from		>	'$now'
								)
						OR		(	active_until	IS	NOT NULL
								AND	active_until	!=	''
								AND	active_until	!=	'0000-00-00 00:00:00'
								AND	active_until	<=	'$now'
								)
						"
				);
*/
				$this->hidden_records = 0;
				
			endif;

			return 1;
		else:
			return 0;
		endif;
	}
	
	private function query_filter
	(	$args	=	array()
	)
	{	extract
		(	debug::function_argument_verify
			(	array
				(	'function'			=>	__CLASS__.'->'.__FUNCTION__
				,	'arguments_input'		=>	$args
				,	'arguments_descriptions'	=>	array
					(	'whered'			=>	array
						(	'blurb'			=>	'True or false: whether or not a WHERE statement has already been concatenated onto the query string.'
						,	'possible_values'	=>	array
							(	0
							,	1
							)
						,	'default_value'	=>	0
						)
					,	'owners'		=>	array
						(	'default_value'	=>	array()
						)
					,	'return_as'		=>	array
						(	'possible_values'	=>	array
							(	'sql'
							,	'text'
							)
						,	'default_value'	=>	'sql'
						)
					)
				)
			)
		);
		
		$filter = '';
		foreach
			(	$this->fields as $field_name => &$field
			):
			if	(	empty
					(	$field->aggregate_value
					)
				):
				if	(	isset
						(	$this->request[$field_name]
						)
					):
					$request_has_value =
					(	is_array
						(	$this->request[$field_name]
						)	
					)
					?	count
						(	$this->request[$field_name]
						)
					:	strlen
						(	trim
							(	$this->request[$field_name]
							)
						)
					;
				else:
					$request_has_value = 0;
				endif;
				
				if	(	$request_has_value
					&&	!in_array
						(	$field_name
						,	$GLOBALS['dbi']->fields_ignore
						)
					):
					switch
						(	$return_as	
						):
						case 'text':
							if	(	!$field->hidden_from_table
								):
//								$field->title
/*
								if (is_array($fuc_keys[$key])):
										$val_splay = $fuc_keys[$key][$val];
								else:
									if (is_array($val)) $val = implode(', ',$val);
									$val_splay = (empty($fields_by_name[$key]['foreign_key_table'])) ? $val : display_foreign_ID($fields_by_name[$key]['foreign_key_table'],$val,0,0,$fields_by_name[$key]['foreign_query'],$fields_by_name[$key]['foreign_display_template']);
								endif;
								$should_be_enum_fields = array
								(	'buyers_age'
								,	'buyers_sex'
								,	'buyers_state'
								,	'dealer_state'
								);
								$equals_or_like = ((in_array($fields_by_name[$key]['type'],$fields_numeric) && is_numeric($val)) || in_array($key,$should_be_enum_fields)) ? '=' : 'LIKE';
								if ($key_label == 'Brandid') $key_label = 'Brand';
								$query_filter .= $key_label.' '.$equals_or_like.' "'.$val_splay.'"; ';
//								$query_filter .= $key_label.' '.$equals_or_like.' ';
//								$query_filter .= ($equals_or_like == '=' && !in_array($key,$should_be_enum_fields)) ? $val_splay : '"'.$val_splay.'"';
//								$query_filter .= '; ';
								endif;
								*/
							endif;
							break;
						default: // case 'sql':
							if	(	!empty
									(	$owners
									)
								):
								$owned = $GLOBALS['dbi']->get_owned_records
								(	array
									(	'owned_table'	=>	$this->name
									,	'owners'		=>	$owners
									)
								);
								if	(	!empty
										(	$owned
										)
									):
									if	(	$whered
										):
										$filter .= " AND ";
									else:
										$whered = 1;
									endif;
									$filter .= "	id	IN	("
									.	implode
										(	','
										,	$owned
										)
									.	")"
									;
								endif;
							endif;
							
							if	(	$whered
								):
								$filter .= " AND ";
							else:
								$whered = 1;
							endif;
							$use_table =
							(	empty
								(	$field->table
								)	
							)
							?	''
							:	$field->table.'.'
							;
							if	(	in_array
									(	$field->type
									,	$GLOBALS['dbi']->field_types_numeric
									)
								&&	is_numeric
									(	$this->request[$field_name]
									)
								):
								$filter .= " "
								.	$use_table
								.	$field_name
								.	" = "
								.	$this->request[$field_name]
								.	" "
								;
							else:
								if	(	is_array
										(	$this->request[$field_name]
										)
									):
									$filter .= " ( ";
									foreach
										(	$this->request[$field_name] as $array_val
										):
										$filter .=
										(	stristr
											(	$field->type_string
											,	'set'
											)	==	$field->type_string	
										)
										?	" "
											.	$use_table
											.	$field_name
											.	" = '"
											.	addslashes
												(	$array_val
												)
											.	"' OR"
										:	" LOWER(TRIM("
											.	$use_table
											.	$field_name
											.	")) LIKE LOWER(TRIM('%"
											.	addslashes
												(	$array_val
												)
											.	"%')) OR"
									;
									endforeach;
									$filter = substr
									(	$filter
									,	0
									,	-2
									)
									.	" ) "
									;
								else:
									$filter .=
									(	stristr
										(	$field->type_string
										,	'enum'
										)	==	$field->type_string	
									)
									?	" "
										.	$use_table
										.	$field_name
										.	" = '"
										.	addslashes
											(	$this->request[$field_name]
											)
										.	"' "
								 	:	" LOWER(TRIM("
										.	$use_table
										.	$field_name
										.	")) LIKE LOWER(TRIM('%"
										.	addslashes
											(	$this->request[$field_name]
											)
										.	"%'))	"
									;
								endif;
							endif;
					endswitch;
				endif;
			else:
				$chunks = &$field->aggregate_value;
				if	(	(	isset
							(	$chunks['year']
							)
						&&	isset
							(	$chunks['month']
							)
						&&	isset
							(	$chunks['day']
							)
						)
					&&	(	$chunks['year'] > 0
						||	$chunks['month'] > 0
						||	$chunks['day']	> 0
						)
					):
					if	(	$chunks['year'] > 0
						&&	$chunks['month'] > 0
						&&	$chunks['day'] > 0
						&&	!checkdate
							(	$chunks['month']
							,	$chunks['day']
							,	$chunks['year']
							)
						):
						$chunks = date_time::valid_date
						(	array
							(	'year'			=>	$chunks['year']
							,	'month'			=>	$chunks['month']
							,	'day'			=>	$chunks['day']
							,	'return_array'	=>	1
							)
						);
					endif;
					switch
						(	$return_as
						):
						case 'text':
							if	(	!$field->hidden_from_table
								):
								// FILTER TEXT DESCRIPTION
/*
						if (empty($fields_by_name[$key]['hidden'])) {
							$key_label = ($show_field_labels && !empty($fields_by_name[$which_date]['field_label'])) ? $fields_by_name[$which_date]['field_label'] : prop_label($which_date);
							if ($mend_ray['year'] > 0) {
								$query_filter .= $key_label.' Year = '.$mend_ray['year'].'; ';
								if ($mend_ray['month'] > 0) {
									$query_filter .= $key_label.' Month = '.$mend_ray['month'].'; ';
									if ($mend_ray['day'] > 0) $query_filter .= $key_label.' Day = '.$mend_ray['day'].'; ';
									}
								}
							}
*/
							endif;
							break;
						default: // case 'sql':
							if	(	$whered
								):
								$filter .= " AND ";
							else:
								$whered = 1;
							endif;
							$use_table =
							(	empty
								(	$field->table
								)	
							)
							?	''
							:	$field->table.'.'
							;
							$use_time = 
							(	$field->is_datetime
								(	'time'
								)	
							)
							?	' 00:00:00'
							:	''
							;
							if	(	$chunks['year'] > 0
								):
								if	(	$chunks['month'] > 0
									):
									if	(	$chunks['day'] > 0
										):
										// YEAR & MONTH & DAY // ALL THREE DATE PARTS SUPPLIED
										$filter .= " "
										.	$use_table
										.	$field_name
										.	" >= '"
										.	$chunks['year']
										.	"-"
										.	$chunks['month']
										.	"-"
										.	$chunks['day']
										.	$use_time
										.	"' "
										
										.	" AND "
										.	$use_table
										.	$field_name
										.	" < '"
										.	$chunks['year']
										.	"-"
										.	$chunks['month']
										.	"-"
										.	str_pad
											(	(	$chunks['day']
												+	1
												)
											,	2
											,	'0'
											,	STR_PAD_LEFT
											)
										.	$use_time
										.	"' "
										;
									else:
										// YEAR & MONTH
										$filter .= " "
										.	$use_table
										.	$field_name
										.	" >= '"
										.	$chunks['year']
										.	"-"
										.	$chunks['month']
										.	"-01"
										.	$use_time
										.	"' "
										
										.	" AND "
										.	$use_table
										.	$field_name
										.	" < '"
										.	$chunks['year']
										.	"-"
										.	str_pad
											(	(	$chunks['month']
												+	1
												)
											,	2
											,	'0'
											,	STR_PAD_LEFT
											)
										.	"-01"
										.	$use_time
										.	"' "
										;
									endif;
								else:
									// YEAR ONLY IF MONTH EMPTY
									$filter .= " "
									.	$use_table
									.	$field_name
									.	" >= '"
									.	$chunks['year']
									.	"-01-01"
									.	$use_time
									.	"' "
									
									.	" AND "
									.	$use_table
									.	$field_name
									.	" < '"
									.	str_pad
										(	(	$chunks['year']
											+	1
											)
										,	4
										,	'0'
										,	STR_PAD_LEFT
										)
									.	"-01-01"
									.	$use_time
									.	"' "
									;
									
									$chunks['day'] = 0;
								endif;
							else:
								$chunks['month'] = 
								$chunks['day'] = 
								0
								;
							endif;
					endswitch;
				endif;
			endif;
		endforeach;
		
		if	(	!empty
				(	$GLOBALS['user']
				)
			&&	isset
				(	$this->hidden_records
				)
			&&	!$GLOBALS['user']->is_admin()
			):
			if	(	!is_array
					(	$this->hidden_records
					)
				):
				$this->hidden_records = $GLOBALS['dbi']->get_result_array
				(	array
					(	'table'			=>	$this->name
					,	'fields'		=>	array
						(	'id'
						)
					,	'key_by'		=>	0
					,	'equals'		=>	array
						(	'status'		=>	'Hidden'
						)
					,	'order_by'		=>	array
						(	'id'			=>	''
						)
					)
				);
			endif;
			if	(	!empty
					(	$this->hidden_records
					)
				):
				if	(	$whered
					):
					$filter .= " AND ";
				endif;
				$use_table =
				(	empty
					(	$this->fields['id']->table
					)	
				)
				?	''
				:	$this->fields['id']->table.'.'
				;
				$filter .= " "
				.	$use_table
				.	"id NOT IN ("
				.	implode
					(	','
					,	$this->hidden_records
					)
				.	") "
				;
			endif;
		endif;
		
		reset
		(	$this->fields
		);
		
		return $filter;
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
					(	'equals'				=>	array
						(	'blurb'				=>	"WHERE X = Y // array('x1'=>'y1','x2'=>'y2',etc.)"
						,	'default_value'		=>	0
						)
					,	'in'					=>	array
						(	'default_value'			=>	0
						)
					,	'order_by'		=>	array
						(	'blurb'			=>	"ORDER BY column direction // array('column1'=>'direction1',etc.)"
						,	'default_value'	=>	array()
						)
					,	'limit_lo'			=>	array
						(	'default_value'		=>	0
						)
					,	'limit_hi'			=>	array
						(	'default_value'		=>	0
						)
					,	'owners'			=>	array
						(	'default_value'		=>	$GLOBALS['user']->owners
						)
					,	'label_fields'			=>	array
						(	'possible_values'		=>	array
							(	0
							,	1
							)
						,	'default_value'		=>	1
						)
					,	'template'			=>	array
						(	'default_value'		=>	$this->name
						)
					,	'template_values'	=>	array
						(	'default_value'		=>	0
						)
					,	'paginate'			=>	array
						(	'possible_values'		=>	array
							(	0
							,	1
							)
						,	'default_value'		=>	1
						)
					,	'expose_query'			=>	array
						(	'blurb'				=>	'TRUE: EXPOSES SQL QUERY'
						,	'default_value'		=>	0
						)
					,	'greedy'			=>	array
						(	'blurb'				=>	'If false, returns only records owned by all owners.  If true, returns all records owned by any owner.  This argument has no effect if no owners are present.'
						,	'possible_values'	=>	array
							(	0
							,	1
							)
						,	'default_value'		=>	0
						)
					,	'records'			=>	array
						(	'default_value'		=>	array
							(
							)
						)
					)
				)
			)
		);
		
		$this->initialize();
		
		if	(	empty
				(	$limit_lo
				)	
			&&	!empty
				(	$this->request['first_item']
				)
			):
			$limit_lo = $this->request['first_item'];
		endif;
			
	
/*
		// THE FOLLOWING PART MAY FUCK UP DEEP OWNERSHIP
		if	(	!empty
				(	$owners
				)
			):
			$relevant_owners = array();
			foreach
				(	$owners	as	$ownership_id	=>	$owner
				):
				if	(	$GLOBALS['dbi']->ownerships[$ownership_id]['owned_table'] == $this->name
					):
					$relevant_owners[$ownership_id]	= $owner;
				endif;
			endforeach;
			$owners = $relevant_owners;
			unset
			(	$relevant_owners
			);
		endif;
*/
		// REMOVE ADMINISTRATIVE ROLES FROM QUERY CONSIDERATION
		if	(	$owners	==	$GLOBALS['user']->owners
			):
			unset
			(	$owners[2]
			);
		endif;
		
		$this->template = new table_template
		(	array
			(	'name'		=>	$template
			,	'table'		=>	$this->name
			,	'values'	=>	$template_values
			,	'owners'	=>	$owners
			)
		);
		
		if	(	$paginate
			):
			if	(	empty
					(	$limit_hi
					)
				):
				$limit_hi = $this->template->template['pagination']['items_per_page'];
			else:
				$this->template->template['pagination']['items_per_page'] = $limit_hi;
			endif;
		else:
			$limit_hi =
			$this->template->template['pagination']['items_per_page'] = 
			0
			;
		endif;
		
		if	(	!empty
				(	$equals
				)
			&&	$this->name == $GLOBALS['page']->request['z']
			&&	empty
				(	$GLOBALS['page']->request['id']
				)
			):
			foreach
				(	$equals	as	$field => $value	
				):
				$this->fields[$field]->hide_from_table = 1;
			endforeach;
		endif;
		
		$new_order_by = array();
		foreach
			(	$this->template->template['columns']	as	$column	=>	$column_info
			):
			if	(	!empty
					(	$this->owners[$column_info['name']]
					)
				&&	!empty
					(	$this->request[$column_info['name']]
					)
				):
				if	(	empty
						(	$owners[$this->owners[$column_info['name']]]
						)
					):
					$owners[$this->owners[$column_info['name']]] = array
					(	$this->request[$column_info['name']]
					);
				else:
					$owners[$this->owners[$column_info['name']]][] = $owners[$this->owners[$column_info['name']]];
				endif;
				$owner_table = $GLOBALS['dbi']->ownerships[$this->owners[$column_info['name']]]['owner_table'];
				if	(	$GLOBALS['dbi']->tables[$owner_table]->is_node_table
					):
					if	(	empty
							(	$GLOBALS['page']->node_trees[$owner_table][$this->request[$column_info['name']]]
							)
						):
						$GLOBALS['page']->node_trees[$owner_table][$this->request[$column_info['name']]] = new node_tree
						(	array
							(	'owner_node'	=>	$this->request[$column_info['name']]
							,	'node_table'	=>	$owner_table
							)
						);
					endif;
					$owners[$this->owners[$column_info['name']]] = array_merge
					(	$owners[$this->owners[$column_info['name']]]
					,	array_keys
						(	$GLOBALS['page']->node_trees[$owner_table][$this->request[$column_info['name']]]->hierarchy[$this->request[$column_info['name']]]
						)
					);
				endif;
			endif;
			if	(	!empty
					(	$column_info['sort']['priority']
					)
				):
				$new_order_by[$column_info['sort']['priority']] = array
				(	$column_info['field']	=>	$column_info['sort']['direction']
				);
			endif;
		endforeach;
		reset
		(	$this->template->template['columns']
		);

		if	(	empty
				(	$records
				)
			):
			$this->template->records_total = $this->select_records
			(	array
				(	'equals'		=>	$equals
				,	'in'			=>	$in
				,	'owners'		=>	$owners
				,	'count_only'	=>	1
				,	'greedy'		=>	$greedy
				,	'expose_query'	=>	$expose_query
				)
			);

			if	(	$this->template->records_total	
				):
				$temp_request = $this->request;
				unset
				(	$temp_request['act']
				,	$temp_request['first_item']
				);
				if	(	empty
						(	$temp_request
						)
					):
					$this->template->template['default_sort'] = '';
				endif;
				if	(	!empty
						(	$new_order_by
						)
					):
					ksort
					(	$new_order_by
					);
					$order_by = array();
					foreach
						(	$new_order_by	as	$sort_priority	=>	$sort_info
						):
						$order_by[key($sort_info)] = current
						(	$sort_info
						);
					endforeach;
				endif;
				
				$select_o_ray = array
				(	'equals'		=>	$equals
				,	'in'			=>	$in
				,	'order_by'		=>	$order_by
				,	'owners'		=>	$owners
				,	'greedy'		=>	$greedy
				,	'expose_query'	=>	$expose_query
				);
				if	(	$paginate
					):
					$select_o_ray['limit_lo'] = $limit_lo;
					$select_o_ray['limit_hi'] = $limit_hi;
				endif;
				$this->template->records = $this->select_records
				(	$select_o_ray
				);
			endif;
		else:
			$this->template->records_total = count
			(	$records
			);
			$this->template->records = $records;
		endif;

		if	(	!empty
				(	$this->template->template['reorder']
				)
			&&	!empty
				(	$in['id']
				)
			):
			
			$reorder_button = array
			(	$this->template->template['reorder']
			);
			$begin_query = strpos
			(	$reorder_button[0]
			,	$_SERVER['SCRIPT_NAME']
				.	'?z='
			);
			$end_query = strpos
			(	$reorder_button[0]
			,	'">Re-'
			);
			$reorder_button[1] = substr
			(	$reorder_button[0]
			,	0
			,	$begin_query
			);
			$reorder_button[2] = uri::generate
			(	array
				(	'query'				=>	substr
					(	$reorder_button[0]
					,	$begin_query
						+	2
					,	strrpos
						(	$reorder_button[0]
						,	'"'
						)
						-	$begin_query
						-	2
					)
					.	'&in='
					.	implode
						(	','
						,	$in['id']
						)
				,	'query_crypt_key'	=>	$GLOBALS['page']->url_crypt_key
				)
			);
			$reorder_button[3] = substr
			(	$reorder_button[0]
			,	$end_query
			);
			unset
			(	$reorder_button[0]
			);
			
			$this->template->template['reorder'] = implode
			(	''
			,	$reorder_button
			);
		endif;
	
		if	(	$this->name == $template
			&&	empty
				(	$this->template->records
				)
			):
			// IF NO RECORDS ARE FOUND IN TABLE
			// CHECK ONE MORE TIME WITH NO FILTERS
			
			$table_populated = $this->select_records
			(	array
				(	'count_only'	=>	1
				,	'expose_query'	=>	$expose_query
				)
			);
			$query_ray =
			(	$table_populated
			)
			?	array
				(	'z'			=>	$GLOBALS['page']->request['z']
				)
			:	array
				(	'z'			=>	$this->name
//				,	'id'		=>	0  // WHEN COMMENTED OUT: WILL NOT REDIRECT TO CREATE NEW RECORD IF NO MATCHING RECORDS ARE FOUND
				)
			;
			
			if	(	!empty
					(	$GLOBALS['page']->request['most_recent_filter']
					)
				):
				$query_ray[$GLOBALS['page']->request['most_recent_filter']] = $GLOBALS['page']->request[$GLOBALS['page']->request['most_recent_filter']];
			endif;
				
			$GLOBALS['page']->redirect
			(	uri::generate
				(	array
					(	'query'	=>	$query_ray
					,	'query_crypt_key'	=>	$GLOBALS['page']->url_crypt_key
					)
				)
			);
		else:
			return $this->template->render();
		endif;

	}
	
	function render_record_title
	(	$args	=	array()
	)
	{	extract
		(	debug::function_argument_verify
			(	array
				(	'function'			=>	__CLASS__.'->'.__FUNCTION__
				,	'arguments_input'		=>	$args
				,	'arguments_descriptions'	=>	array
					(	'values'		=>	array
						(	'blurb'	=>	'Array of record values keyed by db field names.'
						)
					,	'template'	=>	array
						(	'default_value'	=>	$this->record_title_template
						)
					,	'value_masks'	=>	array
						(	'blurb'			=>	'Display masks for rendered values, arranged in array keyed by field names.'
						,	'default_value'	=>	array()
						)
					)
				)
			)
		);
		
		$this->initialize();

		$additional_values = array();
		foreach
			(	$values	as	$val_key	=>	$val_val	
			):
			if	(	$this->fields[$val_key]->is_datetime
					(	'either'
					)
				&&	!empty
					(	$val_val
					)
				):
				$additional_values = array_merge
				(	$additional_values
				,	date_time::db_explode
					(	array
						(	'field'		=>	$val_key
						,	'value'		=>	$val_val
						)
					)
				);
			endif;
		endforeach;
		reset
		(	$values
		);
		$values = array_merge
		(	$values
		,	$additional_values
		);
		
		$record_title = strings::replace_keys_with_values
		(	array
			(	'template_string'	=>	$template
			,	'values'			=>	$values
			)
		);
		
		$record_title = preg_replace
		(	'/\( *\)|\[ *\]|{ *}/'
		,	''
		,	$record_title
		);
		
		return $record_title;
	}

	function reorder
	(	$args	=	array()
	)
	{	extract
		(	debug::function_argument_verify
			(	array
				(	'function'			=>	__CLASS__.'->'.__FUNCTION__
				,	'arguments_input'		=>	$args
				,	'arguments_descriptions'	=>	array
					(	'equals'	=>	array
						(	'default_value'	=>	0
						)
					,	'in'		=>	array
						(	'default_value'	=>	0
						)
					,	'owners'			=>	array
						(	'default_value'		=>	array()
						)
					)
				)
			)
		);

		if	(	!empty
				(	$GLOBALS['page']->request['new_order']
				)
			):
			$new_order_ray = explode
			(	'|'
			,	$GLOBALS['page']->request['new_order']
			);
			
			if	(	!empty
				 	(	$owners
					)
				):
				$owned = $GLOBALS['dbi']->get_owned_records
				(	array
				 	(	'owners'	=>	$owners
					)
				);
				$update_table = 'owned';
			else:
				$update_table = $this->name;
			endif;
			
			$rows = array();
			foreach
				(	$new_order_ray	as	$new_order	=>	$order_this
				):
				$new_order++;
				if	(	!empty
					 	(	$owned
						)
					):
					$order_this = array_search
					(	$order_this
					,	$owned
					);
				endif;
				$rows[$order_this] = array
				(	'sort_order'	=>	$new_order
				);
			endforeach;

			$updated_rows = $GLOBALS['dbi']->affect_rows
			(	array
				(	'table'	=>	$update_table
				,	'rows'	=>	$rows
				)
			);

			$GLOBALS['page']->redirect
			(	$GLOBALS['page']->request['return_to']
			);
		endif;
		
		$GLOBALS['page']->style_sheets[] = 'lists';

		$GLOBALS['page']->scripts['src'][] = 'vendor/tool-man/core';
		$GLOBALS['page']->scripts['src'][] = 'vendor/tool-man/events';
		$GLOBALS['page']->scripts['src'][] = 'vendor/tool-man/css';
		$GLOBALS['page']->scripts['src'][] = 'vendor/tool-man/coordinates';
		$GLOBALS['page']->scripts['src'][] = 'vendor/tool-man/drag';
		$GLOBALS['page']->scripts['src'][] = 'vendor/tool-man/dragsort';

		$GLOBALS['page']->scripts['functions'][] = '
var dragsort = ToolMan.dragsort();
var junkdrawer = ToolMan.junkdrawer();

function verticalOnly(item) {
	item.toolManDragGroup.verticalOnly();
}

function speak(id, what) {
	var element = document.getElementById(id);
	element.innerHTML = \'Clicked \' + what;
}

function saveOrder(item) {
	var group = item.toolManDragGroup;
	var list = group.element.parentNode;
	var id = list.getAttribute(\'id\');
	if (id == null) return;
	group.register(\'dragend\', function() {
		document.getElementById(\'new_order\').value = junkdrawer.serializeList(list);
	})
}
';
		$GLOBALS['page']->scripts['ready'][] = '
dragsort.makeListSortable(document.getElementById(\''.$this->name.'_dragorder\'),verticalOnly,saveOrder);		
';
		$order_items_args = array
		(	'table'			=>	$this->name
		,	'equals'		=>	$equals
		,	'in'			=>	$in
		);
		if	(	empty
			 	(	$owners
				)
			):
			$order_items_args['order_by'] = array
			(	'sort_order'	=>	''
			);
		else:
			$order_items_args['owners'] = $owners;
		endif;

		$order_items = $GLOBALS['dbi']->get_result_array
		(	$order_items_args
		);

		if	(	count
				(	$order_items
				)	>	1
			):

			if	(	empty
				 	(	$owners
					)
				):
				$ownership_id = 0;
				$owner_id = 0;
			else:
				$ownership_id = key
				(	$owners
				);
				$owner_id = array_pop
				(	$owners[$ownership_id]
				);
			endif;

			$return_to_url = uri::generate
			(	array
				(	'query'	=>	(	empty
									(	$owners
									)
									?	array
										(	'z'		=>	$this->name
										)
									:	array
										(	'z'		=>	$GLOBALS['dbi']->ownerships[$ownership_id]['owner_table']
										,	'id'	=>	$owner_id
										,	'owned'	=>	$this->name
										)
								)
				,	'query_crypt_key'	=>	$GLOBALS['page']->url_crypt_key
				)
			);

			$hidden_inputs = xhtml::hidden_inputs
			(	array
				(	'z'				=>	$this->name
				,	'act'			=>	'reorder'
				,	'new_order'		=>	''
				,	'return_to'		=>	$return_to_url // $_SERVER['HTTP_REFERER']
				,	'equals'		=>	$equals
				,	'in'			=>	$in
				,	'ownership_id'	=>	$ownership_id
				,	'owner_id'		=>	$owner_id
				)
			);

			$button_title = strings::label
			(	'submit_new_order'
			);
			$button_anchor = xhtml::element
			(	array
				(	'tag_name'		=>	'A'
				,	'attributes'	=>	array
					(	'HREF'			=>	'#'
					,	'onMouseOver'	=>	"window.status='"
						.	$button_title
						.	"';return true;"
					,	'onMouseOut'	=>	"window.status='';return true;"
					,	'onClick'		=>	'document.order_form.submit();return false'
					)
				,	'content'		=>	$button_title
				)
			);
			
			$submit_button = xhtml::element
			(	array
				(	'tag_name'		=>	'DIV'
				,	'attributes'	=>	array
					(	'CLASS'			=>	'record_button float_left'
					)
				,	'content'		=>	$button_anchor
				)
			);
			
			$cancel_button = xhtml::element
			(	array
				(	'tag_name'		=>	'DIV'
				,	'attributes'	=>	array
					(	'CLASS'			=>	'record_button float_left'
					)
				,	'content'		=>	xhtml::element
					(	array
						(	'tag_name'		=>	'A'
						,	'attributes'	=>	array
							(	'HREF'			=>	'#'
							,	'onMouseOver'	=>	"window.status='Cancel Re-Ordering';return true;"
							,	'onMouseOut'	=>	"window.status='';return true;"
							,	'onClick'		=>	"window.location.href='"
								.	$return_to_url
								.	"';return false"
							)
						,	'content'		=>	'Cancel Re-Ordering'
						)
					)
				)
			);
/*			
// MOMENTARY KLUDGERY TO PREPEND OWNER OWNER TITLES TO OPTION TEXT FOR CERTAIN TABLES
// UNSURE AS OF NOW HOW TO GET THIS INFO INTO THIS FUNCTION IN A MORE MODULAR FASHION
			$owner_labels = array
			(	'project'	=>	'campaign'
			);
			$owner_owners = array();
			if	(	!empty
					(	$owner_labels[$this->name]
					)
				):
				$owner_owners = $GLOBALS['dbi']->get_result_array
				(	array
					(	'table'				=>	$owner_labels[$this->name]
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
			$order_list = '<ul id="'.$this->name.'_dragorder" class="boxy">';
			foreach
				(	$order_items	as	$item_id	=>	$order_item
				):
				if	(	empty
						(	$owner_owners
						)
					):
					$item_title = $this->render_record_title
					(	$order_item
					);
				else:
					$item_title = $owner_owners[array_pop
					(	$GLOBALS['dbi']->get_owner_records
						(	array
							(	'owned'				=>	array
								(	$GLOBALS['dbi']->tables[$this->name]->owners[$owner_labels[$this->name]]	=>	array
									(	$item_id
									)
								)
							)
						)
					)]
					.	' &gt; '
					.	$this->render_record_title
						(	$order_item
						)
					;
				endif;				
				
				$order_list .= '<li itemID="'
				.	$item_id
				.	'">'
				.	$item_title
				.	'</li>'
				;
			endforeach;
			$order_list .= '</ul>';

			$left_cheek = xhtml::element
			(	array
				(	'tag_name'	=>	'TD'
				,	'attributes'	=>	array
					(	'CLASS'		=>	'rec_fld_lbl'
					,	'STYLE'		=>	'vertical-align:top'
					)
				,	'content'		=>	$order_list
				)
			);
			
			$right_cheek = xhtml::element
			(	array
				(	'tag_name'	=>	'TD'
				,	'attributes'	=>	array
					(	'CLASS'		=>	'rec_fld_val'
					,	'STYLE'		=>	'width:200px;vertical-align:top;text-align:left;'
					)
				,	'content'		=>	'<div>Drag and drop the list items into the desired order, then...<br>&nbsp;</div>'
					.	$submit_button
					.	'<br/><br/><br/>'
					.	$cancel_button
				)
			);
			
			$titler = '<table cellspacing="0" class="pre_record">
                <tr class="rec_fld_display">
                    <td class="record_header"><span class="record_title">Reorder '
			.	$this->title_plural
			;
			if	(	!empty
				 	(	$ownership_id
					)
				):
				$titler .= ' belonging to '
				.	$GLOBALS['dbi']->ownerships[$ownership_id]['owner_title']
				.	' <span class="record_subtitle">'
				.	$GLOBALS['dbi']->tables[$GLOBALS['dbi']->ownerships[$ownership_id]['owner_table']]->render_record_title
					(	$GLOBALS['dbi']->get_result_array
						(	array
						 	(	'table'		=>	$GLOBALS['dbi']->ownerships[$ownership_id]['owner_table']
							,	'equals'	=>	array
								(	'id'		=>	$owner_id
								)
							,	'limit_lo'	=>	1
							,	'pop_single_row'	=>	1
							)
						)
					)
				.	'</span>'
				;
			endif;			
			$titler .= '</span></td></tr></table>';

			$tbody = xhtml::element
				(	array
					(	'tag_name'	=>	'TR'
					,	'attributes'	=>	array
						(	'CLASS'		=>	'rec_fld_display'
						)
					,	'content'		=>	$left_cheek
						.	$right_cheek
					)
				)
			;
	
			$table = xhtml::element
			(	array
				(	'tag_name'	=>	'TABLE'
				,	'attributes'	=>	array
					(	'CLASS'			=>	'record'
					,	'CELLSPACING'	=>	0
					)
				,	'content'		=>	$tbody
				)
			);

			$form = xhtml::element
			(	array
				(	'tag_name'	=>	'FORM'
				,	'attributes'	=>	array
					(	'ACTION'		=>	uri::generate()
					,	'METHOD'		=>	'post'
					,	'ID'			=>	'order_form'
					,	'NAME'			=>	'order_form'
					)
				,	'content'		=>	$hidden_inputs
					.	$table
				)
			);
			
			$div = xhtml::element
			(	array
				(	'tag_name'		=>	'DIV'
				,	'attributes'	=>	array
					(	'CLASS'			=>	'record_div'
					)
				,	'content'		=>	$titler
					.	$form
				)
			);
		else:
			//	$ht .= 'Only one '.$table_ray[$table]['capitalized'].' has been created.  There must be at least two '.$table_ray[$table]['pluralized'].' in the database before you can order them.';
		endif;
		
		return $div;
	}
	
	function select_records
	(	$args	=	array()
	)
	{	extract
		(	debug::function_argument_verify
			(	array
				(	'function'			=>	__CLASS__.'->'.__FUNCTION__
				,	'arguments_input'		=>	$args
				,	'arguments_descriptions'	=>	array
					(	'fields'					=>	array
						(	'default_value'				=>	array
							(	'*'
							)
						)
					,	'key_by'				=>	array
						(	'blurb'				=>	'Assign array keys equal to the values in a specific column. Use any column in table with unique values.'
						,	'possible_values'		=>	array
							(	array
								(	'possible_value'	=>	'id'
								,	'blurb'			=>	'Use table primary key values as array key values.'
								)
							,	array
								(	'possible_value'	=>	0
								,	'blurb'			=>	'Produces a numerially keyed array in ORDER BY order.'
								)
							,	'Any other table column containing unique values.'
							)
						,	'relax_possible_values'	=>	1
						,	'default_value'		=>	'id'
						)
					,	'equals'				=>	array
						(	'blurb'				=>	"WHERE X = Y // array('x1'=>'y1','x2'=>'y2',etc.)"
						,	'default_value'		=>	0
						)
					,	'in'					=>	array
						(	'default_value'			=>	0
						)
					,	'order_by'		=>	array
						(	'blurb'			=>	"ORDER BY column direction // array('column1'=>'direction1',etc.)"
						,	'default_value'	=>	0
						)
					,	'limit_lo'			=>	array
						(	'default_value'		=>	0
						)
					,	'limit_hi'			=>	array
						(	'default_value'		=>	0
						)
					,	'owners'			=>	array
						(	'default_value'		=>	array()
						)
					,	'expose_query'			=>	array
						(	'blurb'				=>	'TRUE: EXPOSES SQL QUERY'
						,	'default_value'		=>	0
						)
					,	'count_only'			=>	array
						(	'possible_values'		=>	array
							(	0
							,	1
							)
						,	'default_value'			=>	0
						)
					,	'greedy'			=>	array
						(	'blurb'				=>	'If false, returns only records owned by all owners.  If true, returns all records owned by any owner.  This argument has no effect if no owners are present.'
						,	'possible_values'	=>	array
							(	0
							,	1
							)
						,	'default_value'		=>	1
						)
					)
				)
			)
		);
		
		$this->initialize();
		
		if	(	!empty
			 	(	$owners
				)
			):
			if	(	$owners	==	$GLOBALS['user']->owners
				):
				unset
				(	$owners[2]
				);
			endif;
			if	(	count
					(	$owners
					)
				):
				$order_by = 0;
			endif;
		endif;

		if	(	empty
				(	$order_by
				)
/*			&&	empty
				(	$owners
				)
*/			&&	!$this->hidden
			):
			// ORDER BY // NEW MULTI COLUMN SORT CAPABLE
			$order_by_priority = 
			$order_by = 
			array()
			;
			foreach
				(	$this->fields as $field_name => $field
				):
				if	(	!empty
						(	$field->sort_priority
						)
					):
					$order_by_priority[$field->sort_priority] = $field_name;
				endif;
			endforeach;
			reset
			(	$this->fields
			);
			if	(	!empty
					(	$order_by_priority
					)
				):
				foreach
					(	$order_by_priority as $sort_priority => $field_name	
					):
					$order_by[$field_name] = $this->fields[$field_name]->sort_direction;
				endforeach;
			endif;
		endif;

		return $GLOBALS['dbi']->get_result_array
		(	array
			(	'table'			=>	$this->name
			,	'fields'		=>	$fields
			,	'key_by'		=>	$key_by
			,	'equals'		=>	$equals
			,	'in'			=>	$in
			,	'order_by'		=>	$order_by
			,	'limit_lo'		=>	$limit_lo
			,	'limit_hi'		=>	$limit_hi
			,	'where'			=>	$this->query_filter()
			,	'owners'		=>	$owners
			,	'count_only'	=>	$count_only
			,	'greedy'		=>	$greedy
			,	'expose_query'	=>	$expose_query
			)
		);
	}
	
}

