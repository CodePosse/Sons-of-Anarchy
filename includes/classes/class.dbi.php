<?php

class dbi extends mysqli {

	function affect_rows
	(	$args	=	array()
	)
	{	extract
		(	debug::function_argument_verify
			(	array
				(	'function'			=>	__CLASS__.'->'.__FUNCTION__
				,	'arguments_input'		=>	$args
				,	'arguments_descriptions'	=>	array
					(	'table'
					,	'act'		=>	array
						(	'possible_values'	=>	array
							(	'update'
							,	'delete'
							,	'replace'
							)
						,	'default_value'		=>	'update'
						)
					,	'rows'		=>	array
						(	'blurb'		=>	"for UPDATE: array('id_1'=>array('column_1'=>'value1',etc.),etc.)
for DELETE: array('id_1','id_2',etc.)"
						)
					)
				)
			)
		);
		
		if	(	is_array
				(	$rows
				)
			):
			switch
				(	$act
				):
				case 'update':
					$affected_rows = array();
					foreach
						(	$rows	as	$id	=>	$row
						):
						if	(	is_array
								(	$row
								)
							&&	count
								(	$row
								)
							):
							$sql =	"	UPDATE	`$table`
										SET		";
							foreach
								(	$row	as	$col	=>	$val
								):
								$sql .= 
								(	$val	==	'NULL'
								||	$val	==	NULL
								)
								?	"	`$col` = NULL,"
								:	"	`$col` = '"
									.	(	(	is_numeric
												(	$val
												)
											)
											?	$val
											:	addslashes
												(	$val
												)
										)
									.	"',"
								;
							endforeach;
							$sql = substr
							(	$sql
							,	0
							,	-1
							);
							$sql .= "	WHERE	id = $id ";
							$result = $this->get_result
							(	array
								(	'sql'		=>	$sql
								,	'tables'	=>	array
									(	$table
									)
								)
							);
							if	(	$result
								):
								$affected_rows[] = $id;
							endif;
						endif;
					endforeach;
					break;
				case 'delete':
					$affected_rows = 0;
					if	(	count
							(	$rows
							)
						):
						$result = $this->get_result
						(	array
							(	'sql'		=>	"
									DELETE
									FROM	`$table`
									WHERE	id		IN	(".implode(',',$rows).")	
								"
							,	'tables'	=>	array
								(	$table
								)
							)
						);
						$affected_rows += $this->affected_rows;
					endif;					
					break;
//				case 'replace':
//					break;
			endswitch;
		endif;
		return $affected_rows;
	}
		
	function extract_result
	(	$args	=	array()
	)
	{	extract
		(	debug::function_argument_verify
			(	array
				(	'function'			=>	__CLASS__.'->'.__FUNCTION__
				,	'arguments_input'		=>	$args
				,	'arguments_descriptions'	=>	array
					(	'sql'
					,	'scope'			=>	array
						(	'default_value'		=>	'GLOBALS'
						)
					,	'scope_type'	=>	array
						(	'possible_values'	=>	array
							(	'array'
							,	'object'
							)
						,	'default_value'		=>	'array'
						)
					)
				)
			)
		);
		
		switch
			(	$scope
			):
			case 'GLOBALS':
			case '_SERVER':
			case '_GET':
			case '_POST':
			case '_COOKIE':
			case '_FILES':
			case '_ENV':
			case '_REQUEST':
			case '_SESSION':
				$scope_type = 'array';
				break;
			case 'this':
				$scope_type = 'object';
				break;
		endswitch;
		
		$result = $this->get_result
		(	$sql
		);
		while
			(	$row = $result->fetch_array
				(	MYSQLI_ASSOC
				)
			):
			foreach
				(	$row as $key => $val
				):
				switch
					(	$scope_type
					):
					case 'object':
						eval
						(	'$'
						.	$scope
						.	'->'
						.	$key
						.	' = \''
						.	$val
						.	'\';'
						);
						break;
					case 'array':
						eval
						(	'$'
						.	$scope
						.	'[\''
						.	$key
						.	'\'] = \''
						.	$val
						.	'\';'
						);
						break;
				endswitch;
			endforeach;
		endwhile;
		$result->close();
	}
	
	function fail
	(	$args	=	array()
	)
	{	extract
		(	debug::function_argument_verify
			(	array
				(	'function'			=>	__CLASS__.'->'.__FUNCTION__
				,	'arguments_input'		=>	$args
				,	'arguments_descriptions'	=>	array
					(	'cause_of_death'
					,	'error_backtrace'			=>	array
						(	'possible_values'		=>	array
							(	''
							,	1
							)
						,	'default_value'		=>	''
						)
					)
				)
			)
		);
		
		echo '<pre>'
		.	$_SERVER['SCRIPT_FILENAME']
		.	'<hr />'
		.	$this->host_info
		.	'<hr />'
		.	'mysqli OPERATION FAILED: '
		.	$cause_of_death
		.	'<hr />'
		.	'ERROR: '
		.	$this->errno
		.	'<br />'
		.	$this->error
		.	'<hr />'
		;
		if	(	$error_backtrace
			):
			echo '<h2>PERFORMING ERROR BACKTRACE...</h2><hr /><br />';
			$error_backtrace = debug_backtrace();
			unset
			(	$error_backtrace[0]
			);
			debug::expose
			(	$error_backtrace
			);
			echo '<hr />';
		endif;
		echo '</pre>';
		exit;
	}
	
	function get_owned_records
	(	$args	=	array()
	)
	{	extract
		(	debug::function_argument_verify
			(	array
				(	'function'			=>	__CLASS__.'->'.__FUNCTION__
				,	'arguments_input'		=>	$args
				,	'arguments_descriptions'	=>	array
					(	'owners'		=>	array
						(	'blurb'			=>	"array
(	'ownership_id_1'	=>	array
	(	owner_id_1
	,	owner_id_2
	,	...
	)
,	'ownership_id_2'	=>	array
	(	...
	)
,	...
)"
						)
					,	'owned_table'		=>	array
						(	'default_value'		=>	''
						)
					,	'full_records'		=>	array
						(	'blurb'				=>	'If false, returns an array of owned id\'s.  If true, returns the complete records corresponding to the owned id\'s.'
						,	'possible_values'	=>	array
							(	0
							,	1
							)
						,	'default_value'		=>	0
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
					,	'owned_in'					=>	array
						(	'blurb'				=>	"WHERE X IN (Y) // array('x1'=>array('y1','y1',etc.),etc.)"
						,	'default_value'		=>	0
						)
					,	'limit_lo'			=>	array
						(	'default_value'		=>	0
						)
					,	'limit_hi'			=>	array
						(	'default_value'		=>	0
						)
					,	'greedy'			=>	array
						(	'blurb'				=>	'If false, returns only records owned by all owners.  If true, returns all records owned by any owner.'
						,	'possible_values'	=>	array
							(	0
							,	1
							)
						,	'default_value'		=>	1
						)
					,	'expose_query'			=>	array
						(	'blurb'				=>	'TRUE: EXPOSES SQL QUERY'
						,	'default_value'		=>	0
						)
					)
				)
			)
		);
		
		$sql = "";
		$owned = array();
		
		if	(	empty
				(	$owned_table
				)
			):
			foreach
				(	$owners	as	$ownership_id_in	=>	$owner_ids	
				):
				unset
				(	$also_owned
				);
				if	(	!empty
						(	$owner_ids
						)
					):
					if	(	!empty
							(	$sql
							)
						):
						$sql .= " OR ";
					endif;
					if	(	key
							(	$owner_ids
							)
						):
						$owner_ids = array_keys
						(	$owner_ids
						);
					endif;
					$owner_ids_string = arrays::implode_safe
					(	array
						(	'glue'		=>	','
						,	'pieces'	=>	$owner_ids
						)
					);
					$sql .= "	(	ownership_id	=	$ownership_id_in
								AND	owner_id		";
					$sql .= 
					(	strpos
						(	$owner_ids_string
						,	','
						)
					)
					?	"	IN	("
						.	$owner_ids_string
						.	")	"
					:	"	=	".$owner_ids_string
					;
					$sql .= "	)	";
				endif;
				if	(	!empty
						(	$owned_in
						)
					&&	is_array
						(	$owned_in
						)
					):
					if	(	!empty
							(	$sql
							)
						):
						$sql .= " AND ";
					endif;	
					$sql .= " owned_id IN (	"
					.	implode
						(	','
						,	$owned_in
						)
					.	" ) "
					;
				endif;
				$owned_table = $this->ownerships[$ownership_id_in]['owned_table'];
				if	(	!$greedy
					&&	!empty
						(	$sql
						)
					):
					$sql = "	SELECT	id
								,		owned_id
								FROM	owned
								WHERE	(	"
					.	$sql
					.	"	)	
								ORDER	BY				ownership_id
										,				owner_id
										,				sort_order
										,				owned_id
								"
					;
					if	(	(	!empty
								(	$limit_lo
								)
							||	!empty
								(	$limit_hi
								)
							)
						):
						$sql .= " LIMIT $limit_lo ";
						if	(	!empty
								(	$limit_hi
								)
							):
							$sql .= ", $limit_hi ";
						endif;
					endif;
					$result = $this->get_result
					(	$sql
					);
					if	(	$expose_query
						):
						debug::expose
						(	end
							(	$this->sql
							)
						);
					endif;
					if	(	count
							(	$owned
							)
						):
						$also_owned = array();
					endif;
					while
						(	$row	=	$result->fetch_array
							(	MYSQLI_ASSOC
							)	
						):
						if	(	isset
								(	$also_owned
								)
							):
							$also_owned[$row['id']] = $row['owned_id'];
						else:
							$owned[$row['id']] = $row['owned_id'];
						endif;
					endwhile;
					if	(	!empty
							(	$also_owned
							)
						):
						$also_owned = array_unique
						(	$also_owned
						);
						if	(	count
								(	$also_owned
								)	>=	count
								(	$owned
								)
							):
							$array_1 = $also_owned;
							$array_2 = $owned;
						else:
							$array_1 = $owned;
							$array_2 = $also_owned;
						endif;
						foreach
							(	$array_1	as	$key	=>	$value
							):
							if	(	!in_array
									(	$value
									,	$array_2
									)
								):
								unset
								(	$array_1[$key]
								);
							endif;
						endforeach;
						reset
						(	$array_1
						);
						$owned = $array_1;
					else:
						$owned = array_unique
						(	$owned
						);
					endif;
					$sql = "";
				endif;
			endforeach;
		else:
			$this->tables[$owned_table]->initialize();		
			foreach
				(	$this->tables[$owned_table]->owners	as	$owner_name	=>	$ownership_id_out
				):
/*
debug::expose
(	$owner_name.'	=>	'.$ownership_id_out
);
*/
				$owner_ids = 
				(	empty
					(	$owners[$ownership_id_out]
					)
				)
				?	array()
				:	$owners[$ownership_id_out]
				;
/*				foreach
					(	$owners	as	$ownership_id_in	=>	$owner_ids	
					):
debug::expose
(	$ownership_id_in.'	=>	'.count($owner_ids)
);
*/
					unset
					(	$also_owned
					);
					if	(	/* $this->ownerships[$ownership_id_in]['owner_table'] == $this->ownerships[$ownership_id_out]['owner_table']
						&&	*/ !empty
							(	$owner_ids
							)
						):
						if	(	!empty
								(	$sql
								)
							):
							$sql .= " OR ";
							endif;
						if	(	key
								(	$owner_ids
								)
							):
							$owner_ids = array_keys
							(	$owner_ids
							);
						endif;
						$owner_ids_string = arrays::implode_safe
						(	array
							(	'glue'		=>	','
							,	'pieces'	=>	$owner_ids
							)
						);
						$sql .= "	(	ownership_id	=	$ownership_id_out
									AND	owner_id		";
						$sql .= 
						(	strpos
							(	$owner_ids_string
							,	','
							)
						)
						?	"	IN	("
							.	$owner_ids_string
							.	")	"
						:	"	=	".$owner_ids_string
						;
						$sql .= "	)	";
					endif;
					if	(	!empty
							(	$owned_in
							)
						&&	is_array
							(	$owned_in
							)
						):
						if	(	!empty
								(	$sql
								)
							):
							$sql .= " AND ";
						endif;	
						$sql .= " owned_id IN (	"
						.	implode
							(	','
							,	$owned_in
							)
						.	" ) "
						;
					endif;
					if	(	!$greedy
						&&	!empty
							(	$sql
							)
						):
						$sql = "	SELECT	id
									,		owned_id
									FROM	owned
									WHERE	(	"
						.	$sql
						.	"	)	
									ORDER	BY				ownership_id
											,				owner_id
											,				sort_order
											,				owned_id
									"
						;
/*						if	(	(	!empty
									(	$limit_lo
									)
								||	!empty
									(	$limit_hi
									)
								)
							):
							$sql .= " LIMIT $limit_lo ";
							if	(	!empty
									(	$limit_hi
									)
								):
								$sql .= ", $limit_hi ";
							endif;
						endif;
*/						$result = $this->get_result
						(	$sql
						);
						if	(	$expose_query
							):
							debug::expose
							(	end
								(	$this->sql
								)
							);
						endif;
						if	(	count
								(	$owned
								)
							):
							$also_owned = array();
						endif;
						while
							(	$row	=	$result->fetch_array
							 	(	MYSQLI_ASSOC
								)	
							):
							if	(	isset
									(	$also_owned
									)
								):
								$also_owned[$row['id']] = $row['owned_id'];
							else:
								$owned[$row['id']] = $row['owned_id'];
							endif;
						endwhile;
						if	(	!empty
								(	$also_owned
								)
							):
							$also_owned = array_unique
							(	$also_owned
							);
							if	(	count
									(	$also_owned
									)	>=	count
									(	$owned
									)
								):
								$array_1 = $also_owned;
								$array_2 = $owned;
							else:
								$array_1 = $owned;
								$array_2 = $also_owned;
							endif;
							foreach
								(	$array_1	as	$key	=>	$value
								):
								if	(	!in_array
										(	$value
										,	$array_2
										)
									):
									unset
									(	$array_1[$key]
									);
								endif;
							endforeach;
							reset
							(	$array_1
							);
							$owned = $array_1;
						else:
							$owned = array_unique
							(	$owned
							);
						endif;
						$sql = "";
					endif;
//				endforeach;
			endforeach;
		endif;
		reset
		(	$owners
		);
		if	(	!empty
				(	$sql
				)
			):
			$sql = "	SELECT	id
						,		owned_id
						FROM	owned
						WHERE	(	"
			.	$sql
			.	"	)	
						ORDER	BY				ownership_id
								,				owner_id
								,				sort_order
								,				owned_id
						"
			;
			if	(	(	!empty
						(	$limit_lo
						)
					||	!empty
						(	$limit_hi
						)
					)
				):
				$sql .= " LIMIT $limit_lo ";
				if	(	!empty
						(	$limit_hi
						)
					):
					$sql .= ", $limit_hi ";
				endif;
			endif;
			$result = $this->get_result
			(	$sql
			);
			if	(	$expose_query
				):
				debug::expose
				(	end
					(	$this->sql
					)
				);
			endif;
			while
				(	$row	=	$result->fetch_array
					(	MYSQLI_ASSOC
					)	
				):
				$owned[$row['id']] = $row['owned_id'];
			endwhile;
			$owned = array_unique
			(	$owned
			);
		endif;
		if	(	!empty
				(	$owned
				)
			&&	$full_records
			):
			if	(	empty
					(	$order_by
					)
				&&	!empty
					(	$key_by
					)
				&&	$key_by	!= 'id'
				):
				$order_by = array
				(	$key_by	=>	''
				);
			else:
				$order_by = array();
			endif;

			$full_records = $this->get_result_array
			(	array
				(	'table'		=>	$owned_table
				,	'in'		=>	array
					(	'id'		=>	$owned
					)
				,	'order_by'		=>	$order_by
				,	'expose_query'	=>	$expose_query
				)
			);
			$owned_ordered = array();
			foreach
				(	$owned	as	$own_id	=>	$record_id
				):
				if	(	!empty
					 	(	$full_records[$record_id]
						)
					):
					$owned_ordered[$full_records[$record_id][$key_by]] = $full_records[$record_id];
				endif;
			endforeach;
			reset
			(	$owned
			);
			return $owned_ordered;
		else:
			return $owned;
		endif;
	}
	
	function get_owner_records
	(	$args	=	array()
	)
	{	extract
		(	debug::function_argument_verify
			(	array
				(	'function'			=>	__CLASS__.'->'.__FUNCTION__
				,	'arguments_input'		=>	$args
				,	'arguments_descriptions'	=>	array
					(	'owned'			=>	array
						(	'blurb'				=>	"array
(	'ownership_id_1'	=>	array
	(	owned_id_1
	,	owned_id_2
	,	...
	)
,	'ownership_id_2'	=>	array
	(	...
	)
,	...
)"
						)
					,	'full_records'		=>	array
						(	'blurb'				=>	'If false, returns an array of owned id\'s.  If true, returns the complete records corresponding to the owned id\'s.'
						,	'possible_values'	=>	array
							(	0
							,	1
							)
						,	'default_value'		=>	0
						)
					,	'pop_single_row'		=>	array
						(	'blurb'				=>	'TRUE: DE-NESTS ARRAY IF ONLY ONE RESULT RETURNED.'
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
		
		$sql = "";
		$owners = array();
		
		foreach
			(	$owned	as	$ownership_id_in	=>	$owned_ids
			):
			if	(	$ownership_id_in	
				):	
				if	(	!empty
						(	$owned_ids
						)
					):
					if	(	!empty
							(	$sql
							)
						):
						$sql .= " OR ";
					endif;
					if	(	key
							(	$owned_ids
							)
						):
						$owned_ids = array_keys
						(	$owned_ids
						);
					endif;
					$owned_ids_string = arrays::implode_safe
					(	array
						(	'glue'		=>	','
						,	'pieces'	=>	$owned_ids
						)
					);
					$sql .= "	(	ownership_id	=	$ownership_id_in
								AND	owned_id		";
					$sql .= 
					(	strpos
						(	$owned_ids_string
						,	','
						)
					)
					?	"	IN	("
						.	$owned_ids_string
						.	")	"
					:	"	=	".$owned_ids_string
					;
					$sql .= "	)	";
				endif;
			else:
				reset
				(	$owned
				);
				debug::expose
				(	$owned
				);
				echo '<pre><hr /><h2>PERFORMING ERROR BACKTRACE...</h2><hr /><br />';
				$error_backtrace = debug_backtrace();
				unset
				(	$error_backtrace[0]
				);
				debug::expose
				(	$error_backtrace
				);
				echo '<hr /></pre>';
				exit;
			endif;			
		endforeach;
		reset
		(	$owned
		);
		if	(	!empty
				(	$sql
				)
			):
			$sql = "	SELECT	owner_id
						FROM	owned
						WHERE	(	"
			.	$sql
			.	"	)	
						ORDER	BY				ownership_id
								,				owner_id
								,				sort_order
								,				owned_id
						"
			;
			$result = $this->get_result
			(	$sql
			);
			while
				(	$row	=	$result->fetch_array
					(	MYSQLI_ASSOC
					)	
				):
				$owners[$row['owner_id']] = $row['owner_id'];
			endwhile;
//			$owners = array_unique($owners);
		endif;
		if	(	!empty
				(	$owners
				)
			&&	$full_records
			):
			return $this->get_result_array
			(	array
				(	'table'		=>	$this->ownerships[$ownership_id_in]['owner_table']
				,	'in'		=>	array
					(	'id'		=>	$owners
					)
				,	'pop_single_row'	=>	$pop_single_row
				)
			);
		else:
			if	(	$pop_single_row
				&&	count
					(	$owners
					)	==	1
				):
				return array_pop
				(	$owners
				);
			else:
				return $owners;
			endif;
		endif;
	}

	function get_result
	(	$args	=	array()
	)
	{	extract
		(	debug::function_argument_verify
			(	array
				(	'function'			=>	__CLASS__.'->'.__FUNCTION__
				,	'arguments_input'		=>	$args
				,	'arguments_descriptions'	=>	array
					(	'sql'
					,	'return_error_info'			=>	array
						(	'possible_values'			=>	array
							(	0
							,	1
							)
						,	'default_value'				=>	0
						)
					,	'return_array'				=>	array
						(	'possible_values'			=>	array
							(	0
							,	1
							)
						,	'default_value'				=>	0
						)
					,	'tables'					=>	array	// FOR CACHING PURPOSES
						(	'default_value'				=>	array
							(	
							)
						)
					)
				)
			)
		);
/*		
		if	(	empty
				(	$this->host
				)
			):
			$this->fail
			(	'No target database supplied for SQL query:<hr />"'
			.	$sql
			.	'"'
			);
		endif;
*/
		$trimmed_sql = trim
		(	$sql
		);
		if	(	empty
				(	$tables
				)
			):
			$tables_used = array();
		else:
			$tables_used = array_flip
			(	$tables
			);
		endif;
		if	(	!empty
				(	$GLOBALS['memcache']
				)
			&&	!isset
				(	$tables_used['cache_key']
				)
			&&	!isset
				(	$tables_used['owner']
				)
			&&	!isset
				(	$tables_used['owned']
				)
			):
			if	(	strstr
					(	$trimmed_sql
					,	"SELECT"
					)	==	$trimmed_sql
				||	strstr
					(	$trimmed_sql
					,	"SHOW"
					)	==	$trimmed_sql
				):
				// IF IT'S A SELECT OR SHOW QUERY, CHECK FOR CACHED RESULTS
				$cached_key = $this->get_result_array
				(	array
					(	'table'				=>	'cache_key'
					,	'fields'			=>	array
						(	'sql_md5'
						)
					,	'equals'			=>	array
						(	'sql_md5'			=>	md5
							(	$sql
							)
						)
					,	'limit_lo'			=>	1
					,	'pop_single_row'	=>	1
					)
				);
				if	(	$cached_key
					):
					$results = $GLOBALS['memcache']->get
				    (	$cached_key
				    );
				endif;
			else:
				if	(	!empty
						(	$tables
						)
					&&	is_array
						(	$tables
						)
					):
					// IF IT'S AN UPDATE OR DELETE, BLOW AWAY CACHED RESULTS FOR THE TABLES IN QUESTION
					$cached_keys = $this->get_result_array
					(	array
						(	'table'				=>	'cache_key'
						,	'fields'			=>	array
							(	'sql_md5'
							)
						,	'in'				=>	array
							(	'table_name'		=>	$tables
							)
						)
					);
					if	(	$cached_keys
						):
						foreach
							(	$cached_keys	as	$cached_key
							):
							// DELETE EM
							$deleted = $GLOBALS['memcache']->delete
							(	$cached_key
							); 
						endforeach;
						$this->query
						(	"	DELETE
								FROM	cache_key
								WHERE	table_name	IN	('".implode("','",$tables)."')
								OR		sql_md5		IN	('".implode("','",$cached_keys)."'_
							"	
						);
					endif;					
				endif;				
			endif;
		endif;
		
		if	(	empty
				(	$results
				)
			||	!$return_array
			):

	//		CAN'T DO THE TRIM, IT FUCKS UP SLASHED CHARACTER INPUT
	//		$sql = strings::strip_chrs(trim($sql));
			if	(	debug::get_mode()
				):
				$this->sql[] = $sql;
			endif;
		
			$result = $this->query
			(	$sql
			);

			if	(	$this->errno
				):
				$this->fail
				(	array
					(	'cause_of_death'	=>	'SQL query<hr />"'
						.	$sql
						.	'"'
					,	'error_backtrace'	=>	1
					)
				);
			endif;
			if	(	!$result
				):
				if	(	$return_error_info
					):
					$result = array
					(	'$_SERVER["SCRIPT_FILENAME"]'	=>	$_SERVER['SCRIPT_FILENAME']
					,	'$sql'							=>	'"'
						.	$sql
						.	'"'
					,	'$db->host_info'				=>	$this->host_info
					,	'$db->server_info'				=>	$this->server_info
					,	'$db->protocol_version'			=>	$this->protocol_version
					,	'$db->errno'					=>	$this->errno
					,	'$db->error'					=>	$this->error
					,	'$db->info'						=>	$this->info
					);
				else:
					$this->fail
					(	array
						(	'cause_of_death'	=>	'SQL query<hr />"'
							.	$sql
							.	'"'
						,	'error_backtrace'	=>	1
						)
					);
				endif;
			endif;
	//		$this->results[] = $result;
			$this->result = $result;
			if	(	$return_array
				):
				return $this->result_to_array
				(	array
					(	'result'			=>	&$result
					,	'cachery'			=>	array
						(	'tables'			=>	$tables
						,	'sql'				=>	$sql
						)
					)
				);
			else:
				return $result;
			endif;
		
		else:
			return unserialize
			(	$results
			);
		endif;
	}
	
	function get_result_array
	(	$args	=	array()
	)
	{	extract
		(	debug::function_argument_verify
			(	array
				(	'function'			=>	__CLASS__.'->'.__FUNCTION__
				,	'arguments_input'		=>	$args
				,	'arguments_descriptions'	=>	array
					(	'table'
					,	'fields'					=>	array
						(	'blurb'						=>	'Which fields to return.  If specific fields are listed, only those fields will be returned.  However, all fields will still be queried, as other fields may be needed for order_by and key_by configurations.'
//							.	'If a string value is entered for this argument, only the fields referenced in the table\'s record_title_template will be queried and returned, automatically keyed by id, for maximum efficiency when producing large result arrays.'
						,	'default_value'				=>	array
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
						(	'blurb'				=>	"WHERE X IN (Y) // array('x1'=>array('y1','y1',etc.),etc.)"
						,	'default_value'		=>	0
						)
					,	'order_by'			=>	array
						(	'blurb'				=>	"ORDER BY column direction // array('column1'=>'direction1',etc.)"
						,	'default_value'		=>	0
						)
					,	'limit_lo'			=>	array
						(	'default_value'		=>	0
						)
					,	'limit_hi'			=>	array
						(	'default_value'		=>	0
						)
					,	'pop_single_row'		=>	array
						(	'blurb'				=>	'TRUE: DE-NESTS ARRAY IF ONLY ONE RESULT RETURNED.'
						,	'possible_values'		=>	array
							(	0
							,	1
							)
						,	'default_value'		=>	0
						)
					,	'expose_query'			=>	array
						(	'blurb'				=>	'TRUE: EXPOSES SQL QUERY'
						,	'default_value'		=>	0
						)
					,	'where'				=> array
						(	'blurb'				=>	"ADDITIONAL COMPLEX WHERE CONDITIONS
												// string(x1 >= y1 AND/OR x2 != 'y2' AND x3 LIKE '%y3%'
												// AND x4 IS NULL,etc.)"
						,	'default_value'		=>	0
						)
					,	'owners'			=>	array
						(	'blurb'				=>	"array( 'ownership_id'	=>	array(id1,id2,id3,...))"
						,	'default_value'		=>	array()
						)
					,	'count_only'		=>	array
						(	'possible_values'	=>	array
							(	0
							,	1
							)
						,	'default_value'		=>	0
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
		
		$select = 
		(	$count_only
		)
		?	'COUNT(*)'
		:	'*'
		;
		
		if	(	!is_array
				(	$fields
				)
			):
			$fields = arrays::sort_by_strlen
			(	array
				(	'array'			=>	array_keys
					(	$this->tables[$table]->fields
					)
				,	'sort_by_key'	=>	0
				,	'reverse'		=>	1
				)
			);
			$record_title_template = $this->tables[$table]->record_title_template
			.	'$id'
			;
			foreach
				(	$fields	as	$field
				):
				if	(	strpos
						(	$record_title_template
						,	'$'
							.	$field
						)
					):
					$record_title_template = str_replace
					(	'$'
						.	$field
					,	''
					,	$record_title_template
					);
				else:
					unset
					(	$fields[$field]
					);
				endif;
			endforeach;
			reset
			(	$fields
			);
			$select = arrays::implode_safe
			(	array
				(	'pieces'	=>	$fields
				,	'glue'		=>	' , '
				)
			);
			$key_by = 'id';
		endif;
		
		$sql = "	SELECT	$select
					FROM	`$table`
					";
		$whered = 0;
		
		if	(	!empty
				(	$owners
				)
			):
			foreach
				(	$owners	as	$ownership_id	=>	$owner
				):			
// NEW OWNER FILTERING... REPLACES USER_ROLE ASSOCIATION				
				if	(	$this->ownerships[$ownership_id]['owner_table'] == $table
					):
					if	(	!is_array
							(	$in
							)
						):
						$in = array();
					endif;
					if	(	empty
							(	$in['id']
							)
						||	!is_array
							(	$in['id']
							)
						):
						$in['id'] = array();
					endif;
					$in['id'] += array_keys
					(	$owner
					);
					unset
					(	$owners[$ownership_id]
					);
				endif;	
//////////////////////////////////////////////////////////			
/*
				if	(	$this->ownerships[$ownership_id]['owned_table']	!= $table
					):
					unset
					(	$owners[$ownership_id]
					);
				endif;
*/
			endforeach;
			reset
			(	$owners
			);
		endif;
			
		if	(	is_array
				(	$equals
				)
			):
			foreach
				(	$equals	as	$that_key	=>	$that_val	
				):								
				if	(	is_scalar
						(	$that_val
						)
					):
					$sql .= 
					(	$whered
					)	
					?	" AND "
					:	" WHERE "
					;
					$sql .= " `$that_key` = '$that_val' ";
					$whered = 1;
				else:				
					echo '<pre>Catchable Fatal Error: You tried to search the database field `'
					.	$table
					.	'`.`'
					.	$that_key
					.	'` for a non-scalar value (most likely a PHP object).<hr /><h2>PERFORMING ERROR BACKTRACE...</h2><hr /><br />';
					echo '<hr /></pre>';
					debug::expose
					(	$that_val	
					);
					debug::expose
					(	debug_backtrace()
					);
					exit;
				endif;
			endforeach;
		endif;
		if	(	is_array
				(	$in
				)
			):
			foreach
				(	$in	as	$in_key	=>	$in_val_ray	
				):
				if	(	!empty
						(	$in_val_ray
						)
					&&	is_array
						(	$in_val_ray
						)
					):
					$sql .= 
					(	$whered
					)
					?	" AND "
					:	" WHERE "
					;
					$sql .= " `$in_key` IN (	";
					$commad = 0;
					foreach
						(	$in_val_ray	as	$in_val_key	=>	$in_val	
						):
						if	(	$commad
							):
							$sql .= ',';
						endif;
						$sql .= "'$in_val'";
						$commad = 1;
					endforeach;
					$sql .= "	) ";
					$whered = 1;
				endif;
			endforeach;
		endif;
		if	(	!empty
				(	$where
				)
			):
			$sql .= 
			(	$whered
			)
			?	" AND "
			:	" WHERE "
			;
			$sql .= $where;
			$whered = 1;
		endif;
		
		if	(	!empty
			 	(	$owners
				)
			):
			$viable_owned = 
			(	!empty
				(	$limit_lo
				)
			||	!empty
				(	$limit_hi
				)
			)
			?	$this->get_result_array
				(	array
					(	'table'		=>	$table
					,	'fields'	=>	array
						(	'id'
						)
					,	'equals'	=>	$equals
					,	'in'		=>	$in
					,	'where'		=>	$where
					)
				)
			:	0
			;
			$owned = $this->get_owned_records
			(	array
				(	'owned_table'	=>	$table
				,	'owners'		=>	$owners
				,	'greedy'		=>	$greedy
				,	'owned_in'		=>	$viable_owned
				,	'limit_lo'		=>	$limit_lo
				,	'limit_hi'		=>	$limit_hi
				,	'expose_query'	=>	$expose_query
				)
			);
			if	(	empty
					(	$owned
					)
				):
				return
				(	$count_only
				)
				?	0
				:	array()
				;
			else:
				$sql .= 
				(	$whered
				)
				?	" AND "
				:	" WHERE "
				;
				if	(	!empty
						(	$limit_lo
						)
					||	!empty
						(	$limit_hi
						)
					):
/*
					$owned =
					(	empty
						(	$limit_hi
						)
					)
					?	array_slice
						(	$owned
						,	0
						,	$limit_lo
						)
					:	array_slice
						(	$owned
						,	$limit_lo
						,	$limit_hi
						)
					;
*/
					$limit_applied_to_owned = true;
				endif;
				$sql .= "	id	IN	("
				.	implode
					(	','
					,	$owned
					)
				.	")"
				;
				$whered = 1;
			endif;
		endif;
		
		if	(	empty
				(	$order_by
				)
			&&	!empty
				(	$key_by
				)
			&&	$key_by	!= 'id'
			):
			$order_by = array
			(	$key_by	=>	''
			);
		endif;
        if	(	!empty
        		(	$order_by
                )
            ):
            $sql .= " ORDER BY ";
            $commad = 0;
            foreach
            	(	$order_by	as	$order_by_field	=>	$order_by_dir	
                ):
                if	(	$commad
                	):
                    $sql .= " , ";
                endif;
                $sql .= $order_by_field
				.	" "
				.	$order_by_dir
				;
                $commad = 1;
            endforeach;
        endif;
		if	(	(	!empty
					(	$limit_lo
					)
				||	!empty
					(	$limit_hi
					)
				)
			&&	empty
			 	(	$limit_applied_to_owned
				)
			):
			$sql .= " LIMIT $limit_lo ";
			if	(	!empty
					(	$limit_hi
					)
				):
				$sql .= ", $limit_hi ";
			endif;
		endif;

		$result = $this->get_result
		(	array
			(	'sql'		=>	$sql
			,	'tables'	=>	array
				(	$table
				)
			)
		);
		
		if	(	is_object
				(	$result
				)
			&&	get_class
				(	$result
				)	==	'mysqli_result'
			):

			if	(	$expose_query
				):
				debug::expose
				(	end
					(	$this->sql
					)
				);
			endif;
			if	(	$count_only
				):
				$result_row = $result->fetch_row();
				$results = array_shift
				(	$result_row
				);
			else:
				if	(	in_array
						(	'*'
						,	$fields
						)
					):
					$fields = array();
				endif;
				$results = $this->result_to_array
				(	array
					(	'result'			=>	&$result
					,	'key_by'			=>	$key_by
					,	'fields'			=>	$fields
					,	'pop_single_row'	=>	$pop_single_row
					,	'cachery'			=>	array
						(	'tables'			=>	array
							(	$table
							)
						,	'sql'				=>	$sql
						)
					)
				);
				if	(	empty
					 	(	$order_by
						)
					&&	!empty
						(	$owned
						)
					):
					$owned_ordered = array();
					foreach
						(	$owned	as	$own_id	=>	$record_id
						):
						if	(	!empty
							 	(	$results[$record_id]
								)
							):
							$owned_ordered[$record_id] = $results[$record_id];
						endif;
					endforeach;
					reset
					(	$owned
					);
					$results = $owned_ordered;
					unset
					(	$owned_ordered
					);
				endif;
			endif;
			
		else:
			$results = &$result;		
		endif;
		
		return $results;
	}
	
	function get_tables
	(	
	)
	{	if	(	empty
				(	$this->initialized
				)
			):
			$this->initialize();
		endif;
		
		// OWNERSHIPS
		$this->tables['owner'] = new table
		(	array
			(	'name'		=>	'owner'
			,	'hidden'	=>	1
			)
		);
		$this->ownerships = $this->tables['owner']->select_records
		(	array
			(	'equals'	=>	array
				(	'status'	=>	'Active'
				)
			)
		);
		foreach
			(	$this->ownerships	as	$ownership_id =>	&$ownership_info	
			):
			if	(	empty
					(	$ownership_info['owner_title']
					)
				):
				$ownership_info['owner_title'] = 'Associated '
				.	strings::label
					(	$ownership_info['owner_table']
					)
				;
			endif;
			$ownership_info['owner_name'] = strings::name_safe
			(	$ownership_info['owner_title']
			);
			$temp_title = 0;
			if	(	empty
					(	$ownership_info['owned_title']
					)
				):
				$ownership_info['owned_title'] = strings::label
				(	$ownership_info['owner_table']
					.	' '
					.	$ownership_info['owned_table']
				);
				$temp_title = 1;
			endif;
			if	(	empty
					(	$ownership_info['owned_name']
					)
				):
				$ownership_info['owned_name'] = strings::name_safe
				(	$ownership_info['owned_title']
				);
			endif;
			if	(	$temp_title
				):
				$ownership_info['owned_title'] = '';
			endif;
		endforeach;
		reset
		(	$this->ownerships
		);
		
		// VALIDATORS
		$this->tables['validator'] = new table
		(	array
			(	'name'		=>	'validator'
			,	'hidden'	=>	1
			)
		);
		$this->field_validators = $this->tables['validator']->select_records
		(	array
			(	'key_by'	=>	'name'
			)
		);
		
		// UPLOADABLE FILE FIELDS
		$this->tables['file_type'] = new table
		(	array
			(	'name'		=>	'file_type'
//			,	'hidden'	=>	1
			)
		);
		$this->tables['upload'] = new table
		(	array
			(	'name'		=>	'upload'
//			,	'hidden'	=>	1
			)
		);
		$this->tables['upload']->get_ownerships();
		$file_fields = $this->tables['upload']->select_records
		(	array
			(	'equals'	=>	array
				(	'status'	=>	'Active'
				)
			)
		);
		$this->file_fields_by_table = array();
		foreach
			(	$file_fields	as	$upload_info
			):
			if	(	empty
					(	$this->file_fields_by_table[$upload_info['table_name']]
					)
				):
				$this->file_fields_by_table[$upload_info['table_name']] = array();
			endif;
			$this->file_fields_by_table[$upload_info['table_name']][$upload_info['field_name']] = $upload_info;
			$this->file_fields_by_table[$upload_info['table_name']][$upload_info['field_name']]['select_existing'] = variables::english_to_boolean
			(	$this->file_fields_by_table[$upload_info['table_name']][$upload_info['field_name']]['select_existing']
			);
			$this->file_fields_by_table[$upload_info['table_name']][$upload_info['field_name']]['filter_by_owner'] = variables::english_to_boolean
			(	$this->file_fields_by_table[$upload_info['table_name']][$upload_info['field_name']]['filter_by_owner']
			);
			if	(	empty
					(	$this->file_fields_by_table[$upload_info['table_name']][$upload_info['field_name']]['path']
					)
				):
				$this->file_fields_by_table[$upload_info['table_name']][$upload_info['field_name']]['path'] = $this->file_fields_by_table[$upload_info['table_name']][$upload_info['field_name']]['table_name']
				.	'_'
				.	$this->file_fields_by_table[$upload_info['table_name']][$upload_info['field_name']]['field_name']
				;
			endif;
			if	(	substr
					(	$this->file_fields_by_table[$upload_info['table_name']][$upload_info['field_name']]['path']
					,	-1
					)
					!=	'/'
				):
				$this->file_fields_by_table[$upload_info['table_name']][$upload_info['field_name']]['path'] .= '/';
			endif;
			unset
			(	$this->file_fields_by_table[$upload_info['table_name']][$upload_info['field_name']]['table_name']
			,	$this->file_fields_by_table[$upload_info['table_name']][$upload_info['field_name']]['field_name']
			);
			$this->file_fields_by_table[$upload_info['table_name']][$upload_info['field_name']]['file_types'] = $this->get_owner_records
			(	array
				(	'owned'			=>	array
					(	$this->tables['upload']->owners['file_type']	=>	array
						(	$upload_info['id']
						)
					)
				,	'full_records'	=>	1
				)
			);
		endforeach;
		unset
		(	$file_fields
		);
		
		$result = $this->get_result
		(	"	SHOW	TABLES
			"
		);
		while
			(	$row = $result->fetch_array
				(	MYSQLI_NUM
				)
			):
			$table_name = $row[0];
			if	(	strstr
					(	$table_name
					,	'_bak'
					)
					==	'_bak'
				):
				$this->tables_hidden[] = $table_name;
			else:
				if	(	empty
						(	$this->tables[$table_name]
						)
					):
					$hidden = 
					(	in_array
						(	$table_name
						,	$this->tables_hidden
						)
					)
					?	1
					:	0
					;
					$this->tables[$table_name] = new table
					(	array
						(	'name'		=>	$table_name
						,	'hidden'	=>	$hidden
						)
					);
				endif;
			endif;
		endwhile;
		$result->close();
		
		$tables_info = $this->get_result_array
		(	array
			(	'table'			=>	$this->info_table
			,	'key_by'		=>	'name'
			)
		);
		
		foreach
			(	$this->tables	as	$table_name	=>	&$table_obj	
			):
			$table_obj->file_fields = 
			(	empty
				(	$this->file_fields_by_table[$table_name]
				)
			)
			?	array()
			:	$this->file_fields_by_table[$table_name]
			;
			if	(	!$table_obj->hidden
				):
				if	(	empty
						(	$tables_info[$table_name]
						)
					):
					$table_obj->id = $this->insert_row
					(	array
						(	'table'	=>	$this->info_table
						,	'row'	=>	array
							(	'name'	=>	$table_name
							)
						)
					);
					$tables_info[$table_name] = array();
				endif;
				foreach
					(	$tables_info[$table_name]	as	$info_field	=>	$info_value	
					):
					if	(	isset
							(	$this->tables[$table_name]->$info_field
							)
						&&	empty
							(	$this->tables[$table_name]->$info_field
							)
						):
						switch
							(	$info_value
							):
							case 'Yes':
								$info_value = 1;
								break;
							case 'No':
								$info_value = 0;
								break;
						endswitch;
						$this->tables[$table_name]->$info_field = $info_value;
					endif;
				endforeach;
				reset
				(	$tables_info[$table_name]
				);
			endif;
		
		endforeach;
		
		foreach
			(	$this->ownerships	as	$ownership_id =>	&$ownership_info	
			):
			if	(	empty
					(	$ownership_info['owned_title']
					)
				):
				$ownership_info['owned_title'] = 
				(	empty
					(	$this->tables[$ownership_info['owned_table']]->title
					)
				)
				?	strings::label
					(	$ownership_info['owned_table']
					)
				:	$this->tables[$ownership_info['owned_table']]->title
				;
			endif;
		endforeach;
		reset
		(	$this->ownerships
		);
				
		unset
		(	$tables_info
		,	$table_obj
		);
	}
	
	function initialize
	(
	)
	{	if	(	empty
				(	$this->initialized
				)
			):
			
			$this->info_table = '_table';
			
			$this->field_types		= array
			(	0	=>	'DECIMAL'
			,	1	=>	'TINYINT'
			,	2	=>	'SMALLINT'
			,	3	=>	'INTEGER'
			,	4	=>	'FLOAT'
			,	5	=>	'DOUBLE'
			,	7	=>	'TIMESTAMP'
			,	8	=>	'BIGINT'
			,	9	=>	'MEDIUMINT'
			,	10	=>	'DATE'
			,	11	=>	'TIME'
			,	12	=>	'DATETIME'
			,	13	=>	'YEAR'
			,	14	=>	'DATE'
			,	16	=>	'BIT'
			,	246	=>	'DECIMAL'
			,	247	=>	'ENUM'
			,	248	=>	'SET'
			,	249	=>	'TINYBLOB'
			,	250	=>	'MEDIUMBLOB'
			,	251	=>	'LONGBLOB'
			,	252	=>	'BLOB'
			,	253	=>	'VARCHAR'
			,	254	=>	'CHAR'
			,	255	=>	'GEOMETRY'
			);
			$this->field_types_numeric	= array
			(	'int'
			,	'tinyint'
			,	'smallint'
			,	'mediumint'
			,	'bigint'
			,	'float'
			,	'double'
			,	'decimal'
			);
			$this->tables_hidden	= array
			(	'_table'
			,	'field'
			,	'validator'
			,	'owner'
			,	'owned'
			,	'state'
//			,	'country'
			);
			$this->fields_hidden_from_record = array
			(	'display_order'
			,	'sort_order'
			);
			$this->fields_edit_forbidden = array
			(	'inserted'
			,	'updated'
			,	'signup_browser'
			,	'signup_ip'
			,	'last_login'
			,	'login_count'
			,	'files_uploaded'
			,	'files_downloaded'
			);
			$this->fields_ignore = array
			(	
			);
			$this->fields_dupe_confirm_required = array
			(	'password'
			);
			$this->tables = 
			$this->fields_hidden_from_table = 
			$this->sql = 
			$this->results = 
			array();
			
			// TABLES HIDDEN (DIRECT EDITING FORBIDDEN)
			if	(	!debug::get_mode()
				):
				array_push
				(	$this->tables_hidden
				, 	'file_type'
				,	'file_upload_record'
				);
			endif;
			
			$config = &$GLOBALS['cfg'];
			foreach	
				(	$config['db']	as	$db_key	=>	$db_val
				):
				$this->$db_key	=	$db_val;
			endforeach;
	
			$this->initialized	=	1;
			
		endif;
	}
		
	function insert_row
	(	$args	=	array()
	)
	{	extract
		(	debug::function_argument_verify
			(	array
				(	'function'			=>	__CLASS__.'->'.__FUNCTION__
				,	'arguments_input'		=>	$args
				,	'arguments_descriptions'	=>	array
					(	'table'
					,	'row'		=>	array
						(	'blurb'			=>	"array
(	'column1'	=>	'value1'
,	...
)"
						)
					)
				)
			)
		);
		
		$inserted_rows = $this->insert_rows
		(	array
			(	'table'	=>	$table
			,	'rows'	=>	array
				(	$row
				)
			)
		);
		return array_shift
		(	$inserted_rows
		);
	}
	
	function insert_rows
	(	$args	=	array()
	)
	{	extract
		(	debug::function_argument_verify
			(	array
				(	'function'			=>	__CLASS__.'->'.__FUNCTION__
				,	'arguments_input'		=>	$args
				,	'arguments_descriptions'	=>	array
					(	'table'
					,	'rows'			=>	array
						(	'blurb'		=>	"array
(	array
	(	'column1'	=>	'value1'
	,	...
	)
,	array
	(	'column1'	=>	'value1'
	,	...
	)
,...
)"
						)
					)
				)
			)
		);
		
		$rows_inserted = array();
		if	(	is_array
				(	$rows
				)
			&&	count
				(	$rows
				)
			):
			foreach
				(	$rows	as	$row
				):
				$sql = "	INSERT	INTO	`$table`
									(	";
				$cols = "";
				$vals = "			)
							VALUES	(	";
				if	(	is_array
						(	$row
						)
					):
					$commad = 0;
					foreach
						(	$row	as	$col	=>	$val
						):
						if	(	$commad
							):
							$cols .= "	,	";
							$vals .= "	,	";
						endif;
						$cols .= " `$col` ";
						$vals .= "	'"
						.	(	(	is_numeric
									(	$val
									)
								)
								?	$val
								:	addslashes
									(	$val
									)
							)
						.	"' "
						;
						$commad = 1;
					endforeach;
				endif;
				if	(	empty
						(	$row['inserted']
						)
					):
					$cols .= "	,	inserted	";
					$vals .= "	,	NOW()	";
				endif;
				$sql .= $cols.$vals."	)	";
				$result = $this->get_result
				(	array
					(	'sql'		=>	$sql
					,	'tables'	=>	array
						(	$table
						)
					)
				);
				$rows_inserted[] = $this->insert_id;
			endforeach;			
		endif;
		return $rows_inserted;
	}
	
	function order_by_real_fields
	(	$args	=	array()
	)
	{	extract
		(	debug::function_argument_verify
			(	array
				(	'function'			=>	__CLASS__.'->'.__FUNCTION__
				,	'arguments_input'		=>	$args
				,	'arguments_descriptions'	=>	array
					(	'table'
					,	'order_by'
					,	'return_as'	=>	array
						(	'possible_values'	=>	array
							(	'given'
							,	'array'
							,	'string'
							)
						,	'default_value'		=>	'string'
						)
					)
				)
			)
		);
		
		if	(	empty
				(	$order_by
				)
			||	empty
				(	$this->tables[$table]
				)
			):
			$order_by	=	array
			(	'id'	=>	''
			);
		endif;
		if	(	!empty
				(	$this->tables[$table]
				)
			):
			if	(	empty
					(	$this->tables[$table]->fields
					)
				&&	$table != $this->tables[$table]->info_table
				):
				$this->tables[$table]->initialize();
			endif;
			if	(	!is_array
					(	$order_by
					)
				):
				if	(	$return_as	==	'given'
					):
					$return_as	=	'string';
				endif;
				// CONVERT STRING TO ARRAY FOR FIELD COMPARISON
				$order_by = strings::strip_chrs
				(	trim
					(	$order_by
					)
				);
				if	(	strstr
						(	$order_by
						,	' '
						)
					):
					$order_by_array = array();
					if	(	strstr
							(	$order_by
							,	','
							)
						):
						$order_by = str_replace
						(	' ,'
						,	','
						,	$order_by
						);
						$order_by = str_replace
						(	', '
						,	','
						,	$order_by
						);
						$order_by_pairs = explode
						(	','
						,	$order_by
						);
						foreach
							(	$order_by_pairs	as	$order_by_pair	
							):
							if	(	strstr
									(	$order_by_pair
									,	' '
									)
								):
								$order_by_pair_ray = explode
								(	' '
								,	$order_by_pair
								);
								$order_by_array[$order_by_pair_ray[0]] = $order_by_pair_ray[1];
							else:
								$order_by_array[$order_by_pair] = '';
							endif;
						endforeach;
					else:
						$order_by_pair_ray = explode
						(	' '
						,	$order_by
						);
						$order_by_array[$order_by_pair_ray[0]] = $order_by_pair_ray[1];
					endif;
					$order_by = $order_by_array;
				else:
					$order_by = array
					(	$order_by	=>	''
					);
				endif;
			else:
				if	(	$return_as	==	'given'
					):
					$return_as	=	'array';
				endif;
			endif;
			foreach
				(	$order_by	as	$order_by_field	=>	$order_by_dir	
				):
				if	(	empty
						(	$this->tables[$table]->fields[$order_by_field]
						)
					):
					unset
					(	$order_by[$order_by_field]
					);
				endif;
			endforeach;
			reset
			(	$order_by
			);
		endif;
		if	(	$return_as	!=	'array'
			):
			if	(	count
					(	$order_by
					)
				):
				// CONVERT ARRAY BACK TO STRING
				$order_by_string = '	ORDER	BY	';
				$commad = 0;
				foreach
					(	$order_by	as	$order_by_field	=>	$order_by_dir	
					):
					if	(	$commad
						):
						$order_by_string .= ' , ';
					endif;
					$order_by_string .= $order_by_field
					.	' '
					.	$order_by_dir
					;
					$commad = 1;
				endforeach;
			else:
				$order_by_string = '';
			endif;
			$order_by = $order_by_string;
		endif;
		return $order_by;
	}
	
	function random_id
	(	$args	=	array()
	)
	{	extract
		(	debug::function_argument_verify
			(	array
				(	'function'			=>	__CLASS__.'->'.__FUNCTION__
				,	'arguments_input'		=>	$args
				,	'arguments_descriptions'	=>	array
					(	'table'			
					,	'digits'		=>	array
						(	'default_value'	=>	12
						)
					)
				)
			)
		);
		
		$old_id	= 1;
		while
			(	$old_id
			):
			$rand_id = '';
			for	(	$d	=	0
				;	$d	<	$digits
				;	$d++
				):
				$rand_id .= 
				(	empty
					(	$rand_id
					)	
				)
				?	rand
					(	1
					,	9
					)
				:	rand
					(	0
					,	9
					)
				;
			endfor;
			$old_id = $GLOBALS['dbi']->query
			(	"	SELECT	id
					FROM	`$table`
					WHERE	id	=	$rand_id
					LIMIT	1
				"
			)->num_rows
			;			
		endwhile;
		return $rand_id;
	}	
				
	function result_to_array
	(	$args	=	array()
	)
	{	extract
		(	debug::function_argument_verify
			(	array
				(	'function'			=>	__CLASS__.'->'.__FUNCTION__
				,	'arguments_input'		=>	$args
				,	'arguments_descriptions'	=>	array
					(	'result'			=>	array
						(	'blurb'		=>	'result object'
						)
					,	'fields'			=>	array
						(	'default_value'	=>	array()
						)
					,	'fetch_by'		=>	array
						(	'blurb'		=>	'resulttype constant'
						,	'possible_values'		=>	array
							(	MYSQLI_ASSOC
							,	MYSQLI_NUM
							,	MYSQLI_BOTH
							)
						,	'default_value'		=>	MYSQLI_ASSOC
						)
					,	'key_by'			=>	array
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
					,	'pop_single_row'		=>	array
						(	'blurb'				=>	'TRUE: DE-NESTS ARRAY IF ONLY ONE RESULT RETURNED.'
						,	'possible_values'		=>	array
							(	0
							,	1
							)
						,	'default_value'		=>	0
						)
					,	'cachery'		=>	array
						(	'blurb'				=>	'Table info for elasticaching'
						,	'default_value'		=>	array
							(	'tables'			=>	array
								(
								)
							,	'sql'				=>	''
							)
						)
					)
				)
			)
		);
		
		$result_ray = array();
		while
			(	$row = $result->fetch_array
				(	$fetch_by
				)
			):
			if	(	empty
					(	$fields
					)
				):
				$return_fields = $row;
			else:
				if	(	count
						(	$fields
						)	>	1
					):
					$return_fields = array();
					foreach
						(	$fields	as	$field	
						):
						$return_fields[$field] = $row[$field];
					endforeach;
					reset
					(	$fields
					);
				else:
					$return_fields = @$row[$fields[0]];
				endif;
			endif;
			if	(	empty
					(	$key_by
					)
				):
				$result_ray[] = $return_fields;
			else:
				if	(	$key_by == 'id'
					):
					if	(	!isset
							(	$row['id']
							)
						):
						debug::expose
						(	$row
						);
						trigger_error
						(	'queried table does not contain required <em>id</em> field'
						);
						exit;
					else:
						$result_ray[$row['id']] = $return_fields;
					endif;
				else:
					$result_ray[$row[$key_by]] = $return_fields;
				endif;
			endif;
		endwhile;
		$result->close();
		if	(	count
				(	$result_ray
				)	==	1
			&&	$pop_single_row
			):
			$result_ray = array_pop
			(	$result_ray
			);
		endif;
		
		if	(	!empty
				(	$GLOBALS['memcache']
				)
			&&	!empty
				(	$cachery['sql']
				)
			&&	!empty
				(	$cachery['tables']
				)
			&&	is_array
				(	$cachery['tables']
				)
			):
		
			$serialized = serialize
	    	(	$result_ray
	    	);
	    	
	    	$cache_key = md5
	    	(	$cachery['sql']
	    	);
	     
	  		// add the results to the cache so we can use it next time
	  		$set = $GLOBALS['memcache']->replace
	  		(	$cache_key
	  		,	$serialized
	  		,	0
	  		,	$GLOBALS['cfg']['memcache']['cache_time']
	  		);
	  		if	(	!$set
	  			):
	  			$set = $GLOBALS['memcache']->set
			    (	$cache_key
			    ,	$serialized
		  		,	0
		  		,	$GLOBALS['cfg']['memcache']['cache_time']
			    );
			endif;
			if	(	$set
				):
				$this->query
				(	"	DELETE
						FROM	cache_key
						WHERE	sql_md5		=	'".$cache_key."'
					"	
				);
				foreach
					(	$cachery['tables']	as	$table
					):
					if	(	$table	!=	'cache_key'
						):									
						$this->query
						(	"	INSERT	INTO	cache_key
										(		table_name
										,		sql_md5
										,		sql_query
										,		inserted
										)
								VALUES	(		'".$table."'
										,		'".$cache_key."'
										,		'".addslashes($cachery['sql'])."'
										,		NOW()
										)
							"	
						);
					endif;					
				endforeach;
			endif;

		endif;
		
		return $result_ray;
	}
	
	function select_db
	(	$name
	)
	{	parent::select_db
		(	$name
		);
		$this->get_tables();
	}	
	
}