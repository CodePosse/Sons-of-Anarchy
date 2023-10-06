<?php

class menu {

	function __construct
	(	$args	=	array()
	)
	{	$arguments_descriptions = array
		(	'name'						=>	array
			(	'blurb'						=>	'Name of the menu to construct / render.  Must correspond to existing menu record in db.  No spaces allowed in name string.'
			)
		,	'node_table'				=>	array
			(	'default_value'				=>	'node'
			)
		,	'root_node_id'					=>	array
			(	'default_value'				=>	0
			)
		,	'default_depth'				=>	array
			(	'blurb'						=>	'Node levels to display in menu.'
			,	'possible_values'			=>	array
				(	0
				,	1
				)
			,	'default_value'				=>	0
			)
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

		foreach
			(	$arguments_descriptions	as	$argument_name	=>	$argument_value	
			):
			$this->$argument_name	=	$$argument_name;
		endforeach;
		
		$this->record = $GLOBALS['dbi']->get_result_array
		(	array
			(	'table'				=>	'menu'
			,	'equals'			=>	array
				(	'name'				=>	$this->name
				)
			,	'pop_single_row'	=>	1
			)
		);
		
		$GLOBALS['dbi']->tables['menu']->get_ownerships();
		
		$ownership_ids = array
		(	'root_'
		 	.	$this->node_table
		,	'root'
		,	$this->node_table
		);
		foreach
			(	$ownership_ids	as	$oid
			):
			$ownership_id = $GLOBALS['dbi']->tables['menu']->owns[$oid];
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

		if	(	empty
			 	(	$this->root_node_id
				)
			):	
			$result = $GLOBALS['dbi']->get_result
			(	"	SELECT	owned_id	AS	root_node_id
					FROM	owned
					WHERE	ownership_id	=	".$ownership_id."
					AND		owner_id		=	".$this->record['id']."
					LIMIT	1
					"
			);
			$row = $result->fetch_array
			(	MYSQLI_ASSOC
			);
			$this->root_node_id = $row['root_node_id'];
		endif;
			
		$this->tree = new node_tree
		(	array
			(	'node_table'	=>	$this->node_table
			,	'root_node_id'	=>	$this->root_node_id
			,	'depth'			=>	$this->default_depth
			)
		);
		
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
					(	'title_template'			=>	array
						(	'blurb'						=>	'HTML template for display of menu title.  If not supplied, will be drawn from db.  To suppress display of menu title, set to empty value.'
						,	'default_value'				=>	'$title'
						)
					,	'item_template'				=>	array
						(	'blurb'						=>	'HTML template for display of menu items.  If not supplied, will be drawn from db.  To suppress display of menu title, set to empty value.'
						,	'default_value'				=>	'$title'
						)
					,	'item_separator'			=>	array
						(	'default_value'				=>	''
						)
					,	'depth'						=>	array
						(	'blurb'						=>	'Node levels to display in menu.'
						,	'possible_values'			=>	array
							(	0
							,	1
							)
						,	'default_value'				=>	0
						)
					,	'level_open_template'		=>	array
						(	'default_value'				=>	'' // '<ul>'
						)
					,	'level_close_template'		=>	array
						(	'default_value'				=>	'' // '</ul>'
						)
					,	'expandable'				=>	array
						(	'blurb'						=>	'Set dynamic expandability of menu.'
						,	'possible_values'			=>	array
							(	0
							,	1
							)
						,	'default_value'				=>	0
						)
					,	'return_as'					=>	array
						(	'blurb'						=>	'Return values in specified, pre-rendered format.'
						,	'possible_values'			=>	array
							(	'tree'
							,	'options'
							)
						,	'default_value'				=>	'tree'
						)
					)
				)
			)
		);

		foreach
			(	$args	as	$arg_name	=>	$arg_value
			):
			$this->$arg_name = $arg_value;
		endforeach;

		$output = '';
		foreach
			(	$this->tree->hierarchy	as	$title_node	=>	$item_nodes
			):
			if	(	!empty
					(	$title_template
					)
				):
				switch
					(	$return_as
					):
					case 'options':
						$output = array
						(	$title_node	=>	$this->tree->render_node
							(	array
								(	'node_id'	=>	$title_node
								,	'template'	=>	$title_template
								)
							)
						);
						$subtab = end
						(	$output
						)
						.	' &gt; '
						;
//						$subtab = '&gt; ';
						break;
					default: // case 'tree':
						$output .= $this->tree->render_node
						(	array
							(	'node_id'	=>	$title_node
							,	'template'	=>	$title_template
							)
						);
				endswitch;
			endif;
			switch
				(	$return_as
				):
				case 'options':
					$item_template = str_replace
					(	'$title'
					,	$subtab
						.	'$title'
					,	$item_template
					);
					break;
				default: // case 'tree':
					$output .= $level_open_template;
			endswitch;
			$node_count = count
			(	$item_nodes
			);
			$item_count = 0;
			foreach
				(	$item_nodes	as	$item_node	=>	$item_data
				):
				switch
					(	$return_as
					):
					case 'options':
						$branch = $this->render_node
						(	array
							(	'item_node'			=>	$item_node
							,	'item_data'			=>	$item_data
							,	'item_template'		=>	$item_template
							,	'item_separator'	=>	$item_separator
							,	'return_as'			=>	$return_as
							)
						);
					break;
					default: // case 'tree':
						$output .= $this->render_node
						(	array
							(	'item_node'			=>	$item_node
							,	'item_data'			=>	$item_data
							,	'item_template'		=>	$item_template
							,	'item_separator'	=>	$item_separator
							)
						);
				endswitch;
				
				$item_count++;
				switch
					(	$return_as
					):
					case 'options':
						foreach
							(	$branch	as	$branch_node	=>	$branch_title
							):
							$output[$branch_node] = $branch_title;
						endforeach;
						break;
					default: // case 'tree':
						if	(	$item_count < $node_count
							):
							$output .= $item_separator;
						endif;
				endswitch;
			endforeach;
			switch
				(	$return_as
				):
				case 'options':
					$item_template = str_replace
					(	$subtab
					,	''
					,	$item_template
					);
					break;
				default: // case 'tree':
					$output .= $level_close_template;
			endswitch;
		endforeach;
		return $output;
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
					(	'item_node'
					,	'item_data'
					,	'item_template'				=>	array
						(	'blurb'						=>	'HTML template for display of menu items.  If not supplied, will be drawn from db.  To suppress display of menu title, set to empty value.'
						,	'default_value'				=>	'$title'
						)
					,	'item_separator'			=>	array
						(	'default_value'				=>	''
						)
					,	'level_open_template'		=>	array
						(	'default_value'				=>	'<ul>'
						)
					,	'level_close_template'		=>	array
						(	'default_value'				=>	'</ul>'
						)
					,	'return_as'					=>	array
						(	'blurb'						=>	'Return values in specified, pre-rendered format.'
						,	'possible_values'			=>	array
							(	'tree'
							,	'options'
							)
						,	'default_value'				=>	'tree'
						)
					)
				)
			)
		);

		switch
			(	$return_as
			):
			case 'options':
				$output = array
				(	$item_node	=>	$this->tree->render_node
					(	array
						(	'node_id'	=>	$item_node
						,	'template'	=>	$item_template
						)
					)
				);
				$subtab = $this->tree->data[$item_node]['title']
				.	' &gt; '
				;
//				$subtab = '&gt; ';
				break;
			default: // case 'tree':
				$output = $this->tree->render_node
				(	array
					(	'node_id'	=>	$item_node
					,	'template'	=>	$item_template
					)
				);
		endswitch;

		if	(	!empty
			 	(	$item_data
				)
			):
			switch
				(	$return_as
				):
				case 'options':
					$item_template = str_replace
					(	'$title'
					,	$subtab
						.	'$title'
					,	$item_template
					);
					break;
				default: // case 'tree':
					$output .= $level_open_template;
			endswitch;
			$node_count = count
			(	$item_data
			);
			$child_count = 0;
			foreach
				(	$item_data	as	$child_node	=>	$child_data
				):

				switch
					(	$return_as
					):
					case 'options':
						$branch = $this->render_node
						(	array
							(	'item_node'			=>	$child_node
							,	'item_data'			=>	$child_data
							,	'item_template'		=>	$item_template
							,	'item_separator'	=>	$item_separator
							,	'return_as'			=>	$return_as
							)
						);
						break;
					default: // case 'tree':
						$output .= $this->render_node
						(	array
							(	'item_node'			=>	$child_node
							,	'item_data'			=>	$child_data
							,	'item_template'		=>	$item_template
							,	'item_separator'	=>	$item_separator
							)
						);
				endswitch;
						
				$child_count++;
				switch
					(	$return_as
					):
					case 'options':
						foreach
							(	$branch	as	$branch_node	=>	$branch_title
							):
							$output[$branch_node] = $branch_title;
						endforeach;
						break;
					default: // case 'tree':
						if	(	$child_count < $node_count
							):
							$output .= $item_separator;
						endif;
				endswitch;
			endforeach;
			switch
				(	$return_as
				):
				case 'options':
					$item_template = str_replace
					(	$subtab
					,	''
					,	$item_template
					);
					break;
				default: // case 'tree':
					$output .= $level_close_template;
			endswitch;
		endif;
				
		return $output;
	}
	
}

/*
class menu_item {
	function __construct() {
		
	}
}
*/
