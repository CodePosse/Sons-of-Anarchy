<?php

class page {

	function __construct
	(	$args	=	array()
	)
	{	$this->time = array
		(	'begun'	=>	date_time::get_micro_time
			(	array
				(	'with_time'	=>	1
				)
			)
		);
		
		$this->time['stamp'] = date_time::get_stamp();
		
		$config = &$GLOBALS['cfg'];
	
		$this->files = 
		$this->request = 
		$this->comments = 
		$this->menus = 
		$this->arrays = 
		$this->node_trees = 
		array()
		;
		
		$this->main_domain = strings::domain_name_by_levels(2);
		$this->ip = $_SERVER['SERVER_ADDR'];
		$this->server = $_SERVER['SERVER_NAME'];
	
		// USER CONFIGURATION VARIABLES // DEFAULT VALUES
		$this->language = 'english';

	// LOOP THROUGH CONFIG VALUES AND ASSIGN RELEVANT PAGE PROPERTIES ????		
		$transfer_configs = array
		(	'author'				=>	'Billy Z Duke'
		,	'charset'				=>	'UTF-8'
//		,	'content_type'			=>	'text/html; charset=UTF-8'  // DEPRECATED IN HTML5
		,	'cookies'				=>	array()
		,	'crypt_key'				=>	''
		,	'description'			=>	''
		,	'doc_type_definition'	=>	'<!DOCTYPE html>'
		,	'expires'					=>	''
		,	'flowplayer'				=>	array()
		,	'frameset'					=>	0
		,	'input_sizes'				=>	array()
		,	'keywords'					=>	''
		,	'language'					=>	'english'
		,	'login'						=>	array()
		,	'path'						=>	array
			(	'web_root'					=>	''
			,	'file_root'					=>	$_SERVER['DOCUMENT_ROOT']
			,	'project_root'				=>	$_SERVER['DOCUMENT_ROOT']
			)
		,	'pretty_source'				=>	(debug::get_mode()) ? 1 : 0
		,	'refresh'					=>	array
			(	'seconds'					=>	0
			,	'url'						=>	''
			)
		,	'robots'					=>	'INDEX,NOFOLLOW,NOIMAGEINDEX,NOIMAGECLICK'
		,	'scripts'					=>	array()
		,	'site_title'				=>	''
		,	'shortcut_icon_url'			=>	''
		,	'show_gen_info'				=>	(debug::get_mode()) ? 1 : 0
		,	'style_sheets'				=>	array()
		,	'url_crypt_key'				=>	''
		,	'viewport'					=>	''
		);
		
		foreach
			(	$transfer_configs	as	$tc_key	=>	$tc_val
			):
			if	(	is_array
					(	$tc_val
					)
				):
				if	(	empty
						(	$tc_val
						)
					):
					if	(	!empty
							(	$config[$tc_key]
							)
						):
						$this->$tc_key 	=	$config[$tc_key];
					endif;
				else:
					$this->$tc_key	=	array();
					$tc_keyee = &$this->$tc_key;
					foreach
						(	$tc_val	as	$tc_ray_key	=>	$tc_ray_val
						):
						$tc_keyee[$tc_ray_key]	=	
						(	!isset
							(	$config[$tc_ray_key]
							)
						)
						?	$tc_ray_val
						:	$config[$tc_ray_key]
						;
					endforeach;
				endif;
			else:
				$this->$tc_key	=	
				(	!isset
					(	$config[$tc_key]
					)
				)
				?	$tc_val
				:	$config[$tc_key]
				;
			endif;
		endforeach;
		reset
		(	$transfer_configs
		);
	
		// AUTO-SET FILE ROOT
		if	(	$this->path['file_root'] == $_SERVER['DOCUMENT_ROOT']
			):
			if	(	substr
					(	$this->path['file_root']
					,	-1
					)
					!=	'/'
				):
				$this->path['file_root'] .= '/';
			endif;
			$this->path['file_root'] .= $this->path['web_root'];
			$this->path['admin_file_root'] = $this->path['file_root']
			.	'admin/'
			;
		endif;

		// SET PROJECT ROOT
		if	(	!empty
				(	$this->path['project_root']
				)
			&&	$this->path['project_root']	!=	$_SERVER['DOCUMENT_ROOT']
			):
			$project_root = $_SERVER['DOCUMENT_ROOT'];
			if	(	substr
					(	$project_root
					,	-1
					)
					!=	'/'
				):
				$project_root .= '/';
			endif;
			$this->path['project_root'] = $project_root
			.	$this->path['project_root']
			;
		endif;

		$this->url = array
		(	'base'		=> uri::generate
			(	array
				(	'host'		=>	$this->main_domain
				,	'file_path'	=>	''
				)
			)
		,	'home'		=>	uri::generate
			(	array
				(	'host'		=>	$this->main_domain
				,	'file_path'	=>	substr
					(	$this->path['web_root']
					,	1
//					,	-1
					)
				)
			)
		);
	
		if	(	!empty
				(	$_SERVER['QUERY_STRING']
				)
			):
			// WHEN ENCRYPTED QUERY STRING IS IN USE
			// USE A DOUBLE QUESTION MARK
			// TO ENTER UNENCRYPTED QUERY STRING VARIABLES // IN DEBUG MODE ONLY
			if	(	debug::get_mode()
				&&	strstr
					(	$_SERVER['QUERY_STRING']
					,	'?'
					)	==	$_SERVER['QUERY_STRING']
				):
				if	(	!empty
						(	$_REQUEST
						)
					):
					foreach
						(	$_REQUEST	as	$key	=>	$val	
						):
						if	(	!strlen
								(	$val
								)
							||	isset
								(	$_COOKIE[$key]
								)
							||	strstr
								(	$key
								,	'?'
								)	==	$key
							):
							unset
							(	$_REQUEST[$key]
							);
						endif;
					endforeach;
					reset
					(	$_REQUEST
					);
				endif;
				$query_string = substr
				(	$_SERVER['QUERY_STRING']
				,	1
				);
				$this->request = 
				uri::explode_query_string
				(	$query_string
				);
			else:
				if	(	!empty
						(	$_REQUEST
						)
					):
					foreach
						(	$_REQUEST	as	$key	=>	$val	
						):
						if	(	!is_array
								(	$val	
								)
							):
							if	(	!strlen
									(	$val
									)
								||	isset
									(	$_COOKIE[$key]
									)
								):
								unset
								(	$_REQUEST[$key]
								);
							endif;
						endif;
					endforeach;
					reset
					(	$_REQUEST
					);
				endif;
				if	(	!empty
						(	$this->url_crypt_key
						)
					):
					$this->request = 
					uri::explode_query_string
					(	encryption::my_crypt
						(	array
							(	'data'		=>	urldecode
								(	$_SERVER['QUERY_STRING']
								)
							,	'key'		=>	$this->url_crypt_key
							,	'encrypt'	=>	0
							)
						)
					);
				endif;
			endif;
		endif;
		
		if	(	!empty
				(	$_REQUEST
				)
			):
			$this->request = array_merge
			(	$_REQUEST
			,	$this->request
			);
		endif;
		foreach
			(	$_REQUEST	as	$rqk	=>	$rqv
			):
			if	(	is_string
					(	$rqv
					)
				):
				if	(	strpos
						(	$_SERVER['SCRIPT_NAME']
						,	'/dbi.'
						)	!==	false
					):
					if	(	!(	$rqk			==	'body'
							&&	in_array
								(	$_REQUEST['z']
								,	array
									(	'forgot_password'
									,	'mailing_list'
									,	'register'
									)
								)
							)
						):
						$this->request[$rqk] = 
						(	empty
							(	$GLOBALS['dbi']
							)
						)
						?	strings::scour
							(	$rqv
							)
						:	$GLOBALS['dbi']->real_escape_string
							(	strings::scour
								(	$rqv
								)
							)
						;
					endif;
				else:
					if	(	!empty
							(	$GLOBALS['dbi']
							)
						):
						$GLOBALS['dbi']->real_escape_string
						(	$rqv
						);
					endif;
				endif;
			endif;
		endforeach;

		if	(	!empty
				(	$this->request['waive']
				)
			):
			$this->login['required'] = 0;
		endif;
		
		if	(	empty
				(	$this->request['z']
				)
			):
			$this->request['z'] = 'home';
		endif;
		if	(	empty
				(	$this->request['act']
				)
			):
			$this->request['act'] = 'edit';
		endif;
	
	}

	function alert_redirect
	(	$args	=	array()
	)
	{	extract
		(	debug::function_argument_verify
			(	array
				(	'function'			=>	__CLASS__.'->'.__FUNCTION__
				,	'arguments_input'		=>	$args
				,	'arguments_descriptions'	=>	array
					(	'alert'
					,	'redirect'			=>	array
						(	'default_value'		=>	$_SERVER['HTTP_REFERER']
						)
					,	'clear_body'		=>	array
						(	'blurb'			=>	'Include exit() after header().'
						,	'possible_values'	=>	array
							(	0
							,	1
							)
						,	'default_value'	=>	1
						)
					)
				)
			)
		);
		
		$alert_script = xhtml::script_element
		(	array
			(	'content'	=>	'alert(\''
			.	str_replace
				(	"'"
				,	"\'"
				,	$alert
				)
			.	'\');window.location=\''
			.	$redirect
			.	'\';'
			)
		);
		
		if	(	$clear_body
			):
			$this->body = $alert_script;
		else:
			$this->body .= $alert_script;
		endif;
	}
	
	function close_html() {
		if	(	!empty
				(	$this->files['aft']
				)
			):
			foreach
				(	$this->files['aft']	as	$include_file
				):
				$html_foot = files::include_safe
				(	array
					(	'file'	=>	$include_file
						.	'.php'
					,	'once'	=>	1
					)
				);
				if	(	!empty
						(	$html_foot
						)
					):
					eval
					(	$html_foot
					);
				endif;
			endforeach;
			reset
			(	$this->files['aft']
			);
		endif;
		
		// ENCAPSULATE XHTML HEAD
		$this->head .= $this->render_styles();
		
		if	(	empty
				(	$this->title
				)
			):
			$this->title = '';
		else:
			$this->title = ' : '
			.	$this->title;
		endif;
		$this->head = str_replace
		(	'$page_title'
		,	$this->title
		,	$this->head
		);
		
		$this->head = xhtml::element
		(	array
			(	'tag_name'	=>	'HEAD'
			,	'content'	=>	$this->head
			)
		);
		
		// ENCAPSULATE XHTML BODY
		if	(	empty
				(	$this->frameset
				)
			):
			$body_attributes = array();
			
			$this->body .= $this->render_scripts();

			$this->time['done'] = date_time::get_micro_time
			(	array
				(	'with_time'	=>	1
				)
			);
			$this->time['taken'] = $this->time['done'] - $this->time['begun'];
			if	(	$this->show_gen_info
				):
				$this->comments['begun'] = 
					'<!-- ~PAGE BEGUN: '
				.	date
					(	'Y-m-d H:i:s'
					,	$this->time['begun']
					)
				.	'.'
				.	substr
					(	$this->time['begun']
					,	strpos
						(	$this->time['begun']
						,	'.'
						)
						+ 1
					)
				.	' -->'
				;
				$this->comments['done'] = 
					'<!-- ~PAGE DONE: '
				.	date
					(	'Y-m-d H:i:s'
					,	$this->time['done']
					)
				.	'.'
				.	substr
					(	$this->time['done']
					,	strpos
						(	$this->time['done']
						,	'.'
						)
						+ 1
					)
				.	' | PAGE SIZE (MINUS GENERATION COMMENTS): '
				.	'$page_size'
				.	' | PAGE GENERATION TIME: '
				.	substr
					(	$this->time['taken']
					,	0
					,	strpos
						(	$this->time['taken']
						,	'.'
						)
						+ 7
					)
				.	' SECONDS -->'
				;
				$this->body = 
					$this->comments['begun']
				.	$this->body
				.	$this->comments['done']
				;
			endif;
			$this->body = xhtml::element
			(	array
				(	'tag_name'	=>	'BODY'
				,	'attributes'	=>	$body_attributes
				,	'content'		=>	$this->body
				)
			);
		endif;
		
		
		// ENCAPSULATE XHTML DOCUMENT
		$this->src = $this->doc_type_definition
		.	xhtml::element
			(	array
				(	'tag_name'	=>	'HTML'
				,	'attributes'	=>	array
					(	'lang'		=>	'en'
//					,	'xml:lang'	=>	'en'								// DEPRECATED IN HTML5
//					,	'xmlns'		=>	'http://www.w3.org/1999/xhtml'		// DEPRECATED IN HTML5
					)
				,	'content'		=>	$this->head
					.	$this->body
				)
			)
		;
		
		if	(	!$this->pretty_source
			):
			$this->src = strings::strip_chrs
			(	array
				(	'from_string'		=>	$this->src
				,	'line_feeds'		=>	1
				,	'carriage_returns'	=>	1
				,	'is_html'			=>	1
				)
			);
		endif;
		
		unset
		(	$this->temp
		);
		if	(	$this->show_gen_info
			):
			$this->length = strlen
			(	$this->src
			);
			foreach ($this->comments as $comment):
				$this->length = $this->length - strlen
				(	$comment
				);
			endforeach;
			$this->src = str_replace
			(	'$page_size'
			,	$this->length
			,	$this->src
			);
		endif;
		
		if	(	!empty
				(	$GLOBALS['dbi']
				)
			&&	is_object
				(	$GLOBALS['dbi']
				)
			):
			$GLOBALS['dbi']->close();
		endif;
	}
	
	function disown_records
	(	$args	=	array()
	)
	{	extract
		(	debug::function_argument_verify
			(	array
				(	'function'			=>	__CLASS__.'->'.__FUNCTION__
				,	'arguments_input'		=>	$args
				,	'arguments_descriptions'	=>	array
					(	'owned_table'	=>	array
						(	'default_value'	=>	$this->request['z']
						)
					,	'owner_ids'	=>	array
						(	'default_value'	=>	array()
						)
					,	'owned_ids'	=>	array
						(	'default_value'	=>	$this->request[$GLOBALS['dbi']->tables[$this->request['z']]->request_form.'-disown_id']
						)
					)
				)
			)
		);
		
		$GLOBALS['dbi']->tables[$owned_table]->get_ownerships();
		
		foreach
			(	$GLOBALS['dbi']->tables[$owned_table]->owners	as	$owner_name	=>	$ownership_id
			):
			if	(	!empty
					(	$this->request[$GLOBALS['dbi']->tables[$owned_table]->request_form.'-'.$owner_name]
					)
				):
				$owner_ids = array
				(	$this->request[$GLOBALS['dbi']->tables[$owned_table]->request_form.'-'.$owner_name]
				);
				break;
			endif;
		endforeach;
		reset
		(	$GLOBALS['dbi']->tables[$owned_table]->owners
		);
		
		if	(	empty
				(	$ownership_id
				)
			||	empty
				(	$owner_ids
				)
			||	empty
				(	$owned_ids
				)
			):
			return false;
		else:
			$ownership_ids = $GLOBALS['dbi']->get_result_array
			(	array
				(	'table'		=>	'owned'
				,	'fields'	=>	array
					(	'id'
					)
				,	'equals'	=>	array
					(	'ownership_id'	=>	$ownership_id
					)
				,	'in'		=>	array
					(	'owner_id'		=>	$owner_ids
					,	'owned_id'		=>	$owned_ids
					)
				)
			);
			
			$deleted_owns = $GLOBALS['dbi']->affect_rows
			(	array
				(	'table'	=>	'owned'
				,	'act'	=>	'delete'
				,	'rows'	=>	$ownership_ids
				)
			);
			if	(	$deleted_owns	==	count
					(	$ownership_ids
					)
				):
				$this->redirect
				(	uri::generate
					(	array
						(	'query'	=>	array
							(	'z'		=>	$GLOBALS['dbi']->ownerships[$ownership_id]['owner_table']
							,	'id'	=>	current
								(	$owner_ids
								)
							)
						,	'query_crypt_key'	=>	$GLOBALS['page']->url_crypt_key
						)
					)
				);
			else:
				return false;
			endif;
		endif;
	}
	
	function html() {
		if	(	!isset
				(	$this->body
				)
			):
			$this->open_html();
		endif;
		if	(	empty
				(	$this->src
				)
			):
			$this->close_html();
		endif;
		return 
		(	empty
			(	$this->src
			)
		)
		?	0
		:	1
		;
	}
	
	function kill_cookies
	(	$args	=	array()
	)
	{	extract
		(	debug::function_argument_verify
			(	array
				(	'function'			=>	__CLASS__.'->'.__FUNCTION__
				,	'arguments_input'		=>	$args
				,	'arguments_descriptions'	=>	array
					(	'redirect'	=>	array
						(	'blurb'		=>	'Target to redirect to after killing cookies.'
						,	'default_value'	=>	$_SERVER['SCRIPT_URI']
						)
					)
				)
			)
		);

		$cookie_count = 0;
		foreach
			(	$this->cookies as $cookie_function => $cookie_info
			):
			$cookie_name = 
			(	empty
				(	$cookie_info['name']
				)
			)
			?	$cookie_function
			:	$cookie_info['name']
			;
			if	(	!empty
					(	$_COOKIE[$cookie_name]
					)
				):
				setcookie
				(	$cookie_name
				,	''
				,	(	time()
						-	3600
					)
				,	'/'
				,	$GLOBALS['cfg']['cookie_domain']
				);
				$cookie_count++;
			endif;
		endforeach;
		if	(	$cookie_count > 0
			):
			$this->redirect
			(	$redirect
			);
/*
			if (count($fa_ray) == 0):
				// THE FOLLOWING EREG NEEDS TO BE MODIFIED SO AS TO NOT REMOVE THE ?
				// IF THERE ARE OTHER VARIABLES IN THE QUERY STRING
				header('Location: '.preg_replace('/[\?&]lo=./','',$_SERVER['REQUEST_URI']));
			else:
				$q_str = '?';
				foreach ($fa_ray as	$fa_var	=>	$fa_val) $q_str .= $fa_var.'='.$fa_val.'&';
				header('Location: '.$_SERVER['PHP_SELF'].substr($q_str,0,-1));
			endif;
*/
		else:
			return 0;
		endif;
	}
	
	function open_html
	(	$args	=	array()
	)
	{	extract
		(	debug::function_argument_verify
			(	array
				(	'function'			=>	__CLASS__.'->'.__FUNCTION__
				,	'arguments_input'		=>	$args
				,	'arguments_descriptions'	=>	array
					(	'include_files'	=>	array
						(	'default_value'	=>	array
							(	'fore'	=>	array
								(	'admin/admin.header'
								)
							,	'aft'	=>	array
								(	'admin/admin.footer'
								)
							)
						)
					,	'bust_frames'	=>	array
						(	'possible_values'	=>	array
							(	0
							,	1
							)
						,	'default_value'	=>	1
						)
					)
				)
			)
		);
		
		$this->files = $include_files;
		$this->bust_frames = $bust_frames;
/*		
		if	(	$this->login['required']
			||	$this->request['z'] == 'out'
			):
			$GLOBALS['user'] = new user();
		endif;
*/		

		// INITIALIZE XHTML HEAD
		$head = '';
		
		if	(	!empty
				(	$this->refresh['seconds']
				)
			||	!empty
				(	$this->refresh['url']
				)
			):
			$refresh_content = $this->refresh['seconds'];
			if	(	!empty
					(	$this->refresh['url']
					)
				):
				$refresh_content .= ';url='
				.	$this->refresh['url']
				;
			endif;
			
			$head .= xhtml::element
			(	array
				(	'tag_name'	=>	'META'
				,	'attributes'	=>	array
					(	'HTTP-EQUIV'	=>	'refresh'
					,	'CONTENT'		=>	$refresh_content
					)
				)
			);
		endif;
		
		if	(	debug::get_mode()
			||	isset
				(	$this->expires
				)
			):
			$expires_content = 
			(	isset
				(	$this->expires
				)
			)
			?	$this->expires
			:	0
			;
			$head .= xhtml::element
			(	array
				(	'tag_name'	=>	'META'
				,	'attributes'	=>	array
					(	'HTTP-EQUIV'	=>	'expires'
					,	'CONTENT'		=>	$expires_content
					)
				)
			);
			$head .= xhtml::element
			(	array
				(	'tag_name'	=>	'META'
				,	'attributes'	=>	array
					(	'HTTP-EQUIV'	=>	'cache-control'
					,	'CONTENT'		=>	'no-cache'
					)
				)
			);
/*			
			$head .= xhtml::element
			(	array
				(	'tag_name'	=>	'META'
				,	'attributes'	=>	array
					(	'HTTP-EQUIV'	=>	'pragma'
					,	'CONTENT'		=>	'NO-CACHE'
					)
				)
			);
*/
		endif;
		
		$head .= xhtml::element
		(	array
			(	'tag_name'	=>	'META'
			,	'attributes'	=>	array
				(	'CHARSET'		=>	$this->charset
				)
			)
		);
/*
		$head .= xhtml::element
		(	array
			(	'tag_name'	=>	'META'
			,	'attributes'	=>	array
				(	'HTTP-EQUIV'	=>	'Content-Type'
				,	'CONTENT'		=>	$this->content_type
				)
			)
		);
*/
		$head .= xhtml::element
		(	array
			(	'tag_name'	=>	'META'
			,	'attributes'	=>	array
				(	'NAME'		=>	'robots'
				,	'CONTENT'		=>	$this->robots
				)
			)
		);
		$head .= xhtml::element
		(	array
			(	'tag_name'	=>	'META'
			,	'attributes'	=>	array
				(	'NAME'		=>	'author'
				,	'CONTENT'		=>	$this->author
				)
			)
		);
		
		$head .= xhtml::element
		(	array
			(	'tag_name'	=>	'META'
			,	'attributes'	=>	array
				(	'NAME'		=>	'viewport'
				,	'CONTENT'		=>	$this->viewport
				)
			)
		);
		
		$head .= xhtml::element
		(	array
			(	'tag_name'	=>	'TITLE'
			,	'content'		=>	$this->site_title
				.	'$page_title'
			)
		);
		
		if	(	!empty
				(	$this->description
				)				
			):
			$head .= xhtml::element
			(	array
				(	'tag_name'	=>	'META'
				,	'attributes'	=>	array
					(	'NAME'		=>	'description'
					,	'CONTENT'		=>	$this->description
					)
				)
			);
		endif;
		
		if	(	!empty
				(	$this->keywords
				)
			):
			$head .= xhtml::element
			(	array
				(	'tag_name'	=>	'META'
				,	'attributes'	=>	array
					(	'NAME'		=>	'keywords'
					,	'CONTENT'		=>	$this->keywords
					)
				)
			);
		endif;
		
		if	(	!empty
				(	$this->shortcut_icon_url
				)
			):
			$head .= xhtml::element
			(	array
				(	'tag_name'	=>	'LINK'
				,	'attributes'	=>	array
					(	'REL'		=>	'shortcut icon'
					,	'HREF'		=>	$this->shortcut_icon_url
					)
				)
			);
		endif;
		
		$head .= xhtml::element
		(	array
			(	'tag_name'	=>	'script'
			,	'attributes'	=>	array
				(	'SRC'		=>	'scripts/vendor/modernizr-2.6.2.min.js'
				)
			)
		);

		
		$this->head = $head;
		
		if	(	$this->bust_frames
			):
			$this->scripts['src'][] = 'admin';
		endif;
		
//		$this->scripts['src'][] = 'dom_ready';
		
		// INITIALIZE XHTML BODY
		$this->body = '';

		if	(	!empty
				(	$this->files['fore']
				)
			):
			foreach
				(	$this->files['fore']	as	$include_file
				):
				$html_head = files::include_safe
				(	array
					(	'file'	=>	$include_file
						.	'.php'
					,	'once'	=>	1
					)
				);
				if	(	!empty
						(	$html_head
						)
					):
					eval
					(	$html_head
					);
				endif;
			endforeach;
			reset
			(	$this->files['fore']
			);
		endif;
	}

	function own_records
	(	$args	=	array()
	)
	{	extract
		(	debug::function_argument_verify
			(	array
				(	'function'			=>	__CLASS__.'->'.__FUNCTION__
				,	'arguments_input'		=>	$args
				,	'arguments_descriptions'	=>	array
					(	'owned_table'	=>	array
						(	'default_value'	=>	$this->request['z']
						)
					,	'owner_ids'	=>	array
						(	'default_value'	=>	array()
						)
					,	'owned_ids'	=>	array
						(	'default_value'	=>	array
							(	
							)
						)
					)
				)
			)
		);
		
		$GLOBALS['dbi']->tables[$owned_table]->get_ownerships();
		foreach
			(	$GLOBALS['dbi']->tables[$owned_table]->owners	as	$owner_name	=>	$ownership_id
			):
			if	(	!empty
					(	$this->request[$GLOBALS['dbi']->tables[$owned_table]->request_form.'-'.$owner_name]
					)
				):
				$owner_ids = array
				(	$this->request[$GLOBALS['dbi']->tables[$owned_table]->request_form.'-'.$owner_name]
				);
				break;
			endif;
		endforeach;
		reset
		(	$GLOBALS['dbi']->tables[$owned_table]->owners
		);
		
		if	(	empty
				(	$owned_ids
				)
			):
			$owned_ids = $this->request[$GLOBALS['dbi']->tables[$owned_table]->request_form.'-own_id'];
			if	(	!is_array
				 	(	$owned_ids
					)
				):
				$owned_ids = array
				(	$owned_ids
				);
			endif;
			if	(	$owned_ids[0] == 'O'
				):
				$owned_ids = explode
				(	','
				,	$this->request[$GLOBALS['dbi']->tables[$owned_table]->request_form.'-ownable_ids']
				);
			endif;
		endif;
		
		if	(	empty
				(	$ownership_id
				)
			||	empty
				(	$owner_ids
				)
			||	empty
				(	$owned_ids
				)
			):
			return false;
		else:
/*
Array
(
    [id] => 40
    [owner_table] => project
    [owner_title] => Project
    [owner_where] => 
    [owned_table] => project_media
    [owned_title] => Asset
    [owned_where] => 
    [owners_required] => 1
    [owners_allowed] => 1
    [owned_unique] => Yes
    [owned_required] => 0
    [owned_allowed] => 0
    [status] => Active
    [inserted] => 2009-04-02 18:44:11
    [updated] => 2009-04-02 18:44:11
    [owner_name] => project
    [owned_name] => asset
)
*/
			// IF ONLY ONE OWNER ALLOWED, DISSOCIATE EXISTING OWNED RECORD BEFORE ASSOCIATING NEW ONE
			// NOT SURE WHAT TO DO HERE FOR OTHER OWNER ALLOWED LIMIT VALUES, SO IGNORING FOR NOW
			if	(	$GLOBALS['dbi']->ownerships[$ownership_id]['owners_allowed'] == 1
				):
				$ownership_ids = $GLOBALS['dbi']->get_result_array
				(	array
					(	'table'		=>	'owned'
					,	'fields'	=>	array
						(	'id'
						)
					,	'equals'	=>	array
						(	'ownership_id'	=>	$ownership_id
						)
					,	'in'		=>	array
						(	'owner_id'		=>	$owner_ids
						,	'owned_id'		=>	$owned_ids
						)
					)
				);
				
				$deleted_owns = $GLOBALS['dbi']->affect_rows
				(	array
					(	'table'	=>	'owned'
					,	'act'	=>	'delete'
					,	'rows'	=>	$ownership_ids
					)
				);
			endif;
	
			$insert_owned_rows = array();
			foreach
				(	$owner_ids	as	$owner_id	
				):
				$max_sort_order	= $GLOBALS['dbi']->get_result_array
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
				foreach
					(	$owned_ids	as	$owned_id	
					):
					$max_sort_order++;
					$insert_owned_rows[] = array
					(	'ownership_id'	=>	$ownership_id
					,	'owner_id'		=>	$owner_id
					,	'owned_id'		=>	$owned_id
					,	'sort_order'	=>	$max_sort_order
					);
				endforeach;
				reset
				(	$owned_ids
				);
			endforeach;
			reset
			(	$owner_ids
			);
			$inserted_rows = $GLOBALS['dbi']->insert_rows
			(	array
				(	'table'	=>	'owned'
				,	'rows'	=>	$insert_owned_rows
				)
			);
			if	(	$inserted_rows
				&&	count
					(	$inserted_rows
					)	==	count
					(	$insert_owned_rows
					)
				):
				$this->redirect
				(	uri::generate
					(	array
						(	'query'	=>	array
							(	'z'		=>	$GLOBALS['dbi']->ownerships[$ownership_id]['owner_table']
							,	'id'	=>	current
								(	$owner_ids
								)
							,	'owned'	=>	$owned_table
							)
						,	'query_crypt_key'	=>	$GLOBALS['page']->url_crypt_key
						)
					)
				);
			else:
				return false;
			endif;
		endif;
	}
	
	function redirect
	(	$args	=	array()
	)
	{	extract
		(	debug::function_argument_verify
			(	array
				(	'function'			=>	__CLASS__.'->'.__FUNCTION__
				,	'arguments_input'		=>	$args
				,	'arguments_descriptions'	=>	array
					(	'location'		=>	array
						(	'blurb'			=>	'URL to redirect to.  May be local or external URL.  May contain query string.'
						,	'default_value'	=>	$_SERVER['SCRIPT_URI']
						)
					,	'exit_script'		=>	array
						(	'blurb'			=>	'Include exit() after header().'
						,	'possible_values'	=>	array
							(	0
							,	1
							)
						,	'default_value'	=>	1
						)
					,	'allow_self'		=>	array
						(	'blurb'			=>	'Allow redirection to current URL.'
						,	'possible_values'	=>	array
							(	0
							,	1
							)
						,	'default_value'	=>	1
						)
					)
				)
			)
		);
			

		if	(	!$allow_self
			&&	strstr
				(	$location
				,	$_SERVER['REQUEST_URI']
				)
				==	$_SERVER['REQUEST_URI']
			):
			return false;
		else:
			header
			(	'Location: '
				.	$location
			);
			if	(	$exit_script
				):
				exit;
			endif;
		endif;
	}
	
	function render_html() {
		if	(	$this->html()
			):
			echo $this->src;
			
			if	(	!empty
					(	$this->request['phpinfo']
					)
				):
				expose
				(	get_included_files()
				);
				phpinfo();
			endif;
			
			exit;
		else:
			return 0;
		endif;
	}
	
	function render_menu
	(	$args	=	array()
	)
	{	extract
		(	debug::function_argument_verify
			(	array
				(	'function'			=>	__CLASS__.'->'.__FUNCTION__
				,	'arguments_input'		=>	$args
				,	'arguments_descriptions'	=>	array
					(	'menu_name'
					,	'node_table'				=>	array
						(	'default_value'				=>	'node'
						)
					,	'root_node_id'				=>	array
						(	'default_value'				=>	0
						)
					,	'title_template'			=>	array
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
		
		$this->menus[$menu_name] = new menu
		(	array
			(	'name'			=>	$menu_name
			,	'node_table'	=>	$node_table
			,	'root_node_id'	=>	$root_node_id
			,	'default_depth'	=>	$depth
			)
		);
		return $this->menus[$menu_name]->render
		(	array
			(	'title_template'	=>	$title_template
			,	'item_template'		=>	$item_template
			,	'item_separator'	=>	$item_separator
			,	'depth'				=>	$depth
			,	'expandable'		=>	$expandable
			,	'return_as'			=>	$return_as
			)
		);
	}
	
	function render_paginator
	(	$args	=	array()
	)
	{	extract
		(	debug::function_argument_verify
			(	array
				(	'function'			=>	__CLASS__.'->'.__FUNCTION__
				,	'arguments_input'		=>	$args
				,	'arguments_descriptions'	=>	array
					(	'form_to_submit'
					,	'total_items'				=>	array
						(	'default_value'				=>	0
						)
					,	'first_item'				=>	array
						(	'default_value'				=>	0
						)
					,	'items_per_page'			=>	array
						(	'default_value'				=>	0
						)
					,	'links_per_page'			=>	array
						(	'default_value'				=>	10
						)
					,	'show_items'				=>	array
						(	'possible_values'			=>	array
							(	0
							,	1
							)
						,	'default_value'				=>	1
						)
					,	'show_total'				=>	array
						(	'possible_values'			=>	array
							(	0
							,	1
							)
						,	'default_value'				=>	1
						)
					)
				)
			)
		);
		
		// TRIPLE SUPER DUPER PAGINATOR!!!! //
		$paginator = '';
		
		if	(	!empty
				(	$total_items
				)
			&&	!empty
				(	$items_per_page
				)
			):
			$total_pages = ceil
			(	$total_items
			/	$items_per_page
			);
			
			if	(	$show_total
				):
				$show_total = ' of ';
				$show_total .= 
				(	$show_items
				)
				?	$total_items
				:	$total_pages
				;
			else:
				$show_total = '';
			endif;
			
			$this_page = 
			(	$first_item
			/	$items_per_page
			)	
			+	1
			;
			$link_page = 
			(	floor
				(	(	$this_page
					-	1
					)
					/	$links_per_page
				)
			*	$links_per_page
			)
			+	1
			;
			$last_page = 
			$last_page_link = 
			0
			;
			if	(	$total_pages > 1
				):
//				$GLOBALS['page']->scripts['src'][] = 'paginator_init';
				$paginator .= '<div class="paginator">';
				
				if	(	$link_page > $links_per_page
					):
					// TO FIRST PAGE
					$first_page = 0;
					$first_page_display = 
					(	$show_items
					)
					?	'First Page: 1 - '
						.	$items_per_page
					:	'Page 1'
					;
					$paginator .= xhtml::element
					(	array
						(	'tag_name'		=>	'A'
						,	'attributes'	=>	array
							(	'class'			=>	'paginator_link'
							,	'href'			=>	'#'
							,	'onclick'		=>	'to_page(\''
								.	$form_to_submit
								.	'\','
								.	$first_page
								.	');return false'
							,	'title'			=>	$first_page_display
								.	$show_total
							)
						,	'content'		=>	'&lt;&lt;&lt;'
						)
					);
					
					// TO PREVIOUS SET OF PAGELINKS
					$previous_link_page = 
					(	$link_page
					-	2
					)
					*	$items_per_page
					;
					$previous_link_display = 
					(	$show_items
					)
					?	(	$previous_link_page
						+	1
						)
						.	' - '
						.	(	(	$link_page
								-	1
								)
							*	$items_per_page
							)
					:	'Page '
						.	(	$link_page
							-	2
							)
					;
					$paginator .= '&nbsp;| '
					.	xhtml::element
						(	array
							(	'tag_name'		=>	'A'
							,	'attributes'	=>	array
								(	'class'			=>	'paginator_link'
								,	'href'			=>	'#'
								,	'onclick'		=>	'to_page(\''
									.	$form_to_submit
									.	'\','
									.	$previous_link_page
									.	');return false'
								,	'title'			=>	$previous_link_display
									.	$show_total
								)
							,	'content'		=>	'&lt;&lt;'
							)
						)
					;
				endif;
				
				// TO PREVIOUS PAGE
				if	(	$this_page > 1
					):
					$previous_page = $first_item
					-	$items_per_page
					;
					$previous_page_display = 
					(	$show_items
					)
					?	(	$previous_page
						+	1
						)
						.	' - '
						.	(	$previous_page
							+	$items_per_page
							)
					:	'Page '
						.	$previous_page
					;
					if	(	isset($previous_link_display)
						&&	$previous_page_display	==	$previous_link_display
						):
						unset($previous_page);
					else:
						if	(	isset($previous_link_page)
							):
							$paginator .= '&nbsp;| ';
						endif;
						$paginator .= xhtml::element
						(	array
							(	'tag_name'		=>	'A'
							,	'attributes'	=>	array
								(	'class'			=>	'paginator_link'
								,	'href'			=>	'#'
								,	'onclick'		=>	'to_page(\''
									.	$form_to_submit
									.	'\','
									.	$previous_page
									.	');return false'
								,	'title'			=>	$previous_page_display
									.	$show_total
								)
							,	'content'		=>	'&lt;'
							)
						);
					endif;
				endif;
				
				// GROUPED ITEM NUMBER / PAGINATED LINKS
				for	(	$each_page	=	$link_page
					;	$each_page	<	$link_page + $links_per_page
					;	$each_page++
					):
					if	(	$each_page > 1
						||	isset
							(	$previous_link_page
							)
						||	isset
							(	$previous_page
							)
						):
						$paginator .= '&nbsp;| ';
					endif;
					
					$last_page_item = $each_page
					*	$items_per_page
					;
					$first_page_item = $last_page_item
					-	$items_per_page
					+	1
					;
					
					if	(	$first_page_item	<=	$total_items
						&&	$last_page_item		>=	$total_items
						):
						$last_page_item = $total_items;
						$last_page_link = 1;
					endif;
					
					$page = 
					(	$show_items
					)
					?	$first_page_item
					.	'&nbsp;-&nbsp;'
					.	$last_page_item
					:	$each_page
					;
					
					if	(	$each_page == $this_page
						):
						if	(	$last_page_item == $total_items
							):
							$last_page = 1;
						endif;
						$paginator .= xhtml::element
						(	array
							(	'tag_name'		=>	'SPAN'
							,	'attributes'	=>	array
								(	'id'			=>	'paginator_page_'
									.	$each_page
								,	'class'			=>	'paginator_current'
								)
							,	'content'		=>	$page
								.	$show_total
							)
						);
					else:
						$link_display = 
						(	$show_items
						)
						?	$page
						:	'Page '
							.	$each_page
						;
						$paginator .= xhtml::element
						(	array
							(	'tag_name'		=>	'A'
							,	'attributes'	=>	array
								(	'class'			=>	'paginator_link'
								,	'href'			=>	'#'
								,	'onclick'		=>	'to_page(\''
									.	$form_to_submit
									.	'\','
									.	(	$first_page_item
										-	1
										)
									.	');return false'
								,	'title'			=>	$link_display
									.	$show_total
								)
							,	'content'		=>	$page
							)
						);
					endif;
					if	(	$last_page_link
						):
						break;
					endif;
				endfor;
				
				// TO NEXT PAGE
				if	(	!$last_page
					):
					$next_page = 
					(	$this_page
					*	$items_per_page
					)
					;
					$next_page_display = 
					(	$show_items
					)
					?	(	$next_page
						+	1
						)
						.	' - '
						.	(	$next_page
							+	$items_per_page
							)
					:	'Page '
						.	$this_page
					;
					$next_paginator = '&nbsp;| '
					.	xhtml::element
						(	array
							(	'tag_name'		=>	'A'
							,	'attributes'	=>	array
								(	'class'			=>	'paginator_link'
								,	'href'			=>	'#'
								,	'onclick'		=>	'to_page(\''
									.	$form_to_submit
									.	'\','
									.	$next_page
									.	');return false'
								,	'title'			=>	$next_page_display
									.	$show_total
								)
							,	'content'		=>	'&gt;'
							)
						)
					;
				endif;
				
				if	(	!$last_page_link
					):
					// TO NEXT SET OF PAGELINKS
					$next_link = $last_page_item;
					$next_link_display = 
					(	$show_items
					)
					?	(	$next_link
						+	1
						)
						.	' - '
						.	(	$next_link
							+	$items_per_page
							)
					:	'Page '
						.	floor
							(	$next_link
							/	$items_per_page
							)
					;
					if	(	isset
						 	(	$next_page_display
							)
						&&	$next_page_display	!=	$next_link_display
						):
						$paginator .= $next_paginator;
					endif;
					$paginator .= '&nbsp;| '
					.	xhtml::element
						(	array
							(	'tag_name'		=>	'A'
							,	'attributes'	=>	array
								(	'class'			=>	'paginator_link'
								,	'href'			=>	'#'
								,	'onclick'		=>	'to_page(\''
									.	$form_to_submit
									.	'\','
									.	$next_link
									.	');return false'
								,	'title'			=>	$next_link_display
									.	$show_total
								)
							,	'content'		=>	'&gt;&gt;'
							)
						)
					;
					
					// TO LAST PAGE
					$last_page = 
					(	(	$total_pages
						-	1
						)
						*	$items_per_page
					);
					$last_page_display = 
					(	$show_items
					)
					?	'Last Page: '
						.	$last_page
						.	' - '
						.	$total_items
					:	'Page '
						.	$total_pages
					;
					$paginator .= '&nbsp;| '
					.	xhtml::element
						(	array
							(	'tag_name'		=>	'A'
							,	'attributes'	=>	array
								(	'class'			=>	'paginator_link'
								,	'href'			=>	'#'
								,	'onclick'		=>	'to_page(\''
									.	$form_to_submit
									.	'\','
									.	$last_page
									.	');return false'
								,	'title'			=>	$last_page_display
									.	$show_total
								)
							,	'content'		=>	'&gt;&gt;&gt;'
							)
						)
					;
				endif;
				$paginator .= ' </div>';
			endif;
		endif;
		return $paginator;
	}

	function render_records
	(	$args	=	array()
	)
	{	extract
		(	debug::function_argument_verify
			(	array
				(	'function'			=>	__CLASS__.'->'.__FUNCTION__
				,	'arguments_input'		=>	$args
				,	'arguments_descriptions'	=>	array
					(	'table'
					,	'id'			=>	array
						(	'default_value'	=>	-1
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
					,	'template'	=>	array
						(	'default_value'		=>	''
						)
					,	'order_by'	=>	array
						(	'default_value'		=>	array()
						)
					,	'show_owned'	=>	array
						(	'default_value'		=>	array()
						)
					)
				)
			)
		);

		if	(	!empty
				(	$GLOBALS['dbi']->tables[$table]
				)
			&&	is_object
				(	$GLOBALS['dbi']->tables[$table]
				)
			):
			if	(	empty
					(	$template
					)
				):
				$template = $table;
			endif;
			
			if	(	$id < 0
				):
				$this->body .= $GLOBALS['dbi']->tables[$table]->render
				(	array
					(	'template'	=>	$template
					,	'order_by'	=>	$order_by
					)
				);
			else:
				$GLOBALS['dbi']->tables[$table]->initialize();
				$this->body .= $GLOBALS['dbi']->tables[$table]->record->render
				(	array
					(	'id'			=>	$id
					,	'act'			=>	$act
					,	'name'			=>	$template
					,	'show_owned'	=>	$show_owned
//					,	'force'			=>	true
					)
				);
			endif;
			return 1;
		else:
			return 0;
		endif;
	}
	
	function render_scripts
	(	
	)
	{	$scripts_output = '';
		foreach
			(	$this->scripts	as	$script_type	=>	$scripts
			):
			if	(	!empty
				 	(	$scripts
					)
				&&	is_array
					(	$scripts
					)
				):
				$scripts_bulk = '';
				$scripts = array_unique
				(	$scripts
				);
				foreach
					(	$scripts as $scr	=>	$ipt
					):
					if	(	is_numeric
							(	$scr
							)
						):
						$scr = $ipt;
					endif;
					switch
						(	$script_type
						):
						case 'src':
							$script = array();
							$script['src'] = 
							(	stristr
								(	$scr
								,	'http'
								)	==	$scr
							||	strstr
								(	$scr
								,	'//'
								)	==	$scr
							||	strstr
								(	$scr
								,	'.'
								)	==	$scr	
							)
							?	$scr
							:	'scripts/'
								.	$scr
							;
							$scdot = strrpos
							(	$scr
							,	'.'
							);
							if	(	$scdot === false
								||	strrpos
									(	$scr
									,	'.js'
									)	!==	$scdot
								):
								$script['src'] .= '.js';
							endif;
							$scripts_output .= xhtml::script_element
							(	$script
							);
							break;
						case 'ready':
						default: // case 'functions':
							$scripts_bulk .= $scr
							.	';'
							.	"\n\n"
							;
					endswitch;
				endforeach;
				if	(	!empty
						(	$scripts_bulk
						)
					):
					if	(	$script_type	==	'ready'
						):
						$scripts_bulk = '$(function(){
'
						.	$scripts_bulk
						.	'
});
'
						;
					endif;
					$scripts_output .= xhtml::script_element
					(	array
						(	'content'	=>	$scripts_bulk
						)
					);
				endif;
			endif;
		endforeach;
		return $scripts_output;
	}
	
	function render_styles
	(
	)
	{	$head = '';
		if	(	count
				(	$this->style_sheets
				)
			):
/*
			$head .= xhtml::element
			(	array
				(	'tag_name'	=>	'META'
				,	'attributes'	=>	array
					(	'HTTP-EQUIV'	=>	'Content-Style-Type'
					,	'CONTENT'		=>	'text/css'
					)
				)
			);
*/
			foreach
				(	$this->style_sheets	as	$style_sheet
				):
				$style_href = 'styles/'
				.	$style_sheet
				;
/*
				if	(	$GLOBALS['user']->browser['msie']
					&&	is_file
						(	$this->path['file_root']
						.	$style_href
						.	'-ie.css'
						)
					):
					$style_href .= '-ie';
				endif;
*/
				$style_href .= '.css';
/*
debug::expose
(	'is_file($this->path[\'file_root\'].$style_href) = is_file('.$this->path['file_root'].$style_href.') = '.is_file($this->path['file_root'].$style_href)
);
*/
				if	(	is_file
						(	$this->path['file_root']
						.	$style_href
						)
					):
					if	(	debug::get_mode()
						):
						$style_href .= 
							'?force_refresh='
						.	$this->time['stamp']
						;
					endif;
					$head .= xhtml::element
					(	array
						(	'tag_name'	=>	'LINK'
						,	'attributes'	=>	array
							(	'REL'		=>	'stylesheet'
							,	'HREF'		=>	$style_href
							,	'TYPE'		=>	'text/css'
							)
						)
					);
				endif;
			endforeach;
			reset
			(	$this->style_sheets
			);
		endif;
		
		/*
		if (isset($styles_to_add)) {
			if (is_array($styles_to_add)) {
				$ht .= '<style type="text/css">
					<!-- ';
				while ($style_to_add = array_shift($styles_to_add)) $ht .= $style_to_add."\n";
				$ht .= ' --></style>';
			}
		}
		*/
		
		return $head;
	}

	function reorder_records
	(	$args	=	array()
	)
	{	extract
		(	debug::function_argument_verify
			(	array
				(	'function'			=>	__CLASS__.'->'.__FUNCTION__
				,	'arguments_input'		=>	$args
				,	'arguments_descriptions'	=>	array
					(	'table'
					,	'equals'			=>	array
						(	'default_value'		=>	0
						)
					,	'in'				=>	array
						(	'default_value'		=>	0
						)
					,	'ownership_id'		=>	array
						(	'default_value'		=>	0
						)
					,	'owner_id'			=>	array
						(	'default_value'		=>	0
						)
					)
				)
			)
		);
		
		$reorder_ray = array
		(	'equals'	=>	$equals
		,	'in'		=>	$in
		);
		if	(	!empty
			 	(	$ownership_id
				)
			&&	!empty
				(	$owner_id
				)
			):
			$reorder_ray['owners'] = array
			(	$ownership_id	=>	array
				(	$owner_id
				)
			);
		endif;

		$this->body .= $GLOBALS['dbi']->tables[$table]->reorder
		(	$reorder_ray
		);
	}
	
	function return_html() {
		if	(	$this->html()
			):
			return $this->src;
		else:
			return 0;
		endif;
	}
		
}

