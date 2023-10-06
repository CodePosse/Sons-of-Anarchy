<?php

class permission {

	private static $default_permissions = array
	(	'permissions_applied'		=>	array
		(	'Default Initialization'
		)
	,	'view_table_if'				=>	array(1)
	,	'hide_columns_from_table'	=>	array()
	,	'view_records_if'			=>	array(1)
	,	'filter_records_if'			=>	array(1)
	,	'sort_records_if'			=>	array(1)
	,	're_order_records_if'		=>	array()
	,	'hide_columns_from_record'	=>	array()
	,	'edit_records_if'			=>	array(1)
	,	'lock_columns'				=>	array()
	,	'create_records_if'			=>	array(1)
	,	'delete_records_if'			=>	array(1)
	,	'own_records_if'			=>	array(1)
	,	'disown_records_if'			=>	array(1)
	,	'hide_owned_tables'			=>	array()
	);
	
	public static function evaluate
	(	$args	=	array()
	)
	{	extract
		(	debug::function_argument_verify
			(	array
				(	'function'			=>	__FUNCTION__
				,	'arguments_input'		=>	$args
				,	'arguments_descriptions'	=>	array
					(	'conditions'	=>	array
						(	'blurb'			=>	'Array of conditions to be evaluated.'
						)
					)
				)
			)
		);
		
		$allow = false;
		if	(	!empty
				(	$conditions
				)
			&&	is_array
				(	$conditions
				)
			):		
			foreach
				(	$conditions	as	$condition
				):
				eval
				(	'$allow = (	'
				.	$condition
				.	'	);'
				);
				if	(	!$allow
					):
					break;
				endif;
			endforeach;
		endif;
		return $allow;
	}

	public static function initialize
	(	$args	=	array()
	)
	{	extract
		(	debug::function_argument_verify
			(	array
				(	'function'			=>	__FUNCTION__
				,	'arguments_input'		=>	$args
				,	'arguments_descriptions'	=>	array
					(	'table'	=>	array
						(	'blurb'			=>	'Empty table value applies permission to all tables.'
						,	'default_value'	=>	''
						)
					)
				)
			)
		);
		
		if	(	empty
				(	$GLOBALS['dbi']->tables[$table]->permissions
				)
			):
			if	(	empty
					(	$GLOBALS['user']->permissions[$table]
					)
				):
				$GLOBALS['dbi']->tables[$table]->permissions = self::$default_permissions;
			else:
				$GLOBALS['dbi']->tables[$table]->permissions = $GLOBALS['user']->permissions[$table];
			endif;
		endif;
		
		foreach
			(	self::$default_permissions 	as	$property => $permit	
			):
			if	(	!isset
					(	$GLOBALS['dbi']->tables[$table]->permissions[$property]
					)
				):
				$GLOBALS['dbi']->tables[$table]->permissions[$property] = $permit;
			endif;
		endforeach;
		reset
		(	self::$default_permissions
		);
		
		$affect_fields = array
		(	'hide_from_table'	=>	'hide_columns_from_table'
		,	'hide_from_record'	=>	'hide_columns_from_record'
		,	'edit_allowed'		=>	'lock_columns'
		);
		foreach
			(	$affect_fields	as	$property	=>	$permit	
			):
			if	(	!empty
					(	$GLOBALS['dbi']->tables[$table]->permissions[$permit]
					)
				):
				foreach
					(	$GLOBALS['dbi']->tables[$table]->permissions[$permit]	as	$field_permit	
					):
					if	(	isset
							(	$GLOBALS['dbi']->tables[$table]->fields[$field_permit]
							)
						):
						eval
						(	'$field_property = &$GLOBALS[\'db\']->tables[\''
						.	$table
						.	'\']->fields[\''
						.	$field_permit
						.	'\']->'
						.	$property
						.	';'
						);
						$field_property = 
						(	$property == 'edit_allowed'
						)
						?	(bool) 0
						:	(bool) 1
						;
					endif;
				endforeach;
				reset
				(	$GLOBALS['dbi']->tables[$table]->permissions[$permit]
				);
			endif;
		endforeach;
	}
	
}