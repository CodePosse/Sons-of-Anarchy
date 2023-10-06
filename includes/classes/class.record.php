<?php

class record {

	function __construct
	(	$args	=	array()
	)
	{	extract
		(	debug::function_argument_verify
			(	array
				(	'function'			=>	__CLASS__.'->'.__FUNCTION__
				,	'arguments_input'		=>	$args
				,	'arguments_descriptions'	=>	array
					(	'table'	=>	array
						(	'blurb'		=>	'The name of the table to which this record object belongs.'
						)
					)
				)
			)
		);
		
		$this->table = &$GLOBALS['dbi']->tables[$table];
		
		$this->renderings = 
		$this->initialized = 
		0
		;
		
		$this->dud = array
		(	'invalid'	=>	array()
		,	'missing'	=>	array()
		);
		
		$this->did = '';				
	}
	
	function delete
	(	$args	=	array()
	)
	{	extract
		(	debug::function_argument_verify
			(	array
				(	'function'			=>	__CLASS__.'->'.__FUNCTION__
				,	'arguments_input'		=>	$args
				,	'arguments_descriptions'	=>	array
					(	'deplete'	=>	array
						(	'blurb'		=>	'Whether or not to delete associated sub-records'
						,	'possible_values'	=>	array
							(	0
							,	1
							)
						,	'default_value'		=>	0						)
					)
				)
			)
		);
		
		$deleted_row = 0;
		if	(	!empty
				(	$this->id
				)
			&&	!(	$this->table->name	==	'user'
				&&	$this->id			==	$GLOBALS['user']->id
				)
			):
			
			if	(	$deplete
				):
				$this->get_owned();
				// FIND IF THIS RECORD OWNS ANYTHING, IF IT DOES, ALSO DELETE ALL OWNED RECORDS
				if	(	count
						(	$this->owns
						)
					):
					foreach
						(	$this->owns	as	$ownership_id	=>	$owned_records
						):
						if	(	count
								(	$owned_records
								)
							&&	$GLOBALS['dbi']->ownerships[$ownership_id]['owned_table'] != 'user'
							):
							$owned_record_ids = array_keys
							(	$owned_records
							);
							$deleted_owned_records = $GLOBALS['dbi']->affect_rows
							(	array
								(	'table'	=>	$GLOBALS['dbi']->ownerships[$ownership_id]['owned_table']
								,	'act'	=>	'delete'
								,	'rows'	=>	$owned_record_ids
								)
							);
							$this->owns[$GLOBALS['dbi']->ownerships[$ownership_id]['owned_table']] = array();
						endif;
					endforeach;
					reset
					(	$this->owns
					);
					$deleted_owned = $GLOBALS['dbi']->get_result
					(	"	DELETE
							FROM	owned
							WHERE	ownership_id	=	".$ownership_id."
							AND		owner_id		=	".$this->id."
							"
					);
				endif;
			endif;
			
			// DELETE OWNER RELATIONSHIPS
			if	(	count
					(	$this->owners
					)
				):
				$previous_owners = array();
				foreach
					(	$this->owners	as	$ownership_id	=>	$owner_records
					):
					$deleted_owned = $GLOBALS['dbi']->get_result
					(	"	DELETE
							FROM	owned
							WHERE	ownership_id	=	".$ownership_id."
							AND		owned_id		=	".$this->id."
						"
					);
					$this->owners[$ownership_id] = array();
				endforeach;
				reset
				(	$this->owners
				);
			endif;
			
			$deleted_row = $GLOBALS['dbi']->affect_rows
			(	array
				(	'table'	=>	$this->table->name
				,	'act'	=>	'delete'
				,	'rows'	=>	array
					(	$this->id
					)
				)
			);
		endif;
		return $deleted_row;
	}
	
	function get_owned()
	{	if	(	empty
				(	$this->owners
				)
			):
			$this->owners = array();
			foreach
				(	$this->table->owners	as	$owner_name	=>	$ownership_id	
				):
				$this->owners[$ownership_id] = $GLOBALS['dbi']->get_result_array
				(	array
					(	'table'		=>	'owned'
					,	'key_by'	=>	'owner_id'
					,	'equals'	=>	array
						(	'ownership_id'	=>	$ownership_id
						,	'owned_id'		=>	$this->id
						)
					,	'order_by'	=>	array
						(	'owner_id'	=>	''
						)
					)
				);
				if	(	empty
					 	(	$this->owners[$ownership_id]
						)
					):
					unset
					(	$this->owners[$ownership_id]
					);
				else:
					foreach
						(	$this->owners[$ownership_id]	as	$owner_id	=>	$owner	
						):
						$this->owners[$ownership_id][$owner_id] = $GLOBALS['dbi']->get_result_array
						(	array
							(	'table'		=>	$GLOBALS['dbi']->ownerships[$ownership_id]['owner_table']
							,	'equals'	=>	array
								(	'id'		=>	$owner_id
								)
							,	'pop_single_row'	=>	1
							)
						);
						$this->owners[$ownership_id][$owner_id]['owned_row_id'] = $owner['id'];
					endforeach;
					reset
					(	$this->owners[$ownership_id]
					);
				endif;
			endforeach;
			reset
			(	$this->table->owners
			);
		endif;
		
		if	(	empty
				(	$this->owns
				)
			):
			$this->owns = array();
			foreach
				(	$this->table->owns	as	$owned_name	=>	$ownership_id
				):
				$this->owns[$ownership_id] = $GLOBALS['dbi']->get_result_array
				(	array
					(	'table'		=>	'owned'
					,	'key_by'	=>	'owned_id'
					,	'equals'	=>	array
						(	'ownership_id'	=>	$ownership_id
						,	'owner_id'		=>	$this->id
						)
					,	'order_by'	=>	array
						(	'owned_id'		=>	''
						)
					)
				);
				foreach
					(	$this->owns[$ownership_id]	as	$owned_id	=>	$owned	
					):
					$this->owns[$ownership_id][$owned_id] = $GLOBALS['dbi']->get_result_array
					(	array
						(	'table'		=>	$GLOBALS['dbi']->ownerships[$ownership_id]['owned_table']
						,	'equals'	=>	array
							(	'id'		=>	$owned_id
							)
						,	'pop_single_row'	=>	1
						)
					);
					$this->owns[$ownership_id][$owned_id]['owned_row_id'] = $owned['id'];
				endforeach;
				reset
				(	$this->owns[$ownership_id]
				);
			endforeach;
			reset
			(	$this->table->owns
			);
		endif;
	}
	
	function get_values()
	{	$this->values = $GLOBALS['dbi']->get_result_array
		(	array
			(	'table'			=>	$this->table->name
			,	'equals'			=>	array
				(	'id'				=>	$this->id
				)
			,	'pop_single_row'	=>	1
			,	'expose_query'		=>	$this->expose_query
			)
		);
		$this->get_owned();
	}
	
	function include_action()
	{	$includable_action = $this->table->name
		.	'.record_'
		.	$this->act
		.	'.php'
		;
		if	(	is_file
				(	$GLOBALS['page']->path['file_root']
				.	$includable_action
				)
			):
			if	(	empty
					(	$this->values
					)
				):
				$this->get_values();
			endif;
			
			include
			(	$includable_action
			);
			
		endif;
	}
	
	function initialize
	(	$args	=	array()
	)
	{	if	(	!$this->initialized
			):
			if	(	!$this->table->initialized
				):
				$this->table->initialize();
			endif;
			extract
			(	debug::function_argument_verify
				(	array
					(	'function'			=>	__CLASS__.'->'.__FUNCTION__
					,	'arguments_input'		=>	$args
					,	'arguments_descriptions'	=>	array
						(	'id'		=>	array
							(	'default_value'	=>	0
							)
						,	'expose_query'			=>	array
							(	'blurb'				=>	'TRUE: EXPOSES SQL QUERY'
							,	'default_value'		=>	0
							)
						)
					)
				)
			);
			
			$this->id = $id;
			$this->ignore_owners = array();
			
			if	(	empty
					(	$this->id
					)
				):
				$this->act = 
				(	$this->table->request['act'] == 'insert'
				)
				?	$this->table->request['act']
				:	'create'
				;
			else:
				if	(	empty
						(	$this->act
						)
					):
					$this->act = $this->table->request['act'];
				endif;
			endif;
/*				
			if	(	empty
					(	$GLOBALS['user']->id
					)
				):
				$this->act = 'view';
			endif;
*/			
			$this->expose_query = $expose_query;
			
			switch
				(	$this->act
				):
				case 'insert':
					$good_fields = $this->validate_field_input();
					$good_owners = $this->validate_owner_input();
					if	(	$good_fields
						&&	$good_owners
						):
						$this->id = $this->insert();
						$this->include_action();
					endif;
					
					$this->act = 
					(	empty
						(	$this->id
						)
					)
					?	'create'
					:	'view'
					;
					$this->include_action();
					break;
				case 'update':
					$good_fields = $this->validate_field_input();
					$good_owners = $this->validate_owner_input();
					if	(	$good_fields
						&&	$good_owners
						):
						$updated = $this->update();
						$this->include_action();
					endif;
					
					$this->act = 
					(	empty
						(	$updated
						)
					)
					?	'edit'
					:	'view'
					;
					break;
					$this->include_action();
				case 'delete':
				case 'deplete':
					if	(	empty
							(	$this->values
							)
						):
						// GET VALUES BEFORE DELETING RECORD, IN CASE THEY ARE NEEDED
						$this->get_values();
					endif;
					$deplete =
					(	$this->act == 'deplete'
					)
					?	1
					:	0
					;
					if	(	$this->delete
							(	$deplete
							)
						):
						$this->did = $this->table->name;
						if	(	$deplete
							):
							$this->did .= ' and sub-records';
						endif;
						$this->did .= ' successfully deleted.';
				
						$this->id = 0;
					else:
						$this->act = 'edit';
					endif;
					$this->include_action();
					break;
				case 'create':
				case 'edit':
				case 'view':
					$this->include_action();
					break;
				case 'hide':
					break;
			endswitch;
			
			if	(	empty
				 	(	$this->id
					)
				):
				if	(	$this->act == 'delete'
					):
					$GLOBALS['page']->redirect
					(	uri::generate
						(	array
							(	'query'	=>	array
								(	'z'		=>	$this->table->name
								)
							,	'query_crypt_key'	=>	$GLOBALS['page']->url_crypt_key
							)
						)
					);
				endif;
			else:
				$this->get_values();
			endif;

			$this->initialized = 1;
	
			return $this->initialized;
		else:
			return 0;
		endif;
	}
	
	function insert()
	{	$this->request_values();
	
		$id = $GLOBALS['dbi']->insert_row
		(	array
			(	'table'	=>	$this->table->name
			,	'row'	=>	$this->new_row
			)
		);

		if	(	$id
			):
			foreach
				(	$this->table->owners	as	$owner_name	=>	$ownership_id
				):
				$ownership_info = $GLOBALS['dbi']->ownerships[$ownership_id];
				$owner_ids = 
				(	empty
					(	$this->table->request[$owner_name]
					)
				)
				?	array()
				:	$this->table->request[$owner_name]
				;
				if	(	!empty
						(	$owner_ids
						)
					):
					if	(	!is_array
							(	$owner_ids
							)
						):
						$owner_ids = 
						(	strstr
							(	$owner_ids
							,	','
							)
						)
						?	explode
							(	','
							,	$owner_ids
							)
						:	array
							(	$owner_ids
							)
						;
					endif;
					$insert_owners = array();
					foreach
						(	$owner_ids as $owner_id	
						):
						if	(	$ownership_id
							&&	$owner_id
							&&	$id
							):
							$max_sort_order = $GLOBALS['dbi']->get_result_array
							(	array
								(	'table'				=>	'owned'
								,	'fields'			=>	array
									(	'sort_order'
									)
								,	'equals'			=>	array
									(	'ownership_id'		=>	$ownership_id
									,	'owner_id'			=>	$owner_id
									)
								,	'order_by'			=>	array
									(	'sort_order'		=>	'DESC'
									)
								,	'limit_lo'			=>	1
								,	'pop_single_row'	=>	1
								)
							);
							if	(	!is_numeric
									(	$max_sort_order
									)
								):
								$max_sort_order = 0;
							endif;
							$insert_owners[] = array
							(	'ownership_id'	=>	$ownership_id
							,	'owner_id'		=>	$owner_id
							,	'owned_id'		=>	$id
							,	'sort_order'	=>	$max_sort_order
								+	1	
							);
						endif;
					endforeach;
					
					$inserted_owners = $GLOBALS['dbi']->insert_rows
					(	array
						(	'table'	=>	'owned'
						,	'rows'	=>	$insert_owners
						)
					);
				endif;
			endforeach;
			unset
			(	$this->owners
			);
			$this->get_owned();
		endif;
		return $id;
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
					(	'name'	=>	array
						(	'default_value'	=>	$this->table->name
						)
					,	'id'		=>	array
						(	'default_value'	=>	0
						)
					,	'act'		=>	array
						(	'possible_values'	=>	array
							(	'view'
							,	'edit'
							,	'create'
							,	'insert'
							,	'update'
							,	'delete'
							,	'deplete'
							)
						,	'default_value'	=>	'view'
						)
					,	'show_owned'	=>	array
						(	'default_value'	=>	array()
						)
					)
				)
			)
		);
		
		$this->act = $act;
		
		if	(	!isset
				(	$this->id
				)
			):
			$this->initialize
			(	$id
			);
		endif;
		
		if	(	!$this->id
			):
			$this->act = 'create';
		endif;
		
		// RENDER TABLE DATA into PAGE OBJECT
		$this->template = new record_template
		(	array
			(	'name'		=>	$name
			,	'table'		=>	$this->table->name
			,	'record'	=>	&$this
			)
		);

		return $this->template->render
		(	array
			(	'show_owned'	=>	$show_owned
			)
		);
	}

	function request_values()
	{	$row = array();
		foreach
			(	$this->table->fields	as	$field_name	=>	&$field
			):
			if	(	isset
					(	$field->aggregate_value
					)
				):
				if	(	$field->is_datetime
						(	'either'
						)
					):
					$this->table->request[$field_name] = date_time::assemble
					(	$field->aggregate_value
					);
				endif;
			endif;
			if	(	isset
					(	$this->table->request[$field_name]
					)
				):
				if	(	$field->encrypt
					&&	empty
						(	$this->new_row[$field_name]
						)
					):
					$this->table->request[$field_name] = encryption::my_crypt
					(	array
						(	'data'		=>	$this->table->request[$field_name]
						,	'key'		=>	$field->encrypt
						)
					);
				endif;
				$row[$field_name] = $this->table->request[$field_name];
			endif;
		endforeach;
		reset
		(	$this->table->fields
		);
		$this->new_row = $row;
	}
	
	function update()
	{	foreach
			(	$this->table->owners	as	$owner_name	=>	$ownership_id
			):
			if	(	!in_array
					(	$owner_name
					,	$this->ignore_owners
					)
				):
				$ownership_info = $GLOBALS['dbi']->ownerships[$ownership_id];
				// FIRST CHECK FOR APPROPRIATE OWNER ID SUBMITTED THROUGH FORM
				if	(	isset
						(	$this->table->request[$owner_name]
						)
					):
					$new_owner_ids = 
					(	is_array
						(	$this->table->request[$owner_name]
						)
					)
					?	$this->table->request[$owner_name]
					:	array
						(	$this->table->request[$owner_name]
						)
					;
					$this->get_owned();
					$old_owner_ids = 
					$delete_owners = 
					$insert_owners = 
					array()
					;
					
					// DELETE DE-SELECTED OWNERS
					if	(	!empty
							(	$this->owners[$ownership_id]
							)
						):
						foreach
							(	$this->owners[$ownership_id] as	$old_owner_id	=>	$owner_info	
							):
							$old_owner_ids[] = $old_owner_id;
							if	(	!in_array
									(	$old_owner_id
									,	$new_owner_ids
									)
								):
								$delete_owners[] = $owner_info['owned_row_id'];
							endif;
						endforeach;
						reset
						(	$this->owners[$ownership_id]
						);
					endif;
					
					if	(	!empty
							(	$delete_owners
							)
						):
						$deleted_owners = $GLOBALS['dbi']->affect_rows
						(	array
							(	'table'	=>	'owned'
							,	'act'	=>	'delete'
							,	'rows'	=>	$delete_owners
							)
						);
					endif;
					
					// IF AN OWNER IS REQUIRED AND NONE IS SUBMITTED,
					// CHECK FOR APPROPRIATE OWNER ID IN USER OBJECT
					if	(	!count
							(	$new_owner_ids
							)
						&&	$ownership_info['owners_required']
						&&	!empty
							(	$GLOBALS['user']->owners[$ownership_id]
							)
						):
						eval
						(	'$new_owner_ids = array_keys($GLOBALS[\'user\']->owners[\''
							.	$ownership_id
							.	'\']);'
						);
					endif;

					// INSERT NEWLY SELECTED OWNERS
					foreach
						(	$new_owner_ids	as	$new_owner_id	
						):
						if	(	!empty
								(	$new_owner_id
								)
							&&	!in_array
								(	$new_owner_id
								,	$old_owner_ids
								)
							):
							$max_sort_order = $GLOBALS['dbi']->get_result_array
							(	array
								(	'table'				=>	'owned'
								,	'fields'			=>	array
									(	'sort_order'
									)
								,	'equals'			=>	array
									(	'ownership_id'		=>	$ownership_id
									,	'owner_id'			=>	$new_owner_id
									)
								,	'order_by'			=>	array
									(	'sort_order'		=>	'DESC'
									)
								,	'limit_lo'			=>	1
								,	'pop_single_row'	=>	1
								)
							);
							if	(	!is_numeric
									(	$max_sort_order
									)
								):
								$max_sort_order = 0;
							endif;
							$insert_owners[] = array
							(	'ownership_id'	=>	$ownership_id
							,	'owner_id'		=>	$new_owner_id
							,	'owned_id'		=>	$this->id
							,	'sort_order'	=>	$max_sort_order
								+	1	
							);
						endif;
					endforeach;
					if	(	!empty
							(	$insert_owners
							)
						):
						$inserted_owners = $GLOBALS['dbi']->insert_rows
						(	array
							(	'table'	=>	'owned'
							,	'rows'	=>	$insert_owners
							)
						);
					endif;
				endif;
			endif;
		endforeach;
		unset
		(	$this->owners
		);
		$this->get_owned();
		
		$this->request_values();

		if	(	!empty
				(	$this->new_row['status']
				)
			&&	!empty
				(	$this->table->fields['active_from']
				)
			&&	!empty
				(	$this->table->fields['active_until']
				)
			):
			$this->get_values();
			if	(	$this->values['status']	!=	$this->new_row['status']
				):
				$this->new_row['active_from'] = '0000-00-00 00:00:00';
				$this->new_row['active_until'] = '0000-00-00 00:00:00';
			endif;
		endif;

		$affected_rows = $GLOBALS['dbi']->affect_rows
		(	array
			(	'table'	=>	$this->table->name
			,	'rows'	=>	array
				(	$this->id	=>	$this->new_row
				)
			)
		);

		if	(	!empty
				(	$inserted_owners
				)	
			):
			if	(	empty
					(	$affected_rows
					)
				):
				$affected_rows = 0;
			endif;
			$affected_rows += count
			(	$inserted_owners
			);
		endif;
		
		return $affected_rows;
	}
	
	function validate_field_input()
	{	$this->request_values();
		foreach
			(	$this->table->fields	as	$field_name	=>	&$field	
			):
			$validate_as_file_upload = false;
			if	(	!empty
					(	$this->table->file_fields[$field_name]
					)
				&&	!empty
					(	$this->table->request[$field_name]
					)
				&&	is_array
				 	(	$this->table->request[$field_name]
					)					
				):
				if	(	!empty
						(	$this->table->request[$field_name]['name']
						)
					):
					$validate_as_file_upload = true;
					foreach
						(	$this->table->request[$field_name]	as	$fupk	=>	$fupv
						):
						if	(	is_array
								(	$fupv
								)
							):
							$this->table->request[$field_name][$fupk] = array_shift
							(	$fupv
							);
						endif;
					endforeach;
					reset
					(	$this->table->request[$field_name]
					);
				else:
					$this->table->request[$field_name] = 
					$this->new_row[$field_name] = 
					$field->value = 
					$this->table->request[$field_name][0]
					;					
				endif;
			endif;
			if	(	$validate_as_file_upload
				):
				if	(	(	empty
						 	(	$this->table->request[$field_name]
							)
						&&	!$field->null_allowed
						)
					||	(	!empty
							(	$this->table->request[$field_name]
							)
						&&	is_array
						 	(	$this->table->request[$field_name]
							)
						&&	!empty
							(	$this->table->request[$field_name]['name']
							)
						)
					):
					$this->validate_file_input
					(	$field_name
					);
				else:
					unset
					(	$this->table->request[$field_name]
					);
				endif;
			else:
				if	(	isset
						(	$this->new_row[$field_name]
						)	
					):	
					if	(	!$field->null_allowed
						&&	!strlen
							(	$this->new_row[$field_name]
							)
						):
						$this->dud['missing'][] = $field_name;
					else:
						$this->dud['invalid'][$field_name] = array();
						if	(	!is_array
								(	$this->new_row[$field_name]
								)
							&&	strlen
								(	$this->new_row[$field_name]
								)
							):				
							if	(	$field->is_datetime
									(	'date'
									)
								&&	preg_match
									(	'/[^0:\- ]/'
									,	$this->new_row[$field_name]	
									)
								):
								// AUTOMATIC DATE FIELD VALIDATION
								if	(	date_time::valid_date
										(	array
											(	'year'		=>	substr
												(	$this->new_row[$field_name]	
												,	0
												,	4
												)
											,	'month'		=>	substr
												(	$this->new_row[$field_name]	
												,	5
												,	2
												)
											,	'day'		=>	substr
												(	$this->new_row[$field_name]	
												,	8
												,	2
												)
											,	'return_corrected'	=>	0
											)
										)	==	'0000-00-00'
									):
																	
									$this->dud['invalid'][$field_name][] = 'Invalid date entered.';
									
								endif;
							endif;
							if	(	count
									(	$field->validators
									)
								):							
								foreach
									(	$field->validators	as	$validator
									):
									// 
									// MIGHTY MIGHTY FIELD VALIDATION !!!
									// 
									// IF YOU WANT THE INPUT VALUE TO BE TESTED AGAINST MULTIPLE CRITERIA
									// YOU CAN DO THIS IN TWO WAYS
										// OPTION 1:
											// INCLUDE MULTIPLE CRITERIA IN A SINGLE VALIDATOR RECORD
										// OPTION 2:
											// ASSOCIATE MULTIPLE VALIDATORS WITH THE DB FIELD
									// IN BOTH CASES, THE INPUT VALUE WILL BE TESTED AGAINST ALL CRITERIA
									// TO MINIMIZE THE NUMBER OF TIMES THAT THE USER HAS TO RE-SUBMIT THE INFO 
									// 
									// STEP 1
									// 
									// APPLY ANY REGEX TRANSFORMATIONS TO INPUT VALUE
									// MOST COMMONLY WILL BE USED TO STRIP SPACES AND INVALID CHARACTERS
									if	(	!empty
											(	$validator['preg_strip']
											)
										&&	strlen
											(	$validator['preg_strip']
											)
										):
										$this->new_row[$field_name] = 
										$this->table->request[$field_name] = 
										$field->value = 
										preg_replace
										(	$validator['preg_strip']			//	REGEX PATTERN TO MATCH AND REPLACE
										,	''									//	REPLACEMENT STRING
										,	$this->table->request[$field_name]	//	STRING TO SEARCH AND REPLACE
										);
									endif;
									
									$error_message = 
									(	empty
										(	$validator['error_message']
										)
									)
									?	'Invalid entry.'
									:	$validator['error_message']
									;
									
									// 
									// STEP 2
									// 
									// IF REGEX TEST IS PRESENT, TEST AGAINST INPUT VALUE
									if	(	!empty
											(	$validator['preg_test']
											)
										&&	!preg_match
											(	$validator['preg_test']
											,	$this->new_row[$field_name]
											)
										):
										$this->dud['invalid'][$field_name][] = str_replace
										(	'$field'
										,	$field_name
										,	$error_message
										);
									endif;
									
									// 
									// STEP 3
									// 
									// TEST MINIMUM LENGTH OF INPUT VALUE
									// MAXIMUM LENGTHS SHOULD BE SET IN DB STRUCTURE / FIELD TYPE
										// OR
									// IN FIELD-SPECIFIC VALIDATOR CLASS FUNCTION
										// OR, FOR ENCRYPTED FIELDS
										// WHICH MUST BE VARCHARS AT LEAST 64 CHARACTERS IN LENGTH,
									// PUT MAXIMUM LENGTH IN preg_test FIELD, i.e. [.]{0,12}
									if	(	!empty
											(	$validator['minimum_length']
											)
										&&	strlen
											(	$this->new_row[$field_name]
											)
											<	$validator['minimum_length']
										):
										$this->dud['invalid'][$field_name][] = 'Must be at least '.$validator['minimum_length'].' characters long.';
									endif;
									
									// 
									// STEP 4
									// 
									// TEST WITH VALIDATOR CLASS FUNCTIONS
									if	(	!empty
											(	$validator['validate_with']
											)
										):
										$validated = validator::validate
										(	array
											(	'function'	=>	$validator['validate_with']
											,	'input'		=>	$this->new_row[$field_name]
											)
										);
										if	(	!$validated
											):
											$this->dud['invalid'][$field_name] = array_merge
											(	$this->dud['invalid'][$field_name]
											,	validator::return_errors()
											);
										endif;
									endif;
									
									
									// 
									// STEP 5
									// 
									// TEST UNIQUENESS OF FIELD VALUE AGAINST EXISTING DB VALUES
									
									
			//						debug::expose($validator);
									
								endforeach;
							endif;
						endif;
						if	(	empty
								(	$this->dud['invalid'][$field_name]
								)
							):
							unset
							(	$this->dud['invalid'][$field_name]
							);
						endif;						
						if	(	!is_numeric
								(	$this->new_row[$field_name]	
								)
							):
							$this->new_row[$field_name]	= 
							$this->table->request[$field_name] = 
							str_replace
							(	"'"
							,	"\'"
							,	$this->table->request[$field_name]
							);
						endif;
						if	(	$this->table->name == 'champion'
							&&	strstr
								(	$field_name
								,	'_large'
								)	==	'_large'
							&&	!empty
								(	$this->new_row[$field_name]
								)
							):
							$smaller_field_name = str_replace
							(	'_large'
							,	'_small'
							,	$field_name
							);
							$this->new_row[$smaller_field_name] = 
							$this->table->request[$smaller_field_name] = 
							str_replace
							(	'_large'
							,	'_small'
							,	$this->new_row[$field_name]
							);
						endif;
					endif;
				endif;
			endif;
		endforeach;
		reset
		(	$this->table->fields
		);
		return
		(	count
			(	$this->dud['missing']
			)
		||	count
			(	$this->dud['invalid']
			)
		)
		?	0
		:	1
		;
	}
	
	function validate_file_input
	(	$args	=	array()
	)
	{	extract
		(	debug::function_argument_verify
			(	array
				(	'function'			=>	__CLASS__.'->'.__FUNCTION__
				,	'arguments_input'		=>	$args
				,	'arguments_descriptions'	=>	array
					(	'field_name'
					,	'replace_spaces'	=>	array
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
						(	'default_value'				=>	0	// $GLOBALS['page']->time['stamp']
						)
					)
				)
			)
		);
		
		if	(	empty
				(	$this->table->request[$field_name]
				)
			||	empty
				(	$this->table->request[$field_name]['name']
				)
			):
			// BROWSE FIELD LEFT EMPTY OR NO FILE UPLOADED
			$this->dud['missing'][] = $field_name;
		else:
			$valid_extensions = array();
			if	(	!empty
					(	$this->table->file_fields[$field_name]['file_types']
					)
				):
				foreach
					(	$this->table->file_fields[$field_name]['file_types']	as	$file_type
					):
					foreach
						(	explode
							(	','
							,	$file_type['extensions']
							)	as	$valid_extension
						):
						$valid_extensions[$valid_extension] = $file_type['type'];
					endforeach;
				endforeach;
				unset
				(	$file_type
				);
				reset
				(	$this->table->file_fields[$field_name]['file_types']
				);
			endif;
			
			$attempted_file_upload = files::upload
			(	array
				(	'file_request'				=>	$this->table->request[$field_name]
/*
				,	'destination_path'			=>	
					(	(	$GLOBALS['cfg']['img_cdn_server'] != $_SERVER['SERVER_NAME']
						)
						?	'http://'
							.	$GLOBALS['cfg']['img_cdn_server']
							.	'/'
						:	$GLOBALS['page']->path['file_root']
							.	$this->table->file_fields[$field_name]['path']
					)
*/				
				,	's3_bucket'					=>	$this->table->file_fields[$field_name]['s3_bucket']
				,	'destination_path'			=>	$GLOBALS['page']->path['file_root']
					.	$this->table->file_fields[$field_name]['path']
				,	'max_file_size'				=>	$this->table->file_fields[$field_name]['bytes_max'] // $GLOBALS['page']->request['MAX_FILE_SIZE']
				,	'replace_spaces'			=>	$replace_spaces
				,	'file_name_to_lowercase'	=>	$file_name_to_lowercase
				,	'unique_time_stamp'			=>	$unique_time_stamp
				,	'bytes_min'					=>	$this->table->file_fields[$field_name]['bytes_min']
				,	'valid_extensions'			=>	$valid_extensions
				,	'image_constraints'			=>	array
					(	'width_min'					=>	$this->table->file_fields[$field_name]['width_min']
					,	'width_max'					=>	$this->table->file_fields[$field_name]['width_max']
					,	'height_min'				=>	$this->table->file_fields[$field_name]['height_min']
					,	'height_max'				=>	$this->table->file_fields[$field_name]['height_max']
					)
				)
			);

			if	(	is_array
					(	$attempted_file_upload
					)
				):
				$this->dud['invalid'][$field_name] = $attempted_file_upload;
			else:
				$this->table->request[$field_name] = $attempted_file_upload;
			endif;
		endif;
	}
	
	function validate_owner_input() {
		foreach (	$this->table->owners	as	$owner_name	=>	$ownership_id	
			):
			$this->dud['invalid'][$owner_name] = array();
			$ownership_info = $GLOBALS['dbi']->ownerships[$ownership_id];
			if	(	$ownership_info['owners_required']
				):
				// FIRST CHECK FOR APPROPRIATE OWNER ID SUBMITTED THROUGH FORM
				$new_owner_ids = 
				(	empty
				 	(	$this->table->request[$owner_name]
					)	
				)
				?	array()
				:	$this->table->request[$owner_name]
				;
				$owners_selected = count
				(	$new_owner_ids
				);
				
				if	(	empty
					 	(	$owners_selected
						)
					):
					if	(	$this->act == 'insert'
						):
						// IF AN OWNER IS REQUIRED AND NONE IS SUBMITTED
						// DURING THE CREATION OF THE RECORD
						// CHECK FOR APPROPRIATE OWNER ID IN USER OBJECT
						if	(	$ownership_info['owner_table'] == 'user'
							):
							$new_owner_ids = array
							(	$GLOBALS['user']->id
							);
						else:
							if	(	!empty
									(	$GLOBALS['dbi']->tables['user']->owners[$ownership_info['owner_name']]
									)
								):
								$new_owner_ids = array_keys
								(	$GLOBALS['user']->owners[$GLOBALS['dbi']->tables['user']->owners[$ownership_info['owner_name']]]
								);
							endif;
						endif;
					endif;
					if	(	empty
						 	(	$new_owner_ids
							)
						):
						unset
						(	$this->dud['invalid'][$owner_name]
						);
						$this->ignore_owners[] = $owner_name;
						continue;
					else:
						$this->table->request[$owner_name] = $new_owner_ids;
						$owners_selected = count
						(	$new_owner_ids
						);
					endif;
				endif;
				
				// ENSURE THE APPROPRIATE NUMBER OF OWNERS HAVE BEEN SELECTED
				if	(	!empty
					 	(	$ownership_info['owners_required']
						)
					||	!empty
						(	$ownership_info['owners_allowed']
						)
					):
					$owner_table_title = $ownership_info['owner_title'];
					if	(	$ownership_info['owners_required'] == $ownership_info['owners_allowed']
						&&	$owners_selected	!=	$ownership_info['owners_required']
						):
						if	(	$ownership_info['owners_required']	>	1
							):
							$owner_table_title = strings::pluralize
							(	$owner_table_title
							);
						endif;
						$this->dud['invalid'][$owner_name][] = 'You must select '
						.	$ownership_info['owners_required']
						.	' '
						.	$owner_table_title
						.	'.'
						;
					else:
						if	(	!empty
							 	(	$ownership_info['owners_required']
								)
							&&	!empty
								(	$ownership_info['owners_allowed']
								)
							&&	(	$owners_selected	<	$ownership_info['owners_required']
								||	$owners_selected	>	$ownership_info['owners_allowed']
								)
							):
							$owner_table_title = strings::pluralize
							(	$owner_table_title
							);
							$this->dud['invalid'][$owner_name][] = 'You must select between '
							.	$ownership_info['owners_required']
							.	' and '
							.	$ownership_info['owners_allowed']
							.	' '
							.	$owner_table_title
							.	'.'
							;
						else:
							if	(	!empty
								 	(	$ownership_info['owners_required']
									)
								&&	$owners_selected	<	$ownership_info['owners_required']
								):
								if	(	$ownership_info['owners_required']	>	1
									):
									$owner_table_title = strings::pluralize
									(	$owner_table_title
									);
								endif;
								$this->dud['invalid'][$owner_name][] = 'You must select at least '
								.	$ownership_info['owners_required']
								.	' '
								.	$owner_table_title
								.	'.'
								;
							endif;
							if	(	!empty
								 	(	$ownership_info['owners_allowed']
									)
								&&	$owners_selected	>	$ownership_info['owners_allowed']
								):
								if	(	$ownership_info['owners_allowed']	>	1
									):
									$owner_table_title = strings::pluralize
									(	$owner_table_title
									);
								endif;
								$this->dud['invalid'][$owner_name][] = 'You may not select more than '
								.	$ownership_info['owners_allowed']
								.	' '
								.	$owner_table_title
								.	'.'
								;
							endif;
						endif;
					endif;
				endif;
			endif;
			if	(	empty
					(	$this->dud['invalid'][$owner_name]
					)
				):
				unset
				(	$this->dud['invalid'][$owner_name]
				);
			endif;
		endforeach;
		reset
		(	$this->table->owners
		);
		return
		(	count
			(	$this->dud['invalid']
			)
		)
		?	0
		:	1
		;
	}
	
}

