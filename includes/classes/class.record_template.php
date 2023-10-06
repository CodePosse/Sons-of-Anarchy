<?php

class record_template {

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
					,	'table'	=>	array
						(	'blurb'		=>	'A reference to the table object to which this table object belongs.'
						)
					,	'record'	=>	array
						(	'blurb'		=>	'A reference to the record object to which this template object belongs.'
						)
/*					,	'act'		=>	array
						(	'possible_values'	=>	array
							(	'view'
							,	'edit'
							,	'insert'
							,	'update'
							,	'delete'
							)
						,	'default_value'	=>	'view'
						)
*/					,	'crypt_key'	=>	array
						(	'default_value'	=>	''
						)
					)
				)
			)
		);
		
		$this->table = &$GLOBALS['dbi']->tables[$table];
		$this->record = &$record;
		
		$this->request = &$this->table->request;
		
		$this->name = $name;
		
		$this->record->renderings++;
		$this->form = $this->table->name
		.	'_'
		.	$this->record->renderings
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
		(	'record'	=>	array
			(	'title_plural'
			,	'title'
			,	'new_record'
			,	'view_record'
			,	'edit_record'
			,	'insert_record'
			,	'update_record'
			,	'delete_record'
			,	'cancel_create'
			,	'cancel_update'
			)
		,	'header'	=>	array
			(	
			)
		,	'fields'	=>	array
			(	'row_number'
			,	'toggle'
			,	'upload_file'
			)
		,	'columns'	=>	array
			(	
			)
		);
		
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
		$record_template = 
		array()
		;
		$check_for_template = array
		(	'default.record_template.php'
		,	$this->table->name
			.	'.record_template.php'
		,	$this->name
			.	'.record_template.php'
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
		
		$this->template = $record_template;
		if	(	!isset
				(	$this->template['title']
				)
			):
			if	(	$this->record->act == 'view'
				):
				$this->template['title'] = $this->table->title;
			else:
				$this->template['title'] = strings::label
				(	$this->record->act
				)
				.	' '
				.	$this->table->title
				;
			endif;
		endif;
		if	(	!isset
				(	$this->template['title_plural']
				)
			):
			$this->template['title_plural'] = strings::pluralize
			(	$this->template['title']
			);
		endif;
		if	(	!isset
				(	$this->template['class']
				)
			):
			$this->template['class'] = 'record';
		endif;
		if	(	!isset
				(	$this->template['cellspacing']
				)
			):
			$this->template['cellspacing'] = 0;
		endif;
		if	(	!isset
				(	$this->template['output']
				)
			):
			$this->template['output'] = $this->record->act;
		else:
			$this->record->act = $this->template['output'];
		endif;
		
		if	(	permission::evaluate
				(	$this->table->permissions['view_table_if']
				)
			):
			if	(	!isset
					(	$this->template['all_records']
					)
				):
				switch
					(	$this->table->name
					):
					case 'content_block':
						$all_records_anchor = xhtml::element
						(	array
							(	'tag_name'	=>	'A'
							,	'attributes'	=>	array
								(	'ONCLICK'		=>	"$(location).attr('href',(($('#content_block_1-facebook_tab_frame').length)?'"
									.	$_SERVER['SCRIPT_NAME']
									.	"?z=facebook_tab_frame&id='+$('#content_block_1-facebook_tab_frame').val():$('#table_content_block_1\\\\.tr_14 a').attr('href')));"
								)
							,	'content'		=>	'List All '
								.	strings::pluralize
									(	$this->table->title
									)
							)
						);
						break;
					case 'theater':
						$all_records_anchor = xhtml::element
						(	array
							(	'tag_name'	=>	'A'
							,	'attributes'	=>	array
								(	'ONCLICK'		=>	"$(location).attr('href',(($('#theater_1-content_block').length)?'"
									.	$_SERVER['SCRIPT_NAME']
									.	"?z=content_block&id='+$('#theater_1-content_block').val():$('#table_theater_1\\\\.tr_7 a').attr('href')));"
								)
							,	'content'		=>	'List All '
								.	strings::pluralize
									(	$this->table->title
									)
							)
						);
						break;
					case 'gallery_tab':
						$all_records_anchor = xhtml::element
						(	array
							(	'tag_name'	=>	'A'
							,	'attributes'	=>	array
								(	'ONCLICK'		=>	"$(location).attr('href',(($('#gallery_tab_1-content_block').length)?'"
									.	$_SERVER['SCRIPT_NAME']
									.	"?z=content_block&id='+$('#gallery_tab_1-content_block').val():$('#table_gallery_tab_1\\\\.tr_3 a').attr('href')));"
								)
							,	'content'		=>	'List All '
								.	strings::pluralize
									(	$this->table->title
									)
							)
						);
						break;
					case 'gallery_item':
						$all_records_anchor = xhtml::element
						(	array
							(	'tag_name'	=>	'A'
							,	'attributes'	=>	array
								(	'ONCLICK'		=>	"$(location).attr('href',(($('#gallery_item_1-gallery_tab').length)?'"
									.	$_SERVER['SCRIPT_NAME']
									.	"?z=gallery_tab&id='+$('#gallery_item_1-gallery_tab').val():$('#table_gallery_item_1\\\\.tr_6 a').attr('href')));"
								)
							,	'content'		=>	'List All '
								.	strings::pluralize
									(	$this->table->title
									)
							)
						);
						break;
					default:
						$all_records_link = uri::generate
						(	array
							(	'query'	=>	array
								(	'z'	=>	$this->table->name
								)
							,	'query_crypt_key'	=>	$GLOBALS['page']->url_crypt_key
							)
						);
						
						$all_records_anchor = xhtml::element
						(	array
							(	'tag_name'	=>	'A'
							,	'attributes'	=>	array
								(	'HREF'		=>	$all_records_link
								)
							,	'content'		=>	'List All '
								.	strings::pluralize
									(	$this->table->title
									)
							)
						);
				endswitch;
				
				
				
				$this->template['all_records'] = xhtml::element
				(	array
					(	'tag_name'	=>	'DIV'
					,	'attributes'	=>	array
						(	'ID'			=>	$this->form
							.	'.all_records'
						,	'CLASS'			=>	'record_button float_right'
						)
					,	'content'		=>	$all_records_anchor
					)
				);
			endif;
		else:
			$this->template['all_records'] = '';
		endif;
		
		if	(	permission::evaluate
				(	$this->table->permissions['create_records_if']
				)
			):
			if	(	!isset
					(	$this->template['new_record']
					)
				):
				if	(	$this->record->act == 'create'
					):
					$this->template['new_record'] = '';
				else:
					$new_record_link = uri::generate
					(	array
						(	'query'	=>	array
							(	'z'	=>	$this->table->name
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
							.	$this->table->title
						)
					);
					
					$this->template['new_record'] = xhtml::element
					(	array
						(	'tag_name'	=>	'DIV'
						,	'attributes'	=>	array
							(	'ID'			=>	$this->form
								.	'.new_record'
							,	'CLASS'		=>	'record_button float_right'
							)
						,	'content'		=>	$new_record_anchor
						)
					);
				endif;
			endif;
		else:
			$this->template['new_record'] = '';
		endif;
		
		if	(	$this->table->name	==	'project'
			&&	$this->record->act	!=	'create'
			):
			$project_files_link = uri::generate
			(	array
				(	'query'	=>	array
					(	'z'		=>	'comp_viewer'
					,	'id'	=>	$this->record->id
					)
				,	'query_crypt_key'	=>	$GLOBALS['page']->url_crypt_key
				)
			);
					
			$project_files_anchor = xhtml::element
			(	array
				(	'tag_name'	=>	'A'
				,	'attributes'	=>	array
					(	'HREF'		=>	$project_files_link
					)
				,	'content'		=>	'Quick View'
				)
			);
					
			$this->template['new_record'] .= xhtml::element
			(	array
				(	'tag_name'	=>	'DIV'
				,	'attributes'	=>	array
					(	'ID'			=>	$this->form
						.	'.new_record'
					,	'CLASS'		=>	'record_button float_right'
					)
				,	'content'		=>	$project_files_anchor
				)
			);
		endif;
		
		if	(	in_array
				(	$this->table->name
				,	array
					(	'player'
					,	'team'
					,	'tournament'
					)
				)
			&&	$this->record->act	!=	'create'
			):
			$riot_admin_redirect_link = uri::generate
			(	array
				(	'query'	=>	array
					(	'z'		=>	'riot_admin_redirect'
					,	'table'	=>	$this->table->name
					,	'id'	=>	$this->record->id
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
				,	'content'		=>	'Edit in Site'
				)
			);
					
			$this->template['new_record'] .= xhtml::element
			(	array
				(	'tag_name'	=>	'DIV'
				,	'attributes'	=>	array
					(	'ID'			=>	$this->form
						.	'.riot_admin_redirect'
					,	'CLASS'		=>	'record_button float_right'
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
					(	$this->template['view_record']
					)
				):
				
				$view_record_link = uri::generate
				(	array
					(	'query'	=>	array
						(	'z'		=>	$this->table->name
						,	'id'		=>	$this->record->id
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
				
				$this->template['view_record'] = xhtml::element
				(	array
					(	'tag_name'	=>	'DIV'
					,	'attributes'	=>	array
						(	'ID'			=>	$this->form.'.view_record'
						,	'CLASS'		=>	'record_button float_right'
						)
					,	'content'		=>	$view_record_anchor
					)
				);
				
			endif;
		else:
			$this->template['view_record'] = '';
		endif;
		
		if	(	permission::evaluate
				(	$this->table->permissions['edit_records_if']
				)
			):
			if	(	!isset($this->template['edit_record'])
				):
				
				$edit_record_link = uri::generate
				(	array
					(	'query'				=>	array
						(	'z'					=>	$this->table->name
						,	'id'				=>	$this->record->id
						,	'act'				=>	'edit'
						)
					,	'query_crypt_key'	=>	$GLOBALS['page']->url_crypt_key
					)
				);
				
				$edit_record_anchor = xhtml::element
				(	array
					(	'tag_name'		=>	'A'
					,	'attributes'	=>	array
						(	'HREF'			=>	$edit_record_link
						)
					,	'content'		=>	'Edit '.$this->table->title
					)
				);
				
				$this->template['edit_record'] = xhtml::element
				(	array
					(	'tag_name'		=>	'DIV'
					,	'attributes'	=>	array
						(	'ID'			=>	$this->form.'.edit_record'
						,	'CLASS'			=>	'record_button float_right'
						)
					,	'content'		=>	$edit_record_anchor
					)
				);
				
			endif;
			
			if	(	!isset
					(	$this->template['update_record']
					)
				):
				$update_record_anchor = xhtml::element
				(	array
					(	'tag_name'		=>	'A'
					,	'attributes'	=>	array
						(	'HREF'			=>	'#'
						,	'onMouseOver'	=>	"window.status='Update "
							.	$this->table->title
							.	"';return true;"
						,	'onMouseOut'	=>	"window.status='';return true;"
						,	'onClick'		=>	"document.getElementById('"
							.	$this->form
							.	"').submit();return false;"
						)
					,	'content'		=>	'Update '
						.	$this->table->title
					)
				);
				
				$this->template['update_record'] = xhtml::element
				(	array
					(	'tag_name'		=>	'DIV'
					,	'attributes'	=>	array
						(	'ID'			=>	$this->form
							.	'.update_record'
						,	'CLASS'			=>	'record_button float_right'
						)
					,	'content'		=>	$update_record_anchor
					)
				);
				
			endif;
		else:
			$this->template['edit_record'] = 
			$this->template['update_record'] = 
			''
			;
		endif;
		
		if	(	permission::evaluate
				(	$this->table->permissions['create_records_if']
				)
			):
			if	(	!isset
				 	(	$this->template['insert_record']
					)
				):
				$insert_record_anchor = xhtml::element
				(	array
					(	'tag_name'		=>	'A'
					,	'attributes'	=>	array
						(	'HREF'			=>	'#'
						,	'onMouseOver'	=>	"window.status='Create "
							.	$this->table->title
							.	"';return true;"
						,	'onMouseOut'	=>	"window.status='';return true;"
						,	'onClick'		=>	"document."
							.	$this->form
							.	".submit();return false;"
						)
					,	'content'		=>	'Create '
						.	$this->table->title
					)
				);
				
				$this->template['insert_record'] = xhtml::element
				(	array
					(	'tag_name'		=>	'DIV'
					,	'attributes'	=>	array
						(	'ID'			=>	$this->form
							.	'.insert_record'
						,	'CLASS'			=>	'record_button float_right'
						)
					,	'content'		=>	$insert_record_anchor
					)
				);
				
			endif;
		else:
			$this->template['insert_record'] = '';
		endif;
		
		if	(	permission::evaluate
				(	$this->table->permissions['delete_records_if']
				)
			&&	!(	$this->table->name	==	'user'
				&&	$this->record->id	==	$GLOBALS['user']->id
				)
			):
			if	(	!isset
				 	(	$this->template['delete_record']
					)
				):
				
				$owns_records = 
				$owned_tables = 
				''
				;
				$this->record->get_owned();
				$ownership_count = count
				(	$this->record->owns
				);
				foreach
					(	$this->record->owns	as	$ownership_id	=>	$owned_info
					):
					if	(	count
						 	(	$owned_info
							)
						):
						$owned_table_title = $GLOBALS['dbi']->ownerships[$ownership_id]['owned_title'];
						$owned_table_plural = strings::pluralize
						(	$owned_table_title
						);
						if	(	$ownership_count	==	1
							):
							if	(	empty
								 	(	$owned_tables
									)
								):
								$owned_tables .= $owned_table_plural;
							else:
								$owned_tables = substr
								(	trim
									(	$owned_tables
									)
								,	0
								,	-1
								)	
								.	' and '
								.	$owned_table_plural
								;
							endif;
						else:
							$owned_tables .= $owned_table_plural
							.	', '
							;
						endif;
						if	(	empty
							 	(	$owns_records
								)
							):
							$owns_records = 1;
						endif;
					endif;
					$ownership_count--;
				endforeach;
				reset
				(	$this->record->owns
				);
				if	(	!empty
					 	(	$owns_records
						)
					):
					$owns_records = ' AND all its '
					.	$owned_tables
					;
					$owned_tables = ' &amp; All Sub-Records';
				else:
					$owned_tables = '';
				endif;
				
				$only =
				(	empty
					(	$owned_tables
					)
				)
				?	''
				:	' Only'
				;
				
				$delete_record_anchor = xhtml::element
				(	array
					(	'tag_name'		=>	'A'
					,	'attributes'	=>	array
						(	'HREF'			=>	"javascript:document.getElementById('"
							.	$this->form
							.	"').act.value='delete';document.getElementById('"
							.	$this->form
							.	"').submit();"
						,	'onMouseOver'	=>	"window.status='Delete "
							.	$this->table->title
							.	$only
							.	"';return true;"
						,	'onMouseOut'	=>	"window.status='';return true;"
						,	'onClick'		=>	"return confirm('Are you sure you want to permanently delete this "
							.	$this->table->title
							.	"?');"
						)
					,	'content'		=>	'Delete '
						.	$this->table->title
						.	$only
					)
				);
				
				$this->template['delete_record'] = xhtml::element
				(	array
					(	'tag_name'	=>	'DIV'
					,	'attributes'	=>	array
						(	'ID'			=>	$this->form
							.	'.delete_record'
						,	'CLASS'			=>	'record_button float_left'
						)
					,	'content'		=>	$delete_record_anchor
					)
				);
				
				if	(	!empty
						(	$owned_tables
						)
					):
					$delete_record_anchor = xhtml::element
					(	array
						(	'tag_name'		=>	'A'
						,	'attributes'	=>	array
							(	'HREF'			=>	"javascript:document.getElementById('"
								.	$this->form
								.	"').act.value='deplete';document.getElementById('"
								.	$this->form
								.	"').submit();"
							,	'onMouseOver'	=>	"window.status='Delete "
								.	$this->table->title
								.	'{$owns_tables}'
								.	"';return true;"
							,	'onMouseOut'	=>	"window.status='';return true;"
							,	'onClick'		=>	"return confirm('Are you sure you want to permanently delete this "
								.	$this->table->title
								.	$owns_records
								.	"?');"
							)
						,	'content'		=>	'Delete '
							.	$this->table->title
							.	$owned_tables
						)
					);
					
					$this->template['delete_record'] .= xhtml::element
					(	array
						(	'tag_name'		=>	'DIV'
						,	'attributes'	=>	array
							(	'ID'			=>	$this->form
								.	'.delete_record'
							,	'CLASS'			=>	'record_button float_left'
							)
						,	'content'		=>	$delete_record_anchor
						)
					);
				endif;
				
			endif;
		else:
			$this->template['delete_record'] = '';
		endif;
		
		if	(	!isset
				(	$this->template['cancel_create']
				)
			):
			
			$cancel_create_text = 'Cancel '
			.	$this->table->title
			.	' Creation'
			;
			
			$cancel_create_anchor = xhtml::element
			(	array
				(	'tag_name'		=>	'A'
				,	'attributes'	=>	array
					(	'HREF'			=>	"javascript:window.location='"
						.	$_SERVER['SCRIPT_NAME']
						.	"?z="
						.	$this->table->name
						.	"';"
					,	'onMouseOver'	=>	"window.status='"
						.	$cancel_create_text
						.	"';return true;"
					,	'onMouseOut'	=>	"window.status='';return true;"
					,	'title'			=>	$cancel_create_text
					)
				,	'content'		=>	$cancel_create_text
				)
			);
			
			$this->template['cancel_create'] = xhtml::element
			(	array
				(	'tag_name'		=>	'DIV'
				,	'attributes'	=>	array
					(	'ID'			=>	$this->form
						.	'.cancel_create'
					,	'CLASS'			=>	'record_button float_left'
					)
				,	'content'		=>	$cancel_create_anchor
				)
			);
			
		endif;	
		
		if	(	!isset
				(	$this->template['cancel_update']
				)
			):
			
			$cancel_update_text = 'Cancel '
			.	$this->table->title
			.	' Update'
			;
			
			$cancel_update_anchor = xhtml::element
			(	array
				(	'tag_name'		=>	'A'
				,	'attributes'	=>	array
					(	'HREF'			=>	"javascript:document.getElementById('"
						.	$this->form
						.	"').act.value='view';document.getElementById('"
						.	$this->form
						.	"').submit();"
					,	'onMouseOver'	=>	"window.status='"
						.	$cancel_update_text
						.	"';return true;"
					,	'onMouseOut'	=>	"window.status='';return true;"
					,	'title'			=>	$cancel_update_text
					)
				,	'content'		=>	$cancel_update_text
				)
			);
			
			$this->template['cancel_update'] = xhtml::element
			(	array
				(	'tag_name'		=>	'DIV'
				,	'attributes'	=>	array
					(	'ID'			=>	$this->form
						.	'.cancel_update'
					,	'CLASS'			=>	'record_button float_left'
					)
				,	'content'		=>	$cancel_update_anchor
				)
			);
			
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
		if	(	$this->template['header']['render']
			):
			
		endif;
		
		if	(	!isset
			 	(	$this->template['fields']['render']
				)
			):
			$this->template['fields']['render'] = 1;
		endif;
		if	(	$this->template['fields']['render']
			):
			if	(	!isset
				 	(	$this->template['fields']['show_field_labels']
					)
				):
				$this->template['fields']['show_field_labels'] = 1;
			endif;
			if	(	!isset
				 	(	$this->template['fields']['show_field_blurbs']
					)
				):
				$this->template['fields']['show_field_blurbs'] = 1;
			endif;
			
			if	(	!isset
					(	$this->template['fields']['upload_file']
					)
				):
				switch
					(	$this->record->act	
					):
					case 'create':
/*
						$this->template['fields']['upload_file'] = 'You may upload a $title after you have created your '
						.	$this->table->title
						.	'.'
						;
						break;
*/
					case 'edit':

						$upload_file_anchor = xhtml::element
						(	array
							(	'tag_name'		=>	'A'
							,	'attributes'	=>	array
								(	'HREF'			=>	'#'
								,	'onMouseOver'	=>	"window.status='Select an Existing "
									.	'$title'
									.	"';return true;"
								,	'onMouseOut'	=>	"window.status='';return true;"
								,	'onClick'		=>	"$('#"
									.	$this->form												 
									.	'-upload_$name'
									.	"').html($('#"
									.	$this->form												 
									.	'-upload_$name'
									.	"').html().replace(/type=(.)text/,'type=$1file'));document.getElementById('"
									.	$this->form
									.	"-new_"
									.	'$name'
									.	"').style.display='none';document.getElementById('"
									.	$this->form
									.	"-upload_"
									.	'$name'
									.	"').style.display='block';"
/*								
								,	'onClick'		=>	"document.getElementById('"
									.	$this->form
									.	"').act.value='edit';document.getElementById('"
									.	$this->form
									.	"').upload.value='"
									.	'$name'
									.	"';document.getElementById('"
									.	$this->form
									.	"').submit();return false;"
*/
								)
							,	'content'		=>	'Select an Existing $title'
							)
						);
/*						
						$upload_file_anchor = xhtml::script_element
						(	array
						 	(	'content'	=>	"function upload_it(){document.getElementById('"
								.	$this->form
								.	"').upload.value='"
								.	'$name'
								.	"';};window.onDomReady(upload_it);"
							)
						);
*/						
						$this->template['fields']['upload_file'] = 
						xhtml::element
						(	array
							(	'tag_name'		=>	'DIV'
							,	'attributes'	=>	array
								(	'ID'			=>	$this->form
									.	'-new_$name'
								,	'CLASS'			=>	'record_button float_left'
								,	'STYLE'			=>	'display:block'
								)
							,	'content'		=>	$upload_file_anchor
							)
						);
						break;
				endswitch;
			endif;
			
		endif;
		
		if	(	count
				(	$this->record->dud['missing']
				)
			||	count
				(	$this->record->dud['invalid']
				)
			):
			
			if	(	empty
					(	$this->template['dud']
					)
				):
				$this->template['dud'] = array
				(	'template'	=>	''
				);
			
				if	(	count
						(	$this->record->dud['missing']
						)
					):
					$label_column = xhtml::element
					(	array
						(	'tag_name'	=>	'TH'
						,	'attributes'	=>	array
							(	'CLASS'		=>	'rec_fld_lbl dud'
							)
						,	'content'		=>	'REQUIRED FIELDS:'
						)
					);
					
					$missing_fields = '';
					foreach
						(	$this->record->dud['missing']	as	$missing_field	
						):
						$missing_fields .= xhtml::element
						(	array
							(	'tag_name'	=>	'LI'
							,	'attributes'	=>	array
								(	'CLASS'		=>	'dud'
								)
							,	'content'		=>	$this->table->fields[$missing_field]->title
							)
						);
						$this->table->fields[$missing_field]->dud = 1;
					endforeach;
					
					$value_column = xhtml::element
					(	array
						(	'tag_name'	=>	'TD'
						,	'attributes'	=>	array
							(	'CLASS'		=>	'rec_fld_val dud'
							)
						,	'content'		=>	$missing_fields
						)
					);
					
					$this->template['dud']['template'] .= xhtml::element
					(	array
						(	'tag_name'	=>	'TR'
						,	'attributes'	=>	array
							(	'CLASS'		=>	'rec_fld_display dud'
							)
						,	'content'		=>	$label_column
							.	$value_column
						)
					);
				endif;
				if	(	count
					 	(	$this->record->dud['invalid']
						)
					):
					$label_column = xhtml::element
					(	array
						(	'tag_name'	=>	'TH'
						,	'attributes'	=>	array
							(	'CLASS'		=>	'rec_fld_lbl dud'
							)
						,	'content'		=>	'INVALID SUBMISSION:'
						)
					);
					
					$invalid_fields = '';
					
					foreach
						(	$this->record->dud['invalid'] as $invalid_field => $error_details	
						):
						if	(	isset
							 	(	$this->table->fields[$invalid_field]
								)
							):
							$this->table->fields[$invalid_field]->dud = 1;
							$error_summary = xhtml::element
							(	array
								(	'tag_name'	=>	'LI'
								,	'attributes'	=>	array
									(	'CLASS'		=>	'dud'
									)
								,	'content'		=>	$this->table->fields[$invalid_field]->title
								)
							);
						else:
							$error_summary = '';
						endif;
						$error_specifics = '';
						foreach
							(	$error_details	as	$error_detail	
							):
							$error_specifics .= xhtml::element
							(	array
								(	'tag_name'	=>	'LI'
								,	'attributes'	=>	array
									(	'CLASS'		=>	'dud'
									)
								,	'content'		=>	$error_detail
								)
							);
						endforeach;
						if	(	!empty
							 	(	$error_specifics
								)
							):
							$error_specifics = xhtml::element
							(	array
								(	'tag_name'	=>	'UL'
								,	'attributes'	=>	array
									(	'CLASS'		=>	'dud'
									)
								,	'content'		=>	$error_specifics
								)
							);
						endif;
						$invalid_fields .= $error_summary
						.	$error_specifics
						;
					endforeach;
					
					$value_column = xhtml::element
					(	array
						(	'tag_name'	=>	'TD'
						,	'attributes'	=>	array
							(	'CLASS'		=>	'rec_fld_val dud'
							)
						,	'content'		=>	$invalid_fields
						)
					);
					
					$this->template['dud']['template'] .= xhtml::element
					(	array
						(	'tag_name'	=>	'TR'
						,	'attributes'	=>	array
							(	'CLASS'		=>	'rec_fld_display dud'
							)
						,	'content'		=>	$label_column.$value_column
						)
					);
				endif;
//				$this->template['dud']['template'] .= '<tr><td colspan="2">&nbsp;</td></tr>';
			else:
				// LOOP THROUGH AND REPLACE FIELDS IN CUSTOM DUD TEMPLATE HERE
			endif;
		else:
			$this->template['dud'] = array
			(	'template'	=>	''
			);
		endif;
		
		if	(	empty
				(	$this->template['submit']
				)
			):
			$this->template['submit'] = array();
			if	(	!isset
					(	$this->template['submit']['template']
					)
				):				
				$submit_buttons = '';
				switch
					(	$this->template['output']	
					):
					case 'create':
						$submit_buttons .= '$insert_record$cancel_create';
						break;
					case 'edit':
						$submit_buttons .= '$update_record$cancel_update$delete_record';
						break;
					default: // case 'view':
						$submit_buttons .= '$edit_record';
				endswitch;	
				
				$this->template['submit']['template'] = xhtml::element
				(	array
					(	'tag_name'	=>	'DIV'
					,	'attributes'	=>	array
						(	'CLASS'		=>	'record_submit_buttons'
						,	'ID'			=>	'table_'
							.	$this->table->name
							.	'_'
							.	$this->record->renderings
							.	'.tr_z'
						)
					,	'content'		=>	$submit_buttons
					)
				);
			endif;
		endif;
		
		
		$this->fields = array();
		foreach
			(	$this->table->fields	as	$field_name => &$field
			):
			if	(	!in_array
					(	$field_name
					,	$this->table->permissions['hide_columns_from_record']
					)
				&&	empty
					(	$field->hide_from_record
					)
				&&	!in_array
					(	$field_name
					,	$GLOBALS['dbi']->fields_hidden_from_record
					)
				):
				
				$field->size = $GLOBALS['page']->input_sizes['default'];
				if	(	empty
					 	(	$field->value
						)
					):
					$field->value = 
					(	isset
					 	(	$this->record->values[$field_name]
						)
					)
					?	$this->record->values[$field_name]
					:	$field->default_value
					;
				endif;
				
				if	(	$field_name == 'inserted'
					):
					foreach
						(	$this->table->owners	as	$owner_name	=>	$ownership_id	
						):
						if	(	(	(	$this->table->name == 'user'
									&&	(	!empty
											(	$GLOBALS['dbi']->tables['user']->owners[$owner_name]
											)
										||	!empty
											(	$GLOBALS['user']->owners[$GLOBALS['dbi']->tables['user']->owners[$owner_name]]
											)
										)
									)
								||	(	empty
										(	$GLOBALS['dbi']->tables['user']->owners[$owner_name]
										)
									||	empty
										(	$GLOBALS['user']->owners[$GLOBALS['dbi']->tables['user']->owners[$owner_name]]
										)
									)
								)
							&&	!in_array
								(	$owner_name
								,	$this->table->permissions['hide_columns_from_record']
								)
							):
							$o_edit = 
							(	in_array
								(	$owner_name
								,	$this->table->permissions['lock_columns']
								)
							)
							?	0
							:	1
							;
							
							$o_null = 
							(	$GLOBALS['dbi']->ownerships[$ownership_id]['owners_required']
							)
							?	0
							:	1
							;
							
							$this->record->get_owned();
							$o_values = array();
							if	(	!empty
								 	(	$this->record->owners[$ownership_id]
									)
								):
								foreach
									(	$this->record->owners[$ownership_id]	as	$owner_id => $owner_info	
									):
									$o_values[] = $owner_id;
								endforeach;
							reset
							(	$this->record->owners[$ownership_id]
							);
							endif;
							if	(	count
								 	(	$o_values
									)
								):
								$o_value = implode
								(	','
								,	$o_values
								);
							else:
								$o_value = 0;
							endif;
							
							if	(	empty
								 	(	$o_value
									)
								&&	!empty
									(	$this->table->request[$owner_name]
									)
								):
								$o_value = arrays::implode_safe
								(	array
									(	'pieces'	=>	$this->table->request[$owner_name]
									,	'glue'		=>	','
									)
								);
							endif;
							
							$new_field_properties = array
							(	'name'			=>	$owner_name
							,	'title'			=>	$GLOBALS['dbi']->ownerships[$ownership_id]['owner_title']
							,	'title_plural'	=>	strings::pluralize
								(	$GLOBALS['dbi']->ownerships[$ownership_id]['owner_title']
								)
							,	'table'			=>	$this->table->name
							,	'max_length'	=>	0
							,	'type'			=>	'INTEGER'
							,	'type_string'	=>	'int(11)'
							,	'encrypt'		=>	0
							,	'edit_allowed'	=>	$o_edit
							,	'null_allowed'	=>	$o_null
							,	'default_value'	=>	0
							,	'value'			=>	$o_value
							,	'create_blurb'	=>	''
							,	'edit_blurb'	=>	''
							,	'view_blurb'	=>	''
							,	'foreign_table'	=>	$GLOBALS['dbi']->ownerships[$ownership_id]['owner_table']
							);
							
							if	(	isset
									(	$this->record->dud['invalid'][$owner_name]
									)
								):
								$new_field_properties['dud'] = 1;
							endif;
							
							$owner = new field
							(	array
								(	'fetch_field_object'	=>	$new_field_properties
								)
							);
							
							$this->fields[] = $owner->template
							(	$this->form
							);
						endif;
					endforeach;
					reset
					(	$this->table->owners
					);
				endif;
				
				$this->fields[] = $field->template
				(	$this->form
				);
			endif;
		endforeach;
		reset
		(	$this->table->fields
		);
		
		$this->title_row_template = '';
		
		// RECORD TITLE
		$this->title = $this->template['title'];
		if	(	$this->record->act == 'create'
			):
			$this->subtitle = 'New '
			.	$this->table->title
			;
		else:
			$this->subtitle = $this->table->render_record_title
			(	$this->record->values
			);
		endif;
		if	(	!empty
			 	(	$this->subtitle
				)
			):
			$this->title_row_template .= xhtml::element
			(	array
				(	'tag_name'	=>	'SPAN'
				,	'attributes'	=>	array
					(	'CLASS'		=>	'record_subtitle'
					)
				,	'content'		=>	$this->subtitle
				)
			);
		endif;
		
		$this->title_row_template .= xhtml::element
		(	array
			(	'tag_name'	=>	'SPAN'
			,	'attributes'	=>	array
				(	'CLASS'		=>	'record_title'
				)
			,	'content'		=>	$this->title
			)
		);
		
		$this->title_row_template = xhtml::element
		(	array
			(	'tag_name'	=>	'TD'
			,	'attributes'	=>	array
				(	'CLASS'		=>	'record_header'
				)
			,	'content'		=>	$this->title_row_template
			)
		);
		
		$all_and_new_buttons = '';
		if	(	!empty
			 	(	$this->template['all_records']
				)
			):
			$all_and_new_buttons .= $this->template['all_records'];
		endif;
		if	(	!empty
			 	(	$this->template['new_record']
				)
			):
			$all_and_new_buttons .= $this->template['new_record'];
		endif;
		if	(	!empty
			 	(	$all_and_new_buttons
				)
			):
			$this->title_row_template .= xhtml::element
			(	array
				(	'tag_name'	=>	'TD'
				,	'attributes'	=>	array
					(	'CLASS'		=>	'rec_fld_val'
					,	'STYLE'		=>	'text-align:right;'
					)
				,	'content'		=>	$all_and_new_buttons
				)
			);
		endif;
		
		$this->title_row_template = xhtml::element
		(	array
			(	'tag_name'	=>	'TABLE'
			,	'attributes'	=>	array
				(	'ID'			=>	'table_'
					.	$this->table->name
					.	'_'
					.	$this->record->renderings
					.	'.header'
				,	'CELLSPACING'	=>	0
				,	'CLASS'		=>	'pre_record'
				)
			,	'content'		=>	xhtml::element
				(	array
					(	'tag_name'	=>	'TR'
					,	'attributes'	=>	array
						(	'CLASS'		=>	'rec_fld_display'
						,	'ID'			=>	'table_'
							.	$this->table->name
							.	'_'
							.	$this->record->renderings
							.	'.tr_0'
						)
					,	'content'		=>	$this->title_row_template
					)
				)
			)
		);
		
		
		// RECORD FIELDS & VALUES
		$this->field_row_template = '';
		
		if	(	$this->template['fields']['show_field_labels']
			):
			$this->field_row_template .= xhtml::element
			(	array
				(	'tag_name'	=>	'TH'
				,	'attributes'	=>	array
					(	'CLASS'		=>	'rec_fld_lbl rec_fld_lbl_$toggle'
					)
				,	'content'		=>	'$field_label'
				)
			);
		endif;
		
		$field_value = 
		(	$this->template['fields']['show_field_blurbs']
		&&	!empty
			(	$field_blurb
			)
		)
		?	'$field_value<br />$field_blurb'
		:	'$field_value'
		;
		$this->field_row_template .= xhtml::element
		(	array
			(	'tag_name'	=>	'TD'
			,	'attributes'	=>	array
				(	'CLASS'		=>	'rec_fld_val'
				)
			,	'content'		=>	$field_value
			)
		);
		
		$this->field_row_template = xhtml::element
		(	array
			(	'tag_name'	=>	'TR'
			,	'attributes'	=>	array
				(	'CLASS'		=>	'rec_fld_display'
				,	'ID'			=>	'table_'
					.	$this->table->name
					.	'_'
					.	$this->record->renderings
					.	'.tr_$column'
				)
			,	'content'		=>	$this->field_row_template
			)
		);
		
		$this->field_rows_template = '';
		$toggle = 0;
		foreach
			(	$this->fields as $column => &$this->field	
			):
			if	(	$this->field->name != 'id'
				&&	(	$this->record->id
					||	(	!in_array
							(	$this->field->name
							,	$GLOBALS['dbi']->fields_edit_forbidden
							)
						&&	$this->field->field->edit_allowed
						)
					)
				):
				$field_display = str_replace
				(	'$column'
				,	$column
				,	$this->field_row_template
				);
				
				$field_display = str_replace
				(	'$field_label'
				,	$this->field->title
				,	$field_display
				);
	
				$toggle = 
				(	$toggle
				)
				?	0
				:	1
				;
				$field_display = str_replace
				(	'$toggle'
				,	$toggle
				,	$field_display
				);
								
				$value_content =
				(	empty
				 	(	$this->field->name
					)
				)
				?	''
				:	'$'.$this->field->name
				;
				
				$field_display = str_replace
				(	'$field_value'
				,	$value_content
				,	$field_display
				);
				
				eval
				(	'$this_blurb = $this->field->field->'
				 	.	$this->template['output']
					.	'_blurb;'
				);
				$field_display = str_replace
				(	'$field_blurb'
				,	$this_blurb
				,	$field_display
				);
				
				$this->field_rows_template .= $field_display;
			endif;
		endforeach;
		reset
		(	$this->fields
		);
		
		// END RECORD TEMPLATE CREATION
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
					(	'show_owned'	=>	array
						(	'default_value'	=>	array()
						)
					)
				)
			)
		);
		
		$record = '';
		if	(	permission::evaluate
				(	$this->table->permissions['view_records_if']
				)
			):
			if	(	is_file
				 	(	$GLOBALS['page']->path['admin_file_root']
					.	$this->table->name
					.	'.record_template.fore.php'
					)
				):
				include
				(	$GLOBALS['page']->path['admin_file_root']
					.	$this->table->name
					.	'.record_template.fore.php'
				);
			endif;
			
	/*		// TABLE HEADER TEMPLATE
			$thead = xhtml::element(array
				(	'tag_name'	=>	'THEAD'
				,	'attributes'	=>	array
					(	'CLASS'		=>	'record_head'
					,	'ID'			=>	'table_'.$this->table->name.'_'.$this->record->renderings.'.thead'
					)
				,	'content'		=>	$this->title_row_template
				)
			);
			
			$tfoot = xhtml::element(array
				(	'tag_name'	=>	'TFOOT'
				,	'attributes'	=>	array
					(	'CLASS'		=>	'record_foot'
					,	'ID'			=>	'table_'.$this->table->name.'_'.$this->record->renderings.'.tfoot'
					)
				,	'content'		=>	$this->render_footer_row()
				)
			);
	*/		
			// FIELD / VALUE ROW TEMPLATE
			$tbody = $this->template['dud']['template']
			.	$this->render_field_rows()
			;
	/*		
			$tbody = xhtml::element(array
				(	'tag_name'	=>	'TBODY'
				,	'attributes'	=>	array
					(	'CLASS'		=>	'record_body'
					,	'ID'			=>	'table_'.$this->table->name.'_'.$this->record->renderings.'.tbody'
					)
				,	'content'		=>	$tbody
				)
			);
	*/		
			$table = xhtml::element
			(	array
				(	'tag_name'		=>	'TABLE'
				,	'attributes'	=>	array
					(	'CLASS'			=>	'record'
					,	'ID'			=>	'table_'.$this->table->name
						.	'_'
						.	$this->record->renderings
					,	'CELLSPACING'	=>	$this->template['cellspacing']
					)
				,	'content'		=>	$tbody
	//			,	'content'		=>	$thead.$tfoot.$tbody
				)
			);
			
			$hidden_inputs = array
			(	'z'				=>	$this->table->name
			,	'id'			=>	$this->record->id
//			,	'upload'		=>	''
//			,	'MAX_FILE_SIZE'	=>	''
			,	'waive'			=>	''
			);
			
			$request_hidden_values = array
			(	'waive'
//			,	'upload'
//			,	'MAX_FILE_SIZE'
			);
	
			foreach
				(	$request_hidden_values	as	$request_key
				):
				if	(	!empty
						(	$GLOBALS['page']->request[$request_key]
						)
					):
					$hidden_inputs[$request_key] = $GLOBALS['page']->request[$request_key];
				endif;
			endforeach;
				
			switch
				(	$this->record->act	
				):
				case 'create':
					$hidden_inputs['act'] = 'insert';
					break;
				case 'edit':
					$hidden_inputs['act'] = 'update';
					break;
			endswitch;
				
			$content = xhtml::hidden_inputs
			(	$hidden_inputs
			)
			.	$this->title_row_template
			.	$table
			.	$this->template['submit']['template']
			;
/*
			$content = strings::replace_keys_with_values
			(	array
				(	'template_string'	=>	$content
				,	'values'			=>	$this->template
				)
			);
*/
			$this->replace['record'] = arrays::sort_by_strlen
			(	array
				(	'array'			=>	$this->replace['record']
				,	'sort_by_key'	=>	0
				,	'reverse'		=>	1
				)
			);
			foreach
				(	$this->replace['record'] as $replace
				):
				if	(	isset
						(	$this->template[$replace]
						)
					):
					$content = str_replace
					(	'$'
						.	$replace
					,	$this->template[$replace]
					,	$content
					);
				endif;
			endforeach;
			
			$form_attributes = array
			(	'ACTION'		=>	uri::generate()
			,	'METHOD'		=>	'post'
			,	'ID'			=>	$this->form
			,	'NAME'			=>	$this->form
			,	'ENCTYPE'		=>	'application/x-www-form-urlencoded'
			);

			$form = 
			(	$this->record->act == 'edit'
			||	$this->record->act == 'create'
			)
			?	xhtml::element
				(	array
					(	'tag_name'	=>	'FORM'
					,	'attributes'	=>	$form_attributes
					,	'content'		=>	$content
					)
				)
			:	$content;
			
			$record = xhtml::element
			(	array
				(	'tag_name'	=>	'DIV'
				,	'attributes'	=>	array
					(	'CLASS'		=>	'record_div'
					)
				,	'content'		=>	$form
				)
			);
		endif;
		
		$owned = '';
		if	(	$this->record->act != 'create'
			):
			if	(	empty
					(	$show_owned
					)
				):
				$show_owned = 
				(	empty
					(	$this->template['show_owned']
					)
				)
				?	array_keys
					(	$this->record->owns
					)
				:	$this->template['show_owned']
				;
			endif;

			foreach
				(	$show_owned	as	$ownership_id	
				):
				if	(	!is_numeric
						(	$ownership_id
						)
					):
					$ownership_id = 
					(	empty
						(	$this->table->owns[$ownership_id]
						)
					)
					?	0
					:	$this->table->owns[$ownership_id]
					;
				endif;
				if	(	!empty
						(	$ownership_id
						)
					&&	!empty
						(	$GLOBALS['dbi']->tables[$GLOBALS['dbi']->ownerships[$ownership_id]['owned_table']]
						)
					&&	!in_array
						(	$GLOBALS['dbi']->ownerships[$ownership_id]['owned_name']
						,	$this->table->permissions['hide_owned_tables']
						)
					):
					$owned .= '<div id="owned_'
					.	$GLOBALS['dbi']->ownerships[$ownership_id]['owned_table']
					.	'_squeezer" class="squeezebox">'
					.	$GLOBALS['dbi']->tables[$GLOBALS['dbi']->ownerships[$ownership_id]['owned_table']]->render
						(	array
							(	'template'			=>	'owned'
							,	'owners'			=>	array
								(	$ownership_id	=>	array
									(	$this->record->id
									)
								)
		//					,	'in'				=>	array
		//						(	'id'				=>	$owned_records
		//						)
							,	'template_values'	=>	array
								(	'table_title'			=>	$this->table->render_record_title
									(	$this->record->values
									)
									.	': '
									.	$GLOBALS['dbi']->ownerships[$ownership_id]['owned_title']
								,	$GLOBALS['dbi']->ownerships[$ownership_id]['owner_name']	=>	$this->record->id
								)
							,	'paginate'			=>	0
							)
						)
					.	'</div>'
					;
				endif;
			endforeach;
		endif;
/*		
		if	(	!empty
				(	$owned
				)
			):
			$GLOBALS['page']->scripts['ready'][] = '
$(".squeezebox .pre_tbl").attr("title", "Click to show/hide associated records...");
$(".squeezebox .table_content").hide();
$(".squeezebox .pre_tbl").live(
{	click: function()
	{	if	(	$(this).next().is(":visible")
			)
		{	$(this).next().slideUp("fast");
/*			$(this).attr("title", "Click to show associated records..."); *'.'/
		}
		else
		{	$(this).next().slideDown("fast");
/*			$(this).attr("title", "Click to hide associated records..."); *'.'/
		}
	}
});
';
			if	(	!empty
					(	$GLOBALS['page']->request['owned']
					)
				):
				$GLOBALS['page']->scripts['ready'][] = '
$("#owned_'
.	$GLOBALS['page']->request['owned']
.	'_squeezer .table_content").show();
';
			endif;
		endif;
*/
		$record .= $owned;
		
		if	(	permission::evaluate
				(	$this->table->permissions['view_records_if']
				)
			&&	is_file
				(	$GLOBALS['page']->path['admin_file_root']
					.	$this->table->name
					.	'.record_template.aft.php'
				)
			):
			include
			(	$GLOBALS['page']->path['admin_file_root']
				.	$this->table->name
			 	.	'.record_template.aft.php'
			);
		endif;
		
		return $record;
	}
	
	function render_field_rows() {
		$fields_display = $this->field_rows_template;
		$fields = array();
		foreach
			(	$this->fields	as	$column => $field
			):
			$fields[$field->name] = $column;
		endforeach;
		reset
		(	$this->fields
		);
		$fields = arrays::sort_by_strlen
		(	array
			(	'array'			=>	$fields
			,	'sort_by_key'	=>	1
			,	'reverse'		=>	1
			)
		);
		foreach
			(	$fields as $column
			):
			$this->field =& $this->fields[$column];
			if	(	!empty
					(	$this->field->name
					)
				&&	strstr
					(	$this->field_rows_template
					,	'$'
						.	$this->field->name
					)
				):
				$this->output = 
				(	(	$this->record->act == 'edit'
					||	$this->record->act == 'create'
					)
				&&	!in_array
					(	$this->field->name
					,	$GLOBALS['dbi']->fields_edit_forbidden
					)
				&&	$this->field->field->edit_allowed
				)
				?	'edit'
				:	'view'
				;
				$fields_display = str_replace
				(	'$'
					.	$this->field->name
				,	$this->field->render
					(	$this->output
					)
				,	$fields_display
				);
			endif;
			if	(	!empty
					(	$this->table->file_fields[$this->field->name]
					)
				):
				$replaced_template = 
				(	empty
					(	$this->template['fields']['upload_file']
					)
				)
				?	''
				:	strings::replace_keys_with_values
					(	array
						(	'template_string'	=>	$this->template['fields']['upload_file']
						,	'values'			=>	$this->field->field
						,	'sort_by_key'		=>	1
						)
					)
				;
				$fields_display = str_replace
				(	'$upload_file'
				,	$replaced_template
				,	$fields_display
				);
			endif;
		endforeach;
		unset
		(	$fields
		);
		return $fields_display;
	}

	function render_footer_row() {
		
		// PAGINATOR SHOULD PROBABLY GO in HERE
		
		return xhtml::element
		(	array
			(	'tag_name'	=>	'TR'
			,	'attributes'	=>	array
				(	'CLASS'		=>	'footer_row'
				,	'ID'			=>	'table_'
					.	$this->table->name
					.	'_'
					.	$this->record->renderings
					.	'.tr_Z'
				)
			,	'content'		=>	''
			)
		);
	}

/*	
	function title_field()
	{	if	(	empty($this->table->title_field)
			):
			foreach ($this->table->fields	as	$field_name	=>	$field_info):
				if	(	$field_name	!=	'id'
					&&	!in_array($field_info->type,$GLOBALS['dbi']->field_types_numeric)
					&&	!$field_info->null_allowed
					):
					$this->table->title_field = $field_name;
					break;
				endif;
			endforeach;
			reset($this->table->fields);
			
			$table_info = $GLOBALS['dbi']->get_result_array
			(	array
				(	'table'		=>	$GLOBALS['dbi']->info_table
				,	'equals'	=>	array
					(	'name'	=>	$this->table->name
					)
				,	'pop_single_row'	=>	1
				)
			);
			
			$GLOBALS['dbi']->affect_rows
			(	array
				(	'table'	=>	$GLOBALS['dbi']->info_table
				,	'rows'	=>	array
					(	$table_info['id']	=>	array
						(	'title_field'		=>	$this->table->title_field
						)
					)
				)
			);
		endif;
		return $this->table->title_field;
	}
*/

}
