<?php

class table_template {

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
					,	'table'		=>	array
						(	'blurb'		=>	'A reference to the table object to which this table object belongs.'
						)
					,	'records'	=>	array
						(	'blurb'		=>	'A nested array representing the recordset to render.  Should be keyed by row id, with sub-arrays keyed to table field names.'
						,	'default_value'	=>	array()
						)
					,	'owners'	=>	array
						(	'default_value'	=>	array()
						)
					,	'values'	=>	array
						(	'default_value'	=>	array()
						)
					,	'crypt_key'	=>	array
						(	'default_value'	=>	''
						)
					)
				)
			)
		);
		
		$this->table = &$GLOBALS['dbi']->tables[$table];
		$this->records = $records;
		
		$this->records_total = count
		(	$this->records
		);
		
		$this->owners = $owners;
		$this->record_owners = 
		array()
		;
		
		$this->request = &$this->table->request;
		
		$this->name = $name;
		
		$this->table->renderings++;
		$this->form = $this->table->name
		.	'_'
		.	$this->table->renderings
		;
		
		$this->crypt_key = 
		(	empty
			(	$crypt_key
			)
		&&	!empty
			(	$GLOBALS['page']->crypt_key
			)
		)
		?	$GLOBALS['page']->crypt_key
		:	$crypt_key
		;
		
		$this->replace = array
		(	'table'		=>	array
			(	'table_title_count_sensitive'
			,	'table_title_plural'
			,	'table_title'
			,	'reorder'
			,	'new_record'
			,	'own_record'
			,	'paginator'
			,	'disown_records'
			,	'records_total'
			,	'records_per_page'
			,	'records_on_page'
			,	'owned_subtitle'
			,	'sorted_by'
			,	'filtered_by'
			,	'default_sort'
			,	'clear_filters'
			,	'request_uri'
			)
		,	'header'	=>	array
			(	
			)
		,	'footer'	=>	array
			(	
			)
		,	'records'	=>	array
			(	'view_record'
			,	'edit_record'
			,	'delete_record'
			,	'disown_record'
			,	'row_number'
			,	'toggle'
			,	'front_end_edit'
			,	'inedible'
			,	'nullable'
			)
		,	'columns'	=>	array
			(	
			)
		);
		
		$this->filter_input_default_size = 18;
/*		
		foreach ($this->table->fields as $field_name => &$field):
			$field->size = $GLOBALS['page']->input_sizes['filter'];
		endforeach;
		reset($this->table->fields);
*/		
		
		// ESTABLISH USER PERMISSIONS
		// WHAT USERS OF DIFFERENT ROLES CAN AND CAN'T SEE AND DO WITH THIS TABLE AND ITS RECORDS
		permission::initialize
		(	$this->table->name
		);
		
		// CREATE TABLE TEMPLATE
		//
		// FOR THE MOMENT, NON-DEFAULT TEMPLATES MUST EXIST AS INCLUDABLE PHP FILES IN THE APP WEB ROOT
		// LATER, THE TEMPLATES MAY BECOME DATABASE DRIVEN, SO THEY CAN BE EDITED VIA ADMIN RATHER THAN EXPLICITLY CODED
		$this->template_files_included = 
		$table_template = 
		array()
		;
		
		$check_for_template = array
		(	'default.table_template.php'
		,	$this->table->name
			.	'.table_template.php'
		,	$this->name
			.	'.table_template.php'
		);
		$check_for_template = array_unique
		(	$check_for_template
		);
		
		foreach
			(	$check_for_template as $template	
			):
			if	(	is_file
					(	$GLOBALS['page']->path['admin_file_root']
						.	$template
					)
				):
				include
				(	$GLOBALS['page']->path['admin_file_root']
					.	$template
				);
				$this->template_files_included[] = $template;
			endif;
		endforeach;
		
		$this->values = 
		(	is_array
			(	$values
			)
		)
		?	$values
		:	array()
		;
		$this->hidden_inputees = array();
		
		foreach
			(	$this->values	as	$val_key	=>	$val_val	
			):
			if	(	!empty
					(	$this->table->owners[$val_key]
					)
				):
				$this->hidden_inputees[$this->form.'-'.$val_key] = $val_val;
			else:
				if	(	strstr
						(	$val_key
						,	$this->form
							.	'-'
						)	==	$val_key	
					):
					$this->hidden_inputees[$val_key] = $val_val;
				else:
					$table_template[$val_key] = $val_val;
				endif;
			endif;
		endforeach;
		reset
		(	$this->values
		);
		
		$this->template = $table_template;
		
		if	(	empty
				(	$this->template['pre_tbl']
				)
			):
			$this->template['pre_tbl'] = array();
		endif;
		if	(	!isset
				(	$this->template['pre_tbl']['render']
				)
			):
			$this->template['pre_tbl']['render'] = 1;
		endif;
		if	(	!empty
				(	$this->template['pre_tbl']['render']
				)
			):
			if	(	!isset
					(	$this->template['pre_tbl']['render_if_no_records']
					)
				):
				$this->template['pre_tbl']['render_if_no_records'] = 1;
			endif;
			if	(	!isset
					(	$this->template['pre_tbl']['template']
					)
				):
				$this->template['pre_tbl']['template'] = '<table cellspacing="0" class="table pre_tbl"><tr><th class="table_title">$table_title_plural $owned_subtitle $filtered_by $sorted_by</th><th class="table_buttons"> $reorder $default_sort $clear_filters $new_record</th></table>';
			endif;
		endif;
		
		if	(	!isset
				(	$this->template['table_title']
				)
			):
			$this->template['table_title'] = $this->table->title;
		endif;
		if	(	!isset
				(	$this->template['show_record_count']
				)
			):
			$this->template['show_record_count'] = 0;
		endif;
		if	(	$this->template['show_record_count']
			):
			$this->template['table_title'] = '$records_on_page '
			.	$this->template['table_title']
			;
		endif;
		if	(	!isset
				(	$this->template['table_title_plural']
				)
			):
			$this->template['table_title_plural'] = strings::pluralize
			(	$this->template['table_title']
			);
		endif;
		
		if	(	empty
				(	$this->template['aft_tbl']
				)
			):
			$this->template['aft_tbl'] = array();
		endif;
		if	(	!isset
				(	$this->template['aft_tbl']['render']
				)
			):
			$this->template['aft_tbl']['render'] = 1;
		endif;
		if	(	!empty
				(	$this->template['aft_tbl']['render']
				)
			):
			if	(	!isset
					(	$this->template['aft_tbl']['render_if_no_records']
					)
				):
				$this->template['aft_tbl']['render_if_no_records'] = 1;
			endif;
			if	(	!isset
					(	$this->template['aft_tbl']['own_record']
					)
				):
				$this->template['aft_tbl']['own_record'] = '';
			endif;
			$this->template['own_record'] = $this->template['aft_tbl']['own_record'];
			if	(	!isset
					(	$this->template['aft_tbl']['template']
					)
				):
				$this->template['aft_tbl']['template'] = '<table cellspacing="0" class="table aft_tbl"><tr class="cap_row"><th class="cap" style="vertical-align:top;padding:0px 4px 0px 4px">$paginator$own_record</th><th class="cap" style="vertical-align:top;text-align:right;padding:0px 4px 0px 4px">$disown_records</th></tr></table>';
			endif;
		endif;
		
		$this->template['request_uri'] = $_SERVER['REQUEST_URI'];
		if	(	!isset
				(	$this->template['class']
				)
			):
			$this->template['class'] = 'table';
		endif;
		if	(	!isset
				(	$this->template['cellspacing']
				)
			):
			$this->template['cellspacing'] = 0;
		endif;
		if	(	!isset
				(	$this->template['render_if_no_records']
				)
			):
			$this->template['render_if_no_records'] = 1;
		endif;
		if	(	empty
				(	$this->template['pagination']
				)
			):
			$this->template['pagination'] = array();
		endif;
		if	(	!isset
				(	$this->template['pagination']['items_per_page']
				)
			):
			$this->template['pagination']['items_per_page'] = 0;
		endif;
		if	(	!isset
				(	$this->template['pagination']['links_per_page']
				)
			):
			$this->template['pagination']['links_per_page'] = 5;
		endif;
		
		if	(	!isset
				(	$this->template['reorder']
				)
			):
			if	(	empty
					(	$this->owners
					)
				&&	empty
					(	$this->table->fields['sort_order']
					)
	//	//	//	//	//	//
	//			||	1	//
	//	//	//	//	//	//
				):
				$this->template['reorder'] = '';
			else:
				$reorder_query_string = array
				(	'z'			=>	$this->table->name
				,	'act'		=>	'reorder'
	//			,	'equals'	=>	array()
				);
				
				if	(	!empty
						(	$this->owners
						)
					):
					$reorder_query_string['ownership_id'] = key
					(	$this->owners
					);
					$reorder_query_string['owner_id'] = implode
					(	','
					,	$this->owners[$reorder_query_string['ownership_id']]
					);
				endif;
				
				$this_table_title = $this->table->title;
				
				foreach
					(	$this->hidden_inputees as	$hidden_key	=>	$hidden_val	
					):
	//				$reorder_query_string['equals'][$hidden_key] = $hidden_val;
					if	(	strstr
							(	$hidden_key
							,	'_owner_id'
							)
						):
						
						$owner_table = substr
						(	$hidden_key
						,	strpos
							(	$hidden_key
							,	'-'
							)
							+	1
						,	-	strlen
								(	'_owner_id'
								)
						);
						
						if	(	!empty
								(	$GLOBALS['dbi']->tables[$owner_table]->owns[$this->table->name]['owned_title']
								)
							):
							$this_table_title = $GLOBALS['dbi']->tables[$owner_table]->owns[$this->table->name]['owned_title'];
						endif;
					endif;
				endforeach;
				reset
				(	$this->hidden_inputees
				);
				
				$reorder_link = uri::generate
				(	array
					(	'query'				=>	$reorder_query_string
	//				,	'query_crypt_key'	=>	$GLOBALS['page']->url_crypt_key
					)
				);
				
				$reorder_anchor = xhtml::element
				(	array
					(	'tag_name'	=>	'A'
					,	'attributes'	=>	array
						(	'HREF'		=>	$reorder_link
						)
					,	'content'		=>	'Re-Order' // '.strings::pluralize($this_table_title)
					)
				);
				
				$this->template['reorder'] = xhtml::element
				(	array
					(	'tag_name'	=>	'DIV'
					,	'attributes'	=>	array
						(	'ID'			=>	$this->form.'.reorder'
						,	'CLASS'		=>	'record_button float_right'
						)
					,	'content'		=>	$reorder_anchor
					)
				);
			endif;
		endif;
		
		if	(	permission::evaluate
				(	$this->table->permissions['sort_records_if']
				)
			):
			if	(	!isset
					(	$this->template['default_sort']
					)
				&&	$this->records_total 
				):
				$default_sort_link = uri::generate
				(	array
					(	'query'	=>	array
						(	'z'		=>	$GLOBALS['page']->request['z']
						)
					,	'query_crypt_key'	=>	$GLOBALS['page']->url_crypt_key
					)
				);
				
				$default_sort_anchor = xhtml::element
				(	array
					(	'tag_name'	=>	'A'
					,	'attributes'	=>	array
						(	'HREF'		=>	$default_sort_link
						)
					,	'content'		=>	'All '
						.	$this->table->title_plural
						.	' in default order'
					)
				);
				
				$this->template['default_sort'] = xhtml::element
				(	array
					(	'tag_name'	=>	'DIV'
					,	'attributes'	=>	array
						(	'ID'			=>	$this->form.'.new_record'
						,	'CLASS'		=>	'record_button float_right'
						)
					,	'content'		=>	$default_sort_anchor
					)
				);
				
			endif;
		else:
			$this->template['default_sort'] = '';
		endif;
		
		if	(	permission::evaluate
				(	$this->table->permissions['create_records_if']
				)
			):
			if	(	!isset
					(	$this->template['new_record']
					)
				):
				
				$new_record_query_string = array
				(	'z'		=>	$this->table->name
				,	'id'	=>	0
				);
				
				$this_table_title = $this->table->title;
				
				foreach
					(	$this->hidden_inputees as	$hidden_key	=>	$hidden_val	
					):
					$new_record_query_string[$hidden_key] = $hidden_val;
					$owner_name = substr
					(	$hidden_key
					,	strpos
						(	$hidden_key
						,	'-'
						)
						+	1
					);
					if	(	!empty
							(	$this->table->owners[$owner_name]
							)
						&&	!empty
							(	$GLOBALS['dbi']->ownerships[$this->table->owners[$owner_name]]['owned_title']
							)
						):
						$this_table_title = $GLOBALS['dbi']->ownerships[$this->table->owners[$owner_name]]['owned_title'];
					endif;
				endforeach;
				reset
				(	$this->hidden_inputees
				);
				
				$new_record_link = uri::generate
				(	array
					(	'query'	=>	$new_record_query_string
					,	'query_crypt_key'	=>	$GLOBALS['page']->url_crypt_key
					)
				);
				
				$new_record_anchor = xhtml::element
				(	array
					(	'tag_name'	=>	'A'
					,	'attributes'	=>	array
						(	'HREF'		=>	$new_record_link
						)
					,	'content'		=>	'New '.$this_table_title
					)
				);
				
				$this->template['new_record'] = xhtml::element
				(	array
					(	'tag_name'	=>	'DIV'
					,	'attributes'	=>	array
						(	'ID'			=>	$this->form.'.new_record'
						,	'CLASS'		=>	'record_button float_right'
						)
					,	'content'		=>	$new_record_anchor
					)
				);
				
			endif;
		else:
			$this->template['new_record'] = '';
		endif;
		
		if	(	permission::evaluate
				(	$this->table->permissions['disown_records_if']
				)
			):
			if	(	!empty
					(	$this->owners
					)
				&&	$this->name != $this->table->name
				&&	!isset
					(	$this->template['disown_records']
					)
				):
				
//				$GLOBALS['page']->scripts['src'][] = 'checkbox';
				$GLOBALS['page']->scripts['functions'][] = 'var checking = 0;
function dis_button(wform){
	document.checking = 1;
	var $wbutton = $("#"+wform+"-disown_id_button");
	if	(	any_box_checked(wform+"-disown_id-1")
		){
		$wbutton.show();
	} else {
		$wbutton.hide();		
	}
}';
				
				$disown_records_anchor = xhtml::element
				(	array
					(	'tag_name'	=>	'A'
					,	'attributes'	=>	array
						(	'HREF'			=>	'#'
						,	'onClick'		=>	'if(any_box_checked(\''
							.	$this->form
							.	'-disown_id-1\')){document.'
							.	$this->form
							.	'.act.value=\'disown\';document.'
							.	$this->form
							.	'.submit()};return false'
						,	'id'			=>	$this->form
							.	'-disown_id_button'
						,	'class'			=>	'disown_button'
						)
					,	'content'		=>	'Dissociate Checked '
						.	strings::pluralize
							(	$this->table->title
							)
					)
				);
				
				$this->template['disown_records'] = xhtml::element
				(	array
					(	'tag_name'	=>	'DIV'
					,	'attributes'	=>	array
						(	'ID'			=>	$this->form
							.	'.disown_records'
						,	'CLASS'			=>	'record_button float_right'
						)
					,	'content'		=>	$disown_records_anchor
					)
				);
				
			endif;
		else:
			$this->template['disown_records'] = '';
		endif;
		
		if	(	empty
				(	$this->template['header']
				)
			):
			$this->template['header'] = array();
		endif;
		if	(	!isset
				(	$this->template['header']['render']
				)
			):
			$this->template['header']['render'] = 1;
		endif;
		if	(	!empty
				(	$this->template['header']['render']
				)
			):
			if	(	!isset
					(	$this->template['header']['render_if_no_records']
					)
				):
				$this->template['header']['render_if_no_records'] = 0;
			endif;
			if	(	!isset
					(	$this->template['header']['sorting']
					)
				):
				$this->template['header']['sorting'] = 1;
			endif;
			if	(	!isset
					(	$this->template['header']['filtering']
					)
				||	!is_numeric
					(	$this->template['header']['filtering']
					)
				||	$this->template['header']['filtering'] == 1
				):
				$this->template['header']['filtering'] = $this->filter_input_default_size;
			endif;
			if	(	isset
					(	$this->template['header']['filtering']
					)
				):
				foreach
					(	$this->table->request	as	$trk	=>	$trv
					):
					if	(	strstr
							(	$trk
							,	'-sort'
							)	==	'-sort'
						):
						
						$clear_filters_link = uri::generate
						(	array
							(	'query'	=>	array
								(	'z'		=>	$GLOBALS['page']->request['z']
								)
							,	'query_crypt_key'	=>	$GLOBALS['page']->url_crypt_key
							)
						);
						
						$clear_filters_anchor = xhtml::element
						(	array
							(	'tag_name'	=>	'A'
							,	'attributes'	=>	array
								(	'HREF'		=>	$clear_filters_link
								)
							,	'content'		=>	'Show All '
								.	$this->table->title_plural
							)
						);
						
						$this->template['clear_filters'] = xhtml::element
						(	array
							(	'tag_name'	=>	'DIV'
							,	'attributes'	=>	array
								(	'ID'			=>	$this->form.'.new_record'
								,	'CLASS'		=>	'record_button float_right'
								)
							,	'content'		=>	$clear_filters_anchor
							)
						);						
						
						break;
					endif;
				endforeach;
				reset
				(	$this->table->request
				);
			endif;
			
			if	(	empty
					(	$this->template['header']['row']
					)
				):
				$this->template['header']['row'] = array();
			endif;
			if	(	empty
					(	$this->template['header']['row']['template']
					)
				):
				if	(	empty
						(	$this->template['header']['row']['attributes']
						)
					):
					$this->template['header']['row']['attributes'] = array();
				endif;
				if	(	!isset
						(	$this->template['header']['row']['attributes']['class']
						)
					):
					$this->template['header']['row']['attributes']['class'] = 'cap_row';
				endif;
				if	(	empty
						(	$this->template['header']['row']['attributes']['id']
						)
					):
					$this->template['header']['row']['attributes']['id'] = 'table_'
					.	$this->table->name
					.	'_'
					.	$this->table->renderings
					.	'.tr_0'
					;
				endif;
				$this->template['header']['row']['template'] = xhtml::element
				(	array
					(	'tag_name'	=>	'TR'
					,	'attributes'	=>	$this->template['header']['row']['attributes']
					,	'content'		=>	'$header_cell_templates'
					)
				);
			endif;
			
			if	(	empty
					(	$this->template['header']['cell']
					)
				):
				$this->template['header']['cell'] = array();
			endif;
			if	(	empty
					(	$this->template['header']['cell']['template']
					)
				):
				if	(	empty
						(	$this->template['header']['cell']['attributes']
						)
					):
					$this->template['header']['cell']['attributes'] = array();
				endif;
				if	(	!isset
						(	$this->template['header']['cell']['attributes']['class']
						)
					):
					$this->template['header']['cell']['attributes']['class'] = 'cap';
				endif;
				if	(	empty
						(	$this->template['header']['cell']['attributes']['id']
						)
					):
					$this->template['header']['cell']['attributes']['id'] = 'table_'
					.	$this->table->name
					.	'_'
					.	$this->table->renderings
					.	'.tr_0.th_$column'
					;
				endif;
				$this->template['header']['cell']['attributes']['data-col'] = '$column'; 
				
				$header_cell_template_content = '$name';
				if	(	$this->template['header']['filtering']
					):
					$header_cell_template_content .= '$name_filter';
				endif;
				if	(	$this->template['header']['sorting']
					):
					$header_cell_template_content .= '$name_sort_multi';
					$this->template['header']['cell']['attributes']['onclick'] = '$name_sort_single';
				endif;
				$this->template['header']['cell']['template'] = xhtml::element
				(	array
					(	'tag_name'	=>	'TH'
					,	'attributes'	=>	$this->template['header']['cell']['attributes']
					,	'content'		=>	$header_cell_template_content
					)
				);
			endif;
		endif;
		
		if	(	empty
				(	$this->template['footer']
				)
			):
			$this->template['footer'] = array();
		endif;
		if	(	!isset
				(	$this->template['footer']['render']
				)
			):
			$this->template['footer']['render'] = 1;
		endif;
		if	(	!empty
				(	$this->template['footer']['render']
				)
			):
			if	(	!isset
					(	$this->template['footer']['render_if_no_records']
					)
				):
				$this->template['footer']['render_if_no_records'] = 0;
			endif;
			if	(	!isset
					(	$this->template['footer']['template']
					)
				):
				$this->template['footer']['template'] = '';
			endif;
		endif;
		
		if	(	empty
				(	$this->template['records']
				)
			):
			$this->template['records'] = array();
		endif;
		if	(	!isset
				(	$this->template['records']['render']
				)			
			):
			$this->template['records']['render'] = 1;
		endif;
		if	(	!empty
				(	$this->template['records']['render']
				)
			):
			if	(	!isset
					(	$this->template['records']['number_rows_by_id']
					)
				):
				$this->template['records']['number_rows_by_id'] = 0;
			endif;
			if	(	!isset
					(	$this->template['records']['toggle']
					)
				):
				$this->template['records']['toggle'] = 1;
			endif;
			if	(	!isset
					(	$this->template['records']['show_empty_values']
					)
				):
				$this->template['records']['show_empty_values'] = 1;
			endif;
			if	(	empty
					(	$this->template['records']['default_masks']
					)
				):
				$this->template['records']['default_masks'] = array();
			endif;
			if	(	!isset
					(	$this->template['records']['default_masks']['datetime']
					)
				):
				$this->template['records']['default_masks']['datetime'] = '';//'$Y$-$m$-$d$ $H$:$i$:$s$';
			endif;
/*
			if	(	!isset
					(	$this->template['records']['default_masks']['trim']
					)
				):
				$this->template['records']['default_masks']['trim'] = 100;
			endif;
*/		

			if	(	in_array
					(	$this->table->name
					,	array
						(	'player'
						,	'team'
						,	'tournament'
						)
					)
				):
				$riot_admin_redirect_link = uri::generate
				(	array
					(	'query'	=>	array
						(	'z'		=>	'riot_admin_redirect'
						,	'table'	=>	$this->table->name
						,	'id'	=>	'$id'
						)
					,	'query_crypt_key'	=>	$GLOBALS['page']->url_crypt_key
					)
				);
						
				$riot_admin_redirect_anchor = xhtml::element
				(	array
					(	'tag_name'	=>	'A'
					,	'attributes'	=>	array
						(	'HREF'		=>	'#'
						,	'ONCLICK'	=>	'window.open(\''
							.	$riot_admin_redirect_link
							.	'\',\'riot_admin\');return false;'
						)
					,	'content'		=>	'Site'
					)
				);

				$this->template['records']['front_end_edit'] = xhtml::element
				(	array
					(	'tag_name'	=>	'DIV'
					,	'attributes'	=>	array
						(	'ID'			=>	$this->form
							.	'.riot_admin_redirect_$id'
						,	'CLASS'		=>	'record_button float_left'
						)
					,	'content'		=>	$riot_admin_redirect_anchor
					)
				);
			endif;

			if	(	permission::evaluate
					(	$this->table->permissions['view_records_if']
					)
				):
				if	(	!isset
						(	$this->template['records']['view_record']
						)
					):
					
					$view_record_link = uri::generate
					(	array
						(	'query'	=>	array
							(	'z'		=>	$this->table->name
							,	'id'		=>	'$id'
							)
						,	'query_crypt_key'	=>	$GLOBALS['page']->url_crypt_key
						)
					);
					
					$view_record_anchor = xhtml::element
					(	array
						(	'tag_name'	=>	'A'
						,	'attributes'	=>	array
							(	'HREF'		=>	$view_record_link
							)
						,	'content'		=>	'View '
							.	$this->table->title
						)
					);
					
					$this->template['records']['view_record'] = xhtml::element
					(	array
						(	'tag_name'	=>	'DIV'
						,	'attributes'	=>	array
							(	'ID'			=>	$this->form
								.	'.view_record'
							,	'CLASS'		=>	'record_button float_right'
							)
						,	'content'		=>	$view_record_anchor
						)
					);
					
				endif;
			else:
				$this->template['records']['view_record'] = '';
			endif;
			
			if	(	permission::evaluate
					(	$this->table->permissions['edit_records_if']
					)
				):
				if	(	!isset
						(	$this->template['records']['edit_record']
						)
					):
					
					$edit_record_link = uri::generate
					(	array
						(	'query'	=>	array
							(	'z'		=>	$this->table->name
							,	'id'	=>	'$id'
							,	'act'	=>	'edit'
							)
						,	'query_crypt_key'	=>	$GLOBALS['page']->url_crypt_key
						)
					);
					
					$edit_record_anchor = xhtml::element
					(	array
						(	'tag_name'	=>	'A'
						,	'attributes'	=>	array
							(	'HREF'		=>	$edit_record_link
							)
						,	'content'		=>	'Edit'
//							.	' '
//							.	$this->table->title
						)
					);
					
					$this->template['records']['edit_record'] = xhtml::element
					(	array
						(	'tag_name'	=>	'DIV'
						,	'attributes'	=>	array
							(	'ID'			=>	$this->form
								.	'.edit_record'
							,	'CLASS'			=>	'record_button float_left'
							)
						,	'content'		=>	$edit_record_anchor
						)
					);
					
				endif;
			else:
				$this->template['records']['edit_record'] = '';
			endif;
			
			if	(	permission::evaluate
					(	$this->table->permissions['delete_records_if']
					)
				):
				if	(	!isset
						(	$this->template['records']['delete_record']
						)
					):
					
					$delete_record_link = uri::generate
					(	array
						(	'query'	=>	array
							(	'z'		=>	$this->table->name
							,	'id'	=>	'$id'
							,	'act'	=>	'delete'
							)
						,	'query_crypt_key'	=>	$GLOBALS['page']->url_crypt_key
						)
					);
					
					$delete_record_anchor = xhtml::element
					(	array
						(	'tag_name'	=>	'A'
						,	'attributes'	=>	array
							(	'HREF'			=>	$delete_record_link
							,	'onClick'		=>	'return confirm(\'Are you sure you want to permanently delete this '
								.	$this->table->title
								.	'?\');'
							)
						,	'content'		=>	'Delete '
							.	$this->table->title
						)
					);
					
					$this->template['records']['delete_record'] = xhtml::element
					(	array
						(	'tag_name'	=>	'DIV'
						,	'attributes'	=>	array
							(	'ID'			=>	$this->form
								.	'.delete_record'
							,	'CLASS'			=>	'record_button float_right'
							)
						,	'content'		=>	$delete_record_anchor
						)
					);
					
				endif;
			else:
				$this->template['records']['delete_record'] = '';
			endif;
			
			if	(	!empty
					(	$this->owners
					)
				&&	!isset
					(	$this->template['records']['disown_record']
					)
				):
				
				$this->template['records']['disown_record'] = xhtml::checkbox_element
				(	array
					(	'option_number'		=>	'$row_number'
					,	'option_value'		=>	'$id'
					,	'option_content'	=>	''
					,	'attributes'		=>	array
						(	'name'				=>	$this->form
							.	'-disown_id'
						,	'onclick'			=>	'dis_button(\''
							.	$this->form
							.	'\');'
						,	'onblur'			=>	'document.checking=0;'
						)
					)
				);
			endif;
			
			if	(	empty
					(	$this->template['records']['row']
					)
				):
				$this->template['records']['row'] = array();
			endif;
			if	(	empty
					(	$this->template['records']['row']['onclick_query_vars']
					)
				):
				$this->template['records']['row']['onclick_query_vars'] = array();
			endif;
			if	(	!isset
					(	$this->template['records']['row']['onclick_query_vars']['z']
					)
				):
				$this->template['records']['row']['onclick_query_vars']['z'] = $this->table->name;
			endif;
			if	(	!isset
					(	$this->template['records']['row']['onclick_query_vars']['id']
					)
				):
				$this->template['records']['row']['onclick_query_vars']['id'] = '';
			endif;
			if	(	empty
					(	$this->template['records']['row']['template']
					)
				):
				if	(	empty
						(	$this->template['records']['row']['attributes']
						)
					):
					$this->template['records']['row']['attributes'] = array();
				endif;
				if	(	!isset
						(	$this->template['records']['row']['attributes']['class']
						)
					):
					$this->template['records']['row']['attributes']['class'] = 'cel_row';
				endif;
/*				
				$row_mouseover = 'this.className=\''
				.	$this->template['records']['row']['attributes']['class']
				.	' '
				.	$this->template['records']['row']['attributes']['class']
				.	'_hover\''
				;
				$this->template['records']['row']['attributes']['onmouseover'] = 
				(	empty
					(	$this->template['records']['row']['attributes']['onmouseover']
					)
				)
				?	$row_mouseover
				:	$row_mouseover
					.	';'
					.	$this->template['records']['row']['attributes']['onmouseover']
				;
*/
				if	(	$this->template['records']['toggle']
					&&	!strstr
						(	$this->template['records']['row']['attributes']['class']
						,	'$toggle'
						)
					):
					$this->template['records']['row']['attributes']['class'] .= ' '
					.	$this->template['records']['row']['attributes']['class']
					.	'_$toggle'
					;
				endif;
/*
				$row_mouseout = 'this.className=\''
				.	$this->template['records']['row']['attributes']['class']
				.	'\''
				;
				$this->template['records']['row']['attributes']['onmouseout'] = 
				(	empty
					(	$this->template['records']['row']['attributes']['onmouseout']
					)
				)
				?	$row_mouseout
				:	$row_mouseout
					.	';'
					.	$this->template['records']['row']['attributes']['onmouseout']
				;
*/
				if	(	empty
						(	$this->template['records']['row']['attributes']['id']
						)
					):
					$this->template['records']['row']['attributes']['id'] = 'table_'
					.	$this->table->name
					.	'_'
					.	$this->table->renderings
					.	'.tr_$row_number'
					;
				endif;
				$this->template['records']['row']['attributes']['data-id'] = '$id';
				if	(	$GLOBALS['user']->is_admin()
					):
					if	(	!isset
							(	$this->template['records']['row']['attributes']['onclick']
							)
						):
						$this->template['records']['row']['attributes']['onclick'] = 'if(!document.checking)window.location=\'$data_row_onclick_href\'';
						$this->template['records']['row']['attributes']['class'] .= ' admin';
					endif;
					if	(	!isset
							(	$this->template['records']['row']['attributes']['title']
							)
						):						
						$this->template['records']['row']['attributes']['title'] = 'Click on the row to view full '
						.	$this->table->name
						.	' details...'
						;
						$this->template['records']['row']['attributes']['title'] = $this->table->title
						.	' $id'
						;
					endif;
				endif;
				$this->template['records']['row']['template'] = xhtml::element
				(	array
					(	'tag_name'	=>	'TR'
					,	'attributes'	=>	$this->template['records']['row']['attributes']
					,	'content'		=>	'$data_cell_templates'
					)
				);
			endif;
			
			if	(	empty
					(	$this->template['records']['cell']
					)
				):
				$this->template['records']['cell'] = array();
			endif;
			if	(	empty
					(	$this->template['records']['cell']['template']
					)
				):
				if	(	empty
						(	$this->template['records']['cell']['attributes']
						)
					):
					$this->template['records']['cell']['attributes'] = array();
				endif;
				if	(	!isset
						(	$this->template['records']['cell']['attributes']['class']
						)
					):
					$this->template['records']['cell']['attributes']['class'] = 'cel col_$column';
				endif;
				if	(	empty
						(	$this->template['records']['cell']['attributes']['id']
						)
					):
					$this->template['records']['cell']['attributes']['id'] = 'table_'
					.	$this->table->name
					.	'_'
					.	$this->table->renderings
					.	'.tr_$row_number.td_$column'
					;
				endif;
				$this->template['records']['cell']['attributes']['data-col'] = '$column'; 
				$this->template['records']['cell']['attributes']['data-inedible'] = '$inedible'; 
				$this->template['records']['cell']['attributes']['data-nullable'] = '$nullable'; 
				$this->template['records']['cell']['template'] = xhtml::element
				(	array
					(	'tag_name'	=>	'TD'
					,	'attributes'	=>	$this->template['records']['cell']['attributes']
					,	'content'		=>	'$name'
					)
				);
			endif;
		endif;
		
		if	(	empty
				(	$this->template['columns']
				)
			):
			$this->template['columns'] = array();
			foreach
				(	$this->table->fields as $field_name => &$field	
				):
				if	(	!in_array
						(	$field_name
						,	$this->table->permissions['hide_columns_from_table']
						)
					&&	!$field->hide_from_table
					&&	!in_array
						(	$field_name
						,	$GLOBALS['dbi']->fields_hidden_from_table
						)
					):
					$filtering = 
					$sorting = 
					0
					;
					if 	(	$field_name == 'id'
						):
						$this->template['columns'][] = array
						(	'name'		=>	'edit_record'
						,	'title'		=>	''
						,	'template'	=>	'$edit_record'
						,	'filter'	=>	0
						,	'sort'		=>	0
						);
					else:
						if	(	$this->template['header']['render']
							&&	!$field->encrypt
							):
							if	(	$this->template['header']['sorting']
								&&	permission::evaluate
									(	$this->table->permissions['sort_records_if']
									)
								):
								$sorting = array
								(	'priority'	=>	$field->sort_priority
								,	'direction'	=>	$field->sort_direction
								);
							endif;
							if	(	$this->template['header']['filtering']
								&&	permission::evaluate
									(	$this->table->permissions['filter_records_if']
									)
								):
								$filtering = $this->template['header']['filtering'];
							endif;
						endif;
						
						$this->template['columns'][] = array
						(	'field'			=>	$field_name
						,	'name'			=>	$field_name
						,	'title'			=>	$field->title
						,	'blurb'			=>	$field->view_blurb
						,	'filter'		=>	$filtering
						,	'sort'			=>	$sorting
						,	'show_empty_values'	=>	$this->template['records']['show_empty_values']
						,	'template'		=>	$field->template
							(	array
								(	'form'	=>	$this->form
								,	'type'	=>	'table'
								)
							)
						);
					endif;
				endif;
			endforeach;
			reset
			(	$this->table->fields
			);
		endif;
		
		$skip_columns = array();
		foreach
			(	$this->template['columns']	as	$column	=>	&$this->column
			):
/*		
			if	(	!empty($this->column['field'])
				&&	empty($this->record[$this->column['field']])
				):
				if	(	$this->column['field'] == 'title'
					&&	!empty($this->record['name'])
					):
					$this->column['field'] = 'name';
				endif;
				if	(	$this->column['field'] == 'name'
					&&	!empty($this->record['title'])
					):
					$this->column['field'] = 'title';
				endif;
			endif;
*/
			if	(	!isset
					(	$this->column['name']
					)
				):
				if	(	!empty
						(	$this->column['button']
						)
					):
					$this->column['name'] = $this->column['button'];
				else:
					if	(	!empty
							(	$this->column['field']
							)
						):
						$this->column['name'] = $this->column['field'];
					else:
						$this->column['name'] = '';
					endif;
				endif;
			endif;
			if	(	!isset
					(	$this->column['title']
					)
				):
				if	(	!empty
						(	$this->column['field']
						)
					&&	!empty
						(	$this->table->fields[$this->column['field']]->title
						)
					):
					$this->column['title'] = $this->table->fields[$this->column['field']]->title;
				else:
					if	(	!empty
							(	$this->column['name']
							)
						):
						$this->column['title'] = strings::label
						(	$this->column['name']
						);
					else:
						$this->column['title'] = '';
					endif;
				endif;
			endif;
			
			if	(	!empty
					(	$this->table->owners[$this->column['name']]
					)
				&&	!empty
					(	$this->owners[$this->table->owners[$this->column['name']]]
					)
				):
				$skip_columns[] = $column;
			endif;
		endforeach;
		reset
		(	$this->template['columns']
		);
		
		$remove_skipped_columns = $this->template['columns'];
		$this->template['columns'] = 
		$this->columns_by_key = 
		array()
		;
		foreach
			(	$remove_skipped_columns	as	$column	=>	$template_column
			):
			if	(	!in_array
					(	$column
					,	$skip_columns
					)
				):
				$this->template['columns'][] = $template_column;
				$this->columns_by_key[] = $template_column['name'];
			endif;
		endforeach;
		$this->columns_by_key = arrays::sort_by_strlen
		(	array
			(	'array'			=>	$this->columns_by_key
			,	'sort_by_key'	=>	0
			,	'reverse'		=>	1
			)
		);
		
		foreach
			(	$this->template['columns']	as	$column	=>	&$this->column
			):
			if	(	!isset
					(	$this->column['blurb']
					)
				):
				$this->column['blurb'] =
				(	!empty
					(	$this->column['field']
					)
				&&	!empty
					(	$this->table->fields[$this->column['field']]
					)
				&&	!empty
					(	$this->table->fields[$this->column['field']]->blurb
					)
				)
				?	$this->table->fields[$this->column['field']]->blurb
				:	''
				;
			endif;
			if	(	!isset
					(	$this->column['show_empty_values']
					)
				):
				$this->column['show_empty_values'] = $this->template['records']['show_empty_values'];
			endif;
			
			if	(	empty
					(	$this->column['masks']
					)
				):
				$this->column['masks'] = array();
			endif;
			foreach
				(	$this->template['records']['default_masks'] as $mask_name => $mask
				):
				if	(	!isset
						(	$this->column['masks'][$mask_name]
						)
					):
					$this->column['masks'][$mask_name] = $mask;
				endif;
			endforeach;
			if	(	!empty
					(	$this->column['field']
					)
				&&	!empty
					(	$this->table->fields[$this->column['field']]
					)
				):
				$this->table->fields[$this->column['field']]->masks = $this->column['masks'];
			endif;
			
			$filtering = 
			$sorting = 
			0
			;
			
			if	(	$this->template['header']['render']
				&&	!empty
					(	$this->column['field']
					)
				&&	!empty
					(	$this->table->fields[$this->column['field']]
					)
				&&	!$this->table->fields[$this->column['field']]->encrypt
				):
				if	(	$this->template['header']['sorting']
					&&	permission::evaluate
						(	$this->table->permissions['sort_records_if']
						)
					):
					$sorting = $this->template['header']['sorting'];
				endif;
				if	(	$this->template['header']['filtering']
					&&	permission::evaluate
						(	$this->table->permissions['filter_records_if']
						)
					):
					$filtering = $this->template['header']['filtering'];
				endif;
			endif;
			if	(	permission::evaluate
					(	$this->table->permissions['filter_records_if']
					)
				):
				if	(	!isset
						(	$this->column['filter']
						)
					):
					$this->column['filter'] = 
					(	debug::get_mode()	
					)
					?	$this->filter_input_default_size
					:	$filtering
					;
				endif;
			else:
				$this->column['filter'] = 0;
			endif;
			if	(	permission::evaluate
					(	$this->table->permissions['sort_records_if']
					)
				):
				if	(	!isset
						(	$this->column['sort']
						)
					):
					$this->column['sort'] = 
					(	debug::get_mode()	
					||	$sorting
					)
					?	array
						(	'priority'	=>	0
						,	'direction'	=>	''
						)
					:	0
					;
					if	(	empty
							(	$this->table->request[$this->column['name'].'-sort']
							)
						):
						if	(	!empty
								(	$this->table->fields[$this->column['field']]
								)
							):
							$this->column['sort']['priority'] = $this->table->fields[$this->column['field']]->sort_priority;
							$this->column['sort']['direction'] = $this->table->fields[$this->column['field']]->sort_direction;
						endif;
					else:
						$this->column['sort']['priority'] = $this->table->request[$this->column['name'].'-sort'];
						$this->column['sort']['direction'] = $this->table->request[$this->column['name'].'-dir'];
					endif;
				endif;
			else:
				$this->column['sort'] = 0;
			endif;
			
			if	(	!empty
					(	$this->column['attributes']
					)
				):
				if	(	empty
						(	$this->column['enclosure']
						)
					):
					$this->column['enclosure'] = 'DIV';
				endif;
			endif;
			
			if	(	empty
					(	$this->column['field']
					)
				||	empty
					(	$this->table->fields[$this->column['field']]
					)
				):
				if	(	!empty
						(	$this->table->owners[$this->column['name']]
						)
					):
					$this->column['sort'] = 0;
					
					$ownership_id = $this->table->owners[$this->column['name']];
					
					$this->record_owners[$ownership_id] = $GLOBALS['dbi']->get_result_array
					(	array
						(	'table'		=>	$GLOBALS['dbi']->ownerships[$ownership_id]['owner_table']
						)
					);
					
					$this->column['title'] = 
					(	strstr
						(	$GLOBALS['dbi']->ownerships[$ownership_id]['owner_title']
						,	'Associated '
						)
						==	$GLOBALS['dbi']->ownerships[$ownership_id]['owner_title']
					)
					?	strings::label
						(	$GLOBALS['dbi']->ownerships[$ownership_id]['owner_table']
						)
					:	$GLOBALS['dbi']->ownerships[$ownership_id]['owner_title']
					;
					
					$owner = new field
					(	array
						(	'fetch_field_object'	=>	array
							(	'name'			=>	$this->column['name']
							,	'title'			=>	$this->column['title']
							,	'title_plural'	=>	strings::pluralize
								(	$this->column['title']
								)
							,	'table'			=>	$this->table->name
							,	'max_length'	=>	0
							,	'type'			=>	'INTEGER'
							,	'type_string'	=>	'int(11)'
							,	'encrypt'		=>	0
							,	'edit_allowed'	=>	1
							,	'null_allowed'	=>	1
							,	'default_value'	=>	0
							,	'value'			=>	''
							,	'create_blurb'	=>	''
							,	'edit_blurb'	=>	''
							,	'view_blurb'	=>	''
							,	'foreign_table'	=>	$GLOBALS['dbi']->ownerships[$ownership_id]['owner_table']
							)
						)
					);
					
					$this->column['template'] = $owner->template
					(	array
						(	'type'	=>	'table'
						,	'form'	=>	$this->form
						)
					);
				else:
					$this->column['template'] = new field_template
					(	array
						(	'type'		=>	'table'
						,	'name'		=>	$this->column['name']
						,	'form'		=>	$this->form
						,	'template'	=>	$this->column['template']
						)
					);
				endif;
			else:
				$this->column['template'] = $this->table->fields[$this->column['field']]->template
				(	array
					(	'form'	=>	$this->form
					,	'type'	=>	'table'
					)
				);
			endif;
			
		endforeach;
		reset
		(	$this->template['columns']
		);
		
		if	(	$this->template['header']['render']
			&&	strstr
				(	$this->template['header']['row']['template']
				,	'$header_cell_templates'
				)
			):
			$header_cell_templates = '';
			foreach
				(	$this->template['columns']	as	$column	=>	&$this->column
				):
				$header_cell_template = $this->template['header']['cell']['template'];
				$header_cell_template = str_replace
				(	'$column'
				,	$column
				,	$header_cell_template
				);
				$header_cell_template = str_replace
				(	'$name'
				,	'$'
					.	$this->column['name']
				,	$header_cell_template
				);
				$header_cell_templates .= $header_cell_template;
			endforeach;
			reset
			(	$this->template['columns']
			);
			
			if	(	!empty
					(	$this->template['disown_records']
					)
				):
				$column++;
				$header_cell_template = $this->template['header']['cell']['template'];
				$header_cell_template = str_replace
				(	'$column'
				,	$column
				,	$header_cell_template
				);
				$get_rid_of = array
				(	'name'
				,	'name_sort'
				,	'name_filter'
				);
				foreach
					(	$get_rid_of	as	$get_rid_of_this	
					):
					$header_cell_template = str_replace
					(	'$'
						.	$get_rid_of_this
					,	''
					,	$header_cell_template
					);
				endforeach;
				reset
				(	$get_rid_of
				);
				$header_cell_templates .= $header_cell_template;
			endif;
			
			$this->template['header']['row']['template'] = str_replace
			(	'$header_cell_templates'
			,	$header_cell_templates
			,	$this->template['header']['row']['template']
			);
		endif;
		
		if	(	$this->template['records']['render']
			&&	strstr
				(	$this->template['records']['row']['template']
				,	'$data_cell_templates'
				)
			):
			$data_cell_templates = '';
			foreach
				(	$this->template['columns']	as	$column	=>	&$this->column	
				):
				$data_cell_template = $this->template['records']['cell']['template'];
				$data_cell_template = str_replace
				(	'$column'
				,	$column
				,	$data_cell_template
				);

				$data_cell_template = str_replace
				(	'$inedible'
				,	(	(	!empty
							(	$this->column['field']
							)
						&&	!empty
							(	$this->column['template']->field->edit_allowed
							)
/*							
						&&	empty
							(	$this->column['template']->field->foreign_table
							)
*/
						)
						?	0
						:	1
					)
				,	$data_cell_template
				);
				$data_cell_template = str_replace
				(	'$nullable'
				,	(	(	!empty
							(	$this->column['field']
							)
						&&	!empty
							(	$this->column['template']->field->null_allowed
							)
						)
						?	1
						:	0
					)
				,	$data_cell_template
				);

				$data_cell_template = str_replace
				(	'$name'
				,	'$'
					.	$this->column['name']
				,	$data_cell_template
				);
				$data_cell_templates .= $data_cell_template;
			endforeach;
			reset
			(	$this->template['columns']
			);
			
			if	(	!empty
					(	$this->template['disown_records']
					)
				):
				$column++;
				$data_cell_template = $this->template['records']['cell']['template'];
				$data_cell_template = str_replace
				(	'$column'
				,	$column
				,	$data_cell_template
				);
				$data_cell_template = str_replace
				(	'$name'
				,	'$disown_record'
				,	$data_cell_template
				);
				$data_cell_templates .= $data_cell_template;
			endif;
			
			$this->template['records']['row']['template'] = str_replace
			(	'$data_cell_templates'
			,	$data_cell_templates
			,	$this->template['records']['row']['template']
			);
		endif;
		// END TABLE TEMPLATE CREATION
	}
	
	function own_records()
	{	$existing_owners = 
		$invalid_ownees = 
		array()
		;
		$inner_owners = $this->owners;

		foreach
			(	$this->owners	as	$ownership_id	=>	$owners	
			):
			$ownership_info = $GLOBALS['dbi']->ownerships[$ownership_id];

			if	(	empty
					(	$GLOBALS['dbi']->tables[$ownership_info['owner_table']]->owners
					)
				&&	!empty
					(	$GLOBALS['user']->owners[$ownership_id]
					)
				):
				
				foreach
					(	$inner_owners	as	$inner_ownership_id	=>	$inner_owner
					):
					foreach
						(	$inner_owner	as	$inner_owner_id	=>	$inner_owner_info
						):
						$existing_owners[$inner_ownership_id][$inner_owner_id] = $inner_owner_info;
					endforeach;
				endforeach;
				reset
				(	$inner_owners
				);
				
				/*
				// CAN'T DO THE FOLLOWING WITH NUMERIC ARRAY KEYS // FUCK
				$existing_owners = array_merge_recursive
				(	$existing_owners
				,	$this->owners
				);
				*/
			else:
				foreach
					(	$GLOBALS['dbi']->tables[$ownership_info['owner_table']]->owners	as	$owner_ownership_name	=>	$owner_ownership_id	
					):
//					$owner_ownership_info = $GLOBALS['dbi']->ownerships[$owner_ownership_id];
					if	(	!empty
							(	$this->table->owners[$owner_ownership_id]
							)
						&&	!empty
							(	$GLOBALS['dbi']->tables[$ownership_info['owner_table']]->record->owners[$owner_ownership_id]
							)
						):
						$existing_owners[$owner_ownership_id] = 
						(	!empty
							(	$existing_owners[$owner_ownership_id]
							)
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
			reset
			(	$GLOBALS['dbi']->tables[$ownership_info['owner_table']]->owners
			);

			// CHECK FOR OWNED CONSTRAINT LIMITS
			if	(	!empty
					(	$ownership_info['owners_allowed']
					)
				):
				foreach
					(	$owners	as	$owner_id
					):
					$already_owned = $GLOBALS['dbi']->get_result_array
					(	array
						(	'table'				=>	'owned'
						,	'fields'			=>	array
							(	'owned_id'
							)
						,	'equals'			=>	array
							(	'ownership_id'		=>	$ownership_id
							)
						)
					);
					$already_owned_counts = array_count_values
					(	$already_owned
					);
					foreach
						(	$already_owned_counts	as	$already_owned_id	=>	$already_owned_count
						):
						if	(	$already_owned_count >=	$ownership_info['owners_allowed']
							):
							if	(	empty
									(	$invalid_ownees[$owner_id]
									)
								):
								$invalid_ownees[$owner_id] = array();
							endif;
							$invalid_ownees[$owner_id][] = $already_owned_id;
						endif;
					endforeach;
				endforeach;
			endif;					
			
		endforeach;
		reset
		(	$this->owners
		);
// NEW KLUDGE FOR CLIENT EXTRANET... PROBABLY APPLICABLE IN WIDER SITE CASES
// LIMITS SELECTION OF ASSOCIABLE RECORDS ACCORDING TO USER PRIVILEGES / OWNERSHIPS
		foreach
			(	$GLOBALS['user']->owners	as	$ownership_id	=>	$user_owner
			):
			if	(	$ownership_id	!=	2
				):
				if	(	empty
						(	$existing_owners[$ownership_id]
						)
					):
					$existing_owners[$ownership_id] = array();
				endif;
				foreach
					(	$user_owner	as	$user_owner_id	=>	$user_owner_ray
					):
					$existing_owners[$ownership_id][] = $user_owner_id;
				endforeach;
			endif;
		endforeach;
		reset
		(	$GLOBALS['user']->owners
		);
// END NEW KLUDGE

		$ownable_records = $this->table->select_records
		(	array
			(	'owners'	=>	$existing_owners
			,	'fields'	=>	0
			)
		);
		
		if	(	!empty
				(	$GLOBALS['page']->request['id']
				)
			&&	!empty
				(	$invalid_ownees[$GLOBALS['page']->request['id']]
				)
			):
			foreach
				(	$invalid_ownees[$GLOBALS['page']->request['id']]	as	$invalid_ownee
				):
				unset
				(	$ownable_records[$invalid_ownee]
				);
			endforeach;
		endif;
/*
// MOMENTARY KLUDGERY TO PREPEND OWNER OWNER TITLES TO OPTION TEXT FOR CERTAIN TABLES
// UNSURE AS OF NOW HOW TO GET THIS INFO INTO THIS FUNCTION IN A MORE MODULAR FASHION
		$owner_labels = array
		(	//'project'	=>	'campaign'
		);
		$owner_owners = array();
		if	(	!empty
				(	$owner_labels[$this->table->name]
				)
			):
			$owner_owners = $GLOBALS['dbi']->get_result_array
			(	array
				(	'table'				=>	$owner_labels[$this->table->name]
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
		if	(	!empty
				(	$ownable_records
				)
			&&	count
				(	$this->records	
				)	<	$ownership_info['owned_allowed']
			):
			$options = array
			(	
			);		
			foreach	
				(	$ownable_records	as	$ownable_id	=>	$ownable_info	
				):
				if	(	empty
						(	$this->records[$ownable_id]
						)
					):
					if	(	empty
						 	(	$owner_owners
							)
						):
						$options[$ownable_id] = $this->table->render_record_title
						(	$ownable_info
						);
					else:
						$options[$ownable_id] = $owner_owners[array_pop
						(	$GLOBALS['dbi']->get_owner_records
							(	array
								(	'owned'				=>	array
									(	$GLOBALS['dbi']->tables[$this->table->name]->owners[$owner_labels[$this->table->name]]	=>	array
										(	$ownable_id
										)
									)
								)
							)
						)]
						.	' &gt; '
						.	$this->table->render_record_title
							(	$ownable_info
							);
						;
					endif;
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
			
			natcasesort
			(	$options
			);
			
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
/*		
// THE "ASSOCIATE ALL" FUNCTIONALITY HAS BEEN DEEMED TOO DANGEROUS/IRREVERSIBLE FOR EXTRANET USABILITY	
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
*/			
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
	}

	function render()
	{	if	(	permission::evaluate
				(	$this->table->permissions['view_table_if']
				)
			&&	(	count
					(	$this->records
					)
				||	$this->template['render_if_no_records']
				)
			):
			
			$hidden_inputees = $this->hidden_inputees;
			$pre_tbl = 
			$table = 
			$tbody = 
			''
			;
			
			if	(	$this->template['pre_tbl']['render']
				&&	(	count
						(	$this->records
						)
					||	(	$this->template['render_if_no_records']
						&&	$this->template['pre_tbl']['render_if_no_records']
						)
					)
				):
				// PRE-TABLE TEMPLATE
				$pre_tbl .= $this->template['pre_tbl']['template'];
			endif;
			
			// TABLE HEADER TEMPLATE
			$thead = 
			(	$this->template['header']['render']
			&&	(	$this->records_total
				||	(	$this->template['render_if_no_records']
					&&	$this->template['header']['render_if_no_records']
					)
				)
			)
			?	$this->render_header_row()
			:	''
			;
			
			// TABLE FOOTER TEMPLATE
			$tfoot = 
			(	$this->template['footer']['render']
			&&	(	count
					(	$this->records
					)
				||	(	$this->template['render_if_no_records']
					&&	$this->template['footer']['render_if_no_records']
					)
				)
			)
			?	$this->render_footer_row()
			:	''
			;
			
			// TABLE ROW TEMPLATE
			$this->template['records']['toggle'] = 0;
			$this->template['records_total'] = $this->records_total;
			$this->template['records_per_page'] = count
			(	$this->records
			);
			if	(	empty
					(	$this->template['records_total']
					)
				):
				$this->template['records_on_page'] = '';
			else:
				$this->template['records_on_page'] = 
				(	$this->template['records_per_page'] != $this->template['records_total']
				)
				?	$this->template['records_per_page']
					.	' <span style="text-transform:lowercase">of</span> '
					.	$this->template['records_total']
				:	$this->template['records_total']
				;
			endif;
			
			$this->template['table_title_count_sensitive'] = 
			(	$this->template['records_total'] == 1
			)
			?	$this->template['table_title']
			:	$this->template['table_title_plural']
			;
			if	(	permission::evaluate
					(	$this->table->permissions['view_records_if']
					)
				):
				foreach
					(	$this->records as $record	
					):
					$this->record = $record;
					if	(	(	!empty
								(	$this->record['status']
								)
							&&	$this->record['status'] == 'Hidden'
							)
						):
						$this->template['records']['toggle'] .= ' really_hidden_row';
					endif;
					$tbody .= $this->render_data_row();
					$this->template['records']['toggle'] = substr
					(	$this->template['records']['toggle']
					,	0
					,	1
					);
					$this->template['records']['toggle'] = 
					(	$this->template['records']['toggle']
					)
					?	0
					:	1
					;
				endforeach;
				reset
				(	$this->records
				);
			endif;
			
			if	(	!empty
					(	$thead
					)
				||	!empty
					(	$tfoot
					)
				):
				$thead = xhtml::element
				(	array
					(	'tag_name'	=>	'THEAD'
					,	'attributes'	=>	array
						(	'CLASS'		=>	'table_head'
						,	'ID'			=>	'table_'
							.	$this->table->name
							.	'_'
							.	$this->table->renderings
							.	'.thead'
						)
					,	'content'		=>	$thead
					)
				);
				
				$tfoot = xhtml::element
				(	array
					(	'tag_name'	=>	'TFOOT'
					,	'attributes'	=>	array
						(	'CLASS'		=>	'table_foot'
						,	'ID'			=>	'table_'
							.	$this->table->name
							.	'_'
							.	$this->table->renderings
							.	'.tfoot'
						)
					,	'content'		=>	$tfoot
					)
				);
				
				$tbody = xhtml::element
				(	array
					(	'tag_name'	=>	'TBODY'
					,	'attributes'	=>	array
						(	'CLASS'		=>	'table_body'
						,	'ID'			=>	'table_'
							.	$this->table->name
							.	'_'
							.	$this->table->renderings
							.	'.tbody'
						)
					,	'content'		=>	$tbody
					)
				);
			endif;
			
			$table .= xhtml::element
			(	array
				(	'tag_name'	=>	'TABLE'
				,	'attributes'	=>	array
					(	'CLASS'		=>	$this->template['class']
						.	' big_tbl'
					,	'ID'			=>	'table_'
						.	$this->table->name
						.	'_'
						.	$this->table->renderings
					,	'CELLSPACING'	=>	$this->template['cellspacing']
					)
				,	'content'		=>	$thead
					.	$tfoot
					.	$tbody
				)
			);
			
			// ASSOCIATE/OWN EXISTING RECORD
			if	(	permission::evaluate
					(	$this->table->permissions['own_records_if']
					)
				&&	$this->name != $this->table->name
				&&	!empty
					(	$this->owners
					)
				):
				$this->own_records();
				$table .= xhtml::hidden_inputs
				(	array
					(	'act'			=>	''
	//				,	'ownership_id'	=>	key($this->owners)
					)
				);
			else:
				$this->template['own_record'] = '';
			endif;
			
			// DISSOCIATE/DISOWN EXISTING RECORDS
			if	(	(	empty
						(	$this->owners
						)
					&&	$this->name != $this->table->name
					)
				||	!count
					(	$this->records
					)
				):
				$this->template['disown_records'] = '';
			endif;
			
			if	(	$this->template['aft_tbl']['render']
				&&	(	count
						(	$this->records
						)
					||	(	$this->template['render_if_no_records']
						&&	$this->template['aft_tbl']['render_if_no_records']
						)
					)
				):
				// POST-TABLE TEMPLATE
				$table .= $this->template['aft_tbl']['template'];
			endif;
			
			$table = $pre_tbl
			.	xhtml::element
				(	array
					(	'tag_name'	=>	'DIV'
					,	'attributes'	=>	array
						(	'CLASS'			=>	'table_content'
						)
					,	'content'		=>	$table
					)
				)
			;
			
			//	PAGINATOR
			if	(	!empty
				 	(	$this->template['pagination']['items_per_page']
					)
				&& 	$this->template['records_total']	>	$this->template['pagination']['items_per_page']
				):
				$hidden_inputees[$this->form.'-first_item'] = $this->table->request['first_item'];
				$this->template['paginator'] = $GLOBALS['page']->render_paginator
				(	array
					(	'form_to_submit'	=>	$this->form
					,	'total_items'		=>	$this->records_total
					,	'first_item'		=>	$this->table->request['first_item']
					,	'items_per_page'	=>	$this->template['pagination']['items_per_page']
					,	'links_per_page'	=>	$this->template['pagination']['links_per_page']
					)
				);
			else:
				$this->template['paginator'] = '';
			endif;
			
			$this->replace['table'] = arrays::sort_by_strlen
			(	array
				(	'array'			=>	$this->replace['table']
				,	'sort_by_key'	=>	0
				,	'reverse'		=>	1
				)
			);
			foreach
				(	$this->replace['table'] as $replace	
				):
				if	(	isset
						(	$this->template[$replace]
						)
					):
					if	(	$replace == 'reorder'
						&&	count
							(	$this->records
							)	<	2
						):
						$this->template[$replace] = '';
					endif;
				else:
					$this->template[$replace] = '';
				endif;
				$table = str_replace
				(	'$'.$replace
				,	$this->template[$replace]
				,	$table
				);
			endforeach;
			
			$hidden_inputees['z'] = 
			(	empty
				(	$GLOBALS['page']->request['z']
				)
			||	!empty
				(	$GLOBALS['dbi']->tables[$GLOBALS['page']->request['z']]
				)
			)
			?	$this->table->name
			:	$GLOBALS['page']->request['z']
			;
			
			if	(	$this->template['header']['filtering']
				&&	permission::evaluate
					(	$this->table->permissions['filter_records_if']
					)
				):
				$hidden_inputees['most_recent_filter'] = '';
			endif;
			
			$hidden_inputs = xhtml::hidden_inputs
			(	$hidden_inputees
			);
			
			$form = xhtml::element
			(	array
				(	'tag_name'	=>	'FORM'
				,	'attributes'	=>	array
					(	'ACTION'		=>	uri::generate()
					,	'METHOD'		=>	'post'
					,	'ID'			=>	$this->form
					,	'NAME'			=>	$this->form
					)
				,	'content'		=>	$hidden_inputs
					.	$table
				)
			);
			
			$div = xhtml::element
			(	array
				(	'tag_name'	=>	'DIV'
				,	'attributes'	=>	array
					(	'ID'			=>	'div_'
					 	.	$this->form
					,	'NAME'			=>	'div_'
						.	$this->form
					,	'CLASS'			=>	'table_container'
					)
				,	'content'		=>	$form
				)
			);
/*
			$div = debug::expose
			(	array
				(	'data'	=>	$this->template_files_included
				,	'return'	=>	1
				)
			)
			.	$div
			;
*/
			return $div;
		else:
			return '';
		endif;
	}
	
	function render_data_cell()
	{	$data_cell = $this->column['template']->render();
		if	(	!empty
				(	$this->column['enclosure']
				)
			):
			$element = array
			(	'tag_name'	=>	$this->column['enclosure']
			,	'content'		=>	$data_cell
			);
			
			if	(	!empty
					(	$this->column['attributes']
					)
				):
				$element['attributes'] = $this->column['attributes'];
			endif;
			
			$data_cell = xhtml::element
			(	$element
			);
		endif;
		return $data_cell;
	}

	function render_data_row()
	{	$data_row = $this->template['records']['row']['template'];
	
		$data_row_onclick_query = array();
		foreach
			(	$this->template['records']['row']['onclick_query_vars']	as	$key	=>	$val	
			):
			$data_row_onclick_query[$key] = 
			(	empty
				(	$val
				)
			)
			?	$this->record[$key]
			:	$val
			;
		endforeach;
		reset
		(	$this->template['records']['row']['onclick_query_vars']
		);
		$data_row = str_replace
		(	'$data_row_onclick_href'
		,	uri::generate
			(	array
				(	'query'				=>	$data_row_onclick_query
				,	'query_crypt_key'	=>	$GLOBALS['page']->url_crypt_key
				)
			)
		,	$data_row
		);

		$data_row = str_replace
		(	'$id'
		,	$this->record['id']
		,	$data_row
		);
		foreach
			(	$this->columns_by_key	as	$column	=>	$column_name	
			):
			$this->column = &$this->template['columns'][$column];
			$this_field = '';
			if	(	!empty
					(	$this->column['field']
					)
				):
				$this_field = $this->column['field'];
			else:
				if	(	!empty
						(	$this->column['template']->template
						)
					):
					$this_field = $this->column['name'];
				endif;
			endif;
			if	(	!empty
					(	$this_field
					)	
				):
				if	(	empty
						(	$this->record[$this_field]  //  $this->column['field'] ????
						)
					&&	!empty
						(	$this->table->owners[$this->column['name']]
						)
					):
					$ownership_id = $this->table->owners[$this->column['name']];
					
					$this->column['template']->field->value = 
					$this->column['template']->value['raw'] = 
					$this->column['template']->attributes['value'] =
					implode
					(	','
					,	$GLOBALS['dbi']->get_owner_records
						(	array
							(	'owned'			=>	array
								(	$ownership_id	=>	array
									(	$this->record['id']
									)
								)
							)
						)
					);
				else:
					$this->column['template']->value['raw'] = 
					(	empty
						(	$this->record[$this_field]
						)
					)
					?	''
					:	$this->record[$this_field]; // $this->record[$this->column['field']];
				endif;
				$data_cell = $this->render_data_cell();
				if	(	empty
						(	$data_cell
						)
					&&	!$this->column['show_empty_values']
					):
					$data_cell = '';
				endif;
				$data_row = str_replace
				(	'$'
					.	$this_field
				,	$data_cell
				,	$data_row
				);
			endif;
		endforeach;
		reset
		(	$this->columns_by_key
		);
		if	(	empty
				(	$this->template['records']['row_number']
				)
			):
			$this->template['records']['row_number'] = 0;
		endif;
		if	(	$this->template['records']['number_rows_by_id']
			):
			$this->template['records']['row_number'] = $this->record['id'];
		else:
			$this->template['records']['row_number']++;
		endif;
		$this->replace['records'] = arrays::sort_by_strlen
		(	array
			(	'array'			=>	$this->replace['records']
			,	'sort_by_key'	=>	0
			,	'reverse'		=>	1
			)
		);
		foreach
			(	$this->replace['records']	as	$replace	
			):
			if	(	isset
					(	$this->template['records'][$replace]
					)
				):
				$replacer = str_replace
				(	array
					(	'$id'
					,	'%24id'
					)
				,	$this->record['id']
				,	$this->template['records'][$replace]
				);
				$data_row = str_replace
				(	'$'
					.	$replace
				,	$replacer
				,	$data_row
				);
			endif;
		endforeach;
		return $data_row;
	}
	
	function render_footer_row()
	{	$footer_row = $this->template['footer']['template'];
		
		return xhtml::element
		(	array
			(	'tag_name'	=>	'TR'
			,	'attributes'	=>	array
				(	'CLASS'		=>	'table_footer_row'
				,	'ID'			=>	'table_'
					.	$this->table->name
					.	'_'
					.	$this->table->renderings
					.	'.tr_Z'
				)
			,	'content'		=>	$footer_row
			)
		);
	}
	
	function render_header_cell
	(	$onclick	=	false
	)
	{	$attrs = array
		(	'CLASS'		=>	'table_column_label'
		);
		if	(	!empty
				(	$onclick
				)
			):
			$attrs['CLASS'] .= ' sortable';
			$attrs['ONCLICK'] = $onclick;
		endif;
		return xhtml::element
		(	array
			(	'tag_name'		=>	'SPAN'
			,	'attributes'	=>	$attrs
			,	'content'		=>	$this->column['title']
			)
		);
	}
	
	function render_header_row()
	{	$header_row = $this->template['header']['row']['template'];

		$sortable_columns = 0;
		if	(	$this->template['header']['sorting']
			):
			$sort_script = 'var sort_field_ray = new Array();'
			.	"\n"
			;
			foreach
				(	$this->template['columns'] as $column => &$this->column
				):
				if	(	$this->column['sort']
					):
					$sortable_columns++;
				endif;
			endforeach;
			reset
			(	$this->template['columns']
			);
			
			$dir_option_ray = array
			(	'ASC'	=>	'Forward'
			,	'DESC'	=>	'Reverse'
			);
		endif;
		
		foreach
			(	$this->columns_by_key	as	$column	=>	$column_name	
			):
			$this->column = &$this->template['columns'][$column];
			$column_sorter_multi = 
			$column_sorter_single = 
			$in_use = ''
			;
			if	(	!empty
					(	$this->column['field']
					)
				&&	(	!empty
						(	$this->table->fields[$this->column['field']]
						)
					||	!empty
						(	$this->table->owners[$this->column['field']]
						)
					)
				):
//				$field = &$this->table->fields[$this->column['field']];
				$field = &$this->column['template'];
				
				if	(	$sortable_columns
					&&	$this->column['sort']
					):
					$sortie = $this->form
					.	'-'
					.	$this->column['field']
					;
					$sort_name = $sortie
					.	'-sort'
					;
					$dir_name = $sortie
					.	'-dir'
					;
					
					$sort_script .= 
					(	$this->column['field'] != 'id'	
					)
					?	'sort_field_ray['
						.	$column
						.	'] = "'
						.	$sort_name
						.	'";'
						.	"\n"
					:	'sort_field_ray['
						.	$column
						.	'] = "";'
						.	"\n"
					;
		
					$sort_options = '';

					for (	$o = 0
						;	$o < $sortable_columns
						;	$o++
						):
						$option_attributes = array
						(	'VALUE'		=>	$o
						);
						
						if	(	!empty
								(	$this->column['sort']
								)
							&&	$this->column['sort']['priority'] == $o	
							):
							$option_attributes['SELECTED'] = '';
							if	(	$o
								):
								$in_use = ' in_use';
							endif;
						endif;
						
						$sort_options .= xhtml::element
						(	array
							(	'tag_name'		=>	'OPTION'
							,	'attributes'	=>	$option_attributes
							,	'content'		=>	$o
							)
						);
					endfor;

					$column_sorter_multi .= xhtml::element
					(	array
						(	'tag_name'	=>	'SELECT'
						,	'attributes'	=>	array
							(	'NAME'			=>	$sort_name
							,	'ID'			=>	$sort_name
							,	'CLASS'			=>	'sort_select'
								.	$in_use
							,	'onChange'		=>	'sort_sort(\''
								.	$this->form
								.	'\',this.name,this.selectedIndex)'
							,	'title'			=>	'Column Sort Priority'
							)
						,	'content'		=>	$sort_options
						)
					);
					
					$dir_options = '';
					
					foreach
						(	$dir_option_ray	as	$dir_value	=>	$dir_content	
						):
						$option_attributes = array
						(	'VALUE'		=>	$dir_value
						);
						
						if	(	$this->column['sort']['direction'] == $dir_value
							):
							$option_attributes['SELECTED'] = '';				
						endif;

						$dir_options .= xhtml::element
						(	array
							(	'tag_name'	=>	'OPTION'
							,	'attributes'	=>	$option_attributes
							,	'content'		=>	$dir_content
							)
						);
					endforeach;
					reset
					(	$dir_option_ray
					);
					
					$column_sorter_multi .= xhtml::element
					(	array
						(	'tag_name'	=>	'SELECT'
						,	'attributes'	=>	array
							(	'NAME'			=>	$dir_name
							,	'ID'			=>	$dir_name
							,	'CLASS'			=>	'sort_select'
								.	$in_use
							,	'onChange'		=>	'sort_dir(\''
								.	$this->form
								.	'\',\''
								.	$this->column['field']
								.	'\')'
							,	'title'			=>	'Column Sort Direction'					
							)
						,	'content'		=>	$dir_options
						)
					);
					
					if	(	$this->template['header']['sorting'] == 1
						):
						// SINGLE COLUMN SORTING KLUDGE
						$column_sorter_single = 'sort_single(\''
						.	$sort_name
						.	'\',\''
						.	$dir_name
						.	'\',\''
						.	$this->form
						.	'\',event);'
						;
						
						$GLOBALS['page']->scripts['ready'][] = "
$('.cap').each(function(){
	onclicker = $(this).attr('onclick')+'';
	if	(	onclicker.length > 27
		)
	{	$(this).addClass('pointer');
		$(this).attr('title','Click to sort rows by the values in this column in forward order.');
		if ($(this).find('select').val()==1) {
			if ($(this).find('select[title=\"Column Sort Direction\"]').val()=='ASC') {
				$(this).addClass('header_sorted sorted_asc');
				$(this).attr('title','Click to sort rows by the values in this column in reverse order.');
			} else {
				$(this).addClass('header_sorted sorted_desc');
			}
		}
	}
});											
"
						;


						$selector_style = 'display:none;';					
					else:
						$selector_style = '';
					endif;
											
					$column_sorter_multi = xhtml::element
					(	array
						(	'tag_name'	=>	'DIV'
						,	'attributes'	=>	array
							(	'CLASS'			=>	'srt_sel'
							,	'style'			=>	$selector_style
							)
						,	'content'		=>	$column_sorter_multi
						)
					);

				else:
					$column_sorter = '';
				endif;

				$header_row = str_replace
				(	'$'
					.	$this->column['name']
					.	'_sort_multi'
				,	$column_sorter_multi
				,	$header_row
				);
				$header_row = str_replace
				(	'$'
					.	$this->column['name']
					.	'_sort_single'
				,	'' // $column_sorter_single
				,	$header_row
				);
								
				if	(	$this->template['header']['filtering']
					&&	$this->column['filter']
					):
					
					$column_value = 
					(	isset
						(	$this->request[$this->column['field']]
						)
					)
					?	$this->request[$this->column['field']]
					:	''
					;
					
					$field->size = $this->column['filter'];
					
					$column_filter = xhtml::element
					(	array
						(	'tag_name'	=>	'DIV'
						,	'attributes'	=>	array
							(	'CLASS'			=>	'flt_sel'
							)
						,	'content'		=>	$this->column['template']->render
							(	'filter'
							)
						)
					);
				else:
					$column_filter = '';
				endif;
				$header_row = str_replace
				(	'$'
					.	$this->column['field']
					.	'_filter'
				,	$column_filter
				,	$header_row
				);
			else:
				$header_row = str_replace
				(	'$'
					.	$this->column['name']
					.	'_filter'
				,	''
				,	$header_row
				);
				$header_row = str_replace
				(	'$'
					.	$this->column['name']
					.	'_sort_multi'
				,	''
				,	$header_row
				);
				$header_row = str_replace
				(	'$'
					.	$this->column['name']
					.	'_sort_single'
				,	''
				,	$header_row
				);
			endif;

			$header_cell = $this->render_header_cell
			(	$column_sorter_single
			);
			
			$header_row = str_replace
			(	'$'
				.	$this->column['name']
			,	$header_cell
			,	$header_row
			);
		endforeach;
		reset
		(	$this->columns_by_key
		);
		
		if	(	$sortable_columns
			):
			$GLOBALS['page']->scripts['functions'][] = $sort_script;
//			$GLOBALS['page']->scripts['src'][] = 'sort_table_columns';
		endif;
		
		return $header_row;
	}
	
}
