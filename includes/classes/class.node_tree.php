<?php

class node_tree {

	function __construct
	(	$args	=	array()
	)
	{	$arguments_descriptions = array
		(	'node_table'				=>	array
			(	'default_value'				=>	'node'
			)
		,	'root_node_id'				=> array
			(	'blurb'						=>	'db id of the parent node that will serve as the root of the returned tree.'
			,	'default_value'				=>	0
			)	
		,	'depth'						=> array
			(	'blurb'						=>	'Integer value indicating the number of sub-levels that should be retrieved.  Zero retrieves as many sub-levels as can be found.'
			,	'default_value'				=>	0
			)
		,	'order_by'					=>	array
			(	'blurb'						=>	'Array of SQL sort directions keyed by table.column names, to be used for ordering the results of the query.  When referencing columns in the chosen node table, leave out the table name, and it will be added automatically based on your choice of node_table.'
//	.'  Use in conjunction with the sort_functions argument to further refine the final node order (i.e., reverse, natcasesort, etc.)'
			,	'default_value' 			=>	array
				(	'owned.sort_order'			=>	''
//				,	'sort_order'				=>	''
				,	'title'						=>	''
				)
			)
/*
		,	'sort_functions'			=> array
			(	'blurb'						=>	'Numerically keyed array containing list of sort functions, in the order they are to be run.  Only sort functions that maintain key/value associations should be used:

arsort();		http://php.net/manual/en/function.arsort.php
asort();		http://php.net/manual/en/function.asort.php
ksort();		http://php.net/manual/en/function.ksort.php
krsort();		http://php.net/manual/en/function.krsort.php
natcasesort();	http://php.net/manual/en/function.natcasesort.php
natsort();		http://php.net/manual/en/function.natsort.php

'
			,	'default_value'				=>	array
				(	'natcasesort'
				)
			)
*/
		);
		extract
		(	debug::function_argument_verify
			(	array
				(	'function'					=>	__CLASS__.'->'.__FUNCTION__
				,	'arguments_input'			=>	$args
				,	'arguments_descriptions'	=>	$arguments_descriptions
				)
			)
		);
		
		unset
		(	$arguments_descriptions['order_by']
		);
		
		foreach
			(	$arguments_descriptions	as	$argument_name	=>	$argument_value	
			):
			$this->$argument_name	=	$$argument_name;
		endforeach;
		
		$this->order_by = array();
		foreach
			(	$order_by	as	$order_by_field	=>	$order_by_dir
			):
			if	(	strpos
					(	$order_by_field
					,	'.'
					)
				):
				$this->order_by[$order_by_field] = $order_by_dir;
			else:
				$this->order_by[$this->node_table.'.'.$order_by_field] = $order_by_dir;
			endif;
		endforeach;
		
		$this->data = $this->get_node_data();
		$this->hierarchy = array
		(	$this->root_node_id	=>	$this->get_node_children()
		);
		ksort
		(	$this->data
		);
	}
	
	function get_node_children
	(	$args	=	array()
	)
	{	extract
		(	debug::function_argument_verify
			(	array
				(	'function'			=>	__CLASS__.'->'.__FUNCTION__
				,	'arguments_input'		=>	$args
				,	'arguments_descriptions'	=>	array
					(	'parent_node_id'			=> array
						(	'blurb'						=>	'db id of the parent node that will serve as the root of the returned tree.'
						,	'default_value'				=>	$this->root_node_id
						)
					,	'depth'						=> array
						(	'blurb'						=>	'Integer value indicating the number of sub-levels that should be retrieved.  Zero retrieves as many sub-levels as can be found.'
						,	'default_value'				=>	$this->depth
						)
					,	'order_by'					=>	array
						(	'blurb'						=>	'Array of SQL sort directions keyed by node column names, to be used for ordering the results of the query.'
						,	'default_value' 			=>	$this->order_by
						)
					)
				)
			)
		);
		
		$GLOBALS['dbi']->tables[$this->node_table]->get_ownerships();
		
		
/*
		if	(	empty
				(	$owner_node
				)
			):
WHY DOESN'T THIS QUERY WORK?
			$sql = "	SELECT		".$this->node_table.".*
						FROM		`".$this->node_table."`
						LEFT JOIN	owned
						ON			".$this->node_table.".id = owned.owned_id
						WHERE		owned.ownership_id				= ".$GLOBALS['dbi']->tables[$this->node_table]->owns['child_'.$this->node_table]."
						AND			".$this->node_table.".status	= 'Active'
						AND			owned.owned_id IS NULL
						";
		else:
*/
		$ownership_ids = array
		(	'child_'
		 	.	$this->node_table
		,	'child'
		,	$this->node_table
		);
		foreach
			(	$ownership_ids	as	$oid
			):
			$ownership_id = $GLOBALS['dbi']->tables[$this->node_table]->owns[$oid];
			if	(	!empty
				 	(	$ownership_id
					)
				):
				break;
			endif;
		endforeach;
		reset
		(	$ownership_ids
		);
		

		$sql = "	SELECT	".$this->node_table.".*
					FROM	`".$this->node_table."`
					,		owned
					WHERE	owned.ownership_id				= ".$ownership_id."
					AND		owned.owner_id					= ".$parent_node_id."
					AND		".$this->node_table.".id		= owned.owned_id
					AND		".$this->node_table.".status	= 'Active'
					";
		if	(	!empty
				(	$order_by
				)
			):
			$sql .= "		ORDER	BY
					";
			foreach
				(	$order_by	as	$table_column	=>	$direction	
				):
				$sql .= " $table_column $direction,";
			endforeach;
			reset
			(	$order_by
			);
		endif;

		$branch = $GLOBALS['dbi']->get_result
		(	array
			(	'sql'			=>	substr
				(	$sql
				,	0
				,	-1
				)
			,	'return_array'	=>	1
			)
		);

		$hierarchy = array();
		if	(	!empty
				(	$branch
				)
			):
			
			switch
				(	$depth
				):
				case '0':
					break;
				case '1':
					$depth = $depth - 2;
					break;
				default: // Any other integer
					$depth--;
			endswitch;
			
			if	(	$depth	>=	-1
				):
				foreach
					(	$branch as $child_node_id => &$child_data	
					):
					if	(	empty
							(	$GLOBALS['user']->visible_nodes
							)
						||	!empty
							(	$GLOBALS['user']->visible_nodes[$child_node_id]
							)
						):
						if	(	empty
								(	$this->data[$child_node_id]
								)
							):
							$this->data[$child_node_id] = $child_data;
/*
							if	(	$GLOBALS['user']->id == 1
								&& 	!empty
								 	(	$child_data['file']
									)
								):
								if	(	empty
									 	(	$this->data[$parent_node_id]['href']
										)
									):
									$this->data[$parent_node_id]['href'] = 'dl.php?f='
									.	urlencode
										(	str_replace
										 	(	' '
											,	''
											,	$this->data[$parent_node_id]['title']
											)
										)
									.	'|'
									.	$GLOBALS['dbi']->tables[$this->node_table]->file_fields['file']['path']
									;
								endif;
								$this->data[$parent_node_id]['href'] .= '|'
								.	$child_data['file']
								;
							endif;
*/
						endif;
						$hierarchy[$child_node_id]	=	$this->get_node_children
						(	array
							(	'parent_node_id'	=>	$child_node_id
							,	'depth'				=>	$depth
							,	'order_by'			=>	$order_by
							)
						);
					endif;
				endforeach;
				
			endif;
			unset
			(	$branch
			);
		endif;
		
		return $hierarchy;
	}
	
	function get_node_data
	(	$args	=	array()
	)
	{	extract
		(	debug::function_argument_verify
			(	array
				(	'function'			=>	__CLASS__.'->'.__FUNCTION__
				,	'arguments_input'		=>	$args
				,	'arguments_descriptions'	=>	array
					(	'nodes'						=>	array
						(	'blurb'						=>	'Numerically keyed array of node ids for which table data will be retrieved and returned.'
						,	'default_value'				=>	array
							(	$this->root_node_id
							)
						)
					)
				)
			)
		);
		
		sort
		(	$nodes
		);
		foreach
			(	$nodes	as	$key	=>	$node	
			):
			if	(	!empty
					(	$this->data[$node]
					)
				):
				unset
				(	$nodes[$key]
				);
			endif;
		endforeach;
		reset
		(	$nodes
		);
		
		return 
		(	empty
			(	$nodes
			)
		)
		?	false
		:	$GLOBALS['dbi']->get_result_array
			(	array
				(	'table'		=>	$this->node_table
				,	'equals'	=>	array
					(	'status'	=>	'Active'
					)
				,	'in'		=>	array
					(	'id'		=>	$nodes
					)
				)
			)
		;
	}
	
	function render_node
	(	$args	=	array()
	)
	{	extract
		(	debug::function_argument_verify
			(	array
				(	'function'			=>	__CLASS__.'->'.__FUNCTION__
				,	'arguments_input'		=>	$args
				,	'arguments_descriptions'	=>	array
					(	'node_id'
					,	'template'			=>	array
						(	'blurb'						=>	'HTML template for display of node data.'
						,	'default_value'				=>	'$title'
						)
					)
				)
			)
		);
		
		if	(	empty
				(	$template
				)
			):
			return '';
		else:
			$node_data = &$this->data[$node_id];
			$node_data['anchor'] = 
			$node_data['name'] = 
			$href = 
			$query = 
			''
			;

			if	(	empty
				 	(	$node_data['href']
					)
				&&	empty
				 	(	$node_data['onclick']
					)
				&&	empty
				 	(	$node_data['action']
					)
				&&	empty
				 	(	$node_data['file']
					)
				):
				$node_data['href'] = '.?z='
				.	$this->node_table
				.	'&id='
				.	$node_id
//				.	'&act=edit'
				;
			endif;

			if	(	!empty
					(	$node_data['href']
					)
				||	!empty
					(	$node_data['action']
					)
				||	!empty
					(	$node_data['file']
					)
				):	
				if	(	!empty
						(	$node_data['file']
						)
					):
					$href = $GLOBALS['dbi']->tables[$this->node_table]->file_fields['file']['path']
					.	$node_data['file']
					;
					
					$size = files::get_size
					(	$href
					);
				else:
					if	(	!empty
							(	$node_data['href']
							)
						):
						$node_data['href'] = strings::replace_keys_with_values
						(	array
							(	'template_string'	=>	$node_data['href']
							,	'values'			=>	$node_data
							)
						);
						if	(	strpos
								(	$node_data['href']
								,	'?'
								)
							):
							$href = explode
							(	'?'
							,	$node_data['href']
							);
							$query = $href[1];
							$href = $href[0];
						else:
							$href = $node_data['href'];
						endif;
					endif;
				endif;
		
				if	(	!empty
						(	$node_data['action']
						)
					):
					$query = 
					(	empty
						(	$query
						)
					)
					?	'z='.$node_data['action']
					:	'z='.$node_data['action'].'&'.$query
					;
				endif;
				$node_data['anchor'] = uri::generate
				(	array
					(	'file_path'			=>	$href
					,	'query'				=>	$query
					,	'query_crypt_key'	=>	$GLOBALS['page']->url_crypt_key
					)
				);
			endif;
			if	(	!empty
					(	$node_data['anchor']
					)
				):
				$node_data['anchor'] = ' href="'
				.	$node_data['anchor']
				.	'"'
				;
			endif;
			if	(	!empty
					(	$node_data['onclick']
					)
				):
				$node_data['anchor'] .= 'onclick="'
				.	strings::replace_keys_with_values
					(	array
						(	'template_string'	=>	$node_data['onclick']
						,	'values'			=>	$node_data
						)
					)
				.	'" '
				;
			endif;
			if	(	!empty
				 	(	$node_data['name']
					)
				):
				$node_data['name'] = ' name="'
				.	$node_data['name']
				.	'"'
				;
			endif;
/*
			if	(	!empty
				 	(	$node_data['anchor']
					)
				&&	!empty
					(	$href
					)
				):
				$node_data['anchor'] .= ' target="_blank"';
			endif;
*/	

			if	(	!empty
				 	(	$size
					)
				):
				$template = str_replace
				(	'</li>'
				,	'&nbsp;('
					.	files::bytes_display
						(	array
						 	(	'bytes'				=>	$size
							,	'decimal_places'	=>	0
							)
						)
					.	')</li>'
				,	$template
				);
			endif;
/*
			if	(	!empty
		 			(	$GLOBALS['user']->roles[3]
					)
				):
				$template = str_replace
				(	'</li>'
				,	'&nbsp;&#151&nbsp;[&nbsp;<a href=".?z='
					.	$this->node_table
					.	'&id='
					.	$node_data['id']
					.	'" style="font-variant:small-caps;">EDIT</a>&nbsp;]</li>'
				,	$template
				);
			endif;
*/	

			$output = strings::replace_keys_with_values
			(	array
				(	'template_string'	=>	$template
				,	'values'			=>	$node_data
				)
			);

			return $output;
		endif;
	}
}

